<?php
class FsmCode{
		
	private $_def;

	public function __construct($fsmDef) {
		$this->_def = $fsmDef;
	}

	public function gen($clz) { //{{{
		$states_define = $this->genStates();
		$what_can_i_do_func = $this->genWhatCanDo();
		$can_i_do_func = $this->genCanDo();
		$sth_done_func = $this->genDone();
		return <<<CLZ
class $clz{
	$states_define

	$what_can_i_do_func
	
	$can_i_do_func

	$sth_done_func
}
CLZ;
	} //}}}

	private function genStates() { // {{{
		$res = '';
		$states = $this->_def->states;
		for($i = 0; $i < sizeof($states); $i ++ ) {
			$st = strtoupper($states[$i]);
			$j = $i + 1;
			$res .= "\n\tconst STATUS_${st} = {$j};\n";
		}
		return $res;
	} //}}}

	private function genWhatCanDo() { //{{{
		$cases = '';
		$states = $this->_def->states;
		for($i = 0; $i < sizeof($states); $i ++ ) {
			$c = "self::STATUS_" . strtoupper($states[$i]);
			$cases .= "\n\t\t\tcase({$c}):";
			$actions = $this->_def->actions;
			foreach($actions as $action => $flows) {
				foreach($flows as $flow) {
					if($flow[0] == $states[$i]) {
						$cases .="\n\t\t\t\t\$res[] = '{$action}';";
					}
				}
			}
			$cases .= "\n\t\t\t\tbreak;";
		}
		$res = <<<FUNC

	function nextActions(\$curr) {
		\$res = array();
		switch(\$curr) {
			$cases
			default:
				break;
		}
		return \$res;
	}

FUNC;
		return $res; 
	} //}}}

	private function genCanDo() { //{{{
		$res = '';
		$actions = $this->_def->actions;
		$states = $this->_def->states;
		foreach($actions as $action => $flows) {
			$support_prev_states = '';
			foreach($flows as $flow) {
				for($i = 0; $i < sizeof($states); $i ++) {
					if($flow[0] == $states[$i]) {
						$c = "self::STATUS_" . strtoupper($states[$i]);
						$support_prev_states .= "$c,"; 
					}
				}
			}
			$support_prev_states = trim($support_prev_states, ',');
			$fname = $this->action2FuncName($action);
			$res .= <<<FUNC

	function isCan$fname(\$curr) {
		\$arr = array($support_prev_states);
		return in_array(\$curr, \$arr);
	}

FUNC;
		}
		return $res; 
	} //}}}

	private function genDone() { //{{{
		$res = '';
		$actions = $this->_def->actions;
		$states = $this->_def->states;
		foreach($actions as $action => $flows) {
			$cases = '';
			foreach($flows as $flow) {
				for($i = 0; $i < sizeof($states); $i ++) {
					if($flow[0] == $states[$i]) {
						$prev = "self::STATUS_" . strtoupper($states[$i]);
					}
					if($flow[1] == $states[$i]) {
						$next = "self::STATUS_" . strtoupper($states[$i]);
					}
				}
				$cases .= "\n\t\t\tcase($prev):";
				$cases .= "\n\t\t\t\t\$next = $next;";
				$cases .= "\n\t\t\t\tbreak;";
			}
			$cases = trim($cases);
			$fname = lcfirst($this->action2FuncName($action));
			$res .= <<<FUNC

	function $fname(\$curr) {
		\$next = false;
		switch(\$curr) {
			$cases
			default: 
				break;
		}
		return \$next;
	}

FUNC;
		}
		return $res;
	} //}}}

	private function action2FuncName($action) { //{{{
		$res = '';
		$ts = explode('_', $action);
		foreach($ts as $t) {
			$res .=	ucfirst(strtolower($t));
		}
		return $res;
	} //}}}
}
