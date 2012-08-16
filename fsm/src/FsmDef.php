<?php

/**
 * FSM数据结构(索引)
 */
class FsmDef{

	public $states  = array();
	public $actions = array();

	function __construct($fsm_str) { //{{{
		$fsm_lines = explode("\n", $fsm_str);
		foreach($fsm_lines as $line) {
			$this->parseLine($line);
		}
	} //}}}

	function parseLine($line) { //{{{
		$line = trim($line);
		$tokens = explode('->', $line);
		for($i = 0; $i < sizeof($tokens) - 1 ; $i ++ ) {
			if($i % 2 == 0) { continue;}
			$action = $this->token2Action($tokens[$i]);
			$prev_state = $this->token2State($tokens[$i - 1]);
			$next_state = $this->token2State($tokens[$i + 1]);
			if($action && $prev_state && $next_state) {
				$this->addState($prev_state);
				$this->addState($next_state);
				$this->addAction($action, $prev_state, $next_state);
				//echo $prev_state . '->'. $next_state . ':'. $action . "\n";
			} else {
				die("bad syntax FSM define: $line");
			}
		}
	} //}}}

	function token2State($token) { //{{{
		if( (strpos($token, '[') === false) 
				&& (strpos($token, ']') === false) ) {
			return $token;
		} else {
			return true;
		}
	} //}}}

	function token2Action($token) { //{{{
		if( (strpos($token, '[') === 0) 
			&& (strpos($token, ']') === strlen($token) - 1) ) {
			return trim($token, "[]");
		}
		return false;
	} //}}}

	function addState($state) { //{{{
		if(! in_array($state, $this->states)) {
			$this->states[] = $state;
		} 
	} //}}}

	function addAction($action, $prevState, $nextState) { //{{{
		if( !isset($this->actions[$action])  ) {
			$this->actions[$action] = array();
		}
		$flow = array($prevState, $nextState);
		if(! in_array($flow, $this->actions[$action])) {
			$this->actions[$action][] = $flow;
		}
	} //}}}
	
	function toDot() { //{{{
		$dotg = "";
		foreach($this->actions as $action => $flows) {
			foreach($flows as $flow) {
				$dotg .= "{$flow[0]}->{$flow[1]}[label=\"$action\"];\n";
			}
		}
		return <<<DOT
digraph G {
	$dotg
}
DOT;
	} //}}}
}
?>
