<?php

class authController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        /*Zend_Auth::getInstance()->clearIdentity();*/
    }
 
   public function loginAction()
    { 
       date_default_timezone_set('UTC');
       $form = new Application_Form_Auth();
       $form->send->setLabel('Authenticate');
       $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
              $formData = $this->getRequest()->getPost();
              if ($form->isValid($formData)) {
                    $login = $form->getValue('login');
                    $password = $form->getValue('password');
                    $config = new Zend_Config_Ini('../application/configs/application.ini','production');
                    $log_path = $config->ldap->log_path;
                    $options = $config->ldap->toArray();
                    unset($options['log_path']);
                    $adapter = new Zend_Auth_Adapter_Ldap($options,$login,$password);
                    $auth = Zend_Auth::getInstance();
                    $result = $auth->authenticate($adapter);
                    if ($result->isValid()) {
//                      		$data = $authAdapter->getResultRowObject(null, 'password');
 //                     		$auth->getStorage()->write($data);
                      		$this->_redirect('/index');
                    }else{
                          echo 'connection refused';    
                          if ($log_path) {
                              $messages = $result->getMessages();
                              $logger = new Zend_Log();
                              $logger->addWriter(new Zend_Log_Writer_Stream($log_path));
                              $filter = new Zend_Log_Filter_Priority(Zend_Log::DEBUG);
                              $logger->addFilter($filter);
                              foreach ($messages as $i => $message) {
                                  if ($i-- > 1) {
                                      $message = str_replace("\n", "\n  ", $message);
                                      $logger->log("Ldap: $i: $message", Zend_Log::DEBUG);
                                  }
                              }
                          }
                    }
              } else {
                    $form->populate($formData);
              }       
        }
    }

 
}

