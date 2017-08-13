<?php

use \Layotter\Components\Editable;

class AjaxTest extends WP_UnitTestCase {

    private static $test_element;

    public function setUp() {
        parent::setUp();

        self::$test_element = self::factory()->post->create([
            'post_content' => '',
            'post_type' => Editable::POST_TYPE_EDITABLE
        ]);
        add_post_meta(self::$test_element, Editable::META_FIELD_EDITABLE_TYPE, 'layotter_example_element');
    }

    function test_EditElement() {
        $data = [
            'layotter_element_id' => strval(self::$test_element)
        ];
        $element = \Layotter\Ajax\Elements::edit($data);
        $this->assertArrayHasKey('title', $element);
        $this->assertArrayHasKey('icon', $element);
        $this->assertArrayHasKey('nonce', $element);
        $this->assertArrayHasKey('fields', $element);
        $this->assertContains('<textarea id="wysiwyg-acf-field-content-', $element['fields']);
    }
}