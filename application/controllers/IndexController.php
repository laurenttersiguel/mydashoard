<?php
require_once 'MyStock.php';


class IndexController extends Zend_Controller_Action
{

  	public function preDispatch()
  	{
          $auth = Zend_Auth::getInstance();
          if (!$auth->hasIdentity()) 
            $this->_redirect('/auth/login');
    }

    public function clearAction(){
        $auth=Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/auth/login');
    }

    public function indexAction(){
        $auth=Zend_Auth::getInstance();
//        $sto=$auth->getStorage();
//        $temp= $sto->getPassword();
        $id= $auth->getIdentity();
        $this->view->user_connected=$id;
    }
    
    public function listAction(){

          $config = new Zend_Config_Ini('../application/configs/application.ini','production');
          $options = $config->ldap->server->toArray();
          $ldapcnct = new Zend_Ldap($options);
          if($ldapcnct === false) {
               throw new \Exception("failed to connect to ldap server.");
          }
          $results = $ldapcnct->search('(uid=*)','dc=bluekiwi-software,dc=com','sub');
          echo '<table style="font-size:12px;">';
          echo '<tr>'.'<td>'.'COMMON NAME'.'</td>'.'<td>'.'GIVEN NAME'.'</td>'.'<td>'.'SINGLE NAME'.'</td>'.'<td>'.'MAIL'.'</td>'.'</tr>';
                               
          foreach ($results as $item) {
              echo '<tr>'.'<td>'.$item['cn'][0].'</td>'.'<td>'.$item['givenname'][0].'</td>'.'<td>'.$item['sn'][0].'</td>'.'<td>'.$item['mail'][0].'</td>'.'</tr>';
          }
          echo '</table>';
          var_dump($item);
    }
      public function updateAction(){

              $auth=Zend_Auth::getInstance();
              $config = new Zend_Config_Ini('../application/configs/application.ini','production');
              $options = $config->ldap->server->toArray();
              $ldapcnct = new Zend_Ldap($options);
              $current = $ldapcnct->getEntry('uid='.$auth->getIdentity().',ou=people,dc=bluekiwi-software,dc=com');
              $form = new Application_Form_Update();
              $form->login->setValue($auth->getIdentity());
              $form->mail->setValue($current['mail'][0]);
              $form->givenname->setValue($current['givenname'][0]);
              $form->sn->setValue($current['sn'][0]);
              if ($current['mobile'][0]!=null) $form->mobile->setValue($current['mobile'][0]);
              //TODO title ou
              $this->view->form = $form;
              if ($this->getRequest()->isPost()) {
                    $formData = $this->getRequest()->getPost();
                    if ($form->isValid($formData)) {
                          Zend_Ldap_Attribute::setAttribute($current, 'mail', $form->getValue('mail'));
                          Zend_Ldap_Attribute::setAttribute($current, 'mobile', $form->getValue('mobile'));
                          //Zend\Ldap\Attribute::setPassword($form->getValue('password'), 'newPa$$w0rd', Zend\Ldap\Attribute::PASSWORD_HASH_SHA1);
                          $ldapcnct->update('uid='.$auth->getIdentity().',ou=people,dc=bluekiwi-software,dc=com', $current);
                          $this->_redirect('/index/list');
                    } else {
                          $form->populate($formData);
                    }
              }

      }
}

/*
   'cn' =>    array (size=1)
  'dn' => string  (length=45)
  'givenname' =>    array (size=1)
  'mail' =>    array (size=1)
  'mobile' =>    array (size=1)
  'o' =>    array (size=1)
  'objectclass' =>    array (size=1)
  'ou' =>    array (size=1)
  'preferredlanguage' =>    array (size=1)
  'sn' =>    array (size=1)
  'title' =>    array (size=1)
  'uid' =>    array (size=1)
  'userpassword' =>    array (size=1)
*/


