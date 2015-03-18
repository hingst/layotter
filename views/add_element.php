<div class="eddditor-modal" ng-controller="FormCtrl">
    <div class="eddditor-modal-head">
        <h2><?php _e('Add element', 'eddditor'); ?></h2>
    </div>
    <div class="eddditor-modal-body">
        <?php

        $post_id = get_the_ID();
        $elements = Eddditor::get_filtered_element_types($post_id);

        foreach ($elements as $element) {
            ?>
                <div class="eddditor-modal-add-element" ng-click="selectNewElementType('<?php echo $element->get('type'); ?>')">
                    <div class="eddditor-modal-add-element-icon">
                        <i class="fa fa-<?php echo $element->get('icon'); ?>"></i>
                    </div>
                    <div class="eddditor-modal-add-element-info">
                        <h3><?php echo $element->get('title'); ?></h3>
                        <?php echo $element->get('description'); ?>
                    </div>
                </div>
            <?php
        }

        ?>
    </div>
    <div class="eddditor-modal-foot">
        <span class="button button-large" ng-click="cancelEditing()"><?php _e('Cancel', 'eddditor'); ?></span>
    </div>
</div>