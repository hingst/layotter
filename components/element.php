<?php


/**
 * All custom element types must extend this class
 */
abstract class Layotter_Element extends Layotter_Editable {

    
    protected
        // internal use only
        $type = '',
        $template_id = -1,
        $options = array(),
        // user-defined (mandatory)
        $title,
        $description,
        $icon,
        $field_group,
        // user-defined (optional)
        $order = 0;


    /**
     * Must assign $this->title, $this->description, $this->icon and $this->field_group
     *
     * May assign $this->order to override alphabetical ordering in the "Add Element" screen.
     */
    abstract protected function attributes();


    /**
     * Should output HTMl for the element's backend representation
     *
     * @param array $fields Field values
     */
    abstract protected function backend_view($fields);


    /**
     * Should output HTMl for the element's frontend representation
     *
     * @param array $fields Field values
     */
    abstract protected function frontend_view($fields);


    /**
     * backend_assets() is optional and should be used to enqueue scripts and styles for the backend
     */
    public static function backend_assets() {}


    /**
     * frontend_assets() is optional and should be used to enqueue scripts and styles for the backend
     */
    public static function frontend_assets() {}


    /**
     * Create a new element
     *
     * @param array $structure Element structure
     * @param int $id Element's post ID
     * @throws Exception If the ACF field group defined for this element doesn't exist
     */
    final public function __construct($structure, $id = 0) {
        $this->attributes();

        if ($id) {
            $this->id = $id;
            $this->type = get_post_meta($id, 'layotter_element_type', true);
            $this->fields = $this->get_fields();
            $this->values = get_fields($id);
        } else {
            $structure = $this->validate_structure($structure);
            if (isset($structure['id'])) {
                $this->id = $structure['id'];
            }
            $this->type = $structure['type'];
            $values = $structure['values'];

            $fields = $this->get_fields();
            $this->apply_values($fields, $values);

            $this->form->set_title($this->title);
            $this->form->set_icon($this->icon);
        }

        $option_values = $structure['options'];
        $this->options = new Layotter_Options('element', $option_values);

        $this->register_frontend_hooks();
    }

    /**
     * Get ACF fields for this element
     *
     * @return array ACF fields
     * @throws Exception If $this->field_group wasn't assigned correctly in $this->attributes()
     */
    final protected function get_fields() {
        // ACF field group can be provided as post id (int) or slug ('group_xyz')
        if (!is_int($this->field_group) AND !is_string($this->field_group)) {
            throw new Exception('$this->field_group must be assigned in attributes() (error in class ' . get_called_class() . ')');
        }

        if (is_int($this->field_group)) {
            $field_group = Layotter_ACF::get_field_group_by_id($this->field_group);
            $identifier = 'post_id';
        } else {
            $field_group = Layotter_ACF::get_field_group_by_key($this->field_group);
            $identifier = 'acf-field-group';
        }

        // check if the field group exists
        if (!$field_group) {
            throw new Exception('No ACF field group found for ' . $identifier . '=' . $this->field_group . ' (error in class ' . get_called_class() . ')');
        }

        // return fields for the provided ACF field group
        return Layotter_ACF::get_fields($field_group);
    }


    /**
     * Register hooks for backend assets
     *
     * This allows element type developers to enqueue scripts and styles required to display this element
     * correctly in the backend.
     */
    final public static function register_backend_hooks() {
        add_action('admin_footer', array(get_called_class(), 'register_backend_hooks_helper'));
    }


    /**
     * Helper function for register_backend_hooks
     *
     * To make sure that Layotter::is_enabled() returns the correct value, the check is delayed until admin_footer.
     * Without the check, assets would be included on every single page in the backend.
     */
    final public static function register_backend_hooks_helper() {
        if (Layotter::is_enabled()) {
            call_user_func(array(get_called_class(), 'backend_assets'));
        }
    }


    /**
     * Register hooks for frontend assets
     *
     * This allows element type developers to enqueue scripts and styles required to display this element
     * correctly in the frontend.
     */
    final private function register_frontend_hooks() {
        if (!is_admin()) {
            call_user_func(array(get_called_class(), 'frontend_assets'));
        }
    }


