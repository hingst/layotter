<?php


/**
 * Build a search dump when saving a post
 *
 * Since 1.5.0, the JSON is saved to a custom field while the regular post content contains a search dump, because
 * Wordpress by default only searches the post content. This filter is used to build a search dump when saving a post.
 *
 * It also makes post revisions more usable, because the content is displayed as readable text instead of JSON data.
 */
add_filter('wp_insert_post_data', 'layotter_make_search_dump', 999, 2);
function layotter_make_search_dump($data, $raw_post){
    $post_id = $raw_post['ID'];

    // don't change anything if not editing a Layotter-enabled post
    if (!Layotter::is_enabled_for_post($post_id) OR !isset($raw_post['acf']['field_layotter_post_content'])) {
        return $data;
    }

    // copy JSON from POST and strip slashes that were added by Wordpress
    $json = $raw_post['acf']['field_layotter_post_content'];
    $json = stripslashes_deep($json);

    // turn JSON into post content HTML
    $layotter_post = new Layotter_Post($json);
    $content = $layotter_post->get_frontend_view();

    // insert spaces to prevent <p>foo</p><p>bar</p> becoming "foobar" instead of "foo bar"
    // then strip all tags except <img>
    // then remove excess whitespace
    $spaced_content = str_replace('<', ' <', $content);
    $clean_content = strip_tags($spaced_content, '<img>');
    $normalized_content = trim(preg_replace('/\s+/', ' ', $clean_content));

    // wrap search dump with a [layotter] shortcode and save to database
    $shortcoded_content = '[layotter]' . $normalized_content . '[/layotter]';
    $data['post_content'] = $shortcoded_content;
    return $data;
}
