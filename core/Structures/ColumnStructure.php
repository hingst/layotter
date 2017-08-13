<?php

namespace Layotter\Structures;

use Layotter\Errors;

/**
 * Mirrors JSON structure in which columns are saved to the database
 */
class ColumnStructure {

    /**
     * @var int Options ID
     */
    private $options_id = 0;

    /**
     * @var string Column width, e.g. '1/3'
     */
    private $width = '';

    /**
     * @var ElementStructure[] Contained elements
     */
    private $elements = [];

    /**
     * Constructor.
     *
     * @param $structure array json_decode()'d post structure
     */
    public function __construct($structure) {
        if (is_array($structure)) {
            if (isset($structure['options_id']) && is_int($structure['options_id'])) {
                $this->options_id = $structure['options_id'];
            } else {
                Errors::invalid_argument_recoverable('options_id');
            }

            if (isset($structure['width']) && is_string($structure['width'])) {
                $this->width = $structure['width'];
            } else {
                Errors::invalid_argument_recoverable('width');
            }

            if (isset($structure['elements']) && is_array($structure['elements'])) {
                foreach ($structure['elements'] as $element_structure) {
                    $this->elements[] = new ElementStructure($element_structure);
                }
            } else {
                Errors::invalid_argument_recoverable('elements');
            }
        } else {
            Errors::invalid_argument_recoverable('structure');
        }
    }

    /**
     * Options getter
     *
     * @return int Options ID
     */
    public function get_options_id() {
        return $this->options_id;
    }

    /**
     * Width getter
     *
     * @return string Column width, e.g. '1/3'
     */
    public function get_width() {
        return $this->width;
    }

    /**
     * Elements getter
     *
     * @return ElementStructure[] Contained elements
     */
    public function get_elements() {
        return $this->elements;
    }
}
