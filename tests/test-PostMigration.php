<?php

use \Layotter\Components\Post;

class PostMigrationTest extends WP_UnitTestCase {

    public function setUp() {
        parent::setUp();
        Layotter_Test_Data::setup_postdata();

        // wptexturize does weird things to quotes in JSON
        add_filter('run_wptexturize', '__return_false');
    }

    public function tearDown() {
        remove_filter('run_wptexturize', '__return_false');
        Layotter_Test_Data::reset_postdata();
        parent::tearDown();
    }

    function test_CanMigrateFromPre150Structure() {
        // wp_insert_post() expects magic quotes, https://core.trac.wordpress.org/ticket/21767
        $input = addslashes(Layotter_Test_Data::PRE_150_WRAPPED_JSON);

        $id = self::factory()->post->create([
            'post_content' => $input,
            'post_type' => 'page'
        ]);

        $post = new Post($id);
        $actual = $post->get_frontend_view();
        $model_version = get_post_meta($id, \Layotter\Upgrades\PluginMigrator::META_FIELD_MODEL_VERSION, true);
        $this->assertEquals(Layotter_Test_Data::EXPECTED_VIEW, $actual);
        $this->assertEquals(\Layotter\Upgrades\PluginMigrator::CURRENT_MODEL_VERSION, $model_version);
    }

    function test_CanMigrateFromPost150Structure() {
        // wp_insert_post() expects magic quotes, https://core.trac.wordpress.org/ticket/21767
        $input = addslashes(Layotter_Test_Data::POST_150_JSON);

        $id = self::factory()->post->create([
            'meta_input' => [
                'layotter_json' => $input
            ],
            'post_type' => 'page'
        ]);

        $post = new Post($id);
        $actual = $post->get_frontend_view();
        $model_version = get_post_meta($id, \Layotter\Upgrades\PluginMigrator::META_FIELD_MODEL_VERSION, true);
        $this->assertEquals(Layotter_Test_Data::EXPECTED_VIEW, $actual);
        $this->assertEquals(\Layotter\Upgrades\PluginMigrator::CURRENT_MODEL_VERSION, $model_version);
    }

    function test_CanMigrateFromRegularPost() {
        $id = self::factory()->post->create([
            'post_content' => 'blah blah blah',
            'post_type' => 'page'
        ]);
        $post = new Post($id);
        $model_version = get_post_meta($id, \Layotter\Upgrades\PluginMigrator::META_FIELD_MODEL_VERSION, true);
        $this->assertRegExp(Layotter_Test_Data::EXPECTED_JSON_REGEX, json_encode($post));
        $this->assertEquals(\Layotter\Upgrades\PluginMigrator::CURRENT_MODEL_VERSION, $model_version);
    }

    function test_CanMigrateFromEmptyPost() {
        $id = self::factory()->post->create([
            'post_content' => '',
            'post_type' => 'page'
        ]);
        $post = new Post($id);
        $model_version = get_post_meta($id, \Layotter\Upgrades\PluginMigrator::META_FIELD_MODEL_VERSION, true);
        $this->assertRegExp(Layotter_Test_Data::EXPECTED_EMPTY_JSON_REGEX, json_encode($post));
        $this->assertEquals(\Layotter\Upgrades\PluginMigrator::CURRENT_MODEL_VERSION, $model_version);
    }
}