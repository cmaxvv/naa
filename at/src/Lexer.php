<?php
class Template_Lexer
{
    const STATE_TEXT = 1;
    const STATE_ESCAPE = 2;
    const STATE_MARKER = 3;

    private $_cursor = -1;
    private $_maxCursor = 0;

    private $_positions;

    private $_state = self::STATE_TEXT;

    private $_start_pattern = '';

    private $_last_position = 0;

    public function __construct() {
        $this->_start_pattern = '/(';
        $this->_start_pattern .= preg_quote(YC_ATM_Template_Token::LEFT_DELIMITER, '/');
        $this->_start_pattern .= '|';
        $this->_start_pattern .= preg_quote(YC_ATM_Template_Token::RIGHT_DELIMITER, '/');
        $this->_start_pattern .= ')/s';
    }

    public function  tokenize($str) {

        preg_match_all($this->_start_pattern, $str, $matches, PREG_OFFSET_CAPTURE);
        $this->_positions = $matches[0];
        $this->_maxCursor = sizeof($this->_positions);


        $_tokens = array();
        while($this->_cursor < $this->_maxCursor - 1) {
            $this->_cursor ++;

            $_curr = $this->_positions[$this->_cursor][1];
            $_curr_char = $this->_positions[$this->_cursor][0];



            // 已经进入转义状态
            if($this->_state == self::STATE_ESCAPE) {
                $this->_state = self::STATE_TEXT;
                continue;
            }

            if($this->_state == self::STATE_TEXT) {
                if($this->_cursor + 1 >= $this->_maxCursor) {
                   exit;// TODO ERRRRRR....;
                }
                $_next = $this->_positions[$this->_cursor + 1][1];
                $_next_char = $this->_positions[$this->_cursor + 1][0];
                // 先处理转义
                if($_next - $_curr <= 1) {
                    if($_curr_char == $_next_char) {
                        $this->_state = self::STATE_ESCAPE;
                        continue;
                    } else {
                        exit; //TODO ERRRRR....
                    }
                } else if($str[$_curr] == YC_ATM_Template_Token::LEFT_DELIMITER)  {
                    $this->_state = self::STATE_MARKER;
                    $_data = substr($str, $this->_last_position, $_curr - $this->_last_position);
                    $_token = new YC_ATM_Template_Token_Text();
                    $_token->setData($_data);
                    $_tokens[] = $_token;
                    $this->_last_position = $_curr;
                    continue;
                } else {
                    exit;//TODO err...
                }
            }

            if($this->_state == self::STATE_MARKER) {
                if($str[$_curr] == YC_ATM_Template_Token::RIGHT_DELIMITER) {
                    $this->_state = self::STATE_TEXT;
                    $_data = substr($str, $this->_last_position, $_curr - $this->_last_position + 1);
                    $_token = new YC_ATM_Template_Token_Marker();
                    $_token->setData($_data);
                    $_tokens[] = $_token;
                    $this->_last_position = $_curr + 1;
                    continue;
                } else {
                    exit;//TODO err....
                }
            }
            exit;//TODO....errr. wrong state;
        }


        if($this->_last_position != strlen($str)) {
            $_data = substr($str, $this->_last_position , strlen($str) - ($this->_last_position));
            $_token = new YC_ATM_Template_Token_Text();
            $_token->setData($_data);
            $_tokens[] = $_token;
        }
        return $_tokens;
    }
}
