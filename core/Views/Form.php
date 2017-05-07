<?php

namespace Layotter\Views;

use Layotter\Acf\Adapter;

/**
 * View for edit forms
 */
class Form {

    /**
     * Output view
     */
    public static function view() {
        ?>
        <div class="layotter-modal" ng-controller="ModalCtrl">
            <form action="" id="<?php echo Adapter::get_form_id(); ?>">
                <div class="layotter-modal-head">
                    <div class="layotter-modal-head-icon">
                        <i class="fa fa-{{ form.icon }}"></i>
                    </div>
                    <div class="layotter-modal-head-info">
                        <h2>{{ form.title }}</h2>
                    </div>
                    <div class="layotter-modal-head-fullscreen">
                        <span class="layotter-modal-head-fullscreen-expand" ng-click="toggleFullscreen()" title="<?php __('Switch to fullscreen editor', 'layotter'); ?>"><i class="fa fa-expand"></i></span>
                        <span class="layotter-modal-head-fullscreen-compress hidden" ng-click="toggleFullscreen()" title="<?php __('Switch to small editor', 'layotter'); ?>"><i class="fa fa-compress"></i></span>
                    </div>
                </div>
                <div class="layotter-modal-body">
                    <?php Adapter::output_form_wrapper(); ?>
                </div>
                <div class="layotter-modal-loading-container">
                </div>
                <div class="layotter-modal-foot">
                    <button type="submit" class="button button-primary button-large"><?php _e('Save', 'layotter'); ?></button>
                    <button type="button" class="button button-large" ng-click="cancelEditing()"><?php _e('Cancel', 'layotter'); ?></button>
                    <button type="button" class="button button-large" ng-click="backToShowNewElementTypes()" ng-show="showBackButton"><?php _e('Back', 'layotter'); ?></button>
                </div>
                <span id="layotter-edit-submit" class="hidden" ng-click="saveForm()">triggered by ACF after validation</span>
            </form>
        </div>
        <?php
    }
}
