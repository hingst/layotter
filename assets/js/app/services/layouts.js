/**
 * All things related to post layouts
 */
app.service('layouts', function($rootScope, $http, $animate, $timeout, data, forms, modals, state, history){
    

    var _this = this;
    
    
    // data received from php
    this.savedLayouts = layotterData.savedLayouts;


    /**
     * Save current post content as a new layout
     */
    this.saveNewLayout = function() {
        var json = angular.toJson(data.contentStructure);

        modals.prompt({
            message: layotterData.i18n.save_new_layout_confirmation,
            initialValue: angular.element('#title').val(),
            okText: layotterData.i18n.save_layout,
            okAction: function(value) {
                var postData = 'name=' + encodeURIComponent(value) + '&json=' + encodeURIComponent(json);
                angular.element('.layotter-save-layout-button-wrapper').addClass('layotter-loading');
                $http({
                    url: ajaxurl + '?action=layotter_save_new_layout',
                    method: 'POST',
                    data: postData,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).success(function(reply) {
                    _this.savedLayouts.push(reply);
                    angular.element('.layotter-save-layout-button-wrapper').removeClass('layotter-loading');
                });
            },
            cancelText: layotterData.i18n.cancel
        });
    };


    /**
     * Show a list of existing post layouts
     */
    this.loadLayout = function() {
        forms.showHTML(angular.element('#layotter-load-layout').html());
    };


    /**
     * Triggered when selecting an existing post layout to be loaded
     */
    this.selectSavedLayout = function(layout) {
        var id = layout.layout_id;

        // ask for confirmation if current post content is not empy
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


    /**
     * Fetch data for an existing post layout and overwrite current content
     */
    this.loadSelectedLayout = function(id) {
        state.reset();
        angular.element('#layotter').addClass('layotter-loading');

        $http({
            url: ajaxurl + '?action=layotter_load_layout',
            method: 'POST',
            data: 'layout_id=' + id,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
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


    /**
     * Rename an existing post layout
     */
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
                    data: 'layout_id=' + id + '&name=' + value,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).success(function(reply) {
                    layout.isLoading = undefined;
                    layout.name = reply.name;
                });
            },
            cancelText: layotterData.i18n.cancel
        });
    };


    /**
     * Delete a post layout
     */
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
                    data: 'layout_id=' + id,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).success(function() {
                    _this.savedLayouts.splice(index, 1);
                });
            },
            cancelText: layotterData.i18n.cancel
        });
    };
    
});