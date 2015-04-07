/**
 * Controller for the element templates sidebar
 */
app.controller('TemplatesCtrl', function($scope, $animate, templates, $timeout, history) {
    angular.extend($scope, templates);


    // options for jQuery UI's sortable (via ui.sortable)
    var savedTemplatesBackup;
    $scope.templateSortableOptions = {
        items: '.layotter-element',
        cancel: '.layotter-element-buttons',
        placeholder: 'layotter-placeholder',
        forcePlaceholderSize: true,
        revert: 300,
        connectWith: '#layotter .layotter-elements',
        helper: 'clone',
        start: function (event, ui) {
            $animate.enabled(false); // prevent animation when savedTemplatesBackup is restored after a template was dragged
            templates.unhighlightTemplate(ui.item.sortable.model); // unhighlight hovered template on drag start
            savedTemplatesBackup = angular.copy($scope.savedTemplates); // save current set of templates to be restored after sorting
            jQuery(ui.item).show(); // show clone while dragging
            jQuery(ui.item.parent()).sortable('option', 'revert', false); // prevent revert animation when dropping on saved elements list
        },
        stop: function (event, ui) {
            if (ui.item.sortable.droptarget && event.target !== ui.item.sortable.droptarget[0]) {
                angular.extend(templates.savedTemplates, savedTemplatesBackup); // re-add element to saved elements if it was removed
            }
            history.pushStep(layotterData.i18n.history.create_element_from_template);
            $timeout(function(){
                $animate.enabled(true); // reenable all animations after sorting
            }, 1);
        },
        update: function (event, ui) {
            if (ui.item.sortable.droptarget && event.target === ui.item.sortable.droptarget[0]) {
                ui.item.sortable.cancel(); // disable sorting saved elements
            }
        },
        change: function (event, ui) {
            if (ui.placeholder.parent()[0] !== ui.item.parent()[0]) {
                jQuery(ui.item.parent()).sortable('option', 'revert', 300); // enable revert animation when dropping on a column
            }
        }
    };
});