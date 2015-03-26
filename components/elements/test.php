<?php

class layotter_element_test extends Layotter_Element {
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
Layotter::register_element('test', 'layotter_element_test');

