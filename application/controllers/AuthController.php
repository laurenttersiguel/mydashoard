<?php
require_once 'MyStock.php';

class authController extends Zend_Controller_Action
{
    public function init()
    {
    }

    public function loginAction()
    {
       $form = new Application_Form_Auth();
       $form->send->setLabel('Authenticate');
       $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
              $formData = $this->getRequest()->getPost();
              if ($form->isValid($formData)) {
                    $login = $form->getValue('login');
                    $password = $form->getValue('password');
                    $config = new Zend_Config_Ini('../application/configs/application.ini','production');
                    $options = $config->ldap->toArray();
                    unset($options['log_path']);
                    $adapter = new Zend_Auth_Adapter_Ldap($options,$login,$password);
                    $result = Zend_Auth::getInstance()->authenticate($adapter);
                    if ($result->isValid()) {
                            $this->_redirect('/index');
                     }else{
                          $messages = $result->getMessages();
                          print_r($messages[0]);
                   }
              }
              else {
                      $form->populate($formData);
              }
         }
    }
}

