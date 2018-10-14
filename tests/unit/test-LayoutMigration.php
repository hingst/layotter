<?php

use Layotter\Components\Post;
use Layotter\Core;
use Layotter\Tests\Unit\TestData;
use Layotter\Upgrades\LayoutMigrator;

/**
 * @group unit
 */
class LayoutMigrationTest extends WP_UnitTestCase {

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
        $post = new Post();
        $layouts = $post->get_available_layouts();

        $this->assertArrayHasKey(0, $layouts);

        $id = $layouts[0]->get_id();
        $actual = $layouts[0]->get_frontend_view();

        $model_version = get_post_meta($id, Core::META_FIELD_MODEL_VERSION, true);
        $this->assertEquals(TestData::EXPECTED_VIEW, $actual);
        $this->assertEquals(Core::CURRENT_MODEL_VERSION, $model_version);
    }
}