<?php

use \Layotter\Components\Post;

class PostsTest extends WP_UnitTestCase {

    private static $id;

    public function setUp() {
        parent::setUp();

        // wp_insert_post() expects magic quotes, https://core.trac.wordpress.org/ticket/21767
        $input = addslashes(Layotter_Test_Data::POST_150_JSON);

        self::$id = self::factory()->post->create(array(
            'meta_input' => array(
                'layotter_json' => $input
            ),
            'post_type' => 'page'
        ));

        // wptexturize fucks with quotes in ways that we really don't care about in these tests
        add_filter('run_wptexturize', '__return_false');
    }

    public function tearDown() {
        remove_filter('run_wptexturize', '__return_false');
        parent::tearDown();
    }

    function test_ToArray() {
        $post = new Post(self::$id);
        $actual = $post->to_array();
        $this->assertRegExp(Layotter_Test_Data::EXPECTED_JSON_REGEX, json_encode($actual));
    }

    function test_FrontendView() {
        $post = new Post(self::$id);
        $actual = $post->get_frontend_view();
        $this->assertEquals(Layotter_Test_Data::EXPECTED_VIEW, $actual);
    }
}