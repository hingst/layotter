<?php

namespace Layotter;

use Layotter\Acf\Adapter;
use Layotter\Components\Element;
use Layotter\Components\Options;
use Layotter\Components\Post;
use Layotter\Upgrades\PluginMigrator;
use Layotter\Views\Editor;

/**
 * Holds registered element types and serves as a factory for element instances
 */
class Core {

    /**
     * The current model version
     */
    const CURRENT_MODEL_VERSION = '2.0.0';

    /**
     * Name of the meta field keeping an Editable's type
     */
    const META_FIELD_EDITABLE_TYPE = 'layotter_editable_type';

    /**
     * Name of the meta field that flags an element as a template
     */
    const META_FIELD_IS_TEMPLATE = 'layotter_is_template';

    /**
     * Name of the meta field keeping an Editable's JSON
     */
    const META_FIELD_JSON = 'layotter_json';

    /**
     * Name of the meta field keeping an Editable's model version
     */
    const META_FIELD_MODEL_VERSION = 'layotter_model_version';

    /**
     * Name of the post type for Editables
     */
    const POST_TYPE_EDITABLE = 'layotter_editable';

    /**
     * Name of the post type for post layouts
     */
    const POST_TYPE_LAYOUT = 'layotter_post_layout';

    /**
     * Minimum required ACF version
     */
    const REQUIRED_ACF_VERSION = '5.7.7';

    /**
     * Name of the textarea for a post's JSON
     */
    const TEXTAREA_NAME = 'layotter_json';

    /**
     * @var array Registered element types => their respective class names
     */
    private static $registered_element_types = [];

    /**
     * Entry point that registers all required actions and filters
     */
    public static function init() {
        load_plugin_textdomain('layotter', false, basename(__DIR__) . '/languages/');
        Settings::init();
        self::aliases();

        if (!Adapter::is_available()) {
            return;
        }

        self::includes();

        add_filter('use_block_editor_for_post_type', [__CLASS__, 'disable_gutenberg'], 10, 2);

        add_action('admin_head', [__CLASS__, 'hook_editor']);
        add_filter('wp_post_revision_meta_keys', [__CLASS__, 'track_custom_field']);
        add_action('after_setup_theme', [__CLASS__, 'include_example_element']);
        add_action('init', [__CLASS__, 'upgrade_on_demand']);
        add_filter('wp_insert_post_data', [__CLASS__, 'save_post'], 999, 2);

        add_action('admin_enqueue_scripts', ['Layotter\Assets', 'backend']);
        add_action('admin_footer', ['Layotter\Assets', 'backend_localization']);
        add_action('wp_enqueue_scripts', ['Layotter\Assets', 'frontend']);
        add_action('admin_footer', ['Layotter\Assets', 'views']);

        add_action('wp_ajax_layotter', ['Layotter\Ajax\Handler', 'handle']);

        add_filter('acf/location/rule_types', ['Layotter\Acf\LocationRules', 'category']);
        add_filter('acf/location/rule_values/layotter', ['Layotter\Acf\LocationRules', 'options']);
        add_filter('acf/location/rule_match/layotter', ['Layotter\Acf\LocationRules', 'match_rules'], 10, 3);

        add_shortcode('layotter', ['Layotter\Shortcode', 'register']);
        add_filter('the_content', ['Layotter\Shortcode', 'disable_wpautop'], 1);
        add_filter('no_texturize_shortcodes', ['Layotter\Shortcode', 'disable_wptexturize']);
    }

    /**
     * Disables Wordpress 5 block editor for Layotter-enabled posts.
     *
     * @param bool $enabled Previous setting for the post type
     * @param string $post_type The post type in question
     * @return bool
     */
    public static function disable_gutenberg($enabled, $post_type) {
        $layotter_post_types = Settings::get_enabled_post_types();

        if (in_array($post_type, $layotter_post_types)) {
            return false;
        }

        return $enabled;
    }

    /**
     * Include files
     */
    public static function includes() {
        // this library takes care of saving custom fields for each post revision
        // see https://wordpress.org/plugins/wp-post-meta-revisions/
        if (!class_exists('WP_Post_Meta_Revisioning')) {
            require_once __DIR__ . '/../lib/wp-post-meta-revisions.php';
        }
    }

