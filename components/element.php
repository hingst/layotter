<?php


abstract class Eddditor_Element extends Eddditor_Editable implements JsonSerializable {

    
    protected
        // internal use only
        $type = '',
        // user-defined
        $title,
        $description,
        $icon,
        $field_group,
        // automatically generated
        $options = array();


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
     * @param array $values Field values, or false for default values
     * @param array $option_values Option values, or false for default values
     * @throws Exception If the ACF field group defined for this element doesn't exist
     */
    final public function __construct($values = array(), $option_values = array()) {
        $this->attributes();
        $this->hooks();

        if (!is_array($values)) {
            $values = array();
        }

        $fields = $this->get_fields();
        $this->apply_values($fields, $values);

        $this->form->set_title($this->title);
        $this->form->set_icon($this->icon);

        if (!is_array($option_values)) {
            $option_values = array();
        }

        // create options object for this element
        $this->options = new Eddditor_Options('element', $option_values);
    }


    final protected function get_fields() {
        // field group can be provided as post id (int) or slug ("acf_some-slug") of post type 'acf-field-group'
        if (!is_int($this->field_group) AND !is_string($this->field_group)) {
            throw new Exception('$this->field_group must be assigned in attributes()');
        }

        /*
        $identifier
            = is_int($this->field_group)
            ? 'ID' // filter ACF groups by post ID
            : 'key'; // filter ACF groups by post slug

        // get ACF field group
        $field_group_for_this_element = false;
        $field_groups = acf_get_field_groups(array(
            'post_id' => get_the_ID(),
            'post_type' => get_post_type(),
            'eddditor' => 'element'
        ));
        foreach ($field_groups as $field_group) {
            if ($field_group[$identifier] == $this->field_group) {
                $field_group_for_this_element = $field_group;
                break;
            }
        }

        // check if the field group exists
        if (!$field_group_for_this_element) {
            throw new Exception('No ACF field group found for ' . $identifier . '=' . $this->field_group . '.');
        }

        // fetch fields for the provided ACF field group
        $fields = acf_get_fields($field_group_for_this_element);
        */

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

        // return fields for the provided ACF field group
        return acf_get_fields($field_groups[0]);
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
     * Get element data
     *
     * @param string $what What to get - can be 'type', 'title', 'description', 'icon', 'form'
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

            default:
                return null;
        }
    }


    /**
     */
    final public function jsonSerialize() {
        return array(
            'type' => $this->type,
            'values' => $this->clean_values,
            'options' => $this->options,
            'view' => $this->get_backend_view()
        );
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
        $element_html = ob_get_clean();

        if (has_filter('eddditor/element')) {
            return apply_filters('eddditor/element', $element_html, $this->options->get_formatted_values());
        } else {
            $settings = Eddditor_Settings::get_settings('elements');
            return $settings['html_before'] . $element_html . $settings['html_after'];
        }
    }
    
    
}
