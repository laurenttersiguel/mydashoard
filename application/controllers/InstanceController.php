<?php

define("ZDURL", "https://trunk.devbk.net/api/v3/administration");

class InstanceController extends Zend_Controller_Action
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


			$list=$this->curlWrap('/_statisticsInstance?startDate=2015-07-01&endDate=2015-07-18&access_token='.$token, null, "GET",$login,$token);
			foreach ($list->charts->activeSpaces->activeSpaces as $key => $item)
				$mytabs[]=array($key,$item);
			foreach ($list->charts->activeUsers->activeUsers as $key => $item)
				$mytabu[]=array($key,$item);
			foreach ($list->charts->newContributions->private as $key => $item)
				$mytabp[]=array($key,$item);



			$this->view->space=json_encode($mytabs);
			$this->view->user=json_encode($mytabu);
			$this->view->private=json_encode($mytabp);
		}
		}
		public function curlWrap($url, $json, $action,$login,$token)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
			curl_setopt($ch, CURLOPT_URL, ZDURL.$url);
			//	curl_setopt($ch, CURLOPT_USERPWD, "access_token=".$password);
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
			//	echo 'Curl output: ' . curl_error($ch);
			curl_close($ch);
			$decoded = json_decode($output);
			return $decoded;

		}

		}
		?>
