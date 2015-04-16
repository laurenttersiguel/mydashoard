<?php
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
                    $log_path = $config->ldap->log_path;
                    $options = $config->ldap->toArray();
                    unset($options['log_path']);
                    $adapter = new Zend_Auth_Adapter_Ldap($options,$login,$password);
                    $auth = Zend_Auth::getInstance();
                    $result = $auth->authenticate($adapter);
                    if ($result->isValid()) {
                            $this->_redirect('/index');
                    }else{
                            if ($log_path) {
                                    $messages = $result->getMessages();
                                    print_r($messages[0]);
                            }
                    }
            }
            else {
                    $form->populate($formData);
            }
      }
    }
}

//Update an existing entry on the LDAP

////$options = array(/* ... */);
//$ldap = new Zend\Ldap\Ldap($options);
//$ldap->bind();
//$hm = $ldap->getEntry('cn=Hugo Müller,ou=People,dc=my,dc=local');
//Zend\Ldap\Attribute::setAttribute($hm, 'mail', 'mueller@my.local');
//Zend\Ldap\Attribute::setPassword($hm,
//                                 'newPa$$w0rd',
//                                 Zend\Ldap\Attribute::PASSWORD_HASH_SHA1);
//$ldap->update('cn=Hugo Müller,ou=People,dc=my,dc=local', $hm);
