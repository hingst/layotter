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
        $default_text = __("Welcome to the text editor! Write something, insert links or images, and click save when you're done.", 'layotter') . "\n\n" . __("By the way, Layotter isn't limited to text fields. You can create all kinds of content, like embedded Google maps, image galleries, file uploads, and much more!", 'layotter');
        $content_label = __('Content', 'layotter');

        // hide if disabled in settings, but still register the group so that existing elements keep working
        $location_rule = 'hidden';
        if (Settings::example_element_enabled()) {
            $location_rule = 'element';
        }

        acf_add_local_field_group([
            'key' => Adapter::get_example_field_group_key(),
            'title' => '',
            'fields' => [
                [
                    'key' => Adapter::get_example_field_group_field_key(),
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
    }
}
