<?php

namespace Layotter\Upgrades;

use Layotter\Components\Element;
use Layotter\Core;

class TemplateMigrator {

    private $id;

    public function __construct($id) {
        $this->id = $id;
    }

    public function migrate() {
        $templates = get_option('layotter_element_templates');

        if (is_array($templates) AND isset($templates[$this->id]) AND is_array($templates[$this->id])) {

            $old_data = $templates[$this->id];

            if (isset($old_data['migrated_to'])) {
                return null;
            }

            if (isset($old_data['type']) AND isset($old_data['values'])) {
                $element_template = Core::assemble_new_element($old_data['type']);
                $new_element = new EditableMigrator($old_data['type'], $element_template->get_fields(), $old_data['values']);
                $new_id = $new_element->migrate();

                $element = Core::assemble_element($new_id);
                if (!$old_data['deleted']) {
                    $element->set_template(true);
                }

                $templates[$this->id]['migrated_to'] = $new_id;
                update_option('layotter_element_templates', $templates);

                return $new_id;
            }
        }
    }
}