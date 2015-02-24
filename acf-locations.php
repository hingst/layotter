<?php


/**
 * Add custom location rules for ACF
 * 
 * Allows users to define post, row and element options by simply selecting an
 * option in ACF's location settings when creating a field group.
 */


/**
 * Add 'Use with eddditor' as first-level location option
 */
add_filter('acf/location/rule_types', 'eddditor_acf_location');
function eddditor_acf_location($choices) {
    $choices['Advanced']['eddditor'] = 'Use with Eddditor';
    return $choices;
}


/**
 * Add second-level location options
 */
add_filter('acf/location/rule_values/eddditor', 'eddditor_acf_location_options');
function eddditor_acf_location_options($choices) {
    $choices['element'] = 'Use as element';
    $choices['post_options'] = 'Use for post options';
    $choices['row_options'] = 'Use for row options';
    $choices['element_options'] = 'Use for element options';

    return $choices;
}


/**
 * Determine whether a field group is associated with a specific option
 * 
 * TODO: figure out and document what exactly I did here
 */
add_filter('acf/location/rule_match/eddditor', 'eddditor_acf_location_match_rules', 10, 3);
function eddditor_acf_location_match_rules($match, $rule, $options) {
    if($rule['param'] == 'eddditor' AND $rule['value'] == $options['eddditor'])
    {
        return true;
    }
    
    return $match;
}
