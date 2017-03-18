<?php

namespace Layotter\Upgrades;

use Layotter\Components\Element;
use Layotter\Core;

class TemplateMigrator {

    private $id;
    private $options;

    public function __construct($id, $options = array()) {
        $this->id = $id;
        $this->options = $options;
    }

    public function migrate() {
        $new_data = array(
            'id' => array(),
            'options_id' => 0
        );

        if (!empty($this->options)) {
            $options_template = Core::assemble_new_options('element');
            $new_element = new EditableMigrator('element', $options_template->get_fields(), $this->options);
            $new_data['options_id'] = $new_element->migrate();
        }

        $templates = get_option('layotter_element_templates');

        if (is_array($templates) AND isset($templates[$this->id]) AND is_array($templates[$this->id])) {
            $old_data = $templates[$this->id];

            if (isset($old_data['migrated_to'])) {
                $new_data['id'] = $old_data['migrated_to'];
            } else if (isset($old_data['type']) AND isset($old_data['values'])) {
                $element_template = Core::assemble_new_element($old_data['type']);
                $new_element = new EditableMigrator($old_data['type'], $element_template->get_fields(), $old_data['values']);
                $new_data['id'] = $new_element->migrate();

                $element = Core::assemble_element($new_data['id']);
                if (!$old_data['deleted']) {
                    $element->set_template(true);
                }

                $templates[$this->id]['migrated_to'] = $new_data['id'];
                update_option('layotter_element_templates', $templates);
            }
        }

        return $new_data;
    }
}