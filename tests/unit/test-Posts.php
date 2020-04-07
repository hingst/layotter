<?php

use Layotter\Repositories\ElementTypeRepository;
use Layotter\Repositories\PostRepository;
use Layotter\Initializer;
use Layotter\Renderers\PostRenderer;
use Layotter\Serialization\PostSerializer;
use Layotter\Tests\TestData;

/**
 * @group unit
 */
class PostsTest extends \WP_UnitTestCase {

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
        PostRepository::load(self::$id); // triggers automatic migration
        $model_version = get_post_meta(self::$id, Initializer::META_FIELD_MODEL_VERSION, true);
        $this->assertEquals(Initializer::MODEL_VERSION, $model_version);
    }

    public function test_ToArray() {
        $post = PostRepository::load(self::$id);
        $this->assertRegExp(TestData::EXPECTED_JSON_REGEX, json_encode(new PostSerializer($post)));
    }

    public function test_FrontendView() {
        $post = PostRepository::load(self::$id);
        $renderer = new PostRenderer($post);
        $actual = $renderer->render_frontend_view();
        $this->assertEquals(TestData::EXPECTED_VIEW, $actual);
    }

    public function test_AvailableElementTypes() {
        $element_types = ElementTypeRepository::get_allowed_for_post(self::$id);
        $this->assertEquals(2, count($element_types));
        $elements = [
            $element_types[0]->get_name(),
            $element_types[1]->get_name()
        ];
        $this->assertContains('layotter_example_element', $elements);
        $this->assertContains('layotter_functional_test_element', $elements);
    }

    public function test_SearchDump() {
        $post = PostRepository::load(self::$id);
        $renderer = new PostRenderer($post);
        $this->assertEquals(TestData::EXPECTED_SEARCH_DUMP, $renderer->generate_search_dump());
    }

    public function test_SetJson() {
        $post = PostRepository::load(self::$id);
        $new_post = PostRepository::create(json_encode(new PostSerializer($post)));
        $renderer = new PostRenderer($new_post);
        $this->assertEquals(TestData::EXPECTED_VIEW, $renderer->render_frontend_view());
    }
}