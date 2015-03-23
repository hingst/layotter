app.controller('FormCtrl', function($scope, content, layouts) {

    // include all of content's api methods in the current scope
    angular.extend($scope, content, layouts);

});