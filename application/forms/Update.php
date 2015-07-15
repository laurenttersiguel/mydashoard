<?php

class Application_Form_Update extends Zend_Form
{
    public function init()
    {
        $this->setName('Update');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addFilter('Int');

        $login = new Zend_Form_Element_Text('login');
        $login->setLabel('Login')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $mail = new Zend_Form_Element_Text('mail');
        $mail->setLabel('Mail')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $givenname = new Zend_Form_Element_Text('givenname');
        $givenname->setLabel('Givenname')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $sn = new Zend_Form_Element_Text('sn');
        $sn->setLabel('Sn')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $mobile = new Zend_Form_Element_Text('mobile');
        $mobile->setLabel('Mobile')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');

        $send = new Zend_Form_Element_Submit('send');
        $send->setAttrib('id', 'sendbutton');

        $this->addElements(array($id, $login, $givenname,$sn,$mobile,$mail,$password, $send));
    }
}
