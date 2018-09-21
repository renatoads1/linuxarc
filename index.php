<?php
session_set_cookie_params(0, '/');
date_default_timezone_set ('America/Sao_Paulo');
function my_autoload ($class) {
	$file = str_replace('\\', '/', $class . '.php');
	// var_dump($class, $file);
	$includesPath = explode(';', ini_get('include_path').';../libs/');
	if (is_file('./'.$file)) {//Se no projeto
		require_once('./'.$file);
	} else {
		foreach ($includesPath as $path) {//Se nas pastas de path
			if (is_file($path.$file)) {
				require_once($path.$file);
			}
		}
	}
}
/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */
spl_autoload_register('my_autoload');
session_cache_limiter('private, must-revalidate');
session_start();

use application\libs\application;
use application\libs\controllerRest;

include 'config.php';
include 'mysql.php';
if(!isset(controllerRest::getSession()['aut']['connected'])){
    controllerRest::getSession()['aut']['connected'] = false;
}
////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// Precisa deslogar ///////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if (false) 
    {// Colocar todas as verificações se o usuário precisa deslogar aqui
	controllerRest::deleteSession();
	header('Location: '.URL_RAIZ_EMPRESA);
}
////////////////////////////////////////////////////////////////////////////////
///////////////////////////// Fim precisa deslogar /////////////////////////////
////////////////////////////////////////////////////////////////////////////////

$getsAdicionais = "";
if (isset($_GET['navLeftHide']))
	$getsAdicionais .= "&navLeftHide={$_GET['navLeftHide']}";
if (isset($_GET['navTopHide']))
	$getsAdicionais .= "&navTopHide={$_GET['navTopHide']}";
global $getsAdicionais;

//if (USA_EMPRESA) {
//	$mysqlDinamico = new mysqlDinamico();
//}
$application = new application();
