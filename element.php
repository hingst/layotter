<?php


/**
 * Basic element - all element types must extend this class
 */
abstract class Eddditor_Element {

    
    protected
	    $type = '',
        // user-defined
        $title,
        $description,
        $icon,
        $field_group,
        // automatically generated
        $fields = array(),
        $raw_values = array(),
        $formatted_values = array(),
        $form,
        $options,
        $template_id = false;


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
     * @param array $values Field values, if present
     * @param array $options Option values, if present
     */
    final public function __construct($values = false, $options = false) {
	    $this->attributes();
	    $this->hooks();
        
        // field group can be provided as post id (int) or slug ("field_12345") of post type 'acf-field-group'
        if (!is_int($this->field_group) AND !is_string($this->field_group)) {
            throw new Exception('$this->field_group must be assigned in attributes()');
        }
        
        $identifier
            = is_int($this->field_group)
            ? 'p' // get post by ID
            : 'name'; // get post by slug - acf-field-group slugs are unique
        
        // get acf-field-group post
        $field_groups = get_posts(array(
            'post_type' => 'acf-field-group',
            $identifier => $this->field_group
        ));
        
        // check if the field group exists
        if (!is_array($field_groups) OR empty($field_groups)) {
            throw new Exception('no acf-field-group found for ' . $identifier . '=' . $this->field_group);
        }
        
        $field_group = $field_groups[0];
        $this->fields = acf_get_fields($field_group);

        $parsed_values = Eddditor::parse_values($this->fields, $values);
        $this->raw_values = $parsed_values['raw'];
        $this->formatted_values = $parsed_values['formatted'];
        
        $this->form = new Eddditor_Form('element', $this->fields, $this->raw_values);
        $this->form->set_title($this->title);
        $this->form->set_icon($this->icon);
        
        $this->options = new Eddditor_Options('element', $options);
    }


	/**
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
	 */
	final public function set_type($type) {
		if (is_string($type)) {
			$this->type = $type;
		}
	}
    
    
    /**
     * Set element as template
     * 
     * Changes the return value of $this->get('data') so Eddditor treats this
     * element as a saved template.
     * 
     * @param string $id Template ID
     */
    final public function set_template($id) {
        $this->template_id = $id;
    }
    
    
    /**
     * Get element data
     * 
     * @param string $what What to get - can be 'type', 'title', 'description', 'icon', 'form' or 'data'
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
            case 'data':
                $data = array(
                    'type' => $this->type,
                    'values' => $this->raw_values,
                    'options' => $this->options->get('data'),
                    'view' => is_admin() ? $this->get_backend_view() : $this->get_frontend_view() // provide view based on where we are
                );
                if ($this->template_id !== false) {
                    $data['template'] = $this->template_id;
                }
                return $data;
            case 'template_data':
                return array(
                    'type' => $this->type,
                    'values' => $this->raw_values
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
