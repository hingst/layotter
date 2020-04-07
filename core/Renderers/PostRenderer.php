<?php

namespace Layotter\Renderers;

use Exception;
use InvalidArgumentException;
use Layotter\Models\Post;
use Layotter\Services\OptionsFieldsService;
use Layotter\Settings;

class PostRenderer {

    /**
     * @var Post
     */
    private $model;

    /**
     * @param Post $model
     */
    public function __construct($model) {
        if (!($model instanceof Post)) {
            throw new InvalidArgumentException();
        }

        $this->model = $model;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render_frontend_view() {
        $rows_html = '';
        $option_values = OptionsFieldsService::get_values($this->model->get_options());

        foreach ($this->model->get_rows() as $row_structure) {
            $row_renderer = new RowRenderer($row_structure);
            $rows_html .= $row_renderer->render_frontend_view($option_values);
        }

        if (has_filter('layotter/view/post')) {
            return apply_filters('layotter/view/post', $rows_html, $option_values);
        }

        $html_wrapper = Settings::get_html_wrapper('wrapper');
        return $html_wrapper['before'] . $rows_html . $html_wrapper['after'];
    }

    /**
     * Creates a dump that will be saved to the standard post content field to provide basic search functionality.
     *
     * @return string Post content without HTML tags and whitespace
     * @throws Exception
     */
    public function generate_search_dump() {
        $content = $this->render_frontend_view();

        // <p>foo</p><p>bar</p> should become "foo bar" instead of "foobar"
        // keep images for their alt attributes
        $content = str_replace('<', ' <', $content);
        $content = strip_tags($content, '<img>');
        $content = trim($content);

        // TODO: What kind of Fallback could make sense here? http://php.net/manual/de/mbstring.installation.php "mbstring is a non-default extension."
        if (function_exists('mb_ereg_replace')) {
            $content = mb_ereg_replace('/\s+/', ' ', $content);
        }

        return $content;
    }
}
