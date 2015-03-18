app.service('templates', function($rootScope, $http, view, forms, modals, state){
    

    var _this = this;
    
    
    // data received from php
    this.savedTemplates = eddditorData.savedTemplates;
    
    
    /**
     * Show edit form for an $element template
     */
    this.editTemplate = function(element) {
        state.setElement(element);
        forms.post(ajaxurl + '?action=eddditor_edit_template', {
            template: element.template
        });
    };
    
    
    /**
     * Delete element template at $index
     */
    this.deleteTemplate = function(id) {
        modals.confirm({
            message: eddditorData.i18n.delete_template_confirmation,
            okText: eddditorData.i18n.delete_template,
            okAction: function(){
                _this.savedTemplates[id].isLoading = true;
                $http({
                    url: ajaxurl + '?action=eddditor_delete_template',
                    method: 'POST',
                    data: {
                        template: id
                    }
                }).success(function(reply) {
                    _this.savedTemplates[id].template = undefined;
                    _this.savedTemplates[id].isLoading = undefined;
                });
            },
            cancelText: eddditorData.i18n.cancel
        });
    };
    
    
    /**
     * 
     */
    this.saveNewTemplate = function(element) {
        element.isLoading = true;
        view.showTemplates();
        $http({
            url: ajaxurl + '?action=eddditor_save_new_template',
            method: 'POST',
            data: {
                type: element.type,
                values: element.values
            }
        }).success(function(reply) {
            _this.savedTemplates[reply.template] = angular.copy(reply);
            element.isLoading = undefined;
            element.template = reply.template;
            _this.watchTemplate(element);
        });
    };
    
    
    /**
     * 
     */
    this.updateTemplate = function() {
        var values = jQuery('#eddditor-edit').serializeObject();
        
        // copy editing.element so state can be reset while ajax is still loading
        var editingElement = state.getElement();
        state.reset();
        editingElement.isLoading = true;
        
        $http({
            url: ajaxurl + '?action=eddditor_update_template',
            method: 'POST',
            data: {
                template: editingElement.template,
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


    this.lowlightTemplate = function (element) {
        element.isHighlighted = undefined;
    };


    this.watchTemplate = function (element) {
        var id = element.template;
        if (_this.savedTemplates[id]) {
            $rootScope.$watch(function () {
                return _this.savedTemplates[id];
            }, function (value) {
                var copy = angular.copy(value);
                copy.options = element.options;
                angular.extend(element, copy);
            }, true);
        }
    };
    
});