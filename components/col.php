<?php


class Eddditor_Col implements JsonSerializable {

    private
        $width = '',
        $options,
        $elements = array();


    public function __construct($structure) {
        $structure = $this->validate_structure($structure);

        $this->width = $structure['width'];
        $this->options = new Eddditor_Options('col', $structure['options']);

        foreach ($structure['elements'] as $element) {
            $element_object = Eddditor::create_element($element);
            if ($element_object) {
                $this->elements[] = $element_object;
            }
        }
    }


    private function validate_structure($structure) {
        if (!is_array($structure)) {
            $structure = array();
        }

        if (!isset($structure['width']) OR !is_string($structure['width'])) {
            $structure['width'] = '';
        }

        if (!isset($structure['options']) OR !is_array($structure['options'])) {
            $structure['options'] = array();
        }

        if (!isset($structure['elements']) OR !is_array($structure['elements'])) {
            $structure['elements'] = array();
        }

        return $structure;
    }


    public function jsonSerialize() {
        return array(
            'options' => $this->options,
            'elements' => $this->elements
        );
    }


    public function get_frontend_view() {
        $elements_html = '';
        foreach ($this->elements as $element) {
            $elements_html .= $element->get_frontend_view();
        }

        $class = Eddditor_Settings::get_col_layout_class($this->width);

        if (has_filter('eddditor/col')) {
            return apply_filters('eddditor/col', $elements_html, $class, $this->options->get_formatted_values());
        } else {
            $settings = Eddditor_Settings::get_settings('cols');
            $html_before = str_replace('%%CLASS%%', $class, $settings['html_before']);
            return $html_before . $elements_html . $settings['html_after'];
        }
    }

}