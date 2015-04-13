<?php


define("ZDAPIKEY", "HWT4vmi2YmxsYGoV1Jy00AhSYxSt9m29ythEyO5Y");
define("ZDUSER", "laurent.tersiguel@atos.net");
define("ZDURL", "https://support.bluekiwi.net/api/v2");

/* Note: do not put a trailing slash at the end of v2 */


class SupportController extends Zend_Controller_Action
{
    
    public function preDispatch()
	  {
    		$auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) 
          	$this->_redirect('/auth/login');
    }
    
  //curl -u laurent.tersiguel@atos.net/token:YOUR_TOKEN https://support.bluekiwi.net/api/v2/users/me.json

      public function indexAction()
    {///tickets/recent.json
    $data=$this->curlWrap("/tickets/recent.json", null, "GET");
    //print("Subject = " . $data->groups[0]->name );
    //var_dump($data);
    print("Subject = " . $data->tickets[0]->raw_subject.'<br/>');
    print("Description = " . $data->tickets[0]->description.'<br/>');

    echo '<br/>';

    $data2=$this->curlWrap("/organizations.json", null, "GET");
foreach ($data2->organizations as $key => $value){
print($value->name.' ticket created '.$value->created_at.'<br/>');
}


    }
    
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

    curl_setopt($ch, CURLOPT_CAINFO, "C:\wamp\www\bkInternalDashboard\library\cacert.pem");
  
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