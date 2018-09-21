<?php namespace files\modules\query\controller;
//ini_set("display_errors",false);
use application\libs\controller;

class closeProject extends controller{
	public function action_default(){
		$_SESSION['wizard']['projeto'] = null;
		header("Location: ".URL_RAIZ);
	}
}