<?php

class IndexController extends Zend_Controller_Action
{
  	public function preDispatch()
  	{
          $auth = Zend_Auth::getInstance();
          if (!$auth->hasIdentity()) 
            $this->_redirect('/auth/login');
    }
    
    public function indexAction(){
          $auth = Zend_Auth::getInstance();
          $id= $auth->getIdentity();
          $this->view->user_connected=$id; 
          $this->view->title = "Live Customer";
          $this->view->headTitle($this->view->title, 'PREPEND');
    }
    
    public function listAction(){

          $config = new Zend_Config_Ini('../application/configs/application.ini','production');
          $options = $config->ldap->server->toArray();
          $ldapcnct = new Zend_Ldap($options);
          if($ldapcnct === false) {
               throw new \Exception("failed to connect to ldap server.");
          }

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


