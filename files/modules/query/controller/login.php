<?php namespace files\modules\query\controller;

use application\libs\controller;
use application\libs\application;
use application\libs\controllerRest;

class login extends controller{
	public function action_default(){
		$this->skin = 'txt';
		$username = isset($_POST['username'])?$_POST['username']:'';
		$userpass = isset($_POST['userpass'])?$_POST['userpass']:'';

		$this->toViewer['out'] = json_encode($this->getLogin($username,$userpass));


		$this->getViewer();
	}
	public function getLogin($username,$userpass){

		$conectado = array();
		$conectado['conectado'] = 0;
		if(!empty($username) and !empty($userpass)){
			$username = str_replace("'", "\\'", $username);

			if(!$result = $GLOBALS['database']->runQuery("SELECT cvf.codigo,cvf.senha,cvf.entidade_vendedor,cvf.nomefantasia,cvf.email,cvf.tel1 FROM cvf WHERE (cvf.usuario = '".strtolower($username)."')")){
				//$application->getError($GLOBALS['database']->getError());
				application::getError($GLOBALS['database']->getError());
			} else {
				if(mysql_num_rows($result)==1){
					list($id_usuario,$senha,$entidade_vendedor,$nomefantasia,$email,$telefone) = mysql_fetch_array($result);
					if(!isset(controllerRest::getSession()['aut']['id_'.$id_usuario]['error_senha'])){
						controllerRest::createSession('error_senha', 0, array('aut', 'id_'.$id_usuario));
					}
					if(controllerRest::getSession()['aut']['id_'.$id_usuario]['error_senha']!=5 and md5($userpass)==$senha){
						$conectado['conectado'] = 1;
						$conectado['error'] = 0;//usuário conectado

						controllerRest::createSession('aut', array(
							'id_'.$id_usuario => array(
								'error_senha' => 0
							),
							'user' => array(
								'id' => $id_usuario,
								'nome' => $nomefantasia,
								'nome_fantasia' => $nomefantasia,
								'id_tipo_usuario' => ($entidade_vendedor=='S'?3:1),
								'tipo_usuario' => ($entidade_vendedor=='S'?'Administrador':'Usuario'),
								'id_status_usuario' => 1
							)
						));

						$sql = "SELECT count(codigo_usuario),nivel_grupo FROM suporte_grupo WHERE  codigo_usuario = '{$id_usuario}'";
						$result = $GLOBALS['database']->runQuery($sql);
						list($countUsuario,$nivel_grupo) = mysql_fetch_array($result);

						controllerRest::createSession('gruposuporte', ($countUsuario=1)?$nivel_grupo:'', array('aut', 'user'));
						controllerRest::createSession('status_usuario', 'ativo', array('aut', 'user'));
						controllerRest::createSession('user_login', $username, array('aut', 'user'));
						controllerRest::createSession('email', $email, array('aut', 'user'));
						controllerRest::createSession('telefone', $telefone, array('aut', 'user'));
						controllerRest::createSession('senha', $userpass, array('aut', 'user'));
						controllerRest::createSession('latitude', 0, array('aut', 'user'));
						controllerRest::createSession('longitude', 0, array('aut', 'user'));
						controllerRest::createSession('connected', true, array('aut', 'user'));
					} elseif(controllerRest::getSession()['aut']['id_'.$id_usuario]['error_senha']==5) {
						$conectado['conectado'] = 0;//false
						$conectado['error'] = 3;//Usuário bloqueado

					} else {
						/*controllerRest::getSession()['aut']['id_'.$id_usuario]['error_senha']++;

						if(controllerRest::getSession()['aut']['id_'.$id_usuario]['error_senha']==5){
							//Bloquear usuário
						}*/
						$conectado['conectado'] = 0;//false
						$conectado['error'] = 2;//Senha incorreta
					}
				} else {
					$conectado['conectado'] = 0;//false
					$conectado['error'] = 1;//Usuário nao encontrado
				}
			}
		}
		return $conectado;
	}
}
