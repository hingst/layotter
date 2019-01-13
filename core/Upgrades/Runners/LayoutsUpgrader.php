<?php

namespace Layotter\Upgrades\Runners;

use Layotter\Upgrades\LayoutMigrator;

class LayoutsUpgrader extends OptionArrayUpgrader {

    protected function get_legacy_option_name() {
        return 'layotter_post_layouts';
    }

    protected function is_item_upgradable($item) {
        return !is_null($item);
    }

    protected function migrate($id) {
        $migrator = new LayoutMigrator($id);
        $migrator->migrate();
    }
}