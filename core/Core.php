<?php

namespace Layotter;

use Layotter\Acf\Adapter;
use Layotter\Components\Editable;
use Layotter\Components\Element;
use Layotter\Components\Options;
use Layotter\Components\Post;
use Layotter\Upgrades\MigrationHelper;
use Layotter\Views\Editor;

/**
 * Holds registered element types and serves as a factory for element instances
 */
class Core {

    const META_FIELD_JSON = 'layotter_json';
    const TEXTAREA_NAME = 'layotter_json';

    private static $registered_elements = array();

    public static function init() {
        self::aliases();

        // run only if ACF is available
        if (!Adapter::is_available()) {
            return;
        }


        add_action('admin_head', array(__CLASS__, 'hook_editor'));
        add_filter('wp_post_revision_meta_keys', array(__CLASS__, 'track_custom_field'));
        add_action('after_setup_theme', array(__CLASS__, 'include_example_element'));
        add_filter('wp_insert_post_data', array(__CLASS__, 'save_post'), 999, 2);

        add_action('admin_enqueue_scripts', array('Layotter\Assets', 'backend'));
        add_action('wp_enqueue_scripts', array('Layotter\Assets', 'frontend'));
        add_action('admin_footer', array('Layotter\Assets', 'views'));

        add_action('wp_ajax_layotter_edit_element', array('Layotter\Ajax\Endpoints', 'edit_element'));
        add_action('wp_ajax_layotter_save_element', array('Layotter\Ajax\Endpoints', 'save_element'));
        add_action('wp_ajax_layotter_edit_options', array('Layotter\Ajax\Endpoints', 'edit_options'));
        add_action('wp_ajax_layotter_save_options', array('Layotter\Ajax\Endpoints', 'save_options'));
        add_action('wp_ajax_layotter_save_new_template', array('Layotter\Ajax\Endpoints', 'save_new_template'));
        add_action('wp_ajax_layotter_delete_template', array('Layotter\Ajax\Endpoints', 'delete_template'));
        add_action('wp_ajax_layotter_save_new_layout', array('Layotter\Ajax\Endpoints', 'save_new_layout'));
        add_action('wp_ajax_layotter_load_layout', array('Layotter\Ajax\Endpoints', 'load_layout'));
        add_action('wp_ajax_layotter_rename_layout', array('Layotter\Ajax\Endpoints', 'rename_layout'));
        add_action('wp_ajax_layotter_delete_layout', array('Layotter\Ajax\Endpoints', 'delete_layout'));

        add_filter('acf/location/rule_types', array('Layotter\Acf\LocationRules', 'category'));
        add_filter('acf/location/rule_values/layotter', array('Layotter\Acf\LocationRules', 'options'));
        add_filter('acf/location/rule_match/layotter', array('Layotter\Acf\LocationRules', 'match_rules'), 10, 3);

        add_shortcode('layotter', array('Layotter\Shortcode', 'register'));
        add_filter('the_content', array('Layotter\Shortcode', 'disable_wpautop'), 1);
        add_filter('no_texturize_shortcodes', array('Layotter\Shortcode', 'disable_wptexturize'));
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
     */
    function track_custom_field($keys) {
        $keys[] = self::META_FIELD_JSON;
        return $keys;
    }

    /**
     * Include example element if enabled in options
     */
    public static function include_example_element() {
        if (Settings::example_element_enabled()) {
            Example\FieldGroup::register();
            self::register_element('layotter_example_element', '\Layotter\Example\Element');
        }
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

        if (!self::is_enabled_for_post($post_id) OR !isset($raw_post[self::TEXTAREA_NAME])) {
            return $data;
        }

        // strip slashes that were added by Wordpress
        $json = $raw_post[self::TEXTAREA_NAME];
        $unslashed_json = stripslashes_deep($json);

        // fetch search dump
        $layotter_post = new Post();
        $layotter_post->set_json($unslashed_json);
        $search_dump = '[layotter post="' . $post_id . '"]' . $layotter_post->get_search_dump() . '[/layotter]';

        // oddly enough, Wordpress breaks JSON if it's stripslashed
        update_post_meta($post_id, Core::META_FIELD_JSON, $json);
        update_post_meta($post_id, MigrationHelper::META_FIELD_MODEL_VERSION, MigrationHelper::CURRENT_MODEL_VERSION);

        $data['post_content'] = $search_dump;
        return $data;
    }

    /**
     * Register a new element type
     *
     * @param string $type Unique type identifier
     * @param string $class Class name for this element type
     * @return bool Whether the element type has been registered successfully
     * @throws \Exception
     */
    public static function register_element($type, $class) {
        // fail if provided class name is not a valid class
        if (!class_exists($class) OR !is_subclass_of($class, 'Layotter\Components\Element')) {
            throw new \Exception('Invalid class: ' . $class);
        }

        // fail if provided type is empty or already in use
        $type = self::clean_type($type);
        if (empty($type) OR isset(self::$registered_elements[$type])) {
            throw new \Exception('Invalid class: ' . $class);
        }

        // no errors, register the new element type
        self::$registered_elements[$type] = $class;

        // register element type's hooks for the backend (frontend hooks are registered on demand)
        call_user_func(array($class, 'register_backend_hooks'));

        return true;
    }

    /**
     * Get all registered element types
     *
     * @return array
     */
    public static function get_registered_element_types() {
        return self::$registered_elements;
    }

    /**
     * Create a new element instance
     *
     * @param string $type Element type
     * @return Element
     * @throws \Exception If type is invalid
     */
    public static function assemble_new_element($type) {
        $type = strval($type);

        if (isset(self::$registered_elements[$type])) {
            $element = new self::$registered_elements[$type]();
            $element->set_type($type);
            return $element;
        } else {
            throw new \Exception('Unknown element type: ' . $type);
        }
    }

    /**
     * Create an instance from an existing element
     *
     * @param int $id Element ID
     * @param int $options_id Options ID
     * @return Element
     * @throws \Exception If ID is invalid
     */
    public static function assemble_element($id, $options_id = 0) {
        $id = intval($id);
        $type = get_post_meta($id, Editable::META_FIELD_EDITABLE_TYPE, true);

        if (isset(self::$registered_elements[$type])) {
            $element = new self::$registered_elements[$type]($id);
            $element->set_options($options_id);
            return $element;
        } else {
            throw new \Exception('Element with ID ' . $id . ' has unknown type.');
        }
    }

    /**
     * Create a new options instance
     *
     * @param string $type Options type
     * @return Options
     */
    public static function assemble_new_options($type) {
        $type = strval($type);
        $options = new Options();
        $options->set_type($type);
        $options->set_post_type_context(get_post_type());
        return $options;
    }

    /**
     * Create an instance of existing options
     *
     * @param int $id Options ID
     * @return Options
     */
    public static function assemble_options($id) {
        $id = intval($id);
        $options = new Options($id);
        $options->set_post_type_context(get_post_type());
        return $options;
    }

    /**
     * Remove illegal characters from a type identifier
     *
     * @param string $type Dirty type identifier
     * @return string Clean type identifier
     */
    private static function clean_type($type) {
        if (!is_string($type)) {
            return '';
        }

        return preg_replace('/[^a-z_]/', '', $type); // only a-z and _ allowed
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
        if ($pagenow != 'post.php' AND $pagenow != 'post-new.php') {
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
        $override_enabled = apply_filters('layotter/enable_for_posts', array());
        $override_disabled = apply_filters('layotter/disable_for_posts', array());

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

        // insert layotter
        add_meta_box('layotter_wrapper', // ID
            'Layotter', // title
            array(__CLASS__, 'output_editor'), // callback
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
