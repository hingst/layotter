<?php

class eddditor_element_test extends Eddditor_Element {
    protected function attributes() {
        $this->title = 'Testbox';
        $this->description = 'Einfache Testbox mit Testkram';
        $this->icon = 'file';
        $this->field_group = 'group_54ec8d784e2e1';
    }
    
    public function backend_view($fields) {
        echo '<p>backend?';
        var_dump($fields);
        echo '</p>';
    }
    
    public function frontend_view($fields) {
        echo '<p>frontend?<br>';
        var_dump($fields);
        echo '</p>';
    }
}
Eddditor::register_element('test', 'eddditor_element_test');

