<?php

namespace Layotter\Renderers;

use Exception;
use InvalidArgumentException;
use Layotter\Models\Column;
use Layotter\Services\OptionsFieldsService;
use Layotter\Settings;

class ColumnRenderer {

    /**
     * @var Column
     */
    private $model;

    /**
     * @param Column $model
     */
    public function __construct($model) {
        if (!($model instanceof Column)) {
            throw new InvalidArgumentException();
        }

        $this->model = $model;
    }

    /**
     * @param array $row_options
     * @param array $post_options
     * @return string
     * @throws Exception
     */
    public function render_frontend_view($row_options, $post_options) {
        $elements_html = '';
        $option_values = OptionsFieldsService::get_values($this->model->get_options());
        $css_class = Settings::get_col_layout_class($this->model->get_width());

        foreach ($this->model->get_elements() as $element_structure) {
            $element_renderer = new ElementRenderer($element_structure);
            $elements_html .= $element_renderer->render_frontend_view($option_values, $row_options, $post_options, $css_class);
        }

        if (has_filter('layotter/view/column')) {
            return apply_filters('layotter/view/column', $elements_html, $css_class, $option_values, $row_options, $post_options);
        }

        $html_wrapper = Settings::get_html_wrapper('cols');
        $html_before = str_replace('%%CLASS%%', $css_class, $html_wrapper['before']);
        return $html_before . $elements_html . $html_wrapper['after'];
    }
}
