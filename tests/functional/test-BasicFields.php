<?php

use Layotter\Tests\Functional\BaseSeleniumTest;

/**
 * @group functional
 * @group allfields
 * @group basicfields
 */
class BasicFieldsTest extends BaseSeleniumTest {

    private static $id = 0;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        self::get('/post-new.php?post_type=page');
    }

    public function test_CreateElement() {
        self::click('#layotter *[ng-click="addRow(-1)"]');
        self::click("#layotter .layotter-col-1 *[ng-click='showNewElementTypes(col.elements, -1)']");
        self::click('#dennisbox .layotter-modal-add-element:nth-child(1)');

        self::select('.acf-field[data-name="text"] input')->sendKeys('text');
        self::select('.acf-field[data-name="textarea"] textarea')->sendKeys('textarea');
        self::select('.acf-field[data-name="number"] input')->sendKeys('50');
        self::select('.acf-field[data-name="range"] input[type="number"]')->clear()->sendKeys('50');
        self::select('.acf-field[data-name="email"] input')->sendKeys('email@example.com');
        self::select('.acf-field[data-name="url"] input')->sendKeys('http://example.com');
        self::select('.acf-field[data-name="password"] input')->sendKeys('password');

        self::click('#layotter-edit button[type="submit"]');

        $this->assertEquals(1, self::countElements('.layotter-element'));

        self::$id = self::select('.layotter-element')->getAttribute('data-id');
    }

    public function test_FieldValues() {
        $this->assertEquals('text', get_field('text', self::$id));
        $this->assertEquals('textarea', get_field('textarea', self::$id));
        $this->assertEquals('50', get_field('number', self::$id));
        $this->assertEquals('50', get_field('range', self::$id));
        $this->assertEquals('email@example.com', get_field('email', self::$id));
        $this->assertEquals('http://example.com', get_field('url', self::$id));
        $this->assertEquals('password', get_field('password', self::$id));
    }

    public function test_EditFields() {
        self::mouseOver('.layotter-element');
        self::click('.layotter-element *[ng-click="editElement(element)"]');

        $text = self::select('.acf-field[data-name="text"] input')->getAttribute('value');
        $textarea = self::select('.acf-field[data-name="textarea"] textarea')->getAttribute('innerHTML');
        $number = self::select('.acf-field[data-name="number"] input')->getAttribute('value');
        $range = self::select('.acf-field[data-name="range"] input[type="number"]')->getAttribute('value');
        $email = self::select('.acf-field[data-name="email"] input')->getAttribute('value');
        $url = self::select('.acf-field[data-name="url"] input')->getAttribute('value');
        $password = self::select('.acf-field[data-name="password"] input')->getAttribute('value');

        $this->assertEquals('text', $text);
        $this->assertEquals('textarea', $textarea);
        $this->assertEquals('50', $number);
        $this->assertEquals('50', $range);
        $this->assertEquals('email@example.com', $email);
        $this->assertEquals('http://example.com', $url);
        $this->assertEquals('password', $password);

        self::click('#layotter-edit button[ng-click="cancelEditing()"]');
    }
}