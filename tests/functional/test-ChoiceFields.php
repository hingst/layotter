<?php

use Layotter\Tests\Functional\BaseSeleniumTest;

/**
 * @group functional
 * @group allfields
 * @group choicefields
 */
class ChoiceFieldsTest extends BaseSeleniumTest {

    private static $id = 0;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        self::get('/post-new.php?post_type=page');
    }

    public function test_CreateElement() {
        self::click('#layotter *[ng-click="addRow(-1)"]');
        self::click("#layotter .layotter-col-1 *[ng-click='showNewElementTypes(col.elements, -1)']");
        self::click('#dennisbox .layotter-modal-add-element:nth-child(1)');

        self::click('.acf-tab-group li:nth-child(3)');
        self::click('.acf-field[data-name="select"] select');
        self::click('.acf-field[data-name="select"] select option[value="2"]');
        self::click('.acf-field[data-name="checkbox"] input[value="2"]');
        self::click('.acf-field[data-name="radio"] input[value="2"]');
        self::click('.acf-field[data-name="button_group"] label:nth-child(2)');
        self::click('.acf-field[data-name="boolean"] input[value="1"]');

        self::click('#layotter-edit button[type="submit"]');

        $this->assertEquals(1, self::countElements('#layotter .layotter-element'));

        self::$id = self::select('.layotter-element')->getAttribute('data-id');
    }

    public function test_FieldValues() {
        $this->assertEquals('2', get_field('select', self::$id));
        $this->assertEquals('2', get_field('checkbox', self::$id)[0]);
        $this->assertEquals('2', get_field('radio', self::$id));
        $this->assertEquals('2', get_field('button_group', self::$id));
        $this->assertEquals('1', get_field('boolean', self::$id));
    }

    public function test_EditFields() {
        self::mouseOver('.layotter-element');
        self::click('.layotter-element *[ng-click="editElement(element)"]');

        $select = self::select('.acf-field[data-name="select"] option[selected]')->getAttribute('value');
        $checkbox = self::select('.acf-field[data-name="checkbox"] input[checked]')->getAttribute('value');
        $radio = self::select('.acf-field[data-name="radio"] input[checked]')->getAttribute('value');
        $button_group = self::select('.acf-field[data-name="button_group"] input[checked]')->getAttribute('value');
        $boolean = self::select('.acf-field[data-name="boolean"] input[checked]')->getAttribute('value');

        $this->assertEquals('2', $select);
        $this->assertEquals('2', $checkbox);
        $this->assertEquals('2', $radio);
        $this->assertEquals('2', $button_group);
        $this->assertEquals('1', $boolean);

        self::click('#layotter-edit button[ng-click="cancelEditing()"]');
    }
}