<?php

use Layotter\Tests\BaseSeleniumTest;

/**
 * @group functional
 * @group allfields
 * @group acfprofields
 */
class AcfProFieldsTest extends BaseSeleniumTest {

    private static $id = 0;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        self::get('/post-new.php?post_type=page');

        self::click('#layotter *[ng-click="addRow(-1)"]');
        self::click("#layotter .layotter-col-1 *[ng-click='showNewElementTypes(col.elements, -1)']");
        self::click('#dennisbox .layotter-modal-add-element:nth-child(1)');

        self::click('.acf-tab-group li:nth-child(6)');
        self::click('.acf-gallery-toolbar a.button');
        self::click('.media-router .media-menu-item:nth-child(2)');
        self::click('ul.attachments li:nth-child(1)');
        self::click('.media-frame-toolbar button');
        self::click('.acf-field[data-name="repeater"] .acf-button');
        self::insertIntoTinyMce('.acf-field[data-name="repeater"] iframe', 'Some repeatable test content.');
        self::click('.acf-field[data-name="repeater"] .acf-rel-item');
        self::click('.acf-field[data-name="flexible_content"] .acf-button');
        self::click('.acf-tooltip li:nth-child(1)');
        self::insertIntoTinyMce('.acf-field[data-name="flexible_content"] iframe', 'Some flexible test content.');
        self::click('.acf-field[data-name="flexible_content"] .acf-rel-item');

        self::click('#layotter-edit button[type="submit"]');

        self::$id = self::select('.layotter-element')->getAttribute('data-id');
    }

    public function test_ElementCreated() {
        $this->assertEquals(1, self::countElements('#layotter .layotter-element'));
    }

    public function test_FieldValues() {
        $repeater = get_field('repeater', self::$id);
        $flexible_content = get_field('flexible_content', self::$id);

        $this->assertEquals(TESTS_UPLOAD_FILE_NAME, get_field('gallery', self::$id)[0]['filename']);
        $this->assertContains('<p>Some repeatable test content.</p>', $repeater[0]['wysiwyg']);
        $this->assertEquals('Hello world!', $repeater[0]['relationship'][0]->post_title);
        $this->assertContains('<p>Some flexible test content.</p>', $flexible_content[0]['wysiwyg']);
        $this->assertEquals('Hello world!', $flexible_content[0]['relationship'][0]->post_title);
    }

    public function test_EditFields() {
        self::mouseOver('.layotter-element');
        self::click('.layotter-element *[ng-click="editElement(element)"]');

        $gallery = self::select('.acf-field[data-name="gallery"] .thumbnail img')->getAttribute('src');
        $repeater_wysiwyg = self::getTinyMceValue('#layotter-edit .acf-field[data-name="repeater"] iframe');
        $repeater_relationship = self::select('.acf-field[data-name="repeater"] .acf-field[data-name="relationship"] .values li span.acf-rel-item')->getAttribute('innerHTML');
        $flexible_content_wysiwyg = self::getTinyMceValue('#layotter-edit .acf-field[data-name="flexible_content"] iframe');
        $flexible_content_relationship = self::select('.acf-field[data-name="flexible_content"] .acf-field[data-name="relationship"] .values li span.acf-rel-item')->getAttribute('innerHTML');

        $this->assertStringEndsWith(TESTS_UPLOAD_FILE_NAME, $gallery);
        $this->assertContains('<p>Some repeatable test content.</p>', $repeater_wysiwyg);
        $this->assertContains('Hello world!', $repeater_relationship);
        $this->assertContains('<p>Some flexible test content.</p>', $flexible_content_wysiwyg);
        $this->assertContains('Hello world!', $flexible_content_relationship);

        self::click('#layotter-edit button[ng-click="cancelEditing()"]');
    }
}