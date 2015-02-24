<?php


/**
 * Provides shared functionality
 */
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
     * @param string $class Class name for this element, must extend Eddditor_Element
     * @return boolean Signals whether the element has been registered successfully
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
        
        // no errors, register the new element
        self::$registered_elements[$type] = $class;
        return true;
    }


    /**
     * Create a new element instance with a specific type
     *
     * @param string $type Type identifier
     * @param array|bool $values Field values
     * @param array|bool $options Option values
     * @return mixed New element instance, or false on failure
     */
    public static function create_element($type, $values = false, $options = false) {
        $type = self::clean_type($type);
        if (isset(self::$registered_elements[$type])) {
            try {
                $element = new self::$registered_elements[$type]($values, $options);
	            $element->set_type($type);
                return $element;
            } catch(Exception $e) {
                trigger_error($e->getMessage(), E_USER_WARNING);
            }
        }

        return false;
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
        $options = get_option('eddditor_settings_general');
        $current_post_type = get_post_type();
        if (!is_array($options) OR !isset($options['enable_for'][$current_post_type]) OR $options['enable_for'][$current_post_type] != '1')  {
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
    
    
    /**
     * Parse user-provided values
     * 
     * Takes user-provided values and prepares them for use in different
     * contexts:
     * - returned['raw'] is an array with strings only (can be JSON-encoded
     *   arrays or other data types)
     * - returned['formatted'] is an array with all the values formatted by
     *   ACF's filters - e.g. the value from a Post Object Field can be returned
     *   as post ID or post object, depending on the user's configuration
     * 
     * @param array $existing_fields Existing fields as provided by an ACF field group
     * @param mixed $provided_values Array with user-provided values, or false if dealing with a new element
     * @return array Array with strings-only values as well as formatted values
     */
    public static function parse_values($existing_fields, $provided_values = false) {
        // use default field values if $provided_values === false
        $use_defaults = ($provided_values === false);
        
        $raw_values = array(); // string only (can be JSON-encoded), for use in forms
        $formatted_values = array(); // any type, for frontend use

        foreach ($existing_fields as $field_data) {
            $field_name = $field_data['name'];
            $field_key = $field_data['key'];
            
            // acf compatibility - 'tab' and 'message' fields pollute the array
            // with empty keys and values
            if (empty($field_name)) {
                continue;
            }

            if ($use_defaults) { // assign default value ...
                $value
                    = $field_data['default_value']
                    ? $field_data['default_value']
                    : '';
            } else if (isset($provided_values[$field_name])) { // .. or user provided value identified by name - happens when creating an element in the parser ...
                $value = $provided_values[$field_name];
            } else if (isset($provided_values[$field_key])) { // .. or user provided value identified by key - happens when creating an element with post data ...
                $value = $provided_values[$field_key];
            } else { // .. or set to empty string if nothing was provided
                $value = '';
            }
            
            // for $string_values: JSONify anything thats not a string
            $raw_values[$field_name] = $value;
            
            // for $formatted_values: run value through ACF's formatting filter
            $formatted_values[$field_name] = apply_filters('acf/format_value/type=' . $field_data['type'], $value, 0, $field_data); // value, post id, field
        }

        return array(
            'raw' => $raw_values,
            'formatted' => $formatted_values
        );
    }
    
    
    /**
     * Get content structure for a specific post
     * 
     * @param int $post_id Post ID
     * @return mixed Array with post content, or null if no data available (as is the case with new posts)
     */
    public static function get_content($post_id) {
        // get raw post content (should look like [eddditor]json_data[/eddditor]
        $content_raw = get_post_field('post_content', $post_id);

        // verify that the content is correctly formatted, unwrap from shortcode
        $matches = array();
        if (preg_match('/^\[eddditor\](.*)\[\/eddditor\]$/Um', $content_raw, $matches)) {
            $content_json = $matches[1];
        } else {
            $content_json = false;
        }

        // decode page structure from json
        $content_decoded = json_decode($content_json, true);
        $content
            = is_array($content_decoded)
            ? $content_decoded
            : null; // => new post (using default post options)

        // get views for all elements
        if (is_array($content)) {
            foreach ($content['rows'] as &$row) {
                foreach ($row['cols'] as &$col) {
                    foreach ($col['elements'] as $key => &$element) {
	                    $element_object = false;

	                    // create element from template id
                        if (isset($element['template'])) {
                            $element_object = Eddditor_Templates::create_element($element['template'], $element['options']['values']);
                        }

	                    // if template id not present or invalid, create element from saved values
	                    if (!$element_object) {
		                    $element_object = Eddditor::create_element($element['type'], $element['values'], $element['options']['values']);
	                    }
                        
                        // update element view or remove element if it can't be created,
                        // which will usually only happen if the element type or template doesn't exist anymore
                        if ($element_object) {
                            $element = $element_object->get('data');
                        } else {
                            array_splice($col['elements'], $key, 1);
                        }
                    }
                }
            }
        }
        
        return $content;
    }
    
    
}
