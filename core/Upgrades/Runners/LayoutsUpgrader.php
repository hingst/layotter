<?php

namespace Layotter\Upgrades\Runners;

use Layotter\Upgrades\LayoutMigrator;

/**
 * Migrates layouts from the old options array structure to individual posts.
 */
class LayoutsUpgrader extends OptionArrayUpgrader {

    /**
     * @return string
     */
    protected function get_old_option_name() {
        return 'layotter_post_layouts';
    }

    /**
     * @param array $item
     * @return bool
     */
    protected function is_item_upgradable($item) {
        return !is_null($item);
    }

    /**
     * @param int $id
     */
    protected function migrate($id) {
        $migrator = new LayoutMigrator($id);
        $migrator->migrate();
    }
}