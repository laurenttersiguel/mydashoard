<?php

class Application_Form_Auth extends Zend_Form
{
    public function init()
    {
        $this->setName('User');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addFilter('Int');

        $login = new Zend_Form_Element_Text('login');
        $login->setLabel('Login')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $password = new Zend_Form_Element_Text('password');
        $password->setLabel('Password')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $send = new Zend_Form_Element_Submit('send');
        $send->setAttrib('id', 'sendbutton');

        $this->addElements(array($id, $login, $password, $send));
    }
}
