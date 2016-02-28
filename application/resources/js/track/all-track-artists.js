angular.module('app').directive('allTrackArtists', function(utils, search) {
	return {
		restrict: 'E',
		replace: true,
		scope: { artists: '=' },
		template: '<div class="artists">'+
					'<div class="artist" ng-repeat="name in artists" ng-click="toArtistPage(name, $event)">{{ name }}<span ng-if="!$last">,</span></div>'+
		          '</div>',
		link: function($scope) {
		  	$scope.utils = utils;
			$scope.toArtistPage = function(artistName, $event) {
				$event.preventDefault();
				$event.stopPropagation();
				utils.toState('artist', { name: artistName });
				search.hidePanel();
			};
		}
	};
});