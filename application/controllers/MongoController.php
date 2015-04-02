<?php
// Connect to two hosts

class MongoController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }
    
   public function preDispatch()
	{
		$auth = Zend_Auth::getInstance();
        		if (!$auth->hasIdentity()) {
        			$this->_redirect('/auth/login');
        }
    }
    public function indexAction()
    {
          $m = new MongoClient("localhost"); // connect 27017
          $db = $m->selectDB("analytics");
          $db->setReadPreference(MongoClient::RP_SECONDARY_PREFERRED);

          $evc=$db->spaceagg->count();
          echo 'count on spaceagg '.$evc.'<br>';

          echo '----------------platformagg-------------'.'<br>';
          /*  $query = array( 'batchId' => 'drop-instance' );*/
          /* ,'infos.eventType' => 'read'  */
          $yest=date('d.m.Y',strtotime("-30 days"));
          echo 'yest '.$yest.'<br>';;
          $yestime=strtotime($yest);
          echo 'yestime '.$yestime.'<br>';

          $today=date('d.m.Y');
          $nowd=strtotime($today);
          $mongotime = new Mongodate($yestime);
          $nowmongotime = new Mongodate($nowd);
          
          $query = array('date' => array('$gt'=>$mongotime,'$lt'=>$nowmongotime),'infos.eventType' => 'read');
          $cursorptf = $db->platformagg->find($query,array('date','infos.eventType','hits'));/*->sort(array('date));*/
          $exp=array();
          foreach ( $cursorptf as $id => $value )
          {
              /*$exp[$i]=$value['hits'];*/
              $dateconv=date('Y-M-d', $value['date']->sec);
              $exp[] = array($dateconv,$value['hits']);
          }
          
          $this->view->items=$exp;
          
/*
          foreach($this->items as $item) {
          $scriptItem=$this->escape($item->script_name);
          $instanceItem=$this->escape($item->server_name);
          $nbItem=$this->escape($item->req_count);
          $grtabdata[] = array($instanceItem.$scriptItem,(int)($nbItem*4));
          }     
*/    
          
          
/*
          echo '---------------- batch-------------'.'<br>';
          $cursorbatch = $db->batch->find();
          while ( $cursorbatch->hasNext() )
          {
              var_dump( $cursorbatch->getNext() );
          }

          $query = array( 'i' => 71 );
          $query = array(  "i" => array( '$gt' => 50 )  );
          $cursor = $db->event->find( $query );
          while ( $cursor->hasNext() )
          {
              var_dump( $cursor->getNext() );
          }

          foreach($doc as $value)
            {
               echo $value["_id"] . "\n";
            }
        $stream = fopen ('tokens.data', 'r');
          $start = time(); 
          while ($line = fgets ($stream)) {
          $doc = json_decode ($line);
          $doc->_id = $doc->id;
          unset ($doc->id);
          print "doing {$doc->_id}\n";
          $db->hop->insert($doc);
          }
          print "took: " . (time() - $start);
          fclose ($stream);
*/
          
    }
}
?>