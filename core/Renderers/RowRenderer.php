<?php

namespace Layotter\Renderers;

use Exception;
use InvalidArgumentException;
use Layotter\Models\Row;
use Layotter\Services\OptionsFieldsService;
use Layotter\Settings;

class RowRenderer {

    /**
     * @var Row
     */
    private $model;

    /**
     * @param Row $model
     */
    public function __construct($model) {
        if (!($model instanceof Row)) {
            throw new InvalidArgumentException();
        }

        $this->model = $model;
    }

    /**
     * @param array $post_options
     * @return string
     * @throws Exception
     */
    public function render_frontend_view($post_options) {
        $columns_html = '';
        $option_values = OptionsFieldsService::get_values($this->model->get_options());

        foreach ($this->model->get_columns() as $column_structure) {
            $column_renderer = new ColumnRenderer($column_structure);
            $columns_html .= $column_renderer->render_frontend_view($option_values, $post_options);
        }

        if (has_filter('layotter/view/row')) {
            return apply_filters('layotter/view/row', $columns_html, $option_values, $post_options);
        }

        $html_wrapper = Settings::get_html_wrapper('rows');
        return $html_wrapper['before'] . $columns_html . $html_wrapper['after'];
    }
}
