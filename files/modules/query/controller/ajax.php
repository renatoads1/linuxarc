<?php namespace files\modules\query\controller;
//ini_set("display_errors",false);
use application\libs\controller;
use application\widgets\ajax as ajaxWg;
use files\lib\config;
use files\lib\wizard;

class ajax extends controller{
	public function action_default(){
		if(isset($_POST['fnAjax'])){
			$fn = $_POST['fnAjax'];
			if(method_exists($this, $fn)){
				$this->{$fn}();
			}
		} else {
			$return = array();
			
			$return['status'] = "ERROR";
			$return['error'] = array('codError'=>0,'strError'=>"fnAjax undefined");
			$return['data'] = array();
			echo SEP_CONTENT;
			echo json_encode($return);
			exit;
		}
	}
	public static function testeButton(){
		$isRequest = isset($_POST['fnAjax']);
		$ajax = new ajaxWg(__FUNCTION__);
		//Parametros de Entrada
		$ajax->addParamIn("paramExemplo1","Padrao"); //parametro paramExemplo1 com "Padrao" como valor padrao.
		
		/** imgLoading usando $.imgLoading() do pacoteJS
		 * 
		 * @see http://starling.com.br/pacoteJS/manual/jquery.starling.php#imgLoading
		 */
		$ajax->showImgLoading(URL_RAIZ.ID_EMPRESA."/files/public/images/loading.gif");		
		//$ajax->showProgressBar = true;
		
		//Script JS de retorno
		$ajax->setEvalJSOut_Ok("$.staDialog({title: 'Teste realizado com sucesso', text: 'Você clicou no botao que chamou a funcao \"testeAjaxOk\".',open:true, type: 'success' })");
		
		
		if($isRequest){
			$request = $ajax->getRequest();
			//----------------------------------------------------------//
			//	TRECHO Request, INICIO
			//----------------------------------------------------------//
			if($request['paramExemplo1']=='Padrao'){
				$ajax->returnOk();
			} else {
				$ajax->returnError("Erro cabuloso.rsrsrsrs");
			}
			
			//----------------------------------------------------------//
			//	TRECHO Request, FIM
			//----------------------------------------------------------//			
		}
		
		return $ajax->run();
	}
	public static function selectProject(){
		$isRequest = isset($_POST['fnAjax']);
		$ajax = new ajaxWg(__FUNCTION__);
		//Parametros de Entrada
		$ajax->addParamIn("projectName");
	
		/** imgLoading usando $.imgLoading() do pacoteJS
		 *
		 * @see http://starling.com.br/pacoteJS/manual/jquery.starling.php#imgLoading
		*/
		$ajax->showImgLoading(URL_RAIZ.ID_EMPRESA."/files/public/images/loading.gif");
		//$ajax->showProgressBar = true;
	
		//Script JS de retorno
		$ajax->setEvalJSOut_Ok("window.location.reload();");
	
	
		if($isRequest){
			$request = $ajax->getRequest();
			//----------------------------------------------------------//
			//	TRECHO Request, INICIO
			//----------------------------------------------------------//
			$projectName = $request['projectName'];
			
			$config = new config();
			$cfg = $config->get();
			if(is_dir($cfg->workspace."/".$projectName)){
				$wizard = new wizard();
				$wizard->setProject($projectName);
				$ajax->returnOk();
			} else {
				$ajax->returnError("Projeto não encontrado");
			}
				
			//----------------------------------------------------------//
			//	TRECHO Request, FIM
			//----------------------------------------------------------//
		}
	
		return $ajax->run();
	}
	
	public static function addProject(){
		$isRequest = isset($_POST['fnAjax']);
		$ajax = new ajaxWg(__FUNCTION__);
		//Parametros de Entrada
		$ajax->addParamIn("projectName");
	
		/** imgLoading usando $.imgLoading() do pacoteJS
		 *
		 * @see http://starling.com.br/pacoteJS/manual/jquery.starling.php#imgLoading
		*/
		$ajax->showImgLoading(URL_RAIZ.ID_EMPRESA."/files/public/images/loading.gif");
		//$ajax->showProgressBar = true;
	
		//Script JS de retorno
		$ajax->setEvalJSOut_Ok("window.location.reload();");
	
	
		if($isRequest){
			$request = $ajax->getRequest();
			//----------------------------------------------------------//
			//	TRECHO Request, INICIO
			//----------------------------------------------------------//
			$projectName = $request['projectName'];
			if($projectName==""){
				$ajax->returnError("Informe o nome do projeto");
			}
			$wizard = new wizard();
			$content_wProjects = "";
			$projetosAdd = array();
			$config = new config();
			$cfg = $config->get();
			
			$wizard->createProject($projectName);
			foreach ($wizard->getListProjects() as $nameProject){
				if(!in_array($nameProject, $projetosAdd)){
					$content_wProjects .= "{$nameProject}\r\n";
					$projetosAdd[] = $nameProject;
				}
			}	
			if(!in_array($projectName, $projetosAdd)){
				$content_wProjects .= "{$projectName}\r\n";
			}
						
			$fp = fopen($wizard->config->workspace."/.wProject", "w");
			fwrite($fp, $content_wProjects);
			fclose($fp);
			
			$ajax->returnOk();
			
	
			//----------------------------------------------------------//
			//	TRECHO Request, FIM
			//----------------------------------------------------------//
		}
	
		return $ajax->run();
	}
}