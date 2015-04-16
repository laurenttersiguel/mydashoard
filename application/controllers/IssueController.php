<?php


define("ZDAPIKEY", "5jr8sV2u");
define("ZDUSER", "lte");
define("ZDURL", "http://jira.devbk.net/rest/api/2");

/* Note: do not put a trailing slash at the end of v2 */


class IssueController extends Zend_Controller_Action
{

    public function preDispatch()
	  {
    		$auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity())
          	$this->_redirect('/auth/login');
    }


///rest/api/2/search?jql&startAt&maxResults&validateQuery&fields&expand
//project = BKWEB AND issuetype in (Bug, Incident) AND status in (Open, "In Progress", Reopened) ORDER BY created DESC
//+order+by+duedate&fields=id,key
// and created<=1417392000
      public function indexAction()
    {
    echo "toto"."tata";
$and='and';
$dsq='2015-04-01';
$dateda="/search?fields=created,status&maxResults=5&jql=created>'2015-04-01' ".$and." created<'2015-04-18' and project=Bk-Web and status=open";
$data=$this->curlWrap($dateda, null, "GET");

              var_dump($data->issues[0]);
              var_dump($data->issues[1]);
              var_dump($data->issues[2]);
              var_dump($data->issues[3]);
              var_dump($data->issues[4]);

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


  	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  	curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  	$output = curl_exec($ch);
    if ($output==null)
      echo 'Curl output: ' . curl_error($ch);
  	curl_close($ch);
  	$decoded = json_decode($output);
  	return $decoded;

  }

}
?>