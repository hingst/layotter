<?php

namespace Layotter\Components;

use Layotter\Core;
use Layotter\Settings;
use Layotter\Upgrades\MigrationHelper;
use Layotter\Upgrades\PostMigrator;

/**
 * A single post
 */
class Post {

    protected $id = 0;
    protected $options;
    protected $rows = array();
    protected $json;

    /**
     * Create post instance
     *
     * @param int $id Post ID
     */
    public function __construct($id = 0) {
        $this->id = intval($id);
        $this->options = Core::assemble_new_options('post');

        if ($this->id !== 0) {
            // migrate on demand
            $migrator = new PostMigrator($this->id);
            if ($migrator->needs_upgrade()) {
                $migrator->migrate();
            }

            $json = get_post_meta($this->id, Core::META_FIELD_JSON, true);
            $this->set_json($json);
            $this->options->set_post_type_context(get_post_type($this->id));
        }
    }

    /**
     * Set post content as JSON
     *
     * @param string $json
     */
    public function set_json($json) {
        $content = json_decode($json, true);
        if (is_array($content)) {
            foreach ($content['rows'] as $row) {
                $this->rows[] = new Row($row);
            }
            $this->options = Core::assemble_options($content['options_id']);
            $this->json = $json;
        }
    }

    /**
     * Return array representation of this post
     *
     * @return array
     */
    public function to_array() {
        $rows = array();

        foreach ($this->rows as $row) {
            $rows[] = $row->to_array();
        }

        return array(
            'options_id' => $this->options->get_id(),
            'rows' => $rows
        );
    }

    public function to_json() {
        return json_encode($this->to_array());
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
     * Get array representations for all templates that are available for this post
     *
     * @return array
     */
    public function get_available_templates() {
        $template_posts = get_posts(array(
            'post_type' => Editable::POST_TYPE_EDITABLE,
            'meta_key' => Element::META_FIELD_IS_TEMPLATE,
            'meta_value' => '1'
        ));

        $templates = array();

        foreach ($template_posts as $template) {
            $element = Core::assemble_element($template->ID);
            if ($element->is_enabled_for($this->id)) {
                $templates[] = $element->to_array();
            }
        }

        return $templates;
    }

    /**
     * Get metadata of all element types enabled for this post
     *
     * For display in the "Add Element" modal
     *
     * @return array
     */
    public function get_available_element_types_metadata() {
        $elements = array();

        foreach (array_keys(Core::get_registered_element_types()) as $element_type) {
            $element = Core::assemble_new_element($element_type);
            if ($element->is_enabled_for($this->id)) {
                $elements[] = $element->get_metadata();
            }
        }

        usort($elements, array($this, 'sort_element_types_helper'));

        return $elements;
    }

    /**
     * Helper used to sort a set of element types
     *
     * Sorts using the order attribute. Elements with the same order attribute are sorted alphabetically.
     * Elements without an order attribute come last.
     *
     * @param array $a_metadata Element A
     * @param array $b_metadata Element B
     * @return int -1 if A comes first, 1 if B comes first, 0 if equal
     */
    public static function sort_element_types_helper($a_metadata, $b_metadata) {
        $a_order = $a_metadata['order'];
        $b_order = $b_metadata['order'];
        $a_title = $a_metadata['title'];
        $b_title = $b_metadata['title'];

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
     * @return array
     */
    public function get_available_layouts() {
        $layout_posts = get_posts(array(
            'post_type' => Layout::POST_TYPE_LAYOUTS,
            'order' => 'ASC',
            'posts_per_page' => -1
        ));

        $layouts = array();
        foreach ($layout_posts as $layout_post) {
            $layout = new Layout($layout_post->ID);
            $layouts[] = $layout->to_array();
        }

        return $layouts;
    }

    /**
     * Build a search dump when saving a post, and save JSON to a custom field
     *
     * @param array $data Post data about to be saved to the database
     * @param array $raw_post Raw POST data from the edit screen
     * @return array Post data with modified post_content
     */
    public static function make_search_dump($data, $raw_post) {
        $post_id = $raw_post['ID'];

        // don't change anything if not editing a Layotter-enabled post
        if (!Core::is_enabled_for_post($post_id) OR !isset($raw_post[Core::TEXTAREA_NAME])) {
            return $data;
        }

        // copy JSON from POST and strip slashes that were added by Wordpress
        $json = $raw_post[Core::TEXTAREA_NAME];
        $unslashed_json = stripslashes_deep($json);

        // turn JSON into post content HTML
        $layotter_post = new Post();
        $layotter_post->set_json($unslashed_json);
        $content = $layotter_post->get_frontend_view();

        // save JSON to a custom field (oddly enough, Wordpress breaks JSON if it's stripslashed)
        update_post_meta($post_id, Core::META_FIELD_JSON, $json);

        // <p>foo</p><p>bar</p> should become "foo bar" instead of "foobar"
        // keep images for their alt attributes
        $content = str_replace('<', ' <', $content);
        $content = strip_tags($content, '<img>');
        $content = trim($content);

        // TODO: What kind of Fallback could make sense here? http://php.net/manual/de/mbstring.installation.php "mbstring is a non-default extension."
        if (function_exists('mb_ereg_replace')) {
            $content = mb_ereg_replace('/\s+/', ' ', $content);
        }

        // wrap search dump with a [layotter] shortcode and return modified post data to be saved to the database
        // add a shortcude attribute give the shortcode handler a reliable way to get the post ID
        $content = '[layotter post="' . $post_id . '"]' . $content . '[/layotter]';
        $data['post_content'] = $content;
        return $data;
    }

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
