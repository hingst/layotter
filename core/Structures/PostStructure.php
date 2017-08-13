<?php

namespace Layotter\Structures;

/**
 * Mirrors JSON structure in which posts are saved to the database
 */
class PostStructure {

    /**
     * @var int Options ID
     */
    private $options_id = 0;

    /**
     * @var RowStructure[] Contained rows
     */
    private $rows = array();

    /**
     * Constructor.
     *
     * @param $structure array json_decode()'d post structure
     */
    public function __construct($structure) {
        if (is_array($structure)) {
            if (isset($structure['options_id']) && is_int($structure['options_id'])) {
                $this->options_id = $structure['options_id'];
            }

            if (isset($structure['rows']) && is_array($structure['rows'])) {
                foreach ($structure['rows'] as $row_structure) {
                    $this->rows[] = new RowStructure($row_structure);
                }
            }
        } else {
            throw new \InvalidArgumentException('Structure must be an array.');
        }
    }

    /**
     * Options ID getter
     *
     * @return int Options ID
     */
    public function get_options_id() {
        return $this->options_id;
    }

    /**
     * Rows getter
     *
     * @return RowStructure[] Contained rows
     */
    public function get_rows() {
        return $this->rows;
    }
}
