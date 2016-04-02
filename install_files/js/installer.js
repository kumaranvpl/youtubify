angular.module('installer', ['ui.router'])

.config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
	$urlRouterProvider.otherwise("/");

	$stateProvider
		.state('compatability', {
			url: '/',
			templateUrl: 'install_files/html/compatability.html',
		})
		.state('database', {
			url: '/database',
			templateUrl: 'install_files/html/database.html',
		})
		.state('admin', {
			url: '/admin',
			templateUrl: 'install_files/html/admin.html',
		}).state('finalize', {
			url: '/finalize',
			templateUrl: 'install_files/html/finalize.html',
		})
}])

.run(function($rootScope, $state) {
	$rootScope.$on('$stateChangeStart', function(e, toState) {
		if ($rootScope.currentStep !== toState.name) {
			e.preventDefault();
			$state.go('compatability');
		}
	})
})

.controller('InstallController', ['$scope', '$rootScope', '$http', '$state', '$timeout', function($scope, $rootScope, $http, $state, $timeout) {

	$rootScope.currentStep = 'compatability';

	$scope.steps = ['compatability', 'database', 'admin', 'finalize'];

	$scope.dbDetails = {
		host: 'localhost',
		database: 'database',
		username: 'root',
		password: '',
		prefix: '',
		alreadyFilled: false
	};

	$scope.admin = {};

	$scope.error = '';

	$scope.compatResults = checks;

	$scope.baseUrl = window.location.origin+window.location.pathname;

	$timeout(function() {
		$scope.enableNextStep = !$scope.compatResults.problem;
		window.scrollTo(0,document.body.scrollHeight);
		document.querySelector('button').classList.add('animated', 'pulse', 'infinate');
	}, 2300); //9200

	$scope.nextStep = function() {
		for (var i = 0; i < $scope.steps.length; i++) {
			if ($scope.steps[i] === $scope.currentStep) {
				if ($scope.steps[i+1] === 'finalize') {
					$scope.finalizeInstallation();
				}

				$rootScope.currentStep = $scope.steps[i+1];
				$state.go($scope.steps[i+1]);
				break;
			}
		}
	};

	$scope.createDb = function() {
		showLoader(true);

		$http.post(window.location.pathname, { handler: 'createDb', data: $scope.dbDetails }).success(function(data) {
			if (data.status === 'success') {
				$scope.error = '';
				$scope.nextStep();
			} else {
				$scope.error = data.message;
			}
		}).finally(function() {
			hideLoader();
		});
	};

	$scope.createAdmin = function() {
		showLoader(true);

		$http.post(window.location.pathname, { handler: 'createAdmin', data: $scope.admin }).success(function(data) {
			if (data.status === 'success') {
				$scope.error = '';
				$scope.nextStep();
			} else {
				$scope.error = data.message;
			}
			//location.replace(location.origin);
		}).finally(function() {
			hideLoader();
		});
	};

	$scope.finalizeInstallation = function() {
		showLoader(true);
		$http.post(window.location.pathname, { handler: 'finalizeInstallation'}).success(function(data) {
			if (data.status === 'success') {
				$scope.error = '';
			} else {
				$scope.error = data.message;
			}
		}).finally(function() {
			hideLoader();
		});
	};

    function showLoader(overlay) {
        document.body.classList.add('loading');

        if (overlay) {
            document.body.classList.add('loading-with-overlay');
        }

        document.querySelector('#splash').style.display = 'flex';
    }

    function hideLoader() {
        document.body.classList.remove('loading');
        document.body.classList.remove('loading-with-overlay');
        document.querySelector('#splash').style.display = 'none';
    }
}]);