<div class="eddditor-modal" ng-controller="FormCtrl">
    <div class="eddditor-modal-head">
        <h2><?php _e('Add element', 'eddditor'); ?></h2>
    </div>
    <div class="eddditor-modal-body">
        <?php

        $element_types = Eddditor::get_registered_elements();

        foreach($element_types as $element_type)
        {
            $element = Eddditor::create_element($element_type);
            if(!$element)
            {
                continue;
            }

            ?>
                <div class="eddditor-modal-add-element" ng-click="selectNewElementType('<?php echo $element->get('type'); ?>')">
                    <div class="eddditor-modal-add-element-icon">
                        <i class="fa <?php echo $element->get('icon'); ?>"></i>
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