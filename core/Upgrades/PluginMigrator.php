<?php

namespace Layotter\Upgrades;

use Layotter\Upgrades\Runners\LayoutsUpgrader;
use Layotter\Upgrades\Runners\TemplatesUpgrader;

class PluginMigrator {

    const META_FIELD_MODEL_VERSION = 'layotter_model_version';
    const CURRENT_MODEL_VERSION = '2.0.0';

    /**
     * @return bool
     */
    public static function needs_upgrade() {
        $model_version = get_option(self::META_FIELD_MODEL_VERSION);
        return (empty($model_version) || version_compare($model_version, self::CURRENT_MODEL_VERSION) < 0);
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

        update_option(self::META_FIELD_MODEL_VERSION, self::CURRENT_MODEL_VERSION);
    }

}