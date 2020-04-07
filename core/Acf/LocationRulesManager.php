<?php

namespace Layotter\Acf;

/**
 * @see http://www.advancedcustomfields.com/resources/custom-location-rules/
 */
class LocationRulesManager {

    public static function register() {
        add_filter('acf/location/rule_types', [__CLASS__, 'category']);
        add_filter('acf/location/rule_values/layotter', [__CLASS__, 'options']);
        add_filter('acf/location/rule_match/layotter', [__CLASS__, 'match_rules'], 10, 3);
    }

    /**
     * Adds 'Use with layotter' as a first level location option.
     *
     * @param array $choices
     * @return array
     */
    public static function category($choices) {
        $choices['Advanced']['layotter'] = __('Use with Layotter', 'layotter');
        return $choices;
    }

    /**
     * Adds second-level location options.
     *
     * @param array $choices
     * @return array
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
     * Determines if a set of options match a given rule.
     *
     * @param bool $match Flags if previous rules matched.
     * @param array $rule The rule to check against.
     * @param array $options The set of options to check.
     * @return bool
     */
    public static function match_rules($match, $rule, $options) {
        if (isset($options['layotter']) && $rule['value'] == $options['layotter']) {
            return true; // TODO: shouldn't this return $match instead?
        } else {
            return false;
        }
    }
}
