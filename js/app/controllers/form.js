app.controller('FormCtrl', function($scope, content) {

    // include all of content's api methods in the current scope
    angular.extend($scope, content);

});