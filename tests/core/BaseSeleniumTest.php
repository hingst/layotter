<?php

namespace Layotter\Tests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;

/**
 * Base class for Selenium tests
 */
abstract class BaseSeleniumTest extends \WP_UnitTestCase {

    /**
     * @var RemoteWebDriver
     */
    protected static $webdriver;

    const SLEEP_SHORT = 0.3;
    const SLEEP_MEDIUM = 1;

    /**
     * Set up WebDriver and log into Wordpress
     */
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

    /**
     * Shut down WebDriver
     */
    public static function tearDownAfterClass() {
        self::$webdriver->quit();
    }

    /**
     * Go to a URL relative to wp-admin
     *
     * @param string $relativeUrl
     */
    protected static function get($relativeUrl) {
        self::$webdriver->get(TESTS_WP_HOST . '/wp-admin' . $relativeUrl);
    }

    /**
     * Select an element
     *
     * @param string $selector CSS selector
     * @return RemoteWebElement
     */
    protected static function select($selector) {
        return self::$webdriver->findElement(WebDriverBy::cssSelector($selector));
    }

    /**
     * Select multiple elements
     *
     * @param string $selector CSS selector
     * @return RemoteWebElement[]
     */
    protected static function selectMultiple($selector) {
        return self::$webdriver->findElements(WebDriverBy::cssSelector($selector));
    }

    /**
     * Count the number of elements matching a CSS selector
     *
     * @param string $selector CSS selector
     * @return int
     */
    protected static function countElements($selector) {
        return count(self::selectMultiple($selector));
    }

    /**
     * Move the mouse over an element
     *
     * @param string $selector CSS selector
     */
    protected static function mouseOver($selector) {
        $move_to = self::select($selector)->getCoordinates();
        self::$webdriver->getMouse()->mouseMove($move_to);

        sleep(self::SLEEP_SHORT);
    }

    /**
     * Click on an element
     *
     * @param string $selector CSS selector
     */
    protected static function click($selector) {
        self::select($selector)->click();

        sleep(self::SLEEP_MEDIUM);
    }

    /**
     * Drag an element and drop on another element
     *
     * @param string $element_selector From CSS selector
     * @param string $to_selector To CSS selector
     */
    protected static function dragAndDrop($element_selector, $to_selector) {
        $element = self::select($element_selector);
        $to = self::select($to_selector);
        self::$webdriver->action()->dragAndDrop($element, $to)->perform();

        sleep(self::SLEEP_MEDIUM);
    }

    /**
     * Insert text into the TinyMCE editor
     *
     * @param string $selector CSS selector
     * @param String $content Editor content
     */
    protected static function insertIntoTinyMce($selector, $content) {
        $frame = self::select($selector);
        self::$webdriver->switchTo()->frame($frame);
        self::select('body')->clear()->sendKeys($content);
        self::$webdriver->switchTo()->defaultContent();
    }

    /**
     * Get the value from a TinyMCE editor
     *
     * @param string $selector CSS selector
     * @return null|string Editor content
     */
    protected static function getTinyMceValue($selector) {
        $frame = self::select($selector);
        self::$webdriver->switchTo()->frame($frame);
        $value = self::select('body')->getAttribute('innerHTML');
        self::$webdriver->switchTo()->defaultContent();
        return $value;
    }

    /**
     * Click OK in a modal or prompt
     */
    protected static function clickOk() {
        self::select('#dennisbox-modal *[ng-click$=".okAction()')->click();

        sleep(self::SLEEP_MEDIUM);
    }
}