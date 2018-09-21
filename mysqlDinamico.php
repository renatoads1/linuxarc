<?php

date_default_timezone_set('America/Sao_Paulo');
include_once '../conexao_sta.php';

use application\libs\database;
use application\libs\application;
use application\libs\controllerRest;
use files\lib\auth;
class mysqlDinamico {
	const TSYSTEM_SICS = 'SICS';
	const TSYSTEM_PROJETOTSP = 'PROJETO';
	private $bloquear_login = true;
	private $plano_expirou = true;
	private $plano_venceu = true;
	private $sem_usuario = false;
	public function __construct(){

		if(defined('MYSQLSTA_PASSCRIPT') && MYSQLSTA_PASSCRIPT){
			$senha = application::getDecrypt(MYSQLSTA_USERPASS);
		} else {
			$senha = MYSQLSTA_USERPASS;
		}
		database::addInstance(MYSQLSTA_DATABASE, "sistema", "mysql", MYSQLSTA_HOST, MYSQLSTA_PORT, MYSQLSTA_USERNAME, $senha);

		//if (!in_array(controllerRest::getSession()['ID_EMPRESA'], array("sicsweb", "files")))
		if (!in_array(ID_EMPRESA, array("sicsweb", "files")))
			$this->addInstanceDefault();
		else {
			$this->sem_usuario = true;
		}

		if(!defined("BLOQUEAR_LOGIN"))
			define("BLOQUEAR_LOGIN", $this->bloquear_login);
		if(!defined("EXPIROU"))
			define("EXPIROU", $this->plano_expirou);
		if(!defined("VENCEU"))
			define("VENCEU", $this->plano_venceu);
		if(!defined("SEM_USUARIO"))
			define("SEM_USUARIO", $this->sem_usuario);
	}

