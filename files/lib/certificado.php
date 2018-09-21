<?php namespace files\lib;

use application\libs\controllerRest;

class certKeys{
	public $priKeyFile;
	public $priKey;
	public $pubKeyFile;
	public $pubKey;
	public $certFile;
	public $cert;
	public $password;
	//protected $pfxFile;
	//protected $pfx;

	public function __construct($priKeyFile,$pubKeyFile,$certFile,$password = null){
		$priKey = is_file($priKeyFile)?file_get_contents($priKeyFile):null;
		$pubKey = is_file($pubKeyFile)?file_get_contents($pubKeyFile):null;
		$cert = is_file($certFile)?file_get_contents($certFile):null;
		//$pfx = is_file($pfxFile)?file_get_contents($pfxFile):null;

		$this->priKeyFile	= $priKeyFile;
		$this->priKey		= $priKey;
		$this->pubKeyFile	= $pubKeyFile;
		$this->pubKey		= $pubKey;
		$this->certFile		= $certFile;
		$this->cert			= $cert;
		$this->password		= $password;
		//$this->pfxFile		= $pfxFile;
		//$this->pfx			= $pfx;
	}
}
class certificado{
	private $pathCertificado = "";
	private $filename_pfx = "";
	private $filename_privateKey = "";
	private $filename_publicKey	= "";
	private $filename_certificado = "";
	private $certificadoPass = "";
	private $certificadoIsOpend = false;
	private $certificadoIsError = false;
	private $certificadoStrError = "";
	private $log = "";
	private $name = "";
	private $CommonName = "";
	private $OrgUnit = "";
	private $Location = "";
	private $State = "";
	private $Country = "";
	private $timeValid = 0;
	private $timeValidFrom = 0;
	public function __construct(){
		$this->pathCertificado = "files/users/".ID_EMPRESA."/certificados/";

		$this->filename_pfx = isset(controllerRest::getSession()['webservices']['filename_pfx'])?controllerRest::getSession()['webservices']['filename_pfx']:null;
		$this->certificadoPass = isset(controllerRest::getSession()['webservices']['certificadoPass'])?controllerRest::getSession()['webservices']['certificadoPass']:"";
		$this->filename_privateKey = SESSION_ID."_priKEY.pem";
		$this->filename_publicKey = SESSION_ID."_pubKEY.pem";
		$this->filename_certificado = SESSION_ID."_certKEY.pem";
	}
	/** Pega keys abertas do certificado
	 *
	 * @return boolean|certKeys Se error retornar False
	 */
	public function getKeys(){
		$returnLoad = $this->load();
		if($returnLoad===true){
			$priKeyFile	= $this->pathCertificado.$this->filename_privateKey;
			$pubKeyFile	= $this->pathCertificado.$this->filename_publicKey;
			$certFile	= $this->pathCertificado.$this->filename_certificado;
			//$filename_pfx'	=> $this->filename_pfx;
			$pass = $this->certificadoPass;

			return new certKeys($priKeyFile, $pubKeyFile, $certFile,$pass);
		} else {
			$this->setLog($returnLoad,3);
			return false;
		}
	}


