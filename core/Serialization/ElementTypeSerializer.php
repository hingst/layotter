<?php

namespace Layotter\Serialization;

use InvalidArgumentException;
use JsonSerializable;
use Layotter\Models\ElementType;

class ElementTypeSerializer implements JsonSerializable {

    /**
     * @var ElementType
     */
    private $model;

    /**
     * @param ElementType $model
     */
    public function __construct($model) {
        if (!($model instanceof ElementType)) {
            throw new InvalidArgumentException();
        }

        $this->model = $model;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            'type' => $this->model->get_name(),
            'title' => $this->model->get_title(),
            'description' => $this->model->get_description(),
            'icon' => $this->model->get_icon(),
            'order' => $this->model->get_order()
        ];
    }
}
