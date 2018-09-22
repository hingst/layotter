<?php

/**
 * @group functional
 */
class EditorTest extends BaseSeleniumTest {

    public function test_CanCreateElement() {
        $this->get('/post-new.php?post_type=page');

        $this->byCss('#layotter *[ng-click="addRow(-1)"]')->click();
        sleep(self::SLEEP_SHORT);
        $this->byCss('#layotter *[ng-click="showNewElementTypes(col.elements, -1)"]')->click();
        sleep(self::SLEEP_SHORT);
        $this->byCss('#dennisbox *[ng-click="selectNewElementType(element.type)"]')->click();
        sleep(self::SLEEP_SHORT);

        $frame = $this->byCss('#layotter-edit iframe[id^="acf-editor-"]');
        $this->webDriver->switchTo()->frame($frame);
        $this->byCss('body')->clear()->sendKeys('Some test content.');

        $this->webDriver->switchTo()->defaultContent();
        $this->byCss('#layotter-edit button[type="submit"]')->click();
        sleep(self::SLEEP_SHORT);

        $html = $this->byClass('layotter-example-element')->getAttribute('innerHTML');

        $this->assertContains('<p>Some test content.</p>', $html);
    }
}