<?php


define("ZDAPIKEY", "5jr8sV2u");
define("ZDUSER", "lte");
define("ZDURL", "https://jira.devbk.net/rest/api/2");

/* Note: do not put a trailing slash at the end of v2 */


class IssueController extends Zend_Controller_Action
{
    
    public function preDispatch()
	  {
    		$auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) 
          	$this->_redirect('/auth/login');
    }
    
  
      public function indexAction()
    {
          $data=$this->curlWrap("/project", null, "GET");
          print('<table>');
          foreach($data as $key => $value)
              print('<tr><td>'.$value->key.'</td><td> name= '.$value->name.'</td></tr>');
          print('</table>');

    }
    
  public function curlWrap($url, $json, $action)
  {
  	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  	curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
  	curl_setopt($ch, CURLOPT_URL, ZDURL.$url);
  	curl_setopt($ch, CURLOPT_USERPWD, ZDUSER.":".ZDAPIKEY);
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
  //curl -D- -u fred:fred -X GET -H "Content-Type: application/json" http://kelpie9:8081/rest/api/2/search?jql=assignee=fred
  
  	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  	curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  	$output = curl_exec($ch);
    echo 'Curl output: ' . curl_error($ch);
  	curl_close($ch);
  	$decoded = json_decode($output);
  	return $decoded;
    
  }

}
?>