<?php

/**
 * Build a search dump when saving a post, and save JSON to a custom field
 *
 * Since 1.5.0, the JSON is saved to a custom field while the regular post content contains a search dump. Otherwise
 * Wordpress' native search would be pretty useless (going through cryptic JSON). This feature also makes post
 * revisions more usable, because the content is displayed as readable text instead of JSON data.
 *
 * This filter is used to build a search dump when saving a post, as well as saving the post's JSON strucutre to a
 * custom field.
 */
add_filter('wp_insert_post_data', 'layotter_make_search_dump', 999, 2);
function layotter_make_search_dump($data, $raw_post){
    $post_id = $raw_post['ID'];

    // don't change anything if not editing a Layotter-enabled post
    if (!Layotter::is_enabled_for_post($post_id) OR !isset($raw_post['layotter_json'])) {
        return $data;
    }

    // copy JSON from POST and strip slashes that were added by Wordpress
    $json = $raw_post['layotter_json'];
    $unslashed_json = stripslashes_deep($json);

    // turn JSON into post content HTML
    $layotter_post = new Layotter_Post($unslashed_json);
    $content = $layotter_post->get_frontend_view();

    // save JSON to a custom field (oddly enough, Wordpress breaks JSON if it's stripslashed)
    update_post_meta($post_id, 'layotter_json', $json);

    // insert spaces to prevent <p>foo</p><p>bar</p> becoming "foobar" instead of "foo bar"
    // then strip all tags except <img>
    // then remove excess whitespace
    $spaced_content = str_replace('<', ' <', $content);
    $clean_content = strip_tags($spaced_content, '<img>');
    $normalized_content = trim(mb_ereg_replace('/\s+/', ' ', $clean_content));

    // wrap search dump with a [layotter] shortcode and return modified post data to be saved to the database
    // add the post ID because otherwise the shortcode handler would have no reliable way to get the post ID through
    // which the JSON data will be fetched
    $shortcoded_content = '[layotter post="' . $post_id . '"]' . $normalized_content . '[/layotter]';
    $data['post_content'] = $shortcoded_content;
    return $data;
}


/**
 * Track custom field in post revisions
 *
 * Wordpress normally doesn't track custom field data in post revisions. Layotter includes the WP Post Meta Revisions
 * plugin to remedy this. This filter tells the plugin to keep track of the custom field used by Layotter.
 * See https://wordpress.org/plugins/wp-post-meta-revisions/
 */
add_filter('wp_post_revision_meta_keys', 'layotter_track_custom_field');
function layotter_track_custom_field($keys) {
    $keys[] = 'layotter_json';
    return $keys;
}