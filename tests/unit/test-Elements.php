<?php

use Layotter\Core;

/**
 * @group unit
 */
class ElementsTest extends WP_UnitTestCase {

    public function test_Fields() {
        $element = Core::assemble_new_element('layotter_example_element');
        $fields = $element->get_fields();
        $this->assertEquals('wysiwyg', $fields[0]['type']);
    }

    public function test_EnabledForPost() {
        $id = self::factory()->post->create([
            'post_type' => 'page'
        ]);
        $element = Core::assemble_new_element('layotter_example_element');
        $this->assertTrue($element->is_enabled_for($id));
    }

    public function test_SetTemplate() {
        $element = Core::assemble_new_element('layotter_example_element');
        $element->save_from_post_data();
        $this->assertFalse($element->is_template());

        $element->set_template(true);
        $this->assertTrue($element->is_template());
        $this->assertTrue((bool) get_post_meta($element->get_id(), Core::META_FIELD_IS_TEMPLATE, true));

        $element->set_template(false);
        $this->assertFalse($element->is_template());
        $this->assertFalse((bool) get_post_meta($element->get_id(), Core::META_FIELD_IS_TEMPLATE, true));
    }
}