<?php


/**
 * Basic element - all element types must extend this class
 */
abstract class Eddditor_Element {

    
    protected
        // internal use only
        $type = '',
        // user-defined
        $title,
        $description,
        $icon,
        $field_group,
        // automatically generated
        $clean_values = array(),
        $formatted_values = array(),
        $form,
        $options,
        $template_id = -1;


    /**
     * attributes() is required and should assign $title, $description, $icon and $field_group
     */
    abstract protected function attributes();


    /**
     * admin_assets() is optional and should be used to enqueue scripts and styles
     */
    public function admin_assets() {}


    /**
     * Create a new element instance
     *
     * @param array|bool $values Field values, or false for default values
     * @param array|bool $options Option values, or false for default values
     * @throws Exception If the ACF field group defined for this element doesn't exist
     */
    final public function __construct($values = false, $options = false) {
        $this->attributes();
        $this->hooks();
        
        // field group can be provided as post id (int) or slug ("acf_some-slug") of post type 'acf-field-group'
        if (!is_int($this->field_group) AND !is_string($this->field_group)) {
            throw new Exception('$this->field_group must be assigned in attributes()');
        }
        
        $identifier
            = is_int($this->field_group)
            ? 'p' // get post by ID
            : 'name'; // get post by slug
        
        // get ACF field group
        $field_groups = get_posts(array(
            'post_type' => 'acf-field-group',
            $identifier => $this->field_group
        ));
        
        // check if the field group exists
        if (!is_array($field_groups) OR empty($field_groups)) {
            throw new Exception('No ACF field group found for ' . $identifier . '=' . $this->field_group . '.');
        }

        // fetch fields for the provided ACF field group
        $fields = acf_get_fields($field_groups[0]);

        // parse provided values for use in different contexts
        $this->clean_values = Eddditor::clean_values($fields, $values);
        $this->formatted_values = Eddditor::format_values($fields, $this->clean_values);

        // create edit form for this element
        $this->form = new Eddditor_Form('element', $fields, $this->clean_values);
        $this->form->set_title($this->title);
        $this->form->set_icon($this->icon);

        // create options object for this element
        $this->options = new Eddditor_Options('element', $options);
    }


    /**
     * Register all necessary actions and filters
     */
    final protected function hooks() {
        if (is_callable(array($this, 'admin_assets'))) {
            add_action('admin_footer', array($this, 'admin_assets'));
        }
    }


    /**
     * For internal use only - set a unique type identifier after registering the element
     *
     * @param string $type Type identifier
     *
     * TODO: Find a cleaner way to let element types know their own type ID
     */
    final public function set_type($type) {
        if (is_string($type)) {
            $this->type = $type;
        }
    }
    
    
    /**
     * Set element as template
     * 
     * Changes the return value of $this->get('data') so Eddditor treats this element as a saved template.
     * 
     * @param string $id Template ID
     */
    final public function set_template($id) {
        $this->template_id = $id;
    }
    
    
    /**
     * Get element data
     * 
     * @param string $what What to get - can be 'type', 'title', 'description', 'icon', 'form', 'backend_data' or 'frontend_data'
     * @return mixed Requested data
     */
    final public function get($what) {
        switch ($what) {
            case 'type':
                return $this->type;
            case 'title':
                return $this->title;
            case 'description':
                return $this->description;
            case 'icon':
                return $this->icon;
            case 'form':
                return $this->form;
            case 'backend_data':
                $data = array(
                    'type' => $this->type,
                    'values' => $this->clean_values,
                    'options' => $this->options->get('data'),
                    'view' => $this->get_backend_view()
                );
                if ($this->template_id > -1) {
                    $data['template'] = $this->template_id;
                }
                return $data;
            case 'frontend_data':
                $data = array(
                    'options' => $this->options, // return the object instead of ->get('data') so formatted_values can
                                                 // be fetched for the frontend in eddditor_frontend_elements()
                    'view' => $this->get_frontend_view()
                );
                return $data;
            case 'template_data':
                return array(
                    'type' => $this->type,
                    'values' => $this->clean_values
                );
            default:
                return null;
        }
    }
    
    
    /**
     * Get the backend view
     * 
     * @return string Backend view HTML
     */
    final public function get_backend_view() {
        ob_start();
        $this->backend_view($this->formatted_values);
        return ob_get_clean();
    }
    
    
    /**
     * Get the frontend view
     * 
     * @return string Frontend view HTML
     */
    final public function get_frontend_view() {
        ob_start();
        $this->frontend_view($this->formatted_values);
        return ob_get_clean();
    }
    
    
    /**
     * Output a basic backend view
     * 
     * Should be overwritten by custom element types.
     * 
     * @param array $fields Field values
     */
    protected function backend_view($fields) {
        $this->default_view($fields);
    }
    
    
    /**
     * Output a basic frontend view
     * 
     * Should be overwritten by custom element types.
     * 
     * @param array $fields Field values
     */
    protected function frontend_view($fields) {
        $this->default_view($fields);
    }
    
    
    /**
     * Output a basic default view
     * 
     * Used only if a custom element type doesn't provide its own backend or
     * frontend view.
     * 
     * @param array $fields Field values
     */
    final protected function default_view($fields) {
        echo '<p><strong>' . $this->title . '</strong></p>';

        foreach ($fields as $field_name => $field_value) {
            echo $field_name . ': ';
            print_r($field_value);
            echo '<br>';
        }
    }
    
    
}
