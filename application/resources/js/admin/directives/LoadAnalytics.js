'use strict';

angular.module('app')

.directive('edLoadAnalytics', ['$rootScope', 'utils', function($rootScope, utils) {
    return {
        restrict: 'A',
        compile: function() {

            utils.loadScript($rootScope.baseUrl+'assets/js/chart.min.js');
            utils.loadScript($rootScope.baseUrl+'assets/js/moment.min.js');

            if ( ! $rootScope.analyticsLoaded) {
                (function(w,d,s,g,js,fs){
                    g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
                    js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
                    js.src='https://apis.google.com/js/platform.js';
                    fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
                }(window,document,'script'));

                gapi.analytics.ready(function() {
                    renderActiveUsers();
                    renderViewSelector();
                    authAnalytics();
                    renderAnalytics();
                });
            } else {
                renderActiveUsers();
                renderViewSelector();
                renderAnalytics();
                showAnalytics();
            }

            /**
             * Show analytics charts and hide analytics preview image.
             */
            function showAnalytics() {
                var charts = document.querySelectorAll('.charts-row');
                for (var j = 0; j < charts.length; j++) {
                    charts[j].classList.remove('hidden');
                }

                var nodes = document.querySelectorAll('#embed-api-auth-container, .unauthorized-container');
                for (var i = 0; i < nodes.length; i++) {
                    nodes[i].classList.add('hidden');
                }
            }

            /**
             * Authorize the user immediately if the user has already granted access.
             * If no access has been created, render an authorize button inside the
             * element with the ID "embed-api-auth-container".
             */
            function authAnalytics() {
                gapi.analytics.auth.authorize({
                    container: 'embed-api-auth-container',
                    clientid: utils.getSetting('env.google_id')
                });
            }

            function renderAnalytics() {
                gapi.analytics.auth.on('success', function() {
                    showAnalytics();
                });

                /**
                 * Create a new ActiveUsers instance to be rendered inside of an
                 * element with the id "active-users-container" and poll for changes every
                 * five seconds.
                 */
                var activeUsers = new gapi.analytics.ext.ActiveUsers({
                    container: 'active-users-container',
                    pollingInterval: 5
                });


                /**
                 * Add CSS animation to visually show the when users come and go.
                 */
                activeUsers.once('success', function() {
                    var element = this.container.firstChild;
                    var timeout;

                    this.on('change', function(data) {
                        var element = this.container.firstChild;
                        var animationClass = data.delta > 0 ? 'is-increasing' : 'is-decreasing';
                        element.className += (' ' + animationClass);

                        clearTimeout(timeout);
                        timeout = setTimeout(function() {
                            element.className =
                                element.className.replace(/ is-(increasing|decreasing)/g, '');
                        }, 3000);
                    });
                });


                /**
                 * Create a new ViewSelector2 instance to be rendered inside of an
                 * element with the id "view-selector-container".
                 */
                var viewSelector = new gapi.analytics.ext.ViewSelector2({
                    container: 'view-selector-container'
                }).execute().on('error', function(e) {
                    $('.error').text(e.result.error.message);
                });

                /**
                 * Update the activeUsers component, the Chartjs charts, and the dashboard
                 * title whenever the user changes the view.
                 */
                viewSelector.on('viewChange', function(data) {

                    // Start tracking active users for this view.
                    activeUsers.set(data).execute();

                    // Render all the of charts for this view.
                    renderWeekOverWeekChart(data.ids);
                    renderYearOverYearChart(data.ids);
                    renderTopBrowsersChart(data.ids);
                    renderTopCountriesChart(data.ids);
                });


                /**
                 * Draw the a chart.js line chart with data from the specified view that
                 * overlays session data for the current week over session data for the
                 * previous week.
                 */
                function renderWeekOverWeekChart(ids) {

                    // Adjust `now` to experiment with different days, for testing only...
                    var now = moment(); // .subtract(3, 'day');

                    var thisWeek = query({
                        'ids': ids,
                        'dimensions': 'ga:date,ga:nthDay',
                        'metrics': 'ga:sessions',
                        'start-date': moment(now).subtract(1, 'day').day(0).format('YYYY-MM-DD'),
                        'end-date': moment(now).format('YYYY-MM-DD')
                    });

                    var lastWeek = query({
                        'ids': ids,
                        'dimensions': 'ga:date,ga:nthDay',
                        'metrics': 'ga:sessions',
                        'start-date': moment(now).subtract(1, 'day').day(0).subtract(1, 'week')
                            .format('YYYY-MM-DD'),
                        'end-date': moment(now).subtract(1, 'day').day(6).subtract(1, 'week')
                            .format('YYYY-MM-DD')
                    });

                    Promise.all([thisWeek, lastWeek]).then(function(results) {

                        var data1 = results[0].rows.map(function(row) { return +row[2]; });
                        var data2 = results[1].rows.map(function(row) { return +row[2]; });
                        var labels = results[1].rows.map(function(row) { return +row[0]; });

                        labels = labels.map(function(label) {
                            return moment(label, 'YYYYMMDD').format('ddd');
                        });

                        var data = {
                            labels : labels,
                            datasets : [
                                {
                                    label: 'Last Week',
                                    fillColor : "rgba(220,220,220,0.5)",
                                    strokeColor : "rgba(220,220,220,1)",
                                    pointColor : "rgba(220,220,220,1)",
                                    pointStrokeColor : "#fff",
                                    data : data2
                                },
                                {
                                    label: 'This Week',
                                    fillColor : "rgba(151,187,205,0.5)",
                                    strokeColor : "rgba(151,187,205,1)",
                                    pointColor : "rgba(151,187,205,1)",
                                    pointStrokeColor : "#fff",
                                    data : data1
                                }
                            ]
                        };

                        new Chart(makeCanvas('this-vs-last-week')).Line(data);
                        generateLegend('legend-1-container', data.datasets);
                    });
                }


                /**
                 * Draw the a chart.js bar chart with data from the specified view that
                 * overlays session data for the current year over session data for the
                 * previous year, grouped by month.
                 */
                function renderYearOverYearChart(ids) {
                    // Adjust `now` to experiment with different days, for testing only...
                    var now = moment(); // .subtract(3, 'day');

                    var thisMonth = query({
                        'ids': ids,
                        'dimensions': 'ga:date,ga:nthDay',
                        'metrics': 'ga:sessions',
                        'start-date': moment(now).startOf('month').format('YYYY-MM-DD'),
                        'end-date': moment(now).endOf('month').format('YYYY-MM-DD')
                    });

                    var lastMonth = query({
                        'ids': ids,
                        'dimensions': 'ga:date,ga:nthDay',
                        'metrics': 'ga:sessions',
                        'start-date': moment(now).subtract(1, 'month').startOf('month').format('YYYY-MM-DD'),
                        'end-date': moment(now).subtract(1, 'month').endOf('month').format('YYYY-MM-DD')
                    });

                    Promise.all([thisMonth, lastMonth]).then(function(results) {

                        var data1 = results[0].rows.map(function(row) { return +row[2]; });
                        var data2 = results[1].rows.map(function(row) { return +row[2]; });
                        var labels = new Array(31).join().split(',').map(function(item, index){ return ++index;});

                        var data = {
                            labels : labels,
                            datasets : [
                                {
                                    label: 'Last Month',
                                    fillColor : "rgba(220,220,220,0.5)",
                                    strokeColor : "rgba(220,220,220,1)",
                                    pointColor : "rgba(220,220,220,1)",
                                    pointStrokeColor : "#fff",
                                    data : data2
                                },
                                {
                                    label: 'This Month',
                                    fillColor : "rgba(151,187,205,0.5)",
                                    strokeColor : "rgba(151,187,205,1)",
                                    pointColor : "rgba(151,187,205,1)",
                                    pointStrokeColor : "#fff",
                                    data : data1
                                }
                            ]
                        };

                        new Chart(makeCanvas('chart-2-container')).Line(data);
                        generateLegend('legend-2-container', data.datasets);
                    });
                }

                /**
                 * Draw the a chart.js doughnut chart with data from the specified view that
                 * show the top 5 browsers over the past seven days.
                 */
                function renderTopBrowsersChart(ids) {

                    query({
                        'ids': ids,
                        'dimensions': 'ga:browser',
                        'metrics': 'ga:pageviews',
                        'sort': '-ga:pageviews',
                        'max-results': 5
                    })
                        .then(function(response) {

                            var data = [];
                            var colors = ['#4D5360','#949FB1','#D4CCC5','#E2EAE9','#F7464A'];

                            response.rows.forEach(function(row, i) {
                                data.push({ value: +row[1], color: colors[i], label: row[0] });
                            });

                            new Chart(makeCanvas('chart-3-container')).Doughnut(data);
                            generateLegend('legend-3-container', data);
                        });
                }


                /**
                 * Draw the a chart.js doughnut chart with data from the specified view that
                 * compares sessions from mobile, desktop, and tablet over the past seven
                 * days.
                 */
                function renderTopCountriesChart(ids) {
                    query({
                        'ids': ids,
                        'dimensions': 'ga:country',
                        'metrics': 'ga:sessions',
                        'sort': '-ga:sessions',
                        'max-results': 5
                    })
                        .then(function(response) {

                            var data = [];
                            var colors = ['#4D5360','#949FB1','#D4CCC5','#E2EAE9','#F7464A'];

                            response.rows.forEach(function(row, i) {
                                data.push({
                                    label: row[0],
                                    value: +row[1],
                                    color: colors[i]
                                });
                            });

                            new Chart(makeCanvas('chart-4-container')).Doughnut(data);
                            generateLegend('legend-4-container', data);
                        });
                }


                /**
                 * Extend the Embed APIs `gapi.analytics.report.Data` component to
                 * return a promise the is fulfilled with the value returned by the API.
                 * @param {Object} params The request parameters.
                 * @return {Promise} A promise.
                 */
                function query(params) {
                    return new Promise(function(resolve, reject) {
                        var data = new gapi.analytics.report.Data({query: params});
                        data.once('success', function(response) { resolve(response); })
                            .once('error', function(response) { reject(response); })
                            .execute();
                    });
                }


                /**
                 * Create a new canvas inside the specified element. Set it to be the width
                 * and height of its container.
                 * @param {string} id The id attribute of the element to host the canvas.
                 * @return {RenderingContext} The 2D canvas context.
                 */
                function makeCanvas(id) {
                    var container = document.getElementById(id);
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');

                    container.innerHTML = '';
                    canvas.width = container.offsetWidth;
                    canvas.height = container.offsetHeight;
                    container.appendChild(canvas);

                    return ctx;
                }


                /**
                 * Create a visual legend inside the specified element based off of a
                 * Chart.js dataset.
                 * @param {string} id The id attribute of the element to host the legend.
                 * @param {Array.<Object>} items A list of labels and colors for the legend.
                 */
                function generateLegend(id, items) {
                    var legend = document.getElementById(id);
                    legend.innerHTML = items.map(function(item) {
                        var color = item.color || item.fillColor;
                        var label = item.label;
                        return '<li><i style="background:' + color + '"></i>' + label + '</li>';
                    }).join('');
                }

                // Set some global Chart.js defaults.
                Chart.defaults.global.animationSteps = 60;
                Chart.defaults.global.animationEasing = 'easeInOutQuart';
                Chart.defaults.global.responsive = true;
                Chart.defaults.global.maintainAspectRatio = false;

                $rootScope.analyticsLoaded = true;
            }

            function renderViewSelector() {
                ! function e(t, i, r) {
                    function o(s, c) {
                        if (!i[s]) {
                            if (!t[s]) {
                                var a = "function" == typeof require && require;
                                if (!c && a) return a(s, !0);
                                if (n) return n(s, !0);
                                var p = new Error("Cannot find module '" + s + "'");
                                throw p.code = "MODULE_NOT_FOUND", p
                            }
                            var u = i[s] = {
                                exports: {}
                            };
                            t[s][0].call(u.exports, function(e) {
                                var i = t[s][1][e];
                                return o(i ? i : e)
                            }, u, u.exports, e, t, i, r)
                        }
                        return i[s].exports
                    }
                    for (var n = "function" == typeof require && require, s = 0; s < r.length; s++) o(r[s]);
                    return o
                }({
                    1: [function(e, t, i) {
                        var r = e("javascript-api-utils/lib/account-summaries");
                        gapi.analytics.ready(function() {
                            function e(e, t, i) {
                                e.innerHTML = t.map(function(e) {
                                    var t = e.id == i ? "selected " : " ";
                                    return "<option " + t + 'value="' + e.id + '">' + e.name + "</option>"
                                }).join("")
                            }

                            function t(e) {
                                return e.ids || e.viewId ? {
                                    prop: "viewId",
                                    value: e.viewId || e.ids && e.ids.replace(/^ga:/, "")
                                } : e.propertyId ? {
                                    prop: "propertyId",
                                    value: e.propertyId
                                } : e.accountId ? {
                                    prop: "accountId",
                                    value: e.accountId
                                } : void 0
                            }
                            gapi.analytics.createComponent("ViewSelector2", {
                                execute: function() {
                                    return this.setup_(function() {
                                        this.updateAccounts_(), this.changed_ && (this.render_(), this.onChange_())
                                    }.bind(this)), this
                                },
                                set: function(e) {
                                    if (!!e.ids + !!e.viewId + !!e.propertyId + !!e.accountId > 1) throw new Error('You cannot specify more than one of the following options: "ids", "viewId", "accountId", "propertyId"');
                                    if (e.container && this.container) throw new Error("You cannot change containers once a view selector has been rendered on the page.");
                                    var t = this.get();
                                    return (t.ids != e.ids || t.viewId != e.viewId || t.propertyId != e.propertyId || t.accountId != e.accountId) && (t.ids = null, t.viewId = null, t.propertyId = null, t.accountId = null), gapi.analytics.Component.prototype.set.call(this, e)
                                },
                                setup_: function(e) {
                                    function t() {
                                        r.get().then(function(t) {
                                            i.summaries = t, i.accounts = i.summaries.all(), e()
                                        }, function(e) {
                                            i.emit("error", e)
                                        })
                                    }
                                    var i = this;
                                    gapi.analytics.auth.isAuthorized() ? t() : gapi.analytics.auth.on("success", t)
                                },
                                updateAccounts_: function() {
                                    var e, i, r, o = this.get(),
                                        n = t(o);
                                    if (n) switch (n.prop) {
                                        case "viewId":
                                            e = this.summaries.getProfile(n.value), i = this.summaries.getAccountByProfileId(n.value), r = this.summaries.getWebPropertyByProfileId(n.value);
                                            break;
                                        case "propertyId":
                                            r = this.summaries.getWebProperty(n.value), i = this.summaries.getAccountByWebPropertyId(n.value), e = r && r.views && r.views[0];
                                            break;
                                        case "accountId":
                                            i = this.summaries.getAccount(n.value), r = i && i.properties && i.properties[0], e = r && r.views && r.views[0]
                                    } else i = this.accounts[0], r = i && i.properties && i.properties[0], e = r && r.views && r.views[0];
                                    i || r || e ? (i != this.account || r != this.property || e != this.view) && (this.changed_ = {
                                        account: i && i != this.account,
                                        property: r && r != this.property,
                                        view: e && e != this.view
                                    }, this.account = i, this.properties = i.properties, this.property = r, this.views = r && r.views, this.view = e, this.ids = e && "ga:" + e.id) : this.emit("error", new Error("You do not have access to " + n.prop.slice(0, -2) + " : " + n.value))
                                },
                                render_: function() {
                                    var t = this.get();
                                    this.container = "string" == typeof t.container ? document.getElementById(t.container) : t.container, this.container.innerHTML = t.template || this.template;
                                    var i = this.container.querySelectorAll("select"),
                                        r = this.accounts,
                                        o = this.properties || [{
                                                name: "(Empty)",
                                                id: ""
                                            }],
                                        n = this.views || [{
                                                name: "(Empty)",
                                                id: ""
                                            }];
                                    e(i[0], r, this.account.id), e(i[1], o, this.property && this.property.id), e(i[2], n, this.view && this.view.id), i[0].onchange = this.onUserSelect_.bind(this, i[0], "accountId"), i[1].onchange = this.onUserSelect_.bind(this, i[1], "propertyId"), i[2].onchange = this.onUserSelect_.bind(this, i[2], "viewId")
                                },
                                onChange_: function() {
                                    var e = {
                                        account: this.account,
                                        property: this.property,
                                        view: this.view,
                                        ids: this.view && "ga:" + this.view.id
                                    };
                                    this.changed_ && (this.changed_.account && this.emit("accountChange", e), this.changed_.property && this.emit("propertyChange", e), this.changed_.view && (this.emit("viewChange", e), this.emit("idsChange", e), this.emit("change", e.ids))), this.changed_ = null
                                },
                                onUserSelect_: function(e, t) {
                                    var i = {};
                                    i[t] = e.value, this.set(i), this.execute()
                                },
                                template: '<div class="ViewSelector2">  <div class="ViewSelector2-item">    <label>Account</label>    <select class="FormField"></select>  </div>  <div class="ViewSelector2-item">    <label>Property</label>    <select class="FormField"></select>  </div>  <div class="ViewSelector2-item">    <label>View</label>    <select class="FormField"></select>  </div></div>'
                            })
                        })
                    }, {
                        "javascript-api-utils/lib/account-summaries": 3
                    }],
                    2: [function(e, t, i) {
                        function r(e) {
                            this.accounts_ = e, this.webProperties_ = [], this.profiles_ = [], this.accountsById_ = {}, this.webPropertiesById_ = this.propertiesById_ = {}, this.profilesById_ = this.viewsById_ = {};
                            for (var t, i = 0; t = this.accounts_[i]; i++)
                                if (this.accountsById_[t.id] = {
                                        self: t
                                    }, t.webProperties) {
                                    o(t, "webProperties", "properties");
                                    for (var r, n = 0; r = t.webProperties[n]; n++)
                                        if (this.webProperties_.push(r), this.webPropertiesById_[r.id] = {
                                                self: r,
                                                parent: t
                                            }, r.profiles) {
                                            o(r, "profiles", "views");
                                            for (var s, c = 0; s = r.profiles[c]; c++) this.profiles_.push(s), this.profilesById_[s.id] = {
                                                self: s,
                                                parent: r,
                                                grandParent: t
                                            }
                                        }
                                }
                        }

                        function o(e, t, i) {
                            Object.defineProperty ? Object.defineProperty(e, i, {
                                get: function() {
                                    return e[t]
                                }
                            }) : e[i] = e[t]
                        }
                        r.prototype.all = function() {
                            return this.accounts_
                        }, o(r.prototype, "all", "allAccounts"), r.prototype.allWebProperties = function() {
                            return this.webProperties_
                        }, o(r.prototype, "allWebProperties", "allProperties"), r.prototype.allProfiles = function() {
                            return this.profiles_
                        }, o(r.prototype, "allProfiles", "allViews"), r.prototype.get = function(e) {
                            if (!!e.accountId + !!e.webPropertyId + !!e.propertyId + !!e.profileId + !!e.viewId > 1) throw new Error('get() only accepts an object with a single property: either "accountId", "webPropertyId", "propertyId", "profileId" or "viewId"');
                            return this.getProfile(e.profileId || e.viewId) || this.getWebProperty(e.webPropertyId || e.propertyId) || this.getAccount(e.accountId)
                        }, r.prototype.getAccount = function(e) {
                            return this.accountsById_[e] && this.accountsById_[e].self
                        }, r.prototype.getWebProperty = function(e) {
                            return this.webPropertiesById_[e] && this.webPropertiesById_[e].self
                        }, o(r.prototype, "getWebProperty", "getProperty"), r.prototype.getProfile = function(e) {
                            return this.profilesById_[e] && this.profilesById_[e].self
                        }, o(r.prototype, "getProfile", "getView"), r.prototype.getAccountByProfileId = function(e) {
                            return this.profilesById_[e] && this.profilesById_[e].grandParent
                        }, o(r.prototype, "getAccountByProfileId", "getAccountByViewId"), r.prototype.getWebPropertyByProfileId = function(e) {
                            return this.profilesById_[e] && this.profilesById_[e].parent
                        }, o(r.prototype, "getWebPropertyByProfileId", "getPropertyByViewId"), r.prototype.getAccountByWebPropertyId = function(e) {
                            return this.webPropertiesById_[e] && this.webPropertiesById_[e].parent
                        }, o(r.prototype, "getAccountByWebPropertyId", "getAccountByPropertyId"), t.exports = r
                    }, {}],
                    3: [function(e, t, i) {
                        function r() {
                            var e = gapi.client.request({
                                path: s
                            }).then(function(e) {
                                return e
                            });
                            return new e.constructor(function(t, i) {
                                var r = [];
                                e.then(function o(e) {
                                    var c = e.result;
                                    c.items ? r = r.concat(c.items) : i(new Error("You do not have any Google Analytics accounts. Go to http://google.com/analytics to sign up.")), c.startIndex + c.itemsPerPage <= c.totalResults ? gapi.client.request({
                                        path: s,
                                        params: {
                                            "start-index": c.startIndex + c.itemsPerPage
                                        }
                                    }).then(o) : t(new n(r))
                                }).then(null, i)
                            })
                        }
                        var o, n = e("./account-summaries"),
                            s = "/analytics/v3/management/accountSummaries";
                        t.exports = {
                            get: function(e) {
                                return e && (o = null), o || (o = r())
                            }
                        }
                    }, {
                        "./account-summaries": 2
                    }]
                }, {}, [1]);
            }

            function renderActiveUsers() {
                gapi.analytics.ready(function() {
                    gapi.analytics.createComponent("ActiveUsers", {
                        initialize: function() {
                            this.activeUsers = 0
                        },
                        execute: function() {
                            this.polling_ && this.stop(), this.render_(), gapi.analytics.auth.isAuthorized() ? this.getActiveUsers_() : gapi.analytics.auth.once("success", this.getActiveUsers_.bind(this))
                        },
                        stop: function() {
                            clearTimeout(this.timeout_), this.polling_ = !1, this.emit("stop", {
                                activeUsers: this.activeUsers
                            })
                        },
                        render_: function() {
                            var e = this.get();
                            this.container = "string" == typeof e.container ? document.getElementById(e.container) : e.container, this.container.innerHTML = e.template || this.template, this.container.querySelector("b").innerHTML = this.activeUsers
                        },
                        getActiveUsers_: function() {
                            var e = this.get(),
                                t = 1e3 * (e.pollingInterval || 5);
                            if (isNaN(t) || 5e3 > t) throw new Error("Frequency must be 5 seconds or more.");
                            this.polling_ = !0, gapi.client.analytics.data.realtime.get({
                                ids: e.ids,
                                metrics: "rt:activeUsers"
                            }).execute(function(e) {
                                var i = e.totalResults ? +e.rows[0][0] : 0,
                                    s = this.activeUsers;
                                this.emit("success", {
                                    activeUsers: this.activeUsers
                                }), i != s && (this.activeUsers = i, this.onChange_(i - s)), (this.polling_ = !0) && (this.timeout_ = setTimeout(this.getActiveUsers_.bind(this), t))
                            }.bind(this))
                        },
                        onChange_: function(e) {
                            var t = this.container.querySelector("b");
                            t && (t.innerHTML = this.activeUsers), this.emit("change", {
                                activeUsers: this.activeUsers,
                                delta: e
                            }), e > 0 ? this.emit("increase", {
                                activeUsers: this.activeUsers,
                                delta: e
                            }) : this.emit("decrease", {
                                activeUsers: this.activeUsers,
                                delta: e
                            })
                        },
                        template: '<div class="ActiveUsers">Active Users: <b class="ActiveUsers-value"></b></div>'
                    })
                });
            }
        }
   	}
}]);