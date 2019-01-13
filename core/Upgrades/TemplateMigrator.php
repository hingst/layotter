<?php

namespace Layotter\Upgrades;

use Layotter\Core;

class TemplateMigrator {

    private $id;
    private $options = [];

    public function __construct($id, $options = []) {
        $this->id = intval($id);

        if (is_array($options)) {
            $this->options = $options;
        }
    }

    public function migrate() {
        $new_data = [
            'id' => 0,
            'options_id' => 0
        ];

        if (!empty($this->options)) {
            $options_template = Core::assemble_new_options('element');
            $new_options = new EditableMigrator('element', $options_template->get_fields(), $this->options);
            $new_data['options_id'] = $new_options->migrate();
        }

        $templates = get_option('layotter_element_templates');

        if (is_array($templates) && isset($templates[$this->id]) && is_array($templates[$this->id])) {
            $old_data = $templates[$this->id];

            if (isset($old_data['migrated_to'])) {
                $new_data['id'] = $old_data['migrated_to'];
            } else if (isset($old_data['type']) && isset($old_data['values'])) {
                $element_template = Core::assemble_new_element($old_data['type']);
                $new_element = new EditableMigrator($old_data['type'], $element_template->get_fields(), $old_data['values']);
                $new_data['id'] = $new_element->migrate();

                $element = Core::assemble_element($new_data['id']);
                if (!isset($old_data['deleted'])) {
                    $element->set_template(true);
                }

                $templates[$this->id] = [
                    'migrated_to' => $new_data['id']
                ];
                update_option('layotter_element_templates', $templates);
            }
        }

        return $new_data;
    }
}