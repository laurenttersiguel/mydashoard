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

/*
db.spaceagg.find({"_id" : ObjectId("53c93c99fcd9623aed5b7bb3")});

{ "_id" : ObjectId("53c93c99fcd9623aed5b7bb3"), "date" : ISODate("2012-09-19T00:00:00Z"), 
"infos" : { "eventType" : "createUser", "instance" : "zenstg" }, "hits" : NumberLong(9) }


array (size=4)
  '_id' => 
    object(MongoId)[56]
      public '$id' => string '53c93c99fcd9623aed5b7bb3' (length=24)
  'date' => 
    object(MongoDate)[57]
      public 'sec' => int 1348012800
      public 'usec' => int 0
  'infos' => 
    array (size=2)
      'eventType' => string 'createUser' (length=10)
      'instance' => string 'zenstg' (length=6)
  'hits' => int 9
  
  */
    
    public function indexAction()
    {
          $m = new MongoClient("localhost"); // connect 27017
          $db = $m->selectDB("analytics");
          $db->setReadPreference(MongoClient::RP_SECONDARY_PREFERRED);

          $evc=$db->spaceagg->count();
          echo 'count on spaceagg '.$evc.'<br>';

          echo '---------------- platformagg-------------'.'<br>';
          $doc = $db->platformagg->findOne();
          var_dump($doc);          
          

          echo '----------------platformagg-------------'.'<br>';
/*          $query = array( 'batchId' => 'drop-instance' );*/
          /* ,'infos.eventType' => 'read'  */
          $yest=date('d.m.Y',strtotime("-1 days"));
          echo 'yest'.$yest.'<br>';;
          $yestime=strtotime($yest);
          echo 'yestime'.$yestime.'<br>';

          $today=date('d.m.Y');
          $nowd=strtotime($today);
          echo $nowd;          
          $mongotime = new Mongodate($yestime);
          $nowmongotime = new Mongodate($nowd);
          
          var_dump($mongotime);
          $query = array('date' => array('$gt'=>$mongotime,'$lt'=>$nowmongotime),'infos.eventType' => 'read');
          $cursorptf = $db->platformagg->find($query,array('date','infos.eventType','hits'));
          foreach ( $cursorptf as $id => $value )
          {
              print_r($value);
              echo '<br>';
              echo "$id: ".'<br>';
              echo $value['infos']['eventType'].'<br>'; 
              echo $value['hits'].'<br>'; 
          }

          echo '---------------- batch-------------'.'<br>';
          $cursorbatch = $db->batch->find();
          while ( $cursorbatch->hasNext() )
          {
              var_dump( $cursorbatch->getNext() );
          }

/*
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