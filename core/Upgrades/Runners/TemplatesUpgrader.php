<?php

namespace Layotter\Upgrades\Runners;

use Exception;
use Layotter\Upgrades\TemplateMigrator;

/**
 * Migrates templates from the old options array structure to individual posts.
 */
class TemplatesUpgrader extends OptionArrayUpgrader {

    /**
     * @return string
     */
    protected function get_old_option_name() {
        return 'layotter_element_templates';
    }

    /**
     * @param array $item
     * @return bool
     */
    protected function is_item_upgradable($item) {
        return !isset($item['migrated_to']);
    }

    /**
     * @param int $id
     * @throws Exception
     */
    protected function migrate($id) {
        $migrator = new TemplateMigrator($id);
        $migrator->migrate();
    }
}