<?php

class SettingsTest extends WP_UnitTestCase {

    function test_DefaultSettingsAreAvailable() {
        $settings = get_option('layotter_settings');
        $this->assertInternalType('array', $settings);
        $this->assertArrayHasKey('general', $settings);
    }
}