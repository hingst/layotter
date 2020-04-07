<?php

namespace Layotter\Models;

use InvalidArgumentException;

class Post {

    /**
     * @var Options
     */
    private $options;

    /**
     * @var Row[]
     */
    private $rows;

    /**
     * @param Options $options
     * @param Row[] $rows
     */
    public function __construct($options, $rows) {
        if (!($options instanceof Options) || !is_array($rows)) {
            throw new InvalidArgumentException();
        }

        $this->options = $options;
        $this->rows = $rows;
    }

    /**
     * @return Options
     */
    public function get_options() {
        return $this->options;
    }

    /**
     * @return Row[]
     */
    public function get_rows() {
        return $this->rows;
    }
}
