'use strict';

/**
 * @ngdoc function
 * @name ltvReportApp.controller:reportCtrl
 * @description
 * Controller of the ltvReportApp
 */

angular.module('ltvReportApp')

	.controller('reportCtrl',['$scope','reportService', function($scope,reportService) {

		$scope.btnLabel = "Get Data";
		$scope.isClicked = false;
		$scope.getLtvReport = function() {

			if($scope.period === '' || $scope.period === undefined) {
				alert("Please select period of report!");
				return;
			}

			if($scope.commission === '' || $scope.commission === undefined) {
				alert("Please enter commission!");
				return;
			}

			if($scope.isInt($scope.commission) || $scope.isFloat($scope.commission)) {
				$scope.btnLabel = "Getting data...";
				$scope.isClicked = true;
				reportService.getLtvData($scope);
			} else {
				alert("Please enter proper value for commission");
				return;				
			}			
    	}

		$scope.isInt = function(n){
			return n != "" && !isNaN(n) && Math.round(n) == n;
		}

		$scope.isFloat = function(n){
			return n != "" && !isNaN(n) && Math.round(n) != n;
		}


}]);
