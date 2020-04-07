<?php

namespace Layotter\Ajax;

use Exception;
use InvalidArgumentException;
use JsonSerializable;
use Layotter\Models\Element;
use Layotter\Repositories\ElementRepository;
use Layotter\Serialization\ElementSerializer;
use Layotter\Serialization\FormSerializer;
use Layotter\Services\ElementFieldsService;

class ElementsHandler {

    /**
     * @param array $data
     * @return JsonSerializable
     * @throws Exception
     */
    public static function edit($data = null) {
        $element = self::get_element(is_array($data) ? $data : $_POST);
        $form = ElementFieldsService::get_form($element);
        return new FormSerializer($form);
    }

    /**
     * @param array $data
     * @return JsonSerializable
     * @throws Exception
     */
    public static function save($data = null) {
        $element = self::get_element(is_array($data) ? $data : $_POST);
        ElementRepository::save_from_post_data($element);
        return new ElementSerializer($element);
    }

    /**
     * @param $data
     * @return Element
     * @throws Exception
     */
    private static function get_element($data) {
        if (isset($data['layotter_id']) && RequestManager::is_valid_id($data['layotter_id'])) {
            return ElementRepository::load(intval($data['layotter_id']), 0);
        } else if (isset($data['layotter_type']) && is_string($data['layotter_type'])) {
            return ElementRepository::create($data['layotter_type']);
        } else {
            throw new InvalidArgumentException();
        }
    }
}
