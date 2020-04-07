<?php

namespace Layotter\Upgrades;

use InvalidArgumentException;

class RowMigrator {

    private $old_data = [];

    public function __construct($data) {
        if (!is_array($data)) {
            throw new InvalidArgumentException();
        }

        $this->old_data = $data;
    }

    public function migrate() {
        $new_data = [
            'options_id' => 0,
            'cols' => [],
            'layout' => ''
        ];

        if (isset($this->old_data['options']) && !empty($this->old_data['options'])) {
            $new_options = new OptionsMigrator('row', $this->old_data['options']);
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