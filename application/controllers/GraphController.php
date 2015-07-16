<?php

class GraphController extends Zend_Controller_Action
{
    
    public function preDispatch()
	  {
    		$auth = Zend_Auth::getInstance();
//        if (!$auth->hasIdentity()) 
  //        	$this->_redirect('/auth/login');
    }
    
    public function indexAction()
    {
        $this->view->title = "live customer usage";
        $this->view->headTitle($this->view->title, 'PREPEND');
    
        $dbobj = new Application_Model_DbTable_Report();
        $evc = $dbobj->getRequestDistinct();
        if (!$evc)  throw new Exception('Invalid table data gateway provided');
        $abscisServer=array();
        foreach($evc as $item) {
            $instanceItem=$item->server_name;
            $abscisServer[]=$instanceItem;
        }     
        $form = new Application_Form_Instance();
        $form->instanceSelect->setLabel('choose instance')->setMultiOptions($abscisServer)->setRequired(true)->addValidator('NotEmpty', true);
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
              $formData = $this->getRequest()->getPost();
              if ($form->isValid($formData)) {
                    $evcitem = $form->getValue('instanceSelect');
                    $instance=$abscisServer[$evcitem];
              } else {
                    $form->populate($formData);
                    $instance=$abscisServer[0];
              }         
        }else{
              $instance=$abscisServer[0];
        }
        $this->view->items = $dbobj->getRequestAll($instance);
    }

     public function putThisinjson()
    {
        $dbobj = new Application_Model_DbTable_Report();
        $mytab = $dbobj->fetchAll();
        print(json_encode($mytab));   
    }
    public function figureAction() 
    {
        $request = $this->getRequest()->getPost();
        $message = $request['message'];
        $message2 = $request['message2'];
        $dbobj = new Application_Model_DbTable_Report();
        $item = $dbobj->getRequestByScript($message,$message2);
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        echo $item->req_count;
    }
    public function figure2Action() 
    {
        $request = $this->getRequest()->getPost();
        $message = $request['message'];
        $message2 = $request['message2'];
        $dbobj = new Application_Model_DbTable_Report();
        $item = $dbobj->getRequestByScript($message,$message2);
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        echo $item->req_count;
    }      
    public function randomAction()
    {
        $request = $this->getRequest()->getPost();
        $message = $request['message'];
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $x = time() * 1000;
        $y = rand(0, 100);
        $ret = array($x, $y);
        echo json_encode($ret);
    }
    public function buttonAction() 
    {
        $request = $this->getRequest()->getPost();
        $message = $request['message'];
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        echo $message;
    }
 
}

