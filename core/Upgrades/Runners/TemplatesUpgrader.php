<?php

namespace Layotter\Upgrades\Runners;

use Layotter\Upgrades\TemplateMigrator;

class TemplatesUpgrader extends OptionArrayUpgrader {

    protected function get_legacy_option_name() {
        return 'layotter_element_templates';
    }

    protected function is_item_upgradable($item) {
        return !isset($item['migrated_to']);
    }

    protected function migrate($id) {
        $migrator = new TemplateMigrator($id);
        $migrator->migrate();
    }
}