<?php

namespace Layotter\Serialization;

use InvalidArgumentException;
use JsonSerializable;
use Layotter\Models\Post;

class PostSerializer implements JsonSerializable {

    /**
     * @var Post
     */
    private $model;

    /**
     * @param Post $model
     */
    public function __construct($model) {
        if (!($model instanceof Post)) {
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
            'rows' => array_map(function($model) {
                return new RowSerializer($model);
            }, $this->model->get_rows())
        ];
    }
}
