<?php /**
     * Titulo
     *
     * Description
     * 
     * @author       Jonas Ribeiro Silva <jonasribeiro19@gmail.com>
     * @copyright Starling Software, 2014
     */
namespace files\modules\query\controller;

use application\libs\controller;
use application\widgets\ajax as wgAjax;
use application\libs\query;
use files\database;
use application\libs\application;
use application\widgets\mapa_rota;
use application\libs\communication;
use files\database\qSuporte_situacao;
use files\database\qSuporte_tipochamado;
use files\database\qSuporte_topicos;
use files\database\qSuporte_projetos;
use files\database\qSuporte_chamado;

class widget_ajax extends controller{
	function __construct($autoLoad=true) {
		$module = "query";
		$controller = "widget_ajax";
		$action = "default";
		$query = "query/widget_ajax/default";
		
		$this->action = $action;
		$this->module = $module;
		if($module=='admin'){
			$this->skin = 'default_admin';
		}
		$this->controller = $controller;
		$this->query = $query;
		if($autoLoad){
			if(method_exists($this, "action_{$action}")){	
				call_user_func(array($this, "action_{$action}"));
			} else {
				$this->action_default();
			}
		}
	}
	public function action_default(){
		$this->skin = 'txt';
		if(isset($_POST['fn'])){
			$fn = 'fn_'.$_POST['fn'];
			if(method_exists($this, $fn)){
				$this->toViewer['out'] = json_encode( $this->{$fn}(true) );
			} else {
				$retorno = array('error'=>true,'errorStr'=>"'{$fn}' não encontrado em /files/modules/query/widget_ajax.php");
				$this->toViewer['out'] = json_encode($retorno);
			}
		} else {
			$retorno = array('error'=>true,'errorStr'=>"fn não informado.");
			$this->toViewer['out'] = json_encode($retorno);
		}
		
		$this->getViewer();
	}
	public function fn_salveForm($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('salveForm');
		//Params
		$wgAjax->setParamFunction('tabela');
		$wgAjax->setParamFunction('campos');
		$wgAjax->setParamFunction('actionCloseReturnTrue','param',null,false, "\"$('#error').sysdialog('close');\"");
		$wgAjax->setParamFunction('actionCloseReturnFalse','param',null,false, "\"$('#error').sysdialog('close');\"");
		//Return
		$jsReturn = "$('#error').sysdialog({
					'title': 'Salvo com sucesso!',
					'text': 'Clique em \"Fechar\" para voltar',
					'showTitle': true,
					'open':true,
				 	'buttons': {
	        			btClose:{
	        				text: 'Fechar',
	        				action: data.actionCloseReturnTrue
	        			}
	        		}
				});";		
		$wgAjax->setActionReturn($jsReturn);
		$jsReturnFalse = "$('#error').sysdialog({
					'title': 'Ops! Aconteceu um erro',
					'text': data.errorStr+'<br />Clique em \"Fechar\" para voltar',
					'showTitle': true,
					'open':true,
				 	'buttons': {
	        			btClose:{
	        				text: 'Fechar',
	        				action: data.actionCloseReturnFalse
	        			}
	        		}
				});";
		$wgAjax->setActionReturnError($jsReturnFalse);
		
		
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			/* Tabelas:
			 * 	suporte_situacao
			 * 	suporte_projetos
			 * 	suporte_gruposuporte
			 * 	suporte_chamado
			 * */
			$tabela = $Request['param']['tabela'];
			$campos = $Request['param']['campos'];
			
			switch ($tabela){
				case 'suporte_situacao':
					$qSuporte_situacao = new qSuporte_situacao();
					$campos['codigo'] =  (isset($campos['codigo']) and $campos['codigo']!=0)?$campos['codigo']:null;
					if(!isset($campos['titulo']))
						return $this->returnError("Você deve informar o titulo",0,"{$Request['param']['actionCloseReturnFalse']}");
					if(!isset($campos['classIcon']))
						$campos['classIcon'] = null;
					if(!isset($campos['classText']))
						$campos['classText'] = null;
					if(!isset($campos['grupo_permitido']))
						$campos['grupo_permitido'] = null;
					if(!isset($campos['ordem']))
						$campos['ordem'] = 100;
					$campos['codigo'] = $qSuporte_situacao->save($campos['codigo'],$campos['titulo'],$campos['classIcon'],$campos['classText'],$campos['grupo_permitido'],$campos['ordem']);
					if(!$campos['codigo'])
						return $this->returnError("Erro ao gravar",0,"{$Request['param']['actionCloseReturnFalse']}");
					
					break;
				case 'suporte_tipochamado':
					$qSuporte_tipochamado = new qSuporte_tipochamado();
					$campos['codigo'] =  (isset($campos['codigo']) and $campos['codigo']!=0)?$campos['codigo']:null;
					if(!isset($campos['titulo']))
						return $this->returnError("Você deve informar o titulo",0,"{$Request['param']['actionCloseReturnFalse']}");
					$campos['codigo'] = $qSuporte_tipochamado->save($campos['codigo'],$campos['titulo']);
					if(!$campos['codigo'])
						return $this->returnError("Erro ao gravar",0,"{$Request['param']['actionCloseReturnFalse']}");
						
					break;
				case 'suporte_chamado':
					$qSuporte_chamado = new qSuporte_chamado();
					
					$campos['codigo'] = $qSuporte_chamado->save($campos);
					if(!$campos['codigo']){
						
						return $this->returnError($qSuporte_chamado->errorStr,0,"{$Request['param']['actionCloseReturnFalse']}");
					}	
					break;
				case 'suporte_topicos':
					$qSuporte_topicos = new qSuporte_topicos();
					$campos['codigo'] =  (isset($campos['codigo']) and $campos['codigo']!=0)?$campos['codigo']:null;
					if(!isset($campos['titulo']))
						return $this->returnError("Você deve informar o titulo",0,"{$Request['param']['actionCloseReturnFalse']}");
					if(!isset($campos['codigo_projeto']))
						return $this->returnError("Você deve informar o projeto",0,"{$Request['param']['actionCloseReturnFalse']}");
					
					$campos['codigo'] = $qSuporte_topicos->save($campos['codigo'],$campos['titulo'],$campos['codigo_projeto']);
					if(!$campos['codigo'])
						return $this->returnError("Erro ao gravar",0,"{$Request['param']['actionCloseReturnFalse']}");
						
					break;
				case 'suporte_projetos':
					$qSuporte_projetos = new qSuporte_projetos();
					$campos['codigo'] =  (isset($campos['codigo']) and $campos['codigo']!=0)?$campos['codigo']:null;
					if(!isset($campos['titulo']))
						return $this->returnError("Você deve informar o titulo",0,"{$Request['param']['actionCloseReturnFalse']}");
						
					$campos['codigo'] = $qSuporte_projetos->save($campos['codigo'],$campos['titulo']);
					if(!$campos['codigo'])
						return $this->returnError("Erro ao gravar",0,"{$Request['param']['actionCloseReturnFalse']}");
						
					break;
				default:
					return $this->returnError("tabela não informada no 'widget_ajax->fn_salveform'");
					break;
			}
			
			
			//OK
			$return = array('error'=>false,'errorStr'=>'OK','codError'=>0,'actionCloseReturnTrue'=>"{$Request['param']['actionCloseReturnTrue']}",'codigo'=>$campos['codigo']);
			return $return;
			
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
		return $wgAjax->run();
		}
	}
	public function fn_delReg($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('delReg');
		//Params
		$wgAjax->setParamFunction('tabela');
		$wgAjax->setParamFunction('codigo');
		//Return
		$jsReturn = "$('#error').sysdialog({
					'title': 'Deletado com sucesso!',
					'text': 'Clique em \"Fechar\" para voltar',
					'showTitle': true,
					'open':true,
					'buttons': {
	        			btClose:{
	        				text: 'Fechar',
	        				action: \"$('#error').sysdialog('close');$('#wg_jsongrid1').widgets_jsongrid('refresh');\"
	        			}
	        		}
				});";
		$wgAjax->setActionReturn($jsReturn);

		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			/* Tabelas:
			 * 	suporte_situacao
			* 	suporte_projetos
			* 	suporte_gruposuporte
			* 	suporte_chamado
			* */
			$tabela = $Request['param']['tabela'];
			$codigo = $Request['param']['codigo'];
				
			switch ($tabela){
				case 'suporte_situacao':
					$qSuporte_situacao = new qSuporte_situacao();
					$qSuporte_situacao->run("DELETE FROM suporte_situacao WHERE codigo = '{$codigo}'");
					break;
					
				case 'suporte_tipochamado':
					$qSuporte_tipochamado = new qSuporte_tipochamado();
					$qSuporte_tipochamado->run("DELETE FROM suporte_tipochamado WHERE codigo = '{$codigo}'");						
					break;
					
				case 'suporte_topicos':
					$qSuporte_topicos = new qSuporte_topicos();
					$qSuporte_topicos->run("DELETE FROM suporte_topicos WHERE codigo = '{$codigo}'");					
					break;
					
				case 'suporte_projetos':
					$qSuporte_projetos = new qSuporte_projetos();
					$qSuporte_projetos->run("DELETE FROM suporte_projetos WHERE codigo = '{$codigo}'");					
					break;
					
				default:
					return $this->returnError("tabela não informada no 'widget_ajax->fn_delReg'");
					break;
			}
				
				
			//OK
			$return = array('error'=>false,'errorStr'=>'OK','codError'=>0);
			return $return;
				
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}
	private function returnError($errorStr,$codError = 0,$actionCloseReturnFalse= ''){
		
		$return = array('error'=>true,'errorStr'=>$errorStr,'codError'=>$codError,'actionCloseReturnTrue'=>$actionCloseReturnFalse);
		return $return;
	}
	public function fn_criaMensagem($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('criaMensagem');
		//Params
		$wgAjax->setParamFunction('codigo_chamado');
		//Return
		/*$wgAjax->setActionReturn(
		 "$('#wg_jsongridMapaCarga').widgets_jsongrid('refresh');//Atualizar Grid
				//Atualizar Mapa"
		);*/
		$jsReturn = "abrirMensagem(data.dados.codigo,data.dados.mensagem);";
	
		$wgAjax->setActionReturn($jsReturn);
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			$codigo_chamado = $Request['param']['codigo_chamado'];
			$para = ($UserAuth->id_tipo_usuario==3)?"C":"S";
			$dados = array('codigo_chamado' => $codigo_chamado,'mensagem'=>'');
			
			$query = new query();
			$sql = "SELECT codigo,mensagem  FROM suporte_mensagem WHERE codigo_chamado = {$codigo_chamado} and status = 'C' and codigo_de = {$UserAuth->id}";
			$result = $query->run($sql);
			
			if(mysql_num_rows($result)==0){
				$sql = "SELECT nome,nomefantasia FROM cvf WHERE codigo = {$UserAuth->id}";
				$result = $query->run($sql);
				list($nome, $nomefantasia) = mysql_fetch_array($result);
				$nomefantasia = ($nomefantasia=='')?$nome:$nomefantasia;
				$sql = "INSERT INTO suporte_mensagem
					(codigo_chamado, dt_envio, status, codigo_de, nome_de, para, mensagem) VALUE
					({$codigo_chamado}, now(), 'C', {$UserAuth->id}, '{$nomefantasia}', '{$para}', '')";
				$query->run($sql);
				$dados['codigo'] = $query->getIdInsert();
			} else {
				list($codigo, $mensagem) = mysql_fetch_array($result);
				$dados['codigo'] = $codigo;
				$dados['mensagem'] = $mensagem;
			}
			return array('error'=>false,'errorStr'=>'OK','dados'=> $dados);				
			
			
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}
	public function fn_deletaarquivo($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('deletaarquivo');
		//Params
		$wgAjax->setParamFunction('codigo_arquivo');
		//Return
		$jsReturn = "$('#error').sysdialog({
					'title': 'Deletado com sucesso!',
					'text': 'Clique em \"Fechar\" para voltar',
					'showTitle': true,
					'open':true,
					'buttons': {
	        			btClose:{
	        				text: 'Fechar',
	        				action: \"window.location.reload();\"
	        			}
	        		}
				});";
	
		$wgAjax->setActionReturn($jsReturn);
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			$codigo_arquivo = $Request['param']['codigo_arquivo'];
				
			$sendfile = new sendfile("query", "sendfile", 'delete_arquivo', "query/sendfile/delete_arquivo",false);
			$sendfile->action_delete_arquivo($codigo_arquivo);
			
			return array('error'=>false,'errorStr'=>'OK');
				
				
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}
	public function fn_cancelarMensagem($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('cancelarMensagem');
		//Params
		$wgAjax->setParamFunction('codigo_mensagem');
		//Return
		/*$wgAjax->setActionReturn(
		 "$('#wg_jsongridMapaCarga').widgets_jsongrid('refresh');//Atualizar Grid
				//Atualizar Mapa"
		);*/
		$jsReturn = "";
	
		$wgAjax->setActionReturn($jsReturn);
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			$codigo_mensagem = $Request['param']['codigo_mensagem'];
			
			$query = new query();
			
			$sql = "SELECT count(*) FROM suporte_mensagem WHERE codigo = {$codigo_mensagem} and status = 'C'";
			$result = $query->run($sql);
			list($countMsg) = mysql_fetch_array($result);
			if($countMsg==1){
				//LIMPAR ARQUIVOS
				$sql = "SELECT codigo,filename,codigo_chamado FROM suporte_arquivos WHERE codigo_mensagem = {$codigo_mensagem} AND codigo_enviadopor = {$UserAuth->id}";
				$result = $query->run($sql);
				while(list($codigo_arquivo,$filename,$codigo_chamado) = mysql_fetch_array($result)){
					$sql = "DELETE FROM suporte_arquivos WHERE codigo = {$codigo_arquivo}";
					$query->run($sql);
					if(is_file("files/public/users/{$codigo_chamado}/{$filename}")){
						unlink("files/public/users/{$codigo_chamado}/{$filename}");
					}
				}
					
				$sql = "DELETE FROM suporte_mensagem WHERE codigo_de = {$UserAuth->id} AND  codigo = {$codigo_mensagem}";
				$query->run($sql);
			}
			return array('error'=>false,'errorStr'=>'OK');
							
					
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}

	public function fn_excluirChamadosPorPeriodo($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('excluirChamado');
		//Params
		$wgAjax->setParamFunction('datade');
		$wgAjax->setParamFunction('dataate');
		//Return
		/*$wgAjax->setActionReturn(
		 "$('#wg_jsongridMapaCarga').widgets_jsongrid('refresh');//Atualizar Grid
				//Atualizar Mapa"
		);*/
		$jsReturn = "window.location='".URL_RAIZ.ID_EMPRESA."/chamado'";
	
		$wgAjax->setActionReturn($jsReturn);
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			if($UserAuth==false or $UserAuth->gruposuporte!='A'){
				return array('error'=>true,'errorStr'=>'Usuário não permitido');
			}
				
			$datade = $Request['param']['datade'];
			$dataate = $Request['param']['dataate'];
				
			$query = new query();

			$sql = "SELECT codigo FROM suporte_chamado WHERE dt_conclusao IS NOT NULL AND (dt_conclusao BETWEEN '{$datade}' AND '{$dataate}')";
			if(!$result = $query->run($sql)) return array('error'=>true,'errorStr'=>'Error desconhecido');
			
			while(list($codigo) = mysql_fetch_array($result)){
				$this->excluirChamado($codigo);
			}
						
			return array('error'=>false,'errorStr'=>'OK');
			
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}
	public function fn_excluirChamado($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('excluirChamado');
		//Params
		$wgAjax->setParamFunction('codigo');
		//Return
		/*$wgAjax->setActionReturn(
		 "$('#wg_jsongridMapaCarga').widgets_jsongrid('refresh');//Atualizar Grid
				//Atualizar Mapa"
		);*/
		$jsReturn = "window.location='".URL_RAIZ.ID_EMPRESA."/chamado'";
	
		$wgAjax->setActionReturn($jsReturn);
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			if($UserAuth==false or $UserAuth->gruposuporte!='A'){
				return array('error'=>true,'errorStr'=>'Usuário não permitido');
			}
			
			$codigo = $Request['param']['codigo'];
			
			if($this->excluirChamado($codigo)){			
				return array('error'=>false,'errorStr'=>'OK');
			} else {
				return array('error'=>true,'errorStr'=>'Error desconhecido');
			}	
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}
	private function excluirChamado($codigo){
		$query = new query();
		//Excluir arquivos
		$sql = "SELECT codigo, filename FROM suporte_arquivos WHERE codigo_chamado = {$codigo}";
		if(!$result = $query->run($sql)) return false;
		if(is_dir("files/public/users/{$codigo}")){
			while (list($codigo_arquivo, $arquivo) = mysql_fetch_array($result)){
				if(is_file("files/public/users/{$codigo}/".$arquivo))
					unlink("files/public/users/{$codigo}/".$arquivo);
			}
		}
		$sql = "DELETE FROM suporte_arquivos WHERE codigo_chamado = {$codigo}";
		if(!$query->run($sql)) return false;
			
		//Excluir Mensagens
		$sql = "DELETE FROM suporte_mensagem WHERE codigo_chamado = {$codigo}";
		if(!$query->run($sql)) return false;
			
		//Excluir Relatorios
		$sql = "DELETE FROM suporte_relatorio WHERE codigo_chamado = {$codigo}";
		if(!$query->run($sql)) return false;
			
		//Excluir Chamado
		$sql = "DELETE FROM suporte_chamado WHERE codigo = {$codigo}";
		if(!$query->run($sql)) return false;
		
		return true;
	}
	public function fn_salvarMensagem($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('salvarMensagem');
		//Params
		$wgAjax->setParamFunction('codigo_mensagem');
		$wgAjax->setParamFunction('mensagem');
		$wgAjax->setParamFunction('status');
		$wgAjax->setParamFunction('enviarEmail');		
		//Return
		/*$wgAjax->setActionReturn(
		 "$('#wg_jsongridMapaCarga').widgets_jsongrid('refresh');//Atualizar Grid
				//Atualizar Mapa"
		);*/
		$jsReturn = "";
	
		$wgAjax->setActionReturn($jsReturn);
		$wgAjax->setActionReturnError("alert('Error ao salvar');");
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			if($UserAuth==false){
				return array('error'=>true,'errorStr'=>'USUARIO DESCONECTADO');
			}
			
			$codigo_mensagem = $Request['param']['codigo_mensagem'];
			$mensagem = $Request['param']['mensagem'];
			$status = ($Request['param']['status']!='N')?"C":"N";
			$enviarEmail = ($Request['param']['enviarEmail']=='S')?true:false;
			
				
			$query = new query();
			$sql = "SELECT codigo_chamado,para FROM suporte_mensagem WHERE codigo = {$codigo_mensagem}";
			$result = $query->run($sql);
			if(mysql_num_rows($result)==1){
				list($codigo_chamado,$para) = mysql_fetch_array($result);
				
				$sql = "UPDATE suporte_mensagem SET mensagem = '{$mensagem}',status = '{$status}',dt_envio = now() WHERE codigo = {$codigo_mensagem} and codigo_de = '{$UserAuth->id}' and status = 'C'";
				$query->run($sql);
				
				if($status=='N'){//NOVA
					if($para=='C'){//C = para cliente
						/* //Retirado dia 2014-11-05
						$sql = "UPDATE suporte_chamado SET codigo_situacao = '2' WHERE codigo = {$codigo_chamado}";//2 = Aguardando resposta do suporte
						$query->run($sql);
						*/
						if($enviarEmail){
							$msg = "";
							$msg .= "<h4>Acesse o sistema de suporte da Starling Software</h4>";
							$msg .= "<p>O chamado #{$codigo_chamado} tem uma nova mensagem acesse o sistema para ver.</p>";
							$msg .= "<p>Clique aqui para ver: <a href=\"http://{$_SERVER['HTTP_HOST']}/chamado/ver&codigo={$codigo_chamado}#dvAncMsg\">http://suporte.starling.com.br/chamado/ver&codigo={$codigo_chamado}#dvAncMsg</a></p>";
								
							$sql = "SELECT suporte_chamado.solicitante_nome,suporte_chamado.solicitante_email,cvf.nomefantasia,cvf.email FROM cvf,suporte_chamado where suporte_chamado.codigo = {$codigo_chamado} and suporte_chamado.codigo_empresa = cvf.codigo";
							$result = $query->run($sql);
							list($solicitante_nome,$solicitante_email,$nomefantasia,$email) = mysql_fetch_array($result);
								
							$NomeTo = ($solicitante_nome=='')?$nomefantasia:$solicitante_nome;
							$EmailTo = ($solicitante_email=='')?$email:$solicitante_email;
								
							$com = new communication();
							$com->sendTemplateMensagem("O chamado #{$codigo_chamado} tem uma nova mensagem", $msg, $NomeTo, $EmailTo);
						}
					} else {//T = para tecnico
						/* //Retirado dia 2014-11-05
						$sql = "UPDATE suporte_chamado SET codigo_situacao = '1' WHERE codigo = {$codigo_chamado}";//1 = Aguandando resposta do Cliente
						$query->run($sql);
						*/
					}
				}
			}
			
			return array('error'=>false,'errorStr'=>'OK');
				
				
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}
	public function fn_criaRelatorio($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('criaRelatorio');
		//Params
		$wgAjax->setParamFunction('codigo_chamado');
		//Return
		/*$wgAjax->setActionReturn(
		 "$('#wg_jsongridMapaCarga').widgets_jsongrid('refresh');//Atualizar Grid
				//Atualizar Mapa"
		);*/
		$jsReturn = "abrirRelatorio(data.dados.codigo,data.dados.descricao,data.dados.visivel_ao_cliente);";
	
		$wgAjax->setActionReturn($jsReturn);
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			$codigo_chamado = $Request['param']['codigo_chamado'];
			$dados = array('codigo_chamado' => $codigo_chamado,'relatorio'=>'');
			if($UserAuth->id_tipo_usuario!=3){
				return array('error'=>true,'errorStr'=>'Usuário nao autorizado');
			}
			
			$query = new query();
			$sql = "SELECT codigo, visivel_ao_cliente, descricao ".
				"FROM suporte_relatorio ".
				"WHERE codigo_chamado = {$codigo_chamado} ".
					"and status = 'C' ".
					"and codigo_tecnico = {$UserAuth->id}";
			
			$result = $query->run($sql);
				
			if(mysql_num_rows($result)==0){
				$sql = "INSERT INTO suporte_relatorio
				(codigo_chamado, codigo_tecnico, visivel_ao_cliente, status, dt_inscricao, descricao) VALUE
				({$codigo_chamado}, {$UserAuth->id}, 'N', 'C', now(), '')";
				$query->run($sql);
				
				$dados['codigo'] = $query->getIdInsert();
				$dados['visivel_ao_cliente'] = 'N';//S ou N
				$dados['descricao'] = "";
			} else {
				list($codigo, $visivel_ao_cliente, $descricao) = mysql_fetch_array($result);
				$dados['codigo'] = $codigo;
				$dados['visivel_ao_cliente'] = $visivel_ao_cliente;//S ou N			
				$dados['descricao'] = $descricao;
			}
			return array('error'=>false,'errorStr'=>'OK','dados'=> $dados);
							
					
				//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
		return $wgAjax->run();
		}
		}
	public function fn_cancelarRelatorio($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('cancelarRelatorio');
		//Params
		$wgAjax->setParamFunction('codigo_relatorio');
		//Return
		$jsReturn = "";
		$wgAjax->setActionReturn($jsReturn);
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			$codigo_relatorio = $Request['param']['codigo_relatorio'];
					
			$query = new query();
					
			$sql = "DELETE FROM suporte_relatorio WHERE codigo_tecnico = {$UserAuth->id} AND  codigo = {$codigo_relatorio} AND `status` = 'C'";
			$query->run($sql);
			
			return array('error'=>false,'errorStr'=>'OK');
				
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}
	public function fn_salvarRelatorio($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('salvarRelatorio');
		//Params
		$wgAjax->setParamFunction('codigo_relatorio');//codigo do relatorio
		$wgAjax->setParamFunction('relatorio');//texto
		$wgAjax->setParamFunction('visivel_ao_cliente');//S ou N
		$wgAjax->setParamFunction('status');// C = em criacao ou S = Salvo
		//Return
		$jsReturn = "";
	
		$wgAjax->setActionReturn($jsReturn);
		$wgAjax->setActionReturnError("alert('Error ao salvar');");
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
				$UserAuth = application::getDataUserAuth();
				$codigo_relatorio = (int)$Request['param']['codigo_relatorio'];//codigo do relatorio
				$relatorio = str_replace("'", "\\'", $Request['param']['relatorio']);//texto
				$visivel_ao_cliente = ($Request['param']['visivel_ao_cliente']=='S')?"S":"N";//S ou N
				$status = ($Request['param']['status']=="S")?"S":'C';// C = em criacao ou S = Salvo					
	
				$query = new query();
	
				$sql = "UPDATE suporte_relatorio SET ".
					"descricao = '{$relatorio}', ".
					"visivel_ao_cliente = '{$visivel_ao_cliente}', ".
					"status = '{$status}', ".
					"dt_inscricao = now() ".
				"WHERE codigo = {$codigo_relatorio} and codigo_tecnico = '{$UserAuth->id}' and status = 'C'";
				$query->run($sql);
					
				return array('error'=>false,'errorStr'=>'OK');	
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}

	public function fn_listaTopicos($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('listaTopicos');
		//Params
		$wgAjax->setParamFunction('codigo_projeto');
		//Return
		$jsReturn = "setListaTopicos(data.lista);";
	
		$wgAjax->setActionReturn($jsReturn);
		$wgAjax->setActionReturnError("alert('Error ao buscar topicos');");
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			$codigo_projeto = $Request['param']['codigo_projeto'];
			
			$query = new query();
	
			$sql = "SELECT codigo,titulo FROM suporte_topicos WHERE codigo_projeto = {$codigo_projeto} or codigo_projeto = 1 order by titulo";
			$result = $query->run($sql);
			$lista = array();
			
			while(list($codigo, $titulo) = mysql_fetch_array($result)){
				$lista[] = array('codigo'=>$codigo,'titulo'=>$titulo);
			}
				
			return array('error'=>false,'errorStr'=>'OK','lista' => $lista);
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}
	public function fn_infocliente($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('infocliente');
		//Params
		$wgAjax->setParamFunction('codigo');
		//Return
		$jsReturn = "infoClienteSics(data.dados);";
	
		$wgAjax->setActionReturn($jsReturn);
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			$codigo = $Request['param']['codigo'];
				
			$query = new query();
			if($UserAuth->id_tipo_usuario==3){
				$sql = "SELECT * FROM cvf WHERE codigo = {$codigo}";
				$result = $query->run($sql);
				
				if(mysql_num_rows($result)==1){
					$dados = mysql_fetch_assoc($result);
					
					//CALCULAR SENHA
					$digCep = (int)substr($dados['cep'], -2);
					$digCpfCnpj = (int)substr($dados['cgc_cpf'], -2);					
					$dados['senha'] = ($digCep+5) * ($digCpfCnpj+7) * 999;
					
					//DEVEDOR
					$sqlDevedor = "SELECT count(reccod) FROM receber_cvf WHERE
		recsit = 'A' AND
		cancelado = 'N' AND
		lancbanc = 'N' AND
		cod_cvf = ".$dados['codigo']." AND
		( (recdtvenc < now() and enviado='S') or (recdtvenc < now()- INTERVAL 29 DAY and enviado='N') )
		ORDER BY recdtvenc";
					$result = $query->run($sqlDevedor);
					list($countDevendo) = mysql_fetch_array($result);
					
					if($countDevendo>0){
						$dados['devendo'] = 'Sim';
						$dados['quantDevendo'] = ($countDevendo==1)?', 1 Vencida':', '.$countDevendo.' Vencidas';
					} else {
						$dados['devendo'] = 'Não';
						$dados['quantDevendo'] = '';
					}
					
					
					//SERVICOS
					$sqlServicos = "SELECT s.codigo, m.descricao 
						FROM saida s INNER JOIN movsai m ON (s.codigo = m.movsaicodsaida) 
						WHERE s.cvf_cliFor = ".$dados['codigo']." 
							AND (m.descricao like 'MANUTENCAO%' OR m.descricao like 'HOSPEDAGEM%') 
							AND DATE_ADD(s.dtemissao, INTERVAL 40 DAY) >= NOW() 
							ORDER BY s.dtemissao desc";
					$result = $query->run($sqlServicos);
					
					if(mysql_num_rows($result)==0){
						$dados['servicos'] = "Nenhum";
					} else {
						$dados['servicos'] = "";
						while(list($codigo, $descricao) = mysql_fetch_array($result)){
							$dados['servicos'] = "[{$codigo}] - {$descricao}\r\n";
						}
					}
					return array('error'=>false,'errorStr'=>'OK','dados' => $dados);
					
				} else {
					
					return array('error'=>true,'errorStr'=>'Cliente não encontrado');					
				}
				
			} else {
				
				return array('error'=>true,'errorStr'=>'Usuario não tem permissão para acessar');
			}
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}
	public function fn_setgruposuporte($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('setgruposuporte');
		//Params
		$wgAjax->setParamFunction('codigo_usuario');
		$wgAjax->setParamFunction('nivel_grupo');
		//Return
		$jsReturn = "$('#wg_jsongridgruposuporte').widgets_jsongrid('refresh');";
	
		$wgAjax->setActionReturn($jsReturn);
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			$codigo_usuario = $Request['param']['codigo_usuario'];
			$nivel_grupo = $Request['param']['nivel_grupo'];
	
			$query = new query();
			if($UserAuth->id_tipo_usuario==3 and $UserAuth->gruposuporte=='A'){				

				$sql = "DELETE FROM suporte_grupo WHERE codigo_usuario = {$codigo_usuario}";
				$query->run($sql);
				
				if($nivel_grupo!=''){
					$sql = "INSERT INTO suporte_grupo (codigo_usuario,nivel_grupo) values ({$codigo_usuario},'{$nivel_grupo}')";
					$query->run($sql);
				}
				
				return array('error'=>false,'errorStr'=>'OK');
	
			} else {
	
				return array('error'=>true,'errorStr'=>'Usuario não tem permissão para acessar');
			}
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}
	public function fn_orc_auto_setValidade($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('orc_auto_setValidade');
		//Params
		$wgAjax->setParamFunction('nova_data');
		//Return
		/*$wgAjax->setActionReturn(
				"$('#wg_jsongridMapaCarga').widgets_jsongrid('refresh');//Atualizar Grid
			//Atualizar Mapa"
		);*/
		$jsReturn = "$('#error').sysdialog({
					'title': 'Data alterada com sucesso!',
					'text': 'Clique em \"Fechar\" para voltar',
					'showTitle': true,
					'open':true,
				 	'buttons': {
	        			btClose:{
	        				text: 'Fechar',
	        				action: \"window.location='".URL_RAIZ.ID_EMPRESA."/config/orc_auto'\"
	        			}
	        		}
				});";
		
		$wgAjax->setActionReturn($jsReturn);
		
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$query = new query();
			$sql = "SELECT  count(*) FROM parametro ".
				"WHERE orcamento_automatico = 'S' and confirmado_orc_automatico = 'S' and id_transportadora = ".application::getDataUserAuth()->id;
			$result = $query->run($sql);
			
			list($count) = mysql_fetch_array($result);
			if($count>0){
				$sql = "UPDATE parametro SET ".
					"dt_validade_orc_automatico = '{$Request['param']['nova_data']}' ".
					"WHERE id_transportadora = ".application::getDataUserAuth()->id;
				$query->run($sql);
				$return = array('error'=>false,'errorStr'=>'OK');
				$query->addLogAdmin($sql,'UPDATE',application::getDataUserAuth()->id,'config/orc_auto');
				return $return;				
			} else {
				$return = array('error'=>true,'errorStr'=>$sql/*'Orçamento automático não configurado'*/);
				$query->addLogAdmin("Orçamento automático não configurado",'ERRORUSER',application::getDataUserAuth()->id,'config/orc_auto');
				return $return;
			}
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
		return $wgAjax->run();
		}
	}

	public function fn_orc_auto_delItemTabela($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('orc_auto_delItemTabela');
		//Params
		$wgAjax->setParamFunction('id');
		//Return
		$wgAjax->setActionReturn(
		 "$('#wg_jsongridgridTabela').widgets_jsongrid('refresh');//Atualizar Grid"
		);
		/*
		$jsReturn = "$('#error').sysdialog({
					'title': 'Item excluido com sucesso!',
					'text': 'Item excluido com sucesso!',
					'showTitle': false,
					'open':true
				});";
		$wgAjax->setActionReturn($jsReturn);*/
	
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$query = new query();
			$sql = "DELETE FROM tabela ".
					"WHERE id = '{$Request['param']['id']}' and id_transportadora = ".application::getDataUserAuth()->id;
			$query->run($sql);
			
			$return = array('error'=>false,'errorStr'=>'OK');
			//$return = array('error'=>true,'errorStr'=>$sql);
			$query->addLogAdmin($sql,'DELETE',application::getDataUserAuth()->id,'config/orc_auto');
			return $return;
			
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	}
	public function fn_sendMailConfirm($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('sendMailConfirm');
		//Params
		$wgAjax->setParamFunction('id_usuario');
		//Return
		/*$wgAjax->setActionReturn(
				"$('#wg_jsongridMapaCarga').widgets_jsongrid('refresh');//Atualizar Grid
			//Atualizar Mapa"
		);*/
		$wgAjax->setActionReturn("alert('Mensagem foi enviada para seu email!');");
		
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$com = new communication();
			
			$com->sendConfirmEmail($Request['param']['id_usuario']);
			
			return array('error'=>false,'errorStr'=>"OK");
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
		return $wgAjax->run();
		}
	}
	public function ajax_getNameValue($isRequest = false,$functionReturn = ''){
		$wgAjax = new wgAjax('getNameValue');
		//$wgAjax->setFunctionName('getNameValue');
		//Params
		$wgAjax->addParamIn('tabela');
		$wgAjax->addParamIn('value');// valor para o WHERE		
		$wgAjax->addParamIn('campo_value');//campo no Mysql
		$wgAjax->addParamIn('campo_name');//campo no Mysql
		
		//Return
		$wgAjax->setEvalJSOut_Ok($functionReturn);
		//$wgAjax->setActionReturn($functionReturn);
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$sql = "SELECT {$Request['param']['campo_value']},{$Request['param']['campo_name']} ".
					"FROM {$Request['param']['tabela']} ".
					"WHERE {$Request['param']['campo_value']} = '{$Request['param']['value']}'";
			$query = new query();
			if($result = $query->run($sql)){
				if($result->rowCount()==0){
					$valor = '';
					$nome = '';
				} else {
					list($valor,$nome) = $result->fetchAll();
				}
			} else {
				return array('error'=>true,'errorStr'=>"Error SQL: ","sql" => $sql);
			}
			
			return array('error'=>false,'errorStr'=>"OK",'value'=>$valor,'name'=>$nome);
			//FIM***************INSIRA SEU CODIGO AQUI************************************//			
		} else {
			return $wgAjax->run();
		}
		
	}

	public function fn_saveSenha($isRequest = false){
		$wgAjax = new wgAjax();
		$wgAjax->setFunctionName('saveSenha');
		//Params
		$wgAjax->setParamFunction('senha_atual');
		$wgAjax->setParamFunction('senha_nova');
		$wgAjax->setParamFunction('senha_rnova');		
		//Return
		$wgAjax->setActionReturn("
	$('#error').sysdialog({
		'title': 'Salvo com sucesso',
		'text': 'Senha salva com sucesso',
		'showTitle': true,
		'buttons': {
			btClose:{
		    	text: 'OK',
		        action: \"window.location='".URL_RAIZ.ID_EMPRESA."/'\"
			}
		},
		'open':true
	});");
		if($isRequest){
			$Request = $wgAjax->getRequest();
			if($Request['error']==true){
				return $Request;
			}
			//INICIO***************INSIRA SEU CODIGO AQUI************************************//
			$UserAuth = application::getDataUserAuth();
			$senha_atual	= $Request['param']['senha_atual'];
			$senha_nova		= $Request['param']['senha_nova'];
			$senha_rnova	= $Request['param']['senha_rnova'];
			
			if($senha_nova!=$senha_rnova){
				return array('error'=>true,'errorStr'=>"Senhas não conferem");
			}
			
			if($senha_nova==$senha_atual){
				return array('error'=>true,'errorStr'=>"Nova senha é idêntica a atual");
			}
			
			$sql = "SELECT IF(senha='".md5($senha_atual)."',1,0) FROM cvf WHERE codigo = {$UserAuth->id}";
			$query = new query();
			if($result = $query->run($sql)){
				if(mysql_num_rows($result)==0){
					$senha_atualOk = 0;
				} else {
					list($senha_atualOk) = mysql_fetch_array($result);
				}
				if($senha_atualOk==0){
					return array('error'=>true,'errorStr'=>"Senha atual incorreta");
				}
			} else {
				return array('error'=>true,'errorStr'=>"Error SQL: ","sql" => $sql);
			}

			return array('error'=>true,'errorStr'=>"No momento está desabilitado a alteração de senha pelo site suporte, acesse o SICS para mudar sua senha");
			
			$sql = "UPDATE cvf SET senha = '".md5($senha_nova)."' WHERE codigo = {$UserAuth->id} AND senha = '".md5($senha_atual)."'";			
			if($result = $query->run($sql)){
				return array('error'=>false,'errorStr'=>"OK");
			} else {
				return array('error'=>true,'errorStr'=>"Error SQL: ","sql" => $sql);
			}
			
			
			//FIM***************INSIRA SEU CODIGO AQUI************************************//
		} else {
			return $wgAjax->run();
		}
	
	}
}