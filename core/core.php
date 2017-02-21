<?php


/**
 * Holds registered element types and serves as a factory for element instances
 */
class Layotter {
    
    
    private static
        $registered_elements = array();
    
    
    private function __construct() {} // prevent instantiation
    
    
    /**
     * Register a new element type
     * 
     * @param string $type Unique type identifier
     * @param string $class Class name for this element type, must extend Layotter_Element
     * @return bool Whether the element type has been registered successfully
     */
    public static function register_element($type, $class) {
        // fail if provided class name is not a valid class
        if (!class_exists($class) OR !is_subclass_of($class, 'Layotter_Element')) {
            throw new Exception('Invalid class: ' . $class);
        }
        
        // fail if provided type is empty or already in use
        $type = self::clean_type($type);
        if (empty($type) OR isset(self::$registered_elements[$type])) {
            throw new Exception('Invalid class: ' . $class);
        }
        
        // no errors, register the new element type
        self::$registered_elements[$type] = $class;

        // register element type's hooks for the backend (frontend hooks are registered on demand)
        call_user_func(array($class, 'register_backend_hooks'));

        return true;
    }

    public static function get_registered_element_types() {
        return self::$registered_elements;
    }


    /**
     * @param string $type
     * @return Layotter_Element
     * @throws Exception
     */
    public static function assemble_new_element($type) {
        $type = strval($type);

        if (isset(self::$registered_elements[$type])) {
            $element = new self::$registered_elements[$type]();
            $element->set_type($type);
            return $element;
        } else {
            throw new Exception('Unknown element type: ' . $type);
        }
    }


    /**
     * @param int $id
     * @return Layotter_Element
     * @throws Exception
     */
    public static function assemble_element($id, $options_id = 0) {
        $id = intval($id);
        $type = get_post_meta($id, Layotter_Editable::TYPE_META_FIELD, true);

        if (isset(self::$registered_elements[$type])) {
            $element = new self::$registered_elements[$type]($id);
            $element->set_options($options_id);
            return $element;
        } else {
            throw new Exception('Element with ID ' . $id . ' has unknown type.');
        }
    }


    /**
     * @param string $type
     * @return Layotter_Options
     * @throws Exception
     */
    public static function assemble_new_options($type) {
        $type = strval($type);
        $options = new Layotter_Options();
        $options->set_type($type);
        $options->set_post_type_context(get_post_type());
        return $options;
    }


    /**
     * @param int $id
     * @return Layotter_Options
     * @throws Exception
     */
    public static function assemble_options($id) {
        $id = intval($id);
        $options = new Layotter_Options($id);
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
     * @return bool Whether Layotter is enabled
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
     * @return bool Whether Layotter is enabled
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
        $enabled_post_types = Layotter_Settings::get_enabled_post_types();
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
        add_meta_box(
            'layotter_wrapper', // ID
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

        if (Layotter_Settings::is_debug_mode_enabled()) {
            echo '<p>';
            printf(__('Debug mode enabled: Inspect and manually edit the JSON structure generated by Layotter. Use with caution. A faulty structure will break your page layout and content. Go to <a href="%s">Layotter\'s settings page</a> to disable debug mode.', 'layotter'), admin_url('admin.php?page=layotter-settings'));
            echo '</p>';
            echo '<textarea id="layotter-json" name="layotter_json" style="' . $visible_style . '"></textarea>';
        } else {
            echo '<textarea id="layotter-json" name="layotter_json" style="' . $hidden_style . '"></textarea>';
        }

        require_once __DIR__ . '/../views/editor.php';
    }
    
    
}