	static function checarEmpresa($id_empresa = null){
		if(is_null($id_empresa)){
			if(!defined('ID_EMPRESA'))
				return false;

			$id_empresa = ID_EMPRESA;
		}

		if(isset(controllerRest::getIgnoreSession()['ID_EMPRESA']) && strtolower(controllerRest::getIgnoreSession()['ID_EMPRESA'])!=strtolower($id_empresa)){

			if (!defined('ID_EMPRESA'))
				define("ID_EMPRESA",strtolower($id_empresa));
			if(!defined("SESSION_ID"))
				define("SESSION_ID",session_id());
			//$certificado = new certificado();
			//$certificado->close();

			controllerRest::unsetSession();
			controllerRest::startSession();
		}
		return true;
	}
	/** Adiciona conector default(da empresa cliente)
	 *
	 * @since 06/10/2016
	 * @author Jonas Silva <jonasribeiro19@gmail.com>
	 *
	 */
	public function addInstanceDefault(){
		if(ID_EMPRESA=='system')
			return $this->addInstanceSystem();

		$this->checarEmpresa();
		$cSics = $this->getDatabaseInfor(mysqlDinamico::TSYSTEM_SICS);
		if($cSics!=false){
			if($cSics['data_validade']=='0000-00-00'){
				$this->plano_venceu = false;
				$this->plano_expirou = false;
				$this->bloquear_login = false;
				controllerRest::createSession('dataValidade', "31/12/9999");
				controllerRest::createSession('dataVencimento', "31/12/9999");
				controllerRest::createSession('diffVal', 9999);
				controllerRest::createSession('diffVenc', 9999);
			} else {
				//Ver Validades
				$dataAtual = new DateTime(date('Y-m-d'));
				$dataValidade = new DateTime($cSics['data_validade']);
				//Data de vencimento = data de validade + PRAZO_VALIDADE
				$dataVencimento = clone $dataValidade;
				$dataVencimento->add(new DateInterval('P'.PRAZO_VALIDADE.'D'));
				//Calcula a diferença entre a data atual e a data de validade e formata para mostrar os dias.
				$intervaloVal = $dataAtual->diff($dataValidade);
				$diffVal = $intervaloVal->format("%r%a"); // Armazena a diferença em dias com número signed
				//Calcula a diferença entre a data atual e a data de vencimento e formata para mostrar os dias.
				$intervaloVenc = $dataAtual->diff($dataVencimento);
				$diffVenc = $intervaloVenc->format("%r%a"); // Armazena a diferença em dias com número signed
				//Armazena a validade e a diferença em dias para o término da validade, para serem exibidas para o cliente
				controllerRest::createSession('dataValidade', $dataValidade->format("d/m/Y"));
				controllerRest::createSession('dataVencimento', $dataVencimento->format("d/m/Y"));
				controllerRest::createSession('diffVal', $diffVal);
				controllerRest::createSession('diffVenc', $diffVenc);
				/*Se a diferença for negativa, significa que expirou a validade. Neste caso,
				 * verifica se está dentro do prazo de PRAZO_VALIDADE dias depois e se não, bloqueia o login.*/
				if($diffVal < 0){
					if($diffVenc < 0){
						if(isset(controllerRest::getSession()['aut']['ID_EMPRESA']))
							controllerRest::unsetSession(array('aut','ID_EMPRESA'));
					} else {
						$this->plano_venceu = false;
					}
				} else {
					$this->plano_expirou = false;
					$this->plano_venceu = false;
					$this->bloquear_login = false;
				}
			}
			//Descriptografar senha do banco
			$senha_banco = application::getDecrypt($cSics['senha_banco']);

			database::addInstance(
				$cSics['banco'],
				"default",
				"mysql",
				$cSics['ipservidor'],
				$cSics['porta'],
				$cSics['usuario_banco'],
				$senha_banco
			);

			if(!defined('USA_PROJETOTSP')){
				try {
					$sql = "SELECT usa_projetotsp FROM parametro LIMIT 1";
					$param = array();
					$result = database::runPrepared($sql, $param, 'default');
					list($usa_projetotsp) = $result->fetch();
					$usa_projetotsp = strtoupper($usa_projetotsp)=='S';
				} catch (\Exception $e) {
					$usa_projetotsp = false;
				}

				if($usa_projetotsp){
					try {
						$cProjetoTSP = $this->getDatabaseInfor(mysqlDinamico::TSYSTEM_PROJETOTSP);
					} catch (\Exception $e) {
						$usa_projetotsp = false;
					}
				}

				define('USA_PROJETOTSP', $usa_projetotsp);
			}
			return true;
		} else {
			return false;
		}
	}
	/** Adiciona conector default(da empresa cliente) acesso como system
	 * Exemplo http://localhost/sicsweb/system?cpfCnpj=11952737000120&senha=a
	 * @since 06/10/2016
	 * @author Jonas Silva <jonasribeiro19@gmail.com>
	 *
	 */
	private function addInstanceSystem () {
		if(defined('MYSQLSTA_PASSCRIPT') && MYSQLSTA_PASSCRIPT){
			$senha = application::getDecrypt(MYSQLSTA_USERPASS);
		} else {
			$senha = MYSQLSTA_USERPASS;
		}
		database::addInstance(MYSQLSTA_DATABASE, "default", "mysql", MYSQLSTA_HOST, MYSQLSTA_PORT, MYSQLSTA_USERNAME, $senha);

		$logar = true;
		if(isset($_GET['cpfCnpj'])){
			$cpfCnpj = $_GET['cpfCnpj'];
		} elseif(isset($_POST['cpfCnpj'])){
			$cpfCnpj = $_POST['cpfCnpj'];
		} else {
			$cpfCnpj = NULL;
		}
		if(!isset(controllerRest::getSession()['cpfCnpj']))
			controllerRest::createSession('cpfCnpj', NULL);
		if(is_null($cpfCnpj))
			$cpfCnpj = controllerRest::getSession()['cpfCnpj'];
		controllerRest::createSession('cpfCnpj', $cpfCnpj);

		if($cpfCnpj==controllerRest::getSession()['cpfCnpj'] && isset(controllerRest::getSession()['aut']['connected']) && controllerRest::getSession()['aut']['connected']){
			$logar = false;
		}
		 if(is_null(controllerRest::getSession()['cpfCnpj']))
		 	throw new \Exception("Acesso negado, informe cpfCnpj");

		 if($logar){
		 	$sql = "SELECT * FROM cvf WHERE cgc_cpf = ?";
			$param = array(controllerRest::getSession()['cpfCnpj']);
		 	$result = database::runPrepared($sql, $param);

		 	if($result->rowCount()==0)
		 		throw new \Exception("Acesso negado");
		 	$dadosCvf = $result->fetch();

			$param = array(mysqlDinamico::TSYSTEM_SICS, $dadosCvf['codigo']);
		 	$result = database::runPrepared("SELECT count(*) FROM bancodedados WHERE sistema = ? AND idusuario = ?", $param, "sistema");
			list($clienteNetSics) = $result->fetch();
			if ($clienteNetSics >= 1) {
				header("location: ".URL_RAIZ."{$dadosCvf['usuario']}");
				exit;
			}

			$this->checarEmpresa();

		 	controllerRest::createSession('dataValidade', '01/01/2050');
		 	controllerRest::createSession('dataVencimento', '01/01/2050');
		 	controllerRest::createSession('diffVal', 365);
		 	controllerRest::createSession('diffVenc', 365);
		 	controllerRest::createSession('numMsgNaoLidas', 0);

		 	controllerRest::createSession('aut', array(
		 			'connected' => true,
		 			'user' => array (
		 					'id' => $dadosCvf['codigo'],
		 					'nome' => $dadosCvf['nome'],
		 					'tipo' => 3,
		 					'tipos' => array(auth::USERTYPE_VENDEDOR,auth::USERTYPE_ADM),
		 					'nivelacesso' => 0,
		 					'status' => auth::USERSTATUS_ATIVO,
		 					'username' => $dadosCvf['usuario'],
		 					'email' => $dadosCvf['email'],
		 					'telefone' => $dadosCvf['tel1']),
		 			'empresa' => array(
		 					'id' => ID_EMPRESA,
		 					'nome' => $dadosCvf['nome'],
		 					'cpfCnpj' => $dadosCvf['cgc_cpf'],
		 					'dados' => array(
		 							'cecod' => 0,
		 							'cerazfan' => $dadosCvf['nome'],
		 							'cerazcom' => $dadosCvf['nomefantasia'],
		 							'ceendrua' => $dadosCvf['endereco'],
		 							'ceendbai' => $dadosCvf['bairro'],
		 							'ceendcep' => $dadosCvf['cep'],
		 							'ceendcid' => $dadosCvf['cidade'],
		 							'ceendest' => $dadosCvf['uf'],
		 							'cetel1' => $dadosCvf['tel1'],
		 							'ceramal1' => $dadosCvf['ramaltel1'],
		 							'cetel2' =>$dadosCvf['tel2'],
		 							'ceramal2' => $dadosCvf['ramaltel2'],
		 							'cefax' => $dadosCvf['tel1'],
		 							'ceramalfax' => $dadosCvf['ramaltel1'],
		 							'ceemail' => $dadosCvf['email'],
		 							'ceinsc' => $dadosCvf['insc_rg'],
		 							'cecgc' => $dadosCvf['cgc_cpf'],
		 							'numero_logradouro' => $dadosCvf['nro_endereco'],
		 							'complemento_logradouro' =>  $dadosCvf['complemeto_endereco'],
		 							'dtcaixa' => 0,
		 							'numserie' => 0,
		 							'codmunicipio' => $dadosCvf['codmunicipio'],
		 							'numvetordll' => 0,
		 							'insc_municipal' => $dadosCvf['inscmunicipal']
		 					)
		 			)
		 	));
		 	if(isset(controllerRest::getSession()['webservices']))
		 		controllerRest::unsetSession(array('webservices'));

		 }
		 define('USA_PROJETOTSP', false);

		 if(auth::isAuth()){
		 	$this->plano_expirou = false;
		 	$this->plano_venceu = false;
		 	$this->bloquear_login = false;
		 }
	}
	/** Gera configuração de bancos da empresa na sessao
	 *
	 * @since 06/10/2016
	 * @author Jonas Silva <jonasribeiro19@gmail.com>
	 *
	 */
	private function geraDatabaseInfo(){
		$database = array();

		if (ID_EMPRESA == 'sicsweb')
			return false;

		try {
			database::connectInstance('sistema');
			$sql = "SELECT codigo,nome,1 FROM cvf WHERE inativo = 'N' AND usuario = ?";
			$param = array(ID_EMPRESA);
			$result = database::runPrepared($sql, $param, "sistema");
			if($result->rowCount()==0)
				throw new \Exception("Empresa não localizada");

			list($codigo, $nome,$plano) = $result->fetch();
			$param = array($codigo);
			$result = database::runPrepared("SELECT * FROM bancodedados WHERE idusuario = ?", $param, "sistema");
			//sistema = 'SICS' AND
			if($result->rowCount()==0)
				throw new \Exception("Banco de dados não encontrado, entre em contato com suporte");

			while($bancos = $result->fetch(\PDO::FETCH_ASSOC)){
				$TSystem = strtoupper( $bancos['sistema'] );
				$database[ $TSystem ] = $bancos;
			}

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode());
			return false;
		}
		controllerRest::getSession()['database'] = $database;

		return true;
	}
	/** Pega configuração de bancos da empresa na sessao
	 *
	 * @since 06/10/2016
	 * @author Jonas Silva <jonasribeiro19@gmail.com>
	 *
	 * @param string $typeSystem
	 */
	public function getDatabaseInfor ($typeSystem = null) {
		if (!isset(controllerRest::getSession()['database'])) {
			$this->geraDatabaseInfo();
		}
		if (!is_null($typeSystem)) {
			if(!isset(controllerRest::getSession()['database'][$typeSystem])){
				throw new \Exception("Banco de dados não encontrado, entre em contato com suporte");
			}
			return controllerRest::getSession()['database'][$typeSystem];
		}
		return controllerRest::getSession()['database'];
	}
}
