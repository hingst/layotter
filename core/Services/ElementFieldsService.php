<?php

namespace Layotter\Services;

use Exception;
use Layotter\Acf\Adapter;
use Layotter\Models\Element;
use Layotter\Models\Form;

class ElementFieldsService {

    /**
     * @param Element $element
     * @return array ACF fields in a format provided by ACF
     * @throws Exception
     */
    public static function get_fields($element) {
        $group = $element->get_type()->get_field_group();

        // ACF field group can be provided as post id (int) or slug ('group_xyz')
        if (!is_int($group) && !is_string($group)) {
            throw new Exception('Field group for type ' . $element->get_type()->get_model()->get_name() . ' has not been set correctly.');
        }

        $field_group = Adapter::get_field_group($group);

        if (!$field_group) {
            throw new Exception('No field group found for ID or key ' . $group . ' (type is ' . $element->get_type()->get_model()->get_name() . ').');
        }

        return Adapter::get_fields($field_group);
    }

    /**
     * @param Element $element
     * @return array Associative array with field name => field value
     * @throws Exception
     */
    public static function get_values($element) {
        $values = [];
        $fields = self::get_fields($element);

        foreach ($fields as $field) {
            $values[ $field['name'] ] = Adapter::get_field_value($field['name'], $element->get_id());
        }

        return $values;
    }

    /**
     * @param Element $element
     * @return Form
     * @throws Exception
     */
    public static function get_form($element) {
        return new Form(
            $element->get_id(),
            $element->get_type()->get_model()->get_title(),
            $element->get_type()->get_model()->get_icon(),
            Adapter::render_form(ElementFieldsService::get_fields($element), $element->get_id()),
            wp_create_nonce(Adapter::get_nonce_name())
        );
    }
}