<?php

use Layotter\Core;
use Layotter\Settings;

class SettingsFiltersTest extends WP_UnitTestCase {

    function test_DefaultSettingsSetup() {
        $settings = get_option('layotter_settings');
        $this->assertInternalType('array', $settings);
        $this->assertArrayHasKey('general', $settings);
    }

    function test_EnabledPostTypesFilter() {
        $this->assertContains('page', Settings::get_enabled_post_types());

        add_filter('layotter/enabled_post_types', '__return_empty_array');
        $this->assertNotContains('page', Settings::get_enabled_post_types());

        // reset state
        remove_filter('layotter/enabled_post_types', '__return_empty_array');
        $this->assertContains('page', Settings::get_enabled_post_types());
    }

    function test_EnableByPostIdFilter() {
        $post_id = $GLOBALS['layotter_test_post_id'] = self::factory()->post->create([
            'post_type' => 'post'
        ]);
        $this->assertFalse(Core::is_enabled_for_post($post_id));

        add_filter('layotter/enable_for_posts', [$this, 'helper_enable_for_posts']);
        $this->assertTrue(Core::is_enabled_for_post($post_id));

        // reset state
        remove_filter('layotter/enable_for_posts', [$this, 'helper_enable_for_posts']);
        $this->assertFalse(Core::is_enabled_for_post($post_id));
        unset($GLOBALS['layotter_test_post_id']);
    }

    function helper_enable_for_posts($ids) {
        $ids[] = $GLOBALS['layotter_test_post_id'];
        return $ids;
    }

    function test_DisableByPostIdFilter() {
        $post_id = $GLOBALS['layotter_test_post_id'] = self::factory()->post->create([
            'post_type' => 'page'
        ]);
        $this->assertTrue(Core::is_enabled_for_post($post_id));

        add_filter('layotter/disable_for_posts', [$this, 'helper_disable_for_posts']);
        $this->assertFalse(Core::is_enabled_for_post($post_id));

        // reset state
        remove_filter('layotter/disable_for_posts', [$this, 'helper_disable_for_posts']);
        $this->assertTrue(Core::is_enabled_for_post($post_id));
        unset($GLOBALS['layotter_test_post_id']);
    }

    function helper_disable_for_posts($ids) {
        $ids[] = $GLOBALS['layotter_test_post_id'];
        return $ids;
    }

    function test_PostLayoutsFilter() {
        $this->assertTrue(Settings::post_layouts_enabled());

        add_filter('layotter/enable_post_layouts', '__return_false');
        $this->assertFalse(Settings::post_layouts_enabled());

        // reset state
        remove_filter('layotter/enable_post_layouts', '__return_false');
        $this->assertTrue(Settings::post_layouts_enabled());
    }

    function test_ElementTemplatesFilter() {
        $this->assertTrue(Settings::element_templates_enabled());

        add_filter('layotter/enable_element_templates', '__return_false');
        $this->assertFalse(Settings::element_templates_enabled());

        // reset state
        remove_filter('layotter/enable_element_templates', '__return_false');
        $this->assertTrue(Settings::element_templates_enabled());
    }

    function test_DefaultCssFilter() {
        $this->assertTrue(Settings::default_css_enabled());

        add_filter('layotter/enable_default_css', '__return_false');
        $this->assertFalse(Settings::default_css_enabled());

        // reset state
        remove_filter('layotter/enable_default_css', '__return_false');
        $this->assertTrue(Settings::default_css_enabled());
    }

    function test_DefaultElementFilter() {
        $this->assertTrue(Settings::example_element_enabled());

        add_filter('layotter/enable_example_element', '__return_false');
        $this->assertFalse(Settings::example_element_enabled());

        // reset state
        remove_filter('layotter/enable_example_element', '__return_false');
        $this->assertTrue(Settings::example_element_enabled());
    }

    function test_DebugModeFilter() {
        wp_set_current_user(1);
        $this->assertFalse(Settings::is_debug_mode_enabled());

        add_filter('layotter/debug_mode_roles', [$this, 'helper_enable_debug_mode']);
        $this->assertTrue(Settings::is_debug_mode_enabled());

        // reset state
        remove_filter('layotter/debug_mode_roles', [$this, 'helper_enable_debug_mode']);
        wp_set_current_user(0);
        $this->assertFalse(Settings::is_debug_mode_enabled());
    }

    function helper_enable_debug_mode() {
        return ['administrator'];
    }

    function test_AllowedRowLayoutsFilter() {
        $this->assertEquals(5, count(Settings::get_allowed_row_layouts()));

        add_filter('layotter/rows/allowed_layouts', [$this, 'helper_add_row_layout']);
        $this->assertEquals(6, count(Settings::get_allowed_row_layouts()));
        $this->assertContains('1/6 1/6 2/3', Settings::get_allowed_row_layouts());

        // reset state
        remove_filter('layotter/rows/allowed_layouts', [$this, 'helper_add_row_layout']);
        $this->assertEquals(5, count(Settings::get_allowed_row_layouts()));
    }

    function helper_add_row_layout($layouts) {
        $layouts[] = '1/6 1/6 2/3';
        return $layouts;
    }

    function test_DefaultRowLayoutFilter() {
        $this->assertEquals('1/3 1/3 1/3', Settings::get_default_row_layout());

        add_filter('layotter/rows/default_layout', [$this, 'helper_set_default_layout']);
        $this->assertEquals('1/2 1/2', Settings::get_default_row_layout());

        // reset state
        remove_filter('layotter/rows/default_layout', [$this, 'helper_set_default_layout']);
        $this->assertEquals('1/3 1/3 1/3', Settings::get_default_row_layout());
    }

    function helper_set_default_layout() {
        return '1/2 1/2';
    }

    function test_ColLayoutClassFilter() {
        $this->assertEquals('lo-col-size4of12', Settings::get_col_layout_class('1/3'));

        add_filter('layotter/columns/classes', [$this, 'helper_col_classes']);
        $this->assertEquals('another-class', Settings::get_col_layout_class('1/3'));

        // reset state
        remove_filter('layotter/columns/classes', [$this, 'helper_col_classes']);
        $this->assertEquals('lo-col-size4of12', Settings::get_col_layout_class('1/3'));
    }

    function helper_col_classes($classes) {
        $classes['1/3'] = 'another-class';
        return $classes;
    }

    function test_GetHtmlWrapper() {
        $wrapper = Settings::get_html_wrapper('wrapper');
        $this->assertArrayHasKey('before', $wrapper);
        $this->assertEquals('<div class="lo-wrapper">', $wrapper['before']);
        $this->assertArrayHasKey('after', $wrapper);
        $this->assertEquals('</div>', $wrapper['after']);
    }
}