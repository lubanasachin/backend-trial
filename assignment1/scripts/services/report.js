'use strict';

/**
 * @ngdoc function
 * @name ltvReportApp.Service:reportService
 * @description
 * Service of the ltvReportApp
 */

angular.module('ltvReportApp')

	.service('reportService',['$http', function($http) {

		var retval = {

			getLtvData: function($scope) {
        		var reqdata = JSON.stringify({ 'period' : $scope.period, 'commission': ($scope.commission/100)});
				$http({
				    url: "run.php",
				    method: "POST",
				    data: reqdata,
				    headers: {
						"Content-Type": "application/x-www-form-urlencoded"
					}
				})
				.then(function(response,status) {
					$scope.btnLabel = "Get data";
					$scope.isClicked = false;
				    $scope.reportData = response.data;
				},function(response,status) {
					$scope.btnLabel = "Get data";
					$scope.isClicked = false;
				});
			}
		};

		return retval;

	}]);
