/**
 * Controller for all overlays
 */
app.controller('ModalCtrl', function($scope, content, layouts, forms) {
    angular.extend($scope, content, layouts);
    $scope.elementTypes = layotterData.elementTypes;
    $scope.form = forms.data;
});