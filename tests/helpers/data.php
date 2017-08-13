<?php

class Layotter_Test_Data {

    const POST_150_JSON = '{"options":[],"rows":[{"layout":"1/1","cols":[{"elements":[{"type":"layotter_example_element","values":{"content":"blah blah blah"},"options":[]}],"options":[]}],"options":[]}]}';
    const PRE_150_WRAPPED_JSON = '[layotter]' . self::POST_150_JSON . '[/layotter]';
    const POST_150_TEMPLATE_JSON = '{"options":[],"rows":[{"layout":"1/1","cols":[{"elements":[{"template_id":0,"options":[]}],"options":[]}],"options":[]}]}';
    const EXPECTED_VIEW = '<div class="lo-wrapper"><div class="lo-row"><div class="lo-col-size12of12"><div class="lo-element"><div class="layotter-example-element"><p>blah blah blah</p>
</div></div></div></div></div>';
    const EXPECTED_JSON_REGEX = '~\{"options_id"\:\d+,"rows"\:\[\{"layout"\:"1\\\\/1","options_id"\:\d+,"cols"\:\[\{"options_id"\:\d+,"elements"\:\[\{"id"\:\d+,"options_id"\:\d+,"view"\:"\<div class\=\\\\"layotter\-example\-element\\\\"\>\<p\>blah blah blah\<\\\\/p\>\\\\n\<\\\\/div\>","is_template"\:false\}\]\}\]\}\]~';
    const EXPECTED_EMPTY_JSON_REGEX = '~\{"options_id"\:\d+,"rows"\:\[\]\}~';
    const EXPECTED_SEARCH_DUMP = 'blah blah blah';
}