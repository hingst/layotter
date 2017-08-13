<?php

namespace Layotter\Upgrades;

use Layotter\Components\Editable;

class EditableMigrator {

    private $type;
    private $fields = [];
    private $values = [];

    public function __construct($type, $allowed_fields, $provided_values) {
        $this->type = strval($type);

        if (is_array($allowed_fields)) {
            $this->fields = $allowed_fields;
        }

        if (is_array($provided_values)) {
            $this->values = $provided_values;
        }
    }

    public function migrate() {
        $id = wp_insert_post([
            'post_type' => Editable::POST_TYPE_EDITABLE,
            'meta_input' => [
                Editable::META_FIELD_EDITABLE_TYPE => $this->type
            ],
            'post_status' => 'publish'
        ]);

        foreach ($this->fields as $field) {
            $field_name = $field['name'];

            // quick fix for broken Repeater fields
            if ($field['type'] == 'repeater' && is_array($this->values[ $field_name ])) {
                $values[ $field_name ] = array_values($this->values[ $field_name ]);
            }

            if (isset($this->values[ $field_name ])) {
                $field['value'] = $this->values[ $field_name ];
                update_field($field['key'], addslashes($field['value']), $id);
            }
        }

        return $id;
    }

}
