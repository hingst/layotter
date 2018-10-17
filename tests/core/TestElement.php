<?php

namespace Layotter\Tests;

/**
 * Test element using all field types available in ACF Pro
 */
class TestElement extends \Layotter_Element {

    public static function register() {
        \Layotter::register_element('layotter_functional_test_element', __CLASS__);
    }

    protected function attributes() {
        $this->title = __('All Fields', 'layotter');
        $this->description = __('For functional testing.', 'layotter');
        $this->icon = 'star';
        $this->field_group = 'group_all_fields';
    }

    protected function frontend_view($fields) {
    }

    protected function backend_view($fields) {
    }
}