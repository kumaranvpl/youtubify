angular.module('app').directive('playingIcon', function() {
	return {
		restrict: 'E',
		template: '<div class="playing-icon-container">'+
					  '<div class="playing-icon">'+
						  '<div class="one"></div>'+
						  '<div class="two"></div>'+
						  '<div class="three"></div>'+
					  '</div>'+
		          '</div>',
		replace:true
	};
});