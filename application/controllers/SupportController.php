<?php

define("ZDURL", "https://support.bluekiwi.net/api/v2");

class SupportController extends Zend_Controller_Action
{
	public function preDispatch()
	{
		$auth = Zend_Auth::getInstance();
		//      if (!$auth->hasIdentity())
		//        $this->_redirect('/auth/login');
	}

	public function indexAction()
	{

		$form = new Application_Form_Token();
		$form->send->setLabel('Authenticate');
		$this->view->form = $form;
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				$login = $form->getValue('login');
				$token = $form->getValue('token');
			}


			for ($i = 10; $i >= 0; $i--) {
				$dinf = date("Y-m-d",mktime(0, 0, 0, date("m"),date("d")-$i-1,date("Y")));
				$dsup = date("Y-m-d",mktime(0, 0, 0, date("m"),date("d")-$i,date("Y")));
				$list=$this->curlWrap('/search.json?query=type:ticket,created>='.$dinf.',created<'.$dsup, null, "GET",$login,$token);
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
	}
	public function curlWrap($url, $json, $action,$login,$token)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt($ch, CURLOPT_URL, ZDURL.$url);
		curl_setopt($ch, CURLOPT_USERPWD, $login."/token:".$token);
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
