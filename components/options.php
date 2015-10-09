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

        $this->form->set_icon('cog');
        $titles = array(
            'post' => __('Post options', 'layotter'),
            'row' => __('Row options', 'layotter'),
            'col' => __('Column options', 'layotter'),
            'element' => __('Element options', 'layotter')
        );
        if (isset($titles[$this->type])) {
            $this->form->set_title($titles[$this->type]);
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
        $fields = array();

        // get ACF field groups for this option and post type
        $field_groups = Layotter_ACF::get_filtered_field_groups(array(
            'post_type' => $post_type,
            'layotter' => $this->type . '_options'
        ));

        foreach ($field_groups as $field_group) {
            $fields = array_merge($fields, Layotter_ACF::get_fields($field_group));
        }

        return $fields;
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



