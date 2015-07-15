<?php

define("ZDAPIKEY", "HWT4vmi2YmxsYGoV1Jy00AhSYxSt9m29ythEyO5Y");
define("ZDUSER", "laurent.tersiguel@atos.net");
define("ZDURL", "https://support.bluekiwi.net/api/v2");

class SupportController extends Zend_Controller_Action
{
    public function preDispatch()
	  {
    		$auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity())
            $this->_redirect('/auth/login');
    }
    
    public function indexAction()
    {

  //      function sortFunction( $a, $b ) {
   //         return strtotime($a->created_at) - strtotime($b->created_at);
    //    }
          //        usort($open->results, "sortFunction");

        for ($i = 10; $i >= 0; $i--) {
              $dinf = date("Y-m-d",mktime(0, 0, 0, date("m"),date("d")-$i-1,date("Y")));
              $dsup = date("Y-m-d",mktime(0, 0, 0, date("m"),date("d")-$i,date("Y")));
              $list=$this->curlWrap('/search.json?query=type:ticket,created>='.$dinf.',created<'.$dsup, null, "GET");
              $k=0;$l=0;$m=0;$n=0;$o=0;$p=0;
              if ($list->count>0){
                foreach ($list->results as $key => $item){
                    if($item->status == 'open' ) $k++;
                    if($item->status == 'solved' ) $l++;
                    if($item->status == 'pending' ) $m++;
                    if($item->priority == 'urgent' ) $n++;
                    if($item->priority == 'high' ) $o++;
                    if($item->priority == 'normal' ) $p++;
                }
              }
              $mytabo[]=array($dinf,$k);
              $mytabs[]=array($dinf,$l);
              $mytabp[]=array($dinf,$m);
              $mytabn[]=array($dinf,$n);
              $mytabo[]=array($dinf,$o);
              $mytabp[]=array($dinf,$p);
        }
        $this->view->pending=json_encode($mytabp);
        $this->view->open=json_encode($mytabo);
        $this->view->solved=json_encode($mytabs);
        $this->view->urgent=json_encode($mytabp);
        $this->view->high=json_encode($mytabo);
        $this->view->normal=json_encode($mytabs);
}
//        $data=$this->curlWrap("/tickets/recent.json", null, "GET");
//        $datat=$this->curlWrap("/tickets.json?sort_order=asc", null, "GET");

//      $org=$this->curlWrap('/incremental/users.json?start_time='.mktime(0, 0, 0, date("m"), 1,date("Y")), null, "GET");
//      foreach ($org->organizations as $key => $value)
//         print('<tr><td>'.$value->name.'</td><td> ticket created '.$value->created_at.'</td></tr>');
//      $tf=$this->curlWrap('/ticket_metrics.json', null, "GET");
//      $tf=$this->curlWrap('/audit_logs.json', null, "GET");

