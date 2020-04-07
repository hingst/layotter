<?php

namespace Layotter\Upgrades\Runners;

/**
 * Used to migrate something that was previously stored as an array in a Wordpress option.
 */
abstract class OptionArrayUpgrader {

    /**
     * How many items to migrate with each step
     */
    const CHUNK_SIZE = 2;

    /**
     * @return string
     */
    abstract protected function get_old_option_name();

    /**
     * @param array $item
     * @return bool
     */
    abstract protected function is_item_upgradable($item);

    /**
     * @param int $id
     * @return void
     */
    abstract protected function migrate($id);

    /**
     * @return int
     */
    protected function count_upgradable_items() {
        $items = get_option($this->get_old_option_name());

        if (!is_array($items)) {
            return 0;
        }

        $upgradable_items = array_filter($items, function($item) {
            return $this->is_item_upgradable($item);
        });

        return count($upgradable_items);
    }

    /**
     * @return bool
     */
    public function needs_upgrade() {
        return ($this->count_upgradable_items() > 0);
    }

    /**
     * @return int
     */
    protected function get_lowest_upgradable_item_index() {
        $items = get_option($this->get_old_option_name());

        foreach ($items as $index => $item) {
            if ($this->is_item_upgradable($item)) {
                return $index;
            }
        }

        return -1;
    }

    public function do_upgrade_step() {
        $items = get_option($this->get_old_option_name());
        $items = array_slice($items, $this->get_lowest_upgradable_item_index(), self::CHUNK_SIZE, true);

        foreach ($items as $id => $item) {
            if ($this->is_item_upgradable($item)) {
                $this->migrate($id);
            }
        }

        if (!$this->needs_upgrade()) {
            delete_option($this->get_old_option_name());
        }
    }
}