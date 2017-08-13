<?php

namespace Layotter\Structures;

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
     * @var bool Whether element is a template
     */
    private $is_template = false;

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
            }

            if (isset($structure['options_id']) && is_int($structure['options_id'])) {
                $this->options_id = $structure['options_id'];
            }

            if (isset($structure['is_template']) && is_bool($structure['is_template'])) {
                $this->is_template = $structure['is_template'];
            }
        } else {
            throw new \InvalidArgumentException('Structure must be an array.');
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

    /**
     * IsTemplate getter
     *
     * @return bool Whether element is a template
     */
    public function get_is_template() {
        return $this->is_template;
    }
}
