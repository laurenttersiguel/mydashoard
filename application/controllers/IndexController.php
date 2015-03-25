<?php

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }
    public function indexAction()
    {   
        $dbobj = new Application_Model_DbTable_Report();
        $this->view->items = $dbobj->getRequestAll();
        
    }
    public function putThisinjson()
    {
        $dbobj = new Application_Model_DbTable_Report();
        $mytab = $dbobj->fetchAll();
        print(json_encode($mytab));   
    }
    public function figureAction() 
    {
        $dbobj = new Application_Model_DbTable_Report();
        $item = $dbobj->getRequestByScript("/login");
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        echo $item->req_count;
    }
    public function figure2Action() 
    {
        $dbobj = new Application_Model_DbTable_Report();
        $item = $dbobj->getRequestByScript("/notification/updater");
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

