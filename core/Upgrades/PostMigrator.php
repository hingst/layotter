<?php

namespace Layotter\Upgrades;

use Exception;
use InvalidArgumentException;
use Layotter\Repositories\PostRepository;
use Layotter\Initializer;
use Layotter\Editor;
use Layotter\Renderers\PostRenderer;

class PostMigrator {

    /**
     * Before 1.5.0, all JSON was stored in the post content, so it needs to be extracted with this regex.
     */
    const PRE_150_POST_CONTENT_REGEX = '/\[layotter\](.*)\[\/layotter\]/ms';

    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     */
    public function __construct($id) {
        if (!is_int($id)) {
            throw new InvalidArgumentException();
        }

        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function needs_upgrade() {
        if (!Editor::is_enabled_for_post($this->id)) {
            return false;
        }

        $model_version = get_post_meta($this->id, Initializer::META_FIELD_MODEL_VERSION, true);
        return (empty($model_version) || version_compare($model_version, Initializer::MODEL_VERSION) < 0);
    }

    /**
     * @throws Exception
     */
    public function migrate() {
        $old_data = $this->get_data();

        $new_data = [
            'options_id' => 0,
            'rows' => []
        ];

        if (isset($old_data['options']) && !empty($old_data['options'])) {
            $new_options = new OptionsMigrator('post', $old_data['options']);
            $new_data['options_id'] = $new_options->migrate();
        }

        if (isset($old_data['rows'])) {
            foreach ($old_data['rows'] as $row) {
                $new_row = new RowMigrator($row);
                $new_data['rows'][] = $new_row->migrate();
            }
        }

        $json = json_encode($new_data);
        $post = PostRepository::create($json);
        $renderer = new PostRenderer($post);
        $search_dump = '[layotter post="' . $this->id . '"]' . $renderer->generate_search_dump() . '[/layotter]';

        wp_update_post([
            'ID' => $this->id,
            'post_content' => addslashes($search_dump),
            'meta_input' => [
                Initializer::META_FIELD_JSON => addslashes($json),
                Initializer::META_FIELD_MODEL_VERSION => Initializer::MODEL_VERSION
            ]
        ]);
    }

    private function get_data() {
        $json = $this->get_json();
        if ($this->is_json($json)) {
            return json_decode($json, true);
        } else {
            return [];
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
     * @return string|null JSON string containing post structure or null for new posts
     */
    private function get_json() {
        if ($this->has_post_150_data_structure()) {
            // if post 1.5.0 data structure is present, get JSON from custom field
            return get_post_meta($this->id, 'layotter_json', true);
        } else if ($this->has_pre_150_data_structure()) {
            // if pre 1.5.0 data structure is present, extract data from the post content
            return $this->get_json_from_pre_150_post_content();
        } else {
            // otherwise, we're dealing with a regular Wordpress post; let's convert the post content to a WYSIWYG element
            $rows = [];
            $content = get_post_field('post_content', $this->id);

            if (!empty($content)) {
                $rows[] = [
                    'layout' => '1/1',
                    'options' => [],
                    'cols' => [
                        [
                            'options' => [],
                            'elements' => [
                                [
                                    'options' => [],
                                    'type' => 'layotter_example_element',
                                    'values' => [
                                        'content' => apply_filters('the_content', $content)
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
            }

            return json_encode([
                'options' => [],
                'rows' => $rows
            ]);
        }
    }

    /**
     * Check if post 1.5.0 data structure is present for this post
     *
     * i.e. if JSON is in a custom field instead of the post content
     *
     * @return bool
     */
    private function has_post_150_data_structure() {
        $json = get_post_meta($this->id, 'layotter_json', true);
        return !empty($json);
    }

    /**
     * Check if pre 1.5.0 data structure is present for this post
     *
     * i.e. if JSON is stored directly in the post content
     *
     * @return bool
     */
    private function has_pre_150_data_structure() {
        $content_raw = get_post_field('post_content', $this->id);
        if (preg_match(self::PRE_150_POST_CONTENT_REGEX, $content_raw)) {
            return true;
        } else {
            return null;
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
    private function get_json_from_pre_150_post_content() {
        $content_raw = get_post_field('post_content', $this->id);

        // verify that the content is correctly formatted, unwrap from shortcode
        $matches = [];
        if (preg_match(self::PRE_150_POST_CONTENT_REGEX, $content_raw, $matches)) {
            $content_json = $matches[1];
            return $content_json;
        } else {
            return null;
        }
    }

}
