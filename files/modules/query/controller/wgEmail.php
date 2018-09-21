<?php namespace files\modules\query\controller;

use application\libs\controller;
use application\widgets\email;

class wgEmail extends controller{
	public function action_default(){
		$wgEmail = new email(true);
		$wgEmail->DEBUG = false;
		$wgEmail->run();
	}
}