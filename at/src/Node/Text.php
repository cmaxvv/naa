<?php
class YC_ATM_Template_Node_Text extends YC_ATM_Template_Node{

    private $_text;


    public function render($params)
    {
        return $this->_text;
    }

    public function setText($text)
    {
        $this->_text = $text;
    }

    public function getText()
    {
        return $this->_text;
    }


}
