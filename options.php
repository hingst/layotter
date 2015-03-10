<?php


/**
 * Manages post, row and element options
 */
class Eddditor_Options {


    private
        $type,
        $values_for_storage = array(),
        $values_for_forms = array(),
        $values_for_output = array(),
        $enabled,
        $form;


    /**
     * Create a new options object
     * 
     * @param string $type Options type - can be 'post', 'row' or 'element'
     * @param mixed $values Array containing option values, or false for default values
     */
    public function __construct($type, $values = false) {
        $this->type
            = in_array($type, array('post', 'row', 'element'))
            ? $type
            : 'post';

        // check if a field group exists for this option type
        $field_groups = acf_get_field_groups(array(
            'eddditor' => $type . '_options'
        ));

        // declare this options type ('post', 'row' or 'element') as enabled if a field group exists for the type
        if (is_array($field_groups) AND !empty($field_groups)) {
            $this->enabled = true;
            $fields = acf_get_fields($field_groups[0]);
        } else {
            $this->enabled = false;
            $fields = array();
        }

        $parsed_values = Eddditor::parse_values($fields, $values);
        $this->values_for_storage = $parsed_values['for_storage'];
        $this->values_for_forms = $parsed_values['for_forms'];
        $this->values_for_output = $parsed_values['for_output'];

        $this->form = new Eddditor_Form('options', $fields, $this->values_for_forms);

        switch ($this->type) {
            case 'post':
                $this->form->set_title(__('Post options', 'eddditor'));
                $this->form->set_icon('file-text-o');
                break;
            case 'row':
                $this->form->set_title(__('Row options', 'eddditor'));
                $this->form->set_icon('align-justify');
                break;
            case 'element':
                $this->form->set_title(__('Element options', 'eddditor'));
                $this->form->set_icon('table');
                break;
        }
    }


    /**
     * Check if this option type is enabled (i.e. an ACF field group exists for this option type)
     *
     * @return boolean Whether options are available for this type
     */
    public function is_enabled() {
        return $this->enabled;
    }


    /**
     * Return form object or options data
     * 
     * @param string $what What to get - can be 'form' or 'data'
     * @return mixed Requested data
     */
    public function get($what) {
        switch ($what) {
            case 'form':
                return $this->form;
            case 'data':
                return array(
                    'type' => $this->type,
                    'values' => $this->values_for_storage
                );
            case 'values_for_output':
                return $this->values_for_output;
            default:
                return null;
        }
    }
    
    
}



