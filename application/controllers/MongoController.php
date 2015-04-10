<?php

class MongoController extends Zend_Controller_Action
{
      public function preDispatch()
      {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) 
        	$this->_redirect('/auth/login');
      }
    
      public function indexAction()
      {
        $config = new Zend_Config_Ini('../application/configs/application.ini','production');
        $m = new MongoClient($config->mongo->host); // connect 27017
        $db = $m->selectDB("analytics");
        $db->setReadPreference(MongoClient::RP_SECONDARY_PREFERRED);
        
        $timemong= new Mongodate(strtotime(date('d.m.Y')));
        $evc=$db->platformagg->distinct('infos.instance',array('date' => $timemong));
        $form = new Application_Form_Instance();
        $form->instanceName->setLabel('instanceName')->setMultiOptions($evc)->setRequired(true)->addValidator('NotEmpty', true);
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
              $formData = $this->getRequest()->getPost();
              if ($form->isValid($formData)) {
                    $evcitem = $form->getValue('instanceName');
                    $instance=$evc[$evcitem];
              } else {
                    $form->populate($formData);
                    $instance=$evc[0];
              }         
        }else{
              $instance=$evc[0];
        }
        $this->view->instance=$instance;
        $exp=array();
        for ($i = 6; $i > 0; $i--) {
          $lastmonthinf = mktime(0, 0, 0, date("m")-$i-1, 1,   date("Y"));
          $mongoinf = new Mongodate($lastmonthinf);
          $lastmonthsup = mktime(0, 0, 0, date("m")-$i, 1,   date("Y"));
          $mongosup = new Mongodate($lastmonthsup);
          $query = array('date' => array('$gte'=>$mongoinf,'$lt'=>$mongosup),'infos.privacy'=>1,'infos.instance'=>$instance,'infos.eventType' => 'read');
          $cursorptf = $db->platformagg->find($query,array('date','infos.eventType','hits'))->sort(array('date'))->limit(100);
          $sum=0;
          foreach ( $cursorptf as $id => $value )
              $sum += $value['hits'];
              $dateconv=date('Y-M', $lastmonthinf);
              $datesup=date('Y-M', $lastmonthsup);
              echo 'between '.$dateconv.' and '.$datesup.' summ is '.$sum.'<br/>';
              $exp[]=array($dateconv,$sum);
        }
        $this->view->items=json_encode($exp);
      }
          
      public function jsgetAction()
      {
          $config = new Zend_Config_Ini('../application/configs/application.ini','production');
          $m = new MongoClient($config->mongo->host); // connect 27017
          $db = $m->selectDB("analytics");
          $db->setReadPreference(MongoClient::RP_SECONDARY_PREFERRED);
          $evc=$db->spaceagg->count();
      
          $yest=date('d.m.Y',strtotime("-365 days"));
          $yestime=strtotime($yest);
          $mongoinf = new Mongodate($yestime);
      
          $today=date('d.m.Y');
          $nowd=strtotime($today);
          $mongosup = new Mongodate($nowd);
          
          $request = $this->getRequest()->getPost();
          $instance = $request['message'];

          $query = array('date' => array('$gt'=>$mongoinf,'$lt'=>$mongosup),'infos.privacy'=>0,'infos.eventType' => 'like','infos.instance'=>$instance);
          $cursorptf = $db->platformagg->find($query,array('date','infos.eventType','hits'))->sort(array('date'))->limit(100);
          $exp=array();
          foreach ( $cursorptf as $id => $value )
          {
              $dateconv=date('Y-M-d', $value['date']->sec);
              $exp[] = array($dateconv,$value['hits']);
          }
          $this->view->items=$exp;
          $this->_helper->viewRenderer->setNoRender();
          $this->_helper->getHelper('layout')->disableLayout();
          echo json_encode($exp);
      }
      
      public function jsgetpostAction()
      {
          $config = new Zend_Config_Ini('../application/configs/application.ini','production');
          $m = new MongoClient($config->mongo->host); // connect 27017
          $db = $m->selectDB("analytics");
          $db->setReadPreference(MongoClient::RP_SECONDARY_PREFERRED);
          $evc=$db->spaceagg->count();
      
          $yest=date('d.m.Y',strtotime("-365 days"));
          $yestime=strtotime($yest);
          $mongoinf = new Mongodate($yestime);
      
          $today=date('d.m.Y');
          $nowd=strtotime($today);
          $mongosup = new Mongodate($nowd);
          
          $request = $this->getRequest()->getPost();
          $instance = $request['message'];
          $query = array('date' => array('$gt'=>$mongoinf,'$lt'=>$mongosup),'infos.privacy'=>0,'infos.eventType' => 'publish','infos.instance'=>$instance);
          $cursorptf = $db->platformagg->find($query,array('date','infos.eventType','hits'))->sort(array('date'))->limit(100);
          $exp=array();
          foreach ( $cursorptf as $id => $value )
          {
              $dateconv=date('Y-M-d', $value['date']->sec);
              $exp[] = array($dateconv,$value['hits']);
          }
          $this->view->items=$exp;
          $this->_helper->viewRenderer->setNoRender();
          $this->_helper->getHelper('layout')->disableLayout();
          echo json_encode($exp);
      }
}
?>