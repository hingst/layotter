<?php

namespace Layotter\Upgrades;

use Layotter\Core;
use Layotter\Upgrades\Runners\LayoutsUpgrader;
use Layotter\Upgrades\Runners\TemplatesUpgrader;

class PluginMigrator {

    /**
     * @return bool
     */
    public static function needs_upgrade() {
        $model_version = get_option(Core::META_FIELD_MODEL_VERSION);
        return (empty($model_version) || version_compare($model_version, Core::CURRENT_MODEL_VERSION) < 0);
    }

    public static function upgrade() {
        $templates_upgrader = new TemplatesUpgrader();
        while ($templates_upgrader->needs_upgrade()) {
            $templates_upgrader->do_upgrade_step();
        }

        $layouts_upgrader = new LayoutsUpgrader();
        while ($layouts_upgrader->needs_upgrade()) {
            $layouts_upgrader->do_upgrade_step();
        }

        update_option(Core::META_FIELD_MODEL_VERSION, Core::CURRENT_MODEL_VERSION);
    }

}