<?php
class YC_ATM_Template_Parser_Text extends YC_ATM_Template_Parser {

    function doParser() {
        $_node = new YC_ATM_Template_Node_Text();
        $_node->setText($this->_resolveEscapedChar($this->getRawData()));
        return $_node;
    }

    private function _resolveEscapedChar($str) {
       $str = str_replace(YC_ATM_Template_Token::LEFT_DELIMITER_ESCAPE, YC_ATM_Template_Token::LEFT_DELIMITER, $str);
       $str = str_replace(YC_ATM_Template_Token::RIGHT_DELIMITER_ESCAPE, YC_ATM_Template_Token::RIGHT_DELIMITER, $str);
       return $str;
    }
}
