<?php

namespace Layotter;

use Layotter\Acf\Adapter;
use Layotter\Acf\LocationRulesManager;
use Layotter\Ajax\RequestManager;
use Layotter\ElementTypes\BaseElementType;
use Layotter\ElementTypes\ExampleType\ExampleTypeManager;
use Layotter\Repositories\ElementTypeRepository;
use Layotter\Repositories\PostRepository;
use Layotter\Upgrades\PluginMigrator;

class Initializer {

    /**
     * Minimum required ACF version
     */
    const REQUIRED_ACF_VERSION = '5.7.12';

    /**
     * The current model version used to determine if upgrades have to be performed
     */
    const MODEL_VERSION = '2.0.0';

    /**
     * Post type for elements
     */
    const POST_TYPE_ELEMENT = 'layotter_element';

    /**
     * Post type for options
     */
    const POST_TYPE_OPTIONS = 'layotter_options';

    /**
     * Post type for saved layouts
     */
    const POST_TYPE_LAYOUT = 'layotter_post_layout';

    /**
     * Name of a post's meta field that keeps the element type
     */
    const META_FIELD_ELEMENT_TYPE = 'layotter_element_type';

    /**
     * Name of a post's meta field that flags an element as a template
     */
    const META_FIELD_IS_TEMPLATE = 'layotter_is_template';

    /**
     * Name of a layout's meta field that keeps the post type that the layout applies to
     */
    const META_FIELD_LAYOUT_FOR_POST_TYPE = 'layotter_post_type';

    /**
     * Name of a post's meta field that keeps Layotter's serialized content information
     */
    const META_FIELD_JSON = 'layotter_json';

    /**
     * Name of a post's meta field that keeps the post's model version
     */
    const META_FIELD_MODEL_VERSION = 'layotter_model_version';

    /**
     * Name of the textarea where Layotter serializes post information
     */
    const TEXTAREA_NAME = 'layotter_json';

    /**
     * Entry point.
     */
    public static function run() {
        load_plugin_textdomain('layotter', false, basename(__DIR__) . '/languages/');
        Settings::init();

        // class aliases for backwards compatibility
        // Layotter::register_element replaced by ElementTypeRepository::register
        // Layotter_Element as a base class replaced by ElementType
        class_alias(ElementTypeRepository::class, 'Layotter');
        class_alias(BaseElementType::class, 'Layotter_Element');

        if (!Adapter::meets_requirements()) {
            add_action('admin_notices', [Adapter::class, 'print_error']);
            return;
        }

        // takes care of saving custom fields for each post revision
        // see https://wordpress.org/plugins/wp-post-meta-revisions/
        if (!class_exists('WP_Post_Meta_Revisioning')) {
            require_once __DIR__ . '/../lib/wp-post-meta-revisions.php';
        }

        ExampleTypeManager::register();
        LocationRulesManager::register();
        RequestManager::register();

        add_action('init', [PluginMigrator::class, 'upgrade_on_demand']);

        add_action('admin_enqueue_scripts', [Assets::class, 'enqueue_backend_assets']);
        add_action('admin_footer', [Assets::class, 'backend_localization']);
        add_action('admin_footer', [Assets::class, 'print_views']);
        add_action('wp_enqueue_scripts', [Assets::class, 'enqueue_frontend_assets']);

        add_action('admin_head', [Editor::class, 'register_editor']);
        add_filter('use_block_editor_for_post', [Editor::class, 'should_use_gutenberg_for_post'], 10, 2);
        add_filter('wp_post_revision_meta_keys', [Editor::class, 'track_custom_field']);

        add_filter('wp_insert_post_data', [PostRepository::class, 'insert_post_data'], 999, 2);

        add_shortcode('layotter', [Shortcode::class, 'register']);
        add_filter('no_texturize_shortcodes', [Shortcode::class, 'disable_wptexturize']);
        add_filter('the_content', [Shortcode::class, 'disable_wpautop'], 1);
    }
}
