<?php

define("ZDAPIKEY", "HWT4vmi2YmxsYGoV1Jy00AhSYxSt9m29ythEyO5Y");
define("ZDUSER", "laurent.tersiguel@atos.net");
define("ZDURL", "https://support.bluekiwi.net/api/v2");

class SupportController extends Zend_Controller_Action
{
    public function preDispatch()
	  {
    		$auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) $this->_redirect('/auth/login');
    }
    
    public function indexAction()
    {

  //      function sortFunction( $a, $b ) {
   //         return strtotime($a->created_at) - strtotime($b->created_at);
    //    }

        for ($i = 10; $i >= 0; $i--) {
                    $dinf = date("Y-m-d",mktime(0, 0, 0, date("m")-$i-1,1,date("Y")));
                    $dsup = date("Y-m-d",mktime(0, 0, 0, date("m")-$i,1,date("Y")));
                  $open=$this->curlWrap('/search.json?query=status:open,created>'.$dinf.',created<'.$dsup, null, "GET");
                  $solved=$this->curlWrap('/search.json?query=status:solved,created>'.$dinf.',created<'.$dsup, null, "GET");
                  $pending=$this->curlWrap('/search.json?query=status:pending,created>'.$dinf.',created<'.$dsup, null, "GET");
          //        usort($pending->results, "sortFunction");
                  $k=0;
//TO SUBSTITUTE                echo $pending->count
//TO SUBSTITUTE                echo $pending->results[0]->status

                  foreach ($pending->results as $key => $item){
                      $k++;
                      $itc=$item->created_at;
                      $itcd=date("d.m.Y", strtotime($itc));
                  }
                   $mytabp[]=array($dinf,$k);

          //        usort($open->results, "sortFunction");
                  $k=0;
                  foreach ($open->results as $key => $item){
                      $k++;
                      $itc=$item->created_at;
                      $itcd=date("d.m.Y", strtotime($itc));
                  }
                  $mytabo[]=array($dinf,$k);

          //        usort($solved->results, "sortFunction");
                  $k=0;
                  foreach ($solved->results as $key => $item){
                      $k++;
                      $itc=$item->created_at;
                      $itcd=date("d.m.Y", strtotime($itc));
                  }
                  $mytabs[]=array($dinf,$k);

        }
        $this->view->pending=json_encode($mytabp);
        $this->view->open=json_encode($mytabo);
        $this->view->solved=json_encode($mytabs);

}
/*        $di=$this->curlWrap('/incremental/tickets.json?start_time='.mktime(0, 0, 0, date("m"), 1,date("Y")), null, "GET");
        $i=0;
        foreach ($di->tickets as $key => $item){
            $i++;
            $itc=$item->created_at;
            $itcd=date("d.m.Y", strtotime($itc));
            $mytab[]=array($itcd,$i);
        }
        function sortFunction( $a, $b ) {
            return strtotime($a[0]) - strtotime($b[0]);
        }
        usort($mytab, "sortFunction");
        $this->view->tgraph=json_encode($mytab);
*/
//        $data=$this->curlWrap("/tickets/recent.json", null, "GET");
//        $datat=$this->curlWrap("/tickets.json?sort_order=asc", null, "GET");

//      $org=$this->curlWrap('/incremental/users.json?start_time='.mktime(0, 0, 0, date("m"), 1,date("Y")), null, "GET");
//      foreach ($org->organizations as $key => $value)
//         print('<tr><td>'.$value->name.'</td><td> ticket created '.$value->created_at.'</td></tr>');
//      $tf=$this->curlWrap('/ticket_metrics.json', null, "GET");
//      $tf=$this->curlWrap('/audit_logs.json', null, "GET");

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