<?php


/**
 * Abstract class for editable components (options and elements)
 */
abstract class Layotter_Editable {

    protected
        $id = 0,
        // new
        $icon,
        $title,
        $type;

    const
        TYPE_META_FIELD = 'layotter_editable_type';


    abstract protected function get_fields();


    /**
     * Get clean values
     *
     * @return array Clean values
     */
    final public function get_values() {
        return get_fields($this->id);
    }




    public function set_type($type) {
        $this->type = $type;
    }



    final public function get_form_data() {
        return array(
            'title' => $this->title,
            'icon' => $this->icon,
            'nonce' => wp_create_nonce('post'),
            'fields' => Layotter_ACF::get_form_html($this->get_fields(), $this->id)
        );
    }


    final public function get_form_json() {
        return json_encode($this->get_form_data());
    }


    final public function save_from_post_data() {
        // wp_insert_post triggers ACF hooks that read from $_POST and save custom fields
        // it's ridiculous
        $this->id = wp_insert_post(array(
            'post_type' => Layotter_Editable_Model::post_type,
            'meta_input' => array(
                self::TYPE_META_FIELD => $this->type
            )
        ));
    }


    public function get_id() {
        return $this->id;
    }

}
