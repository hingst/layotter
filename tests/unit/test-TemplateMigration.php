<?php

use Layotter\Repositories\PostRepository;
use Layotter\Renderers\PostRenderer;
use Layotter\Tests\TestData;

/**
 * @group unit
 */
class TemplateMigrationTest extends \WP_UnitTestCase {

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

        $post = PostRepository::load($post_id);
        $renderer = new PostRenderer($post);
        $actual = $renderer->render_frontend_view();

        $this->assertEquals(TestData::EXPECTED_VIEW, $actual);
    }
}