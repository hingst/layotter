<?php

namespace Layotter\Ajax;

use InvalidArgumentException;
use JsonSerializable;
use Layotter\Models\Options;
use Layotter\Repositories\OptionsRepository;
use Layotter\Serialization\FormSerializer;
use Layotter\Services\OptionsFieldsService;

class OptionsHandler {

    /**
     * @param array $data
     * @return JsonSerializable
     */
    public static function edit($data = null) {
        $data = is_array($data) ? $data : $_POST;
        $post_type_context = null;

        if (isset($data['layotter_post_id']) && RequestManager::is_valid_id($data['layotter_post_id'])) {
            $post_type_context = get_post_type($data['layotter_post_id']);
        }

        $options = self::get_options($data);
        $options->set_post_type_context($post_type_context);
        $form = OptionsFieldsService::get_form($options);
        return new FormSerializer($form);
    }

    /**
     * @param array $data
     * @return int
     */
    public static function save($data = null) {
        $data = is_array($data) ? $data : $_POST;
        $options = self::get_options($data);
        OptionsRepository::save_from_post_data($options);

        // TODO: why just the ID and not the whole object?

        return $options->get_id();
    }

    /**
     * @param array $data
     * @return Options
     */
    private static function get_options($data) {
        if (!isset($data['layotter_type']) || !is_string($data['layotter_type'])) {
            throw new InvalidArgumentException();
        }

        if (isset($data['layotter_options_id']) && RequestManager::is_valid_id($data['layotter_options_id'])) {
            return OptionsRepository::load($data['layotter_type'], intval($data['layotter_options_id']));
        } else {
            return OptionsRepository::create($data['layotter_type']);
        }
    }
}
