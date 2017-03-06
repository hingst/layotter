<?php

namespace Layotter\Upgrades;

use Layotter\Core;

class ElementMigrator {

    private $old_data;

    public function __construct($data) {
        $this->old_data = $data;
    }

    public function migrate() {
        $new_data = array(
            'id' => array(),
            'options_id' => 0
        );

        if (isset($this->old_data['options'])) {
            $options_template = Core::assemble_new_options('element');
            $new_element = new EditableMigrator('element', $options_template->get_fields(), $this->old_data['options']);
            $new_data['options_id'] = $new_element->migrate();
        }

        if (isset($this->old_data['type']) AND isset($this->old_data['values'])) {
            $element_template = Core::assemble_new_element($this->old_data['type']);
            $new_element = new EditableMigrator($this->old_data['type'], $element_template->get_fields(), $this->old_data['values']);
            $new_data['id'] = $new_element->migrate();
        }

        return $new_data;
    }
}