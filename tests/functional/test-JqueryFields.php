<?php

use Layotter\Tests\BaseSeleniumTest;

/**
 * @group functional
 * @group allfields
 * @group jqueryfields
 */
class JqueryFieldsTest extends BaseSeleniumTest {

    private static $id = 0;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        self::get('/post-new.php?post_type=page');
    }

    public function test_CreateElement() {
        self::click('#layotter *[ng-click="addRow(-1)"]');
        self::click("#layotter .layotter-col-1 *[ng-click='showNewElementTypes(col.elements, -1)']");
        self::click('#dennisbox .layotter-modal-add-element:nth-child(1)');

        self::click('.acf-tab-group li:nth-child(5)');
        // google maps field can't be tested without API key
        self::click('.acf-field[data-name="date_picker"] input.input');
        self::click('.acf-ui-datepicker .ui-state-highlight');
        self::click('.acf-field[data-name="date_time_picker"] input.input');
        self::click('.acf-ui-datepicker .ui-state-highlight');
        self::click('.acf-ui-datepicker .ui-datepicker-close');
        self::click('.acf-field[data-name="time_picker"] input.input');
        self::click('.acf-ui-datepicker .ui-datepicker-close');
        self::click('.acf-field[data-name="color_picker"] button.button');
        self::select('input.wp-color-picker')->sendKeys('#123456');

        self::click('#layotter-edit button[type="submit"]');

        $this->assertEquals(1, self::countElements('#layotter .layotter-element'));

        self::$id = self::select('.layotter-element')->getAttribute('data-id');
    }

    public function test_FieldValues() {
        $this->assertEmpty(get_field('google_map', self::$id));
        $this->assertEquals(date('Y-m-d'), get_field('date_picker', self::$id));
        $this->assertEquals(date('Y-m-d') . ' 00:00:00', get_field('date_time_picker', self::$id));
        $this->assertEquals('00:00:00', get_field('time_picker', self::$id));
        $this->assertEquals('#123456', get_field('color_picker', self::$id));
    }

    public function test_EditFields() {
        self::mouseOver('.layotter-element');
        self::click('.layotter-element *[ng-click="editElement(element)"]');

        $google_map = self::select('.acf-field[data-name="google_map"] input')->getAttribute('value');
        $date_picker = self::select('.acf-field[data-name="date_picker"] input')->getAttribute('value');
        $date_time_picker = self::select('.acf-field[data-name="date_time_picker"] input')->getAttribute('value');
        $time_picker = self::select('.acf-field[data-name="time_picker"] input')->getAttribute('value');
        $color_picker = self::select('.acf-field[data-name="color_picker"] input')->getAttribute('value');

        $this->assertEmpty($google_map);
        $this->assertEquals(date('Ymd'), $date_picker);
        $this->assertEquals(date('Y-m-d') . ' 00:00:00', $date_time_picker);
        $this->assertEquals('00:00:00', $time_picker);
        $this->assertEquals('#123456', $color_picker);

        self::click('#layotter-edit button[ng-click="cancelEditing()"]');
    }
}