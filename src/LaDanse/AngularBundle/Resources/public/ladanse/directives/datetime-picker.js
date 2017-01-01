/*
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

var ladanseApp = angular.module(LADANSE_APP_NAME);

ladanseApp.directive('datetimePicker', function(){
    return {
        require: '?ngModel',
        restrict: 'AE',
        link: function(scope, elem, attr, ngModel)
        {
            var pickerOptions = scope.$eval(attr.datetimePicker);

            $(elem).datetimepicker(pickerOptions)
                .on(
                    'changeDate',
                    function(event)
                    {
                        ngModel.$setViewValue(event.date);
                    }
                );

            scope.$watch(
                attr.ngModel,
                function(newValue)
                {
                    if(newValue)
                        $(elem).datetimepicker('update', newValue);
                });
        }
    };
});