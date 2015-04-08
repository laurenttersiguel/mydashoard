<?php

class MongoController extends Zend_Controller_Action
{
    
       public function preDispatch()
      {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
        	$this->_redirect('/auth/login');
        }
      }
    
      public function indexAction()
      {
        date_default_timezone_set('UTC');
        $m = new MongoClient("localhost"); // connect 27017
        $db = $m->selectDB("analytics");
        $db->setReadPreference(MongoClient::RP_SECONDARY_PREFERRED);
        
        $evc=$db->platformagg->distinct('infos.instance');
        
        $form = new Application_Form_Instance();
        
        $form->send->setLabel('Instance',array('date' =>new Mongodate(time())));
        $form->id->setLabel('id')->setMultiOptions($evc)->setRequired(true)->addValidator('NotEmpty', true);
        
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
              $formData = $this->getRequest()->getPost();
              if ($form->isValid($formData)) {
                    $instance = $form->getValue('instance');
                            } else {
                    $form->populate($formData);
              }         
        }
        $exp=array();
        for ($i = 10; $i > 0; $i--) {
          $lastmonthinf = mktime(0, 0, 0, date("m")-$i-1, 1,   date("Y"));
          $mongoinf = new Mongodate($lastmonthinf);
          $lastmonthsup = mktime(0, 0, 0, date("m")-$i, 1,   date("Y"));
          $mongosup = new Mongodate($lastmonthsup);
          $query = array('date' => array('$gte'=>$mongoinf,'$lt'=>$mongosup),'infos.privacy'=>1,'infos.instance'=>'zenstg','infos.eventType' => 'read');
          $cursorptf = $db->platformagg->find($query,array('date','infos.eventType','hits'))->sort(array('date'))->limit(100);
          $sum=0;
          foreach ( $cursorptf as $id => $value ){
              $sum += $value['hits'];
              $dateconv=date('Y-M', $lastmonthinf);
              $datesup=date('Y-M', $lastmonthsup);
              /*echo 'between '.$dateconv.' and '.$datesup.' summ is '.$sum.'<br/>';*/
              $exp[]=array($dateconv,$sum);
              }
        }
        $this->view->items=json_encode($exp);
      }
          
      public function jsgetAction()
      {
          $m = new MongoClient("localhost"); // connect 27017
          $db = $m->selectDB("analytics");
          $db->setReadPreference(MongoClient::RP_SECONDARY_PREFERRED);
          $evc=$db->spaceagg->count();
      
          $yest=date('d.m.Y',strtotime("-365 days"));
          $yestime=strtotime($yest);
          $mongoinf = new Mongodate($yestime);
      
          $today=date('d.m.Y');
          $nowd=strtotime($today);
          $mongosup = new Mongodate($nowd);
          
          $query = array('date' => array('$gt'=>$mongoinf,'$lt'=>$mongosup),'infos.privacy'=>0,'infos.eventType' => 'read');
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
            public function jsgetbAction()
      {
          $m = new MongoClient("localhost"); // connect 27017
          $db = $m->selectDB("analytics");
          $db->setReadPreference(MongoClient::RP_SECONDARY_PREFERRED);
          $evc=$db->spaceagg->count();
      
          $yest=date('d.m.Y',strtotime("-365 days"));
          $yestime=strtotime($yest);
          $mongoinf = new Mongodate($yestime);
      
          $today=date('d.m.Y');
          $nowd=strtotime($today);
          $mongosup = new Mongodate($nowd);
          
          $query = array('date' => array('$gt'=>$mongoinf,'$lt'=>$mongosup),'infos.privacy'=>0,'infos.eventType' => 'read');
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