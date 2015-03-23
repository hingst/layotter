<div class="eddditor-modal" ng-controller="FormCtrl">
    <div class="eddditor-modal-head">
        <div class="eddditor-modal-head-icon">
            <i class="fa fa-upload"></i>
        </div>
        <div class="eddditor-modal-head-info">
            <h2><?php _e('Load a layout', 'eddditor'); ?></h2>
        </div>
    </div>
    <div class="eddditor-modal-body">
        <div class="eddditor-modal-load-layout-wrapper" ng-repeat="layout in savedLayouts" ng-class="{ 'eddditor-loading' : layout.isLoading }" ng-hide="layout.isDeleted">
            <div class="eddditor-modal-load-layout" ng-click="selectSavedLayout(layout)">
                <div class="eddditor-modal-load-layout-header">
                    <h3>{{ layout.name }}</h3>
                    <span class="eddditor-layout-rename" ng-click="renameLayout($index, $event)" title="<?php _e('Rename layout', 'eddditor'); ?>"><i class="fa fa-pencil"></i></span>
                    <span class="eddditor-layout-delete" ng-click="deleteLayout($index, $event)" title="<?php _e('Delete layout', 'eddditor'); ?>"><i class="fa fa-trash-o"></i></span>
                </div>
                <div class="eddditor-modal-load-layout-info">
                    <?php printf(__('Created on %s', 'eddditor'), "{{ layout.time_created * 1000 | date:'short' }}"); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="eddditor-modal-foot">
        <span class="button button-large" ng-click="cancelEditing()"><?php _e('Cancel', 'eddditor'); ?></span>
    </div>
</div>