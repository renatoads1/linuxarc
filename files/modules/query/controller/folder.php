<?php namespace files\modules\query\controller;

use application\libs\controllerRest;
use files\lib\parametrosSICS;
use application\libs\query;

const SPLIT_SPACES = 4;

class folder extends controllerRest {

	public function action_default () {
		$isLocal = false;
		if ($isLocal) {
			switch ($this->action) {
				case 'download':
					$this->download_local();
					break;
				case 'list':
					$this->list_local();
					break;
				default:break;
			}
		} else {
			switch ($this->action) {
				case 'download':
				case 'list':
					$this->remoto($this->action);
					break;
				default:break;
			}
		}
	}
	public function remoto ($tipo) {
		$this->addInput('id');
		$this->addInput('path');
		$this->addInput('context');
		$this->addInput('selection', null, false, '[]');
		$this->addInput('name', null, false, '');
		$inputs = $this->getInputs();
		$param = new parametrosSICS();
		$inputs['caminho_servidor'] = $param->getParam('caminho_servidor');
		$inputs['tipo'] = $tipo;
		$bancoDeDados = controllerRest::getSession()['database']['SICS']['banco'];
		$inputs['empresa'] = $this->encode($bancoDeDados, rand(20, 100));

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, ARQUIVO_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $inputs);

		$output = curl_exec($ch);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($output, 0, $header_size);
		$body = substr($output, $header_size);

		curl_close($ch);

		if (gettype(strpos($header, "Content-Description: File Transfer")) == 'integer') {
			foreach (explode("\r\n", $header) as $key => $value) {
				if ($value != "") {
					header($value);
				}
			}
		}

