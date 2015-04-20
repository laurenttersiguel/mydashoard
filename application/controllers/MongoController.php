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
        $form->instanceSelect->setLabel('choose instance')->setMultiOptions($evc)->setRequired(true)->addValidator('NotEmpty', true);
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
              $formData = $this->getRequest()->getPost();
              if ($form->isValid($formData)) {
                    $evcitem = $form->getValue('instanceSelect');
                    $instance=$evc[$evcitem];
              } else {
                    $form->populate($formData);
                    $instance=$evc[0];
              }         
        }else{
              $instance=$evc[0];
        }
        $this->view->instance=$instance;
        
        $currentmonthinf = new Mongodate(mktime(0, 0, 0, date("m")-1, 1,date("Y")));
        $currentmonthsup = new Mongodate(mktime(0, 0, 0, date("m"), 1,date("Y")));
        foreach ( $evc as $id => $value ) {
            $currentquery = array('date' => array('$gte'=>$currentmonthinf,'$lt'=>$currentmonthsup),
                'infos.sourceValue'=>array('$ne'=>0),'infos.instance'=>$value,'infos.eventType' => 'read');
            $currentcursorptf = $db->platformagg->count($currentquery);
            $currentexp[]=array($value,$currentcursorptf);
        }
        $this->view->currentitems=$currentexp;

          
        for ($i = 99; $i >= 0; $i--) {
          $lastmonthinf = mktime(0, 0, 0, date("m")-$i-1,1,date("Y"));
          $mongoinf = new Mongodate($lastmonthinf);
          $lastmonthsup = mktime(0, 0, 0, date("m")-$i,1,date("Y"));
          $mongosup = new Mongodate($lastmonthsup);
          $query = array('date' => array('$gte'=>$mongoinf,'$lt'=>$mongosup),
                        'infos.sourceValue'=>array('$ne'=>0),'infos.instance'=>$instance,'infos.eventType' => 'read');
          //'infos.privacy'=>1,

//PREVIOUSLY
//          $extract = $db->useragg->find($query,array('date','infos.eventType','hits','infos.sourceValue'))->sort(array('date'))->limit(100);
//          foreach ( $extract as $id => $value )
//              echo $value['infos']['sourceValue'].'<br/>';

          $sum = $db->platformagg->count($query);

          $dateconv=date('Y-M', $lastmonthinf);
          $datesup=date('Y-M', $lastmonthsup);
//        echo 'between '.$dateconv.' and '.$datesup.' summ is '.$sum.'<br/>';
          $exp[]=array($dateconv,$sum);
        }
        $this->view->items=json_encode($exp);
      }

//GROUP BY SOLUTION
//          $cursorgrp=$db->platformagg->group(
//                          array('infos.sourceValue'=>true),
//                          array('count' => 0),
//                          "function (obj, prev) { prev.count++; }",
//                          array('condition'=>$query)
//                          );
//echo $cursorgrp['count'].'<br/>';
//
// CODE EXAMPLE
//$m = new Mongo();
//$db = $m->selectDB('test');
//$collection = new MongoCollection($db, 'FooBar');
// grouping results by categories, where foo is 'bar'
//$keys = array('categorie'=>true, 'foo'=>true); // the fields list we want to return
//$initial = array('count' => 0); // gets a subtotal for each categorie
//$reduce = "function(obj,prev) { prev.count += 1; }"; // yes, this is js code
//$conditions = array('foo'=> 'bar');
//$grouped = $myColl::group($keys, $initial, $reduce, array('condition'=>$conditions));
//$result = $grouped['retval'];
                          
        
        
//       scala code
//coll.aggregate(
//$match(MongoDBObject("infos.instance"  -> ctx.instanceId,
//                    "infos.eventType" -> ctx.eventType,
//                    // Some "views" are made by connectors with sourceValue=0. Must not counted
//                    "infos.sourceValue" -> MongoDBObject("$ne" -> 0))
//                ++( "date" $gte startOfDay(ctx.startDate))),
//$group(MongoDBObject("year"  -> MongoDBObject("$year"       -> "$date"),
//                    "month" -> MongoDBObject("$month"      -> "$date"),
//                    "user"  -> "$infos.sourceValue"),
// // Summing on the number of days where user has had at least a hit
// // Warning: do not sum on "$hits". We do not want the total hit count on the month
// $sum),
//$match(MongoDBObject("count" -> MongoDBObject("$gte" -> ctx.licenseValue))),
//$group(MongoDBObject("year" -> "$_id.year","month" -> "$_id.month"),$sum)
//)

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