    /**
     * Check if this element type is enabled for a specific post
     *
     * @param int $post_id Post ID
     * @return bool Whether this element type is enabled
     */
    final public function is_enabled_for($post_id) {
        $post_id = intval($post_id);
        $post_type = get_post_type($post_id);

        if (is_int($this->field_group)) {
            $field_group = Layotter_ACF::get_field_group_by_id($this->field_group);
        } else {
            $field_group = Layotter_ACF::get_field_group_by_key($this->field_group);
        }

        return Layotter_ACF::is_field_group_visible($field_group, array(
            'post_id' => $post_id,
            'post_type' => $post_type,
            'layotter' => 'element'
        ));
    }


    /**
     * Get element data
     *
     * @param string $what What to get - can be 'type', 'title', 'description', 'icon'
     * @return string Requested data
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

            case 'order':
                return $this->order;

            default:
                return null;
        }
    }


    /**
     * Declare this element as a template
     *
     * Templates are managed through the Layotter_Templates class, this method simply declares this element as an
     * instance of a saved template. The template ID will be present in this element's JSON representation.
     *
     * @param int $template_id Template ID
     */
    final public function set_template_id($template_id) {
        $this->template_id = $template_id;
    }


    /**
     * Remove template ID and treat as a regular element
     */
    final public function unset_template_id() {
        $this->template_id = -1;
    }


    /**
     * Get element data to be saved in the database as a template
     *
     * Options and view are not necessary because:
     *      1. Templates never have options, only an instance of a template has options
     *      2. View is refreshed before every output, no need to save it to the database
     *
     * @return array Array representation of this element to be saved as a template
     */
    final public function get_template_data() {
        return array(
            'template_id' => $this->template_id,
            'type' => $this->type,
            'values' => $this->clean_values
        );
    }


    /**
     * Validate an array containing an element's structure
     *
     * Validates array structure and presence of required key/value pairs
     *
     * @param array $structure Element structure
     * @return array Validated element structure
     */
    private function validate_structure($structure) {
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
     * Return array representation of this element for use in json_encode()
     *
     * PHP's JsonSerializable interface would be cleaner, but it's only available >= 5.4.0
     *
     * @return array Array representation of this element
     */
    public function to_array() {
        if ($this->template_id > -1) {
            return array(
                'id' => $this->id,
                'template_id' => $this->template_id,
                'options' => $this->options->to_array(),
                'view' => $this->get_backend_view()
            );
        } else if ($this->has_id()) {
            return array(
                'id' => $this->id,
                'type' => $this->type,
                'values' => $this->values,
                'options' => $this->options->to_array(),
                'view' => $this->get_backend_view()
            );
        } else {
            return array(
                'id' => $this->id,
                'type' => $this->type,
                'values' => $this->clean_values,
                'options' => $this->options->to_array(),
                'view' => $this->get_backend_view()
            );
        }
    }


    /**
     * Get the backend view
     *
     * @return string Backend view HTML
     */
    final public function get_backend_view() {
        ob_start();
        if ($this->has_id()) {
            $this->backend_view($this->values);
        } else {
            $this->backend_view($this->formatted_values);
        }
        return ob_get_clean();
    }


    /**
     * Get the frontend view
     *
     * @param array $col_options Formatted options for the parent column
     * @param array $row_options Formatted options for the parent row
     * @param array $post_options Formatted options for the parent post
     * @return string Frontend view HTML
     */
    final public function get_frontend_view($col_options, $row_options, $post_options) {
        ob_start();
        $this->frontend_view($this->formatted_values);
        $element_html = ob_get_clean();

        if (has_filter('layotter/view/element')) {
            return apply_filters('layotter/view/element', $element_html, $this->options->get_formatted_values(), $col_options, $row_options, $post_options);
        } else {
            $html_wrapper = Layotter_Settings::get_html_wrapper('elements');
            return $html_wrapper['before'] . $element_html . $html_wrapper['after'];
        }
    }


    final public function get_form_data() {
        if ($this->has_id()) {
            return array(
                'title' => $this->title,
                'icon' => $this->icon,
                'nonce' => wp_create_nonce('post'),
                'fields' => Layotter_ACF::get_form_html($this->fields, $this->id)
            );
        } else {
            return parent::get_form_data();
        }
    }


}
