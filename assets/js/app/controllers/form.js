/**
 * Controller for all overlays
 *
 * TODO: Change misleading controller name
 */
app.controller('FormCtrl', function($scope, content, layouts) {
    angular.extend($scope, content, layouts);
});