<?php

namespace Layotter\Upgrades;

class RowMigrator {

    private $old_data;

    public function __construct($data) {
        $this->old_data = $data;
    }

    public function migrate() {
        $new_data = array(
            'options_id' => 0,
            'cols' => array(),
            'layout' => ''
        );

        if (isset($this->old_data['options'])) {
            $new_options = new EditableMigrator('row', $ALLOWED_FIELDS, $this->old_data['options']); // TODO: where do I get allowed fields?
            $new_data['options_id'] = $new_options->migrate();
        }

        if (isset($this->old_data['cols'])) {
            foreach ($this->old_data['cols'] as $col) {
                $new_column = new ColumnMigrator($col);
                $new_data['cols'][] = $new_column->migrate();
            }
        }

        if (isset($this->old_data['layout'])) {
            $new_data['layout'] = $this->old_data['layout'];
        }

        return $new_data;
    }
}