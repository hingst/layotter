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


/**
 * Add 'Use with eddditor' as first-level location option
 */
add_filter('acf/location/rule_types', 'eddditor_acf_location_category');
function eddditor_acf_location_category($choices) {
    $choices['Advanced']['eddditor'] = __('Use with Eddditor', 'eddditor');
    return $choices;
}


/**
 * Add second-level location options
 */
add_filter('acf/location/rule_values/eddditor', 'eddditor_acf_location_options');
function eddditor_acf_location_options($choices) {
    $choices['element'] = __('Use as element', 'eddditor');
    $choices['post_options'] = __('Use for post options', 'eddditor');
    $choices['row_options'] = __('Use for row options', 'eddditor');
    $choices['element_options'] = __('Use for element options', 'eddditor');
    return $choices;
}


/**
 * Determine whether a field group is associated with a specific option
 */
add_filter('acf/location/rule_match/eddditor', 'eddditor_acf_location_match_rules', 10, 3);
function eddditor_acf_location_match_rules($match, $rule, $options) {
    if (isset($options['eddditor']) AND $rule['value'] == $options['eddditor']) {
        return true;
    } else {
        return $match;
    }
}
