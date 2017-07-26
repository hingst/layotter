<?php

class Test_Text_Element extends Layotter_Element {
	protected function attributes() {
		$this->title       = 'Text';
		$this->description = 'A very simple text element.';
		$this->icon        = 'font';
		$this->field_group = 'group_test';
	}
	protected function frontend_view($fields) {
		echo 'f';
	}
	protected function backend_view($fields) {
		echo 'b';
	}
}
Layotter::register_element('element_test', 'Test_Text_Element');