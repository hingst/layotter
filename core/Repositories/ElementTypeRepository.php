<?php

namespace Layotter\Repositories;

use Exception;
use InvalidArgumentException;
use Layotter\Acf\Adapter;
use Layotter\ElementTypes\BaseElementType;
use Layotter\Models\ElementType;

class ElementTypeRepository {

    /**
     * @var BaseElementType[]
     */
    protected static $types;

    /**
     * @param string $type_name Unique type name
     * @param string $class_name Class name, must extend ElementType
     */
    public static function register($type_name, $class_name) {
        if (!is_string($type_name) || !is_string($class_name)) {
            throw new InvalidArgumentException();
        }

        if (!class_exists($class_name) || !is_subclass_of($class_name, BaseElementType::class)) {
            throw new InvalidArgumentException();
        }

        // We *should* fail if the provided type identifier is invalid, but in older versions the identifier was simply
        // stripped of invalid characters, so we have to keep that behavior to keep old types working.
        $type_name = self::clean_type_name($type_name);
        if (empty($type_name) || isset(self::$types[ $type_name ])) {
            throw new InvalidArgumentException();
        }

        self::$types[ $type_name ] = new $class_name($type_name);
        self::$types[ $type_name ]::register_backend_hooks();
    }

    /**
     * @param string $type_name
     * @param string $class
     * @deprecated Kept for backwards compatibility, use register() instead.
     */
    public static function register_element($type_name, $class) {
        self::register($type_name, $class);
    }

    /**
     * @param string $type_name
     * @return mixed
     */
    public static function get($type_name) {
        if (!self::type_name_exists($type_name)) {
            throw new InvalidArgumentException();
        }

        return self::$types[ $type_name ];
    }

    /**
     * @param int $id
     * @return ElementType[]
     * @throws Exception
     */
    public static function get_allowed_for_post($id) {
        if (!is_int($id)) {
            throw new InvalidArgumentException();
        }

        $types = [];

        foreach (self::$types as $type) {
            if (ElementTypeRepository::is_allowed_for_post($type, $id)) {
                $types[] = $type->get_model();
            }
        }

        usort($types, [__CLASS__, 'sort_element_types']);

        return $types;
    }

    /**
     * @param BaseElementType $type
     * @param int $id
     * @return bool
     */
    public static function is_allowed_for_post($type, $id) {
        if (!is_int($id)) {
            return false;
        }

        $post_type = get_post_type($id);
        $field_group = Adapter::get_field_group($type->get_field_group());

        return Adapter::is_field_group_visible($field_group, [
            'post_id' => $id,
            'post_type' => $post_type,
            'layotter' => 'element'
        ]);
    }

    /**
     * Sorts using the order property, then alphabetically.
     *
     * @param ElementType $a_meta
     * @param ElementType $b_meta
     * @return int -1 if A comes first, 1 if B comes first, 0 if equal
     */
    public static function sort_element_types($a_meta, $b_meta) {
        $a_order = $a_meta->get_order();
        $b_order = $b_meta->get_order();
        $a_title = $a_meta->get_title();
        $b_title = $b_meta->get_title();

        if ($a_order < $b_order) {
            return -1;
        } else if ($a_order > $b_order) {
            return 1;
        } else {
            return strcasecmp($a_title, $b_title);
        }
    }

    /**
     * @param string $type_name
     * @return bool
     */
    public static function type_name_exists($type_name) {
        return is_string($type_name) && isset(self::$types[ $type_name ]);
    }

    /**
     * @param string $type_name
     * @return string
     */
    private static function clean_type_name($type_name) {
        if (!is_string($type_name)) {
            return '';
        }

        return preg_replace('/[^a-z_]/', '', $type_name);
    }
}