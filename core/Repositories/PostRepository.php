<?php

namespace Layotter\Repositories;

use Exception;
use InvalidArgumentException;
use Layotter\Editor;
use Layotter\Initializer;
use Layotter\Models\Post;
use Layotter\Renderers\PostRenderer;
use Layotter\Serialization\PostDeserializer;
use Layotter\Upgrades\PostMigrator;

class PostRepository {

    /**
     * @param string $json
     * @return Post
     * @throws Exception
     */
    public static function create($json) {
        if (!is_string($json)) {
            throw new InvalidArgumentException();
        }

        $deserializer = new PostDeserializer($json);
        return $deserializer->get_model();
    }

    /**
     * @param int $id
     * @return Post
     * @throws Exception
     */
    public static function load($id) {
        if (!is_int($id)) {
            throw new InvalidArgumentException();
        }

        // migrate on demand
        $migrator = new PostMigrator($id);
        if ($migrator->needs_upgrade()) {
            $migrator->migrate();
        }

        $json = self::get_json_by_id($id);
        $deserializer = new PostDeserializer($json);
        return $deserializer->get_model();
    }

    /**
     * @param int $id
     * @return string
     */
    private static function get_json_by_id($id) {
        if (!is_int($id)) {
            throw new InvalidArgumentException();
        }

        $json = get_post_meta($id, Initializer::META_FIELD_JSON, true);

        if (!is_string($json)) {
            throw new InvalidArgumentException();
        }

        return $json;
    }

    /**
     * Hooked to wp_insert_post_data, writes a search dump to the content and saves JSON to a custom field.
     *
     * @param array $data Post data about to be saved to the database
     * @param array $raw_post Raw POST data from the edit screen
     * @return array Post data with modified post_content
     * @throws Exception
     */
    public static function insert_post_data($data, $raw_post) {
        $post_id = $raw_post['ID'];

        if (!Editor::is_enabled_for_post($post_id) || !isset($raw_post[Initializer::TEXTAREA_NAME])) {
            return $data;
        }

        // Wordpress escapes POST data, we have to remove backslashes explicitly
        $json = $raw_post[Initializer::TEXTAREA_NAME];
        $unescaped_json = stripslashes_deep($json);

        $layotter_post = PostRepository::create($unescaped_json);
        $renderer = new PostRenderer($layotter_post);
        $search_dump = '[layotter post="' . $post_id . '"]' . $renderer->generate_search_dump() . '[/layotter]';

        update_post_meta($post_id, Initializer::META_FIELD_JSON, $json); // $json is still escaped at this point
        update_post_meta($post_id, Initializer::META_FIELD_MODEL_VERSION, Initializer::MODEL_VERSION);

        $data['post_content'] = $search_dump;
        return $data;
    }
}