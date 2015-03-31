<?php

class authController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
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
                                        echo "<br>";
                                                            echo "login".$login;
                    echo "<br>";
                    echo $password;      
                    echo "<br>";
                    $config = new Zend_Config_Ini('../application/configs/application.ini','production');
                    $log_path = $config->ldap->log_path;
                    $options = $config->ldap->toArray();
                    unset($options['log_path']);

                    $adapter = new Zend_Auth_Adapter_Ldap($options,$login,$password);

                    $auth = Zend_Auth::getInstance();
                    $result = $auth->authenticate($adapter);
                    
                    if ($result->isValid()) {
                      		$data = $authAdapter->getResultRowObject(null, 'password');
                      		$auth->getStorage()->write($data);
                      		$this->_redirect('choice');
                    }
                    
                   
                    if ($log_path) {
                        echo 'test';       
                        echo "<br>";
                        $messages = $result->getMessages();
                        print_r($messages);
                        echo "<br>";
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
                    $this->_helper->redirector('choice');
              
              } else {
                    $form->populate($formData);
              }
        }
    }

 
}

