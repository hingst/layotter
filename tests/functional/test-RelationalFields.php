<?php

use Layotter\Tests\BaseSeleniumTest;

/**
 * @group functional
 * @group allfields
 * @group relationalfields
 */
class RelationalFieldsTest extends BaseSeleniumTest {

    private static $id = 0;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        self::get('/post-new.php?post_type=page');

        self::click('#layotter *[ng-click="addRow(-1)"]');
        self::click("#layotter .layotter-col-1 *[ng-click='showNewElementTypes(col.elements, -1)']");
        self::click('#dennisbox .layotter-modal-add-element:nth-child(1)');

        self::click('.acf-tab-group li:nth-child(4)');
        self::click('.acf-field[data-name="link"] a.button');
        self::click('.query-results li:nth-child(1)');
        self::click('#wp-link-submit');
        self::click('.acf-field[data-name="post_object"] .select2');
        self::click('.select2-container li:nth-child(1)');
        self::click('.acf-field[data-name="page_link"] .select2');
        self::click('.select2-container li:nth-child(1)');
        self::click('.acf-field[data-name="relationship"] .acf-rel-item');
        self::click('.acf-field[data-name="taxonomy"] input[value="1"]');
        self::click('.acf-field[data-name="user"] .select2');
        self::click('.select2-container li:nth-child(1)');

        self::click('#layotter-edit button[type="submit"]');

        self::$id = self::select('.layotter-element')->getAttribute('data-id');
    }

    public function test_ElementCreated() {
        $this->assertEquals(1, self::countElements('#layotter .layotter-element'));
    }

    public function test_FieldValues() {
        $this->assertContains('sample-page', get_field('link', self::$id));
        $this->assertEquals('Hello world!', get_field('post_object', self::$id)->post_title);
        $this->assertEquals(home_url(), get_field('page_link', self::$id));
        $this->assertEquals('Hello world!', get_field('relationship', self::$id)[0]->post_title);
        $this->assertEquals('Uncategorized', get_field('taxonomy', self::$id)[0]->name);
        $this->assertEquals(TESTS_WP_USER, get_field('user', self::$id)->data->user_login);
    }

    public function test_EditFields() {
        self::mouseOver('.layotter-element');
        self::click('.layotter-element *[ng-click="editElement(element)"]');

        $link = self::select('.acf-field[data-name="link"] a.link-url')->getAttribute('href');
        $post_object = self::select('.acf-field[data-name="post_object"] span.select2-selection__rendered')->getAttribute('innerHTML');
        $page_link = self::select('.acf-field[data-name="page_link"] span.select2-selection__rendered')->getAttribute('innerHTML');
        $relationship = self::select('.acf-field[data-name="relationship"] .values li span.acf-rel-item')->getAttribute('innerHTML');
        $taxonomy = self::select('.acf-field[data-name="taxonomy"] input[type="checkbox"]')->getAttribute('checked');
        $user = self::select('.acf-field[data-name="user"] span.select2-selection__rendered')->getAttribute('innerHTML');

        $this->assertContains('sample-page', $link);
        $this->assertEquals('Hello world!', $post_object);
        $this->assertEquals(home_url(), $page_link);
        $this->assertContains('Hello world!', $relationship);
        $this->assertEquals('true', $taxonomy);
        $this->assertEquals(TESTS_WP_USER, $user);

        self::click('#layotter-edit button[ng-click="cancelEditing()"]');
    }
}