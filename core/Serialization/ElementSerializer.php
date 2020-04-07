<?php

namespace Layotter\Serialization;

use Exception;
use InvalidArgumentException;
use JsonSerializable;
use Layotter\Models\Element;
use Layotter\Repositories\ElementRepository;
use Layotter\Services\ElementFieldsService;

class ElementSerializer implements JsonSerializable {

    /**
     * @var Element
     */
    private $model;

    /**
     * @param Element $model
     */
    public function __construct($model) {
        if (!($model instanceof Element)) {
            throw new InvalidArgumentException();
        }

        $this->model = $model;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function jsonSerialize() {
        $is_template = false;
        $view = '';

        if ($this->model->get_id() !== 0) {
            $element = ElementRepository::load($this->model->get_id(), $this->model->get_options()->get_id());
            $view = $element->get_type()->render_backend_view(ElementFieldsService::get_values($element));
            $is_template = ElementRepository::is_template($element);
        }

        return [
            'id' => $this->model->get_id(),
            'options_id' => $this->model->get_options()->get_id(),
            'view' => $view,
            'is_template' => $is_template
        ];
    }
}