	public function uploadCert(){
		$auth = auth::getInfoUser();

		if($auth===false)
			$this->returnErrorJSON("Usuário não conectado");

		if(!isset($_FILES['filePfx']))
			$this->returnErrorJSON("O arquivo do certificado deve ser enviado");

		$certPass = isset($_POST['certPass'])?$_POST['certPass']:"";
		$filename_pfxName = SESSION_ID."_".$_FILES['filePfx']['name'];

		//Verifica Pastas---------------------------------------------------------//
		if(!is_dir("files/users"))
			mkdir("files/users",0777);

		if(!is_dir("files/users/".ID_EMPRESA))
			mkdir("files/users/".ID_EMPRESA,0777);

		if(!is_dir("files/users/".ID_EMPRESA."/certificados"))
			mkdir("files/users/".ID_EMPRESA."/certificados",0777);

		$filename_pfx = "files/users/".ID_EMPRESA."/certificados/".$filename_pfxName;

		configNfe::clearFiles("files/users/".ID_EMPRESA."/certificados/");

		if(is_file($filename_pfx))
			unlink($filename_pfx);
		//Verifica Pastas---------------------------------------------------------//
		if(!@move_uploaded_file($_FILES['filePfx']['tmp_name'],$filename_pfx)){
			$this->returnErrorJSON("Erro ao abrir certificado");
		}

		controllerRest::createSession('filename_pfx', $filename_pfx, array('webservices'));
		controllerRest::createSession('certificadoPass', $certPass, array('webservices'));
		$this->filename_pfx = $filename_pfx;
		$this->certificadoPass = $certPass;

		//Diego Starling Fonseca - 02/06/2016
		/*Constrói o arquivo de configuração a partir do modelo, carrega, seta os parâmetros
		 * "nome do certificado" e "senha" e grava de volta no arquivo.*/
		configNfe::buildConfig();
		configNfe::loadConfigFromFile(DIR_EMPRESA . 'config.json');
		configNfe::setParams(array(
			'certPfxName'=>$filename_pfxName,
			'certPassword'=>$certPass
		));
		configNfe::writeConfigToFile();
		//FIM -- Diego Starling Fonseca - 07/06/2016

		$return = $this->load();
		if($return===true){
			$this->returnNormalJSON();
		} else {
			$this->returnNormalJSON(array(),true,$return);
		}
	}
	public function infoCertificate(){
		$return = array();
		$return['Name'] = $this->name;
		$return['CommonName'] = $this->CommonName;
		$return['OrgUnit'] = $this->OrgUnit;
		$return['Location'] = $this->Location;
		$return['State'] = $this->State;
		$return['Country'] = $this->Country;
		$return['timeValidFrom'] = $this->timeValidFrom;
		$return['timeValid'] = $this->timeValid;
		return $return;
	}
	public function timeValid(){
		return $this->timeValid;
	}
	public function formUpload($withPanel = true,$reduzido = false){

		$out = "";
		if($withPanel){
			$out .= "<div class=\"panel panel-default\">";// style=\"width:50%;margin:5px auto;\"
			$out .= "<div class=\"panel-heading\"><span class=\"fa fa-key\"></span> Certificado Digital</div>";
			$out .= "<div class=\"panel-body\">";
		}
		$certificadoAberto = false;
		if(isset(controllerRest::getSession()['webservices']['filename_pfx']) and !is_null(controllerRest::getSession()['webservices']['filename_pfx'])){
			$resultValid = $this->checkValid();
			$certificadoAberto = $resultValid['status'];
			if($reduzido){
				if($resultValid['status']){
					$out .= "<div class=\"btn-group\">";

					$out .= "<div class=\"btn btn-default disabled\">".
						"<span class=\"fa fa-check\"></span> Certificado aberto".
						"</div>";
					$out .= "<div id=\"btnFecharCert\" class=\"btn btn-danger\">".
						"<span class=\"fa fa-times\"></span> Fechar".
						"</div>";

					$out .= "</div>";
					if($this->CommonName!='')
						$out .= "<p style=\"font-size:0.8em;margin:10px auto;\" class=\"text-success\"><strong>Nome:</strong> {$this->CommonName}</p>";
					if($this->Location!='' || $this->State!=''){
						$out .= "<p style=\"font-size:0.8em;\" class=\"text-success\"><strong>Localidade:</strong> ";
						$sep = "";
						if($this->Location!=''){
							$out .= $sep."{$this->Location}";
							$sep = " - ";
						}
						if($this->State!=''){
							$out .= $sep."{$this->State}";
						}
						$out .= "</p>";
					}
				} else {
					$out .= "<div class=\"panel panel-danger\">";
					$out .= "<div class=\"panel-body\">";

					$out .= "<div class=\"text-danger\">";
					$out .= "{$resultValid['strError']}";
					$out .= "</div>";

					$out .= "</div></div>";
				}
			} else {
				if($resultValid['status']){
					$out .= "<div class=\"panel panel-success\">";
					$out .= "<div class=\"panel-body\">";
					$out .= "<div class=\"text-success\"><span class=\"fa fa-check\"></span> Certificado aberto com sucesso.</div>";
					$out .= "</div>";
					$out .= "</div>";
				} else {
					$out .= "<div class=\"panel panel-danger\">";
					$out .= "<div class=\"panel-body\">";
					$out .= "<div class=\"text-danger\"><span class=\"fa fa-exclamation-triangle\"></span> {$resultValid['strError']}</div>";
					$out .= "</div>";
					$out .= "</div>";
				}

				$out .= "<strong>Detalhes:</strong><div style=\"margin:10px 0;\">";
				if($this->CommonName!='')
					$out .= "<p><strong>Nome:</strong> {$this->CommonName}</p>";
				if($this->OrgUnit!='')
					$out .= "<p><strong>Certificado por:</strong> <pre>{$this->OrgUnit}</pre></p>";

				if($this->Location!='' or $this->State!='' or $this->Country!=''){
					$out .= "<p><strong>Localidade:</strong> ";
					$sep = "";
					if($this->Location!=''){
						$out .= $sep."{$this->Location}";
						$sep = ", ";
					}
					if($this->State!=''){
						$out .= $sep."{$this->State}";
						$sep = " - ";
					}

					if($this->Country!='')
						$out .= $sep."{$this->Country}";


					$out .= "</p>";
				}
				if($this->timeValid>0)
					$out .= "<p><strong>Válido:</strong> ". date("d/m/Y", $this->timeValidFrom)." até ".date("d/m/Y", $this->timeValid)."</p>";
				$out .= "</div>";

				$out .= "<button id=\"btnFecharCert\" style=\"width:100%;margin:5px 0;\" class=\"btn btn-danger\"><span class=\"fa fa-times\"></span> Fechar certificado</button>";
			}
			$out .= "<script>";
			$out .= "$('#btnFecharCert').click(function(){ $.staAjaxJSON('".URL_RAIZ.ID_EMPRESA."/certificado/index/fechar',{},{method: 'GET',
				fncSuccess: function(data){ window.location.reload(); },
				fncFailed: function(xhr, ajaxOptions, thrownError){ window.location.reload(); }
				});
			});";
			$out .= "</script>";
		}
		//INICIO-Certificado Fechado-------------------------------------//
		if(!$certificadoAberto){
			$out .= "<div id=\"dvprogress\" class=\"progress\" style=\"display:none;\">";
			$out .= "<div id=\"dvprogress_bar\" class=\"progress-bar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\">";
			$out .= "<span id=\"dvprogress_label\">0%</span>";
			$out .= "</div>";
			$out .= "</div>";

			$out .= "<form id=\"formFilePfx\" enctype=\"multipart/form-data\" method=\"post\">";
			$out .= "<div id=\"dvStatusUpload\" style=\"display:none;\">";
			$out .= "<div id=\"dvprogressUpload\"></div>";
			$out .= "</div>";
			$out .= "<div id=\"dvFilePfx_input\" style=\"display:none;\">";


			$out .= "<input type=\"file\" id=\"filePfx_input\" name=\"filePfx\" />";
			$out .= "</div>";
			$out .= "<div id=\"dvFilePfx_btn\"><button id=\"filePfx_btn\" class=\"btn btn-success\" style=\"width:100%;\"><span class=\"fa fa-lock\"></span> Selecionar certificado</button></div>";
			$out .= "<div id=\"dvCertPass\" style=\"display:none;\">";
			$out .= "<div id=\"dvFilePfx_label\" style=\"font-weight:bold;text-align:center;margin:10px;font-size:1.2em;\"></div>";
			$out .= "<div class=\"text-primary\" style=\"font-weight:bold;\">Senha do Certificado:</div>".
				" <input type=\"password\" id=\"passPfx_input\" name=\"certPass\" class=\"form-control\" />";
			$out .= "<div id=\"dvFilePfx_btns\" class=\"btn-group btn-group-justified\" style=\"margin:5px 0;\">";
			$out .= "<a class=\"btn btn-success\" href=\"#\" id=\"filePfx_btnSend\"><span class=\"fa fa-check\"></span> Abrir</a>";
			$out .= "<a class=\"btn btn-default\" href=\"#\" id=\"filePfx_btn1\"><span class=\"fa fa-lock\"></span> Selecionar novamente</a>";
			$out .= "</div>";
			$out .= "</div>";
			$out .= "</form>";

			$out .= "<script>";
			$out .= "$('#filePfx_btn,#filePfx_btn1').click(function(){\r\n";
			$out .= "	$('#filePfx_input').click();\r\n";
			$out .= "	return false;\r\n";
			$out .= "});";

			$out .= "$('#filePfx_input').change(function(){\r\n";
			$out .= "	var arq = this.files[0];\r\n";
			$out .= "	$('#dvFilePfx_label').html(arq.name + \" (\"+parseInt(arq.size/1024)+\" KB)\");\r\n";
			$out .= "	$('#dvFilePfx_btn').hide();\r\n";
			$out .= "	$('#dvCertPass').fadeIn(500);\r\n";

			//Diego Starling Fonseca - 03/06/2016
			$out .= "	$('#passPfx_input').focus();\r\n";
			//FIM - Diego Starling Fonseca - 03/06/2016
			$out .= "});";

			//Diego Starling Fonseca - 03/06/2016
			$out .= "$('#passPfx_input').keypress(function(e){
						if(e.which == 13){
							$('#filePfx_btnSend').click();
							return false;
						}
					});";
			//FIM - Diego Starling Fonseca - 03/06/2016

			$out .= "$('#filePfx_btnSend').click(function(){\r\n";
			$out .= "	$('#formFilePfx').submit();\r\n";
			$out .= "	return false;\r\n";
			$out .= "});\r\n";

			$out .= "
