app.controller('EditorCtrl', function($scope, $animate, data, content, templates) {

    // add common data properties and methods to the current scope
    angular.extend($scope, content, templates);
    $scope.data = data.contentStructure;


    // data received from php
    $scope.allowedRowLayouts = eddditorData.allowedRowLayouts;
    $scope.optionsEnabled = {
        post: eddditorData.options.post.enabled,
        row: eddditorData.options.row.enabled,
        element: eddditorData.options.element.enabled
    };


    // on content change, update textarea
    $scope.$watch('data', function(value) {
        angular.element('#eddditor-content').html(angular.toJson(value, true)); // true = pretty
        angular.element('#content').html(extractSearchDump(value));
    }, true);


    // options for jQuery UI's sortable (via ui.sortable)
    $scope.rowSortableOptions = {
        items: '.eddditor-row',
        placeholder: 'eddditor-placeholder',
        forcePlaceholderSize: true,
        revert: 300,
        handle: '.eddditor-row-move'
    };
    $scope.elementSortableOptions = {
        items: '.eddditor-element',
        placeholder: 'eddditor-placeholder',
        forcePlaceholderSize: true,
        revert: 300,
        handle: '.eddditor-element-move',
        connectWith: '#eddditor .eddditor-elements',
        // prevent slide-in animation after moving an element
        start: function(event, ui){
            $animate.enabled(false);
        },
        stop: function(event, ui){
            $animate.enabled(true);
        }
    };
});