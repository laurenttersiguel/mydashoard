<?php

class Application_Form_Instance extends Zend_Form
{
    public function init()
    {
        $this->setName('Instance');

        $id = new Zend_Form_Element_Select('id');

        $send = new Zend_Form_Element_Submit('send');
        $send->setAttrib('id', 'sendbutton');
        
        $this->addElements(array($id,$send));
    }
}