    /**
     * Alias old class names for backwards compatibility
     */
    public static function aliases() {
        class_alias('Layotter\Core', 'Layotter');
        class_alias('Layotter\Components\Element', 'Layotter_Element');
    }

    /**
     * Track custom field in post revisions
     *
     * Track JSON data with the WP Post Meta Revisions plugin because Wordpress normally doesn't track custom fields
     *
     * @param $keys array Meta field names that are already being tracked
     * @return array Input with added Layotter JSON meta field
     */
    public static function track_custom_field($keys) {
        $keys[] = self::META_FIELD_JSON;
        return $keys;
    }

    /**
     * Upgrades the database if necessary
     */
    public static function upgrade_on_demand() {
        if (PluginMigrator::needs_upgrade()) {
            PluginMigrator::upgrade();
        }
    }

    /**
     * Include example element
     *
     * Hook up the example element even if it is disabled in settings so that
     * existing elements keep working. The element is simply hidden from the
     * "Add Element" screen.
     */
    public static function include_example_element() {
        Example\FieldGroup::register();
        self::register_element('layotter_example_element', '\Layotter\Example\Element');
    }

    /**
     * Build a search dump when saving a post, and save JSON to a custom field
     *
     * @param array $data Post data about to be saved to the database
     * @param array $raw_post Raw POST data from the edit screen
     * @return array Post data with modified post_content
     */
    public static function save_post($data, $raw_post) {
        $post_id = $raw_post['ID'];

        if (!self::is_enabled_for_post($post_id) || !isset($raw_post[ self::TEXTAREA_NAME ])) {
            return $data;
        }

        // strip slashes that were added by Wordpress
        $json = $raw_post[ self::TEXTAREA_NAME ];
        $unslashed_json = stripslashes_deep($json);

        // fetch search dump
        $layotter_post = new Post();
        $layotter_post->set_json($unslashed_json);
        $search_dump = '[layotter post="' . $post_id . '"]' . $layotter_post->get_search_dump() . '[/layotter]';

        // no addslashes() here because $json is still magic-quoted by Wordpress
        update_post_meta($post_id, Core::META_FIELD_JSON, $json);
        update_post_meta($post_id, self::META_FIELD_MODEL_VERSION, self::CURRENT_MODEL_VERSION);

        $data['post_content'] = $search_dump;
        return $data;
    }

    /**
     * Register a new element type
     *
     * @param string $type Unique type identifier
     * @param string $class Class name for this element type
     */
    public static function register_element($type, $class) {
        if (!is_string($type) || !is_string($class)) {
            Errors::invalid_argument_not_recoverable('type or class');
        }

        // fail if provided class name is not a valid class
        if (!class_exists($class) || !is_subclass_of($class, 'Layotter\Components\Element')) {
            Errors::invalid_argument_not_recoverable('class');
        }

        // We *should* fail if the provided type identifier is invalid, but for backwards compatibility
        // we'll have to keep going (in older versions the identifier was simply stripped of invalid
        // characters, so all these elements from older versions would break if we changed the logic now)
        $type = self::clean_type_identifier($type);
        if (empty($type) || isset(self::$registered_element_types[ $type ])) {
            Errors::invalid_argument_not_recoverable('type');
        }

        // no errors, register the new element type
        self::$registered_element_types[ $type ] = $class;

        // register element type's hooks for the backend (frontend hooks are registered on demand)
        call_user_func([$class, 'register_backend_hooks']);
    }

    /**
     * Get identifiers for all registered element types
     *
     * @return array
     */
    public static function get_registered_element_types() {
        return array_keys(self::$registered_element_types);
    }

    /**
     * Create a new element instance
     *
     * @param string $type Element type
     * @return Element
     */
    public static function assemble_new_element($type) {
        if (!is_string($type) || !isset(self::$registered_element_types[ $type ])) {
            Errors::invalid_argument_not_recoverable('type');
        }

        /** @var $element Element */
        $element = new self::$registered_element_types[ $type ]();
        $element->set_type($type);
        return $element;
    }

