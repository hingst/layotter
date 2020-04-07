<?php

namespace Layotter\Upgrades;

use Layotter\Initializer;
use Layotter\Upgrades\Runners\LayoutsUpgrader;
use Layotter\Upgrades\Runners\TemplatesUpgrader;

class PluginMigrator {

    public static function upgrade_on_demand() {
        if (PluginMigrator::needs_upgrade()) {
            PluginMigrator::upgrade();
        }
    }

    /**
     * @return bool
     */
    public static function needs_upgrade() {
        $model_version = get_option(Initializer::META_FIELD_MODEL_VERSION);
        return (empty($model_version) || version_compare($model_version, Initializer::MODEL_VERSION) < 0);
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

        update_option(Initializer::META_FIELD_MODEL_VERSION, Initializer::MODEL_VERSION);
    }

}