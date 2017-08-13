<?php

namespace Layotter\Example;

use Layotter\Acf\Adapter;
use Layotter\Settings;

/**
 * Used to register the example element
 */
class FieldGroup {

    /**
     * Register field group that's used by the example element
     */
    public static function register() {
        $key = Adapter::get_example_field_group_name();
        $default_text = __("Welcome to the text editor! Write something, insert links or images, and click save when you're done.", 'layotter') . "\n\n" . __("By the way, Layotter isn't limited to text fields. You can create all kinds of content, like embedded Google maps, image galleries, file uploads, and much more!", 'layotter');
        $content_label = __('Content', 'layotter');

        $location_rule = 'hidden';
        if (Settings::example_element_enabled()) {
            $location_rule = 'element';
        }

        if (Adapter::is_pro_installed()) {
            acf_add_local_field_group([
                'key' => $key,
                'title' => '',
                'fields' => [
                    [
                        'key' => 'field_5605a6ed2418e',
                        'label' => $content_label,
                        'name' => 'content',
                        'type' => 'wysiwyg',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ],
                        'default_value' => $default_text,
                        'tabs' => 'all',
                        'toolbar' => 'full',
                        'media_upload' => 1,
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'layotter',
                            'operator' => '==',
                            'value' => $location_rule,
                        ],
                    ],
                ],
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            ]);
        } else {
            register_field_group([
                'id' => $key,
                'title' => '',
                'fields' => [
                    [
                        'key' => 'field_58011c654f032',
                        'label' => $content_label,
                        'name' => 'content',
                        'type' => 'wysiwyg',
                        'default_value' => $default_text,
                        'toolbar' => 'full',
                        'media_upload' => 'yes',
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'layotter',
                            'operator' => '==',
                            'value' => $location_rule,
                            'order_no' => 0,
                            'group_no' => 0,
                        ],
                    ],
                ],
                'options' => [
                    'position' => 'normal',
                    'layout' => 'no_box',
                    'hide_on_screen' => [],
                ],
                'menu_order' => 0,
            ]);
        }
    }
}
