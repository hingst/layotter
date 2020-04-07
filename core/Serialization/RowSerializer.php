<?php

namespace Layotter\Serialization;

use InvalidArgumentException;
use JsonSerializable;
use Layotter\Models\Row;

class RowSerializer implements JsonSerializable {

    /**
     * @var Row
     */
    private $model;

    /**
     * @param Row $model
     */
    public function __construct($model) {
        if (!($model instanceof Row)) {
            throw new InvalidArgumentException();
        }

        $this->model = $model;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            'layout' => $this->model->get_layout(),
            'options_id' => $this->model->get_options()->get_id(),
            'cols' => array_map(function($model) {
                return new ColumnSerializer($model);
            }, $this->model->get_columns())
        ];
    }
}
