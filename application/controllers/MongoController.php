<?php
class MongoController extends Zend_Controller_Action
{
      public function preDispatch()
      {
        $auth = Zend_Auth::getInstance();
//        if (!$auth->hasIdentity()) 
  //      	$this->_redirect('/auth/login');
      }
    
      public function indexAction()
      {

            $config = new Zend_Config_Ini('../application/configs/application.ini','production');
            $mongoClient = new MongoClient($config->mongo->host); // connect 27017
            $db = $mongoClient->selectDB("analytics");
            $db->setReadPreference(MongoClient::RP_SECONDARY_PREFERRED);
            //get distinct instance    //$timemong= new Mongodate(strtotime(date('d.m.Y')));
            $instanceList=$db->platformagg->distinct('infos.instance'/*,array('date' => $timemong)*/);
            //fill the form
            $form = new Application_Form_Instance();
            $form->instanceSelect->setLabel('choose instance')->setMultiOptions($instanceList)->setRequired(true)->addValidator('NotEmpty', true);
            $this->view->form = $form;
            if ($this->getRequest()->isPost()) {
                  $formData = $this->getRequest()->getPost();
                  if ($form->isValid($formData)) {
                        $item = $form->getValue('instanceSelect');
                        $instanceChosen=$instanceList[$item];
                  } else {
                        $form->populate($formData);
                        $instanceChosen=$instanceList[0];
                  }
            }else{
                  $instanceChosen=$instanceList[0];
            }
            $this->view->instance=$instanceChosen;

            //side bar list
            $monthPrev = new Mongodate(mktime(0, 0, 0, date("m")-1, 1,date("Y")));
            $monthCurr = new Mongodate(mktime(0, 0, 0, date("m"), 1,date("Y")));
            foreach ( $instanceList as $id => $value ) {
                $query = array('date' => array('$gte'=>$monthPrev,'$lt'=>$monthCurr),
                    'infos.sourceValue'=>array('$ne'=>0),'infos.instance'=>$value,'infos.eventType' => 'read');
                $count = $db->platformagg->count($query);
                $activeTab[]=array($value,$count);
            }
            $this->view->currentitems=$activeTab;

            //Active Members
            for ($i = 99; $i >= 0; $i--) {
                $dinf = mktime(0, 0, 0, date("m")-$i-1,1,date("Y"));
                $dateconv=date('Y-M', $dinf);
                $mongoDinf = new Mongodate($dinf);
                $dsup = mktime(0, 0, 0, date("m")-$i,1,date("Y"));
                $mongoDsup = new Mongodate($dsup);
                //TODO : Add "count>=license_value
                $queryHisto = array('date' => array('$gte'=>$mongoDinf,'$lt'=>$mongoDsup),
                                       'infos.sourceValue'=>array('$ne'=>0),
                                       'infos.instance'=>$instanceChosen,
                                       'infos.eventType' => 'read');
                //COUNT COLUTION
                // $histoCount = $db->platformagg->count($queryHisto);
                //GROUP BY SOLUTION
                $cursorgrp=$db->platformagg->group(
                        array('infos.sourceValue'=>true),
                        array('count' => 0),
                        "function (obj, prev) { prev.count++; }",
                        array('condition'=>$queryHisto)
                        );
                $histoCount=$cursorgrp['count'];
                // echo 'between '.$dateconv.' and '.$datesup.' summ is '.$histoCount.'<br/>';
                $activeMemberTab[]=array($dateconv,$histoCount);
            }
            $this->view->items=json_encode($activeMemberTab);

      }

//BLUEKARMA CODE
/*
coll.aggregate(
$match(MongoDBObject("infos.instance"    -> ctx.instanceId,
                     "infos.eventType"   -> ctx.eventType,
                     "infos.sourceValue" -> MongoDBObject("$ne" -> 0))
       ++( "date" $gte startOfDay(ctx.startDate))),
$group(MongoDBObject("year"  -> MongoDBObject("$year"  -> "$date"),
                     "month" -> MongoDBObject("$month" -> "$date"),
                     "user"  -> "$infos.sourceValue"),
        $histoCount),
$match(MongoDBObject("count" -> MongoDBObject("$gte" -> ctx.licenseValue))),
$group(MongoDBObject("year" -> "$_id.year",
                     "month" -> "$_id.month"),
        $histoCount)
)
// Some "views" are made by connectors with sourceValue=0. Must not counted
// Summing on the number of days where user has had at least a hit
// Warning: do not sum on "$hits". We do not want the total hit count on the month
*/

      public function getAction($iC,$action)
      {
          $config = new Zend_Config_Ini('../application/configs/application.ini','production');
          $mongoClient = new MongoClient($config->mongo->host); // connect 27017
          $db = $mongoClient->selectDB("analytics");
          $db->setReadPreference(MongoClient::RP_SECONDARY_PREFERRED);
          $dinf = new Mongodate(strtotime(date('d.m.Y',strtotime("-365 days"))));
          $dsup = new Mongodate(strtotime(date('d.m.Y')));
          $query = array('date' => array('$gt'=>$dinf,'$lt'=>$dsup),
                         'infos.privacy'=>0,
                         'infos.eventType' => $action,
                         'infos.instance'=>$iC);
          $cursorptf = $db->platformagg->find($query,array('date','infos.eventType','hits'))->sort(array('date'))->limit(100);
          $myActionTab=array();
          foreach ( $cursorptf as $id => $value )
              $myActionTab[] = array(date('Y-M-d', $value['date']->sec),$value['hits']);
          $this->view->items=$myActionTab;
          $this->_helper->viewRenderer->setNoRender();
          $this->_helper->getHelper('layout')->disableLayout();
          return $myActionTab;
      }

      public function jsgetAction()
      {
          $request = $this->getRequest()->getPost();
          $instanceChosen = $request['message'];
          echo json_encode($this->getAction($instanceChosen,'like'));
      }

      public function jsgetpostAction()
      {
          $request = $this->getRequest()->getPost();
          $instanceChosen = $request['message'];
          echo json_encode($this->getAction($instanceChosen,'publish'));
      }
}
?>
