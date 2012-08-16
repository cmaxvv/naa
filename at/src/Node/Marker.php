<?php
class Template_Node_Marker extends YC_ATM_Template_Node {

    const FORMAT_CLASS_PREFIX = 'YC_ATM_Template_Format_';
    const FUNCTION_CLASS_PREFIX = 'YC_ATM_Template_Function_';
    const DEFAULT_CLASS = 'Default';

    private $_functionClass;
    private $_functionMethod;
    private $_formatClass;
    private $_formatMethod;
    private $_varList;

	private $_logger;

	public function __construct() {
		$this->_logger = Zend_Registry::get('logger');
	}

    public function render($params)
    {
        $_paramList = $this->_getUsedParamList($params);

        if($_paramList) {
            $_res = $_paramList;
        } else {
            $_res = '';
        }

        if($this->getFunctionClass() && $this->getFunctionMethod()) {
            $_res = $this->_doFunction($params);
        }
        if($this->getFormatClass() && $this->getFormatMethod()) {
            if(is_array($_res)) {
                for($i=0;$i<sizeof($_res);$i++) {
                    $_res[$i] = $this->_doFormat($_res[$i]);
                }
            } else {
                $_res = $this->_doFormat($_res);
            }
        }

        if(is_array($_res)) {$_res = implode('', $_res);}
        return $_res;
    }

    private function _doFormat($str) {
        $_fmt_obj = new $this->_formatClass;
        $_fmt_method = $this->_formatMethod;
        return $_fmt_obj->$_fmt_method($str);
    }

    private function _doFunction($params) {
        $_func_obj = new $this->_functionClass;
        $_func_method = $this->_functionMethod;

        $_param_list = $this->_getUsedParamList($params);
        if($_param_list == null) {
            return $_func_obj->$_func_method();
        } elseif (is_array($_param_list)) {
            return $_func_obj->$_func_method($_param_list);
        } else {
            return $_func_obj->$_func_method($_param_list);
        }
    }

    private function _getUsedParamList($params) {
        $_res = array();

        foreach ($this->_varList as $_v) {
            if(array_key_exists($_v, $params)) {
                $_res[] = $params[$_v];
            } else {
				$_res[] = $_v;
				$this->_logger->ERR("模板处理：无法找到提供的变量'$_v'");
            }
        }
        return $_res;

    }

    public function setFormatClass($format_class = null){
        if($format_class) {
            $this->_formatClass = self::FUNCTION_CLASS_PREFIX . $format_class;
        } else {
            $this->_formatClass = self::FORMAT_CLASS_PREFIX . self::DEFAULT_CLASS;
        }
    }

    public function getFormatClass()
    {
        return $this->_formatClass;
    }

    public function setFormatMethod($format_method)
    {
        $this->_formatMethod = $format_method;
    }

    public function getFormatMethod()
    {
        return $this->_formatMethod;
    }

    public function setFunctionClass($function_class = null)
    {
        if($function_class) {
            $this->_functionClass = self::FUNCTION_CLASS_PREFIX . $function_class;
        } else {
            $this->_functionClass = self::FUNCTION_CLASS_PREFIX . self::DEFAULT_CLASS;
        }
    }

    public function getFunctionClass()
    {
        return $this->_functionClass;
    }

    public function setFunctionMethod($function_method)
    {
        $this->_functionMethod = $function_method;
    }

    public function getFunctionMethod()
    {
        return $this->_functionMethod;
    }

    public function setVarList($varList)
    {
        $this->_varList = $varList;
    }

    public function getVarList()
    {
        return $this->_varList;
    }
}
