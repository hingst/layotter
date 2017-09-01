<?php

namespace Layotter\Acf;

/**
 * Add custom location rules for ACF
 *
 * Allows us to define post, row and element options by selecting an
 * option in ACF's location settings when creating a field group.
 *
 * learn more: http://www.advancedcustomfields.com/resources/custom-location-rules/
 */
class LocationRules {

    /**
     * Add 'Use with layotter' as first-level location option
     *
     * @param array $choices Options
     * @return array More options
     */
    public static function category($choices) {
        $choices['Advanced']['layotter'] = __('Use with Layotter', 'layotter');
        return $choices;
    }

    /**
     * Add second-level location options
     *
     * @param array $choices Options
     * @return array More options
     */
    public static function options($choices) {
        $choices['element'] = __('Use as element', 'layotter');
        $choices['post_options'] = __('Use for post options', 'layotter');
        $choices['row_options'] = __('Use for row options', 'layotter');
        $choices['col_options'] = __('Use for column options', 'layotter');
        $choices['element_options'] = __('Use for element options', 'layotter');
        return $choices;
    }

    /**
     * Determine whether a field group is associated with a specific option
     *
     * @param bool $match Did it match?
     * @param array $rule Rule to check
     * @param array $options User's selected options
     * @return bool
     */
    public static function match_rules($match, $rule, $options) {
        if (isset($options['layotter']) && $rule['value'] == $options['layotter']) {
            return true;
        } else {
            return false;
        }
    }

}
