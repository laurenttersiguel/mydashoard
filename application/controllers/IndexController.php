<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {

       $form = new Application_Form_Auth();
       $form->send->setLabel('Authentificate');
       $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
      
              $formData = $this->getRequest()->getPost();
              
              if ($form->isValid($formData)) {

                    $user = $form->getValue('user');
                    $password = $form->getValue('password');
 
/*paste from here*/                    
                    $auth = Zend_Auth::getInstance();
                    $config = new Zend_Config_Ini('../application/configs/application.ini','production');
                    $log_path = $config->ldap->log_path;
                    $options = $config->ldap->toArray();
                    unset($options['log_path']);
                    $adapter = new Zend_Auth_Adapter_Ldap($options,$user,$password);
                    $result = $auth->authenticate($adapter);
                    if ($log_path) {
                        $messages = $result->getMessages();
echo $messages;
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
/*paste from here*/     
                    
                   /* $ds=ldap_connect("10.110.0.25"); 
                      if ($ds) {
                      echo "connection $ds";
                      if (ldap_bind($ds)) {
                          $dn = "uid=$user , dc=Bluekiwi, dc=fr"; 
                          $value = "{MD5}".base64_encode(pack('H*' ,md5($password)));
                          $attr = "userpassword";
                          $r=ldap_compare($ds, $dn, $attr, $value);
                          if ($r === -1) {
                              echo "Erreur : ".ldap_error($ds);
                              return 1;
                          } elseif ($r === TRUE) {
                              echo "correct password";
                          } elseif ($r === FALSE) {
                              echo "incorect password";
                            }
                      } else {
                            echo "no bind connection";
                      }
                      ldap_close($ds);
                    } else {
                        echo "no connection";
                    }*/
                    
                    
                    
                    
                    
                    $this->redirect('graph');

              } else {

                    $form->populate($formData);

              }
        }
    }
}

