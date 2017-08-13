<?php

namespace Layotter\Structures;

use Layotter\Errors;

/**
 * Mirrors JSON structure in which elements are saved to the database
 */
class ElementStructure {

    /**
     * @var int Element ID
     */
    private $id = 0;

    /**
     * @var int Options ID
     */
    private $options_id = 0;

    /**
     * Constructor.
     *
     * @param $structure array json_decode()'d post structure
     * @throws \Exception If element ID is missing
     */
    public function __construct($structure) {
        if (is_array($structure)) {
            if (isset($structure['id']) && is_int($structure['id'])) {
                $this->id = $structure['id'];
            } else {
                Errors::invalid_argument_recoverable('id');
            }

            if (isset($structure['options_id']) && is_int($structure['options_id'])) {
                $this->options_id = $structure['options_id'];
            } else {
                Errors::invalid_argument_recoverable('options_id');
            }
        } else {
            Errors::invalid_argument_recoverable('structure');
        }
    }

    /**
     * Element getter
     *
     * @return int Element ID
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Options getter
     *
     * @return int Options ID
     */
    public function get_options_id() {
        return $this->options_id;
    }

}
