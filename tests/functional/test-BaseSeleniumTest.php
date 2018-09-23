<?php

use \Facebook\WebDriver\Remote\RemoteWebDriver;
use \Facebook\WebDriver\Remote\RemoteWebElement;
use \Facebook\WebDriver\WebDriverBy;

/**
 * @group functional
 */
abstract class BaseSeleniumTest extends WP_UnitTestCase {

    /** @var RemoteWebDriver */
    protected static $webdriver;

    const SLEEP_SHORT = 0.3;
    const SLEEP_MEDIUM = 1;

    protected static function get($relativeUrl) {
        self::$webdriver->get(TESTS_WP_HOST . '/wp-admin' . $relativeUrl);
    }

    protected static function select($selector) {
        return self::$webdriver->findElement(WebDriverBy::cssSelector($selector));
    }

    /**
     * @param $selector
     * @return RemoteWebElement[]
     */
    protected static function selectMultiple($selector) {
        return self::$webdriver->findElements(WebDriverBy::cssSelector($selector));
    }

    protected static function countElements($selector) {
        return count(self::selectMultiple($selector));
    }

    public static function setUpBeforeClass() {
        self::$webdriver = RemoteWebDriver::create(
            TESTS_SELENIUM_HOST . '/wd/hub',
            array('browserName' => TESTS_SELENIUM_BROWSER)
        );

        self::get('/');
        sleep(self::SLEEP_MEDIUM);

        self::select('#user_login')->sendKeys(TESTS_WP_USER);
        self::select('#user_pass')->sendKeys(TESTS_WP_PASSWORD);
        self::select('#wp-submit')->click();
    }

    public static function tearDownAfterClass() {
        sleep(5);
        self::$webdriver->quit();
    }

    protected function mouseOver($selector) {
        $move_to = self::select($selector)->getCoordinates();
        self::$webdriver->getMouse()->mouseMove($move_to);

        sleep(self::SLEEP_SHORT);
    }

    protected function click($selector) {
        self::select($selector)->click();

        sleep(self::SLEEP_MEDIUM);
    }

    protected function dragAndDrop($element_selector, $to_selector) {
        $element = self::select($element_selector);
        $to = self::select($to_selector);

        self::$webdriver->action()
            ->dragAndDrop($element, $to)
            ->perform();

        sleep(self::SLEEP_MEDIUM);
    }

    protected static function insertIntoTinyMce($content) {
        $frame = self::select('#layotter-edit iframe[id^="acf-editor-"]');
        self::$webdriver->switchTo()->frame($frame);
        self::select('body')->clear()->sendKeys($content);
        self::$webdriver->switchTo()->defaultContent();
    }

    protected static function clickOk() {
        self::select('#dennisbox-modal *[ng-click$=".okAction()')->click();

        sleep(self::SLEEP_MEDIUM);
    }
}