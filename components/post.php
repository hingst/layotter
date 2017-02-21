<?php


/**
 * A single post
 */
class Layotter_Post {

    private
        $id = 0,
        $options,
        $rows = array();


    /**
     * Create an object for a post
     *
     * @param int $id Post ID
     */
    public function __construct($id = 0) {
        $this->id = intval($id);

        if ($this->id !== 0) {
            $json = get_post_meta($this->id, 'layotter_json', true);
            $this->set_json($json);
        } else {
            $this->options = Layotter::assemble_new_options('post');
        }

        $this->options->set_post_type_context(get_post_type($this->id));
    }


    public function set_json($json) {
        $content = json_decode($json, true);
        if (is_array($content)) {
            foreach ($content['rows'] as $row) {
                $this->rows[] = new Layotter_Row($row);
            }
            $this->options = Layotter::assemble_options($content['options_id']);
        } else {
            throw new Exception('Post has broken JSON structure.');
        }
    }


    /**
     * Return array representation of this post for use in json_encode()
     *
     * PHP's JsonSerializable interface would be cleaner, but it's only available >= 5.4.0
     *
     * @return array Array representation of this post
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
            $html_wrapper = Layotter_Settings::get_html_wrapper('wrapper');
            return $html_wrapper['before'] . $rows_html . $html_wrapper['after'];
        }
    }


    /**
     * Get array representations of blank element instances for all saved templates that are available for a specific post
     *
     * @return array Array representations of element instances for all templates
     */
    public function get_available_templates() {
        $template_posts = get_posts(array(
            'post_type' => Layotter_Editable_Model::post_type,
            'meta_key' => Layotter_Element::IS_TEMPLATE_META_FIELD,
            'meta_value' => '1'
        ));

        $templates = array();

        foreach ($template_posts as $template) {
            $element = Layotter::assemble_element($template->ID);
            if ($element->is_enabled_for($this->id)) {
                $templates[] = $element->to_array();
            }
        }

        return $templates;
    }


    /**
     * Get element types enabled for a specific post
     *
     * @return array Element instances
     */
    public function get_available_element_types() {
        $elements = array();

        foreach (array_keys(Layotter::get_registered_element_types()) as $element_type) {
            $element = Layotter::assemble_new_element($element_type);
            if ($element->is_enabled_for($this->id)) {
                $elements[] = $element;
            }
        }

        usort($elements, array($this, 'sort_element_types_helper'));

        return $elements;
    }


    /**
     * Helper used to sort a set of element types (to be used with usort())
     *
     * Sorts using the order attribute. Elements with the same order attribute are sorted alphabetically
     * by name. Elements without an order attribute are treated as order = 0.
     *
     * @param Layotter_Element $element_type_a First element type for comparison
     * @param Layotter_Element $element_type_b Second element type for comparison
     * @return int -1 if A comes first, 1 if B comes first, 0 if equal
     */
    public static function sort_element_types_helper($element_type_a, $element_type_b) {
        $a_metadata = $element_type_a->get_metadata();
        $b_metadata = $element_type_b->get_metadata();

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

}