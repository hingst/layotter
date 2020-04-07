<?php

namespace Layotter\Ajax;

use Exception;
use InvalidArgumentException;
use JsonSerializable;
use Layotter\Repositories\LayoutRepository;
use Layotter\Repositories\PostRepository;
use Layotter\Serialization\LayoutSerializer;

class LayoutsHandler {

    /**
     * @param array $data
     * @return JsonSerializable
     * @throws Exception
     */
    public static function create($data = null) {
        $data = is_array($data) ? $data : $_POST;

        if (!isset($data['layotter_post_type'], $data['layotter_name'], $data['layotter_json']) || !is_string($data['layotter_post_type']) || !is_string($data['layotter_name']) || !is_string($data['layotter_json'])) {
            throw new InvalidArgumentException();
        }

        $post = PostRepository::create(stripslashes($data['layotter_json']));
        $layout = LayoutRepository::save($post, $data['layotter_post_type'], $data['layotter_name']);
        return new LayoutSerializer($layout);
    }

    /**
     * @param array $data
     * @return JsonSerializable
     * @throws Exception
     */
    public static function load($data = null) {
        $data = is_array($data) ? $data : $_POST;

        if (!isset($data['layotter_id']) || !RequestManager::is_valid_id($data['layotter_id'])) {
            throw new InvalidArgumentException();
        }

        $layout = LayoutRepository::load(intval($data['layotter_id']));
        return new LayoutSerializer($layout);
    }

    /**
     * @param array $data
     * @return JsonSerializable
     * @throws Exception
     */
    public static function rename($data = null) {
        $data = is_array($data) ? $data : $_POST;

        if (!isset($data['layotter_id'], $data['layotter_name']) || !RequestManager::is_valid_id($data['layotter_id']) || !is_string($data['layotter_name'])) {
            throw new InvalidArgumentException();
        }

        $layout = LayoutRepository::rename(intval($data['layotter_id']), $data['layotter_name']);
        return new LayoutSerializer($layout);
    }

    /**
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public static function delete($data = null) {
        $data = is_array($data) ? $data : $_POST;

        if (!isset($data['layotter_id']) || !RequestManager::is_valid_id($data['layotter_id'])) {
            throw new InvalidArgumentException();
        }

        return LayoutRepository::delete(intval($data['layotter_id']));
    }
}
