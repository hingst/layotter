<?php

namespace Layotter\Components;

use Layotter\Core;
use Layotter\Errors;
use Layotter\Settings;
use Layotter\Structures\ColumnStructure;

/**
 * A single column
 */
class Column implements \JsonSerializable {

    /**
     * @var string Column width, e.g. '1/3'
     */
    private $width = '';

    /**
     * @var Options Column options
     */
    private $options;

    /**
     * @var Element[] Contained elements
     */
    private $elements = [];

    /**
     * Create a new column
     *
     * @param ColumnStructure $data Column structure
     */
    public function __construct($data) {
        if (!($data instanceof ColumnStructure)) {
            Errors::invalid_argument_not_recoverable('data');
        }

        $this->width = $data->get_width();
        $this->options = Core::assemble_options($data->get_options_id());

        foreach ($data->get_elements() as $element) {
            $this->elements[] = Core::assemble_element($element->get_id(), $element->get_options_id());
        }
    }

    /**
     * Return array representation for use in json_encode()
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'options_id' => $this->options->get_id(),
            'elements' => $this->elements
        ];
    }

    /**
     * Return frontend HTML for this column
     *
     * @param array $row_options Option values for the parent row
     * @param array $post_options Option values for the parent post
     * @return string Frontend HTML
     */
    public function get_frontend_view($row_options, $post_options) {
        $elements_html = '';
        foreach ($this->elements as $element) {
            $elements_html .= $element->get_frontend_view($this->options->get_values(), $row_options, $post_options, $this->width);
        }

        $class = Settings::get_col_layout_class($this->width);

        if (has_filter('layotter/view/column')) {
            return apply_filters('layotter/view/column', $elements_html, $class, $this->options->get_values(), $row_options, $post_options);
        } else {
            $html_wrapper = Settings::get_html_wrapper('cols');
            $html_before = str_replace('%%CLASS%%', $class, $html_wrapper['before']);
            return $html_before . $elements_html . $html_wrapper['after'];
        }
    }

}
