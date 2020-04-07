<?php

namespace Layotter\Repositories;

use Exception;
use InvalidArgumentException;
use Layotter\Models\Layout;
use Layotter\Models\Post;
use Layotter\Initializer;
use Layotter\Serialization\PostSerializer;

class LayoutRepository {

    /**
     * @param Post $post
     * @param string $for_post_type
     * @param string $name Human-readable name
     * @return Layout
     * @throws Exception
     */
    public static function save($post, $for_post_type, $name) {
        if (!($post instanceof Post) || !is_string($for_post_type) || !is_string($name)) {
            throw new InvalidArgumentException();
        }

        $id = wp_insert_post([
            'post_type' => Initializer::POST_TYPE_LAYOUT,
            'meta_input' => [
                Initializer::META_FIELD_JSON => addslashes(json_encode(new PostSerializer($post))),
                Initializer::META_FIELD_MODEL_VERSION => Initializer::MODEL_VERSION,
                Initializer::META_FIELD_LAYOUT_FOR_POST_TYPE => $for_post_type
            ],
            'post_status' => 'publish',
            'post_title' => empty($name) ? __('Unnamed layout', 'layotter') : $name
        ]);

        return self::load($id);
    }

    /**
     * @param int $id
     * @return Layout
     * @throws Exception
     */
    public static function load($id) {
        if (!self::exists($id)) {
            throw new InvalidArgumentException();
        }

        $post = PostRepository::load($id);
        return new Layout($id, get_the_title($id), $post, get_the_date('U', $id));
    }

    /**
     * @param int $id
     * @param string $name Human-readable name
     * @return Layout
     * @throws Exception
     */
    public static function rename($id, $name) {
        if (!self::exists($id) || !is_string($name)) {
            throw new InvalidArgumentException();
        }

        wp_update_post([
            'ID' => $id,
            'post_title' => empty($name) ? __('Unnamed layout', 'layotter') : $name
        ]);

        return self::load($id);
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function delete($id) {
        if (!self::exists($id)) {
            throw new InvalidArgumentException();
        }

        return wp_delete_post($id);
    }

    /**
     * @param string $post_type
     * @return Layout[]
     * @throws Exception
     */
    public static function get_allowed_for_post_type($post_type) {
        if (!is_string($post_type)) {
            throw new InvalidArgumentException();
        }

        $layout_posts = get_posts([
            'post_type' => Initializer::POST_TYPE_LAYOUT,
            'order' => 'ASC',
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => Initializer::META_FIELD_LAYOUT_FOR_POST_TYPE,
                    'value' => $post_type
                ],
                [
                    // load layouts without post type for backwards compatibility
                    'key' => Initializer::META_FIELD_LAYOUT_FOR_POST_TYPE,
                    'compare' => 'NOT EXISTS'
                ]
            ]
        ]);

        $layouts = [];

        foreach ($layout_posts as $layout_post) {
            $layouts[] = self::load($layout_post->ID);
        }

        return $layouts;
    }

    /**
     * @param int $id
     * @return bool
     */
    private static function exists($id) {
        return is_int($id) && get_post_type($id) == Initializer::POST_TYPE_LAYOUT;
    }
}