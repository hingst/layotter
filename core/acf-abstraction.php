<?php


/**
 * This abstraction layer makes it easier to adapt Layotter if ACF changes its API
 */
class Layotter_ACF
{
    const REQUIRED_VERSION = '4.4.3';
    const REQUIRED_PRO_VERSION = '5.3.0';


    public static function is_installed() {
        return class_exists('acf');
    }


    public static function is_pro_installed() {
        return class_exists('acf_pro');
    }


    public static function is_version_compatible() {
        if (self::is_pro_installed()) {
            return version_compare(acf_get_setting('version'), self::REQUIRED_PRO_VERSION) >= 0;
        } else {
            return version_compare(acf()->get_info('version'), self::REQUIRED_VERSION) >= 0;
        }
    }


    public static function get_all_field_groups() {
        if (self::is_pro_installed()) {
            return acf_get_field_groups();
        } else {
            return apply_filters('acf/get_field_groups', array());
        }
    }


    public static function get_filtered_field_groups($filters) {
        if (self::is_pro_installed()) {
            $field_groups = self::get_all_field_groups();
            $filtered_field_groups = array();

            foreach ($field_groups as $field_group) {
                $visible = acf_get_field_group_visibility($field_group, $filters);
                if ($visible) {
                    $filtered_field_groups[] = $field_group;
                }
            }

            return $filtered_field_groups;
        } else {
            return apply_filters('acf/location/match_field_groups', array(), $filters);
        }
    }


    public static function is_field_group_visible($field_group, $filters) {
        if (self::is_pro_installed()) {
            return acf_get_field_group_visibility($field_group, $filters);
        } else {
            $filtered_field_groups = apply_filters('acf/location/match_field_groups', array(), $filters);
            foreach ($filtered_field_groups as $group) {
                if ($field_group['id'] == $group['id']) {
                    return true;
                }
            }
            return false;
        }
    }


    public static function get_fields($field_group) {
        if (self::is_pro_installed()) {
            return acf_get_fields($field_group);
        } else {
            return apply_filters('acf/field_group/get_fields', array(), $field_group['id']);
        }
    }


    public static function get_form_html($fields) {
        ob_start();
        acf_render_fields(0, $fields); // 0 = post_id
        return ob_get_clean();
    }


    public static function get_field_group_by_id($id) {
        if (self::is_pro_installed()) {
            return _acf_get_field_group_by_id($id);
        } else {
            $field_groups = self::get_all_field_groups();
            foreach ($field_groups as $field_group) {
                if ($field_group['id'] == $id) {
                    return $field_group;
                }
            }
            return false;
        }
    }


    public static function get_field_group_by_key($key) {
        if (self::is_pro_installed()) {
            return _acf_get_field_group_by_key($key);
        } else {
            // keys are not supported with ACF 4, fall back to ID
            return self::get_field_group_by_id($key);
        }
    }


    public static function format_value($value, $field_data) {
        if (self::is_pro_installed()) {
            return acf_format_value($value, 0, $field_data); // 0 = post_id
        } else {
            return apply_filters('acf/format_value', $value, 0, $field_data); // 0 = post_id
        }
    }

}
