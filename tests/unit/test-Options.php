<?php

use Layotter\Models\Options;
use Layotter\Repositories\OptionsRepository;
use Layotter\Services\OptionsFieldsService;

/**
 * @group unit
 */
class OptionsTest extends \WP_UnitTestCase {

    public function test_ElementOptions() {
        $options = OptionsRepository::create('element');

        $this->assertTrue($options instanceof Options);
        $this->assertTrue(OptionsFieldsService::has_fields($options));
        $this->assertEquals(1, count(OptionsFieldsService::get_fields($options)));
        $this->assertEquals(1, count(OptionsFieldsService::get_values($options)));
    }

    public function test_ColumnOptions() {
        $options = OptionsRepository::create('col');

        $this->assertTrue($options instanceof Options);
        $this->assertTrue(OptionsFieldsService::has_fields($options));
        $this->assertEquals(1, count(OptionsFieldsService::get_fields($options)));
        $this->assertEquals(1, count(OptionsFieldsService::get_values($options)));
    }

    public function test_RowOptions() {
        $options = OptionsRepository::create('row');

        $this->assertTrue($options instanceof Options);
        $this->assertTrue(OptionsFieldsService::has_fields($options));
        $this->assertEquals(1, count(OptionsFieldsService::get_fields($options)));
        $this->assertEquals(1, count(OptionsFieldsService::get_values($options)));
    }

    public function test_PostOptions() {
        $options = OptionsRepository::create('post');

        $this->assertTrue($options instanceof Options);
        $this->assertTrue(OptionsFieldsService::has_fields($options));
        $this->assertEquals(1, count(OptionsFieldsService::get_fields($options)));
        $this->assertEquals(1, count(OptionsFieldsService::get_values($options)));
    }

    public function test_PostTypeContext() {
        $options = OptionsRepository::create('post');
        $options->set_post_type_context('post');

        $this->assertFalse(OptionsFieldsService::has_fields($options));

        $options->set_post_type_context('page');

        $this->assertTrue(OptionsFieldsService::has_fields($options));
    }

}