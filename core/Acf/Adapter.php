<?php

namespace Layotter\Acf;

use InvalidArgumentException;
use Layotter\Initializer;

class Adapter {

    /**
     * @return bool
     */
    public static function is_installed() {
        return defined('ACF');
    }

    /**
     * @return bool
     */
    public static function is_version_compatible() {
        return defined('ACF_VERSION')
            && version_compare(ACF_VERSION, Initializer::REQUIRED_ACF_VERSION) >= 0;
    }

    /**
     * @return bool
     */
    public static function meets_requirements() {
        return self::is_installed() && self::is_version_compatible();
    }

    /**
     * Hooked to admin_notices to provide human-readable information in case of unmet requirements.
     */
    public static function print_error() {
        $message = '';

        if (!self::is_installed()) {
            $message = sprintf(__('Layotter requires the <a href="%s" target="_blank">Advanced Custom Fields</a> plugin, please install it before using Layotter.', 'layotter'), 'http://www.advancedcustomfields.com');
        } else if (!self::is_version_compatible()) {
            $message = sprintf(__('Your version of Advanced Custom Fields is outdated. Please install version %s or higher to be able to use Layotter.', 'layotter'), Initializer::REQUIRED_ACF_VERSION);
        }

        ?>
        <div class="error">
            <p>
                <?php echo $message; ?>
            </p>
        </div>
        <?php
    }

    /**
     * @return string
     */
    public static function get_field_group_post_type() {
        return 'acf-field-group';
    }

    /**
     * @return array
     */
    public static function get_all_field_groups() {
        return acf_get_field_groups();
    }

    /**
     * Gets the field group key for Layotter's example element.
     *
     * @return string
     */
    public static function get_example_field_group_key() {
        return 'group_layotter_example';
    }

    /**
     * Gets the field key for the WYSIWYG field in Layotter's example element.
     *
     * @return string
     */
    public static function get_example_field_group_field_key() {
        return 'field_layotter_example';
    }

    /**
     * Gets ACF field groups that match a given set of filters.
     *
     * @param array $filters ACF filters (e.g. post_type => 'post').
     * @return array
     */
    public static function get_filtered_field_groups($filters) {
        if (!is_array($filters)) {
            throw new InvalidArgumentException();
        }

        $field_groups = self::get_all_field_groups();
        $filtered_field_groups = [];

        // TODO: check if a simpler rule matching method is available in current ACF version, see http://www.advancedcustomfields.com/resources/custom-location-rules/

        foreach ($field_groups as $field_group) {
            foreach ($field_group['location'] as $rules) {
                if (empty($rules)) {
                    continue;
                }

                // assume field group matches
                $match = true;

                // field group should only match if it has a Layotter rule
                $found_layotter_rule = false;

                if (is_array($rules)) {
                    foreach ($rules as $rule) {
                        if ($rule['param'] == 'layotter') {
                            $match = apply_filters('acf/location/rule_match/layotter', $match, $rule, $filters);
                            $found_layotter_rule = true;
                        } else if ($rule['param'] == 'post_type' && !isset($filters['post_type'])) {
                            // if a post type rule exists but no filter, ignore the rule
                            $match = true;
                        } else if ($rule['param'] == 'post_type') {
                            $match = apply_filters('acf/location/rule_match/post_type', $match, $rule, $filters);
                        } else {
                            // other rules are not supported
                            $match = false;
                        }

                        if (!$match) {
                            break;
                        }
                    }

                    if ($found_layotter_rule && $match) {
                        $filtered_field_groups[] = $field_group;
                    }
                }
            }
        }

        return $filtered_field_groups;
    }

    /**
     * Checks if a field group matches a set of ACF filters.
     *
     * @param array $field_group
     * @param array $filters
     * @return bool
     */
    public static function is_field_group_visible($field_group, $filters) {
        if (!is_array($field_group) || !is_array($filters)) {
            throw new InvalidArgumentException();
        }

        return acf_get_field_group_visibility($field_group, $filters);
    }

    /**
     * Gets all fields for an ACF field group.
     *
     * @param array $field_group
     * @return array ACF fields, or empty array if the group doesn't exist.
     */
    public static function get_fields($field_group) {
        if (!is_array($field_group)) {
            throw new InvalidArgumentException();
        }

        return acf_get_fields($field_group);
    }

    /**
     * Renders form for a collection of ACF fields.
     *
     * @param array $fields
     * @param int $id An element's post ID to fetch values from, if 0 no values are fetched.
     * @return string
     */
    public static function render_form($fields, $id) {
        if (!is_array($fields) || !is_int($id)) {
            throw new InvalidArgumentException();
        }

        ob_start();
        acf_render_fields($fields, $id);
        return ob_get_clean();
    }

    /**
     * Gets an ACF field group by its key or ID.
     *
     * @param string|int $key_or_id
     * @return array|bool The field group, or false if the key or ID doesn't exist.
     */
    public static function get_field_group($key_or_id) {
        if (!is_string($key_or_id) && !is_int($key_or_id)) {
            throw new InvalidArgumentException();
        }

        return acf_get_field_group($key_or_id);
    }

    /**
     * Prints form wrapper HTML containing all necessary hidden fields.
     */
    public static function print_form_wrapper() {
        ?>
        <div class="acf-postbox">
            <div id="acf-form-data" class="acf-hidden">
                <input type="hidden" name="_acf_post_id" value="{{ form.editableID }}" />
                <input type="hidden" name="_acf_nonce" value="{{ form.nonce }}" />
                <input type="hidden" name="_acf_validation" value="1" />
                <input type="hidden" name="_acf_ajax" value="1" />
                <input type="hidden" name="_acf_changed" value="0" />
            </div>
            <div class="acf-fields" ng-bind-html="form.fields | rawHtml"></div>
        </div>
        <?php
    }

    /**
     * Gets the name of the nonce that ACF validates when saving an element.
     *
     * @return string
     */
    public static function get_nonce_name() {
        return 'post';
    }

    /**
     * @param string $field_name
     * @param mixed $value Field value, can be any type that ACF supports.
     * @param int $post_id
     */
    public static function update_field_value($field_name, $value, $post_id) {
        if (!is_string($field_name) || !is_int($post_id)) {
            throw new InvalidArgumentException();
        }

        // TODO: does this work for arrays containing strings that need slashing?
        $new_value = is_string($value) ? addslashes($value) : $value;
        update_field($field_name, $new_value, $post_id);
    }

    /**
     * @param string $field_name
     * @param int $post_id
     * @return mixed Field value as provided by ACF.
     */
    public static function get_field_value($field_name, $post_id) {
        if (!is_string($field_name) || !is_int($post_id)) {
            throw new InvalidArgumentException();
        }

        return get_field($field_name, $post_id);
    }
}
