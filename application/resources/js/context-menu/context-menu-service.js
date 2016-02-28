angular.module('app').factory('contextMenu',function($rootScope, utils, player) {

    var items = [
        { name: utils.trans('play'), icon: 'play', action: 'playArtist', context: ['artist'] },
        { name: utils.trans('addToQueue'), icon: 'list-add', action: 'addToQueue', context: ['track', 'queue', 'album', 'artist'] },
        { name: utils.trans('removeFromQueue'), icon: 'trash', action: 'removeFromQueue', context: ['queue'], skip: true },
        { name: utils.trans('addToPlaylist'), icon: 'note', action: 'addToPlaylist', context: ['track', 'queue', 'album'] },
        { name: utils.trans('addToYourMusic'), icon: 'cd', action: 'addToYourMusic', context: ['track', 'queue', 'album'] },
        { name: utils.trans('copySongLink'), icon: 'link', action: 'copySongLink', context: ['track', 'queue'] },
        { name: utils.trans('copyAlbumLink'), icon: 'link', action: 'copyAlbumLink', context: ['album'] },
        { name: utils.trans('startArtistRadio'), icon: 'wifi', action: 'startArtistRadio', context: ['artist'], skip: !utils.getSetting('echonest_api_key') },
        { name: utils.trans('copyArtistLink'), icon: 'link', action: 'copyArtistLink', context: ['artist'] },
        { name: utils.trans('share'), icon: 'share', action: 'showShareModal', context: ['track', 'queue', 'album', 'artist']},
        { name: utils.trans('removeFromPlaylist'), icon: 'trash', action: 'removeFromPlaylist', context: ['track'], state: 'playlist', separator: true }
    ];

    var contextmenu = {
        open: false,
        context: 'track',
        item: false,
        attrs: {},

        /**
         * Show context menu.
         *
         * @param {object}  e     right-click event
         * @param {object}  item  item user right clicked on
         * @param {string|undefined} attrs
         * @param {object|undefined} $scope
         */
        show: function(e, item, attrs, $scope) {
            this.item = item;
            this.context = attrs.contextMenuItem || 'track';
            this.$scope = $scope;
            this.attrs = attrs;

            e.preventDefault();
            this.generateMenu(this.context);
            this.positionMenu(e);

            this.open = true;
        },

        hide: function() {
            document.getElementById('context-menu').classList.add('hidden');
            this.open = false;

            var panel = document.querySelector('.add-to-playlist');

            if (panel) {
                panel.classList.add('slide-out');
            }
        },

        positionMenu: function(e) {
            var menu = document.getElementById('context-menu');

            menu.classList.remove('hidden');

            var box = menu.getBoundingClientRect();

            var menuWidth    = box.width + 4,
                menuHeight   = box.height + 20,
                windowWidth  = window.innerWidth,
                windowHeight = window.innerHeight,
                clickCoordsX = e.clientX,
                clickCoordsY = e.clientY;

            if ((windowWidth - clickCoordsX) < menuWidth) {
                menu.style.left = windowWidth - menuWidth + 1 + 'px';

            } else {
                menu.style.left = clickCoordsX + 1 + 'px';
            }

            if ((windowHeight - clickCoordsY) < menuHeight) {
                menu.style.top = windowHeight - menuHeight + 1 + 'px';
            } else {
                menu.style.top = clickCoordsY + 1 + 'px';
            }
        },

        generateMenu: function(context) {
            var menu  = document.getElementById('context-menu'),
                body  = menu.querySelector('.body'),
                old   = body.getElementsByTagName('li');

            while(old[0]) {
                old[0].parentNode.removeChild(old[0]);
            }

            populateHeader();

            for (var i = 0; i < items.length; i++) {
                var item = items[i];

                if (item.skip) continue;
                if (item.context.indexOf(context) === -1) continue;
                if (item.state && ! utils.stateIs(item.state)) continue;

                if (item.action === 'addToQueue' && context === 'queue' && player.isInQueue(contextmenu.item)) {
                    item = getItemByAction('removeFromQueue');
                }

                //if we are in playlist state and this track is already in playlist, continue
                if (item.action === 'addToPlaylist' && utils.stateIs('playlist') && contextmenu.$scope.playlist) {
                    for (var j = 0; j < contextmenu.$scope.playlist.tracks.length; j++) {
                        if (contextmenu.$scope.playlist.tracks[j].id == contextmenu.item.id) {
                            var trackExists = true; break;
                        }
                    }

                    if (trackExists) continue;
                }

                if (item.action === 'removeFromPlaylist' && ! contextmenu.$scope.playlist.is_owner) {
                    continue;
                }

                createMenuItem(item);
            }

            menu.classList.remove('hidden');
        }
    };

    /**
     * Populate context menu header with item image and name.
     */
    populateHeader = function() {
        var menu    = document.getElementById('context-menu'),
            image   = menu.querySelector('.image'),
            src     = getHeaderImage(),
            name    = contextmenu.item.name,
            subname = getSubname();

        //utils.img
        var img = document.createElement('img');
        img.src = src;

        //remove previous img element
        while (image.firstChild) {
            image.removeChild(image.firstChild);
        }

        image.appendChild(img);

        menu.querySelector('.name').textContent = name;
        menu.querySelector('.sub-name').textContent = subname;
    };

    getHeaderImage = function() {
        if (contextmenu.item.album) {
            return utils.img(contextmenu.item.album.image, 'album');
        }

        if (contextmenu.item.image) {
            return utils.img(contextmenu.item.image, 'album');
        }

        if (contextmenu.item.image_small) {
            return utils.img(contextmenu.item.image_small, 'artist');
        }

        return utils.img('', 'album');
    };

    getSubname = function() {
        if (contextmenu.attrs.subName) {
            return contextmenu.$scope.$eval(contextmenu.attrs.subName);
        }

        if (contextmenu.item.artist) {
            if (angular.isString(contextmenu.item.artist)) {
                return contextmenu.item.artist;
            }

            return contextmenu.item.artist.name;
        }

        if (contextmenu.item.album) {
            return contextmenu.item.album.artist.name;
        }

        if (contextmenu.item.name) {
            return contextmenu.item.name;
        }
    };

    createMenuItem = function(item) {
        var body = document.querySelector('#context-menu > .body'),
            li = document.createElement('li');

        if (item.separator) {
            var separator = document.createElement('li');
            separator.className = 'separator';
            body.insertBefore(separator, null);
        }

        li.className = 'context-menu-item';
        li.dataset.action = item.action;

        var icon = document.createElement('i');
        icon.className = 'icon icon-'+item.icon;

        var text = document.createTextNode(item.name);

        li.appendChild(icon);
        li.appendChild(text);

        body.appendChild(li);
    };

    /**
     * Return context menu item by action name.
     *
     * @param {string} action
     * @return {object}
     */
    getItemByAction = function(action) {
        for (var i = 0; i < items.length; i++) {
            if (items[i].action === action) {
                return items[i];
            }
        }
    };

    $rootScope.$on('contextmenu.closed', function() {
        contextmenu.hide();
    });

    $rootScope.$on('$stateChangeSuccess', function() {
        contextmenu.hide();
    });

    return contextmenu;
});