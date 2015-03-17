<?php


class Eddditor {
    
    
    private static
        $registered_elements = array();
    
    
    private function __construct() {} // prevent instantiation
    
    
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
     * Register a new element type
     * 
     * @param string $type Unique type identifier
     * @param string $class Class name for this element type, must extend Eddditor_Element
     * @return boolean Whether the element type has been registered successfully
     */
    public static function register_element($type, $class) {
        // fail if provided class name is not a valid class
        if (!class_exists($class) OR !is_subclass_of($class, 'Eddditor_Element')) {
            return false;
        }
        
        // fail if provided type is empty or already in use
        $type = self::clean_type($type);
        if (empty($type) OR isset(self::$registered_elements[$type])) {
            return false;
        }
        
        // no errors, register the new element type
        self::$registered_elements[$type] = $class;
        return true;
    }


    /**
     * Create a new element instance with a specific type
     *
     * @param string|array $type_or_structure Type identifier or array with type, values and option values
     * @param array|bool $values Field values, or false for default values
     * @param array|bool $option_values Option values, or false for default values
     * @return mixed New element instance, or false on failure
     */
    public static function create_element($type_or_structure, $values = array(), $option_values = array()) {
        if (is_string($type_or_structure)) {
            $structure = self::validate_element_structure(array(
                'type' => $type_or_structure,
                'values' => $values,
                'options' => $option_values
            ));
        } else if (is_array($type_or_structure)) {
            $structure = self::validate_element_structure($type_or_structure);
        } else {
            return false;
        }

        $type = self::clean_type($structure['type']);
        $values = $structure['values'];
        $option_values = $structure['options'];

        if (isset(self::$registered_elements[$type])) {
            try {
                $element = new self::$registered_elements[$type]($values, $option_values);
                $element->set_type($type);
                return $element;
            } catch(Exception $e) {
                trigger_error($e->getMessage(), E_USER_WARNING);
            }
        }

        return false;
    }


    private static function validate_element_structure($structure) {
        if (!isset($structure['type']) OR !is_string($structure['type'])) {
            $structure['type'] = '';
        }

        if (!isset($structure['values']) OR !is_array($structure['values'])) {
            $structure['values'] = array();
        }

        if (!isset($structure['options']) OR !is_array($structure['options'])) {
            $structure['options'] = array();
        }

        return $structure;
    }


    /**
     * Get type identifiers for all registered element types
     *
     * @return array Type identifiers
     */
    public static function get_registered_elements() {
        return array_keys(self::$registered_elements);
    }
    
    
    /**
     * Check if Eddditor is enabled for the current screen
     * 
     * @return boolean Signals whether Eddditor is enabled
     */
    public static function is_enabled() {
        // bail if not in the backend
        if (!is_admin()) {
            return false;
        }

        // false if eddditor isn't enabled for the current post type
        $settings = Eddditor_Settings::get_settings('general');
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
