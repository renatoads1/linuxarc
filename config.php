<?php
	$config = array();

	use application\libs\controllerRest;

	// 0 - Local
	// 1 - Oficial
	// 2 - Teste
	if (!defined('LOCAL')) {
		define('LOCAL', 0);
	}
	if (!defined('OFICIAL')) {
		define('OFICIAL', 1);
	}
	if (!defined('TESTE')) {
		define('TESTE', 2);
	}

	if (is_file("../local.txt")) {
		$ambiente = LOCAL;
	}
	else if (is_file("../ambienteteste.php")) {
		$ambiente = TESTE;
	}
	else {
		$ambiente = OFICIAL;
	}

	switch ($ambiente) {
		case LOCAL://local
			$config['showSqlGrid'] = false;
			$config['showUrlGrid'] = false;
			$config['modeDebug'] = !false;
			$config['sqlDebug'] = true;
			$config['minimizedJsDebug'] = true;
			$config['urlRaiz'] = "/novoarquivamento/";
			$config['urlSite'] = "http://localhost/novoarquivamento/";
			$config['urlPosition'] = 2; //Posição na URL com o ID da emrpesa
			$config['wkhtmltopdf_dir'] = 'C:\Program Files\wkhtmltopdf\bin';
			$config['dir_libs'] = "C:/xampp/htdocs/libs/";
			$config['url_libs'] = "../libs/";

			break;
		case OFICIAL://Oficial
			$config['showSqlGrid'] = false;
			$config['showUrlGrid'] = false;
			$config['modeDebug'] = false;
			$config['sqlDebug'] = false;
			$config['minimizedJsDebug'] = false;
			$config['urlRaiz'] = "/";
			$config['urlSite'] = "http://";
			$config['urlPosition'] = 1;
			$config['wkhtmltopdf_dir'] = '';
			$config['dir_libs'] = __DIR__."/../libs/";
			$config['url_libs'] = "../libs/";
			break;
		case TESTE: //teste
			$config['showSqlGrid'] = false;
			$config['showUrlGrid'] = false;
			$config['modeDebug'] = false;
			$config['sqlDebug'] = false;
			$config['minimizedJsDebug'] = false;
			$config['urlRaiz'] = "/";
			$config['urlSite'] = "http://";
			$config['urlPosition'] = 2;
			$config['wkhtmltopdf_dir'] = '';
			$config['arquivo_url'] = '54.94.131.102:8088/arquivo.php';
			$config['dir_libs'] = __DIR__."/../../libs/";
			$config['url_libs'] = "../libs/";
			break;
		default:
			$config['showSqlGrid'] = false;
			$config['showUrlGrid'] = false;
			$config['modeDebug'] = false;
			$config['sqlDebug'] = false;
			$config['minimizedJsDebug'] = false;
			$config['urlRaiz'] = "/";
			$config['urlSite'] = "http://";
			$config['urlPosition'] = 1;
			$config['wkhtmltopdf_dir'] = '';
			$config['arquivo_url'] = 'localhost/arquivo.php';
			$config['dir_libs'] = __DIR__."/../libs/";
			$config['url_libs'] = "../libs/";
			break;
	}
	$config['dirRaiz'] = __DIR__."/";

	if (!defined('ID_APLICATIVO')) {
		define('ID_APLICATIVO', "arquivamento");
	}

	if (!defined('EMPRESA_DEFAULT')) {
		define('EMPRESA_DEFAULT', "starling");
	}

	$id_empresa = '';
    if(isset($_GET['idempresa'])){$id_empresa =$_GET['idempresa']; }
	if (isset($_GET['debug'])) {
		controllerRest::setSession($_GET['debug'] == "yes", array('mode_debug'));
	}
	if (isset(controllerRest::getIgnoreSession()["mode_debug"]) && controllerRest::getIgnoreSession()["mode_debug"]) {
		$config['showSqlGrid'] = $config['showUrlGrid'] = $config['modeDebug'] = true;
	}

	if (is_file("../local.csv")) {
		$local = file_get_contents('../local.csv');
		foreach (explode("\r\n", $local) as $linha) {
			$values = explode(";", $linha);
			if ($values[0] == 'IP_SERVIDOR_LOCAL' && isset($values[1]) && !empty($values[1])) {
				define('IP_SERVIDOR_LOCAL', $values[1]);
			}
		}
		define('IS_LOCAL', true);
		if (!defined("IP_SERVIDOR_LOCAL")) {
			define('IP_SERVIDOR_LOCAL', $_SERVER['SERVER_ADDR']);
		}
	}
	else {
		define('IS_LOCAL', false);
		define('IP_SERVIDOR_LOCAL', '54.207.117.73');
	}

	if (!defined('SHOW_SQL_GRID')) {
		define('SHOW_SQL_GRID', $config['showSqlGrid']);
	}
	if (!defined('SHOW_URL_GRID')) {
		define('SHOW_URL_GRID', $config['showUrlGrid']);
	}
	// Humberto Carvalho - 23/03/2017 - Adicionado LOG_SQL para salvar em arquivo as consultas SQL
	if (!defined('LOG_SQL')) {
		define('LOG_SQL', $config['sqlDebug']);
	}
	if (!defined('MODE_DEBUG')) {
		define('MODE_DEBUG', $config['modeDebug']);
	}
	if (!defined('MINIMIZED_JS_DEBUG')) {
		define('MINIMIZED_JS_DEBUG', $config['minimizedJsDebug']);
	}
	if (!defined('URL_RAIZ')) {
		define('URL_RAIZ', $config['urlRaiz']);
	}
	if (!defined('URL_SITE')) {
		define('URL_SITE', $config['urlSite']);
	}
	if (!defined('DIR_RAIZ')) {
		define('DIR_RAIZ', $config['dirRaiz']);
	}
	if (!defined('DIR_ARQUIVOS')) {
		define('DIR_ARQUIVOS', $config['dirRaiz']."files/private/arquivos/");
	}
	if (!defined('ID_EMPRESA')) {
		define('ID_EMPRESA', $id_empresa);
	}
    if (!defined('DIR_EMPRESA')) {
		define('DIR_EMPRESA', DIR_RAIZ."files/private/".$id_empresa."/");
        if(!is_dir(DIR_EMPRESA)){
            mkdir(DIR_EMPRESA,0700);
        }
	}
	if (!defined('URL_RAIZ_EMPRESA')) {
		define('URL_RAIZ_EMPRESA', $config['urlRaiz'].ID_EMPRESA."/");
	}
	if (!defined('URL_SITE_EMPRESA')) {
		define('URL_SITE_EMPRESA', $config['urlSite'].ID_EMPRESA."/");
	}
	if (!defined('DIR_EMPRESA')) {
		define('DIR_EMPRESA', $config['dirRaiz']."files/users/".$id_empresa."/");
	}
