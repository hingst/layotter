<?php

namespace Layotter\Upgrades;

use InvalidArgumentException;
use Layotter\Acf\Adapter;

class FieldsMigrator {

    private $post_id;

    private $fields = [];

    private $values = [];

    public function __construct($post_id, $fields, $values) {
        if (!is_int($post_id) || !is_array($fields) || !is_array($values)) {
            throw new InvalidArgumentException();
        }

        $this->post_id = $post_id;
        $this->fields = $fields;
        $this->values = $values;
    }

    public function migrate() {
        foreach ($this->fields as $field) {
            $field_name = $field['name'];

            // quick fix for broken Repeater fields
            if ($field['type'] == 'repeater' && isset($this->values[ $field_name ]) && is_array($this->values[ $field_name ])) {
                $values[ $field_name ] = array_values($this->values[ $field_name ]);
            }

            if (isset($this->values[ $field_name ])) {
                $field['value'] = $this->values[ $field_name ];
                Adapter::update_field_value($field['key'], $field['value'], $this->post_id);
            }
        }
    }
}
