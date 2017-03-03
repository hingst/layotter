<?php

namespace Layotter\Example;
use Layotter\Acf\Adapter;

/**
 * Register the field group required for the example element that comes with Layotter
 */
$key = Adapter::get_example_field_group_name();
$title = __('Example element for Layotter', 'layotter');
$message_label = __('Thank you for trying out Layotter!', 'layotter');
$message = __("This is an example element you can use to get started with Layotter. When you're ready to create your own element types, go to the settings page to disable it.", 'layotter');
$default_text = __("Welcome to the text editor! Write something, insert links or images, and click save when you're done.", 'layotter') . "\n\n" . __("By the way, Layotter isn't limited to text fields. You can create all kinds of content, like embedded Google maps, image galleries, file uploads, and much more!", 'layotter');
$content_label = __('Content', 'layotter');
if (Adapter::is_pro_installed()) {
    acf_add_local_field_group(array(
        'key' => $key,
        'title' => $title,
        'fields' => array(
            array(
                'key' => 'field_5605a6602418d',
                'label' => $message_label,
                'name' => '',
                'type' => 'message',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => $message,
                'esc_html' => 0,
            ),
            array(
                'key' => 'field_5605a6ed2418e',
                'label' => $content_label,
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
                'default_value' => $default_text,
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
} else {
    register_field_group(array (
        'id' => $key,
        'title' => $title,
        'fields' => array (
            array (
                'key' => 'field_58011c544f031',
                'label' => $message_label,
                'name' => '',
                'type' => 'message',
                'message' => $message,
            ),
            array (
                'key' => 'field_58011c654f032',
                'label' => $content_label,
                'name' => 'content',
                'type' => 'wysiwyg',
                'default_value' => $default_text,
                'toolbar' => 'full',
                'media_upload' => 'yes',
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'layotter',
                    'operator' => '==',
                    'value' => 'element',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array (
            'position' => 'normal',
            'layout' => 'no_box',
            'hide_on_screen' => array (
            ),
        ),
        'menu_order' => 0,
    ));
}