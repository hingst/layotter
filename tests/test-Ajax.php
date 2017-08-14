<?php

use \Layotter\Ajax\Elements;
use \Layotter\Components\Editable;
use \Layotter\Structures\FormMeta;

class AjaxTest extends WP_UnitTestCase {

    private static $test_element;

    public function setUp() {
        parent::setUp();

        self::$test_element = self::factory()->post->create([
            'post_type' => Editable::POST_TYPE_EDITABLE
        ]);
        add_post_meta(self::$test_element, Editable::META_FIELD_EDITABLE_TYPE, 'layotter_example_element');
    }

    function test_EditElement() {
        $data = [
            'layotter_id' => strval(self::$test_element)
        ];
        $element = Elements::edit($data);
        $this->assertTrue($element instanceof FormMeta);
        $this->assertContains(Layotter_Test_Data::EXPECTED_TEXTAREA_FIRST_LINE, $element->get_fields());
    }
}