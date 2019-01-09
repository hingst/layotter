<?php

namespace Layotter\Tests;

/**
 * Contains test content
 */
class TestData {

    const POST_150_JSON = '{"options":{"option":"post option value"},"rows":[{"layout":"1/1","options":{"option":"row option value"},"cols":[{"options":{"option":"column option value"},"elements":[{"type":"layotter_example_element","values":{"content":"blah blah blah"},"options":{"option":"element option value"}}]}]}]}';
    const PRE_150_WRAPPED_JSON = '[layotter]' . self::POST_150_JSON . '[/layotter]';
    const POST_150_TEMPLATE_JSON = '{"options":{"option":"post option value"},"rows":[{"layout":"1/1","options":{"option":"row option value"},"cols":[{"options":{"option":"column option value"},"elements":[{"template_id":0,"options":{"option":"element option value"}}]}]}]}';
    const EXPECTED_VIEW = '<div class="layotter-test-post">post option value|<div class="layotter-test-row">row option value|post option value|<div class="layotter-test-column lo-col-size12of12">column option value|row option value|post option value|<div class="layotter-test-element">element option value|column option value|row option value|post option value|<div class="layotter-example-element"><p>blah blah blah</p>
</div></div></div></div></div>';
    const EXPECTED_JSON_REGEX = '~\{"options_id"\:\d+,"rows"\:\[\{"layout"\:"1\\\\/1","options_id"\:\d+,"cols"\:\[\{"options_id"\:\d+,"elements"\:\[\{"id"\:\d+,"options_id"\:\d+,"view"\:"\<div class\=\\\\"layotter\-example\-element\\\\"\>\<p\>blah blah blah\<\\\\/p\>\\\\n\<\\\\/div\>","is_template"\:false\}\]\}\]\}\]~';
    const EXPECTED_EMPTY_JSON_REGEX = '~\{"options_id"\:\d+,"rows"\:\[\]\}~';
    const EXPECTED_SEARCH_DUMP = 'post option value| row option value|post option value| column option value|row option value|post option value| element option value|column option value|row option value|post option value|  blah blah blah'; // TODO: get rid of double space
    const EXPECTED_TEXTAREA_FIRST_LINE = 'Welcome to the text editor! Write something, insert links or images, and click save when you\'re done.';
    const POST_150_ALL_FIELDS_JSON = '{"options":[],"rows":[{"layout":"1/1","options":[],"cols":[{"options":[],"elements":[{"type":"layotter_functional_test_element","values":{"text":"text","textarea":"textarea","number":"50","range":"50","email":"email@example.com","url":"http://example.com","password":"password","image":"ATTACHMENT_ID","file":"ATTACHMENT_ID","wysiwyg":"wysiwyg","oembed":"https://www.youtube.com/watch?v=5bqpcIX2VDQ","gallery":["ATTACHMENT_ID"],"select":"2","checkbox":["2"],"radio":"2","button_group":"2","boolean":"1","link":{"title":"","url":"http://example.com","target":""},"post_object":"1","page_link":"1","relationship":["1"],"taxonomy":["1"],"user":"1","google_map":{"address":"","lat":"","lng":""},"date_picker":"20190109","date_time_picker":"2019-01-09 00:00:00","time_picker":"00:00:00","color_picker":"#123456","repeater":[{"field_5bad117af1587":"wysiwyg","field_5bad11c4f158a":["1"]}],"flexible_content":{"5c361ea42a602":{"acf_fc_layout":"two_fields","field_5bad1198f1588":"wysiwyg","field_5bad11aff1589":["1"]}}},"options":[]}]}]}]}';
    const EXPECTED_ALL_FIELDS_JSON_REGEX = '~\{"options_id"\:\d+,"rows"\:\[\{"layout"\:"1\\\/1","options_id"\:\d+,"cols"\:\[\{"options_id"\:\d+,"elements"\:\[\{"id":(\d+),"options_id"\:\d+,"view"\:"","is_template"\:false\}\]\}\]\}\]\}~';

    /**
     * Must be hooked to 'init' to set up test data
     */
    public static function register() {
        FieldGroups::register();
        TestElement::register();
        ViewFilters::register();
    }
}
