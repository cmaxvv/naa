<?php
class Template_Token_Marker extends YC_ATM_Template_Token {

    public function __construct() {
        $this->_parser = new YC_ATM_Template_Parser_Marker();
    }
}
