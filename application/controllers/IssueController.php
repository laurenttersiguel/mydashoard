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

/*
header("Content-Type: application/json");
$status = $_GET["status"];
$jql = "project = MY-PROJECT AND status in ($status) ORDER BY Rank ASC";
$url = "https://display-user:display-pwd@jira.corp.company.de/jira/rest/api/2/search?jql=".urlencode($jql)."&fields=key,summary,assignee,status";
echo shell_exec("curl -k --user display-user:display-pwd \"$url\"");
*/
    public function indexAction()
    {
/*
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new MyStock());
        $auth->getStorage()->read();
        $temp= $auth->getIdentity();
        var_dump($temp);
*/
        for ($i = 10; $i >= 0; $i--) {
          $dinf = date("Y-m-d",mktime(0, 0, 0, date("m"),date("d")-$i-1,date("Y")));
          $dsup = date("Y-m-d",mktime(0, 0, 0, date("m"),date("d")-$i,date("Y")));
          $jqlo = 'project = Bk-Web AND created>='.$dinf.' AND created<'.$dsup.' ';
//          $jqlr = 'project = Bk-Web AND created>='.$dinf.' AND created<'.$dsup.'  AND status = resolved';
//          $jqlc = 'project = Bk-Web AND created>='.$dinf.' AND created<'.$dsup.'  AND status = closed';
          $open="/search?jql=".urlencode($jqlo)." ";
//          $resolved="/search?maxResults=5&jql=".urlencode($jqlr)."&fields=key,summary,assignee,status";
//          $closed="/search?maxResults=5&jql=".urlencode($jqlc)."&fields=key,summary,assignee,status";

          $dataopen=$this->curlWrap($open, null, "GET");
//          $dataresolved=$this->curlWrap($resolved, null, "GET");
//          $dataclosed=$this->curlWrap($closed, null, "GET");
          
          $opennb=0;
          $closednb=0;
          $resolvednb=0;
          if($dataopen->total>0){
              foreach ($dataopen->issues as $issue){
              //   echo $issue->fields->status->name;
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

          $p0=0;
          $p1=0;
          $p2=0;
          if($dataopen->total>0){
              foreach ($dataopen->issues as $issue){
              //   echo $issue->fields->status->name;
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

      $data=$this->curlWrap("/project", null, "GET");
      foreach($data as $key => $value)
                $projecttab[]=array($value->key,$value->name);
      $this->view->currentitems=$projecttab;

    }

/* $dataopen->issues*//*
BKWEB-21811 [fields] [summary]  Drafts_GenerateDraft_Event
[issuetype]
      [self]  https://jira.devbk.net/rest/api/2/issuetype/9
      [id]  9
      [description]  This Issue Type is used to create Zephyr Test within Jira. [
      [iconUrl]  https://jira.devbk.net/download/resources/com.thed.zephyr.je/images/icons/ico_zephyr_issuetype.png
      [name]  Test
      [subtask]
[votes]
[self]  https://jira.devbk.net/rest/api/2/issue/-21811/votes [votes]  0 [hasVoted]
[fixVersions] [resolution]  [resolutiondate]  
[reporter]
      [self]  https://jira.devbk.net/rest/api/2/user?username=tlm [name]  tlm
      [emailAddress]  tlm@bluekiwi-software.com [avatarUrls]([16x16]  https://jira.devbk.net/secure/useravatar?size=xsmall&ownerId=tlm&avatarId=11260)
      [displayName]  Thibault Lemoigne [active]  1
[created]  2015-04-15T17:20:55.000+0100
[updated]  2015-04-15T17:25:55.000+0100 [customfield_10331]
[description]  -20957: As a bluekiwi system, I am able to save in a draft all input fields of a Event [priority]
    [self]  https://jira.devbk.net/rest/api/2/priority/6 [iconUrl]  https://jira.devbk.net/images/icons/priorities/major.png
    [name]  P2 [id]  6
[duedate]  []  [] [issuelinks]  [watches]
     [self]  https://jira.devbk.net/rest/api/2/issue/21811/watchers
     [watchCount]  2 [isWatching]
[status]
    [self]  https://jira.devbk.net/rest/api/2/status/1
    [description]  The issue is open and ready for the assignee to start work on it.
    [iconUrl]  https://jira.devbk.net/images/icons/statuses/open.png [name]  Open [id]  1
[labels]  ( [0]  DraftsV2 ) [workratio]  -1 [assignee]  stdClass
       [self]  https://jira.devbk.net/rest/api/2/user?username=tlm [name]  tlm [emailAddress]  tlm@bluekiwi-software.com [avatarUrls]
       [16x16]  https://jira.devbk.net/secure/useravatar?size=xsmall&ownerId=tlm&avatarId=11260
       [displayName]  Thibault Lemoigne
       [active]  1
[project]
       [self]  https://jira.devbk.net/rest/api/2/project/10460 [id]  10460 [key]
       [name]  Bk-Web
       [avatarUrls]
       [16x16]  https://jira.devbk.net/secure/projectavatar?size=xsmall&pid=10460&avatarId=10011)
[versions]  [environment]  [lastViewed] Not Started
[components]
    [self]  https://jira.devbk.net/rest/api/2/component/10810 [id]  10810
    [name]  Drafts
    [description]  Everything related to drafts - drafts view in profile page and autosaved feature on created content
    [expand]  editmeta,renderedFields,transitions,changelog,operations
    [id]  51998
    [self]  https://jira.devbk.net/rest/api/2/issue/51998 [key]
*/


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