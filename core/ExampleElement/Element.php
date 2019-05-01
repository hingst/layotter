<?php

namespace Layotter\ExampleElement;

use Layotter\Acf\Adapter;

/**
 * Represents Layotter's example element that contains only a WYSIWYG field and can be disabled in settings.
 */
class Element extends \Layotter\Components\Element {

    /**
     * Sets element attributes.
     */
    protected function attributes() {
        $this->title = __('Text editor', 'layotter');
        $this->description = __('An intuitive editor for text and pictures.', 'layotter');
        $this->icon = 'star';
        $this->field_group = Adapter::get_example_field_group_key();
    }

    /**
     * Prints the frontend view.
     *
     * @param array $fields Field values.
     */
    protected function frontend_view($fields) {
        echo '<div class="layotter-example-element">';
        echo $fields['content'];
        echo '</div>';
    }

    /**
     * Prints the backend view.
     *
     * @param array $fields Field values.
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
