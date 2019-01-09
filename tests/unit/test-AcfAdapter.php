<?php

use Layotter\Acf\Adapter;
use Layotter\Tests\BaseTest;

/**
 * @group unit
 */
class AcfAdapterTest extends BaseTest {

    public function test_AcfIsAvailable() {
        $this->assertTrue(Adapter::is_available());
    }

    public function test_DefaultFieldGroupIsAvailable() {
        $field_groups = Adapter::get_all_field_groups();
        $this->assertEquals(4, count($field_groups)); // example element + 3 test helper groups
    }

    public function test_FieldGroupFilter() {
        $expect_zero = Adapter::get_filtered_field_groups([
            'post_type' => 'page',
            'layotter' => 'dummy'
        ]);
        $expect_two = Adapter::get_filtered_field_groups([
            'post_type' => 'page',
            'layotter' => 'element'
        ]);
        $this->assertEquals(0, count($expect_zero));
        $this->assertEquals(3, count($expect_two)); // example element + 2 test helper groups
    }

    public function test_FieldGroupVisible() {
        $field_group = Adapter::get_field_group_by_key('group_test');
        $post_id = self::factory()->post->create([
            'post_type' => 'page'
        ]);
        $expect_true = \Layotter\Acf\Adapter::is_field_group_visible($field_group, [
            'post_id' => $post_id,
            'post_type' => get_post_type($post_id),
            'layotter' => 'element'
        ]);
        $this->assertTrue($expect_true);
    }

    public function test_FieldGroupNotVisible() {
        $field_group = Adapter::get_field_group_by_key('group_test');
        $post_id = self::factory()->post->create([
            'post_type' => 'post'
        ]);
        $expect_false = \Layotter\Acf\Adapter::is_field_group_visible($field_group, [
            'post_id' => $post_id,
            'post_type' => get_post_type($post_id),
            'layotter' => 'element'
        ]);
        $this->assertFalse($expect_false);
    }

    public function test_GetFieldsForGroup() {
        $field_group = Adapter::get_field_group_by_key('group_test');
        $fields = Adapter::get_fields($field_group);
        $this->assertNotEmpty($fields);
        $this->assertArrayHasKey('key', $fields[0]);
        $this->assertEquals('field_test', $fields[0]['key']);
    }

    public function test_GenerateFormForNewElement() {
        $field_group = Adapter::get_field_group_by_key('group_test');
        $fields = Adapter::get_fields($field_group);
        $html = Adapter::get_form_html($fields);
        $this->assertContains('field_test', $html);
    }

    public function test_GenerateFormForExistingElement() {
        $post_id = self::factory()->post->create();
        Adapter::update_field_value('field_test', 'blah blah blah', $post_id);
        $field_group = Adapter::get_field_group_by_key('group_test');
        $fields = Adapter::get_fields($field_group);
        $html = Adapter::get_form_html($fields, $post_id);
        $this->assertContains('blah blah blah', $html);
    }

    /*
    public function test_GetFieldGroupById() {
        // TODO
    }
    */

    public function test_GetFieldGroupByKey() {
        $group = Adapter::get_field_group_by_key('group_test');
        $this->assertNotEmpty($group);
    }
}