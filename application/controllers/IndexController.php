<?php

class IndexController extends Zend_Controller_Action
{


	public function preDispatch()
	{
		$auth = Zend_Auth::getInstance();
    if (!$auth->hasIdentity()) {
    	$this->_redirect('/auth/login');
    }
      $response = $this->getResponse();
        $response->insert('sidebar', $this->view->render('sidebar.phtml'));       /* Initialize action controller here */
 date_default_timezone_set('UTC');


  }
    
  
    
    public function indexAction(){
     
		$auth = Zend_Auth::getInstance();
     $id= $auth->getIdentity();
     echo 'user connected '.$id; 
    
    }
    
    public function listAction(){

          $config = new Zend_Config_Ini('../application/configs/application.ini','production');
          $options = $config->ldap->server->toArray();
          $ldapcnct = new Zend_Ldap($options);
          if($ldapcnct === false) {
               throw new \Exception("failed to connect to ldap server.");
          }
          /*
          $acctname = $ldapcnct->getCanonicalAccountName('lmm',
                                                     Zend_Ldap::ACCTNAME_FORM_USERNAME);
          echo "$acctname\n";
          */
          /*$ldap->bind();*/

/*
          $hm = $ldapcnct->getEntry('uid=lmm,ou=people,dc=bluekiwi-software,dc=com');
          foreach($hm as $hfg) {
              echo $hfg[0];
              echo "<br>";
          }
*/          
          $results = $ldapcnct->search('(uid=*)',
                                  'dc=bluekiwi-software,dc=com',
                                  'sub');
                                  echo '<table>';
          echo '<tr>';
          echo '<td>'.'COMMON NAME'.'</td>'.'<td>'.'GIVEN NAME'.'</td>'.'<td>'.'MAIL'.'</td>' ;
          echo '</tr>';
                               
          foreach ($results as $item) {
              echo '<tr>';
              echo '<td>'.$item['cn'][0].'</td>'.'<td>'.$item['givenname'][0].'</td>'.'<td>'.$item['mail'][0].'</td>' ;
              echo '</tr>';
          }
          echo '</table>';
    }
}
/*
array (size=10)
  'cn' => 
    array (size=1)
      0 => string 'Administrateur LDAP blueKiwi' (length=28)
  'dn' => string 'uid=adminldap,ou=people,dc=bluekiwi-software,dc=com' (length=51)
  'givenname' => 
    array (size=1)
      0 => string 'Administrateur' (length=14)
  'mail' => 
    array (size=1)
      0 => string 'admin@bluekiwi-software.com' (length=27)
  'o' => 
    array (size=1)
      0 => string 'bluekiwi-software' (length=17)
  'objectclass' => 
    array (size=1)
      0 => string 'inetOrgPerson' (length=13)
  'preferredlanguage' => 
    array (size=1)
      0 => string 'en' (length=2)
  'sn' => 
    array (size=1)
      0 => string 'blueKiwi' (length=8)
  'uid' => 
    array (size=1)
      0 => string 'adminldap' (length=9)
  'userpassword' => 
    array (size=1)
      0 => string 'fodovamei' (length=9)
      */
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


