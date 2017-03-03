<?php

namespace Layotter\Components;

use Layotter\Core;
use Layotter\Settings;

/**
 * A single row
 */
class Row {

    private $layout = '';
    private $options;
    private $cols = array();

    /**
     * Create a row instance
     *
     * @param array $structure Row structure
     */
    public function __construct($structure) {
        $structure = $this->apply_row_layout_to_cols($structure);

        $this->layout = $structure['layout'];
        $this->options = Core::assemble_options($structure['options_id']);

        foreach ($structure['cols'] as $col) {
            $this->cols[] = new Column($col);
        }
    }

    /**
     * Take a row structure and apply the row layout (e.g. '1/3 1/3 1/3') to the contained columns
     *
     * @param array $structure Row structure with layout and columns
     * @return array Row structure with layout applied to columns
     */
    private function apply_row_layout_to_cols($structure) {
        $layout_array = explode(' ', $structure['layout']);

        foreach ($structure['cols'] as $i => &$col) {
            $col['width'] = isset($layout_array[$i]) ? $layout_array[$i] : '';
        }

        return $structure;
    }

    /**
     * Return array representation of this row
     *
     * @return array
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
