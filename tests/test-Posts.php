<?php

use \Layotter\Components\Post;

class PostsTest extends WP_UnitTestCase {

	private static $id;
    private static $input = '{"options":[],"rows":[{"layout":"1/3 1/3 1/3","cols":[{"elements":[],"options":[]},{"elements":[{"type":"element_test","values":{"content":"my test content"},"options":[]}],"options":[]},{"elements":[],"options":[]}],"options":[]},{"layout":"1/3 1/3 1/3","cols":[{"elements":[],"options":[]},{"elements":[],"options":[]},{"elements":[],"options":[]}],"options":[]}]}';
    private static $output = '~\{"options_id"\:\d+,"rows"\:\[\{"layout"\:"1\\\\/3 1\\\\/3 1\\\\/3","options_id"\:\d+,"cols"\:\[\{"options_id"\:\d+,"elements"\:\[\]\},\{"options_id"\:\d+,"elements"\:\[\{"id"\:\d+,"options_id"\:\d+,"view"\:"b","is_template"\:false\}\]\},\{"options_id"\:\d+,"elements"\:\[\]\}\]\},\{"layout"\:"1\\\\/3 1\\\\/3 1\\\\/3","options_id"\:\d+,"cols"\:\[\{"options_id"\:\d+,"elements"\:\[\]\},\{"options_id"\:\d+,"elements"\:\[\]\},\{"options_id"\:\d+,"elements"\:\[\]\}\]\}\]\}~';
    private static $frontend_view = '<div class="lo-wrapper"><div class="lo-row"><div class="lo-col-size4of12"></div><div class="lo-col-size4of12"><div class="lo-element">f</div></div><div class="lo-col-size4of12"></div></div><div class="lo-row"><div class="lo-col-size4of12"></div><div class="lo-col-size4of12"></div><div class="lo-col-size4of12"></div></div></div>';

    public function setUp() {
        parent::setUp();

	    // wp_insert_post() expects magic quotes https://core.trac.wordpress.org/ticket/21767
	    $input = addslashes(self::$input);

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
		$this->assertRegExp(self::$output, json_encode($actual));
	}

	function test_FrontendView() {
		$post = new Post(self::$id);
		$actual = $post->get_frontend_view();
		$this->assertEquals(self::$frontend_view, $actual);
	}
}