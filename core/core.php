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
            return false;
        }
        
        // fail if provided type is empty or already in use
        $type = self::clean_type($type);
        if (empty($type) OR isset(self::$registered_elements[$type])) {
            return false;
        }
        
        // no errors, register the new element type
        self::$registered_elements[$type] = $class;

        // register element type's hooks for the backend (frontend hooks are registered on demand)
        call_user_func(array($class, 'register_backend_hooks'));

        return true;
    }


    /**
     * Create a new element instance with a specific type
     *
     * @param string|array $type_or_structure Type identifier or array with type, values and option values
     * @param array $values Field values, or empty array for default values
     * @param array $option_values Option values, or empty array for default values
     * @return object|bool New element instance, or false on failure
     */
    public static function create_element($type_or_structure, $values = array(), $option_values = array()) {
        if (is_string($type_or_structure)) {
            $structure = array(
                'type' => $type_or_structure,
                'values' => $values,
                'options' => $option_values
            );
        } else if (is_array($type_or_structure)) {
            $structure = $type_or_structure;
        } else {
            return false;
        }

        if (isset($structure['type']) AND isset(self::$registered_elements[$structure['type']])) {
            try {
                $type = $structure['type'];
                return new self::$registered_elements[$type]($structure);
            } catch(Exception $e) {
                trigger_error($e->getMessage(), E_USER_WARNING);
            }
        }

        return false;
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
            $element = Layotter::create_element($element_type);
            if ($element AND $element->is_enabled_for($post_id)) {
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
        $a_name = $element_type_a->get('title');
        $b_name = $element_type_b->get('title');

        if ($a_order < $b_order) {
            return -1;
        } else if ($a_order > $b_order) {
            return 1;
        } else {
            return strcasecmp($a_name, $b_name);
        }
    }


    /**
     * Check if Layotter is enabled for the current screen
     *
     * @return bool Whether Layotter is enabled
     */
    public static function is_enabled() {
        // fail if not in the backend
        if (!is_admin()) {
            return false;
        }

        // fail if not on a relevant edit screen
        global $pagenow;
        if ($pagenow != 'post.php' AND $pagenow != 'post-new.php') {
            return false;
        }

        // fail if layotter isn't enabled for the current post
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
