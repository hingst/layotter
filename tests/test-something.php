<?php

class DummyTest extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	function test_sample() {
		$post_id = $this->factory->post->create(array(
		    'post_title' => 'some title'
        ));
		$title = get_the_title($post_id);
        $this->assertEquals($title, 'some title');
	}
}
