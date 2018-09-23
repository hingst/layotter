<?php

namespace Layotter\Views;

/**
 * View for the "Add Element" popup screen
 */
class AddElement {

    /**
     * Output view
     */
    public static function view() {
        ?>
        <div class="layotter-modal" ng-controller="ModalCtrl">
            <div class="layotter-modal-head">
                <div class="layotter-modal-head-icon">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="layotter-modal-head-info">
                    <h2><?php _e('Add element', 'layotter'); ?></h2>
                </div>
            </div>
            <div class="layotter-modal-body">
                <div class="layotter-modal-add-element" ng-repeat="element in elementTypes" ng-click="selectNewElementType(element.type)">
                    <div class="layotter-modal-add-element-icon">
                        <i class="fa fa-{{ element.icon }}"></i>
                    </div>
                    <div class="layotter-modal-add-element-info">
                        <h3>{{ element.title }}</h3>
                        {{ element.description }}
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
