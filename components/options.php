<?php


class Eddditor_Options extends Eddditor_Editable implements JsonSerializable {


    private
        $type = '',
        $enabled = false;


    /**
     * Create a new options object
     * 
     * @param string $type Options type - can be 'post', 'row', 'col' or 'element'
     * @param mixed $values Array containing option values, or false for default values
     * @param int|string $post_id
     */
    public function __construct($type, $values = array(), $post_id = 0) {
        if (in_array($type, array('post', 'row', 'col', 'element'))) {
            $this->type = $type;
        }

        $fields = $this->get_fields($post_id);
        $this->apply_values($fields, $values);

        if (!empty($fields)) {
            $this->enabled = true;
        }

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


    private function get_fields($post_id) {
        $post_id = intval($post_id);
        $post_type = get_post_type($post_id);

        // check if a field group exists for this option type
        $field_groups = acf_get_field_groups(array(
            'post_type' => $post_type,
            'eddditor' => $this->type . '_options'
        ));

        if (is_array($field_groups) AND !empty($field_groups)) {
            return acf_get_fields($field_groups[0]);
        } else {
            return array();
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


    public function jsonSerialize() {
        return $this->clean_values;
    }
    
    
}



