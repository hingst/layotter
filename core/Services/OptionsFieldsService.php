<?php

namespace Layotter\Services;

use InvalidArgumentException;
use Layotter\Acf\Adapter;
use Layotter\Models\Form;
use Layotter\Models\Options;

class OptionsFieldsService {

    /**
     * @param Options $options
     * @return array ACF fields in a format provided by ACF
     */
    public static function get_fields($options) {
        if (!($options instanceof Options)) {
            throw new InvalidArgumentException();
        }

        $filters = [
            'layotter' => $options->get_type() . '_options'
        ];

        if (!is_null($options->get_post_type_context())) {
            $filters['post_type'] = $options->get_post_type_context();
        }

        $field_groups = Adapter::get_filtered_field_groups($filters);
        $fields = [];

        foreach ($field_groups as $field_group) {
            $fields = array_merge($fields, Adapter::get_fields($field_group));
        }

        return $fields;
    }

    /**
     * @param Options $options
     * @return bool
     */
    public static function has_fields($options) {
        return !empty(self::get_fields($options));
    }

    /**
     * @param Options $options
     * @return array Associative array with field name => field value
     */
    public static function get_values($options) {
        if (!($options instanceof Options)) {
            throw new InvalidArgumentException();
        }

        $values = [];
        $fields = OptionsFieldsService::get_fields($options);

        foreach ($fields as $field) {
            $values[ $field['name'] ] = Adapter::get_field_value($field['name'], $options->get_id());
        }

        return $values;
    }

    /**
     * @param Options $options
     * @return Form
     */
    public static function get_form($options) {
        if (!($options instanceof Options)) {
            throw new InvalidArgumentException();
        }

        return new Form(
            $options->get_id(),
            self::get_form_title($options->get_type()),
            'cog',
            Adapter::render_form(OptionsFieldsService::get_fields($options), $options->get_id()),
            wp_create_nonce(Adapter::get_nonce_name())
        );
    }

    /**
     * @param string $type
     * @return string
     */
    private static function get_form_title($type) {
        switch ($type) {
            case 'post':
                return __('Post options', 'layotter');
            case 'row':
                return __('Row options', 'layotter');
            case 'column':
                return __('Column options', 'layotter');
            case 'element':
                return __('Element options', 'layotter');
            default:
                return '';
        }
    }
}