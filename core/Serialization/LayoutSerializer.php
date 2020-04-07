<?php

namespace Layotter\Serialization;

use InvalidArgumentException;
use JsonSerializable;
use Layotter\Models\Layout;

class LayoutSerializer implements JsonSerializable {

    /**
     * @var Layout
     */
    private $model;

    /**
     * @param Layout $model
     */
    public function __construct($model) {
        if (!($model instanceof Layout)) {
            throw new InvalidArgumentException();
        }

        $this->model = $model;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            'layout_id' => $this->model->get_id(),
            'name' => $this->model->get_name(),
            'json' => new PostSerializer($this->model->get_post()),
            'time_created' => $this->model->get_time_created()
        ];
    }
}
