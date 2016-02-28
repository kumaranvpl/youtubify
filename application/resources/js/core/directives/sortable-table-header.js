angular.module('app').directive('sortableTableHeader', function($timeout, utils) {
	return {
		restrict: 'A',
		link: function ($scope, el, attrs) {
			if ( ! $scope.params) $scope.params = {};

			//set default sort if passed in
			if (attrs.sortableTableHeader) {
				$timeout(function() {
					$scope.params.sort = attrs.sortableTableHeader;
				})
			}

			el.on('click', function(e) {
				var columnEl = utils.closest(e.target, '.flex-table-row-item');

				if ( ! columnEl) return;

				var sortBy = columnEl.getAttribute('sort-field');

				if ( ! sortBy) return;

				$scope.$apply(function() {
					if ($scope.params.sort === sortBy) {
						$scope.params.sort = '-'+sortBy;
						utils.removeNode('.sort-table-angle-icon');
						angular.element(columnEl).append('<i class="icon icon-angle-down sort-table-angle-icon"></i>');
					} else {
						$scope.params.sort = sortBy;
						utils.removeNode('.sort-table-angle-icon');
						angular.element(columnEl).append('<i class="icon icon-angle-up sort-table-angle-icon"></i>');
					}
				})

			})
		}
	};
});