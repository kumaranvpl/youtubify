angular.module('app').run(function($rootScope, $state, $location, users, utils) {
    $rootScope.$on('$stateChangeStart', function(e, toState, toParams, fromState, fromParams) {
        if (fromParams) {
            for(var key in fromParams) {
                fromParams[key] = utils.decodeUrlParam(fromParams[key]);
            }
        }

        $rootScope.previousState = { name: fromState.name, params: fromParams };

        var needsLogin = ['songs', 'albums', 'artists'],
            doesntNeedLogin = ['login', 'register'];

        //if homepage is set to custom allow all not logged in
        //users to access it so admin can display landing page
        if (utils.getSetting('homepage') === 'custom') {
            doesntNeedLogin.push('home');
        }

        //extract parent state name if it's a child state
        var stateName = toState.name.replace(/\..+?$/, '');

        //if state doesn't exist, bail
        if ( ! $state.get(stateName)) {
            e.preventDefault();
        }

        if (utils.getSetting('force_login', false) && ! users.current && doesntNeedLogin.indexOf(toState.name) === -1) {
            e.preventDefault();
            $state.go('login');
        }

        //show login page if login is needed for this state
        if (needsLogin.indexOf(toState.name) > -1 && ! users.current) {
            e.preventDefault();
            $state.go('login');
        }

        //disable routes that require echonest api key, if that key is not set
        if ( ! utils.getSetting('echonest_api_key') && toState.name.indexOf('radio') > -1) {
            e.preventDefault();
            $state.go('home');
        }

        //logged in users can't access login or register state
        if ((stateName == 'login' || stateName == 'register') && users.current) {
            e.preventDefault();
            $state.go('songs');
        }

        //if registration is disabled redirect to login state
        if (stateName == 'register' && ! utils.getSetting('enableRegistration', true)) {
            e.preventDefault();
            $state.go('login');
        }

        //only admins can access admin states
        if (stateName == 'admin') {
            if (! users.current || ! users.current.isAdmin) {
                e.preventDefault();
                $state.go('songs');
            }
        }

        if (stateName === 'home') {
            var homepage = utils.getSetting('homepage', 'default');

            if (homepage === 'custom') {
                toState.templateUrl = utils.getSetting('customHomePath');
            } else if (homepage === 'login') {
                e.preventDefault();
                $state.go('login');
            } else {
                e.preventDefault();
                $state.go(utils.getSetting('primaryHomeSection'));
            }
        }

        //push url change event to google analytics
        if (window.ga) {
            window.ga('send', 'pageview', { page: toState.url });
        }
    })
});