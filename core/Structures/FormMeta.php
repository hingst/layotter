<?php

namespace Layotter\Structures;

use Layotter\Errors;

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
    private $fields = '';

    /**
     * @var int ID of the item that is being edited
     */
    private $editable_id = 0;

    /**
     * Constructor.
     *
     * @param $title string Human readable form title
     * @param $icon string Icon name from the Font Awesome set
     * @param $nonce string Nonce for ACF form validation
     * @param $fields string ACF form HTML
     */
    public function __construct($title, $icon, $nonce, $fields, $editable_id) {
        if (is_string($title)) {
            $this->title = $title;
        } else {
            Errors::invalid_argument_recoverable('title');
        }

        if (is_string($icon)) {
            $this->icon = $icon;
        } else {
            Errors::invalid_argument_recoverable('icon');
        }

        if (is_string($nonce)) {
            $this->nonce = $nonce;
        } else {
            Errors::invalid_argument_recoverable('nonce');
        }

        if (is_string($fields)) {
            $this->fields = $fields;
        } else {
            Errors::invalid_argument_recoverable('fields');
        }

        if (is_int($editable_id)) {
            $this->editable_id = $editable_id;
        } else {
            Errors::invalid_argument_recoverable('editable_id');
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
            'fields' => $this->fields,
            'editableID' => $this->editable_id // ACF needs this
        ];
    }

    /**
     * Fields getter
     *
     * @return string ACF form HTML
     */
    public function get_fields() {
        return $this->fields;
    }
}