/*
object(stdClass)[241]
  public 'url' => string 'https://support.bluekiwi.net/api/v2/tickets/14758.json' (length=54)
  public 'id' => int 14758
  public 'external_id' => null
  public 'via' =>
    object(stdClass)[309]
      public 'channel' => string 'web' (length=3)
      public 'source' =>
        object(stdClass)[121]
          public 'from' =>
            object(stdClass)[278]
              ...
          public 'to' =>
            object(stdClass)[279]
              ...
          public 'rel' => null
  public 'created_at' => string '2015-04-20T07:19:23Z' (length=20)
  public 'updated_at' => string '2015-04-20T14:23:27Z' (length=20)
  public 'type' => string 'problem' (length=7)
  public 'subject' => string 'URGENT  : KBIN inacessible' (length=26)
  public 'raw_subject' => string 'URGENT  : KBIN inacessible' (length=26)
  public 'description' => string 'Bonjour,



Nous sommes dans l'impossibilité d’accéder à l'intranet (copie d'écran ci-joint). Toute la société est concernée.



 ( La connexion en externe est possible.)



Cdt,

Amandine







' (length=208)
  public 'priority' => string 'urgent' (length=6)
  public 'status' => string 'solved' (length=6)
  public 'recipient' => null
  public 'requester_id' => int 434613656
  public 'submitter_id' => int 434613656
  public 'assignee_id' => int 287958291
  public 'organization_id' => int 23974821
  public 'group_id' => int 64840
  public 'collaborator_ids' =>
    array (size=0)
      empty
  public 'forum_topic_id' => null
  public 'problem_id' => null
  public 'has_incidents' => boolean false
  public 'due_at' => null
  public 'tags' =>
    array (size=4)
      0 => string 'qualified' (length=9)
      1 => string 'z_authentication' (length=16)
      2 => string 'zd_elc' (length=6)
      3 => string 'zd_solved' (length=9)
  public 'custom_fields' =>
    array (size=14)
      0 =>
        object(stdClass)[277]
          public 'id' => int 198856
          public 'value' => string 'Kaufman&Broad (kb-in)' (length=21)
      1 =>
        object(stdClass)[276]
          public 'id' => int 22354397
          public 'value' => string '' (length=0)
      2 =>
        object(stdClass)[280]
          public 'id' => int 20859717
          public 'value' => string 'z_authentication' (length=16)
      3 =>
        object(stdClass)[281]
          public 'id' => int 21914802
          public 'value' => string '' (length=0)
      4 =>
        object(stdClass)[282]
          public 'id' => int 22023277
          public 'value' => string '' (length=0)
      5 =>
        object(stdClass)[283]
          public 'id' => int 23858166
          public 'value' => null
      6 =>
        object(stdClass)[284]
          public 'id' => int 22697348
          public 'value' => string 'BKINFRA-3540' (length=12)
      7 =>
        object(stdClass)[285]
          public 'id' => int 23108336
          public 'value' => boolean false
      8 =>
        object(stdClass)[286]
          public 'id' => int 23760933
          public 'value' => boolean false
      9 =>
        object(stdClass)[287]
          public 'id' => int 24060503
          public 'value' => string '' (length=0)
      10 =>
        object(stdClass)[291]
          public 'id' => int 24105516
          public 'value' => boolean false
      11 =>
        object(stdClass)[290]
          public 'id' => int 22697438
          public 'value' => string '' (length=0)
      12 =>
        object(stdClass)[288]
          public 'id' => int 24099586
          public 'value' => string '2492' (length=4)
      13 =>
        object(stdClass)[289]
          public 'id' => int 24099596
          public 'value' => string '289' (length=3)
  public 'satisfaction_rating' =>
    object(stdClass)[158]
      public 'score' => string 'unoffered' (length=9)
  public 'sharing_agreement_ids' =>
    array (size=0)
      empty
  public 'fields' =>
    array (size=14)
      0 =>
        object(stdClass)[66]
          public 'id' => int 198856
          public 'value' => string 'Kaufman&Broad (kb-in)' (length=21)
      1 =>
        object(stdClass)[69]
          public 'id' => int 22354397
          public 'value' => string '' (length=0)
      2 =>
        object(stdClass)[65]
          public 'id' => int 20859717
          public 'value' => string 'z_authentication' (length=16)
      3 =>
        object(stdClass)[64]
          public 'id' => int 21914802
          public 'value' => string '' (length=0)
      4 =>
        object(stdClass)[57]
          public 'id' => int 22023277
          public 'value' => string '' (length=0)
      5 =>
        object(stdClass)[61]
          public 'id' => int 23858166
          public 'value' => null
      6 =>
        object(stdClass)[62]
          public 'id' => int 22697348
          public 'value' => string 'BKINFRA-3540' (length=12)
      7 =>
        object(stdClass)[63]
          public 'id' => int 23108336
          public 'value' => boolean false
      8 =>
        object(stdClass)[58]
          public 'id' => int 23760933
          public 'value' => boolean false
      9 =>
        object(stdClass)[59]
          public 'id' => int 24060503
          public 'value' => string '' (length=0)
      10 =>
        object(stdClass)[60]
          public 'id' => int 24105516
          public 'value' => boolean false
      11 =>
        object(stdClass)[56]
          public 'id' => int 22697438
          public 'value' => string '' (length=0)
      12 =>
        object(stdClass)[55]
          public 'id' => int 24099586
          public 'value' => string '2492' (length=4)
      13 =>
        object(stdClass)[53]
          public 'id' => int 24099596
          public 'value' => string '289' (length=3)
  public 'ticket_form_id' => int 6188
  public 'brand_id' => int 2939866
  public 'result_type' => string 'ticket' (length=6)
*/
  public function curlWrap($url, $json, $action)
  {
  	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  	curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
  	curl_setopt($ch, CURLOPT_URL, ZDURL.$url);
  	curl_setopt($ch, CURLOPT_USERPWD, ZDUSER."/token:".ZDAPIKEY);
  	switch($action){
  		case "POST":
  			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
  			break;
  		case "GET":
  			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
  			break;
  		case "PUT":
  			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
  			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
  			break;
  		case "DELETE":
  			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
  			break;
  		default:
  			break;
  	}

    $config = new Zend_Config_Ini('../application/configs/application.ini','production');
    $certif_path = $config->certif->file_path;
    curl_setopt($ch, CURLOPT_CAINFO, $certif_path);

  	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  	curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  	$output = curl_exec($ch);
   //    echo 'Curl output: ' . curl_error($ch);
  	curl_close($ch);
  	$decoded = json_decode($output);
  	return $decoded;
    
  }

}
?>