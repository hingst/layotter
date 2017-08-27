<?php

class Layotter_Test_Data {

    private static $post_backup = null;

    const POST_150_JSON = '{"options":{"option":"post option value"},"rows":[{"layout":"1/1","options":{"option":"row option value"},"cols":[{"options":{"option":"column option value"},"elements":[{"type":"layotter_example_element","values":{"content":"blah blah blah"},"options":{"option":"element option value"}}]}]}]}';
    const PRE_150_WRAPPED_JSON = '[layotter]' . self::POST_150_JSON . '[/layotter]';
    const POST_150_TEMPLATE_JSON = '{"options":{"option":"post option value"},"rows":[{"layout":"1/1","options":{"option":"row option value"},"cols":[{"options":{"option":"column option value"},"elements":[{"template_id":0,"options":{"option":"element option value"}}]}]}]}';
    const EXPECTED_VIEW = '<div class="layotter-test-post">post option value|<div class="layotter-test-row">row option value|post option value|<div class="layotter-test-column lo-col-size12of12">column option value|row option value|post option value|<div class="layotter-test-element">element option value|column option value|row option value|post option value|<div class="layotter-example-element"><p>blah blah blah</p>
</div></div></div></div></div>';
    const EXPECTED_JSON_REGEX = '~\{"options_id"\:\d+,"rows"\:\[\{"layout"\:"1\\\\/1","options_id"\:\d+,"cols"\:\[\{"options_id"\:\d+,"elements"\:\[\{"id"\:\d+,"options_id"\:\d+,"view"\:"\<div class\=\\\\"layotter\-example\-element\\\\"\>\<p\>blah blah blah\<\\\\/p\>\\\\n\<\\\\/div\>","is_template"\:false\}\]\}\]\}\]~';
    const EXPECTED_EMPTY_JSON_REGEX = '~\{"options_id"\:\d+,"rows"\:\[\]\}~';
    const EXPECTED_SEARCH_DUMP = 'post option value| row option value|post option value| column option value|row option value|post option value| element option value|column option value|row option value|post option value|  blah blah blah'; // TODO: get rid of double space
    const EXPECTED_TEXTAREA_FIRST_LINE = 'Welcome to the text editor! Write something, insert links or images, and click save when you\'re done.';

    /**
     * Manipulates post data so get_post_type() returns 'page'. Normally the global post object is not set
     * in the context of WP unit tests.
     */
    public static function setup_postdata() {
        global $post;
        self::$post_backup = $post;
        $id = wp_insert_post([
            'post_type' => 'page'
        ]);
        $post = get_post($id);
        setup_postdata($post);
    }

    /**
     * Restores post data to the way it was before self::setup_postdata().
     */
    public static function reset_postdata() {
        global $post;
        $post = self::$post_backup;
        setup_postdata($post);
    }
}
