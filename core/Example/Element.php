<?php

namespace Layotter\Example;

use Layotter\Acf\Adapter;

/**
 * This example element comes with Layotter and can be disabled in settings
 */
class Element extends \Layotter\Components\Element {

    /**
     * Set attributes
     */
    protected function attributes() {
        $this->title = __('Text editor', 'layotter');
        $this->description = __('An intuitive editor for text and pictures.', 'layotter');
        $this->icon = 'star';
        $this->field_group = Adapter::get_example_field_group_name();
    }

    /**
     * Output frontend view
     *
     * @param array $fields Field values
     */
    protected function frontend_view($fields) {
        echo '<div class="layotter-example-element">';
        echo $fields['content'];
        echo '</div>';
    }

    /**
     * Output backend view
     *
     * @param array $fields Field values
     */
    protected function backend_view($fields) {
        echo '<div class="layotter-example-element">';

        if (empty($fields['content'])) {
            echo '<center>';
            _e('This element is empty. Click the edit button at the top right to add some content.', 'layotter');
            echo '</center>';
        } else {
            echo $fields['content'];
        }

        echo '</div>';
    }
}
