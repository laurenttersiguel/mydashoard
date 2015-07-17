<?php
define("ZDURL", "http://jira.devbk.net/rest/api/2");

class IssueController extends Zend_Controller_Action
{
    public function preDispatch()
	  {
    		$auth = Zend_Auth::getInstance();
//        if (!$auth->hasIdentity())
  //        	$this->_redirect('/auth/login');
    }

    public function indexAction()
    {

       $form = new Application_Form_Auth();
       $form->send->setLabel('Authenticate');
       $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
              $formData = $this->getRequest()->getPost();
              if ($form->isValid($formData)) {
                    $login = $form->getValue('login');
                    $password = $form->getValue('password');
                   }

          for ($i = 10; $i >= 0; $i--) {
            $dinf = date("Y-m-d",mktime(0, 0, 0, date("m"),date("d")-$i-1,date("Y")));
            $dsup = date("Y-m-d",mktime(0, 0, 0, date("m"),date("d")-$i,date("Y")));
            $jqlo = 'project = Bk-Web AND created>='.$dinf.' AND created<'.$dsup.' ';
            $open="/search?jql=".urlencode($jqlo)." ";
            $dataopen=$this->curlWrap($open, null, "GET",$login,$password);
            $opennb=0; $closednb=0;  $resolvednb=0;
            if($dataopen->total>0){
                foreach ($dataopen->issues as $issue){
                    if($issue->fields->status->name == 'Open' ) $opennb++;
                    if($issue->fields->status->name == 'Closed' ) $closednb++;
                    if($issue->fields->status->name == 'Resolved' ) $resolvednb++;
                }
  
            }
            $tabopen[]=array($dinf,$opennb);
            $tabresolved[]=array($dinf,$closednb);
            $tabclosed[]=array($dinf,$resolvednb);
            $this->view->open=json_encode($tabopen);
            $this->view->resolved=json_encode($tabresolved);
            $this->view->closed=json_encode($tabclosed);
            $p0=0; $p1=0; $p2=0;
            if($dataopen->total>0){
              foreach ($dataopen->issues as $issue){
                  if($issue->fields->priority->name == 'P0' ) $p0++;
                  if($issue->fields->priority->name == 'P1' ) $p1++;
                  if($issue->fields->priority->name == 'P2' ) $p2++;
              }
            }
            $tabp0[]=array($dinf,$p0);
            $tabp1[]=array($dinf,$p1);
            $tabp2[]=array($dinf,$p2);
            $this->view->p0=json_encode($tabp0);
            $this->view->p1=json_encode($tabp1);
            $this->view->p2=json_encode($tabp2);
         }

        $data=$this->curlWrap("/project", null, "GET",$login,$password);
        foreach($data as $key => $value)
                $projecttab[]=array($value->key,$value->name);
        $this->view->currentitems=$projecttab;
      }
  }


  public function curlWrap($url, $json, $action,$login,$password)
  {
  	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  	curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
  	curl_setopt($ch, CURLOPT_URL, ZDURL.$url);

    //$auth=Zend_Auth::getInstance();
    //$config = new Zend_Config_Ini('../application/configs/application.ini','production');
    //$options = $config->ldap->server->toArray();
    //$ldapcnct = new Zend_Ldap($options);
    //$current = $ldapcnct->getEntry('uid='.$auth->getIdentity().',ou=people,dc=mydomain,dc=com');
    //curl_setopt($ch, CURLOPT_USERPWD, $current['uid'][0].":".$current['userpassword'][0]);
  	curl_setopt($ch, CURLOPT_USERPWD, $login.":".$password);
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
