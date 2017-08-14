<?php

namespace Layotter\Upgrades;

use Layotter\Core;

class ColumnMigrator {

    private $old_data;

    public function __construct($data) {
        $this->old_data = $data;
    }

    public function migrate() {
        $new_data = [
            'options_id' => 0,
            'elements' => []
        ];

        if (isset($this->old_data['options'])) {
            $options_template = Core::assemble_new_options('col');
            $new_options = new EditableMigrator('col', $options_template->get_fields(), $this->old_data['options']);
            $new_data['options_id'] = $new_options->migrate();
        }

        if (isset($this->old_data['elements'])) {
            foreach ($this->old_data['elements'] as $element) {
                if (isset($element['template_id'])) {
                    $options = isset($element['options']) ? $element['options'] : [];
                    $new_element = new TemplateMigrator($element['template_id'], $options);
                    $new_data['elements'][] = $new_element->migrate();
                } else {
                    $new_element = new ElementMigrator($element);
                    $new_data['elements'][] = $new_element->migrate();
                }
            }
        }

        return $new_data;
    }
}