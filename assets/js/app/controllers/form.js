/**
 * Controller for all overlays
 *
 * TODO: Change misleading controller name
 */
app.controller('FormCtrl', function($scope, content, layouts, forms) {
    angular.extend($scope, content, layouts);
    $scope.elementTypes = layotterData.elementTypes;
    $scope.form = forms.data;
});