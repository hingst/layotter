<?php


/**
 * A single row
 */
class Layotter_Row {

    private
        $layout = '',
        $options,
        $cols = array();


    /**
     * Create a new row
     *
     * @param array $structure Row structure
     */
    public function __construct($structure) {
        $structure = $this->validate_structure($structure);
        $structure = $this->apply_layout($structure);

        $this->layout = $structure['layout'];
        $this->options = Layotter::assemble_options($structure['options_id']);

        foreach ($structure['cols'] as $col) {
            $this->cols[] = new Layotter_Col($col);
        }
    }


    /**
     * Validate an array containing a row's structure
     *
     * Validates array structure and presence of required key/value pairs
     *
     * @param array $structure Row structure
     * @return array Validated row structure
     */
    private function validate_structure($structure) {
        if (!is_array($structure)) {
            $structure = array();
        }

        if (!isset($structure['layout']) OR !is_string($structure['layout'])) {
            $structure['layout'] = Layotter_Settings::get_default_row_layout();
        }

        if (!isset($structure['options']) OR !is_array($structure['options'])) {
            $structure['options'] = array();
        }

        if (!isset($structure['cols']) OR !is_array($structure['cols'])) {
            $structure['cols'] = array();
        }

        return $structure;
    }


    /**
     * Take a row structure and apply the row layout (e.g. '1/3 1/3 1/3') to the contained columns
     *
     * @param array $structure Row structure with layout and columns
     * @return array Row structure with layout applied to columns
     */
    private function apply_layout($structure) {
        $layout_array = explode(' ', $structure['layout']);

        foreach ($structure['cols'] as $i => &$col) {
            $col['width']
                = isset($layout_array[$i])
                ? $layout_array[$i]
                : '';
        }

        return $structure;
    }


    /**
     * Return array representation of this row for use in json_encode()
     *
     * PHP's JsonSerializable interface would be cleaner, but it's only available >= 5.4.0
     *
     * @return array Array representation of this row
     */
    public function to_array() {
        $cols = array();

        foreach ($this->cols as $col) {
            $cols[] = $col->to_array();
        }

        return array(
            'layout' => $this->layout,
            'options_id' => $this->options->get_id(),
            'cols' => $cols
        );
    }


    /**
     * Return frontend HTML for this row
     *
     * @param array $post_options Formatted options for the parent post
     * @return string Frontend HTML
     */
    public function get_frontend_view($post_options) {
        $cols_html = '';
        foreach ($this->cols as $col) {
            $cols_html .= $col->get_frontend_view($this->options->get_values(), $post_options);
        }

        if (has_filter('layotter/view/row')) {
            return apply_filters('layotter/view/row', $cols_html, $this->options->get_values(), $post_options);
        } else {
            $html_wrapper = Layotter_Settings::get_html_wrapper('rows');
            return $html_wrapper['before'] . $cols_html . $html_wrapper['after'];
        }
    }

}