<?php

use Layotter\Tests\BaseSeleniumTest;

/**
 * @group functional
 * @group editor
 */
class EditorTest extends BaseSeleniumTest {

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        self::get('/post-new.php?post_type=page');
    }

    public static function tearDownAfterClass() {
        self::click('#delete-action .submitdelete');

        parent::tearDownAfterClass();
    }

    public function test_CreateRow() {
        self::click('#layotter *[ng-click="addRow(-1)"]');

        $this->assertEquals(1, self::countElements('#layotter .layotter-row'));
    }

    public function test_AddElement() {
        self::click("#layotter .layotter-col-1 *[ng-click='showNewElementTypes(col.elements, -1)']");
        self::click('#dennisbox .layotter-modal-add-element:nth-child(2)');
        self::insertIntoTinyMce('#layotter-edit iframe', 'Some test content.');
        self::click('#layotter-edit button[type="submit"]');

        $html = self::select('#layotter .layotter-example-element')->getAttribute('innerHTML');

        $this->assertEquals(1, self::countElements('#layotter .layotter-element'));
        $this->assertContains('<p>Some test content.</p>', $html);
    }

    public function test_DuplicateRow() {
        self::mouseOver('#layotter .layotter-row-0 .layotter-row-canvas');
        self::click('#layotter .layotter-row-0 *[ng-click="duplicateRow($index)"]');

        $this->assertEquals(2, self::countElements('#layotter .layotter-row'));
    }

    public function test_Undo() {
        self::click('#layotter *[ng-click="undoStep()"]');

        $this->assertEquals(1, self::countElements('#layotter .layotter-row'));

        self::click('#layotter *[ng-click="undoStep()"]');
        self::click('#layotter *[ng-click="undoStep()"]');

        $this->assertEquals(0, self::countElements('#layotter .layotter-row'));
    }

    public function test_Redo() {
        self::click('#layotter *[ng-click="redoStep()"]');
        self::click('#layotter *[ng-click="redoStep()"]');
        self::click('#layotter *[ng-click="redoStep()"]');

        $this->assertEquals(2, self::countElements('#layotter .layotter-row'));
    }

    public function test_MoveElement() {
        self::dragAndDrop('#layotter .layotter-row-1 .layotter-col-1 .layotter-element-0', '#layotter .layotter-row-1 .layotter-col-2 .layotter-elements');

        $this->assertEquals(0, self::countElements('#layotter .layotter-row-1 .layotter-col-1 .layotter-element'));
        $this->assertEquals(1, self::countElements('#layotter .layotter-row-1 .layotter-col-2 .layotter-element'));
    }

    public function test_MoveRow() {
        self::mouseOver('#layotter .layotter-row-1 .layotter-row-canvas');
        self::dragAndDrop('#layotter .layotter-row-1 .layotter-row-move', '#layotter .layotter-row-0');

        $this->assertEquals(0, self::countElements('#layotter .layotter-row-0 .layotter-col-1 .layotter-element'));
        $this->assertEquals(1, self::countElements('#layotter .layotter-row-0 .layotter-col-2 .layotter-element'));
        $this->assertEquals(1, self::countElements('#layotter .layotter-row-1 .layotter-col-1 .layotter-element'));
        $this->assertEquals(0, self::countElements('#layotter .layotter-row-1 .layotter-col-2 .layotter-element'));

        self::mouseOver('#layotter .layotter-row-1 .layotter-row-canvas');
        self::dragAndDrop('#layotter .layotter-row-1 .layotter-row-move', '#layotter .layotter-row-0');

        $this->assertEquals(1, self::countElements('#layotter .layotter-row-0 .layotter-col-1 .layotter-element'));
        $this->assertEquals(0, self::countElements('#layotter .layotter-row-0 .layotter-col-2 .layotter-element'));
        $this->assertEquals(0, self::countElements('#layotter .layotter-row-1 .layotter-col-1 .layotter-element'));
        $this->assertEquals(1, self::countElements('#layotter .layotter-row-1 .layotter-col-2 .layotter-element'));
    }

    public function test_DeleteRow() {
        self::mouseOver('#layotter .layotter-row-0');
        self::click('#layotter .layotter-row-0 *[ng-click="deleteRow($index)"]');
        self::clickOk();

        $this->assertEquals(1, self::countElements('#layotter .layotter-row'));
    }

    public function test_DuplicateElement() {
        self::mouseOver('#layotter .layotter-element');
        self::mouseOver('#layotter .layotter-element-dropdown');
        self::click('#layotter *[ng-click="duplicateElement(col.elements, $index)"]');

        $this->assertEquals(2, self::countElements('#layotter .layotter-element'));
    }

    public function test_EditElement() {
        self::mouseOver('#layotter .layotter-element-1');
        self::click('#layotter .layotter-element-1 *[ng-click="editElement(element)"]');
        self::insertIntoTinyMce('#layotter-edit iframe', 'Some other test content.');
        self::click('#layotter-edit button[type="submit"]');

        $html = self::select('#layotter .layotter-element-1 .layotter-example-element')->getAttribute('innerHTML');

        $this->assertContains('<p>Some other test content.</p>', $html);
    }

    public function test_SaveElementAsTemplate() {
        self::mouseOver('#layotter .layotter-element');
        self::mouseOver('#layotter .layotter-element-dropdown');
        self::click('#layotter *[ng-click="saveNewTemplate(element)"]');

        $template_indicator = self::select('#layotter .layotter-element-message');

        $this->assertTrue(self::select('#layotter-templates')->isDisplayed());
        $this->assertEquals(1, self::countElements('#layotter-templates .layotter-element'));
        $this->assertTrue($template_indicator->isDisplayed());
    }

    public function test_CreateNewElementFromTemplate() {
        self::dragAndDrop('#layotter-templates .layotter-element-0', '#layotter .layotter-row-0 .layotter-col-0 .layotter-elements');

        $this->assertEquals(1, self::countElements('#layotter-templates .layotter-element'));
        $this->assertEquals(1, self::countElements('#layotter .layotter-row-0 .layotter-col-0 .layotter-element'));
    }

    public function test_EditTemplate() {
        self::mouseOver('#layotter-templates .layotter-element');
        self::click('#layotter-templates *[ng-click="editTemplate(element)"]');
        self::clickOk();
        self::insertIntoTinyMce('#layotter-edit iframe', 'Some template content.');
        self::click('#layotter-edit button[type="submit"]');

        $template1_html = self::select('#layotter .layotter-col-0 .layotter-element-0 .layotter-example-element')->getAttribute('innerHTML');
        $template2_html = self::select('#layotter .layotter-col-2 .layotter-element-0 .layotter-example-element')->getAttribute('innerHTML');
        $element_html = self::select('#layotter .layotter-col-2 .layotter-element-1 .layotter-example-element')->getAttribute('innerHTML');
        $original_html = self::select('#layotter-templates .layotter-example-element')->getAttribute('innerHTML');
        $template1_indicator = self::select('#layotter .layotter-col-0 .layotter-element-0 .layotter-element-message');
        $template2_indicator = self::select('#layotter .layotter-col-2 .layotter-element-0 .layotter-element-message');
        $element_indicator = self::select('#layotter .layotter-col-2 .layotter-element-1 .layotter-element-message');

        $this->assertContains('<p>Some template content.</p>', $template1_html);
        $this->assertContains('<p>Some template content.</p>', $template2_html);
        $this->assertContains('<p>Some other test content.</p>', $element_html);
        $this->assertContains('<p>Some template content.</p>', $original_html);
        $this->assertTrue($template1_indicator->isDisplayed());
        $this->assertTrue($template2_indicator->isDisplayed());
        $this->assertFalse($element_indicator->isDisplayed());
    }

    public function test_SaveNewLayout() {
        self::click('#layotter *[ng-click="saveNewLayout()"]');
        self::select('#layotter-modal-prompt-input')->sendKeys('My Layout');
        self::clickOk();

        $this->assertTrue(self::select('#layotter *[ng-click="loadLayout()"]')->isDisplayed());
    }

    public function test_ResizeRow() {
        self::mouseOver('#layotter .layotter-row');
        self::mouseOver('#layotter .layotter-row-select-layout');
        self::click('#layotter *[ng-click="setRowLayout(row, colbutton)"]');

        $this->assertEquals(3, self::countElements('#layotter .layotter-element'));
        $this->assertEquals(1, self::countElements('#layotter .layotter-col'));
    }

    public function test_DeleteTemplate() {
        self::mouseOver('#layotter-templates .layotter-element');
        self::click('#layotter-templates *[ng-click="deleteTemplate($index)"]');
        self::clickOk();

        $template_indicator = self::select('#layotter .layotter-element-message');

        $this->assertEquals(3, self::countElements('#layotter .layotter-element'));
        $this->assertEquals(0, self::countElements('#layotter-templates .layotter-element'));
        $this->assertFalse(self::select('#layotter-templates')->isDisplayed());
        $this->assertFalse($template_indicator->isDisplayed());
    }

    public function test_DeleteElement() {
        self::mouseOver('#layotter .layotter-element');
        self::click('#layotter *[ng-click="deleteElement(col.elements, $index)"]');
        self::clickOk();

        $this->assertEquals(2, self::countElements('#layotter .layotter-element'));
    }

    public function test_RenameLayout() {
        self::click('#layotter *[ng-click="loadLayout()"]');
        self::click('#dennisbox *[ng-click="renameLayout($index, $event)"]');
        self::select('#layotter-modal-prompt-input')->sendKeys('My Renamed Layout');
        self::clickOk();

        $html = self::select('.layotter-modal-load-layout-header')->getAttribute('innerHTML');

        $this->assertContains('My Renamed Layout', $html);
    }

    public function test_LoadLayout() {
        self::click('#dennisbox .layotter-modal-load-layout-header');
        self::clickOk();

        $first_html = self::select('#layotter .layotter-col-0 .layotter-element-0')->getAttribute('innerHTML');
        $first_indicator = self::select('#layotter .layotter-col-0 .layotter-element-0 .layotter-element-message');

        $this->assertEquals(1, self::countElements('#layotter .layotter-row'));
        $this->assertEquals(3, self::countElements('#layotter .layotter-element'));
        $this->assertContains('<p>Some template content.</p>', $first_html);
        $this->assertFalse($first_indicator->isDisplayed());
    }

    public function test_DeleteLayout() {
        self::click('#layotter *[ng-click="loadLayout()"]');
        self::click('#dennisbox *[ng-click="deleteLayout($index, $event)"]');
        self::clickOk();

        $this->assertEquals(0, self::countElements('#dennisbox'));
        $this->assertFalse(self::select('#layotter *[ng-click="loadLayout()"]')->isDisplayed());
    }

    public function test_FrontendView() {
        self::select('#title')->sendKeys('Editor Test Page');
        self::click('#publish');
        self::click('#sample-permalink');

        $contents = self::selectMultiple('.layotter-example-element');

        $this->assertEquals(1, self::countElements('.layotter-test-post'));
        $this->assertEquals(1, self::countElements('.layotter-test-row'));
        $this->assertEquals(3, self::countElements('.layotter-test-column'));
        $this->assertEquals(3, self::countElements('.layotter-test-element'));
        $this->assertContains('<p>Some template content.</p>', $contents[0]->getAttribute('innerHTML'));
        $this->assertContains('<p>Some template content.</p>', $contents[1]->getAttribute('innerHTML'));
        $this->assertContains('<p>Some other test content.</p>', $contents[2]->getAttribute('innerHTML'));

        self::$webdriver->navigate()->back();
    }
}