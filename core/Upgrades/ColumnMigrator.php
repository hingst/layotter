<?php

namespace Layotter\Upgrades;

use Exception;
use InvalidArgumentException;

class ColumnMigrator {

    /**
     * @var array
     */
    private $old_data = [];

    /**
     * @param array $data
     */
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
            'options_id' => 0,
            'elements' => []
        ];

        if (isset($this->old_data['options']) && !empty($this->old_data['options'])) {
            $new_options = new OptionsMigrator('col', $this->old_data['options']);
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