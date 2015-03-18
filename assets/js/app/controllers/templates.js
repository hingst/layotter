app.controller('TemplatesCtrl', function($scope, $animate, templates) {

    // include all of data's api methods in the current scope
    angular.extend($scope, templates);


    // options for jQuery UI's sortable (via ui.sortable)
    var savedTemplatesBackup;
    $scope.templateSortableOptions = {
        items: '.eddditor-element',
        placeholder: 'eddditor-placeholder',
        forcePlaceholderSize: true,
        revert: 300,
        handle: '.eddditor-element-move',
        connectWith: '#eddditor .eddditor-elements',
        helper: 'clone',
        //scroll: false,
        start: function (event, ui) {
            $animate.enabled(false);
            savedTemplatesBackup = angular.copy($scope.savedTemplates);
            angular.element(ui.item).show(); // show clone while dragging
            angular.element(ui.item.parent()).sortable('option', 'revert', false); // prevent revert animation when dropping on saved elements list
        },
        stop: function (event, ui) {
            if (ui.item.sortable.droptarget && event.target !== ui.item.sortable.droptarget[0]) {
                angular.extend(templates.savedTemplates, savedTemplatesBackup); // re-add element to saved elements if it was removed
                ui.item.sortable.model.options = angular.copy(eddditorData.options.element.defaults); // use default options for new element
                templates.watchTemplate(ui.item.sortable.model);
            }
            $animate.enabled(true);
        },
        update: function (event, ui) {
            if (ui.item.sortable.droptarget && event.target === ui.item.sortable.droptarget[0]) {
                ui.item.sortable.cancel(); // disable sorting of saved elements
            }
        },
        change: function (event, ui) {
            if (ui.placeholder.parent()[0] !== ui.item.parent()[0]) {
                angular.element(ui.item.parent()).sortable('option', 'revert', 300); // enable revert animation when dropping on a col
            }
        }
    };
});