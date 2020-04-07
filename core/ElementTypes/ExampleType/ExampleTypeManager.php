<?php

namespace Layotter\ElementTypes\ExampleType;

use Layotter\Acf\Adapter;
use Layotter\Repositories\ElementTypeRepository;
use Layotter\Settings;

class ExampleTypeManager {

    public static function register() {
        add_action('after_setup_theme', [__CLASS__, 'include_example_element']);
    }

    public static function include_example_element() {
        self::register_field_group();
        ElementTypeRepository::register('layotter_example_element', ExampleType::class);
    }

    public static function register_field_group() {
        $default_text = __("Welcome to the text editor! Write something, insert links or images, and click save when you're done.", 'layotter') . "\n\n" . __("By the way, Layotter isn't limited to text fields. You can create all kinds of content, like embedded Google maps, image galleries, file uploads, and much more!", 'layotter');
        $content_label = __('Content', 'layotter');

        // hide if disabled in settings, but still register the group so that existing elements keep working
        $location_rule = Settings::example_element_enabled() ? 'element' : 'hidden';

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
