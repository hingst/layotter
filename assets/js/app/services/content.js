/**
 * Main provider for content and editing
 */
app.service('content', ['$rootScope', '$http', '$animate', '$timeout', 'data', 'forms', 'modals', 'state', 'templates', 'history', function($rootScope, $http, $animate, $timeout, data, forms, modals, state, templates, history){


    var _this = this;
    this.showBackButton = state.showBackButton;
    this.toggleFullscreen = forms.toggleFullscreen;
    $rootScope.$watch(function(){
        return state.showBackButton;
    }, function(value){
        _this.showBackButton = value;
    });
    
    
    /**
     * Show a list of available element types - new element will be inserted at $index in the $parent col
     */
    this.showNewElementTypes = function(parent, index) {
        state.setElement(angular.copy(data.templates.element));
        state.setParent(parent);
        state.setIndex(index);
        forms.showHTML(angular.element('#layotter-add-element').html());
    };
    
    
    /**
     * Go back to show the list of available element types when editing a new element
     */
    this.backToShowNewElementTypes = function() {
        if (forms.fieldsChanged) {
            modals.confirm({
                message: layotterData.i18n.discard_changes_go_back_confirmation,
                okText: layotterData.i18n.discard_changes,
                okAction: function(){
                    forms.showHTML(angular.element('#layotter-add-element').html());
                    forms.fieldsChanged = false;
                    forms.listenForFieldChanges = false;
                },
                cancelText: layotterData.i18n.cancel
            });
        } else {
            forms.showHTML(angular.element('#layotter-add-element').html());
            forms.fieldsChanged = false;
            forms.listenForFieldChanges = false;
        }
    };
    
    
    /**
     * Select the desired element $type from the showNewElementTypes list
     */
    this.selectNewElementType = function(type) {
        state.setElement(angular.extend({}, data.templates.element, { type: type }));
        state.setBackButton(true);
        _this.editElement(state.getElement());
    };
    
    
    /**
     * Save values from the edit form being currently displayed - can be an options form or an element edit form
     */
    this.saveForm = function() {
        var editingElement = state.getElement();
        if (state.getOptionsType()) {
            _this.saveOptions();
        } else if (editingElement.is_template) {
            templates.saveTemplate();
        } else {
            _this.saveElement();
        }
    };
    
    
    /**
     * Show edit form for an $element
     */
    this.editElement = function(element) {
        state.setElement(element);
        forms.fetchDataAndShowForm(ajaxurl + '?action=layotter', {
            layotter_action: 'edit_element',
            layotter_id: element.id,
            layotter_type: element.type
        });
    };
    
    
    /**
     * Save values from the element edit form being currently displayed
     */
    this.saveElement = function() {
        // add element to model if creating a new element
        var isNewElement = false;
        if (state.getParent() !== null) {
            isNewElement = true;
            state.getParent().splice(state.getIndex()+1, 0, state.getElement());
        }
        
        // copy editing.element so state can be reset while ajax is still loading
        var editingElement = state.getElement();
        state.reset();
        editingElement.isLoading = true;

        // build query string from form data
        var values = jQuery('#layotter-edit, .layotter-modal #post').serialize()
            + '&layotter_action=save_element&layotter_id=' + encodeURIComponent(editingElement.id) + '&layotter_type=' + encodeURIComponent(editingElement.type);
        
        $http({
            url: ajaxurl + '?action=layotter',
            method: 'POST',
            data: values,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(reply) {
            editingElement.id = reply.id;
            editingElement.view = reply.view;
            editingElement.is_template = reply.is_template;
            editingElement.isLoading = undefined;
            editingElement.type = undefined; // TODO: do I need this?
            if (isNewElement) {
                history.pushStep(layotterData.i18n.history.add_element);
            } else {
                history.pushStep(layotterData.i18n.history.edit_element);
            }

            // ACF compatibility
            acf.validation.unlockForm();
        });
    };
    
    
    /**
     * Show edit form for an $item's (post, row, col or element) $options
     */
    this.editOptions = function(type, item) {
        state.setOptionsType(type);
        state.setElement(item);
        forms.fetchDataAndShowForm(ajaxurl + '?action=layotter', {
            layotter_action: 'edit_options',
            layotter_type: type,
            layotter_options_id: item.options_id,
            layotter_post_id: layotterData.postID
        });
    };
    
    
    /**
     * Save values from the options edit form being currently displayed
     */
    this.saveOptions = function() {
        // copy editing.element so editing can be reset while ajax is still loading
        var editingItem = state.getElement();
        var optionsType = state.getOptionsType();
        state.reset();
        editingItem.isLoading = true;

        // build query string from form data
        var values = jQuery('#layotter-edit, .layotter-modal #post').serialize()
            + '&layotter_action=save_options&layotter_type=' + encodeURIComponent(optionsType) + '&layotter_post_id=' + encodeURIComponent(layotterData.postID);

        $http({
            url: ajaxurl + '?action=layotter',
            method: 'POST',
            data: values,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(reply) {
            editingItem.options_id = parseInt(reply);
            editingItem.isLoading = undefined;
            history.pushStep(layotterData.i18n.history['edit_' + optionsType + '_options']);

            // ACF compatibility
            acf.validation.unlockForm();
        });
    };
    
    
    /**
     * Delete element at $index in the $parent col
     */
    this.deleteElement = function(parent, index) {
        modals.confirm({
            message: layotterData.i18n.delete_element_confirmation,
            okText: layotterData.i18n.delete_element,
            okAction: function(){
                parent.splice(index, 1);
                history.pushStep(layotterData.i18n.history.delete_element);
            },
            cancelText: layotterData.i18n.cancel
        });
    };
    
    
    /**
     * Delete row at $index
     */
    this.deleteRow = function(index) {
        var hasElements = false;
        
        angular.forEach(data.contentStructure.rows[index].cols, function(col){
            if (col.elements.length) {
                hasElements = true;
            }
        });

        // ask for confirmation only if the row contains any elements
        if (!hasElements) {
            data.contentStructure.rows.splice(index, 1);
            history.pushStep(layotterData.i18n.history.delete_row);
            return;
        }

        modals.confirm({
            message: layotterData.i18n.delete_row_confirmation,
            okText: layotterData.i18n.delete_row,
            okAction: function(){
                data.contentStructure.rows.splice(index, 1);
                history.pushStep(layotterData.i18n.history.delete_row);
            },
            cancelText: layotterData.i18n.cancel
        });
    };
    
    
    /**
     * Add a new empty row at $index
     */
    this.addRow = function(index) {
        data.contentStructure.rows.splice(index+1, 0, angular.copy(data.templates.row));
        history.pushStep(layotterData.i18n.history.add_row);
    };
    
    
    /**
     * Create an exact copy of the row at $index
     */
    this.duplicateRow = function(index) {
        data.contentStructure.rows.splice(index, 0, angular.copy(data.contentStructure.rows[index]));
        history.pushStep(layotterData.i18n.history.duplicate_row);
    };
    
    
    /**
     * Create an exact copy of the element at $index in the $parent col
     */
    this.duplicateElement = function(parent, index) {
        parent.splice(index, 0, angular.copy(parent[index]));
        history.pushStep(layotterData.i18n.history.duplicate_element);
    };
    
    
    /**
     * Get column layout string ('1/2', '2/3', etc.) for column at $index in $row
     */
    this.getColLayout = function(row, index) {
        return row.layout.split(' ')[index];
    };
    
    
    /**
     * Change row layout for $row to new $layout (e.g. '1/2 1/4 1/4')
     */
    this.setRowLayout = function(row, layout) {
        var oldColCount = row.layout.split(' ').length;
        var newColCount = layout.split(' ').length;
        row.layout = layout;
        
        // add empty cols if number of cols is increased
        if (newColCount > oldColCount) {
            for (var i = oldColCount; i < newColCount; i++) {
                row.cols.push(angular.copy(data.templates.col));
            }
        } else { // move surplus elements to last remaining col if number of cols is decreased
            $animate.enabled(false);
            for (var i = newColCount; i < oldColCount; i++) {
                angular.forEach(row.cols[i].elements, function(element){
                    row.cols[newColCount - 1].elements.push(element);
                });
            }
            row.cols.splice(newColCount);
            $timeout(function(){
                $animate.enabled(true);
            }, 1);
        }

        history.pushStep(layotterData.i18n.history.change_row_layout);
    };
    
    
    /**
     * Close Lightbox only if no edit form is currently present
     */
    this.cancelEditing = function() {
        if (forms.fieldsChanged) {
            modals.confirm({
                message: layotterData.i18n.discard_changes_confirmation,
                okText: layotterData.i18n.discard_changes,
                okAction: function(){
                    state.reset();
                },
                cancelText: layotterData.i18n.cancel
            });
        } else {
            state.reset();
        }
    };


    /**
     * Close current overlay when clicking the dark background
     */
    // forms
    angular.element(document).on('click', '#dennisbox .dennisbox-overlay', function(){
        _this.cancelEditing();
    });
    // modals
    angular.element(document).on('click', '#dennisbox-modal .dennisbox-overlay', function(){
        if (typeof $rootScope.confirm !== 'undefined') {
            $rootScope.confirm.cancelAction();
        }
        if (typeof $rootScope.prompt !== 'undefined') {
            $rootScope.prompt.cancelAction();
        }
    });
    // when ESC is pressed and an edit form is open (but no confirmation or prompt modal), cancel editing
    angular.element(document).on('keyup', function(e){
        if (e.keyCode == 27 && angular.element('#dennisbox').length && !angular.element('.layotter-modal-confirm').length && !angular.element('.layotter-modal-prompt').length) {
            angular.element('#layotter-edit :focus, .layotter-modal #post :focus').blur();
            _this.cancelEditing();
        }
    });
    
}]);