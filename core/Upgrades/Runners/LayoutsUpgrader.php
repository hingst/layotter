<?php

namespace Layotter\Upgrades\Runners;

use Layotter\Upgrades\LayoutMigrator;

class LayoutsUpgrader extends OptionArrayUpgrader {

    protected function get_chunk_size() {
        return 2;
    }

    protected function get_legacy_option_items() {
        return 'layotter_post_layouts';
    }

    protected function get_option_needs_upgrade() {
        return 'layotter_upgrades_layouts_needs_upgrade';
    }

    protected function is_item_upgradable($item) {
        return !is_null($item);
    }

    protected function migrate($id) {
        $migrator = new LayoutMigrator($id);
        $migrator->migrate();
    }
}