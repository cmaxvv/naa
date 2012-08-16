<?php
function __autoload($clazz) {
	require('../src/'. $clazz . '.php');
}
$fsm = <<<FSM
init->[do_pay]->pay_pending->[pay_success]->pay_confirmed->[account_reduce]->checked->[card_shipping]->done
pay_pending->[pay_failed]->init
init->[cancel]->cancel
pay_confirmed->[account_not_enough]->init
FSM;
$def = new FsmDef($fsm);
$gen = new FsmCode($def);
$code =  $gen->gen("Test");
echo $code;
?>
