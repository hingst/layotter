<?php

namespace Layotter\Models;

use InvalidArgumentException;

class ElementType {

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var int
     */
    private $order;

    /**
     * @param string $name
     * @param string $title
     * @param string $description
     * @param string $icon
     * @param int $order
     */
    public function __construct($name, $title, $description, $icon, $order) {
        if (!is_string($name) || !is_string($title) || !is_string($description) || !is_string($icon) || !is_int($order)) {
            throw new InvalidArgumentException();
        }

        $this->name = $name;
        $this->title = $title;
        $this->description = $description;
        $this->icon = $icon;
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * @return string
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * @return string
     */
    public function get_icon() {
        return $this->icon;
    }

    /**
     * @return int
     */
    public function get_order() {
        return $this->order;
    }
}
