<?php

class Application_Form_Instance extends Zend_Form
{
    public function init()
    {
        $this->setName('Instance');

        $instanceName = new Zend_Form_Element_Select('instanceSelect');

        $send = new Zend_Form_Element_Submit('send');
        $send->setAttrib('id', 'sendbutton')
              ->setLabel('Valid');
        
        $this->addElements(array($instanceName,$send));
    }
}
