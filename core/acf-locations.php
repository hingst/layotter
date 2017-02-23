<?php


/**
 * Add custom location rules for ACF
 * 
 * Allows users to define post, row and element options by simply selecting an
 * option in ACF's location settings when creating a field group.
 *
 * See ACF documentation on custom location rules for more info on what's happening in this file:
 * http://www.advancedcustomfields.com/resources/custom-location-rules/
 */
class Layotter_Acf_Location_Rules {

    /**
     * Add 'Use with layotter' as first-level location option
     */
    public static function category($choices) {
        $choices['Advanced']['layotter'] = __('Use with Layotter', 'layotter');
        return $choices;
    }


    /**
     * Add second-level location options
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
     */
    public static function match_rules($match, $rule, $options) {
        if (isset($options['layotter']) AND $rule['value'] == $options['layotter']) {
            return true;
        } else {
            return $match;
        }
    }

}