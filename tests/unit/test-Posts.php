<?php

use Layotter\Components\Post;
use Layotter\Core;
use Layotter\Tests\TestData;

/**
 * @group unit
 */
class PostsTest extends WP_UnitTestCase {

    private static $id;

    public function setUp() {
        parent::setUp();

        // wp_insert_post() expects magic quotes, https://core.trac.wordpress.org/ticket/21767
        $input = addslashes(TestData::POST_150_JSON);

        self::$id = self::factory()->post->create([
            'meta_input' => [
                'layotter_json' => $input
            ],
            'post_type' => 'page'
        ]);

        // wptexturize fucks with quotes in ways that we really don't care about in these tests
        add_filter('run_wptexturize', '__return_false');
    }

    public function tearDown() {
        remove_filter('run_wptexturize', '__return_false');
        parent::tearDown();
    }

    public function test_ModelVersion() {
        new Post(self::$id);
        $model_version = get_post_meta(self::$id, Core::META_FIELD_MODEL_VERSION, true);
        $this->assertEquals(Core::CURRENT_MODEL_VERSION, $model_version);
    }

    public function test_ToArray() {
        $post = new Post(self::$id);
        $this->assertRegExp(TestData::EXPECTED_JSON_REGEX, json_encode($post));
    }

    public function test_FrontendView() {
        $post = new Post(self::$id);
        $actual = $post->get_frontend_view();
        $this->assertEquals(TestData::EXPECTED_VIEW, $actual);
    }

    public function test_AvailableElementTypes() {
        $post = new Post(self::$id);
        $element_types = $post->get_available_element_types_meta();
        $this->assertEquals(2, count($element_types));
        $elements = [
            $element_types[0]->get_type(),
            $element_types[1]->get_type()
        ];
        $this->assertContains('layotter_example_element', $elements);
        $this->assertContains('layotter_functional_test_element', $elements);
    }

    public function test_SearchDump() {
        $post = new Post(self::$id);
        $this->assertEquals(TestData::EXPECTED_SEARCH_DUMP, $post->get_search_dump());
    }

    public function test_SetJson() {
        $post = new Post(self::$id);
        $new_post = new Post();
        $new_post->set_json(json_encode($post));
        $this->assertEquals(TestData::EXPECTED_VIEW, $new_post->get_frontend_view());
    }
}