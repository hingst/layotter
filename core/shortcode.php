<?php


class Layotter_Shortcode {
    /**
     * Process post content
     *
     * @param array $atts Shortcode attributes
     * @param string $input Data wrapped by the [layotter] shortcode
     * @return string HTML for frontend view of the current post
     */
    public static function register($atts, $input = '') {
        // since 1.5.0, shortcode attributes carry a post ID, and JSON is stored in a custom field
        // before 1.5.0, JSON was stored directly in the post content
        // get_the_ID() wouldn't be reliable here because this shortcode handler might be triggered in a context where the
        // $post variable hasn't been correctly initialized, like do_shortcode() or apply_filters('the_content')
        // TODO: absolutely keep the previous comment in mind when creating the migration script!


        if (isset($atts['post']) AND Layotter::is_enabled_for_post($atts['post'])) {
            $post_id = intval($atts['post']);
            $layotter = new Layotter_Post($post_id);
        } else {
            // if previewing a post
            $layotter = new Layotter_Post();
            $layotter->set_json($input);
        }

        $html = $layotter->get_frontend_view();

        // apply wptexturize manually after post HTML has been parsed because automatic wptexturizing is disabled for
        // Layotter content (see layotter_disable_wptexturize() below)
        return wptexturize($html);
    }


    /**
     * Disable wptexturize for [layotter] shortcode
     *
     * Wordpress replaces some characters with html entities, e.g. < becomes &lt; - this breaks post previews, so we'll
     * disable it for Layotter contents.
     */
    public static function disable_wptexturize($shortcodes) {
        $shortcodes[] = 'layotter';
        return $shortcodes;
    }


    /**
     * Disable wpautop for [layotter] shortcode
     *
     * When previewing changes to a post, Wordpress normally adds <p> tags that break JSON, so we'll disable that.
     */
    public static function disable_wpautop($content) {
        if (Layotter::is_enabled_for_post(get_the_ID())) {
            remove_filter('the_content', 'wpautop');
        }
        return $content;
    }
}

