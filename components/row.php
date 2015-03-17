<?php


class Eddditor_Row implements JsonSerializable {

    private
        $layout = '',
        $options,
        $cols = array();


    public function __construct($structure) {
        $structure = $this->validate_structure($structure);
        $structure = $this->apply_layout($structure);

        $this->layout = $structure['layout'];
        $this->options = new Eddditor_Options('row', $structure['options']);

        foreach ($structure['cols'] as $col) {
            $this->cols[] = new Eddditor_Col($col);
        }
    }


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


    public function jsonSerialize() {
        return array(
            'layout' => $this->layout,
            'options' => $this->options,
            'cols' => $this->cols
        );
    }


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