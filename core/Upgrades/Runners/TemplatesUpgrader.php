<?php

namespace Layotter\Upgrades\Runners;

use Layotter\Upgrades\TemplateMigrator;

class TemplatesUpgrader extends OptionArrayUpgrader {

    protected function get_chunk_size() {
        return 2;
    }

    protected function get_legacy_option_items() {
        return 'layotter_element_templates';
    }

    protected function get_option_needs_upgrade() {
        return 'layotter_upgrades_templates_needs_upgrade';
    }

    protected function is_item_upgradable($item) {
        return !isset($item['migrated_to']);
    }

    protected function get_migrator($id) {
        return new TemplateMigrator($id);
    }
}