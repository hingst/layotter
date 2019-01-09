<?php

use Layotter\Components\Post;
use Layotter\Tests\BaseTest;
use Layotter\Tests\TestData;

/**
 * @group unit
 */
class TemplateMigrationTest extends BaseTest {

    public function test_CanMigrateTemplate() {
        update_option('layotter_element_templates', [
            [
                'template_id' => 0,
                'type' => 'layotter_example_element',
                'values' => [
                    'content' => 'blah blah blah',
                ],
                'options' => [],
                'deleted' => false,
            ]
        ]);

        $post_id = self::factory()->post->create([
            'meta_input' => [
                'layotter_json' => TestData::POST_150_TEMPLATE_JSON
            ],
            'post_type' => 'page'
        ]);

        $post = new Post($post_id);
        $actual = $post->get_frontend_view();

        $this->assertEquals(TestData::EXPECTED_VIEW, $actual);
    }
}