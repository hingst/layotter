<?php

use \Layotter\Acf\Adapter;

class AcfAdapterTest extends WP_UnitTestCase {

    function test_AcfIsAvailable() {
        $this->assertTrue(Adapter::is_available());
    }

    function test_DefaultFieldGroupIsAvailable() {
        $field_groups = Adapter::get_all_field_groups();
        $this->assertEquals(2, count($field_groups)); // example element + unit test helper group
    }

    function test_FieldGroupFilter() {
        $expect_zero = Adapter::get_filtered_field_groups(array(
            'post_type' => 'page',
            'layotter' => 'dummy'
        ));
        $expect_two = Adapter::get_filtered_field_groups(array(
            'post_type' => 'page',
            'layotter' => 'element'
        ));
        $this->assertEquals(0, count($expect_zero));
        $this->assertEquals(2, count($expect_two)); // example element + unit test helper group
    }

    function test_FieldGroupVisible() {
        $field_group = Adapter::get_field_group_by_key('group_test');
        $post_id = self::factory()->post->create(array(
            'post_type' => 'page'
        ));
        $expect_true = \Layotter\Acf\Adapter::is_field_group_visible($field_group, array(
            'post_id' => $post_id,
            'post_type' => get_post_type($post_id),
            'layotter' => 'element'
        ));
        $this->assertTrue($expect_true);
    }

    function test_FieldGroupNotVisible() {
        $field_group = Adapter::get_field_group_by_key('group_test');
        $post_id = self::factory()->post->create(array(
            'post_type' => 'post'
        ));
        $expect_false = \Layotter\Acf\Adapter::is_field_group_visible($field_group, array(
            'post_id' => $post_id,
            'post_type' => get_post_type($post_id),
            'layotter' => 'element'
        ));
        $this->assertFalse($expect_false);
    }

    function test_GetFieldsForGroup() {
        $field_group = Adapter::get_field_group_by_key('group_test');
        $fields = Adapter::get_fields($field_group);
        $this->assertNotEmpty($fields);
        $this->assertArrayHasKey('key', $fields[0]);
        $this->assertEquals('field_test', $fields[0]['key']);
    }

    function test_GenerateFormForNewElement() {
        $field_group = Adapter::get_field_group_by_key('group_test');
        $fields = Adapter::get_fields($field_group);
        $html = Adapter::get_form_html($fields);
        $this->assertContains('field_test', $html);
    }

    function test_GenerateFormForExistingElement() {
        $post_id = self::factory()->post->create();
        update_field('field_test', 'blah blah blah', $post_id);
        $field_group = Adapter::get_field_group_by_key('group_test');
        $fields = Adapter::get_fields($field_group);
        $html = Adapter::get_form_html($fields, $post_id);
        $this->assertContains('blah blah blah', $html);
    }

    function test_GetFieldGroupById() {
        // TODO
    }

    function test_GetFieldGroupByKey() {
        $group = Adapter::get_field_group_by_key('group_test');
        $this->assertNotEmpty($group);
    }
}