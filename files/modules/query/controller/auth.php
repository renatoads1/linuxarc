<?php namespace files\modules\query\controller;

use application\libs\controller;
use files\lib\auth as classAuth;

class auth extends controller{
	static function checkPermissaoAcesso($onlylogin = false,$onlyTipos = null){
		return controller::checkPermissaoAcesso($onlylogin,$onlyTipos);
	}
	public function action_signup(){
		$username = isset($_POST['username'])?$_POST['username']:"";
		$password = isset($_POST['password'])?$_POST['password']:'';
						
		classAuth::signUp($username, $password);
	}
	public function action_signupToken(){
		if(isset($_POST['token'])){
			$token = $_POST['token'];
		} elseif(isset($_GET['token'])){
			$token = $_GET['token'];			
		} else {
			$return = array(
				'status'	=> false,
				'strError'	=> "Erro ao logar"
			);
			echo SEP_CONTENT;
			echo json_encode($return);
			exit;
		}
		
		classAuth::signUpToken($token);
	}
	public function action_checkLogin(){
		$return = array('status' => true,'strError' => "");
		
		if(!isset($_POST['username'])){
			$return['status']	= false;
			$return['strError'] = "Informe o Usuário";
		} else {
			$username = $_POST['username'];
		
			if(classAuth::checkLoginExist($username)){
				$return['status']	= false;
				$return['strError'] = "Usuário existente";
			} else {
				$return['status']	= true;
				$return['strError'] = "Usuário disponível";
			}
		}
		
		header("Content-type:text/plain;charset=utf-8");
		echo SEP_CONTENT;
		echo json_encode($return);
		exit;
	}
}