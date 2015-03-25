<?php

class Application_Model_DbTable_Report extends Zend_Db_Table_Abstract
{

    protected $_name = 'report_by_server_and_script';
    protected $_primary = 'script_name';
    
     public function getRequestAll() 
    {
        $select = $this->select()->order('req_count desc')
                          ->limit(10);
        
        $row = $this->fetchAll($select);
        
        if (!$row) {
            throw new Exception("unable to find ");
        }
        return $row;
    }
     public function getRequestByScript($id) 
    {
        $select  = $this->select()->where('script_name = ?', $id);
        $row = $this->fetchRow($select);
        
        if (!$row) {
            throw new Exception("unable to find $id");
        }
        return $row;
    }


}
