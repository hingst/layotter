<?php


/**
 * This abstraction layer makes it easier to adapt Layotter if ACF changes its API
 */
class Layotter_ACF
{
    const REQUIRED_VERSION = '5.7.12';

    private static $error_message = '';


    /**
     * Check if ACF is installed
     *
     * @return bool
     */
    public static function is_installed() {
        return defined('ACF');
    }


    /**
     * Check if the installed version of ACF is compatible with this version of Layotter
     *
     * @return bool
     */
    public static function is_version_compatible() {
        return defined('ACF_VERSION')
            && version_compare(ACF_VERSION, self::REQUIRED_VERSION) >= 0;
    }


    /**
     * Check if a compatible version of ACF is installed and output an error message if not
     *
     * @return bool
     */
    public static function is_available() {
        if (!self::is_installed()) {
            self::$error_message = sprintf(__('Layotter requires the <a href="%s" target="_blank">Advanced Custom Fields</a> plugin, please install it before using Layotter.', 'layotter'), 'http://www.advancedcustomfields.com');
        } else if (!self::is_version_compatible()) {
            self::$error_message = sprintf(__('Your version of Advanced Custom Fields is outdated. Please install version %s or higher to be able to use Layotter.', 'layotter'), self::REQUIRED_VERSION);
        }

        if (!empty(self::$error_message)) {
            add_action('admin_notices', array(__CLASS__, 'print_error'));
            return false;
        }

        return true;
    }


    /**
     * Output an error message if ACF isn't installed (hooked to admin_notices by self::is_available())
     */
    public static function print_error() {
        ?>
        <div class="error">
            <p>
                <?php echo self::$error_message; ?>
            </p>
        </div>
        <?php
    }


    /**
     * Get the post type for ACF field groups depending on the installed version of ACF
     *
     * @return string Post type for ACF field groups
     */
    public static function get_field_group_post_type() {
        return 'acf-field-group';
    }


    /**
     * Get all ACF field groups
     *
     * @return array All ACF field groups (array format varies between the free and Pro versions of ACF)
     */
    public static function get_all_field_groups() {
        return acf_get_field_groups();
    }


    /**
     * Returns field group name for the example element that comes with Layotter
     *
     * @return string Field group name
     */
    public static function get_example_field_group_name() {
        return 'group_5605a65191086';
    }


    /**
     * Get ACF field groups that match a given set of location rules
     *
     * @param array $filters ACF location rules
     * @return array Filtered ACF field groups (array format varies between the free and Pro versions of ACF)
     */
    public static function get_filtered_field_groups($filters) {
        $field_groups = self::get_all_field_groups();
        $filtered_field_groups = array();

        foreach ($field_groups as $field_group) {
            foreach ($field_group['location'] as $location_group) {
                if (!empty($location_group)) {
                    foreach ($location_group as $rule) {
                        if ($rule['param'] == 'layotter') {
                            // force rule match for 'layotter' rules
                            $match = apply_filters('acf/location/rule_match/layotter', false, $rule, $filters);
                            if ($match) {
                                $filtered_field_groups[] = $field_group;
                            }
                        }
                    }
                }
            }
        }

        return $filtered_field_groups;
    }


    /**
     * Check if a given field group matches a given set of location rules
     *
     * @param array $field_group ACF field group (array format varies between the free and Pro versions of ACF)
     * @param array $filters ACF location rules
     * @return bool
     */
    public static function is_field_group_visible($field_group, $filters) {
        return acf_get_field_group_visibility($field_group, $filters);
    }


    /**
     * Get fields for a given field group
     *
     * @param array $field_group ACF field group (array format varies between the free and Pro versions of ACF)
     * @return array|bool ACF fields, or false or empty array (depending on the ACF version) if the group doesn't exist
     */
    public static function get_fields($field_group) {
        return acf_get_fields($field_group);
    }


    /**
     * Get form HTML for a set of fields
     *
     * @param array $fields ACF fields
     * @return string Form HTML
     */
    public static function get_form_html($fields) {
        ob_start();
        acf_render_fields($fields, 0);
        return ob_get_clean();
    }


    /**
     * Get a field group by its key or ID
     *
     * @param string|int $key_or_id ACF field group key (slug) or ID
     * @return array|bool ACF field group, or false or empty array (depending on the ACF version) if the key doesn't exist
     */
    public static function get_field_group($key_or_id) {
        return acf_get_field_group($key_or_id);
    }


    /**
     * Run a field value through ACF's formatting filters to prepare them for output
     *
     * For example, paragraphs and line breaks are added to WYSIWYG fields.
     *
     * @param mixed $value ACF field value
     * @param array $field_data Info about the field type as provided by ACF
     * @return mixed Formatted field value
     */
    public static function format_value($value, $field_data) {
        return acf_format_value($value, uniqid('layotter_acf_'), $field_data);
    }


    /**
     * Output form wrapper HTML depending on the installed version of ACF
     */
    public static function output_form_wrapper() {
        ?>
        <div class="acf-postbox">
            <div id="acf-form-data" class="acf-hidden">
                <input type="hidden" name="_acfnonce" value="{{ form.nonce }}">
                <input id="layotter-changed" type="hidden" name="_acfchanged" value="0">
            </div>
            <div class="acf-fields" ng-bind-html="form.fields | rawHtml"></div>
        </div>
        <?php
    }


    /**
     * Unwrap POST values from ACF wrapper
     *
     * @return array Raw field values
     */
    public static function unwrap_post_values() {
        $post_data = stripslashes_deep($_POST); // strip Wordpress magic quotes

        if (isset($post_data['acf']) AND is_array($post_data['acf'])) {
            return $post_data['acf'];
        }

        return array();
    }

}
