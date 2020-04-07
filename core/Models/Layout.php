<?php

namespace Layotter\Models;

use InvalidArgumentException;

class Layout {

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Post
     */
    private $post;

    /**
     * @var int
     */
    private $time_created;

    /**
     * @param int $id
     * @param string $name
     * @param Post $post
     * @param int $time_created UNIX timestamp
     */
    public function __construct($id, $name, $post, $time_created) {
        if (!is_int($id) || !is_string($name) || !($post instanceof Post) || !is_int($time_created)) {
            throw new InvalidArgumentException();
        }

        $this->id = $id;
        $this->name = $name;
        $this->post = $post;
        $this->time_created = $time_created;
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
    public function get_name() {
        return $this->name;
    }

    /**
     * @return Post
     */
    public function get_post() {
        return $this->post;
    }

    /**
     * @return int
     */
    public function get_time_created() {
        return $this->time_created;
    }
}
