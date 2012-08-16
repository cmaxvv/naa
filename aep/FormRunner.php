<?php
class FormRunner{

	const DEBUG = False;

	// token 类型
	const TOKEN_VAR = 1; 
	const TOKEN_OP  = 2; 
	const TOKEN_FUNC  = 3; 
	const TOKEN_NUM  = 4; 

	// 操作符（关键字）
	static $op_table = array('?', ':',',', '=', '+', '-', '*', '/', '(', ')');

	public function run($input, $form) {
		$_res = $this->parse($form);
		$rform = $_res['form'];
		$var = $_res['var'];
		$output  = $_res['output'];
		extract($var);
		extract($input, EXTR_OVERWRITE);
		eval($rform);
		foreach ($output as $k => $v) {
			$output[$k] = $$k;
		}
		return $output;
	}

	// 公式解析，结果为：
	//		1,可执行的php代码
	//		2,变量表
	//		3,输出表 
	private function parse($form) { //{{{
		$output  = array();
		$var = array();
		$lines = explode("\n", $form);
		$newlines = array();
		foreach($lines as $line) {
			if(trim($line)) {
				$_tmp = $this->parseLine($line);
				$newlines[] = $_tmp['form'];
				$output[$_tmp['output']] = 0;
				$var = array_merge($var, $_tmp['var']);
			} else {
				continue;
			}
		}
		//$form = implode(";\n", $newlines) . ";";
		$form = implode(";", $newlines) . ";";
		//$var = array_diff_key($var, $output);
		if(self::DEBUG) {
			error_log("参数表：");
			foreach ($var as $k => $v) {
				error_log("\t$k");
			}
			error_log("输出表：");
			foreach ($output as $k => $v) {
				error_log("\t$k");
			}
		}
		return array(
				'form' => $form,
				'var' => $var,
				'output' => $output
			);	
	} // }}}
		
	// 解析单行公式
	private function parseLine($line) { //{{{
		$tokens = $this->tokenize($line);
		$output = '';
		$vars = array();;
		$form = '';
		foreach($tokens as $t) {
			if($t['type'] == self::TOKEN_VAR) {
				$form  .= "\$";
				if($output) {
					$vars[$t['value']] = 0;
				} else {
					$output = $t['value'];
				}
			}
			$form .= $t['value'];
		}
		return array(
				'form' => $form,
				'var' => $vars,
				'output' => $output
			);
	} //}}}


	// 词法分析，解析单行为token
	private function tokenize($line) { //{{{
		$tokens = array();
		$buf = '';
		$line = trim($line);
		$state  = self::TOKEN_VAR;
		for($i = 0; $i < strlen($line); $i++) {
			if($line[$i] == ' ') {
				continue;
			}
			if( ($i + 1)  == strlen($line) ) {
				if(!in_array($line[$i], self::$op_table)) {
					$buf .= $line[$i];
					$tokens[] = array(
							'type' => self::TOKEN_VAR,
							'value' => trim($buf)
							);
				}  else if ($line[$i] == ')') {
					if($state == self::TOKEN_VAR) {
						$tokens[] = array(
								'type' => self::TOKEN_VAR,
								'value' => trim($buf)
								);
					}
					$tokens[] = array(
							'type' => self::TOKEN_OP,
							'value' => $line[$i]
							);
				}
				break;
			}

			if($state == self::TOKEN_OP) {
				if(in_array($line[$i], self::$op_table)) {
					$tokens[] = array(
							'type' => self::TOKEN_OP,
							'value' => $line[$i]
						);
				} else {
					$buf .= $line[$i];
					$state = self::TOKEN_VAR;
				}
				continue;
			}

			if($state == self::TOKEN_VAR) {
				if(in_array($line[$i], self::$op_table)) {
					if($line[$i] == '(') {
						$_type = self::TOKEN_FUNC;
					} else {
						$_type = self::TOKEN_VAR;
					}
					$tokens[] = array(
							'type' => $_type,
							'value' => trim($buf)
						);
					$buf = '';
					$tokens[] = array(
							'type' => self::TOKEN_OP,
							'value' => $line[$i]
						);
					$state = self::TOKEN_OP;
				} else {
					$buf .= $line[$i];
				}
				continue;
			}
		}

		for($i = 0; $i < sizeof($tokens); $i++ ) {
			$t = $tokens[$i];
			if(is_numeric($t['value'])) {
				$tokens[$i]['type'] = self::TOKEN_NUM;
			}	
		}
		return $tokens;
	} //}}}
}
?>
