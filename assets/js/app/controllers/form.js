/**
 * Controller for all overlays
 */
app.controller('ModalCtrl', ['$scope', 'content', 'layouts', 'forms', function($scope, content, layouts, forms) {
    angular.extend($scope, content, layouts);
    $scope.elementTypes = window.layotterData.elementTypes;
    $scope.form = forms.data;
}]);