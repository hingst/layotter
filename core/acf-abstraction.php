<?php


/**
 * This abstraction layer makes it easier to adapt Layotter if ACF changes its API
 */
class Layotter_ACF
{
    const REQUIRED_VERSION = '5.3.0';


    public static function is_installed() {
        return class_exists('acf');
    }


    public static function is_pro_installed() {
        return class_exists('acf_pro');
    }


    public static function is_version_compatible() {
        return function_exists('acf_get_setting') AND version_compare(acf_get_setting('version'), self::REQUIRED_VERSION) >= 0;
    }


    public static function get_all_field_groups() {
        return acf_get_field_groups();
    }


    public static function get_filtered_field_groups($filters) {
        $field_groups = self::get_all_field_groups();
        $filtered_field_groups = array();

        foreach ($field_groups as $field_group) {
            $visible = acf_get_field_group_visibility($field_group, $filters);
            if ($visible) {
                $filtered_field_groups[] = $field_group;
            }
        }

        return $filtered_field_groups;
    }


    public static function is_field_group_visible($field_group, $filters) {
        return acf_get_field_group_visibility($field_group, $filters);
    }


    public static function get_fields($field_group) {
        return acf_get_fields($field_group);
    }


    public static function get_form_html($fields) {
        ob_start();
        acf_render_fields(0, $fields); // 0 = post_id
        return ob_get_clean();
    }


    public static function get_field_group_by_id($id) {
        return _acf_get_field_group_by_id($id);
    }


    public static function get_field_group_by_key($key) {
        return _acf_get_field_group_by_key($key);
    }


    public static function format_value($value, $field_data) {
        return acf_format_value($value, 0, $field_data); // 0 = post_id
    }

}
