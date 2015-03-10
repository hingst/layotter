<?php


/**
 * Add shortcode for frontend output
 *
 * Eddditor uses the shortcode [eddditor] containing the JSON-encoded content
 * structure of a given post.
 * 
 * These functions loop through all rows, cols and elements and run them
 * through user-provided filters (where present), or wrap with html code
 * provided in settings (if no user-provided filter is present).
 * 
 * Everything required to render the frontend view resides in this file.
 */
add_shortcode('eddditor', 'eddditor_frontend_shortcode');


/**
 * Process post content
 *
 * @param $atts array Required by Wordpress, but unused
 * @param string $input Current unfiltered post content
 * @return string HTML for frontend view of the current post
 */
function eddditor_frontend_shortcode($atts, $input = '') {
    $output = '';

    global $post;
    $content_structure = Eddditor::get_content_structure($post->ID);

    // $content_structure should always be an array, this is just a failsafe in case the JSON data was corrupted
    if (!is_array($content_structure)) {
        return $input;
    }

    $rows_html = eddditor_frontend_rows($content_structure['rows']);
    if (has_filter('eddditor/post')) {
        $options = new Eddditor_Options('post', $content_structure['options']['values']);
        $output .= apply_filters('eddditor/post', $rows_html, $options->get('formatted_values'));
    } else {
        $settings = get_option('eddditor_settings_wrapper');
        $output .= $settings['html_before'] . $rows_html . $settings['html_after'];
    }

    return $output;
}


/**
 * Process rows
 * 
 * @param array $rows Array containing multiple rows' data
 * @return string HTML for frontend view of provided rows
 */
function eddditor_frontend_rows($rows) {
    $output = '';

    $has_filter = has_filter('eddditor/row');
    if (!$has_filter) {
        $settings = get_option('eddditor_settings_rows');
    }
    
    foreach ($rows as $row) {
        // explode row layout (looks like 'third third third') and apply to individual cols
        $row_layout = explode(' ', $row['layout']);
        foreach ($row['cols'] as $i => &$col) {
            $col['width']
                = isset($row_layout[$i])
                ? $row_layout[$i]
                : '';
        }
        
        $cols_html = eddditor_frontend_cols($row['cols']);
        if ($has_filter) {
            $options = new Eddditor_Options('row', $row['options']['values']);
            $output .= apply_filters('eddditor/row', $cols_html, $options->get('formatted_values'));
        } else {
            $output .= $settings['html_before'] . $cols_html . $settings['html_after'];
        }
    }
    
    return $output;
}


/**
 * Process cols
 * 
 * @param array $cols Array containing multiple cols' data
 * @return string HTML for frontend view of provided cols
 */
function eddditor_frontend_cols($cols) {
    $output = '';

    $has_filter = has_filter('eddditor/col');
    if (!$has_filter) {
        $settings = get_option('eddditor_settings_cols');
    }
    
    foreach ($cols as $col) {
        // translate col layouts to CSS classes (e.g. 'third' could become 'col size4of12')
        $class = Eddditor_Settings::get_col_layout_class($col['width']);

        $elements_html = eddditor_frontend_elements($col['elements']);
        if ($has_filter) {
            $output .= apply_filters('eddditor/col', $elements_html, $class);
        } else {
            $html_before = str_replace('%%CLASS%%', $class, $settings['html_before']);
            $output .= $html_before . $elements_html . $settings['html_after'];
        }
    }
    
    return $output;
}


/**
 * Process elements
 * 
 * @param array $elements Array containing multiple elements' data
 * @return string HTML for frontend view of provided elements
 */
function eddditor_frontend_elements($elements) {
    $output = '';

    $has_filter = has_filter('eddditor/element');
    if (!$has_filter) {
        $settings = get_option('eddditor_settings_elements');
    }
    
    foreach ($elements as $element) {
        if ($has_filter) {
            $options = new Eddditor_Options('element', $element['options']['values']);
            $output .= apply_filters('eddditor/element', $element['view'], $options->get('formatted_values'));
        } else {
            $output .= $settings['html_before'] . $element['view'] . $settings['html_after'];
        }
    }
    
    return $output;
}