//	if (!defined('DIR_EMPRESA_NFE')) {
//		define('DIR_EMPRESA_NFE', $config['dirRaiz']."files/users/".$id_empresa."/nfe/");
//	}
//	if (!defined('DIR_EMPRESA_NFSE')) {
//		define('DIR_EMPRESA_NFSE', $config['dirRaiz']."files/users/".$id_empresa."/nfse/");
//	}
	if (!defined("AUTH_TOKEN_APP")) {
		define("AUTH_TOKEN_APP", "__BLANK__");
	}
	if (!defined("AUTH_URL")) {
		define("AUTH_URL", "http://".IP_SERVIDOR_LOCAL."/auth/");
	}
	if (!defined('AMBIENTE')) {
		define('AMBIENTE', $ambiente);
	}
	if (!defined('SESSION_ID')) {
		define('SESSION_ID', session_id());
	}
	if (!defined('WKHTMLTOPDF_DIR')) {
		define('WKHTMLTOPDF_DIR', $config['wkhtmltopdf_dir']);
	}
	if (!defined('DIR_LIBS')) {
		define('DIR_LIBS', $config['dir_libs']);
	}
	if (!defined('URL_LIBS')) {
		define('URL_LIBS', "/".$config['url_libs']);
	}

	//Inserção de nome do projeto e versão
	if (!defined('NOME_PROJETO')) {
		define('NOME_PROJETO', "Arquivamento");
	}
	if (!defined('SUFIXO_PROJETO')) {
		define('SUFIXO_PROJETO', "1.0");
	}
	if (!defined('USA_EMPRESA')) {
		define('USA_EMPRESA', true);
	}

	//Diego Starling Fonseca - 28/08/2017 - Sistema de versionamento
	$versao = "0.0.0.1";
	if (is_file(DIR_RAIZ."version.txt")) {
		$versao = file_get_contents(DIR_RAIZ."version.txt");
	}
	if (!defined("NUM_VERSAO_SISTEMA")) {
		define("NUM_VERSAO_SISTEMA", $versao);
	}

	if (!defined("REVISION_SISTEMA")) {
		define("REVISION_SISTEMA", explode(".", $versao)[3]);
	}

	define('SEP_CONTENT', "");
	define('SEP_CONTENTHTML', "");

	application\libs\controllerRest::startSession();