		echo $body;
	}
	public function download_local () {
		die('deprecated use remote instead or update this one');
		$this->addInput('id');
		$this->addInput('path');
		$this->addInput('context');
		$this->addInput('selection', null, false, '[]');
		$this->addInput('name', null, false, '');
		$inputs = $this->getInputs();
		$param = new parametrosSICS();
		$query = new query();

		$inputs['selection'] =  json_decode($inputs['selection']);

		switch ($inputs['context']) {
			case 'pedido':
				$pasta = $param->getParam('caminho_servidor');
				$exclude[] = 'entidade';
				$exclude[] = 'financeiro';
				break;
			default: $this->returnRestError('Contexto desconhecido', 410, controllerRest::TYPE_FATALERROR);
		}
		if ($inputs['path'] == '.')
			$inputs['path'] = '/'.$inputs['context'];
		switch ($inputs['path']) {
			case '/pedido':
				$subPasta = str_replace('../', '', $inputs['id']);//TODO Tem que fazer uma validação pra não deixar o cara colocar ../ ou ..\ ou qualquer outra coisa que permita ele mudar a pasta
				break;
			case '/fatura':
				$subPasta = $query->runPrepared('select codnf from faturas where orc_pv_oc_de_origem = ?', array(intval($inputs['id'])))->fetch(\PDO::FETCH_NUM)[0];
				break;
			default:
				$this->returnRestError('Caminho desconhecido', 411, controllerRest::TYPE_FATALERROR);
		}
		$caminho = $pasta.$inputs['path']."/".$subPasta."/";

		if ($inputs['name'] != '' && is_file($caminho.$inputs['name'])) {
			$this->printFile($caminho, $inputs['name']);
			if (strpos($inputs['name'], 'temp/arquivos_') === 0)
				@unlink($caminho.$inputs['name']);
			if (is_dir($caminho."temp") && count(glob($caminho."temp/*")) == 0)
				@rmdir($caminho."temp");
			return;
		}
		if (count($inputs['selection']) == 0 && $inputs['name'] == '') {
			$this->returnRestError('Sem arquivos', 412, controllerRest::TYPE_FATALERROR);
		} elseif (count($inputs['selection']) == 1) {
			if (is_file($caminho.$inputs['selection'][0])) {
				$this->addData('name', $inputs['selection'][0]);
				$this->printReturn();
			} elseif (!is_dir($caminho.$inputs['selection'][0]) && !is_file($caminho.$inputs['selection'][0])) {
				// $nameZip = $this->compressFiles(array(), $caminho);
				// if (gettype($nameZip) == 'string')
				// 	$this->addData('name', $nameZip);
				// else $this->returnRestError('Não foi possível gerar o zip<br>Código: '.$nameZip, 413, controllerRest::TYPE_FATALERROR);
				// $this->printReturn();
				$this->returnRestError('Para baixar todo o conteúdo de uma pasta, entre na mesma, clique no botão <button class=\'btn btn-default btn-xs disabled\'><i class=\'fa fa-check-circle\' ></i> Marcar todos</button> e depois em <button class=\'btn btn-default btn-xs disabled\'><i class=\'fa fa-cloud-download\' ></i> Baixar</button>', 415, controllerRest::TYPE_ERROR);
			}

		} else {
			$nameZip = $this->compressFiles($inputs['selection'], $caminho);
			if (gettype($nameZip) == 'string')
				$this->addData('name', $nameZip);
			else $this->returnRestError('Não foi possível gerar o zip<br>Código: '.$nameZip, 414, controllerRest::TYPE_FATALERROR);
			$this->printReturn();
		}
	}
	public function list_local () {
		die('deprecated use remote instead or update this one');
		$this->addInput('id');
		$this->addInput('path');
		$this->addInput('context');
		$inputs = $this->getInputs();

		$param = new parametrosSICS();
		//Verificar permissões também
		$exclude = array();
		switch ($inputs['context']) {
			case 'pedido':
				$pasta = $param->getParam('caminho_servidor');
				$exclude[] = 'entidade';
				$exclude[] = 'financeiro';
				break;
			default: $this->returnRestError('Contexto desconhecido', 410, controllerRest::TYPE_FATALERROR);
		}
		if ($pasta == '')
			$pasta = DIR_RAIZ;
		$files = array();
		$inputs['path'] =  str_replace('../', '', $inputs['path']);
		if ($inputs['path'] != '') {
			$files[] = array(
				'nome' => 'Voltar',
				'tipo' => 'chevron-left',
				'size' => 0,
				'goto' => '',
				'class' => 'skip'
			);
			switch ($inputs['path']) {
				case '/pedido':
					$subPasta = str_replace('../', '', $inputs['id']);
					break;
				case '/fatura':
					$query = new query();
					$subPasta = $query->runPrepared('select codnf from faturas where orc_pv_oc_de_origem = ?', array(intval($inputs['id'])))->fetch(\PDO::FETCH_NUM)[0];
					break;
				default:
					$subPasta = '';
					break;
			}
			$inputs['path'] = $inputs['path']."/".$subPasta;
		}
		$arquivos = glob($pasta.$inputs['path']."/*");
		$this->addData('path', $inputs['path']);
		foreach ($arquivos as $key => $value) {
			if ($inputs['path'] == '' && in_array(basename($value), $exclude))
				continue;
			if (is_file($value))
				$files[] = array(
					'nome' => basename($value),
					'tipo' => $this->getFileIcon(basename($value)),
					'size' => filesize($value),
					'goto' => '',
					'class' => ''
				);
			else
				$files[] = array(
					'nome' => basename($value),
					'tipo' => 'folder-o',
					'size' => 0,
					'goto' => $inputs['path'].'/'.basename($value),
					'class' => ''
				);
		}
		$this->addData('files', $files);
		$this->printReturn();
	}
	/**
	 * Retorna o ícone apropriado para um tipo de arquivo
	 * @param string $name nome do arquivo
	 * @return string com o nome do ícone
	*/
	private function getFileIcon ($name) {
		$ext = substr($name, strrpos($name, '.') + 1);
		switch ($ext) {
			case 'jpg':
			case 'png':
			case 'gif':
			case 'ico':
				return 'file-image-o';
			case 'doc':
			case 'docx':
				return 'file-word-o';
			case 'xls':
			case 'xlsx':
				return 'file-excel-o';
			case 'ppt':
			case 'pptx':
				return 'file-powerpoint-o';
			case 'pdf':
				return 'file-pdf-o';
			case 'php':
			case 'xml':
			case 'html':
				return 'file-code-o';
			case 'txt':
			case 'rtf':
				return 'file-text-o';
			case 'mp3':
				return 'file-audio-o';
			case 'mp4':
			case 'mov':
			case 'avi':
				return 'file-video-o';
			case 'zip':
			case 'rar':
			case '7z':
			case 'tar':
			case 'gz':
				return 'file-zip-o';
			default:
				return 'file-o';
		}
	}
	private function compressFiles ($listFiles, $defaultPathFile) {
		$sep = "/";
		//var_dump(class_exists('\ZipArchive'));
		//$ziparc = '\ZipArchive'
		$zip = new \ZipArchive();
		$dateToday = new \DateTime();
		$pathTemp = $defaultPathFile."temp";

		if(!is_dir($pathTemp)){
			mkdir($pathTemp, 0777);
		} else {
			$files = array_slice(scandir($pathTemp), 2);
			if(count($files) > 100){
				foreach($files as $file){
					if(substr_compare($pathTemp.$sep.$file, ".zip", -4) == 0){
						$dateMod = new DateTime();
						$dateMod->setTimestamp(filemtime($pathTemp.$sep.$file));

						if($dateToday->diff($dateMod)->days > 0){
							unlink($pathTemp.$sep.$file);
						}
					}
				}
			}
		}

		$strName = session_id() . $dateToday->format("YmdHis") ;
		$nameZip = "arquivos_{$strName}.zip";
		$fullNameZip = $pathTemp.$sep.$nameZip;

		if ($zip->open($fullNameZip, \ZipArchive::CREATE)!==TRUE) {
			exit("Não foi possível construir o arquivo de download\n");
		}

		if (count($listFiles) == 0) {
			$file = $defaultPathFile;
			$options = array('add_path' => 'pedido/');
			var_dump($file.'*', glob($file.'*'));
			$zip->addGlob($file.'*', GLOB_BRACE, $options);
		}

		$notCompressed = array();
		foreach($listFiles as $file){
			$filePath = $defaultPathFile.$file;
			$return = false;
			if(is_file($filePath)){
				$return = $zip->addFile($filePath, $file);
				if(!$return){
					$notCompressed[] = $file;
				}
			}
			else{
				$notCompressed[] = $file;
			}
		}

		if (count($listFiles) == 0) {
			$rootPath = realpath($pathTemp);
			$files = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($defaultPathFile),
				\RecursiveIteratorIterator::LEAVES_ONLY
			);

			foreach ($files as $name => $file) {
				if (!$file->isDir() && str_replace('/', '\\', $file->getPath()) != str_replace('/', '\\', $pathTemp)) {
					var_dump($file->getPath());
					$filePath = $file->getRealPath();
					$relativePath = substr($filePath, strlen($rootPath) + 1);
					//$zip->addFile($filePath.'/'.$file->getFileName(), $relativePath.'/'.$file->getFileName());
				}
			}
		}
		$zip->close();

		if(count($notCompressed) == count($listFiles)){
			//exit("Não foi possível gerar o arquivo de download porque nenhum arquivo existe no servidor ou nenhum arquivo pôde ser compactado.\n");
			return -1;
		} else if(count($notCompressed) != 0){
			//echo "Os seguintes arquivos não existem no servidor ou não puderam ser compactados:<br>";
			return -2;
			foreach($notCompressed as $ncFile){
				//echo $ncFile . "<br>";
			}
		}
		//echo "O arquivo de download foi gerado com sucesso.";
		return "temp/".$nameZip;
	}

	private function printFile ($path, $name) {
		$ext = substr($name, strrpos($name, '.') + 1);
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".( ($ext == 'zip') ? substr($name, 5) : $name )."\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($path.$name));
		echo file_get_contents($path.$name);
	}
	function encode ($string, $num) {//Recebe uma string para ser criptografada
		$hash = decoct($num).";";//hash começa sendo o número aleatório gerado . ';'
		$primo = $this->nextPrime($num);//pegue o próximo número primo maior ou igual a $num
		foreach (str_split($string) as $key => $value) {//para cada caracter da string,
			$aux = base_convert((ord($value) * $primo + ($primo - $num)), 10, 36);//pegue o código do caracter, multiplique-o pelo por $primo e some a diferença de $primo e $num
			while (strlen($aux) < SPLIT_SPACES)//se o tamanho da string for menor que 5
				$aux = '0'.$aux;//coloque 0 até que ele seja maior
			$hash .= $aux;//concatena no $hash o código gerado
			$primo = $this->nextPrime($primo + 1);//calcula o próximo número primo
		}
		return $hash;
	}
	function decode ($hash) {
		preg_match_all('/(.*?);.*/', $hash, $primo, PREG_PATTERN_ORDER, 0);//Pega o primeiro número separado por ;
		$primo = octdec($primo[1][0]);
		$num = $primo;
		$hash = substr($hash, strlen($primo) + 1);//Remove essa parte do resto da string (123;987654321 vira 987654321)
		$hash = str_replace(';', '', $hash);//Remove qualquer ; extra que esteja na string
		$decoded = '';

		if ($this->nextPrime($primo) == $primo)//Se o número for primo, diminuir 1 para não bugar o incremento do número dentro do foreach
			$primo--;

		foreach (str_split($hash, SPLIT_SPACES) as $key => $value) {//para cada grupo de 5 caracteres,
			$primo = $this->nextPrime($primo + 1);//pegue o próximo número primo maior ou igual a $primo
			$decoded .= chr((base_convert($value, 36, 10) - ($primo - $num)) / $primo);//divida o valor dele pelo número, subtraia a diferença de $primo e $num e retorne o char do número
		}
		return $decoded;
	}
	function nextPrime ($num = 1) {//Pega o primeiro número primo maior ou igual ao $num
		$achou = false;
		while (!$achou) {
			for ($i = $num - 1; $i > 1; $i--) {
				$achou = true;
				if ($num % $i == 0) {
					$achou = false;
					break;
				}
			}
			$num++;
		}
		return $num - 1;
	}
}
