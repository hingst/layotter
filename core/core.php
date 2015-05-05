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

        // register element type's hooks
        call_user_func(array($class, 'hooks'));

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

        return $elements;
    }
    
    
    /**
     * Check if Layotter is enabled for the current screen
     * 
     * @return bool Whether Layotter is enabled
     */
    public static function is_enabled() {
        // bail if not in the backend
        if (!is_admin()) {
            return false;
        }

        // false if layotter isn't enabled for the current post type
        $settings = Layotter_Settings::get_settings('basic');
        $current_post_type = get_post_type();
        if (!is_array($settings) OR !isset($settings['enable_for'][$current_post_type]) OR $settings['enable_for'][$current_post_type] != '1')  {
            return false;
        }

        // false if we're not on a relevant edit screen
        global $pagenow;
        if ($pagenow != 'post.php' AND $pagenow != 'post-new.php') {
            return false;
        }

        // no errors
        return true;
    }
    
    
}
