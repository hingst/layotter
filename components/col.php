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
        $this->width = $structure['width'];
        $this->options = Layotter::assemble_options($structure['options_id']);

        foreach ($structure['elements'] as $element) {
            $element_object = Layotter::assemble_element($element['id'], $element['options_id']);
            $this->elements[] = $element_object;
        }
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
            'options_id' => $this->options->get_id(),
            'elements' => $elements
        );
    }


    /**
     * Return frontend HTML for this column
     *
     * @param array $row_options Formatted options for the parent row
     * @param array $post_options Formatted options for the parent post
     * @return string Frontend HTML
     */
    public function get_frontend_view($row_options, $post_options) {
        $elements_html = '';
        foreach ($this->elements as $element) {
            $elements_html .= $element->get_frontend_view($this->options->get_values(), $row_options, $post_options, $this->width);
        }

        $class = Layotter_Settings::get_col_layout_class($this->width);

        if (has_filter('layotter/view/column')) {
            return apply_filters('layotter/view/column', $elements_html, $class, $this->options->get_values(), $row_options, $post_options);
        } else {
            $html_wrapper = Layotter_Settings::get_html_wrapper('cols');
            $html_before = str_replace('%%CLASS%%', $class, $html_wrapper['before']);
            return $html_before . $elements_html . $html_wrapper['after'];
        }
    }

}