    /**
     * Create an instance from an existing element
     *
     * @param int $id Element ID
     * @param int $options_id Options ID
     * @return Element
     */
    public static function assemble_element($id, $options_id = 0) {
        if (!is_int($id) || !is_int($options_id)) {
            Errors::invalid_argument_not_recoverable('id or options_id');
        }

        $type = get_post_meta($id, self::META_FIELD_EDITABLE_TYPE, true);

        if (!is_string($type) || !isset(self::$registered_element_types[ $type ])) {
            Errors::invalid_argument_not_recoverable('type');
        }

        /** @var $element Element */
        $element = new self::$registered_element_types[ $type ]($id);
        $element->set_options($options_id);
        return $element;
    }

    /**
     * Create a new options instance
     *
     * @param string $type Options type
     * @return Options
     */
    public static function assemble_new_options($type) {
        if (!Options::is_valid_type($type)) {
            Errors::invalid_argument_not_recoverable('type');
        }

        $options = new Options();
        $options->set_type($type);
        return $options;
    }

    /**
     * Create an instance of existing options
     *
     * @param int $id Options ID
     * @return Options
     */
    public static function assemble_options($id) {
        if (!is_int($id)) {
            Errors::invalid_argument_not_recoverable('id');
        }

        $options = new Options($id);
        return $options;
    }

    /**
     * Remove illegal characters from a type identifier
     *
     * @param string $type Dirty type identifier
     * @return string Clean type identifier
     */
    private static function clean_type_identifier($type) {
        if (!is_string($type)) {
            return '';
        }

        return preg_replace('/[^a-z_]/', '', $type);
    }

    /**
     * Check if Layotter is enabled for the current screen
     *
     * @return bool
     */
    public static function is_enabled() {
        if (!is_admin()) {
            return false;
        }

        global $pagenow;
        if ($pagenow != 'post.php' && $pagenow != 'post-new.php') {
            return false;
        }

        if (!self::is_enabled_for_post(get_the_ID())) {
            return false;
        }

        return true;
    }

    /**
     * Check if Layotter is enabled for a specific post
     *
     * @param int $post_id Post ID
     * @return bool
     */
    public static function is_enabled_for_post($post_id) {
        if (!is_int($post_id)) {
            return false;
        }

        $override_enabled = apply_filters('layotter/enable_for_posts', []);
        $override_disabled = apply_filters('layotter/disable_for_posts', []);

        if (in_array($post_id, $override_enabled)) {
            return true;
        }

        if (in_array($post_id, $override_disabled)) {
            return false;
        }

        $post_type = get_post_type($post_id);
        $enabled_post_types = Settings::get_enabled_post_types();
        return in_array($post_type, $enabled_post_types);
    }

    /**
     * Replace TinyMCE with Layotter on Layotter-enabled screens
     */
    public static function hook_editor() {
        if (!self::is_enabled()) {
            return;
        }

        $post_type = get_post_type();

        // remove TinyMCE
        remove_post_type_support($post_type, 'editor');

        // insert Layotter
        add_meta_box('layotter_wrapper', // ID
            'Layotter', // title
            [__CLASS__, 'output_editor'], // callback
            $post_type, // post type for which to enable
            'normal', // position
            'high' // priority
        );
    }

    /**
     * Output backend HTML for Layotter
     *
     * @param $post object Post object as provided by Wordpress
     */
    public static function output_editor($post) {
        $hidden_style = 'width: 1px; height: 1px; position: fixed; top: -999px; left: -999px;';
        $visible_style = 'width: 100%; height: 200px;margin-bottom: 30px;';
        echo '<textarea id="content" name="content" style="' . $hidden_style . '"></textarea>';

        if (Settings::is_debug_mode_enabled()) {
            echo '<p>';
            printf(__('Debug mode enabled: Inspect and manually edit the JSON structure generated by Layotter. Use with caution. A faulty structure will break your page layout and content. Go to <a href="%s">Layotter\'s settings page</a> to disable debug mode.', 'layotter'), admin_url('admin.php?page=layotter-settings'));
            echo '</p>';
            echo '<textarea id="layotter-json" name="' . Core::TEXTAREA_NAME . '" style="' . $visible_style . '"></textarea>';
        } else {
            echo '<textarea id="layotter-json" name="' . Core::TEXTAREA_NAME . '" style="' . $hidden_style . '"></textarea>';
        }

        Editor::view();
    }

}
