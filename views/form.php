<div class="layotter-modal" ng-controller="FormCtrl">
    <form action="" id="layotter-edit">
        <div class="layotter-modal-head">
            <div class="layotter-modal-head-icon">
                <i class="fa fa-<?php echo $icon; ?>"></i>
            </div>
            <div class="layotter-modal-head-info">
                <h2><?php echo $title; ?></h2>
            </div>
        </div>
        <div class="layotter-modal-body">
            <div id="acf-form-data" class="acf-postbox">
                <div class="inside acf-fields">
                    <div id="acf-form-data" class="acf-hidden">
                        <input type="hidden" name="_acfnonce" value="<?php echo wp_create_nonce('post'); ?>">
                        <input id="layotter-changed" type="hidden" name="_acfchanged" value="0">
                    </div>
                    <?php acf_render_fields(0, $fields); ?>
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