<?php

use Layotter\Tests\BaseSeleniumTest;

/**
 * @group functional
 * @group revisions
 */
class RevisionsTest extends BaseSeleniumTest {

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        self::get('/post-new.php?post_type=page');

        // add row
        self::click('#layotter *[ng-click="addRow(-1)"]');

        // add element
        self::click('#layotter *[ng-click="showNewElementTypes(col.elements, -1)"]');
        self::click('#dennisbox .layotter-modal-add-element:nth-child(2)');
        self::insertIntoTinyMce('#layotter-edit iframe', 'Some test content.');
        self::click('#layotter-edit button[type="submit"]');

        // publish page
        self::select('#title')->sendKeys('Revisions Test Page');
        self::click('#publish');

        // add another element
        self::mouseOver('#layotter .layotter-col');
        self::click('#layotter *[ng-click="showNewElementTypes(col.elements, $index)"]');
        self::click('#dennisbox .layotter-modal-add-element:nth-child(2)');
        self::insertIntoTinyMce('#layotter-edit iframe', 'Some other test content.');
        self::click('#layotter-edit button[type="submit"]');

        // publish updated page
        self::click('#publish');
    }

    public static function tearDownAfterClass() {
        self::click('#delete-action .submitdelete');

        parent::tearDownAfterClass();
    }

    public function test_RestoreRevision() {
        self::click('#misc-publishing-actions .misc-pub-revisions a');
        self::dragAndDrop('.revisions-controls .wp-slider .ui-slider-handle', '.revisions-controls .wp-slider');
        self::click('.restore-revision');

        $html = self::select('#layotter .layotter-example-element')->getAttribute('innerHTML');

        $this->assertEquals(1, self::countElements('#layotter .layotter-element'));
        $this->assertContains('<p>Some test content.</p>', $html);

        self::click('#misc-publishing-actions .misc-pub-revisions a');
        self::dragAndDrop('.revisions-controls .wp-slider .ui-slider-handle', '.revisions-controls .wp-slider');
        self::click('.restore-revision');

        $html_element1 = self::select('#layotter .layotter-element-0 .layotter-example-element')->getAttribute('innerHTML');
        $html_element2 = self::select('#layotter .layotter-element-1 .layotter-example-element')->getAttribute('innerHTML');

        $this->assertEquals(2, self::countElements('#layotter .layotter-element'));
        $this->assertContains('<p>Some test content.</p>', $html_element1);
        $this->assertContains('<p>Some other test content.</p>', $html_element2);
    }
}