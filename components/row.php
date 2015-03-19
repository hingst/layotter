<?php


/**
 * A single row
 */
class Eddditor_Row implements JsonSerializable {

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
        $this->options = new Eddditor_Options('row', $structure['options']);

        foreach ($structure['cols'] as $col) {
            $this->cols[] = new Eddditor_Col($col);
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
            $structure['layout'] = Eddditor_Settings::get_default_row_layout();
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
     * Take a row structure and apply the row layout (e.g. 'third third third') to the contained columns
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
     * @return array Array representation of this row
     */
    public function jsonSerialize() {
        return array(
            'layout' => $this->layout,
            'options' => $this->options,
            'cols' => $this->cols
        );
    }


    /**
     * Return frontend HTML for this row
     *
     * @return string Frontend HTML
     */
    public function get_frontend_view() {
        $cols_html = '';
        foreach ($this->cols as $col) {
            $cols_html .= $col->get_frontend_view();
        }

        if (has_filter('eddditor/row')) {
            return apply_filters('eddditor/row', $cols_html, $this->options->get_formatted_values());
        } else {
            $settings = Eddditor_Settings::get_settings('rows');
            return $settings['html_before'] . $cols_html . $settings['html_after'];
        }
    }

}