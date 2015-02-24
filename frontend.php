<?php


/**
 * Filter the_content for frontend output
 * 
 * These functions loop through all rows, cols and elements and run them
 * through user-provided filters (where present), or wrap with html code
 * provided in settings (if no user-provided filter is present).
 * 
 * Everything required to render the frontend view resides in this file.
 */
add_filter('the_content', 'eddditor_filter_content', 99999);


/**
 * Filter post content
 *
 * @param $input string Current unfiltered post content
 * @return string HTML for frontend view of the current post
 */
function eddditor_filter_content($input)
{
    global $post;
    $output = '';
    $content = Eddditor::get_content($post->ID);

    if(!is_array($content))
    {
        return $input;
    }
    $rows = eddditor_frontend_rows($content['rows']);
    
    if(has_filter('eddditor/post'))
    {
        $output .= apply_filters('eddditor/post', $rows, $content['options']['values']);
    }
    else
    {
        $settings = get_option('eddditor_settings_wrapper');
        $output .= $settings['html_before'] . $rows . $settings['html_after'];
    }
    
    return $output;
}


/**
 * Filter rows
 * 
 * @param array $rows Array containing multiple rows' data
 * @return string HTML for frontend view of provided rows
 */
function eddditor_frontend_rows($rows)
{
    $output = '';
    $has_filter = has_filter('eddditor/row');
    if(!$has_filter)
    {
        $settings = get_option('eddditor_settings_rows');
    }
    
    foreach($rows as $row)
    {
        $row_layout = explode(' ', $row['layout']);
        foreach($row['cols'] as $i => &$col)
        {
            $col['width']
                = isset($row_layout[$i])
                ? $row_layout[$i]
                : '';
        }
        
        $cols = eddditor_frontend_cols($row['cols']);
        if($has_filter)
        {
            $output .= apply_filters('eddditor/row', $cols, $row['options']['values']);
        }
        else
        {
            $output .= $settings['html_before'] . $cols . $settings['html_after'];
        }
    }
    
    return $output;
}


/**
 * Filter cols
 * 
 * @param array $cols Array containing multiple cols' data
 * @return string HTML for frontend view of provided cols
 */
function eddditor_frontend_cols($cols)
{
    $output = '';
    $has_filter = has_filter('eddditor/col');
    if(!$has_filter)
    {
        $settings = get_option('eddditor_settings_cols');
    }
    
    foreach($cols as $col)
    {
        $elements = eddditor_frontend_elements($col['elements']);
        $class = Eddditor_Settings::get_col_layout_class($col['width']);
        if($has_filter)
        {
            $output .= apply_filters('eddditor/col', $elements, $class);
        }
        else
        {
            $html_before = str_replace('%%CLASS%%', $class, $settings['html_before']);
            $output .= $html_before . $elements . $settings['html_after'];
        }
    }
    
    return $output;
}


/**
 * Filter elements
 * 
 * @param array $elements Array containing multiple elements' data
 * @return string HTML for frontend view of provided elements
 */
function eddditor_frontend_elements($elements)
{
    $output = '';
    $has_filter = has_filter('eddditor/element');
    if(!$has_filter)
    {
        $settings = get_option('eddditor_settings_elements');
    }
    
    foreach($elements as $element)
    {
        if($has_filter)
        {
            $output .= apply_filters('eddditor/element', $element['view'], $element['options']['values']);
        }
        else
        {
            $output .= $settings['html_before'] . $element['view'] . $settings['html_after'];
        }
    }
    
    return $output;
}