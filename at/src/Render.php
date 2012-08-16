<?php

interface YC_ATM_Template_Token_Interface {
    public function compile();
}

interface YC_ATM_Template_Parser_Interface {
    public function parse($token);
}

interface YC_ATM_Template_Node_Interface {
    public function render($params);
}



class YC_ATM_Template_Render {

    public function render($str, $params) {
        $_lexer = new YC_ATM_Template_Lexer();
        $_tokenList = $_lexer->tokenize($str);
        $_nodeList = array();
        foreach($_tokenList as $_token) {
            $_nodeList[] = $_token->compile();
        }
        $_res = '';
        foreach($_nodeList as $_node) {
            $_res .= $_node->render($params);
        }
        return $_res;
    }

}
