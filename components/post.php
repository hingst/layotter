<?php


/**
 * A single post
 */
class Layotter_Post {

    private
        $options,
        $rows = array();


    /**
     * Create an object for a post
     *
     * @param int|string $id_or_json Post ID or JSON object holding post structure
     */
    public function __construct($id_or_json) {
        $structure = $this->get_structure($id_or_json);
        $structure = $this->validate_structure($structure);

        $this->options = Layotter::assemble_options($structure['options_id']);

        foreach ($structure['rows'] as $row) {
            $this->rows[] = new Layotter_Row($row);
        }
    }


    /**
     * Create a post structure array using a post ID or JSON data
     *
     * @param int|string $id_or_json Post ID, JSON or post content
     * @return string JSON string containing post structure or null for new posts
     */
    private function get_structure($id_or_json) {
        if (is_int($id_or_json)) {
            $json = $this->get_json_by_post_id($id_or_json);
        } else {
            $json = $id_or_json;
        }

        if ($this->is_json($json)) {
            return json_decode($json, true);
        } else {
            return json_decode(null, true); // TODO: what am I doing here?
        }
    }


    /**
     * Validate an array containing the post structure
     *
     * Validates array structure and presence of required key/value pairs
     *
     * @param array $structure Post structure
     * @return array Validated post structure
     */
    private function validate_structure($structure) {
        if (!is_array($structure)) {
            $structure = array();
        }

        if (!isset($structure['options']) OR !is_array($structure['options'])) {
            $structure['options'] = array();
        }

        if (!isset($structure['rows']) OR !is_array($structure['rows'])) {
            $structure['rows'] = array();
        }

        return $structure;
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
            // $rows_html .= $row->get_frontend_view($this->options->get_formatted_values());
            // TODO: re-implement line above
        }

        // if a custom filter for frontend was hooked, run through that filter and return HTML
        if (has_filter('layotter/view/post')) {
            return apply_filters('layotter/view/post', $rows_html, $this->options->get_formatted_values());
        } else {
            // otherwise, get HTML wrapper from settings, apply and return HTML
            $html_wrapper = Layotter_Settings::get_html_wrapper('wrapper');
            return $html_wrapper['before'] . $rows_html . $html_wrapper['after'];
        }
    }


    /**
     * Check if a string contains the JSON representation of an array
     *
     * @param mixed $maybe_json Something that might be a string containing JSON data
     * @return bool Whether the parameter contained a JSON array
     */
    private function is_json($maybe_json) {
        $maybe_array = json_decode($maybe_json, true);
        return is_array($maybe_array);
    }


    /**
     * Check if post 1.5.0 data structure is present for this post
     *
     * i.e. if JSON is in a custom field instead of the post content
     *
     * @param int $post_id Post ID
     * @return bool
     */
    private function has_new_data_structure($post_id) {
        $json = get_post_meta($post_id, 'layotter_json', true);

        if (!empty($json)) {
            return true;
        }

        return false;
    }


    /**
     * Get post JSON by post ID
     *
     * @param int $post_id Post ID
     * @return string|null JSON string containing post structure or null for new posts
     */
    private function get_json_by_post_id($post_id) {
        if ($this->has_new_data_structure($post_id) !== false) {
            // if post 1.5.0 data structure is present, get JSON from custom field
            return get_post_meta($post_id, 'layotter_json', true);
        } else {
            // otherwise, try to extract data from the post content
            return $this->get_json_from_legacy_post_content($post_id);
        }
    }


    /**
     * Extract post JSON from post content for a post ID
     *
     * JSON used to be stored in the main content wrapped like this: [layotter]json[/layotter]
     * This method extracts JSON from posts that haven't been updated to the new style yet.
     *
     * @param int $post_id Post ID with pre 1.5.0 style post content
     * @return string|null JSON string containing post structure or null for new posts
     */
    private function get_json_from_legacy_post_content($post_id) {
        $content_raw = get_post_field('post_content', $post_id);

        // verify that the content is correctly formatted, unwrap from shortcode
        $matches = array();
        if (preg_match('/\[layotter\](.*)\[\/layotter\]/ms', $content_raw, $matches)) {
            $content_json = $matches[1];
            return $content_json;
        } else {
            return null;
        }
    }


    /**
     * Get array representations of blank element instances for all saved templates that are available for a specific post
     *
     * @param int $post_id Post ID
     * @return array Array representations of element instances for all templates
     */
    public function get_available_templates($post_id) {
        $template_posts = get_posts(array(
            'post_type' => Layotter_Editable_Model::post_type,
            'meta_key' => Layotter_Element::IS_TEMPLATE_META_FIELD,
            'meta_value' => '1'
        ));

        $templates = array();

        foreach ($template_posts as $template) {
            $element = Layotter::assemble_element($template->ID);
            if ($element->is_enabled_for($post_id)) { // TODO: use this post object's ID
                $templates[] = $element->to_array();
            }
        }

        return $templates;
    }


    /**
     * Get element types enabled for a specific post
     *
     * @param int $post_id Post ID
     * @return array Element instances
     */
    public function get_available_element_types($post_id) {
        $elements = array();

        foreach (array_keys(Layotter::get_registered_element_types()) as $element_type) {
            $element = Layotter::assemble_new_element($element_type);
            if ($element->is_enabled_for($post_id)) { // TODO: use this post object's ID
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