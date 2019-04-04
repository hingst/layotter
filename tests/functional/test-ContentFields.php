<?php

use Layotter\Tests\BaseSeleniumTest;

/**
 * @group functional
 * @group allfields
 * @group contentfields
 */
class ContentFieldsTest extends BaseSeleniumTest {

    private static $id = 0;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        self::upload_attachment();
        self::get('/post-new.php?post_type=page');

        self::click('#layotter *[ng-click="addRow(-1)"]');
        self::click("#layotter .layotter-col-1 *[ng-click='showNewElementTypes(col.elements, -1)']");
        self::click('#dennisbox .layotter-modal-add-element:nth-child(1)');

        self::click('.acf-tab-group li:nth-child(2)');
        self::click('.acf-field[data-name="image"] a[data-name="add"]');
        self::click('.media-router .media-menu-item:nth-child(2)');
        self::click('ul.attachments li:nth-child(1)');
        self::click('.media-frame-toolbar button');
        self::click('.acf-field[data-name="file"] a[data-name="add"]');
        self::click('.media-router .media-menu-item:nth-child(2)');
        self::click('ul.attachments li:nth-child(1)');
        self::click('.media-frame-toolbar button');
        self::insertIntoTinyMce('.acf-field[data-name="wysiwyg"] iframe', 'Some test content.');
        self::select('.acf-field[data-name="oembed"] input.input-search')->sendKeys('https://www.youtube.com/watch?v=5bqpcIX2VDQ');

        self::click('#layotter-edit button[type="submit"]');

        self::$id = self::select('.layotter-element')->getAttribute('data-id');
    }

    public static function tearDownAfterClass() {
        self::delete_attachment();
        parent::tearDownAfterClass();
    }

    public function test_ElementCreated() {
        $this->assertEquals(1, self::countElements('#layotter .layotter-element'));
    }

    public function test_FieldValues() {
        $this->assertStringEndsWith(TESTS_UPLOAD_FILE_NAME, get_field('image', self::$id));
        $this->assertStringEndsWith(TESTS_UPLOAD_FILE_NAME, get_field('file', self::$id));
        $this->assertContains('<p>Some test content.</p>', get_field('wysiwyg', self::$id));
        $this->assertContains('5bqpcIX2VDQ', get_field('oembed', self::$id));
    }

    public function test_EditFields() {
        self::mouseOver('.layotter-element');
        self::click('.layotter-element *[ng-click="editElement(element)"]');

        $image = self::select('.acf-field[data-name="image"] img')->getAttribute('src');
        $file = self::select('.acf-field[data-name="file"] a[data-name="filename"]')->getAttribute('innerHTML');
        $wysiwyg = self::getTinyMceValue('#layotter-edit iframe');
        $oembed = self::select('.acf-field[data-name="oembed"] input.input-search')->getAttribute('value');

        $this->assertStringEndsWith(TESTS_UPLOAD_FILE_NAME, $image);
        $this->assertEquals(TESTS_UPLOAD_FILE_NAME, $file);
        $this->assertContains('<p>Some test content.</p>', $wysiwyg);
        $this->assertEquals('https://www.youtube.com/watch?v=5bqpcIX2VDQ', $oembed);

        self::click('#layotter-edit button[ng-click="cancelEditing()"]');
    }
}