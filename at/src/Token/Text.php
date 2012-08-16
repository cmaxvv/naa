<?php
class YC_ATM_Template_Token_Text extends YC_ATM_Template_Token{
    public function __construct() {
        $this->_parser = new YC_ATM_Template_Parser_Text();
    }
}
