<div class="eddditor-modal" ng-controller="FormCtrl">
    <form action="" id="eddditor-edit">
        <div class="eddditor-modal-head">
            <div class="eddditor-modal-head-icon">
                <i class="fa fa-<?php echo $icon; ?>"></i>
            </div>
	        <div class="eddditor-modal-head-info">
                <h2><?php echo $title; ?></h2>
	        </div>
        </div>
        <div class="eddditor-modal-body">
            <div id="acf-form-data" class="acf-postbox">
                <div class="inside acf-fields">
                    <div id="acf-form-data" class="acf-hidden">
                        <input type="hidden" name="_acfnonce" value="<?php echo wp_create_nonce('post'); ?>">
                        <input id="eddditor-changed" type="hidden" name="_acfchanged" value="0">
                    </div>
                    <?php acf_render_fields(0, $fields); ?>
                </div>
            </div>
        </div>
        <div class="eddditor-modal-foot">
            <button id="eddditor-edit-fake-submit" type="submit" class="button button-primary button-large"><?php _e('Save', 'eddditor'); ?></button>
            <span class="button button-large" ng-click="cancelEditing()"><?php _e('Cancel', 'eddditor'); ?></span>
	        <span class="button button-large" ng-click="backToShowNewElementTypes()" ng-show="showBackButton"><?php _e('Back', 'eddditor'); ?></span>
        </div>
        <span id="eddditor-edit-submit" class="hidden" ng-click="saveForm()">triggered by ACF after validation</span>
    </form>
</div>