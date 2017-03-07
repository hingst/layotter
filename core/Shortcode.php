<?php

namespace Layotter;

use Layotter\Components\Post;

/**
 * Handles the shortcode for frontend view
 */
class Shortcode {

    /**
     * Process post content
     *
     * @param array $atts Shortcode attributes
     * @param string $input Search dump wrapped by [layotter] shortcode
     * @return string HTML for frontend view of the current post
     */
    public static function register($atts, $input = '') {
        // since 1.5.0, shortcode attributes carry a post ID, and JSON is stored in a custom field
        // before 1.5.0, JSON was stored directly in the shortcoded post content without a post ID attribute
        // get_the_ID() wouldn't be reliable here because this shortcode handler can be triggered in a context where
        // $post hasn't been correctly initialized, like do_shortcode() or apply_filters('the_content')

        if (isset($atts['post'])) {
            $post_id = intval($atts['post']);
            $layotter = new Post($post_id);
        } else {
            // for post previews only
            $layotter = new Post();
            $layotter->set_json($input);
        }

        $input = $layotter->get_frontend_view();
        return wptexturize($input);
    }

    /**
     * Disable wptexturize for [layotter] shortcode
     *
     * Wordpress replaces some characters with html entities, e.g. < becomes &lt; - this breaks post previews, so we'll
     * disable it for Layotter contents.
     *
     * @param array $shortcodes wptexturize-disabled shortcodes
     * @return array More wptexturize-disabled shortcodes
     */
    public static function disable_wptexturize($shortcodes) {
        $shortcodes[] = 'layotter';
        return $shortcodes;
    }

    /**
     * Disable wpautop for [layotter] shortcode
     *
     * When previewing changes to a post, Wordpress normally adds <p> tags that break JSON, so we'll disable that.
     *
     * @param string $content Post content
     * @return string Post content
     */
    public static function disable_wpautop($content) {
        if (Core::is_enabled_for_post(get_the_ID())) {
            remove_filter('the_content', 'wpautop');
        }
        return $content;
    }
}
