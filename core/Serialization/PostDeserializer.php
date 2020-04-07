<?php

namespace Layotter\Serialization;

use Exception;
use InvalidArgumentException;
use Layotter\Models\Column;
use Layotter\Models\Element;
use Layotter\Models\Post;
use Layotter\Models\Row;
use Layotter\Repositories\ElementRepository;
use Layotter\Repositories\OptionsRepository;

class PostDeserializer {

    /**
     * @var Post
     */
    private $model;

    /**
     * @param string $json
     * @throws Exception
     */
    public function __construct($json) {
        if (!is_string($json)) {
            throw new InvalidArgumentException();
        }

        $structure = json_decode($json, true);
        $this->model = $this->deserialize_post($structure);
    }

    /**
     * @return Post
     */
    public function get_model() {
        return $this->model;
    }

    /**
     * @param array $structure
     * @return Post
     * @throws Exception
     */
    private function deserialize_post($structure) {
        $options_id = 0;
        $rows = [];

        if (is_array($structure)) {
            if (isset($structure['options_id']) && is_int($structure['options_id'])) {
                $options_id = $structure['options_id'];
            }

            if (isset($structure['rows']) && is_array($structure['rows'])) {
                foreach ($structure['rows'] as $row_structure) {
                    $rows[] = $this->deserialize_row($row_structure);
                }
            }
        }

        $options = OptionsRepository::load('post', $options_id);
        return new Post($options, $rows);
    }

    /**
     * @param array $structure
     * @return Row
     * @throws Exception
     */
    private function deserialize_row($structure) {
        $layout = '';
        $options_id = 0;
        $columns = [];

        if (is_array($structure)) {
            if (isset($structure['layout']) && is_string($structure['layout'])) {
                $layout = $structure['layout'];
            }

            if (isset($structure['options_id']) && is_int($structure['options_id'])) {
                $options_id = $structure['options_id'];
            }

            $layout_array = explode(' ', $layout);
            if (isset($structure['cols']) && is_array($structure['cols'])) {
                foreach ($structure['cols'] as $i => $col_structure) {
                    $col_structure['width'] = isset($layout_array[ $i ]) ? $layout_array[ $i ] : '';
                    $columns[] = $this->deserialize_column($col_structure);
                }
            }
        }

        $options = OptionsRepository::load('row', $options_id);
        return new Row($layout, $options, $columns);
    }

    /**
     * @param array $structure
     * @return Column
     * @throws Exception
     */
    private function deserialize_column($structure) {
        $options_id = 0;
        $width = '';
        $elements = [];

        if (is_array($structure)) {
            if (isset($structure['options_id']) && is_int($structure['options_id'])) {
                $options_id = $structure['options_id'];
            }

            if (isset($structure['width']) && is_string($structure['width'])) {
                $width = $structure['width'];
            }

            if (isset($structure['elements']) && is_array($structure['elements'])) {
                foreach ($structure['elements'] as $element_structure) {
                    $elements[] = $this->deserialize_element($element_structure);
                }
            }
        }

        $options = OptionsRepository::load('col', $options_id);
        return new Column($width, $options, $elements);
    }

    /**
     * @param array $structure
     * @return Element
     * @throws Exception
     */
    private function deserialize_element($structure) {
        $id = 0;
        $options_id = 0;

        if (is_array($structure)) {
            if (isset($structure['id']) && is_int($structure['id'])) {
                $id = $structure['id'];
            }

            if (isset($structure['options_id']) && is_int($structure['options_id'])) {
                $options_id = $structure['options_id'];
            }
        }

        return ElementRepository::load($id, $options_id);
    }
}
