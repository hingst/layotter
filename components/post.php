<?php


class Eddditor_Post implements JsonSerializable {

    private
        $options,
        $rows = array();


    public function __construct($id_or_json_or_post_content) {
        $structure = $this->get_structure($id_or_json_or_post_content);
        $structure = $this->validate_structure($structure);

        $this->options = new Eddditor_Options('post', $structure['options']);

        foreach ($structure['rows'] as $row) {
            $this->rows[] = new Eddditor_Row($row);
        }
    }


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


    public function jsonSerialize() {
        return array(
            'options' => $this->options,
            'rows' => $this->rows
        );
    }


    public function get_frontend_view() {
        $rows_html = '';
        foreach ($this->rows as $row) {
            $rows_html .= $row->get_frontend_view();
        }

        if (has_filter('eddditor/post')) {
            return apply_filters('eddditor/post', $rows_html, $this->options->get_formatted_values());
        } else {
            $settings = Eddditor_Settings::get_settings('wrapper');
            return $settings['html_before'] . $rows_html . $settings['html_after'];
        }
    }


    private function is_json($maybe_json) {
        $maybe_array = json_decode($maybe_json, true);
        return is_array($maybe_array);
    }


    private function get_json_by_post_id($post_id) {
        // get raw post content (should look like [eddditor]json_data[/eddditor] for existing posts)
        $content_raw = get_post_field('post_content', $post_id);
        return $this->get_json_by_post_content($content_raw);
    }


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