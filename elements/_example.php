<?php

class eddditor_element_example extends Eddditor_Element
{
    protected function attributes()
    {
        $this->title = 'Textbox';
        $this->description = 'Einfache Textbox mit Titelzeile';
        $this->icon = plugins_url('icons/text.png', __FILE__);
        $this->field_group = 'group_5447af2e4f65c';
    }
    
    public function backend_view($fields)
    {
        echo '<p>backend?';
        var_dump($fields['text']);
        echo '</p>';
    }
    
    public function frontend_view($fields)
    {
        echo '<p>frontend?<br>';
        echo $fields['text'];
        echo '</p>';
    }
}
//Eddditor::register_element('eddditor_text', 'eddditor_element_example');

