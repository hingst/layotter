<?php

use Layotter\Components\Post;
use Layotter\Core;
use Layotter\Tests\TestData;

/**
 * @group unit
 */
class PostMigrationTest extends WP_UnitTestCase {

    public function setUp() {
        parent::setUp();

        // wptexturize does weird things to quotes in JSON
        add_filter('run_wptexturize', '__return_false');
    }

    public function tearDown() {
        remove_filter('run_wptexturize', '__return_false');
        parent::tearDown();
    }

    public function test_CanMigrateFromPre150Structure() {
        // wp_insert_post() expects magic quotes, https://core.trac.wordpress.org/ticket/21767
        $input = addslashes(TestData::PRE_150_WRAPPED_JSON);

        $id = self::factory()->post->create([
            'post_content' => $input,
            'post_type' => 'page'
        ]);

        $post = new Post($id);
        $actual = $post->get_frontend_view();
        $model_version = get_post_meta($id, Core::META_FIELD_MODEL_VERSION, true);
        $this->assertEquals(TestData::EXPECTED_VIEW, $actual);
        $this->assertEquals(Core::CURRENT_MODEL_VERSION, $model_version);
    }

    public function test_CanMigrateFromPost150Structure() {
        // wp_insert_post() expects magic quotes, https://core.trac.wordpress.org/ticket/21767
        $input = addslashes(TestData::POST_150_JSON);

        $id = self::factory()->post->create([
            'meta_input' => [
                'layotter_json' => $input
            ],
            'post_type' => 'page'
        ]);

        $post = new Post($id);
        $actual = $post->get_frontend_view();
        $model_version = get_post_meta($id, Core::META_FIELD_MODEL_VERSION, true);
        $this->assertEquals(TestData::EXPECTED_VIEW, $actual);
        $this->assertEquals(Core::CURRENT_MODEL_VERSION, $model_version);
    }

    public function test_CanMigrateFromRegularPost() {
        $id = self::factory()->post->create([
            'post_content' => 'blah blah blah',
            'post_type' => 'page'
        ]);
        $post = new Post($id);
        $model_version = get_post_meta($id, Core::META_FIELD_MODEL_VERSION, true);
        $this->assertRegExp(TestData::EXPECTED_JSON_REGEX, json_encode($post));
        $this->assertEquals(Core::CURRENT_MODEL_VERSION, $model_version);
    }

    public function test_CanMigrateFromEmptyPost() {
        $id = self::factory()->post->create([
            'post_content' => '',
            'post_type' => 'page'
        ]);
        $post = new Post($id);
        $model_version = get_post_meta($id, Core::META_FIELD_MODEL_VERSION, true);
        $this->assertRegExp(TestData::EXPECTED_EMPTY_JSON_REGEX, json_encode($post));
        $this->assertEquals(Core::CURRENT_MODEL_VERSION, $model_version);
    }
}