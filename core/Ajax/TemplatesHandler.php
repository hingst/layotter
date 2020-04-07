<?php

namespace Layotter\Ajax;

use Exception;
use InvalidArgumentException;
use JsonSerializable;
use Layotter\Repositories\ElementRepository;
use Layotter\Serialization\ElementSerializer;

class TemplatesHandler {

    /**
     * @param array $data
     * @return JsonSerializable
     * @throws Exception
     */
    public static function create($data = null) {
        $data = is_array($data) ? $data : $_POST;

        if (!isset($data['layotter_id']) || !RequestManager::is_valid_id($data['layotter_id'])) {
            throw new InvalidArgumentException();
        }

        $element = ElementRepository::load(intval($data['layotter_id']), 0);
        ElementRepository::promote_element($element);
        return new ElementSerializer($element);
    }

    /**
     * @param array $data
     * @return JsonSerializable
     * @throws Exception
     */
    public static function delete($data = null) {
        $data = is_array($data) ? $data : $_POST;

        if (!isset($data['layotter_id']) || !RequestManager::is_valid_id($data['layotter_id'])) {
            throw new InvalidArgumentException();
        }

        $element = ElementRepository::load(intval($data['layotter_id']), 0);
        ElementRepository::demote_element($element);
        return new ElementSerializer($element);
    }
}
