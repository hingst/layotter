<?php

namespace Layotter\Structures;

use Layotter\Errors;

/**
 * Mirrors JSON structure in which rows are saved to the database
 */
class RowStructure {

    /**
     * @var string Describes contained column widths, e.g. '1/3 1/3 1/3'
     */
    private $layout = '';

    /**
     * @var int Options ID
     */
    private $options_id = 0;

    /**
     * @var ColumnStructure[] Contained columns
     */
    private $columns = [];

    /**
     * Constructor.
     *
     * @param $structure array json_decode()'d post structure
     */
    public function __construct($structure) {
        if (is_array($structure)) {
            if (isset($structure['layout']) && is_string($structure['layout'])) {
                $this->layout = $structure['layout'];
            } else {
                Errors::invalid_argument_recoverable('layout');
            }

            if (isset($structure['options_id']) && is_int($structure['options_id'])) {
                $this->options_id = $structure['options_id'];
            } else {
                Errors::invalid_argument_recoverable('options_id');
            }

            $layout_array = explode(' ', $this->layout);
            if (isset($structure['cols']) && is_array($structure['cols'])) {
                foreach ($structure['cols'] as $i => $col_structure) {
                    $col_structure['width'] = isset($layout_array[ $i ]) ? $layout_array[ $i ] : '';
                    $this->columns[] = new ColumnStructure($col_structure);
                }
            } else {
                Errors::invalid_argument_recoverable('cols');
            }
        } else {
            Errors::invalid_argument_recoverable('structure');
        }
    }

    /**
     * Layout getter
     *
     * @return string Describes contained column widths, e.g. '1/3 1/3 1/3'
     */
    public function get_layout() {
        return $this->layout;
    }

    /**
     * Options getter
     *
     * @return int Row options
     */
    public function get_options_id() {
        return $this->options_id;
    }

    /**
     * Columns getter
     *
     * @return ColumnStructure[] Contained columns
     */
    public function get_columns() {
        return $this->columns;
    }
}
