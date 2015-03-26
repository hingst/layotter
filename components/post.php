<?php


/**
 * A single post
 */
class Eddditor_Post {

    private
        $options,
        $rows = array();


    /**
     * Create an object for a post
     *
     * @param int|string $id_or_json_or_post_content Post ID, JSON object holding post structure, or post content
     *      containing [eddditor]JSON structure[/eddditor]
     */
    public function __construct($id_or_json_or_post_content) {
        $structure = $this->get_structure($id_or_json_or_post_content);
        $structure = $this->validate_structure($structure);

        $this->options = new Eddditor_Options('post', $structure['options']);

        foreach ($structure['rows'] as $row) {
            $this->rows[] = new Eddditor_Row($row);
        }
    }


    /**
     * Get post structure by post ID, JSON data or post content containing [eddditor]JSON structure[/eddditor]
     *
     * @param int|string $id_or_json_or_post_content Post ID, JSON or post content
     * @return array|null Array containing post structure or null for new posts
     */
    private function get_structure($id_or_json_or_post_content) {
        if (is_int($id_or_json_or_post_content)) {
            $json = $this->get_json_by_post_id($id_or_json_or_post_content);
        } else if ($this->is_json($id_or_json_or_post_content)) {
            $json = $id_or_json_or_post_content;
        } else {
            $json = $this->get_json_by_post_content($id_or_json_or_post_content);
        }

        return json_decode($json, true);
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
            'options' => $this->options->to_array(),
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
            $rows_html .= $row->get_frontend_view();
        }

        // if a custom filter for frontend was hooked, run through that filter and return HTML
        if (has_filter('eddditor/post')) {
            return apply_filters('eddditor/post', $rows_html, $this->options->get_formatted_values());
        } else {
            // otherwise, get HTML wrapper from settings, apply and return HTML
            $settings = Eddditor_Settings::get_settings('wrapper');
            return $settings['html_before'] . $rows_html . $settings['html_after'];
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
     * Get post JSON by post ID
     *
     * @param int $post_id Post ID
     * @return array|null Array containing post structure or null for new posts
     */
    private function get_json_by_post_id($post_id) {
        // get raw post content (should look like [eddditor]json_data[/eddditor] for existing posts)
        $content_raw = get_post_field('post_content', $post_id);
        return $this->get_json_by_post_content($content_raw);
    }


    /**
     * Extract post JSON from post content
     *
     * @param string $content_raw Post content containing [eddditor]JSON structure[/eddditor]
     * @return array|null Array containing post structure or null for new posts
     */
    private function get_json_by_post_content($content_raw) {
        if (!is_string($content_raw)) {
            return null;
        }

        // verify that the content is correctly formatted, unwrap from shortcode
        $matches = array();
        if (preg_match('/\[eddditor\](.*)\[\/eddditor\]/ms', $content_raw, $matches)) {
            $content_json = $matches[1];
            return $content_json;
        } else {
            return null;
        }
    }

}