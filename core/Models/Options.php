<?php

namespace Layotter\Models;

use InvalidArgumentException;

class Options {

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null Used to determine which options fields should be visible
     */
    private $post_type_context = null;

    /**
     * @param string $type Options type (post, row, col, element)
     * @param int $id
     */
    public function __construct($type, $id) {
        if (!is_string($type) || !is_int($id)) {
            throw new InvalidArgumentException();
        }

        $this->type = $type;
        $this->id = $id;
    }

    /**
     * @param int $id
     */
    public function set_id($id) {
        if (!is_int($id)) {
            throw new InvalidArgumentException();
        }

        $this->id = $id;
    }

    /**
     * @param string $post_type
     */
    public function set_post_type_context($post_type) {
        if (!is_string($post_type)) {
            throw new InvalidArgumentException();
        }

        $this->post_type_context = $post_type;
    }

    /**
     * @return int
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * @return string
     */
    public function get_post_type_context() {
        return $this->post_type_context;
    }
}
