<?php

/**
 * Register the field group required for the example element that comes with Layotter
 */
if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array (
        'key' => 'group_5605a65191086',
        'title' => 'Example element for Layotter',
        'fields' => array (
            array (
                'key' => 'field_5605a6602418d',
                'label' => __('Thank you for trying out Layotter!', 'layotter'),
                'name' => '',
                'type' => 'message',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => __("This is an example element you can use to get started with Layotter. When you're ready to create your own element types, go to the settings page to disable it.", 'layotter'),
                'esc_html' => 0,
            ),
            array (
                'key' => 'field_5605a6ed2418e',
                'label' => __('Content', 'layotter'),
                'name' => 'content',
                'type' => 'wysiwyg',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => __("Welcome to the text editor! Write something, insert links or images, and click save when you're done.", 'layotter') . "\n\n" . __("By the way, Layotter isn't limited to text fields. You can create all kinds of content, like embedded Google maps, image galleries, file uploads, and much more!", 'layotter'),
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'layotter',
                    'operator' => '==',
                    'value' => 'element',
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

endif;