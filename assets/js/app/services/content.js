app.service('content', function($rootScope, $http, data, forms, modals, state, templates){


    var _this = this;
    this.showBackButton = state.showBackButton;
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
        forms.show(angular.element('#layotter-add-element').html());
    };
    
    
    /**
     * Go back to show the list of available element types when editing a new element
     */
    this.backToShowNewElementTypes = function() {
        forms.show(angular.element('#layotter-add-element').html());
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
        } else if (typeof editingElement.template_id !== 'undefined') {
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
        forms.post(ajaxurl + '?action=layotter_edit_element', {
            type: element.type,
            values: element.values
        });
    };
    
    
    /**
     * Save values from the edit form being currently displayed
     */
    this.saveElement = function() {
        // ACF wraps all form fields in a required object called 'acf'
        var values = jQuery('#layotter-edit').serializeObject();
        if (typeof values.acf == 'undefined') {
            values.acf = {};
        }
        
        // add element to model if creating a new element
        if (state.getParent() !== null) {
            state.getParent().splice(state.getIndex()+1, 0, state.getElement());
        }
        
        // copy editing.element so state can be reset while ajax is still loading
        var editingElement = state.getElement();
        state.reset();
        editingElement.isLoading = true;
        
        $http({
            url: ajaxurl + '?action=layotter_parse_element',
            method: 'POST',
            data: {
                type: editingElement.type,
                values: values
            }
        }).success(function(reply) {
            editingElement.values = reply.values;
            editingElement.view = reply.view;
            editingElement.isLoading = undefined;
        });
    };
    
    
    /**
     * 
     */
    this.editOptions = function(type, element) {
        state.setOptionsType(type);
        state.setElement(element);
        forms.post(ajaxurl + '?action=layotter_edit_options', {
            type: type,
            values: element.options,
            post_id: layotterData.postID
        });
    };
    
    
    /**
     * 
     */
    this.saveOptions = function() {
        // ACF wraps all form fields in a required object called 'acf'
        var values = jQuery('#layotter-edit').serializeObject();
        if (typeof values.acf == 'undefined') {
            values.acf = {};
        }
        
        // copy editing.element so editing can be reset while ajax is still loading
        var editingElement = state.getElement();
        var optionsType = state.getOptionsType();
        state.reset();
        
        editingElement.isLoading = true;
        
        $http({
            url: ajaxurl + '?action=layotter_parse_options',
            method: 'POST',
            data: {
                type: optionsType,
                values: values,
                post_id: layotterData.postID
            }
        }).success(function(reply) {
            editingElement.options = reply;
            editingElement.isLoading = undefined;
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
            },
            cancelText: layotterData.i18n.cancel
        });
    };
    
    
    /**
     * Delete row at $index - ask for confirmation if it contains any elements
     */
    this.deleteRow = function(index) {
        var hasElements = false;
        
        angular.forEach(data.contentStructure.rows[index].cols, function(col){
            if (col.elements.length) {
                hasElements = true;
            }
        });
        
        if (!hasElements) {
            data.contentStructure.rows.splice(index, 1);
            return;
        }
        
        modals.confirm({
            message: layotterData.i18n.delete_row_confirmation,
            okText: layotterData.i18n.delete_row,
            okAction: function(){
                data.contentStructure.rows.splice(index, 1);
            },
            cancelText: layotterData.i18n.cancel
        });
    };
    
    
    /**
     * Add a new empty row at $index
     */
    this.addRow = function(index) {
        data.contentStructure.rows.splice(index+1, 0, angular.copy(data.templates.row));
    };
    
    
    /**
     * Create an exact copy of the row at $index
     */
    this.duplicateRow = function(index) {
        data.contentStructure.rows.splice(index, 0, angular.copy(data.contentStructure.rows[index]));
    };
    
    
    /**
     * Create an exact copy of the element at $index in the $parent col
     */
    this.duplicateElement = function(parent, index) {
        parent.splice(index, 0, angular.copy(parent[index]));
    };
    
    
    /**
     * Get column layout string (half, third, etc.) for column at $index in $row
     */
    this.getColLayout = function(row, index) {
        return row.layout.split(' ')[index];
    };
    
    
    /**
     * Change row layout for $row to new $layout (e.g. 'half fourth fourth')
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
            for (var i = newColCount; i < oldColCount; i++) {
                angular.forEach(row.cols[i].elements, function(element){
                    row.cols[newColCount - 1].elements.push(element);
                });
            }
            row.cols.splice(newColCount);
        }
    };
    
    
    /**
     * Close Lightbox only if no edit form is currently present
     */
    this.cancelEditing = function() {
        if (angular.element('#layotter-changed').val() === '1') {
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
    
    
    angular.element(document).on('click', '#dennisbox .dennisbox-overlay', function(){
        _this.cancelEditing();
    });
    angular.element(document).on('keyup', function(e){
        // when ESC is pressed and an edit form is open but no confirmation modal, cancel editing
        if (e.keyCode == 27 && angular.element('#dennisbox').length && !angular.element('.layotter-modal-confirm').length) {
            angular.element('#layotter-edit :focus').blur();
            _this.cancelEditing();
        }
    });
    
});