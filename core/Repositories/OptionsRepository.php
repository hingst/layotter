<?php


namespace Layotter\Repositories;

use InvalidArgumentException;
use Layotter\Initializer;
use Layotter\Models\Options;

class OptionsRepository {

    /**
     * @param string $type Type (post, row, col, element)
     * @return Options
     */
    public static function create($type) {
        if (!self::type_exists($type)) {
            throw new InvalidArgumentException();
        }

        return new Options($type, 0);
    }

    /**
     * @param string $type
     * @param int $id
     * @return Options
     */
    public static function load($type, $id) {
        if (!self::type_exists($type) || !is_int($id)) {
            throw new InvalidArgumentException();
        }

        return new Options($type, $id);
    }

    /**
     * Triggers ACF handlers that save custom fields from POST to the database
     *
     * @param Options $options
     */
    public static function save_from_post_data($options) {
        if (!($options instanceof Options)) {
            throw new InvalidArgumentException();
        }

        $new_id = wp_insert_post([
            'post_type' => Initializer::POST_TYPE_OPTIONS,
            'post_status' => 'publish'
        ]);

        $options->set_id($new_id);
    }

    /**
     * @param $type string
     * @return bool
     */
    public static function type_exists($type) {
        return (is_string($type) && in_array($type, ['post', 'row', 'col', 'element']));
    }
}