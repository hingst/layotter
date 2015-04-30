<?php


/**
 * A single column
 */
class Layotter_Col {

    private
        $width = '',
        $options,
        $elements = array();


    /**
     * Create a new columns
     *
     * @param array $structure Column structure
     */
    public function __construct($structure) {
        $structure = $this->validate_structure($structure);

        $this->width = $structure['width'];
        $this->options = new Layotter_Options('col', $structure['options']);

        foreach ($structure['elements'] as $element) {
            $element_object = false;

            // if a template_id is set, try to create a template
            if (isset($element['template_id'])) {
                $element_object = Layotter_Templates::create_element($element);
            }

            // if the template doesn't exist anymore, create a regular element
            if (!$element_object) {
                $element_object = Layotter::create_element($element);
            }

            if ($element_object) {
                $this->elements[] = $element_object;
            }
        }
    }


    /**
     * Validate an array containing a columns's structure
     *
     * Validates array structure and presence of required key/value pairs
     *
     * @param array $structure Column structure
     * @return array Validated column structure
     */
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


    /**
     * Return array representation of this column for use in json_encode()
     *
     * PHP's JsonSerializable interface would be cleaner, but it's only available >= 5.4.0
     *
     * @return array Array representation of this column
     */
    public function to_array() {
        $elements = array();

        foreach ($this->elements as $element) {
            $elements[] = $element->to_array();
        }

        return array(
            'options' => $this->options->to_array(),
            'elements' => $elements
        );
    }


    /**
     * Return frontend HTML for this column
     *
     * @return string Frontend HTML
     */
    public function get_frontend_view() {
        $elements_html = '';
        foreach ($this->elements as $element) {
            $elements_html .= $element->get_frontend_view();
        }

        $class = Layotter_Settings::get_col_layout_class($this->width);

        if (has_filter('layotter/view/column')) {
            return apply_filters('layotter/view/column', $elements_html, $class, $this->options->get_formatted_values());
        } else {
            $settings = Layotter_Settings::get_settings('cols');
            $html_before = str_replace('%%CLASS%%', $class, $settings['html_before']);
            return $html_before . $elements_html . $settings['html_after'];
        }
    }

}