<?php

use Layotter\Ajax\Elements;
use Layotter\Core;
use Layotter\Structures\FormMeta;
use Layotter\Tests\BaseTest;
use Layotter\Tests\TestData;

/**
 * @group unit
 */
class AjaxTest extends BaseTest {

    private static $test_element;

    public function setUp() {
        parent::setUp();

        self::$test_element = self::factory()->post->create([
            'post_type' => Core::POST_TYPE_EDITABLE
        ]);
        add_post_meta(self::$test_element, Core::META_FIELD_EDITABLE_TYPE, 'layotter_example_element');
    }

    public function test_EditElement() {
        $data = [
            'layotter_id' => strval(self::$test_element)
        ];
        $element = Elements::edit($data);
        $this->assertTrue($element instanceof FormMeta);
        $this->assertContains(TestData::EXPECTED_TEXTAREA_FIRST_LINE, $element->get_fields());
    }
}