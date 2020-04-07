<?php

namespace Layotter\Serialization;

use InvalidArgumentException;
use JsonSerializable;
use Layotter\Models\Form;

class FormSerializer implements JsonSerializable {

    /**
     * @var Form
     */
    private $model;

    /**
     * @param Form $model
     */
    public function __construct($model) {
        if (!($model instanceof Form)) {
            throw new InvalidArgumentException();
        }

        $this->model = $model;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            'editableID' => $this->model->get_item_id(), // ACF needs this
            'title' => $this->model->get_title(),
            'icon' => $this->model->get_icon(),
            'nonce' => $this->model->get_nonce(),
            'fields' => $this->model->get_html()
        ];
    }
}
