<?php

namespace Layotter\Components;

use Layotter\Core;
use Layotter\Errors;
use Layotter\Settings;
use Layotter\Structures\ElementTypeMeta;
use Layotter\Structures\PostStructure;
use Layotter\Upgrades\PostMigrator;

/**
 * A single post
 */
class Post implements \JsonSerializable {

    /**
     * @var int Post ID
     */
    protected $id = 0;

    /**
     * @var Options Post options
     */
    protected $options;

    /**
     * @var Row[] Contained Rows
     */
    protected $rows = [];

    /**
     * Create post instance
     *
     * @param int $id Post ID
     */
    public function __construct($id = 0) {
        if (!is_int($id)) {
            Errors::invalid_argument_not_recoverable('id');
        }

        $this->id = $id;
        $this->options = Core::assemble_new_options('post');

        if ($this->id !== 0) {
            // migrate on demand
            $migrator = new PostMigrator($this->id);
            if ($migrator->needs_upgrade()) {
                $migrator->migrate();
            }

            $json = get_post_meta($this->id, Core::META_FIELD_JSON, true);
            $this->set_json($json);
        }
    }

    /**
     * Set post content as JSON
     *
     * @param string $json
     */
    public function set_json($json) {
        if (!is_string($json)) {
            Errors::invalid_argument_not_recoverable('json');
        }

        $structure = json_decode($json, true);

        if (!is_array($structure)) {
            Errors::invalid_argument_not_recoverable('json');
        }

        $data = new PostStructure($structure);
        $this->options = Core::assemble_options($data->get_options_id());
        $this->rows = [];

        foreach ($data->get_rows() as $row) {
            $this->rows[] = new Row($row);
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
            'rows' => $this->rows
        ];
    }

    /**
     * Return frontend HTML for this post
     *
     * @return string Frontend HTML
     */
    public function get_frontend_view() {
        $rows_html = '';
        foreach ($this->rows as $row) {
            $rows_html .= $row->get_frontend_view($this->options->get_values());
        }

        // if a custom filter for frontend was hooked, run through that filter and return HTML
        if (has_filter('layotter/view/post')) {
            return apply_filters('layotter/view/post', $rows_html, $this->options->get_values());
        } else {
            // otherwise, get HTML wrapper from settings, apply and return HTML
            $html_wrapper = Settings::get_html_wrapper('wrapper');
            return $html_wrapper['before'] . $rows_html . $html_wrapper['after'];
        }
    }

    /**
     * Get instances of all templates that are available for this post
     *
     * @return Element[]
     */
    public function get_available_templates() {
        $template_posts = get_posts([
            'post_type' => Core::POST_TYPE_EDITABLE,
            'meta_key' => Core::META_FIELD_IS_TEMPLATE,
            'meta_value' => '1',
            'order' => 'ASC',
            'posts_per_page' => -1
        ]);

        $templates = [];

        foreach ($template_posts as $template) {
            $element = Core::assemble_element($template->ID);
            if ($element->is_enabled_for($this->id)) {
                $templates[] = $element;
            }
        }

        return $templates;
    }

    /**
     * Get meta data of all element types enabled for this post
     *
     * For display in the "Add Element" modal.
     *
     * @return ElementTypeMeta[]
     */
    public function get_available_element_types_meta() {
        $elements = [];

        foreach (Core::get_registered_element_types() as $element_type) {
            $element = Core::assemble_new_element($element_type);
            if ($element->is_enabled_for($this->id)) {
                $elements[] = $element->get_type_meta();
            }
        }

        usort($elements, [$this, 'sort_element_types_helper']);

        return $elements;
    }

    /**
     * Helper used to sort a set of element types
     *
     * Sorts using the order attribute. Elements with the same order attribute are sorted alphabetically.
     * Elements without an order attribute come last.
     *
     * @param ElementTypeMeta $a_meta Element A meta data
     * @param ElementTypeMeta $b_meta Element B meta data
     * @return int -1 if A comes first, 1 if B comes first, 0 if equal
     */
    public static function sort_element_types_helper($a_meta, $b_meta) {
        $a_order = $a_meta->get_order();
        $b_order = $b_meta->get_order();
        $a_title = $a_meta->get_title();
        $b_title = $b_meta->get_title();

        if ($a_order < $b_order) {
            return -1;
        } else if ($a_order > $b_order) {
            return 1;
        } else {
            return strcasecmp($a_title, $b_title);
        }
    }

    /**
     * Get array representations of all available layouts
     *
     * @return Layout[] All available layouts
     */
    public function get_available_layouts() {
        $layout_posts = get_posts([
            'post_type' => Core::POST_TYPE_LAYOUT,
            'order' => 'ASC',
            'posts_per_page' => -1
        ]);

        $layouts = [];
        foreach ($layout_posts as $layout_post) {
            $layout = new Layout($layout_post->ID);
            $layouts[] = $layout;
        }

        return $layouts;
    }

    /**
     * Create a search dump that will be saved to the post content
     *
     * @return string Post content without HTML tags and whitespace
     */
    public function get_search_dump() {
        $content = $this->get_frontend_view();

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
