<?php

namespace Layotter;

use Exception;
use Layotter\Renderers\PostRenderer;
use Layotter\Repositories\PostRepository;

/**
 * Handles the [layotter] shortcode that wraps all post content for posts that use Layotter.
 */
class Shortcode {

    /**
     * @param array $attributes
     * @param string $content Search dump wrapped by [layotter] shortcode
     * @return string
     * @throws Exception
     */
    public static function register($attributes, $content = '') {
        // shortcode attributes carry a post ID, and JSON is stored in a custom field -- get_the_ID() wouldn't be
        // reliable because the shortcode handler can be triggered in a context where $post hasn't been initialized
        // correctly, like do_shortcode() or apply_filters('the_content')
        if (isset($attributes['post'])) {
            $post_id = intval($attributes['post']);
            $post = PostRepository::load($post_id);
        } else {
            // for post previews only -- during editing, JSON is stored in the default textarea, it will be written
            // to the custom field in an action hooked to wp_insert_post_data
            $post = PostRepository::create($content);
        }

        $renderer = new PostRenderer($post);
        $content = $renderer->render_frontend_view();
        return wptexturize($content);
    }

    /**
     * Disables wptexturize for [layotter] shortcode to prevent Wordpress from replaces some characters with html
     * entities, e.g. < would become &lt; which would break post previews.
     *
     * @param array $shortcodes
     * @return array
     */
    public static function disable_wptexturize($shortcodes) {
        $shortcodes[] = 'layotter';
        return $shortcodes;
    }

    /**
     * Disables wpautop for [layotter] shortcode to prevent Wordpress from adding <p> tags that break JSON, thus
     * breaking post previews.
     *
     * @param string $content
     * @return string
     */
    public static function disable_wpautop($content) {
        if (Editor::is_enabled_for_post(get_the_ID())) {
            remove_filter('the_content', 'wpautop');
        }
        return $content;
    }
}
