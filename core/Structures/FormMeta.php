<?php

namespace Layotter\Structures;

/**
 * Used to pass around form meta data in a predictable structure
 */
class FormMeta implements \JsonSerializable {

    /**
     * @var string Human readable form title
     */
    private $title = '';

    /**
     * @var string Icon name from the Font Awesome set
     */
    private $icon = '';

    /**
     * @var string Nonce for ACF form validation
     */
    private $nonce = '';

    /**
     * @var string ACF form HTML
     */
    private $fields = [];

    /**
     * Constructor.
     *
     * @param $title string Human readable form title
     * @param $icon string Icon name from the Font Awesome set
     * @param $nonce string Nonce for ACF form validation
     * @param $fields string ACF form HTML
     */
    public function __construct($title, $icon, $nonce, $fields) {
        if (is_string($title)) {
            $this->title = $title;
        }

        if (is_string($icon)) {
            $this->icon = $icon;
        }

        if (is_string($nonce)) {
            $this->nonce = $nonce;
        }

        if (is_string($fields)) {
            $this->fields = $fields;
        }
    }

    /**
     * Return array representation for use in json_encode()
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'title' => $this->title,
            'icon' => $this->icon,
            'nonce' => $this->nonce,
            'fields' => $this->fields
        ];
    }
}
