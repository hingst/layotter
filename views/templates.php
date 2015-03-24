<div id="eddditor-templates" ng-controller="TemplatesCtrl">
    <span class="eddditor-templates-close" toggle-templates title="<?php _e('Hide templates', 'eddditor'); ?>"><i class="fa fa-arrow-right"></i></span>
    <div class="eddditor-templates-head">
        <h3>
            <?php _e('Element templates', 'eddditor'); ?>
        </h3>
    </div>
    <div class="eddditor-elements" ui-sortable="templateSortableOptions" ng-model="savedTemplates">
        <div class="eddditor-element eddditor-animate" ng-repeat="element in savedTemplates" ng-mouseenter="highlightTemplate(element)" ng-mouseleave="lowlightTemplate(element)" ng-class="{ 'eddditor-loading' : element.isLoading }" ng-init="watchTemplate(element)">
            <div class="eddditor-element-canvas">
                <div class="eddditor-element-head">
                    <span class="eddditor-element-delete" ng-click="deleteTemplate($index)" title="<?php _e('Delete template', 'eddditor'); ?>"><i class="fa fa-trash-o"></i></span>
                    <span class="eddditor-element-edit" ng-click="editTemplate(element)" title="<?php _e('Edit template', 'eddditor'); ?>"><i class="fa fa-pencil"></i></span>
                    <span class="eddditor-element-move"><i class="fa fa-arrows"></i><?php _e('Move', 'eddditor'); ?></span>
                </div>
                <div class="eddditor-element-content" ng-bind-html="element.view"></div>
            </div>
            <span class="eddditor-button eddditor-add-element-button"></span>
        </div>
    </div>
</div>