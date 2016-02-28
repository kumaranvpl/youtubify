angular.module('app').directive('featuredArtists', function(utils) {
	return {
		restrict: 'E',
		replace: true,
		scope: { artist: '=', artists: '=' },
		template: '<div class="featuring" ng-show="artists.length > 1">'+
					'<span class="dash">-</span>'+
					'<div class="artist" ng-repeat="name in artists" ng-if="name !== artist.name" ui-sref="artist({ name: utils.encodeUrlParam(name) })">{{ name }}<span ng-if="!$last">,</span></div>'+
		          '</div>',
		link: function($scope) {
			$scope.utils = utils;
		}
	};
});