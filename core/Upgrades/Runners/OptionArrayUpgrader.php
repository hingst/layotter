<?php

namespace Layotter\Upgrades\Runners;

abstract class OptionArrayUpgrader {

    const CHUNK_SIZE = 2;

    abstract protected function get_legacy_option_name();

    abstract protected function is_item_upgradable($item);

    abstract protected function migrate($id);

    protected function count_upgradable_items() {
        $items = get_option($this->get_legacy_option_name());
        if (!is_array($items)) {
            return 0;
        }
        $upgradable_items = array_filter($items, function($item) {
            return $this->is_item_upgradable($item);
        });
        return count($upgradable_items);
    }

    public function needs_upgrade() {
        return ($this->count_upgradable_items() > 0);
    }

    protected function get_lowest_upgradable_item_index() {
        $items = get_option($this->get_legacy_option_name());
        foreach ($items as $index => $item) {
            if ($this->is_item_upgradable($item)) {
                return $index;
            }
        }
        return -1;
    }

    public function do_upgrade_step() {
        $items = get_option($this->get_legacy_option_name());
        $items = array_slice($items, $this->get_lowest_upgradable_item_index(), self::CHUNK_SIZE, true); // preserve keys

        foreach ($items as $id => $item) {
            if ($this->is_item_upgradable($item)) {
                $this->migrate($id);
            }
        }

        if (!$this->needs_upgrade()) {
            delete_option($this->get_legacy_option_name());
        }
    }
}