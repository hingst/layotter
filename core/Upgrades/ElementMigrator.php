<?php

namespace Layotter\Upgrades;

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
            $new_element = new EditableMigrator('element', $ALLOWED_FIELDS, $this->old_data['options']); // TODO: where do I get allowed fields?
            $new_data['options_id'] = $new_element->migrate();
        }

        if (isset($this->old_data['type']) AND isset($this->old_data['values'])) {
            $new_element = new EditableMigrator($this->old_data['type'], $ALLOWED_FIELDS, $this->old_data['values']); // TODO: where do I get allowed fields?
            $new_data['id'] = $new_element->migrate();
        }

        return $new_data;
    }
}