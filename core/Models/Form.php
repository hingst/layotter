<?php

namespace Layotter\Models;

use InvalidArgumentException;

class Form {

    /**
     * @var int
     */
    private $item_id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $html;

    /**
     * @var string
     */
    private $nonce;

    /**
     * @param int $item_id
     * @param string $title
     * @param string $icon
     * @param string $html
     * @param string $nonce
     */
    public function __construct($item_id, $title, $icon, $html, $nonce) {
        if (!is_int($item_id) || !is_string($title) || !is_string($icon) || !is_string($html) || !is_string($nonce)) {
            throw new InvalidArgumentException();
        }

        $this->item_id = $item_id;
        $this->title = $title;
        $this->icon = $icon;
        $this->html = $html;
        $this->nonce = $nonce;
    }

    /**
     * @return string
     */
    public function get_item_id() {
        return $this->item_id;
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
    public function get_icon() {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function get_nonce() {
        return $this->nonce;
    }

    /**
     * @return string
     */
    public function get_html() {
        return $this->html;
    }
}
