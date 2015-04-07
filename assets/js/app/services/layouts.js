app.service('layouts', function($rootScope, $http, $animate, $timeout, data, forms, modals, state, history){
    

    var _this = this;
    
    
    // data received from php
    this.savedLayouts = layotterData.savedLayouts;


    this.saveNewLayout = function() {
        var json = angular.toJson(data.contentStructure);

       modals.prompt({
            message: layotterData.i18n.save_new_layout_confirmation,
            initialValue: angular.element('#title').val(),
            okText: layotterData.i18n.save_layout,
            okAction: function(value) {
                angular.element('.layotter-save-layout-button-wrapper').addClass('layotter-loading');
                $http({
                    url: ajaxurl + '?action=layotter_save_new_layout',
                    method: 'POST',
                    data: {
                        name: value,
                        json: json
                    }
                }).success(function(reply) {
                    _this.savedLayouts.push(reply);
                    angular.element('.layotter-save-layout-button-wrapper').removeClass('layotter-loading');
                });
            },
            cancelText: layotterData.i18n.cancel
        });
    };


    this.loadLayout = function() {
        forms.show(angular.element('#layotter-load-layout').html());
    };


    this.selectSavedLayout = function(layout) {
        var id = layout.layout_id;

        if (data.contentStructure.rows.length === 0) {
            _this.loadSelectedLayout(id);
        } else {
            modals.confirm({
                message: layotterData.i18n.load_layout_confirmation,
                okText: layotterData.i18n.load_layout,
                okAction: function(){
                    _this.loadSelectedLayout(id);
                },
                cancelText: layotterData.i18n.cancel
            });
        }
    };


    this.loadSelectedLayout = function(id) {
        state.reset();
        angular.element('#layotter').addClass('layotter-loading');

        $http({
            url: ajaxurl + '?action=layotter_load_layout',
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
            angular.element('#layotter').removeClass('layotter-loading');
            history.pushStep(layotterData.i18n.history.load_post_layout);
        });
    };


    this.renameLayout = function(index, $event) {
        $event.stopPropagation();
        var layout = _this.savedLayouts[index];

        modals.prompt({
            message: layotterData.i18n.rename_layout_confirmation,
            initialValue: layout.name,
            okText: layotterData.i18n.rename_layout,
            okAction: function(value) {
                var id = layout.layout_id;
                layout.isLoading = true;

                $http({
                    url: ajaxurl + '?action=layotter_rename_layout',
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
            cancelText: layotterData.i18n.cancel
        });
    };


    this.deleteLayout = function(index, $event) {
        $event.stopPropagation();
        var layout = _this.savedLayouts[index];

        modals.confirm({
            message: layotterData.i18n.delete_layout_confirmation,
            okText: layotterData.i18n.delete_layout,
            okAction: function(){
                var id = layout.layout_id;
                layout.isLoading = true;

                $http({
                    url: ajaxurl + '?action=layotter_delete_layout',
                    method: 'POST',
                    data: {
                        layout_id: id
                    }
                }).success(function() {
                    layout.isDeleted = true;
                });
            },
            cancelText: layotterData.i18n.cancel
        });
    };
    
});