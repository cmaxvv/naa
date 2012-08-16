<?php
abstract class Template_Parser implements YC_ATM_Template_Parser_Interface
{
    protected $_rawData;

    function parse($token) {
        $this->_rawData = $token->getData();
        return $this->doParser();
    }

    abstract function doParser();

    public function setRawData($data)
    {
        $this->_rawData = $data;
    }

    public function getRawData()
    {
        return $this->_rawData;
    }

}
