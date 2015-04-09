<?php

class Application_Model_DbTable_Report extends Zend_Db_Table_Abstract
{

    protected $_name = 'report_by_server_and_script';
    protected $_primary = 'script_name';
    
    
     public function getRequestDistinct() 
    {
        $id='backend';
        $id2='/home';
        $preselect = $this->select()->where('script_name <> ?', $id)
                          ->where('script_name = ?', $id2)
                          ->order('req_count desc')
                          ->limit(100);
        $prerow = $this->fetchAll($preselect);
       
        if (!$prerow) {
            throw new Exception("executed script was not found in database");
        }
      
        return $prerow;
    }
     public function getRequestAll($instance) 
    {
        $id='backend';
        if ($instance == null){
              $preselect = $this->select()->where('script_name <> ?', $id)
                                ->order('req_count desc')
                                ->limit(1);
              $prerow = $this->fetchRow($preselect);
             
              if (!$prerow) {
                  throw new Exception("executed script was not found in database");
              }
        }
              
        $select = $this->select()->where('script_name <> ?', $id)
                       ->where('server_name = ?', $instance)
                       ->order('req_count desc')
                       ->limit(10);
        
        $row = $this->fetchAll($select);
                
        if (!$row) {
            throw new Exception("executed script was not found in database");
        }
       
        return $row;
    }
    
     public function getRequestByScript($id,$id2) 
    {
        $db = $this->getAdapter();
        $select  = $this->select()->where(
        $db->quoteInto('script_name = ? ',$id).'AND'.$db->quoteInto(' server_name = ? ', $id2)
        );
        $row = $this->fetchRow($select);
        
        if (!$row) {
            throw new Exception("unable to find $id");
        }
        return $row;
    }

}
