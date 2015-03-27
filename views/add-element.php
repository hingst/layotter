<div class="layotter-modal" ng-controller="FormCtrl">
    <div class="layotter-modal-head">
        <h2><?php _e('Add element', 'layotter'); ?></h2>
    </div>
    <div class="layotter-modal-body">
        <?php
        $post_id = get_the_ID();
        $elements = Layotter::get_filtered_element_types($post_id);

        foreach ($elements as $element) {
            ?>
                <div class="layotter-modal-add-element" ng-click="selectNewElementType('<?php echo $element->get('type'); ?>')">
                    <div class="layotter-modal-add-element-icon">
                        <i class="fa fa-<?php echo $element->get('icon'); ?>"></i>
                    </div>
                    <div class="layotter-modal-add-element-info">
                        <h3><?php echo $element->get('title'); ?></h3>
                        <?php echo $element->get('description'); ?>
                    </div>
                </div>
            <?php
        }
        ?>
    </div>
    <div class="layotter-modal-foot">
        <button type="button" class="button button-large" ng-click="cancelEditing()"><?php _e('Cancel', 'layotter'); ?></button>
    </div>
</div>