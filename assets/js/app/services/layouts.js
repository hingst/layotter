app.service('layouts', function($rootScope, $http, $animate, $timeout, data, forms, modals, state){
    

    var _this = this;
    
    
    // data received from php
    this.savedLayouts = eddditorData.savedLayouts;


    this.saveNewLayout = function() {
        var json = angular.toJson(data.contentStructure);

       modals.prompt({
            message: eddditorData.i18n.save_new_layout_confirmation,
            initialValue: angular.element('#title').val(),
            okText: eddditorData.i18n.save_layout,
            okAction: function(value) {
                angular.element('.eddditor-save-layout-button').parent().addClass('eddditor-loading');
                $http({
                    url: ajaxurl + '?action=eddditor_save_new_layout',
                    method: 'POST',
                    data: {
                        name: value,
                        json: json
                    }
                }).success(function(reply) {
                    _this.savedLayouts.push(reply);
                    angular.element('.eddditor-save-layout-button').parent().removeClass('eddditor-loading');
                });
            },
            cancelText: eddditorData.i18n.cancel
        });
    };


    this.loadLayout = function() {
        forms.show(angular.element('#eddditor-load-layout').html());
    };


    this.selectSavedLayout = function(layout) {
        var id = layout.layout_id;

        modals.confirm({
            message: eddditorData.i18n.load_layout_confirmation,
            okText: eddditorData.i18n.load_layout,
            okAction: function(){
                state.reset();
                angular.element('#eddditor').addClass('eddditor-loading');

                $http({
                    url: ajaxurl + '?action=eddditor_load_layout',
                    method: 'POST',
                    data: {
                        layout_id: id
                    }
                }).success(function(reply) {
                    $animate.enabled(false);
                    data.contentStructure.options = reply.options;
                    data.contentStructure.rows = reply.rows;
                    $timeout(function(){
                        $animate.enabled(true);
                    }, 1);
                    angular.element('#eddditor').removeClass('eddditor-loading');
                });
            },
            cancelText: eddditorData.i18n.cancel
        });
    };


    this.renameLayout = function(index, $event) {
        $event.stopPropagation();
        var layout = _this.savedLayouts[index];

        modals.prompt({
            message: eddditorData.i18n.rename_layout_confirmation,
            initialValue: layout.name,
            okText: eddditorData.i18n.rename_layout,
            okAction: function(value) {
                var id = layout.layout_id;
                layout.isLoading = true;

                $http({
                    url: ajaxurl + '?action=eddditor_rename_layout',
                    method: 'POST',
                    data: {
                        layout_id: id,
                        name: value
                    }
                }).success(function(reply) {
                    layout.isLoading = undefined;
                    layout.name = reply.name;
                });
            },
            cancelText: eddditorData.i18n.cancel
        });
    };


    this.deleteLayout = function(index, $event) {
        $event.stopPropagation();
        var layout = _this.savedLayouts[index];

        modals.confirm({
            message: eddditorData.i18n.delete_layout_confirmation,
            okText: eddditorData.i18n.delete_layout,
            okAction: function(){
                var id = layout.layout_id;
                layout.isLoading = true;

                $http({
                    url: ajaxurl + '?action=eddditor_delete_layout',
                    method: 'POST',
                    data: {
                        layout_id: id
                    }
                }).success(function() {
                    layout.isDeleted = true;
                });
            },
            cancelText: eddditorData.i18n.cancel
        });
    };
    
});