<?php namespace files\modules\error\controller;

use application\libs\controller;
use application\libs\application;
/** controller_page
 *
 * @author Jonas R. Silva, jonasribeiro19@gmail.com
 * @copyright Starling Software
 *
 */
class index extends controller{
	public $dir_logs = "logs/";
	public $formatFile_logs = "<Date>_<type>.err";

	function __construct($action=null,$moduleError=null,$controllerError=null,$actionError=null, $opqError=null){
		$this->toAction['moduleError'] = $moduleError;
		$this->toAction['controllerError'] = $controllerError;
		$this->toAction['actionError'] = $actionError;
		$this->toAction['opqError'] = $opqError;

		parent::__construct("error","index",$action);
	}
	function __destruct(){
		//$query = $this->query;
		$dir = $this->dir_logs;
		$fileName = $this->formatFile_logs;
		$fileName = str_ireplace("<Date>", date('Y-m-d',time()), $fileName);
		$fileName = str_ireplace("<type>", $this->action, $fileName);

		if(!is_dir($dir)){
			mkdir($dir,0777);
		}

		$error = "--BEGIN--Error {$this->action}\r\n";

		$error .= "-- ".$this->toAction['moduleError']."/".$this->toAction['controllerError']."/".$this->toAction['actionError']."/".$this->toAction['opqError']."\r\n";
		switch ($this->action){
			case 'error404':
				$error .= "File not found\r\n";
				break;
			default:
				$error .= implode("\r\n", application::$errors);
				break;
		}
		$error .= "--END\r\n";

		$handle = fopen($dir.$fileName, 'a');
		fwrite($handle, $error);
		fclose($handle);
	}
}
