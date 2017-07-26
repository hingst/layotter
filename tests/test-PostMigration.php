<?php

use \Layotter\Components\Post;

class PostMigrationTest extends WP_UnitTestCase {

    private static $input = '{"options":[],"rows":[{"layout":"1/3 1/3 1/3","cols":[{"elements":[],"options":[]},{"elements":[{"type":"element_test","values":{"content":"Here\'s some text. With a <a href=\"http://google.com#hey\">link</a>. With some <strong>HTML</strong> <em>tags</em>. With a bunch of mean Unicode characters: áî　ラメ単色\"\'\\\\ß😆⚖️👩."},"options":[]}],"options":[]},{"elements":[],"options":[]}],"options":[]},{"layout":"1/3 1/3 1/3","cols":[{"elements":[],"options":[]},{"elements":[],"options":[]},{"elements":[],"options":[]}],"options":[]}]}';
    //private static $input = '{"content":"Here\'s some text. With a <a href=\"http://google.com#hey\">link</a>"}';
    private static $expected = '<div class="lo-wrapper"><div class="lo-row"><div class="lo-col-size4of12"></div><div class="lo-col-size4of12"><div class="lo-element"><div class="layotter-example-element"><p>Here\'s some text. With a <a href="http://google.com#hey">link</a>. With some <strong>HTML</strong> <em>tags</em>. With a bunch of mean Unicode characters: áî　ラメ単色"\'\\ß😆⚖️👩.</p>
</div></div></div><div class="lo-col-size4of12"></div></div><div class="lo-row"><div class="lo-col-size4of12"></div><div class="lo-col-size4of12"></div><div class="lo-col-size4of12"></div></div></div>';

    public function setUp() {
        parent::setUp();

        // wptexturize fucks with quotes in ways that we really don't care about in these tests
        add_filter('run_wptexturize', '__return_false');
    }

    public function tearDown() {
        remove_filter('run_wptexturize', '__return_false');
        parent::tearDown();
    }

    function test_CanMigrateFromPre150Structure() {
        //$input = '[layotter]' . self::$input . '[/layotter]';

        // wp_insert_post() expects magic quotes, fucking lunatics https://core.trac.wordpress.org/ticket/21767
        $input = addslashes(self::$input);

        $id = self::factory()->post->create(array(
            'post_content' => $input,
            'post_type' => 'page'
        ));
        //var_dump($input);
        //var_dump(get_post_field('post_content', $id));

        $post = new Post($id);
        $actual = $post->get_frontend_view();
        $this->assertEquals(self::$expected, $actual);
    }

    function test_CanMigrateFromPost150Structure() {
        // wp_insert_post() expects magic quotes, fucking lunatics https://core.trac.wordpress.org/ticket/21767
        $input = addslashes(self::$input);

        $id = self::factory()->post->create(array(
            'meta_input' => array(
                'layotter_json' => $input
            ),
            'post_type' => 'page'
        ));
        //var_dump($input);
        //var_dump(get_post_meta($id, 'layotter_json', true));

        $post = new Post($id);
        $actual = $post->get_frontend_view();
        $this->assertEquals(self::$expected, $actual);
    }
/*
    function test_CanMigrateFromRegularPost() {
        $id = self::factory()->post->create(array(
            'post_content' => 'arghl blarghl',
            'post_type' => 'page'
        ));
        $post = new Post($id);
        $data = $post->to_json();
        $this->assertEquals('', $data); // TODO
    }

    function test_CanMigrateFromEmptyPost() {
        $id = self::factory()->post->create(array(
            'post_type' => 'page'
        ));
        $post = new Post($id);
        $data = $post->to_json();
        $this->assertEquals('', $data); // TODO
    }*/
}