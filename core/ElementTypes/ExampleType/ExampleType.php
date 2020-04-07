<?php

namespace Layotter\ElementTypes\ExampleType;

use Layotter\Acf\Adapter;
use Layotter\ElementTypes\BaseElementType;

class ExampleType extends BaseElementType {

    protected function attributes() {
        $this->title = __('Text editor', 'layotter');
        $this->description = __('An intuitive editor for text and pictures.', 'layotter');
        $this->icon = 'star';
        $this->field_group = Adapter::get_example_field_group_key();
    }

    /**
     * @param array $fields
     */
    protected function frontend_view($fields) {
        echo '<div class="layotter-example-element">';
        echo $fields['content'];
        echo '</div>';
    }

    /**
     * @param array $fields
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
