<?php

class eddditor_element_test extends Eddditor_Element {
    protected function attributes() {
        $this->title = 'Testbox';
        $this->description = 'Einfache Testbox mit Testkram';
        $this->icon = 'file';
        $this->field_group = 'group_550f2278a3eb9';
    }
    
    public function backend_view($fields) {
        echo '<p>backend<br>';
        var_dump($fields['text']);
        echo '</p>';
    }
    
    public function frontend_view($fields) {
        echo '<p>frontend<br>';
        var_dump($fields['text']);
        echo '</p>';
    }
}
Eddditor::register_element('test', 'eddditor_element_test');

