<?php

namespace Layotter\Models;

use Exception;
use InvalidArgumentException;

class Row {

    /**
     * @var string
     */
    private $layout;

    /**
     * @var Options
     */
    private $options;

    /**
     * @var Column[]
     */
    private $columns;

    /**
     * @param string $layout
     * @param Options $options
     * @param Column[] $columns
     * @throws Exception
     */
    public function __construct($layout, $options, $columns) {
        if (!is_string($layout) || !($options instanceof Options) || !is_array($columns)) {
            throw new InvalidArgumentException();
        }

        $this->layout = $layout;
        $this->options = $options;
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function get_layout() {
        return $this->layout;
    }

    /**
     * @return Options
     */
    public function get_options() {
        return $this->options;
    }

    /**
     * @return Column[]
     */
    public function get_columns() {
        return $this->columns;
    }
}
