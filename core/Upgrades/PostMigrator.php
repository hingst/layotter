<?php

namespace Layotter\Upgrades;

use Layotter\Components\Post;
use Layotter\Core;

class PostMigrator {

    private $id;

    public function __construct($id) {
        $this->id = intval($id);
    }

    public function needs_upgrade() {
        $model_version = get_post_meta($this->id, MigrationHelper::META_FIELD_MODEL_VERSION, true);
        if (empty($model_version) OR version_compare($model_version, MigrationHelper::CURRENT_MODEL_VERSION) < 0) {
            return true;
        }
        return false;
    }

    public function migrate() {
        $old_data = $this->get_data();
        $new_data = array(
            'options_id' => 0,
            'rows' => array()
        );

        if (isset($old_data['options'])) {
            $options_template = Core::assemble_new_options('post');
            $new_options = new EditableMigrator('post', $options_template->get_fields(), $old_data['options']);
            $new_data['options_id'] = $new_options->migrate();
        }

        if (isset($old_data['rows'])) {
            foreach ($old_data['rows'] as $row) {
                $new_row = new RowMigrator($row);
                $new_data['rows'][] = $new_row->migrate();
            }
        }

        $json = json_encode($new_data);
        $post = new Post();
        $post->set_json($json);
        $search_dump = '[layotter post="' . $this->id . '"]' . $post->get_search_dump() . '[/layotter]';

        wp_update_post(array(
            'ID' => $this->id,
            'post_content' => $search_dump,
            'meta_input' => array(
                Core::META_FIELD_JSON => $json,
                MigrationHelper::META_FIELD_MODEL_VERSION => MigrationHelper::CURRENT_MODEL_VERSION
            )
        ));
    }

    private function get_data() {
        $json = $this->get_json();
        if ($this->is_json($json)) {
            return json_decode($json, true);
        } else {
            return array();
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
     * @return bool
     */
    private function has_new_data_structure() {
        $json = get_post_meta($this->id, 'layotter_json', true);
        return !empty($json);
    }

    /**
     * Get post JSON by post ID
     *
     * @return string|null JSON string containing post structure or null for new posts
     */
    private function get_json() {
        if ($this->has_new_data_structure()) {
            // if post 1.5.0 data structure is present, get JSON from custom field
            return get_post_meta($this->id, 'layotter_json', true);
        } else {
            // otherwise, try to extract data from the post content
            return $this->get_json_from_legacy_post_content();
        }
    }

    /**
     * Extract post JSON from post content for a post ID
     *
     * JSON used to be stored in the main content wrapped like this: [layotter]json[/layotter]
     * This method extracts JSON from posts that haven't been updated to the new style yet.
     *
     * @return string|null JSON string containing post structure or null for new posts
     */
    private function get_json_from_legacy_post_content() {
        $content_raw = get_post_field('post_content', $this->id);

        // verify that the content is correctly formatted, unwrap from shortcode
        $matches = array();
        if (preg_match('/\[layotter\](.*)\[\/layotter\]/ms', $content_raw, $matches)) {
            $content_json = $matches[1];
            return $content_json;
        } else {
            return null;
        }
    }

}
