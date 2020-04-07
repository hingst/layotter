<?php

namespace Layotter\Repositories;

use Exception;
use InvalidArgumentException;
use Layotter\Initializer;
use Layotter\Models\Element;

class ElementRepository {

    /**
     * @param string $type Element type name
     * @return Element
     * @throws Exception
     */
    public static function create($type) {
        if (!is_string($type)) {
            throw new InvalidArgumentException();
        }

        $type = ElementTypeRepository::get($type);
        $options = OptionsRepository::create('element');
        return new Element($type, 0, $options);
    }

    /**
     * @param int $id
     * @param int $options_id
     * @return Element
     * @throws Exception
     */
    public static function load($id, $options_id) {
        if (!is_int($id) || !is_int($options_id)) {
            throw new InvalidArgumentException();
        }

        $type = ElementTypeRepository::get(self::get_type_name_by_id($id));
        $options = OptionsRepository::load('element', $options_id);
        return new Element($type, $id, $options);
    }

    /**
     * Triggers ACF handlers that save custom fields from POST to the database
     *
     * @param Element $element
     */
    public static function save_from_post_data($element) {
        if (!($element instanceof Element)) {
            throw new InvalidArgumentException();
        }

        if (self::is_template($element)) {
            wp_update_post([
                'ID' => $element->get_id()
            ]);
        } else {
            $new_id = wp_insert_post([
                'post_type' => Initializer::POST_TYPE_ELEMENT,
                'meta_input' => [
                    Initializer::META_FIELD_ELEMENT_TYPE => $element->get_type()->get_model()->get_name()
                ],
                'post_status' => 'publish'
            ]);

            $element->set_id($new_id);
        }
    }

    /**
     * @param int $id
     * @return string
     */
    private static function get_type_name_by_id($id) {
        if (!is_int($id)) {
            throw new InvalidArgumentException();
        }

        $type = get_post_meta($id, Initializer::META_FIELD_ELEMENT_TYPE, true);

        if (!ElementTypeRepository::type_name_exists($type)) {
            throw new InvalidArgumentException();
        }

        return $type;
    }

    /**
     * @param Element $element
     * @return bool
     */
    public static function is_template($element) {
        if (!($element instanceof Element)) {
            throw new InvalidArgumentException();
        }

        return (bool)get_post_meta($element->get_id(), Initializer::META_FIELD_IS_TEMPLATE, true);
    }

    /**
     * Turns an existing element into a template.
     *
     * @param Element $element
     */
    public static function promote_element($element) {
        if (!($element instanceof Element)) {
            throw new InvalidArgumentException();
        }

        update_post_meta($element->get_id(), Initializer::META_FIELD_IS_TEMPLATE, true);
    }

    /**
     * Turns an existing template back into a regular element.
     *
     * @param Element $element
     */
    public static function demote_element($element) {
        if (!($element instanceof Element)) {
            throw new InvalidArgumentException();
        }

        update_post_meta($element->get_id(), Initializer::META_FIELD_IS_TEMPLATE, false);
    }

    /**
     * @param $id
     * @return Element[]
     * @throws Exception
     */
    public static function get_available_templates_for_post($id) {
        if (!is_int($id)) {
            throw new InvalidArgumentException();
        }

        $template_posts = get_posts([
            'post_type' => Initializer::POST_TYPE_ELEMENT,
            'meta_key' => Initializer::META_FIELD_IS_TEMPLATE,
            'meta_value' => '1',
            'order' => 'ASC',
            'posts_per_page' => -1
        ]);

        $templates = [];

        foreach ($template_posts as $template) {
            $element = ElementRepository::load($template->ID, 0);

            if (ElementTypeRepository::is_allowed_for_post($element->get_type(), $id)) {
                $templates[] = $element;
            }
        }

        return $templates;
    }
}