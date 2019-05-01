<?php

namespace Layotter\Acf;

use Layotter\Core;

/**
 * This abstraction layer wraps all backend interaction with ACF, making it easier to adapt to new versions.
 */
class Adapter {

    /**
     * Checks if ACF is installed.
     *
     * @return bool
     */
    public static function is_installed() {
        return defined('ACF');
    }

    /**
     * Checks if the installed version of ACF is compatible with Layotter.
     *
     * @return bool
     */
    public static function is_version_compatible() {
        return defined('ACF_VERSION')
            && version_compare(ACF_VERSION, Core::REQUIRED_ACF_VERSION) >= 0;
    }

    /**
     * Checks if all requirements to run Layotter are met.
     *
     * @return bool
     */
    public static function meets_requirements() {
        return self::is_installed() && self::is_version_compatible();
    }

    /**
     * Prints an error message if any requirements to run Layotter aren't met.
     */
    public static function print_error() {
        $message = '';

        if (!self::is_installed()) {
            $message = sprintf(__('Layotter requires the <a href="%s" target="_blank">Advanced Custom Fields</a> plugin, please install it before using Layotter.', 'layotter'), 'http://www.advancedcustomfields.com');
        } else if (!self::is_version_compatible()) {
            $message = sprintf(__('Your version of Advanced Custom Fields is outdated. Please install version %s or higher to be able to use Layotter.', 'layotter'), Core::REQUIRED_ACF_VERSION);
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
     * Gets the post type slug for ACF field groups.
     *
     * @return string The post type slug.
     */
    public static function get_field_group_post_type() {
        return 'acf-field-group';
    }

    /**
     * Gets meta information for all registered field groups.
     *
     * @return array All registered field groups.
     */
    public static function get_all_field_groups() {
        return acf_get_field_groups();
    }

    /**
     * Returns the field group key for Layotter's example element.
     *
     * @return string The field group key.
     */
    public static function get_example_field_group_key() {
        return 'group_layotter_example';
    }

    /**
     * Returns the field key for the WYSIWYG field in Layotter's example element.
     *
     * @return string The field key.
     */
    public static function get_example_field_group_field_key() {
        return 'field_layotter_example';
    }

    /**
     * Gets meta information for ACF field groups that match a given set of location rules.
     *
     * @param array $filters ACF location rules.
     * @return array Matching ACF field groups.
     */
    public static function get_filtered_field_groups($filters) {
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
     * Checks if a given field group matches a given set of location rules.
     *
     * @param array $field_group ACF field group.
     * @param array $filters ACF location rules.
     * @return bool
     */
    public static function is_field_group_visible($field_group, $filters) {
        return acf_get_field_group_visibility($field_group, $filters);
    }

    /**
     * Gets fields for a given field group.
     *
     * @param array $field_group ACF field group.
     * @return array ACF fields, or empty array if the group doesn't exist.
     */
    public static function get_fields($field_group) {
        return acf_get_fields($field_group);
    }

    /**
     * Gets form HTML for a collection of fields.
     *
     * @param array $fields A collection of ACF fields.
     * @param int $id An element's post ID to fetch field values from, or 0 if it's a new element.
     * @return string The rendered form HTML.
     */
    public static function get_form_html($fields, $id = 0) {
        ob_start();
        acf_render_fields($fields, $id);
        return ob_get_clean();
    }

    /**
     * Gets a field group by its key or ID.
     *
     * @param string|int $key_or_id ACF field group key or ID.
     * @return array|bool The field group, or false if the key or ID doesn't exist.
     */
    public static function get_field_group($key_or_id) {
        return acf_get_field_group($key_or_id);
    }

    /**
     * Prints form wrapper HTML containing all necessary hidden fields.
     */
    public static function output_form_wrapper() {
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
     * @return string The nonce name.
     */
    public static function get_nonce_name() {
        return 'post';
    }

    /**
     * Updates a field's value for a given post ID.
     *
     * @param string $field_name The field's name.
     * @param mixed $value New value, can be any type that ACF supports.
     * @param int $post_id The post ID.
     */
    public static function update_field_value($field_name, $value, $post_id) {
        // TODO: does this work for arrays containing strings that need slashing?
        $new_value = is_string($value) ? addslashes($value) : $value;
        update_field($field_name, $new_value, $post_id);
    }

    /**
     * Gets a field's value for a given post ID.
     *
     * @param string $field_name The field's name.
     * @param int $post_id The post ID.
     * @return mixed Field value as provided by ACF.
     */
    public static function get_field_value($field_name, $post_id) {
        return get_field($field_name, $post_id);
    }
}
