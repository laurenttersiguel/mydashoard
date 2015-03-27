<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
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
                    $auth = Zend_Auth::getInstance();
                    $config = new Zend_Config_Ini('../application/configs/application.ini','production');
                    $log_path = $config->ldap->log_path;
                    $options = $config->ldap->toArray();
                    unset($options['log_path']);
                    $adapter = new Zend_Auth_Adapter_Ldap($options,$login,$password);
                    $result = $auth->authenticate($adapter);
                    if ($result === false){
                    echo 'result is false';
                    }else
                    {
                    echo 'result is true';
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
    
    public function choiceAction(){
    
    }
    
    public function listAction(){

          $options = array(
          'host'              => 'localhost',
          'port'             => '389',
          'username'          => 'cn=admin,dc=bluekiwi-software,dc=com',
          'password'          => '',
          'bindRequiresDn'    => false,
          'accountDomainName' => 'bluekiwi-software.com',
          'baseDn'            => 'ou=people,dc=bluekiwi-software,dc=com',
          );
          
          $ldapcnct = new Zend_Ldap($options);
          if($ldapcnct === false) {
               throw new \Exception("failed to connect to ldap server.");
          }
          echo "<br>";
          /*
          $acctname = $ldapcnct->getCanonicalAccountName('lmm',
                                                     Zend_Ldap::ACCTNAME_FORM_USERNAME);
          echo "$acctname\n";
          */
          /*$ldap->bind();*/
          $hm = $ldapcnct->getEntry('uid=lmm,ou=people,dc=bluekiwi-software,dc=com');
          foreach($hm as $hfg) {
              echo $hfg[0];
              echo "<br>";
          }
          
          $result = $ldapcnct->search('(uid=*)',
                                  'dc=bluekiwi-software,dc=com',
                                  'sub');
          foreach ($result as $item) {
              echo $item["dn"] . ': ' . $item['uid'][0] . PHP_EOL;
          }
          
    }

}
/*
$ldapconn = ldap_connect('localhost','389');
if($ldapconn === false) {
     throw new \Exception("failed to connect to ldap server.");
}
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
$ldapbind = ldap_bind($ldapconn,'cn=admin,dc=bluekiwi-software,dc=com','XXXXXXXX');
if (!$ldapbind) {
    throw new \Exception("failed to connect to ldap server - invalid credentials.");
}
$ldapAttributes=array('title','mail','o','cn','sn','ou');
$ldapResult = ldap_search($ldapconn, 'ou=people,dc=bluekiwi-software,dc=com', 'uid=lmm', $ldapAttributes);
$info = ldap_get_entries($ldapconn, $ldapResult);
if ($info["count"] != 1) {
    throw new \Exception("failed to get info.");
}
ldap_close($ldapconn);
$res = array();
foreach($ldapAttributes as $ldapAttribute) {
    echo $info[0][(string)$ldapAttribute][0];
}
*/

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

/*$this->redirect('graph');*/


