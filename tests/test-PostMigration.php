<?php

use \Layotter\Components\Post;

class PostMigrationTest extends WP_UnitTestCase {

    private static $input = '{"options":[],"rows":[{"layout":"1/3 1/3 1/3","cols":[{"elements":[],"options":[]},{"elements":[{"type":"layotter_example_element","values":{"content":"Here\'s some text. With a <a href=\"http://google.com#hey\">link</a>. With some <strong>HTML</strong> <em>tags</em>. With a bunch of mean Unicode characters: áî　ラメ単色\"\'\\ß😆⚖️👩."},"options":[]}],"options":[]},{"elements":[],"options":[]}],"options":[]},{"layout":"1/3 1/3 1/3","cols":[{"elements":[],"options":[]},{"elements":[],"options":[]},{"elements":[],"options":[]}],"options":[]}]}'; // TODO
    private static $expected = '<div class="lo-wrapper"><div class="lo-row"><div class="lo-col-size4of12"></div><div class="lo-col-size4of12"><div class="lo-element"><div class="layotter-example-element"><p>Here&#8217;s some text. With a link. With some HTML tags. With a bunch of mean Unicode characters: áî　ラメ単色&#8221;&#8216;\ß😆⚖️👩.</p>
</div></div></div><div class="lo-col-size4of12"></div></div><div class="lo-row"><div class="lo-col-size4of12"></div><div class="lo-col-size4of12"></div><div class="lo-col-size4of12"></div></div></div>	</div>'; // TODO

    function test_CanMigrateFromPre150Structure() {
        $id = self::factory()->post->create(array(
            'post_content' => '[layotter]' . self::$input . '[/layotter]',
            'post_type' => 'page'
        ));
        $post = new Post($id);
        $data = $post->get_frontend_view();
        var_dump($data);
        $this->assertEquals(self::$expected, $data);
    }
/*
    function test_CanMigrateFromPost150Structure() {
        $id = self::factory()->post->create(array(
            'meta_input' => array(
                'layotter_json' => self::$input,
                'post_type' => 'page'
            )
        ));
        $post = new Post($id);
        $data = $post->to_json();
        $this->assertEquals(self::$expected, $data);
    }

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