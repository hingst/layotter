<?php

namespace Layotter\Structures;

use Layotter\Errors;

/**
 * Used to pass around element type meta data in a predictable structure
 */
class ElementTypeMeta implements \JsonSerializable {

    /**
     * @var string Element type identifier
     */
    private $type = '';

    /**
     * @var string Human readable element type title
     */
    private $title = '';

    /**
     * @var string Human readable element type description
     */
    private $description = '';

    /**
     * @var string Icon name from the Font Awesome set
     */
    private $icon = '';

    /**
     * @var int Ordering number relative to other element types
     */
    private $order = 0;

    /**
     * Constructor.
     *
     * @param $type
     * @param $title
     * @param $description
     * @param $icon
     * @param $order
     */
    public function __construct($type, $title, $description, $icon, $order) {
        if (is_string($type)) {
            $this->type = $type;
        } else {
            Errors::invalid_argument_recoverable('type');
        }

        if (is_string($title)) {
            $this->title = $title;
        } else {
            Errors::invalid_argument_recoverable('title');
        }

        if (is_string($description)) {
            $this->description = $description;
        } else {
            Errors::invalid_argument_recoverable('description');
        }

        if (is_string($icon)) {
            $this->icon = $icon;
        } else {
            Errors::invalid_argument_recoverable('icon');
        }

        if (is_int($order)) {
            $this->order = $order;
        } else {
            Errors::invalid_argument_recoverable('order');
        }
    }

    /**
     * Type getter
     *
     * @return string Element type identifier
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * Title getter
     *
     * @return string Human readable element type title
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Order getter
     *
     * @return int Ordering number relative to other element types
     */
    public function get_order() {
        return $this->order;
    }

    /**
     * Return array representation for use in json_encode()
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'order' => $this->order
        ];
    }
}
