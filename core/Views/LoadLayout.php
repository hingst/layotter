<?php

namespace Layotter\Views;

/**
 * View for the "Load Layout" modal
 */
class LoadLayout {

    /**
     * Output view
     */
    public static function view() {
        ?>
        <div class="layotter-modal" ng-controller="ModalCtrl">
            <div class="layotter-modal-head">
                <div class="layotter-modal-head-icon">
                    <i class="fa fa-upload"></i>
                </div>
                <div class="layotter-modal-head-info">
                    <h2><?php _e('Load layout', 'layotter'); ?></h2>
                </div>
            </div>
            <div class="layotter-modal-body">
                <div class="layotter-modal-load-layout-wrapper" ng-repeat="layout in savedLayouts" ng-class="{ 'layotter-loading' : layout.isLoading }">
                    <div class="layotter-modal-load-layout" ng-click="selectSavedLayout(layout)">
                        <div class="layotter-modal-load-layout-header">
                            <h3>{{ layout.name }}</h3>
                            <span class="layotter-layout-rename" ng-click="renameLayout($index, $event)" title="<?php _e('Rename layout', 'layotter'); ?>"><i class="fa fa-edit"></i></span>
                            <span class="layotter-layout-delete" ng-click="deleteLayout($index, $event)" title="<?php _e('Delete layout', 'layotter'); ?>"><i class="fa fa-trash"></i></span>
                        </div>
                        <div class="layotter-modal-load-layout-info">
                            <?php printf(__('Created on %s', 'layotter'), "{{ layout.time_created * 1000 | date:'short' }}"); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layotter-modal-foot">
                <button type="button" class="button button-large" ng-click="cancelEditing()"><?php _e('Cancel', 'layotter'); ?></button>
            </div>
        </div>
        <?php
    }
}
