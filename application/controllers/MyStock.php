<?php
require_once 'Zend/Auth/Storage/Interface.php';
require_once 'Zend/Session.php';
class MyStock implements Zend_Auth_Storage_Interface
{
    const NAMESPACE_DEFAULT = 'mystock';
    const MEMBER_DEFAULT = 'mystorage';
    protected $_session;
    protected $_namespace;
    protected $_member;
    protected $_password;

    public function __construct($namespace = self::NAMESPACE_DEFAULT, $member = self::MEMBER_DEFAULT)
    {
        $this->_namespace = $namespace;
        $this->_member    = $member;
        $this->_password    = " ";
        $this->_session   = new Zend_Session_Namespace($this->_namespace);
    }

    public function getNamespace()
    {
        return $this->_namespace;
    }
    public function getMember()
    {
        return $this->_member;
    }
    public function isEmpty()
    {
        return !isset($this->_session->{$this->_member});
    }
    public function read()
    {
        return $this->_session->{$this->_member};
    }
    public function write($contents)
    {
        $this->_session->{$this->_member} = $contents;
    }
    public function clear()
    {
        unset($this->_session->{$this->_member});
    }
///added
     public function setPassword($p){
           $this->_password = $p;
      }

      public function getPassword(){
        return $this->_password;
      }
}
?>