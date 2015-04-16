<?php
define("ZDAPIKEY", "5jr8sV2u");
define("ZDUSER", "lte");
define("ZDURL", "http://jira.devbk.net/rest/api/2");

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
        for ($i = 10; $i >= 0; $i--) {
          $dinf = date("Y-m-d",mktime(0, 0, 0, date("m"),date("d")-$i-1,date("Y")));
          $dsup = date("Y-m-d",mktime(0, 0, 0, date("m"),date("d")-$i,date("Y")));
          $open="/search?maxResults=5&fields=status&jql=created>='".$dinf."' and created<'".$dsup."' and project=Bk-Web and status=open";
          $resolved="/search?maxResults=5&fields=status&jql=created>='".$dinf."' and created<'".$dsup."' and project=Bk-Web and status=resolved";
          $closed="/search?maxResults=5&fields=status&jql=created>='".$dinf."' and created<'".$dsup."' and project=Bk-Web and status=closed";
          $dataopen=$this->curlWrap($open, null, "GET");
          $dataresolved=$this->curlWrap($resolved, null, "GET");
          $dataclosed=$this->curlWrap($closed, null, "GET");
          $tabopen[]=array($dinf,$dataopen->total);
          $tabresolved[]=array($dinf,$dataresolved->total);
          $tabclosed[]=array($dinf,$dataclosed->total);
        }
        $this->view->open=json_encode($tabopen);
        $this->view->resolved=json_encode($tabresolved);
        $this->view->closed=json_encode($tabclosed);

        $data=$this->curlWrap("/project", null, "GET");
        print('<div  style="float:left; width:150px; "><table>');
        foreach($data as $key => $value)
            print('<tr><td>'.$value->key.'</td><td> name= '.$value->name.'</td></tr>');
        print('</table></div>');

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

    $config = new Zend_Config_Ini('../application/configs/application.ini','production');
    $certif_path = $config->certif->file_path;
    curl_setopt($ch, CURLOPT_CAINFO, $certif_path);


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