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
        
        // no errors, register the new element
        self::$registered_elements[$type] = $class;
        return true;
    }


    /**
     * Create a new element instance with a specific type
     *
     * @param string $type Type identifier
     * @param array|bool $values Field values, or false for default values
     * @param array|bool $options Option values, or false for default values
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
     * Clean user-provided values
     *
     * Depending on the context, field values are provided in different ways. This method normalizes an array
     * of user-provided field values. The return value is an array where the keys are human-readable field names
     * (as provided by the user in an ACF field group), and the values are unfiltered data of any type (as provided
     * by an element edit form or an existing post's JSON data).
     *
     * @param array $existing_fields Existing fields as provided by an ACF field group
     * @param array|bool $provided_values Array with user-provided values, or false if dealing with a new element
     * @return array Array with values ready for use in different contexts
     */
    public static function clean_values($existing_fields, $provided_values = false) {
        $values = array();

        // use default field values if $provided_values === false
        $use_defaults = ($provided_values === false);

        // loop through existing fields and see if there's a user provided value for each one
        foreach ($existing_fields as $field_data) {
            $field_name = $field_data['name'];
            $field_key = $field_data['key'];

            // skip ACF 'tab' and 'message' fields to prevent pollution of $values with empty keys and values
            if (empty($field_name)) {
                continue;
            }

            // assign a value to this field
            if ($use_defaults) {
                // use default value ...
                $value
                    = isset($field_data['default_value'])
                    ? $field_data['default_value']
                    : '';
            } else if (isset($provided_values[$field_name])) {
                // ... or user provided value identified by name ...
                // (when $provided_values came from an existing post's JSON data)
                $value = $provided_values[$field_name];
            } else if (isset($provided_values[$field_key])) {
                // ... or user provided value identified by ACF key ...
                // (when $provided_values came from an edit form generated by ACF)
                $value = $provided_values[$field_key];
            } else {
                // ... or set to null if nothing was provided (to make sure that keys are present for all fields)
                $value = null; // note: used to be ''
            }

            // note:
            // in default ACF, field values are run through these filters before saving them to the database:
            // acf/validate_value - not necessary, as validation is performed by ACF beforehand
            // acf/update_value breaks some fields, e.g. Repeater
            // TODO: Find out why the acf/update_value filter breaks some fields

            $values[$field_name] = $value;
        }

        return $values;
    }


    /**
     * Format user-provided values for output
     *
     * @param array $existing_fields Existing fields as provided by an ACF field group
     * @param array $clean_values Array with clean values (that were run through Eddditor::clean_values() first)
     * @return array Array with values ready for use in different contexts
     */
    public static function format_values_for_output($existing_fields, $clean_values) {
        $values = array();

        // run all provided values through formatting filters
        foreach ($existing_fields as $field_data) {
            $field_name = $field_data['name'];

            // skip ACF 'tab' and 'message' fields to prevent pollution of $values with empty keys and values
            if (empty($field_name)) {
                continue;
            }

            $value = $clean_values[$field_name];

            // acf/load_value - from ACF docs: 'This filter is applied to the $value after it is loaded from the db'
            $value = apply_filters('acf/load_value', $value, 0, $field_data);
            $value = apply_filters('acf/load_value/type=' . $field_data['type'], $value, 0, $field_data);
            $value = apply_filters('acf/load_value/name=' . $field_data['name'], $value, 0, $field_data);
            $value = apply_filters('acf/load_value/key=' . $field_data['key'], $value, 0, $field_data);

            // acf/format_value - from ACF docs: 'This filter is appied to the $value after it is loaded from the db and before it is returned to the template'
            $value = apply_filters('acf/format_value', $value, 0, $field_data);
            $value = apply_filters('acf/format_value/type=' . $field_data['type'], $value, 0, $field_data);

            $values[$field_name] = $value;
        }

        return $values;
    }
    
    
    /**
     * Get content structure for a specific post
     * 
     * @param int $post_id Post ID
     * @return mixed Array with post content, or null if no data is available (as is the case with new posts)
     */
    public static function get_content_structure($post_id) {
        // get raw post content (should look like [eddditor]json_data[/eddditor] for existing posts)
        $content_raw = get_post_field('post_content', $post_id);

        // verify that the content is correctly formatted, unwrap from shortcode
        $matches = array();
        if (preg_match('/\[eddditor\](.*)\[\/eddditor\]/ms', $content_raw, $matches)) {
            $content_json = $matches[1];
        } else {
            $content_json = false;
        }

        // decode page structure from json
        $content_decoded = json_decode($content_json, true);
        $content
            = is_array($content_decoded)
            ? $content_decoded
            : null; // => new post (default post options will be applied via Javascript)

        // get views for all elements
        if (is_array($content)) {
            foreach ($content['rows'] as &$row) {
                foreach ($row['cols'] as &$col) {
                    foreach ($col['elements'] as $key => &$element) {
                        $element_object = false;

                        // create element from template ID if a template ID is present
                        if (isset($element['template'])) {
                            $element_object = Eddditor_Templates::create_element($element['template'], $element['options']['values']);
                        }

                        // if template ID is not present or invalid, create a regular element.
                        // when a saved template is deleted, the element type and values are preserved, so each instance
                        // of the template lives on as a regular element
                        if (!$element_object) {
                            $element_object = Eddditor::create_element($element['type'], $element['values'], $element['options']['values']);
                        }
                        
                        // refresh element view (backend or frontend view is determined automatically)
                        // or remove the element if it can't be created - which should only happen if the element type
                        // has been removed by a developer
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
