<?php


/**
 * A form for options or an element
 */
class Layotter_Form {
    
    
    private
        $type = '',
        $title = '',
        $icon = '',
        $allowed_fields = array(),
        $provided_values = array();
    
    
    /**
     * Create a new form
     *
     * @param array $allowed_fields Allowed fields array as provided by ACF
     * @param array $provided_values Provided field values
     */
    public function __construct($allowed_fields, $provided_values) {
        $this->allowed_fields = $allowed_fields;
        $this->provided_values = $provided_values;
    }
    
    
    /**
     * Set human-readable title for this form
     * 
     * @param string $title Form title
     */
    public function set_title($title) {
        $this->title = $title;
    }
    
    
    /**
     * Set an icon for this form
     * 
     * @param string $icon Font Awesome icon name (without the fa- prefix)
     */
    public function set_icon($icon) {
        $this->icon = $icon;
    }
    
    
    /**
     * Output form HTML
     */
    public function output() {
        // used in the form.php template
        $title = $this->title;
        $icon = $this->icon;
        $fields = array();
        
        // loop through allowed fields and add field values to the array (where provided)
        foreach ($this->allowed_fields as $field) {
            $field_name = $field['name'];
            
            if (isset($this->provided_values[$field_name])) {
                $field['value'] = $this->provided_values[$field_name];
            }
            
            $fields[] = $field;
        }
        
        require dirname(__FILE__) . '/../views/form.php';
    }

    
}