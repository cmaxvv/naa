<?php
/**
 * 格式如下
 *  {var}
 *  {fmt#func:var1,var2}
 *  {ns.fmt#ns.func:var1,var2}
 */
class YC_ATM_Template_Parser_Marker extends YC_ATM_Template_Parser {

    function doParser() {
        $_node = new YC_ATM_Template_Node_Marker();

        $_exp = $this->_getExp();

        $_sections = $this->_explodeExp($_exp);

        $_node->setVarList($this->_parseVarList($_sections['vars']));

        $_fmt = $this->_parseFormat($_sections['fmt']);
        if($_fmt) {
            $_node->setFormatClass($_fmt['class']);
            $_node->setFormatMethod($_fmt['method']);
        }

        $_func = $this->_parseFunction($_sections['func']);
        if($_func) {
            $_node->setFunctionClass($_func['class']);
            $_node->setFunctionMethod($_func['method']);
        }

        return $_node;
    }

    private function _getExp() {
        $_data = trim($this->getRawData());
        $_data = trim($_data, YC_ATM_Template_Token::LEFT_DELIMITER);
        $_data = trim($_data, YC_ATM_Template_Token::RIGHT_DELIMITER);
        return $_data;
    }

    private function _parseVarList($exp) {
        $_res = $exp ? explode(',', trim($exp)) : array();
        for($i = 0; $i < sizeof($_res); $i ++) {
            $_res[$i] = trim($_res[$i]);
        }
        return $_res;
    }

    private function _parseFormat($exp) {
       return $exp ? $this->_splitClassAndMethod($exp) : null;
    }

    private function _parseFunction($exp) {
        return $exp ? $this->_splitClassAndMethod($exp) : null;
    }

    private function _explodeExp($exp) {

        $_res['fmt'] = '';
        $_res['func'] = '';
        $_res['vars'] = '';

        $_sections = explode('#', $exp);
        if(sizeof($_sections) == 2) {
            $_res['fmt'] = $_sections[0];
            $exp = $_sections[1];
        } elseif(sizeof($_sections) == 1) {
            // 没有命中
        } else {
            // TODO err ...occur
        }

        $_sections = explode(':', $exp);
        if(sizeof($_sections) == 2) {
            $_res['func'] = $_sections[0];
            $exp = $_sections[1];
        } elseif(sizeof($_sections) == 1) {
            // 没有命中
        } else {
            // TODO err ...occur
        }

        $_res['vars'] =  $exp;

        return $_res;
    }

    private function _splitClassAndMethod($exp) {
        $_res = array();
        if($exp) {
            $_sections = explode('.', trim($exp));
            if(sizeof($_sections) == 2) {
                $_res['class'] = trim($_sections[0]);
                $_res['method'] = trim($_sections[1]);
            } elseif (sizeof($_sections) == 1){
                $_res['class'] = '';
                $_res['method'] = trim($_sections[0]);
            } else {
                //TODO err....;
            }
        }
        return $_res;
    }
}