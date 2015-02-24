<?php


/**
 * Manages post, row and element options
 */
class Eddditor_Options {


    private
        $type,
        $formatted_values,
        $enabled,
        $form;


    /**
     * Create a new options object
     * 
     * @param string $type Options type - can be 'post', 'row' or 'element'
     * @param mixed $values Array containing option values, or false for default values
     */
    public function __construct($type, $values = false) {
        $this->type = (string) $type;

        // check if a field group exists for this option type
        $field_groups = acf_get_field_groups(array(
            'eddditor' => $type . '_options'
        ));
        if (is_array($field_groups) AND !empty($field_groups)) {
            $this->enabled = true;
            $fields = acf_get_fields($field_groups[0]);
        } else {
            $this->enabled = false;
            $fields = array();
        }

        $values = Eddditor::parse_values($fields, $values);
        $this->formatted_values = $values['formatted'];
        $this->form = new Eddditor_Form('options', $fields, $values['raw']);

        switch ($this->type) {
            case 'post':
                $this->form->set_title(__('Post options', 'eddditor'));
                break;
            case 'row':
                $this->form->set_title(__('Row options', 'eddditor'));
                break;
            case 'element':
                $this->form->set_title(__('Element options', 'eddditor'));
                break;
        }
    }


    /**
     * Check if an ACF field group is available for this option type
     *
     * @return boolean Signals whether options are available for this type
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
                    'values' => $this->formatted_values
                );
            default:
                return null;
        }
    }
    
    
}



