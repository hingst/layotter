<?php

namespace Layotter\Components;

use Layotter\Core;
use Layotter\Errors;
use Layotter\Settings;
use Layotter\Structures\RowStructure;

/**
 * A single row
 */
class Row implements \JsonSerializable {

    /**
     * @var string Contained column layout, e.g. '1/3 1/3 1/3'
     */
    private $layout = '';

    /**
     * @var Options Row options
     */
    private $options;

    /**
     * @var Column[] Contained columns
     */
    private $cols = [];

    /**
     * Create a row instance
     *
     * @param RowStructure $data Row structure
     */
    public function __construct($data) {
        if (!($data instanceof RowStructure)) {
            Errors::invalid_argument_not_recoverable('data');
        }

        $this->layout = $data->get_layout();
        $this->options = Core::assemble_options($data->get_options_id());

        foreach ($data->get_columns() as $col) {
            $this->cols[] = new Column($col);
        }
    }

    /**
     * Return array representation for use in json_encode()
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'layout' => $this->layout,
            'options_id' => $this->options->get_id(),
            'cols' => $this->cols
        ];
    }

    /**
     * Return frontend HTML for this row
     *
     * @param array $post_options Option values for the parent post
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
            $html_wrapper = Settings::get_html_wrapper('rows');
            return $html_wrapper['before'] . $cols_html . $html_wrapper['after'];
        }
    }

}
