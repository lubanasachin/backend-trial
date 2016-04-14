'use strict';
/**
 * @ngdoc LTV Report Application
 * @name ltvReportApp
 * @description
 *
 * Main module of the application.
 */
angular
	.module('ltvReportApp', ['oc.lazyLoad','ui.router'])
	.config(['$stateProvider','$urlRouterProvider','$ocLazyLoadProvider',function ($stateProvider,$urlRouterProvider,$ocLazyLoadProvider) {
		$ocLazyLoadProvider.config({debug:false,events:true});
		
		//defaults to report
		$urlRouterProvider.otherwise('/report');

		//state provider
		$stateProvider

			.state('report',{
				templateUrl:'views/report.html',
				url:'/report',
				controller:'reportCtrl',
				resolve: {
					loadMyFiles:function($ocLazyLoad) {
						return $ocLazyLoad.load({
							name:'ltvReportApp',
							files:[
								'scripts/constants.js',
								'scripts/controllers/report.js',
								'scripts/services/report.js',
							]
						})
					}
				}
			})


	}]);

    
