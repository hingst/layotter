<div class="layotter-modal" ng-controller="FormCtrl">
    <form action="" id="layotter-edit">
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
            <div id="acf-form-data" class="acf-postbox">
                <div class="inside acf-fields">
                    <div id="acf-form-data" class="acf-hidden">
                        <input type="hidden" name="_acfnonce" value="{{ form.nonce }}">
                        <input id="layotter-changed" type="hidden" name="_acfchanged" value="0">
                    </div>
                    <div class="acf-fields" ng-bind-html="form.fields | rawHtml"></div>
                </div>
            </div>
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