<?php

namespace Layotter\Renderers;

use Exception;
use InvalidArgumentException;
use Layotter\Models\Element;
use Layotter\Services\ElementFieldsService;
use Layotter\Services\OptionsFieldsService;
use Layotter\Settings;

class ElementRenderer {

    /**
     * @var Element
     */
    private $model;

    /**
     * @param Element $model
     */
    public function __construct($model) {
        if (!($model instanceof Element)) {
            throw new InvalidArgumentException();
        }

        $this->model = $model;
    }

    /**
     * @param array $column_options
     * @param array $row_options
     * @param array $post_options
     * @param string $column_width Width of the parent column, e.g. '1/3'
     * @return string
     * @throws Exception
     */
    public function render_frontend_view($column_options, $row_options, $post_options, $column_width) {
        $option_values = OptionsFieldsService::get_values($this->model->get_options());

        // provide more parameters than the function requires for backwards compatibility
        $element_html = $this->model->get_type()->render_frontend_view(ElementFieldsService::get_values($this->model), $column_width, $column_options, $row_options, $post_options, $option_values);

        if (has_filter('layotter/view/element')) {
            return apply_filters('layotter/view/element', $element_html, $option_values, $column_options, $row_options, $post_options);
        }

        $html_wrapper = Settings::get_html_wrapper('elements');
        return $html_wrapper['before'] . $element_html . $html_wrapper['after'];
    }
}
