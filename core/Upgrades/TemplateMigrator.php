<?php

namespace Layotter\Upgrades;

use Exception;
use InvalidArgumentException;
use Layotter\Repositories\ElementRepository;

class TemplateMigrator {

    private $id;

    private $options = [];

    public function __construct($id, $options = []) {
        if (!is_int($id) || !is_array($options)) {
            throw new InvalidArgumentException();
        }

        $this->id = $id;
        $this->options = $options;
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

        if (!empty($this->options)) {
            $new_options = new OptionsMigrator('element', $this->options);
            $new_data['options_id'] = $new_options->migrate();
        }

        $templates = get_option('layotter_element_templates');

        if (is_array($templates) && isset($templates[$this->id]) && is_array($templates[$this->id])) {
            $old_data = $templates[$this->id];

            if (isset($old_data['migrated_to'])) {
                $new_data['id'] = $old_data['migrated_to'];
            } else if (isset($old_data['type']) && isset($old_data['values'])) {
                $new_element = new NewElementMigrator($old_data['type'], $old_data['values']);
                $new_data['id'] = $new_element->migrate();

                $element = ElementRepository::load($new_data['id'], $new_data['options_id']);
                if (!isset($old_data['deleted'])) {
                    ElementRepository::promote_element($element);
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