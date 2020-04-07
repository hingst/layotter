<?php

namespace Layotter\Models;

use Exception;
use InvalidArgumentException;
use Layotter\ElementTypes\BaseElementType;
use Layotter\Repositories\ElementRepository;
use Layotter\Services\ElementFieldsService;

class Element {

    /**
     * @var BaseElementType
     */
    private $type;

    /**
     * @var int
     */
    private $id;

    /**
     * @var Options
     */
    private $options;

    /**
     * @param BaseElementType $type
     * @param int $id
     * @param Options $options
     */
    public function __construct($type, $id, $options) {
        if (!($type instanceof BaseElementType) || !is_int($id) || !($options instanceof Options)) {
            throw new InvalidArgumentException();
        }

        $this->type = $type;
        $this->id = $id;
        $this->options = $options;
    }

    /**
     * @param int $id
     */
    public function set_id($id) {
        if (!is_int($id)) {
            throw new InvalidArgumentException();
        }

        $this->id = $id;
    }

    /**
     * @return BaseElementType
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * @return int
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * @return Options
     */
    public function get_options() {
        return $this->options;
    }
}
