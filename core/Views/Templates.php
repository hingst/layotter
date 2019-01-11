<?php

namespace Layotter\Views;

/**
 * View for the templates side bar
 */
class Templates {

    /**
     * Output view
     */
    public static function view() {
        ?>
        <div id="layotter-templates" ng-controller="TemplatesCtrl">
            <span class="layotter-templates-close" toggle-templates title="<?php _e('Hide templates', 'layotter'); ?>"><i class="fa fa-arrow-right"></i></span>
            <div class="layotter-templates-head">
                <h3><?php _e('Element templates', 'layotter'); ?></h3>
            </div>
            <div class="layotter-elements" ui-sortable="templateSortableOptions" ng-model="savedTemplates">
                <div class="layotter-element layotter-element-{{$index}} layotter-animate" ng-repeat="element in savedTemplates" ng-mouseenter="highlightTemplate(element)" ng-mouseleave="unhighlightTemplate(element)" ng-class="{ 'layotter-loading' : element.isLoading }" ng-init="watchTemplate(element)">
                    <div class="layotter-element-canvas">
                        <div class="layotter-element-buttons">
                            <span class="layotter-element-button" ng-click="deleteTemplate($index)" title="<?php _e('Delete template', 'layotter'); ?>"><i class="fa fa-trash"></i></span>
                            <span class="layotter-element-button" ng-click="editTemplate(element)" title="<?php _e('Edit template', 'layotter'); ?>"><i class="fa fa-edit"></i></span>
                        </div>
                        <div class="layotter-element-content" ng-bind-html="element.view"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
