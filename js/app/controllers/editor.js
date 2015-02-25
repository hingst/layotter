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
        // prepare JSON data for representation in textarea
        // replace
        //      &#10; (line feed)       with \n
        //      &#13; (carriage return) with \r
        // as they break JSON validity
        // and
        //      <                       with &lt;
        //      >                       with &gt;
        // as they could potentially break the textarea
        // flag: [g]lobal = replace all matches
        console.log(value);
        var cleanValue = angular.toJson(value, false)
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/&#10;/g, '\\n')
            .replace(/&#13;/g, '\\r');
        angular.element('#content').html('[eddditor]' + cleanValue + '[/eddditor]'); // change false to true for pretty JSON
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