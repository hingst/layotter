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
    public static function assemble_element($id) {
        $id = intval($id);
        $type = get_post_meta(Layotter_Editable::TYPE_META_FIELD, $id, true);

        if (isset(self::$registered_elements[$type])) {
            return new self::$registered_elements[$type]($id);
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
     * Get element types enabled for a specific post
     *
     * @param int $post_id Post ID
     * @return array Element instances
     */
    public static function get_filtered_element_types($post_id) {
        $elements = array();

        foreach (array_keys(self::$registered_elements) as $element_type) {
            $element = Layotter::assemble_new_element($element_type);
            if ($element->is_enabled_for($post_id)) {
                $elements[] = $element;
            }
        }

        usort($elements, array(__CLASS__, 'sort_element_types_helper'));

        return $elements;
    }


    /**
     * Helper used to sort a set of element types (to be used with usort())
     *
     * Sorts using the order attribute. Elements with the same order attribute are sorted alphabetically
     * by name. Elements without an order attribute are treated as order = 0.
     *
     * @param Layotter_Element $element_type_a First element type for comparison
     * @param Layotter_Element $element_type_b Second element type for comparison
     * @return int -1 if A comes first, 1 if B comes first, 0 if equal
     */
    public static function sort_element_types_helper($element_type_a, $element_type_b) {
        $a_order = $element_type_a->get('order');
        $b_order = $element_type_b->get('order');
        $a_title = $element_type_a->get('title');
        $b_title = $element_type_b->get('title');

        if ($a_order < $b_order) {
            return -1;
        } else if ($a_order > $b_order) {
            return 1;
        } else {
            return strcasecmp($a_title, $b_title);
        }
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
    
    
}
