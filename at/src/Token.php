<?php
class Template_Token implements YC_ATM_Template_Token_Interface {

    const LEFT_DELIMITER = "{";
    const LEFT_DELIMITER_ESCAPE = "{{";
    const RIGHT_DELIMITER = "}";
    const RIGHT_DELIMITER_ESCAPE = "}}";

    protected $_parser;
    protected $_data;

    public function compile() {
        return $this->_parser->parse($this);
    }

    public function setData($data) {
        $this->_data = $data;
    }

    public function getData() {
        return $this->_data;
    }
    protected function setParser($parser) {
        $this->_parser = $parser;
    }
    protected function getParser() {
        return $this->_parser;
    }
}
