<?php


/**
 * Options for a post, row, columns or element
 */
class Layotter_Options extends Layotter_Editable {


    private
        $type = '',
        $enabled = false;


    /**
     * Create new options
     * 
     * @param string $type Options type - can be 'post', 'row', 'col' or 'element'
     * @param mixed $values Array containing option values, or empty array for default values
     * @param int $post_id Post ID (to determine if options are enabled for the current post in AJAX context)
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
                $this->form->set_title(__('Post options', 'layotter'));
                $this->form->set_icon('file-text-o');
                break;
            case 'row':
                $this->form->set_title(__('Row options', 'layotter'));
                $this->form->set_icon('align-justify');
                break;
            case 'col':
                $this->form->set_title(__('Column options', 'layotter'));
                $this->form->set_icon('columns');
                break;
            case 'element':
                $this->form->set_title(__('Element options', 'layotter'));
                $this->form->set_icon('table');
                break;
        }
    }


    /**
     * Get ACF fields for options in a specific post
     *
     * @param int $post_id Post ID
     * @return array ACF fields
     */
    private function get_fields($post_id) {
        $post_id = intval($post_id);
        $post_type = get_post_type($post_id);

        // check if a field group exists for this option type
        $field_groups = acf_get_field_groups(array(
            'post_type' => $post_type,
            'layotter' => $this->type . '_options'
        ));

        if (is_array($field_groups) AND !empty($field_groups)) {
            return acf_get_fields($field_groups[0]);
        } else {
            return array();
        }
    }


    /**
     * Check if this option type is enabled for the current post (i.e. an ACF field group exists)
     *
     * @return boolean Whether options are enabled
     */
    public function is_enabled() {
        return $this->enabled;
    }


    /**
     * Return array representation of option values for use in json_encode()
     *
     * PHP's JsonSerializable interface would be cleaner, but it's only available >= 5.4.0
     *
     * @return array Array representation of option values
     */
    public function to_array() {
        return $this->clean_values;
    }
    
    
}



