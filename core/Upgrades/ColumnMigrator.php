<?php

namespace Layotter\Upgrades;

class ColumnMigrator {

    private $old_data;

    public function __construct($data) {
        $this->old_data = $data;
    }

    public function migrate() {
        $new_data = array(
            'options_id' => 0,
            'elements' => array()
        );

        if (isset($this->old_data['options'])) {
            $new_options = new EditableMigrator('col', $ALLOWED_FIELDS, $this->old_data['options']); // TODO: where do I get allowed fields?
            $new_data['options_id'] = $new_options->migrate();
        }

        if (isset($this->old_data['elements'])) {
            foreach ($this->old_data['elements'] as $element) {
                $new_element = new ElementMigrator($element);
                $new_data['elements'][] = $new_element->migrate();
            }
        }

        return $new_data;
    }
}