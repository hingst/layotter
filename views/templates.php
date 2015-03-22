<div id="eddditor-templates" ng-controller="TemplatesCtrl">
    <span class="eddditor-templates-close" toggle-templates title="<?php _e('Close', 'eddditor'); ?>"><?php _e('Close', 'eddditor'); ?></span>
    <div class="eddditor-templates-head">
        <h3>
            <?php _e('Element gallery', 'eddditor'); ?>
        </h3>
        <p>
            <a href="javascript:void()"><?php _e('What is this?', 'eddditor'); ?></a>
        </p>
    </div>
    <div class="eddditor-elements" ui-sortable="templateSortableOptions" ng-model="savedTemplates">
        <div class="eddditor-element eddditor-animate" ng-repeat="element in savedTemplates" ng-mouseenter="highlightTemplate(element)" ng-mouseleave="lowlightTemplate(element)" ng-class="{ 'eddditor-loading' : element.isLoading }">
            <div class="eddditor-element-canvas">
                <div class="eddditor-element-head">
                    <span class="eddditor-element-delete" ng-click="deleteTemplate($index)" title="<?php _e('Remove from favorites', 'eddditor'); ?>"><?php _e('Remove from favorites', 'eddditor'); ?></span>
                    <span class="eddditor-element-edit" ng-click="editTemplate(element)" title="<?php _e('Edit favorite', 'eddditor'); ?>"><?php _e('Edit favorite', 'eddditor'); ?></span>
                    <span class="eddditor-element-move"><?php _e('Move', 'eddditor'); ?></span>
                </div>
                <div class="eddditor-element-content" ng-bind-html="element.view"></div>
            </div>
            <span class="eddditor-button eddditor-add-element-button"></span>
        </div>
    </div>
</div>