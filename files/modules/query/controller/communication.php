<?php namespace files\modules\query\controller;

use application\libs\controllerRest;
use application\libs\communication as comm;

class communication extends controllerRest{
	//sendTesteConfigEmail
	function action_sendTesteConfigEmail(){
		try {
			comm::sendTesteConfigEmail();

			$this->returnRest();
		} catch (\Exception $e) {
			$this->returnRestError($e->getMessage());
		}
	}
	function action_saveTesteConfigEmail(){
		try {
			comm::saveTesteConfigEmail();
	
			$this->returnRest();
		} catch (\Exception $e) {
			$this->returnRestError($e->getMessage());
		}
	}
}
