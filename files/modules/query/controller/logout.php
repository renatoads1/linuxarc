<?php namespace files\modules\query\controller;

use application\libs\controllerRest;
use files\lib\certificado;

class logout extends controllerRest{
	public function action_default(){
		if(isset($_GET['redirectTo'])){
			$redirectTo = $_GET['redirectTo'];
		} else {
			$redirectTo = URL_RAIZ_EMPRESA;
		}
		if(isset($_GET['redirectExt']) and $_GET['redirectExt']=='yes'){
			$redirectExt = true;
		} else {
			$redirectExt = false;
		}

		//Diego Starling Fonseca - 07/06/2016
		//Fecha o certificado quando dá o logout
		$certificado = new certificado();
		$certificado->clear();
		//FIM - Diego Starling Fonseca - 07/06/2016

		controllerRest::deleteSession();

		header('Content-type:text/plain;Charset=utf=8');
		echo SEP_CONTENT;
		echo json_encode(array('status'=>true,'strError'=>'','redirect'=>$redirectTo));
		exit;
		/*
		if(!$redirectExt){
			header('location:'.$redirectTo);
		} else {
			$out = "<h1>Redirecionamendo...</h1>";
			$out .= "<p>Aguarde o redirecionamento, ou <a href=\"{$redirectTo}\">clique aqui.</a></p>";

			$js = "window.location = 'http://{$redirectTo}';";

			$this->toViewer['out'] = $out;
			$this->toViewer['js'] = $js;

			$this->getViewer();
		}*/
	}
}
