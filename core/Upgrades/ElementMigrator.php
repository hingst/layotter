<?php

namespace Layotter\Upgrades;

use Exception;
use InvalidArgumentException;

class ElementMigrator {

    private $old_data = [];

    public function __construct($data) {
        if (!is_array($data)) {
            throw new InvalidArgumentException();
        }

        $this->old_data = $data;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function migrate() {
        $new_data = [
            'id' => 0,
            'options_id' => 0
        ];

        if (isset($this->old_data['options']) && !empty($this->old_data['options'])) {
            $new_options = new OptionsMigrator('element', $this->old_data['options']);
            $new_data['options_id'] = $new_options->migrate();
        }

        if (isset($this->old_data['type']) && isset($this->old_data['values'])) {
            $new_element = new NewElementMigrator($this->old_data['type'], $this->old_data['values']);
            $new_data['id'] = $new_element->migrate();
        }

        return $new_data;
    }
}