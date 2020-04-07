<?php

use Layotter\Repositories\LayoutRepository;
use Layotter\Repositories\PostRepository;
use Layotter\Initializer;
use Layotter\Renderers\PostRenderer;
use Layotter\Tests\TestData;
use Layotter\Upgrades\LayoutMigrator;

/**
 * @group unit
 */
class LayoutMigrationTest extends \WP_UnitTestCase {

    public function test_CanMigrateLayout() {
        update_option('layotter_post_layouts', [
            [
                'layout_id' => 0,
                'name' => 'cheese',
                'json' => TestData::POST_150_JSON,
                'time_created' => 1502645077,
            ],
        ]);

        $migrator = new LayoutMigrator(0);
        $migrator->migrate();
        //$post = PostRepository::create(0); // TODO: was this line necessary?
        $layouts = LayoutRepository::get_allowed_for_post_type('dummy');

        $this->assertArrayHasKey(0, $layouts);

        $id = $layouts[0]->get_id();
        $post = PostRepository::load($id);
        $renderer = new PostRenderer($post);
        $actual = $renderer->render_frontend_view();

        $model_version = get_post_meta($id, Initializer::META_FIELD_MODEL_VERSION, true);
        $this->assertEquals(TestData::EXPECTED_VIEW, $actual);
        $this->assertEquals(Initializer::MODEL_VERSION, $model_version);
    }
}