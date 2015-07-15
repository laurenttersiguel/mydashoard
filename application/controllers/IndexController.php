<?php

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
}    
