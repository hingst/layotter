<?php

use \Facebook\WebDriver\Remote\RemoteWebDriver;
use \Facebook\WebDriver\WebDriverBy;

/**
 * @group functional
 */
abstract class BaseSeleniumTest extends WP_UnitTestCase {

    /** @var RemoteWebDriver */
    protected $webDriver;

    const SLEEP_SHORT = 1;

    protected function get($relativeUrl) {
        $this->webDriver->get(TESTS_WP_HOST . '/wp-admin' . $relativeUrl);
    }

    protected function byId($selector) {
        return $this->webDriver->findElement(WebDriverBy::id($selector));
    }

    protected function byClass($selector) {
        return $this->webDriver->findElement(WebDriverBy::className($selector));
    }

    protected function byCss($selector) {
        return $this->webDriver->findElement(WebDriverBy::cssSelector($selector));
    }

    public function setUp() {
        $this->webDriver = RemoteWebDriver::create(
            TESTS_SE_HOST . '/wd/hub',
            array('browserName' => TESTS_SE_BROWSER)
        );

        $this->get('/');
        $this->byId('user_login')->sendKeys(TESTS_WP_USER);
        $this->byId('user_pass')->sendKeys(TESTS_WP_PASSWORD);
        $this->byId('wp-submit')->click();
    }

    public function tearDown() {
        $this->webDriver->quit();
    }
}