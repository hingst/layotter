app.service('templates', function($rootScope, $http, $animate, $timeout, view, forms, modals, state, data, history){
    

    var _this = this;


    // data received from php
    this.savedTemplates = layotterData.savedTemplates;


    /**
     * Show edit form for an $element template
     */
    this.editTemplate = function(element) {
        state.setElement(element);
        forms.post(ajaxurl + '?action=layotter_edit_template', {
            template_id: element.template_id
        });
    };
    
    
    /**
     * Delete element template at $index
     */
    this.deleteTemplate = function(index) {
        modals.confirm({
            message: layotterData.i18n.delete_template_confirmation,
            okText: layotterData.i18n.delete_template,
            okAction: function(){
                _this.savedTemplates[index].isLoading = true;
                $http({
                    url: ajaxurl + '?action=layotter_delete_template',
                    method: 'POST',
                    data: {
                        template_id: _this.savedTemplates[index].template_id
                    }
                }).success(function(reply) {
                    history.deletedTemplates.push(_this.savedTemplates[index].template_id);
                    _this.savedTemplates[index].type = reply.type;
                    _this.savedTemplates[index].values = reply.values;
                    _this.savedTemplates[index].view = reply.view;
                    _this.savedTemplates[index].isLoading = undefined;
                    _this.savedTemplates[index].isHighlighted = undefined;
                    _this.savedTemplates[index].template_deleted = true;
                    _this.savedTemplates.splice(index, 1);
                });
            },
            cancelText: layotterData.i18n.cancel
        });
    };
    
    
    /**
     * 
     */
    this.saveNewTemplate = function(element) {
        delete element.template_deleted;
        element.isLoading = true;
        view.showTemplates();
        $http({
            url: ajaxurl + '?action=layotter_save_new_template',
            method: 'POST',
            data: {
                type: element.type,
                values: element.values
            }
        }).success(function(reply) {
            $animate.enabled(false);
            _this.savedTemplates.push(angular.copy(reply));
            $timeout(function(){
                $animate.enabled(true);
            }, 1);
            element.isLoading = undefined;
            element.template_id = reply.template_id;
            element.type = undefined;
            element.values = undefined;
            _this.watchTemplate(element);
            history.pushStep(layotterData.i18n.history.save_element_as_template);
        });
    };
    
    
    /**
     * 
     */
    this.saveTemplate = function() {
        var values = jQuery('#layotter-edit').serializeObject();
        
        // copy editing.element so state can be reset while ajax is still loading
        var editingElement = state.getElement();
        state.reset();
        editingElement.isLoading = true;
        
        $http({
            url: ajaxurl + '?action=layotter_update_template',
            method: 'POST',
            data: {
                template_id: editingElement.template_id,
                values: values
            }
        }).success(function(reply) {
            editingElement.view = reply.view;
            editingElement.isLoading = undefined;
        });
    };


    this.highlightTemplate = function (element) {
        element.isHighlighted = true;
    };


    this.unhighlightTemplate = function (element) {
        element.isHighlighted = undefined;
    };


    // TODO: improve performance, currently all elements are inspected every time a template changes
    this.watchTemplate = function (template) {
        $rootScope.$watch(function () {
            return template;
        }, function (value) {
            var template_id = value.template_id;

            angular.forEach(data.contentStructure.rows, function(row){
                angular.forEach(row.cols, function(col){
                    angular.forEach(col.elements, function(element){
                        if (element.template_id == template_id) {
                            var templateCopy = angular.copy(value);
                            templateCopy.options = element.options;
                            angular.extend(element, templateCopy);
                        }
                    });
                });
            });
        }, true);
    };
    
});