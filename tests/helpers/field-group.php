<?php

use \Layotter\Acf\Adapter;

if (Adapter::is_pro_installed()) {
    acf_add_local_field_group(array(
        'key' => 'group_test',
        'title' => '',
        'fields' => array(
            array(
                'key' => 'field_test',
                'label' => '',
                'name' => 'content',
                'type' => 'wysiwyg',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'layotter',
                    'operator' => '==',
                    'value' => 'element',
                ),
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));
    acf_add_local_field_group(array(
        'key' => 'group_597e17d33715a',
        'title' => 'options',
        'fields' => array(
            array(
                'key' => 'field_597e17d7a2ed1',
                'label' => 'text',
                'name' => 'text',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'layotter',
                    'operator' => '==',
                    'value' => 'element_options',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));
    // TODO: non Pro field group
} else {
    register_field_group(array(
        'id' => 'group_test',
        'title' => '',
        'fields' => array(
            array(
                'key' => 'field_test',
                'label' => '',
                'name' => 'content',
                'type' => 'wysiwyg',
                'default_value' => '',
                'toolbar' => 'full',
                'media_upload' => 'yes',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'layotter',
                    'operator' => '==',
                    'value' => 'element',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array(
            'position' => 'normal',
            'layout' => 'no_box',
            'hide_on_screen' => array(),
        ),
        'menu_order' => 0,
    ));
}