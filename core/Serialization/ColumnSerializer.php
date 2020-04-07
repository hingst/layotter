<?php

namespace Layotter\Serialization;

use InvalidArgumentException;
use JsonSerializable;
use Layotter\Models\Column;

class ColumnSerializer implements JsonSerializable {

    /**
     * @var Column
     */
    private $model;

    /**
     * @param Column $model
     */
    public function __construct($model) {
        if (!($model instanceof Column)) {
            throw new InvalidArgumentException();
        }

        $this->model = $model;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            'options_id' => $this->model->get_options()->get_id(),
            'elements' => array_map(function($model) {
                return new ElementSerializer($model);
            }, $this->model->get_elements())
        ];
    }
}
