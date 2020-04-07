<?php

namespace Layotter\Models;

use Exception;
use InvalidArgumentException;

class Column {

    /**
     * @var string
     */
    private $width;

    /**
     * @var Options
     */
    private $options;

    /**
     * @var Element[]
     */
    private $elements;

    /**
     * @param string $width
     * @param Options $options
     * @param array $elements
     * @throws Exception
     */
    public function __construct($width, $options, $elements) {
        if (!is_string($width) || !($options instanceof Options) || !is_array($elements)) {
            throw new InvalidArgumentException();
        }

        $this->width = $width;
        $this->options = $options;
        $this->elements = $elements;
    }

    /**
     * @return string
     */
    public function get_width() {
        return $this->width;
    }

    /**
     * @return Options
     */
    public function get_options() {
        return $this->options;
    }

    /**
     * @return Element[]
     */
    public function get_elements() {
        return $this->elements;
    }
}
