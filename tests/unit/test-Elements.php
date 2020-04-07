<?php

use Layotter\Repositories\ElementRepository;
use Layotter\Initializer;
use Layotter\Repositories\ElementTypeRepository;
use Layotter\Services\ElementFieldsService;

/**
 * @group unit
 */
class ElementsTest extends \WP_UnitTestCase {

    public function test_Fields() {
        $element = ElementRepository::create('layotter_example_element');
        $fields = ElementFieldsService::get_fields($element);
        $this->assertEquals('wysiwyg', $fields[0]['type']);
    }

    public function test_EnabledForPost() {
        $id = self::factory()->post->create([
            'post_type' => 'page'
        ]);
        $element = ElementRepository::create('layotter_example_element');
        $this->assertTrue(ElementTypeRepository::is_allowed_for_post($element->get_type(), $id));
    }

    public function test_SetTemplate() {
        $element = ElementRepository::create('layotter_example_element');
        ElementRepository::save_from_post_data($element);
        $this->assertFalse(ElementRepository::is_template($element));

        ElementRepository::promote_element($element);
        $this->assertTrue(ElementRepository::is_template($element));
        $this->assertTrue((bool) get_post_meta($element->get_id(), Initializer::META_FIELD_IS_TEMPLATE, true));

        ElementRepository::demote_element($element);
        $this->assertFalse(ElementRepository::is_template($element));
        $this->assertFalse((bool) get_post_meta($element->get_id(), Initializer::META_FIELD_IS_TEMPLATE, true));
    }
}