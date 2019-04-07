<?php

use Layotter\Components\Post;
use Layotter\Core;
use Layotter\Tests\TestData;

/**
 * @group unit
 */
class PostMigrationTest extends \WP_UnitTestCase {

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

    public function test_CanMigrateAllFieldTypes() {
        $input = str_replace('ATTACHMENT_ID', TestData::get_attachment_id(), TestData::POST_150_ALL_FIELDS_JSON);

        // wp_insert_post() expects magic quotes, https://core.trac.wordpress.org/ticket/21767
        $input = addslashes($input);

        $id = self::factory()->post->create([
            'meta_input' => [
                'layotter_json' => $input
            ],
            'post_type' => 'page'
        ]);

        $post = new Post($id);
        $this->assertRegExp(TestData::EXPECTED_ALL_FIELDS_JSON_REGEX, json_encode($post));

        $matches = [];
        preg_match(TestData::EXPECTED_ALL_FIELDS_JSON_REGEX, json_encode($post), $matches);
        $this->assertEquals(3, count($matches));
        $element_id = $matches[1];
        $options_id = $matches[2];

        // basic fields
        $this->assertEquals('text', get_field('text', $element_id));
        $this->assertEquals('textarea', get_field('textarea', $element_id));
        $this->assertEquals(50, get_field('number', $element_id));
        $this->assertEquals(50, get_field('range', $element_id));
        $this->assertEquals('email@example.com', get_field('email', $element_id));
        $this->assertEquals('http://example.com', get_field('url', $element_id));
        $this->assertEquals('password', get_field('password', $element_id));

        // content fields
        $this->assertStringEndsWith(TESTS_UPLOAD_FILE_NAME, get_field('image', $element_id));
        $this->assertStringEndsWith(TESTS_UPLOAD_FILE_NAME, get_field('file', $element_id));
        $this->assertContains('<p>wysiwyg</p>', get_field('wysiwyg', $element_id));
        $this->assertContains('5bqpcIX2VDQ', get_field('oembed', $element_id));

        // choice fields
        $this->assertEquals(2, get_field('select', $element_id));
        $this->assertEquals(2, get_field('checkbox', $element_id)[0]);
        $this->assertEquals(2, get_field('radio', $element_id));
        $this->assertEquals(2, get_field('button_group', $element_id));
        $this->assertEquals(1, get_field('boolean', $element_id));

        // relational fields
        $this->assertEquals('http://example.com', get_field('link', $element_id));
        $this->assertEquals('Hello world!', get_field('post_object', $element_id)->post_title);
        $this->assertContains(home_url(), get_field('page_link', $element_id));
        $this->assertEquals('Hello world!', get_field('relationship', $element_id)[0]->post_title);
        $this->assertEquals('Uncategorized', get_field('taxonomy', $element_id)[0]->name);
        $this->assertEquals(TESTS_WP_USER, get_field('user', $element_id)->data->user_login);

        // jquery fields
        $this->assertEmpty(get_field('google_map', $element_id));
        $this->assertEquals('2019-01-01', get_field('date_picker', $element_id));
        $this->assertEquals('2019-01-01 00:00:00', get_field('date_time_picker', $element_id));
        $this->assertEquals('00:00:00', get_field('time_picker', $element_id));
        $this->assertEquals('#123456', get_field('color_picker', $element_id));

        // options
        $this->assertEquals('option', get_field('option', $options_id));
    }

    /**
     * @group acfprofields
     */
    public function test_CanMigrateAcfProFieldTypes() {
        $input = str_replace('ATTACHMENT_ID', TestData::get_attachment_id(), TestData::POST_150_ALL_FIELDS_JSON);

        // wp_insert_post() expects magic quotes, https://core.trac.wordpress.org/ticket/21767
        $input = addslashes($input);

        $id = self::factory()->post->create([
            'meta_input' => [
                'layotter_json' => $input
            ],
            'post_type' => 'page'
        ]);

        $post = new Post($id);

        $matches = [];
        preg_match(TestData::EXPECTED_ALL_FIELDS_JSON_REGEX, json_encode($post), $matches);
        $element_id = $matches[1];

        // gallery
        $this->assertEquals(TESTS_UPLOAD_FILE_NAME, get_field('gallery', $element_id)[0]['filename']);

        // layout fields
        $repeater = get_field('repeater', $element_id);
        $flexible_content = get_field('flexible_content', $element_id);
        $this->assertContains('<p>wysiwyg</p>', $repeater[0]['wysiwyg']);
        $this->assertEquals('Hello world!', $repeater[0]['relationship'][0]->post_title);
        $this->assertContains('<p>wysiwyg</p>', $flexible_content[0]['wysiwyg']);
        $this->assertEquals('Hello world!', $flexible_content[0]['relationship'][0]->post_title);
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