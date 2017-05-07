<?php

class DummyTest extends WP_UnitTestCase {

    function test_CanCreatePost() {
        $post_id = self::factory()->post->create(array(
            'post_title' => 'some title'
        ));
        $title = get_the_title($post_id);
        $this->assertEquals($title, 'some title');
    }

    function test_AcfProIsAvailable() {
        $this->assertTrue(\Layotter\Acf\Adapter::is_available());
        $this->assertTrue(\Layotter\Acf\Adapter::is_pro_installed());
    }

    function test_DefaultSettingsAreAvailable() {
        $settings = get_option('layotter_settings');
        $this->assertInternalType('array', $settings);
        $this->assertArrayHasKey('general', $settings);
    }
}