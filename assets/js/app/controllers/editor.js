app.controller('EditorCtrl', function($scope, $animate, data, content, templates, layouts) {

    // add common data properties and methods to the current scope
    angular.extend($scope, content, templates, layouts);
    $scope.data = data.contentStructure;


    // data received from php
    $scope.allowedRowLayouts = layotterData.allowedRowLayouts;
    $scope.optionsEnabled = {
        post: layotterData.options.post.enabled,
        row: layotterData.options.row.enabled,
        col: layotterData.options.col.enabled,
        element: layotterData.options.element.enabled
    };


    // on content change, update textarea
    $scope.$watch('data', function(value) {
        // remove views from JSON data, no need to store them as they're always regenerated before output
        var valueClone = angular.copy(value);
        angular.forEach(valueClone.rows, function(row){
            angular.forEach(row.cols, function(col){
                angular.forEach(col.elements, function(element){
                    delete element.view;
                });
            });
        });

        // prepare JSON data for representation in textarea
        // replace
        //      & with &amp;
        //      < with &lt;
        //      > with &gt;
        // as they can break the textarea and/or JSON validity
        // flag: [g]lobal = replace all matches instead of just the first one
        var cleanValue = angular.toJson(valueClone, false) // change to true for pretty JSON
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // enter JSON data wrapped in layotter shortcode into textarea
        angular.element('#content').html('[layotter]' + cleanValue + '[/layotter]');
    }, true);


    // options for jQuery UI's sortable (via ui.sortable)
    $scope.rowSortableOptions = {
        items: '.layotter-row',
        placeholder: 'layotter-placeholder',
        forcePlaceholderSize: true,
        revert: 300,
        handle: '.layotter-row-move'
    };
    $scope.elementSortableOptions = {
        items: '.layotter-element',
        placeholder: 'layotter-placeholder',
        forcePlaceholderSize: true,
        revert: 300,
        handle: '.layotter-element-move',
        connectWith: '#layotter .layotter-elements',
        // prevent slide-in animation after moving an element
        start: function(event, ui){
            $animate.enabled(false);
        },
        stop: function(event, ui){
            $animate.enabled(true);
        }
    };
});