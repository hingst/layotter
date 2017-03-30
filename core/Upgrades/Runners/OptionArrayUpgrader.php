<?php

namespace Layotter\Upgrades\Runners;

abstract class OptionArrayUpgrader {

    abstract protected function get_chunk_size();

    abstract protected function get_legacy_option_items();

    abstract protected function get_option_needs_upgrade();

    abstract protected function is_item_upgradable($item);

    abstract protected function get_migrator($id);

    public function __construct() {
    }

    public function check_on_plugin_upgrade() {
        update_option($this->get_option_needs_upgrade(), $this->has_more_steps());
    }

    public function needs_upgrade() {
        return (get_option($this->get_option_needs_upgrade()) == true);
    }

    protected function count_upgradable_items() {
        $layouts = get_option($this->get_legacy_option_items());
        if (!is_array($layouts)) {
            return 0;
        }
        $upgradable_layouts = array_filter($layouts, function($item) {
            return $this->is_item_upgradable($item);
        });
        return count($upgradable_layouts);
    }

    protected function has_more_steps() {
        return ($this->count_upgradable_items() > 0);
    }

    protected function get_lowest_upgradable_item_index() {
        $layouts = get_option($this->get_legacy_option_items());
        foreach ($layouts as $index => $layout) {
            if ($this->is_item_upgradable($layout)) {
                return $index;
            }
        }
        return -1;
    }

    public function do_upgrade_step() {
        $layouts = get_option($this->get_legacy_option_items());
        $layouts = array_slice($layouts, $this->get_lowest_upgradable_item_index(), $this->get_chunk_size(), true); // preserve keys

        foreach ($layouts as $id => $layout) {
            if ($this->is_item_upgradable($layout)) {
                $lm = $this->get_migrator($id);
                $lm->migrate();
            }
        }

        if (!$this->has_more_steps()) {
            update_option($this->get_option_needs_upgrade(), false);
        }
    }

    public function get_status() {
        $all_items = get_option($this->get_legacy_option_items());
        if (!is_array($all_items) OR empty($all_items)) {
            return 100;
        }
        $items_total = count($all_items);
        $items_remaining = $this->count_upgradable_items();
        $items_done = $items_total - $items_remaining;
        return ($items_done / $items_total * 100);
    }
}