function setProgressUpload(current){
	$('#dvprogressUpload').progbar({
		'current':current,
		'max':100,
		'showText':false,
		'viewOnlyPercent':false,
		'style': 'progress-bar-success',
		'open':true
	});
}
function showDialog(title, content,fncButtonOK){
	if(typeof fncButtonOK!='string')
		var fncButtonOK = '';

	$.staDialog({
	'title': title,
	'text': content,
	'type': 'default',
	'buttons': {
		btClose:{
			iconLeft: 'fa fa-check',
			text: 'OK',
			action: fncButtonOK+\"$.staDialog('close')\",
			type: 'primary'
		}
	},
	'showTitle': true,
	'open': true
});
}";
			$out .= "$('#formFilePfx').submit(function() {\r\n";
			$out .= "	setProgressUpload(0);";
			$out .= "	var fd = new FormData(document.getElementById('formFilePfx'));\r\n";
			$out .= "	$('#dvprogress').show();\r\n";
			$out .= "	$.ajax({\r\n";
			$out .= "		type: 'POST',\r\n";
			$out .= "		dataType: 'JSON',\r\n";
			$out .= "		enctype: 'multipart/form-data',\r\n";
			$out .= "		processData: false,\r\n";
			$out .= "		contentType: false,\r\n";
			$out .= "		url: '".URL_RAIZ.ID_EMPRESA."/certificado/index/upload_json',\r\n";
			$out .= "		data: fd,\r\n";
			$out .= "		xhr: function(){\r\n";
			$out .= "			var xhr = new window.XMLHttpRequest();\r\n";
			$out .= "			//Upload progress\r\n";
			$out .= "			xhr.upload.addEventListener('progress', function(evt){\r\n";
			$out .= "				if (evt.lengthComputable) {\r\n";
			$out .= "					var percentComplete = evt.loaded / evt.total;\r\n";
			$out .= "					var perc = parseInt(percentComplete*100);\r\n";
			$out .= "					setProgressUpload(perc);\r\n";
			$out .= "					if(percentComplete==1) $('#dvprogress').hide();\r\n";
			$out .= "				}\r\n";
			$out .= "		}, false);\r\n";
			$out .= "		return xhr;\r\n";
			$out .= "		},\r\n";
			$out .= "		success: function(data){\r\n";
			$out .= "			if(data.status){\r\n";
			$out .= "				window.location.reload();\r\n";
			$out .= "			} else {\r\n";
			$out .= "				showDialog('Error',data.strError );\r\n";
			$out .= "			}\r\n";
			$out .= "		},\r\n";//Fim-Success
			$out .= "		error: function(xhr, ajaxOptions, thrownError){\r\n";
			$out .= "			showDialog('Error',thrownError);";
			$out .= "		}\r\n";//Fim-Error
			$out .= "	});\r\n";//Fim-Ajax
			$out .= "	return false;\r\n";
			$out .= "});\r\n";//Fim-submit
			$out .= "</script>";
		}

		if($withPanel){
			$out .= "</div>";//Fim Panel-Body
			$out .= "</div>";//Fim Panel
		}
		//$out .= "<div class=\"clearfix\"></div>";
		return $out;
	}
	public function certificadoIsOpend(){
		return $this->certificadoIsOpend;
	}
	public function certificadoGetError(){
		if($this->certificadoIsError){
			return $this->certificadoStrError;
		}
		return false;
	}
	public function setLog($log, $type = 0){
		switch ($type){
			case 1:
				$log = "[ALERT] ".$log;
			//showError($log,(isset(controllerRest::getSession()['usuario']['id'])?controllerRest::getSession()['usuario']['id']:0),"alerta", false);
			break;
			case 2:
				$log = "[ERROR] ".$log;
			//showError($log,(isset(controllerRest::getSession()['usuario']['id'])?controllerRest::getSession()['usuario']['id']:0),"Erro", true);
			break;
			case 3:
				throw new \Exception($log);
				$log = "[ERROR_FATAL] ".$log;

			//showError($log,(isset(controllerRest::getSession()['usuario']['id'])?controllerRest::getSession()['usuario']['id']:0),"erro_fatal", true);
			break;
			default:
				$log = "[INFO] ".$log;
			break;
		}
		$this->log .= $log."\r\n";
	}
	public function getLog(){
		return $this->log;
	}
	/** Fecha certificado
	 *
	 */
	public function close(){
		$privateKey		= $this->pathCertificado.$this->filename_privateKey;
		$publicKey		= $this->pathCertificado.$this->filename_publicKey;
		$certificado	= $this->pathCertificado.$this->filename_certificado;

		if(is_file($privateKey)) unlink($privateKey);
		if(is_file($publicKey)) unlink($publicKey);
		if(is_file($certificado)) unlink($certificado);

		$this->certificadoIsOpend = false;
	}
	/** Fecha e Apaga certificados no Servidor
	 *
	 *
	 */
	public function clear(){
		$this->close();
		$filename_pfx	= $this->filename_pfx;

		if(is_file($filename_pfx))
			unlink($filename_pfx);

		if(isset(controllerRest::getSession()['webservices']['filename_pfx']))
			controllerRest::unsetSession(array('webservices','filename_pfx'));
		if(isset(controllerRest::getSession()['webservices']['certificadoPass']))
			controllerRest::unsetSession(array('webservices','certificadoPass'));
	}
	public function checkValid(){
		$certificado	= $this->pathCertificado.$this->filename_certificado;
		if(!is_file($certificado)){
			$result = $this->load();
			if(!($result===true)){
				return array('status'=>false, 'strError'=>$result);
			}
		}

		return $this->__validCerts();

	}
	public function load(){
		$privateKey		= $this->pathCertificado.$this->filename_privateKey;
		$publicKey		= $this->pathCertificado.$this->filename_publicKey;
		$certificado	= $this->pathCertificado.$this->filename_certificado;
		$filename_pfx	= $this->filename_pfx;

		//Monta o caminho completo ate o certificado pfx
		if(is_null($filename_pfx)){
			$this->setLog("Certificado nao aberto!",3);
			$this->certificadoIsOpend = false;
			return "Certificado nao aberto";
		}
		if(!is_file($filename_pfx)){
			$this->clear();
			$this->setLog("Certificado inacessivel!",3);
			$this->certificadoIsOpend = false;
			return "Certificado inacessivel";
		} else {
			$this->setLog("Certificado localizado",0);
		}

		$x509certdata = array();
		//carrega o certificado em um string
		if(!$key = file_get_contents($filename_pfx)){
			return "Erro ao abrir arquivo";
		}

		//carrega os certificados e chaves para um array denominado $x509certdata
		if (!openssl_pkcs12_read($key,$x509certdata,$this->certificadoPass) ){
			$this->setLog("Certificado não Carregado",0);
			$this->setLog("O Certificado não pode ser lido! Provavelmente corrompido ou com formato inválido ou Senha!",1);
			return "<h4>O Certificado não pode ser usado! </h4>".
				"O que pode ser:<br /><ul><li>Arquivo corrompido</li> ".
				"<li>Formato do arquivo é inválido</li> ".
				"<li>Senha digitada é incorreta</li></ul>";
		} else {
			$this->setLog("Certificado Carregado",0);
		}

		//verifica se arquivo com a chave publica jc existe
		if(file_exists($publicKey)) unlink($publicKey);
		//Verifica se arquivo já existe
		if(file_exists($privateKey)) unlink($privateKey);

		//Salva a chave publica no formato pem para uso do SOAP
		file_put_contents($publicKey,$x509certdata['cert']);

		//Salva a chave privada no formato pem para uso so SOAP
		if ( !file_put_contents($privateKey,$x509certdata['pkey']) ){
			$this->setLog("Impossivel gravar no diretório!!! Permissão negada!!",3);
			$this->setLog("[ERROR]Impossivel gravar no diretorio");
			return "[ERROR]Impossivel gravar no diretorio";
		} else {
			$this->setLog("Certificado gravado na pasta");
		}

		//Salva o certificado completo no formato pem
		file_put_contents($certificado, $x509certdata['pkey']."\r\n".$x509certdata['cert']);

		//Verifica sua validade
		$aResp = $this->__validCerts();
		if ($aResp['status']==false){
			$this->setLog("Certificado invalido! - " . $aResp['strError'],3);
			$this->setLog("Certificado invalido",0);
			return "Certificado invalido - " . $aResp['strError'];
		} else {
			$this->setLog("Certificado valido",0);
		}

		$this->certificadoIsOpend = true;


		return true;
	}

	/** __validCerts - Checar validade do certificado
	 * @return array
	 * status boolean(true|false),
	 * error string,
	 * meses int,
	 * dias int,
	 */
	protected function __validCerts($cert = null){
		//$privateKey		= $this->pathCertificado.$this->filename_privateKey;
		$publicKey		= $this->pathCertificado.$this->filename_publicKey;
		//$certificado	= $this->pathCertificado.$this->filename_certificado;
		//$filename_pfx	= $this->filename_pfx;
		//$cert = null;

		if(is_file($publicKey)){
			$this->certificadoIsOpend = true;
			$cert = file_get_contents($publicKey);
		} else {
			$this->certificadoIsOpend = false;
		}

		if (is_null($cert)){
			$this->setLog("Certificado não foi aberto.", 3);
			return array('status'=>false,'strError'=>"Certificado não foi aberto.");
		}
		if (!$data = openssl_x509_read($cert)){
			$this->setLog("Erro no certificado, ele não pode ser lido pelo OpenSSL.",3);
			return array('status'=>false,'strError'=>"Erro no certificado, ele não pode ser lido pelo OpenSSL.");
		}

		$status = true;
		$strError = "";
		$cert_data = openssl_x509_parse($data);
		//var_dump($cert_data);exit;
		$this->name = $cert_data['name'];
		$this->CommonName	= isset($cert_data['subject']['CN'])?$cert_data['subject']['CN']:'';
		$this->OrgUnit		= "";
		if(isset($cert_data['subject']['OU']) and is_array($cert_data['subject']['OU'])){
			foreach ($cert_data['subject']['OU'] as $indice=>$ou){
				$this->OrgUnit		.= "[".($indice+1)."] ".$ou."\r\n";
			}
		} else {
			$this->OrgUnit		.= $cert_data['subject']['OU']."\r\n";
		}
		$this->Location		= isset($cert_data['subject']['L'])?$cert_data['subject']['L']:'';
		$this->State		= isset($cert_data['subject']['ST'])?$cert_data['subject']['ST']:"";
		$this->Country		= isset($cert_data['subject']['C'])?$cert_data['subject']['C']:'';

		//Reformata a data de validade - yy-mm-dd
		$anoF = substr($cert_data['validFrom'],0,2);
		$mesF = substr($cert_data['validFrom'],2,2);
		$diaF = substr($cert_data['validFrom'],4,2);
		//obtem o timeestamp da data de validade do certificado
		$dValidFrom = gmmktime(0,0,0,$mesF,$diaF,$anoF);

		//Reformata a data de validade - yy-mm-dd
		$ano = substr($cert_data['validTo'],0,2);
		$mes = substr($cert_data['validTo'],2,2);
		$dia = substr($cert_data['validTo'],4,2);
		//obtem o timeestamp da data de validade do certificado
		$dValid = gmmktime(0,0,0,$mes,$dia,$ano);

		$this->timeValidFrom = $dValidFrom;
		$this->timeValid = $dValid;
		// obtem o timestamp da data de hoje
		$dHoje = gmmktime(0,0,0,date("m"),date("d"),date("Y"));
		// compara a data de validade com a data atual
		if ($dValid < $dHoje ){
			$status = false;
			$strError = "Erro no certificado: Certificado expirou em {$dia}/{$mes}/{$ano}";
		}
		//diferenca em segundos entre os timestamp
		$diferenca = $dValid - $dHoje;
		// convertendo para dias
		$diferenca = round($diferenca /(60*60*24),0);
		//carregando a propriedade
		$daysToExpire = $diferenca;
		//Convertendo para meses e carregando a propriedade
		$m = ($ano * 12 + $mes);
		$n = (date("y") * 12 + date("m"));
		//numero de meses ate o certificado expirar
		$monthsToExpire = ($m-$n);
		if($status==false){
			$this->setLog($strError,3);
		}

		return array('status'=>$status,'strError'=>$strError,'name'=>$this->name,'meses'=>$monthsToExpire,'dias'=>$daysToExpire,'dtExpire'=>$cert_data['validTo']);
	}
	/** Retirar tags do certificado como -----BEGIN CERTIFICATE
	 * @param $certFile
	 */
	protected function retirarTagsCert($certFile){
		//carregar a chave publica do arquivo pem
		if (!$pubKey = file_get_contents($certFile)){
			$this->setLog("Error Certificado: Error ao abrir chave publica.",3);
			return false;
		}
		//inicializa variavel
		$data = '';
		//carrega o certificado em um array usando o LF como referencia
		$arCert = explode("\n", $pubKey);
		foreach ($arCert AS $curData) {
			//remove a tag de inicio e fim do certificado
			if (/*!empty($curData) &&*/ strncmp($curData, '-----BEGIN CERTIFICATE', 22) != 0 && strncmp($curData, '-----END CERTIFICATE', 20) != 0 ) {
				//carrega o resultado numa string
				//$data .= $curData."\n";
				$data .= trim($curData);
			}
		}
		return $data;
	}
	function assinarXml($docxml = '', $tagid = ''){
		$privateKey		= $this->pathCertificado.$this->filename_privateKey;
		$publicKey		= $this->pathCertificado.$this->filename_publicKey;
		$certificado	= $this->pathCertificado.$this->filename_certificado;
		$filename_pfx	= $this->filename_pfx;

		if ( $tagid == '' ){
			$this->setLog("Uma tagId deve ser indicada para que seja assinada", 3);
			return false;
		}
		if ( $docxml == '' ){
			$this->setLog("Nenhum xml foi passado para ser assinado", 3);
			return false;
		}

		if(!$this->certificadoIsOpend){
			$this->setLog("Erro no certificado: Certificado não aberto.", 3);
			return false;
		}

		if(is_file($privateKey)){
			$this->setLog("Erro no certificado: Chave não existe ou certificado não aberto.", 3);
			return false;
		}
		//Obter o chave privada para a ssinatura
		if(!$priv_key = file_get_contents($privateKey)){
			$this->setLog("Erro no certificado: Erro ao abrir chave Privada", 3);
			return false;
		}
		if(is_file($privateKey)){
			$this->setLog("Erro no certificado: Chave não existe ou certificado não aberto.", 3);
			return false;
		}
		//Obter o chave Publico para a ssinatura
		if(!$pub_key = file_get_contents($publicKey)){
			$this->setLog("Erro no certificado: Erro ao abrir chave Privada", 3);
			return false;
		}

		//Pegar privKey
		if(!$pkeyid = openssl_pkey_get_private($priv_key, $this->certificadoPass)){
			$this->setLog("Erro com o certificado", 2);
			return false;
		}
		//Obter a chave publica
		$certPub = $this->retirarTagsCert($pub_key);
		$docxml = str_replace(array("\r", "\n", "\r\n", "\t"),"", $docxml);

		//Calcular o hash dos dados
		$hashValue = hash('sha1',$docxml,true);

		//$this->log = "";
		$this->setLog("Sign ".$tagid);

		//Converte o valor para base64 para serem colocados no xml
		$digValue = base64_encode($hashValue);

		//monta a tag da assinatura digital
		$sign = '<Signature xmlns="http://www.w3.org/2000/09/xmldsig#" Id="Ass_'.$tagid.'">';
		//SignedInfo
		$signInfo = '<SignedInfo xmlns="http://www.w3.org/2000/09/xmldsig#">';
		$signInfo .= '<CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"></CanonicalizationMethod>';
		$signInfo .= '<SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"></SignatureMethod>';
		$signInfo .= '<Reference URI="#'.$tagid.'">';
		$signInfo .= '<Transforms>';
		$signInfo .= '<Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"></Transform>';
		$signInfo .= '<Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></Transform>';
		$signInfo .= '</Transforms>';
		$signInfo .= '<DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"></DigestMethod>';
		$signInfo .= '<DigestValue>'.$digValue.'</DigestValue>';
		$signInfo .= '</Reference>';
		$signInfo .= '</SignedInfo>';

		// extrai os dados a serem assinados para uma string
		$dados = $signInfo;
		//$this->setLog("Signature de:\n".$dados);
		//inicializa a variavel que irc receber a assinatura
		//executa a assinatura digital usando o resource da chave privada
		$signature = '';
		if($resp = openssl_sign($dados,$signature,$pkeyid, OPENSSL_ALGO_SHA1))
		{
			$this->setLog("Sign OK");
		} else {
			$this->setLog("Sign Error",1);
		}
		//codifica assinatura para o padrao base64
		$signatureValue = base64_encode($signature);
		//$this->setLog("signatureValue:\n".$signatureValue);
		$sign .= str_replace(' xmlns="http://www.w3.org/2000/09/xmldsig#"', "", $signInfo);
		$sign .= '<SignatureValue>'.$signatureValue.'</SignatureValue>';
		$sign .= '<KeyInfo>';
		$sign .= '<X509Data>';
		$sign .= '<X509Certificate>'.$certPub.'</X509Certificate>';
		$sign .= '</X509Data>';
		$sign .= '</KeyInfo>';
		$sign .= '</Signature>';
		openssl_free_key($pkeyid);//Libera chave privada

		return $sign;//Retorna TagAssinada
	}
	/** assinarXmlDoc Cria a assinatura do xml
	 *
	 * @param DOMDocument $xmldoc
	 * @param DOMElement $root
	 * @param DOMElement $node
	 * @return string xml assinado
	 * @internal param DOMDocument $xmlDoc
	 */
	public function assinarXmlDoc(&$xmldoc, $root, $node){

		if(!$this->certificadoIsOpend){
			$this->setLog("Erro no certificado: Certificado não aberto.", 3);
			return false;
		}
		if(!$keys = $this->getKeys()){
			$this->setLog("Erro no certificado: Chave não existe ou certificado não aberto.", 3);
			return false;
		}

		$privateKey = $keys->priKeyFile;
		$priv_key = $keys->priKey;
		$publicKey = $keys->pubKeyFile;
		$pub_key = $keys->pubKey;
		$certFile = $keys->certFile;
		$cert = $keys->cert;
		$certPass = $keys->password;

		if(is_null($priv_key) || is_null($pub_key) || is_null($cert)){
			$this->setLog("Erro no certificado: Certificado não aberto(Chaves não encontradas).", 3);
			return false;
		}
		/*
		if(is_file($privateKey)){
			$this->setLog("Erro no certificado: Chave não existe ou certificado não aberto.", 3);
			return false;
		}
		//Obter o chave privada para a ssinatura
		if(!$priv_key = file_get_contents($privateKey)){
			$this->setLog("Erro no certificado: Erro ao abrir chave Privada", 3);
			return false;
		}
		if(is_file($privateKey)){
			$this->setLog("Erro no certificado: Chave não existe ou certificado não aberto.", 3);
			return false;
		}
		//Obter o chave Publico para a ssinatura
		if(!$pub_key = file_get_contents($publicKey)){
			$this->setLog("Erro no certificado: Erro ao abrir chave Privada", 3);
			return false;
		}*/

		//Pegar privKey
		if(!$pkeyid = openssl_pkey_get_private($priv_key, $certPass)){
			$this->setLog("Erro com o certificado", 2);
			return false;
		}

		$nsDSIG = 'http://www.w3.org/2000/09/xmldsig#';
		$nsCannonMethod = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
		$nsSignatureMethod = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
		$nsTransformMethod1 ='http://www.w3.org/2000/09/xmldsig#enveloped-signature';
		$nsTransformMethod2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
		$nsDigestMethod = 'http://www.w3.org/2000/09/xmldsig#sha1';

		//pega o atributo id do node a ser assinado
		$idSigned = $node->getAttribute("Id");

		if(empty($idSigned) || is_null($idSigned))
			$idSigned = $node->getAttribute("id");

		if(empty($idSigned) || is_null($idSigned))
			$idSigned = '';

		$idSigned = trim($idSigned);

		//extrai os dados da tag para uma string na forma canonica
		$dados = $node->C14N(true, false, null, null);
		//calcular o hash dos dados
		$hashValue = hash('sha1', $dados, true);
		//converter o hash para base64
		$digValue = base64_encode($hashValue);


		//cria o node <Signature>-------------------------------------------------
		$signatureNode = $xmldoc->createElementNS($nsDSIG, 'S1:Signature');
		//adiciona a tag <Signature> ao node raiz
		$root->appendChild($signatureNode);
		//cria o node <SignedInfo>
		$signedInfoNode = $xmldoc->createElement('S1:SignedInfo');
		//adiciona o node <SignedInfo> ao <Signature>
		$signatureNode->appendChild($signedInfoNode);
		//cria no node com o método de canonização dos dados
		$canonicalNode = $xmldoc->createElement('S1:CanonicalizationMethod');
		//adiona o <CanonicalizationMethod> ao node <SignedInfo>
		$signedInfoNode->appendChild($canonicalNode);
		//seta o atributo ao node <CanonicalizationMethod>
		$canonicalNode->setAttribute('Algorithm', $nsCannonMethod);
		//cria o node <SignatureMethod>
		$signatureMethodNode = $xmldoc->createElement('S1:SignatureMethod');
		//adiciona o node <SignatureMethod> ao node <SignedInfo>
		$signedInfoNode->appendChild($signatureMethodNode);
		//seta o atributo Algorithm ao node <SignatureMethod>
		$signatureMethodNode->setAttribute('Algorithm', $nsSignatureMethod);
		//cria o node <Reference>
		$referenceNode = $xmldoc->createElement('S1:Reference');
		//adiciona o node <Reference> ao node <SignedInfo>
		$signedInfoNode->appendChild($referenceNode);
		//seta o atributo URI a node <Reference>
		$referenceNode->setAttribute('URI', '#'.$idSigned);
		//cria o node <Transforms>
		$transformsNode = $xmldoc->createElement('S1:Transforms');
		//adiciona o node <Transforms> ao node <Reference>
		$referenceNode->appendChild($transformsNode);
		//cria o primeiro node <Transform> OBS: no singular
		$transfNode1 = $xmldoc->createElement('S1:Transform');
		//adiciona o primeiro node <Transform> ao node <Transforms>
		$transformsNode->appendChild($transfNode1);
		//set o atributo Algorithm ao primeiro node <Transform>
		$transfNode1->setAttribute('Algorithm', $nsTransformMethod1);
		//cria outro node <Transform> OBS: no singular
		$transfNode2 = $xmldoc->createElement('S1:Transform');
		//adiciona o segundo node <Transform> ao node <Transforms>
		$transformsNode->appendChild($transfNode2);
		//set o atributo Algorithm ao segundo node <Transform>
		$transfNode2->setAttribute('Algorithm', $nsTransformMethod2);
		//cria o node <DigestMethod>
		$digestMethodNode = $xmldoc->createElement('S1:DigestMethod');
		//adiciona o node <DigestMethod> ao node <Reference>
		$referenceNode->appendChild($digestMethodNode);
		//seta o atributo Algorithm ao node <DigestMethod>
		$digestMethodNode->setAttribute('Algorithm', $nsDigestMethod);
		//cria o node <DigestValue>
		$digestValueNode = $xmldoc->createElement('S1:DigestValue', $digValue);
		//adiciona o node <DigestValue> ao node <Reference>
		$referenceNode->appendChild($digestValueNode);
		//extrai node <SignedInfo> para uma string na sua forma canonica
		$cnSignedInfoNode = $signedInfoNode->C14N(true, false, null, null);
		//cria uma variavel vazia que receberá a assinatura
		$signature = '';
		//calcula a assinatura do node canonizado <SignedInfo>
		//usando a chave privada em formato PEM
		//if (! openssl_sign($cnSignedInfoNode, $signature, $objSSLPriKey)) {//TODO
		if(!openssl_sign($cnSignedInfoNode,$signature,$pkeyid, OPENSSL_ALGO_SHA1)){
			//Libera chave privada
			openssl_free_key($pkeyid);

			$this->setLog("Houve erro durante a assinatura digital.",3);
			return false;
		}
		//converte a assinatura em base64
		$signatureValue = base64_encode($signature);
		//cria o node <SignatureValue>
		$signatureValueNode = $xmldoc->createElement('S1:SignatureValue', $signatureValue);
		//adiciona o node <SignatureValue> ao node <Signature>
		$signatureNode->appendChild($signatureValueNode);
		//cria o node <KeyInfo>
		$keyInfoNode = $xmldoc->createElement('S1:KeyInfo');
		//adiciona o node <KeyInfo> ao node <Signature>
		$signatureNode->appendChild($keyInfoNode);
		//cria o node <X509Data>
		$x509DataNode = $xmldoc->createElement('S1:X509Data');
		//adiciona o node <X509Data> ao node <KeyInfo>
		$keyInfoNode->appendChild($x509DataNode);
		//remove linhas desnecessárias do certificado
		$pubKeyClean = $this->zCleanPubKey($pub_key);
		//cria o node <X509Certificate>
		$x509CertificateNode = $xmldoc->createElement('S1:X509Certificate', $pubKeyClean);
		//adiciona o node <X509Certificate> ao node <X509Data>
		$x509DataNode->appendChild($x509CertificateNode);
		//Libera chave privada
		openssl_free_key($pkeyid);

		return true;
	}
	/**
	 * zCleanPubKey
	 * Remove a informação de inicio e fim do certificado
	 * contido no formato PEM, deixando o certificado (chave publica) pronta para ser
	 * anexada ao xml da NFe
	 * @return string contendo o certificado limpo
	 */
	protected function zCleanPubKey($pubKey)
	{
		//inicializa variavel
		$data = '';
		//carrega o certificado em um array usando o LF como referencia
		$arCert = explode("\n", $pubKey);
		foreach ($arCert as $curData) {
			//remove a tag de inicio e fim do certificado
			if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) != 0 &&
			strncmp($curData, '-----END CERTIFICATE', 20) != 0 ) {
				//carrega o resultado numa string
				$data .= trim($curData);
			}
		}
		return $data;
	}
	/** Retorna mensagem JSON (de Error)
	 *
	 * @param unknown $strError Descricao do error
	 */
	private function returnErrorJSON($strError){
		$this->returnNormalJSON(array(),false,$strError);
	}
	/** Retorna mensagem JSON (Normal)
	 *
	 * @param array $return Dados de retorno
	 * @param string $status Status, Se True = Success / False = Error
	 * @param string $strError Descricao do error
	 */
	private function returnNormalJSON($return=array(),$status=true,$strError=null){
		$return['status'] = $status;
		$return['strError'] = is_null($strError)?'':$strError;

		echo json_encode($return);
		exit;
	}
}
