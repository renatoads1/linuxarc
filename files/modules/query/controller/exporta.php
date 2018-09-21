<?php namespace files\modules\query\controller;

use application\libs\controllerRest;
use application\libs\application;
use application\libs\query;
use application\libs\format;

class exporta extends controllerRest {
	public function action_excel() {
		$csv = "";
		$sep = "";
		$this->addInput('w');
		$this->addInput('n');
		$this->addInput('t');//nome do arquivo
		$inputs = $this->getInputs();
		$data = self::getDados($inputs);
		self::getTableFormat($data, true);
		foreach ($data['header'] as $value) {
			$csv .= $sep.str_replace('&nbsp;', ' ', $value['title']);
			$sep = ";";
		}
		foreach ($data['body'] as $value) {
			$csv .= "\r\n";
			$sep = "";
			foreach ($value as $k => $v) {
				if (gettype($k) == 'integer')
					continue;
				$v = '"'.str_replace('"', '""', $v).'"';// Força todo o conteúdo de uma célula a se manter dentro de uma célula
				$csv .= $sep.$v;
				$sep = ";";
			}
		}
		foreach($data["erros"] as $erro){
			$v = '"'.str_replace('"', '""', $erro).'"';// Força todo o conteúdo de uma célula a se manter dentro de uma célula
			$csv .= "\r\n" . $v;
		}
		header('Content-Description: File Transfer');
		header("Content-Type: text/csv; charset=UTF-16LE");
		header('Content-Disposition: attachment; filename="Exportação da tabela '.str_replace(" ", "_", $inputs['t']).'.csv"');
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		echo mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');
	}

	static function getDados ($inputs) {
		$classeQ = "files\\database\\".$inputs['n'];
		$classeG = "files\\staGrid\\g".substr($inputs['n'], 1);
		$query = new $classeQ();
		$staGrid = new $classeG();
		$inputs['w'] = str_replace('\'', '"', $inputs['w']);
		foreach (json_decode($inputs['w']) as $key => $value) {
			$exp = explode("::", $value);
			if (count($exp) == 3)
				$staGrid->addCondicao($exp[0], $exp[1], $exp[2]);
			else
				$staGrid->addCondicao($exp[0], $exp[1], $exp[2], $exp[3]);
		}
		$SQLs = $staGrid->getQuery();
		$tot = $query->runPrepared($SQLs['queryCountTotal'], $SQLs['param'])->fetch(\PDO::FETCH_NUM)[0];
		$errosGrid = [];
		if($tot > 2000){
			$tot = 2000;
			$errosGrid[] = "Mostrando somente as primeiras 2000 linhas.";
			$errosGrid[] = "Especifique melhor os filtros para diminuir a quantidade de registros buscados.";
		}
		$staGrid->setResultPerPage($tot);
		$staGrid->run();
		$grid = $staGrid->getData();
		
		foreach ($grid['header'] as $key => $value) {
			foreach ($grid['body'] as $k => $v)
				foreach ($v as $p => $c)
					if (gettype($p) == 'integer')
						unset($grid['body'][$k][$p]);
			if ($value['classCss'] == 'hidden') {
				foreach ($grid['body'] as $k => $v)
					unset($grid['body'][$k][ $grid['header'][$key]['id'] ]);
				unset($grid['header'][$key]);
			}
		}
		$grid['header'] = array_values(array_filter($grid['header']));
		unset($grid['footer']);
		unset($grid['params']);
		unset($grid['title']);
		unset($grid['query_sql']);
		$grid["erros"] = $errosGrid;
		return $grid;
	}

	static function getTableFormat (&$grid, $formatOnly = false) {
		foreach ($grid['body'] as $chave => $value) {
			$i = 0;
			foreach ($value as $k => $v) {
				if (isset($grid['header'][$i])) {
					$grid['body'][$chave][$k] = self::getValueFormat($grid['header'][$i]['formatSaida'], $grid['body'][$chave][$k], $formatOnly);
				}
				$i++;
			}
		}
	}

	/**
	 * Formata uma string $value com base em um formato $format
 	 *
 	 * Formatos suportados:<br>
 	 * <strong>Com suporte a recursividade</strong>
 	 * <ul>
 	 * 	<li>colorRed</li>
 	 * 	<li>colorBlue</li>
 	 * 	<li>colorGreen</li>
 	 * 	<li>colorOrange</li>
 	 * 	<li>colorHEX</li>
 	 * 	<li>textBold</li>
 	 * 	<li>iconOK</li>
 	 * 	<li>iconErro</li>
 	 * 	<li>classicon</li>
 	 * 	<li>flink</li>
 	 * 	<li>fnobr</li>
 	 * 	<li>fmascarar</li>
 	 * 	<li>fsubstr<i>* Não é recomendável usar o substring com recursividade pois o valor cortado pode não ser o esperado devido ao tamanho váviável que a string terá</i></li>
 	 * 	<li>fchoice</li>
 	 * </ul>
	 * <br>
 	 * <strong>Sem suporte a recursividade</strong>
 	 * <ul>
 	 * 	<li>fimage</li>
 	 * 	<li>fnohtml</li>
 	 * 	<li>fnumber</li>
 	 * 	<li>fmoney</li>
 	 * 	<li>fdate</li>
 	 * 	<li>fdatetime</li>
 	 * 	<li>ftel</li>
 	 * 	<li>fcpfcnpj</li>
 	 * 	<li>nil</li>
 	 * </ul>
	 *
 	 * Se $formatOnly for <i>true</i>, as seguintes funções não serão executadas, elas simplesmente serão puladas permitindo que uma função aninhada seja executada
 	 * <ul>
 	 * 	<li>colorRed</li>
 	 * 	<li>colorBlue</li>
 	 * 	<li>colorGreen</li>
 	 * 	<li>colorOrange</li>
 	 * 	<li>colorHEX</li>
 	 * 	<li>textBold</li>
 	 * 	<li>iconOK</li>
 	 * 	<li>iconErro</li>
 	 * 	<li>classicon</li>
 	 * 	<li>fimage</li>
 	 * 	<li>flink</li>
 	 * 	<li>fnobr</li>
 	 * </ul>
	 * Ex:
	 * <pre>
 	 * 	fchoice({A,Q,T},{colorGreen(Aberto),colorRed(Quitado),Todos},<Value>)
	 * 	/*Será executado como*\/
 	 * 	fchoice({A,Q,T},{Aberto,Quitado,Todos},<Value>)
	 *
 	 * 	textBold(ftel(<Value>))
	 * 	/*Será executado como*\/
 	 * 	ftel(<Value>)
 	 * </pre>
 	 *
 	 *
 	 * @param string $format : formato do texto
 	 * @param string $value : valor a ser fomatado
 	 * @param boolean $formatOnly : se verdadeiro, não adiciona tags html
 	 * @param int $level : nível de recursão inicial, coloque 5 para desabilitar a recursão
 	 * @return string texto formatado
	 */
	static function getValueFormat ($format, $value, $formatOnly = false, $level = 0) {

		$format = str_replace('<Value>', $value, $format);
		if ($level > 5) return $format;//NOTE isso está aqui só para testes
		$recusive = false;
		$regex = '/^([^\(]+)(?:\((.*)\)|)$/';
		preg_match($regex, $format, $cur);
		if (count($cur) < 3)//Não tem função, só <Value>
			return $format;
		$f = $cur[1];//Função
		$a = $cur[2];//Argumentos

		if ($formatOnly && !in_array($f, array('fnohtml', 'fnumber', 'fmoney', 'fdate', 'fdatetime', 'fsubstr', 'ftel', 'fcpfcnpj', 'fmascarar', 'fchoice', 'nil', 'tableNameValue', 'tableNameValueCache')))
			$f = '--REMOVE_FUNCTIONS--';
		switch ($f) {
			case 'colorRed':
				$valor = "<span class='text-danger' >".self::getValueFormat($a, '', $formatOnly, $level + 1)."</span>";
				break;
			case 'colorBlue':
				$valor = "<span class='text-primary' >".self::getValueFormat($a, '', $formatOnly, $level + 1)."</span>";
				break;
			case 'colorGreen':
				$valor = "<span class='text-success' >".self::getValueFormat($a, '', $formatOnly, $level + 1)."</span>";
				break;
			case 'colorOrange':
				$valor = "<span class='text-warning' >".self::getValueFormat($a, '', $formatOnly, $level + 1)."</span>";
				break;
			case 'colorHEX':
				$points = self::splitArgs($format);
				$valor = "<span style='color: #".$points[1]."' >".self::getValueFormat($points[0], '', $formatOnly, $level + 1)."</span>";
				break;
			case 'textBold':
				$valor = "<strong>".self::getValueFormat($a, '', $formatOnly, $level + 1)."</strong>";
				break;
			case 'iconOK':
				$valor = "<span class='text-success' ><i class='fa fa-check' ></i> ".self::getValueFormat($a, '', $formatOnly, $level + 1)."</span>";
				break;
			case 'iconErro':
				$valor = "<span class='text-danger' ><i class='fa fa-times' ></i> ".self::getValueFormat($a, '', $formatOnly, $level + 1)."</span>";
				break;
			case 'classicon':
				$arr = explode(':', $a);
				$valor = "<span class='text-danger' ><i class='{$arr[0]}' ></i> ".self::getValueFormat($arr[1], '', $formatOnly, $level + 1)."</span>";
				break;
			case 'fimage':
				$valor = "<img src=\"{$a}\" />";
				break;
			case 'fnohtml':
				$valor = preg_replace ('/<(?:[^>=]|=\'[^\']*\'|="[^"]*"|=[^\'"][^\s>]*)*>/', '', $a);
				break;
			case 'fnumber':
				$valor = ( round(100 * floatval($a)) ) / 100;
				break;
			case 'fmoney':
				$valor = format::MoneyBRL((( round(100 * floatval($a)) ) / 100), 2);
				break;
			case 'flink':
				$arr = explode(':', $a);
				if (count($arr) == 1 || $arr[1] == '')
					$arr[1] = $arr[0];
				$valor = "<a href='{$arr[0]}' >".self::getValueFormat($arr[1], '', $formatOnly, $level + 1)."</a>";
				break;
			case 'fnobr':
				$valor = "<nobr>".self::getValueFormat($a, '', $formatOnly, $level + 1)."</nobr>";
				break;
			case 'fdate':
				$valor = date('d/m/Y', strtotime($a));
				break;
			case 'fdatetime':
				$valor = date('d/m/Y H:i:s', strtotime($a));
				break;
			case 'fmascarar':
				$arr = self::splitArgs($format);
				$arr[1] = str_replace('9', '#', $arr[1]);
				$valor = self::mask(self::getValueFormat($arr[0], '', $formatOnly, $level + 1), $arr[1]);
				break;
			case 'fsubstr':
				$points = self::splitArgs($format);
				if (count($points) > 2)//<Value>, 2, 9
					$valor = substr(self::getValueFormat($points[0], '', $formatOnly, $level + 1), intval($points[1]), intval($points[2]));
				else//<Value>, 2
					$valor = substr(self::getValueFormat($points[0], '', $formatOnly, $level + 1), intval($points[1]));
				break;
			case 'ftel':
				$len = strlen($a);
				$mask = "";
				if (strpos($a, '0800') === 0 || strpos($a, '0300') === 0)
					$mask = '#### ### ####';
				elseif ($len == 8)
					$mask = '####-####';
				elseif ($len == 9)
					$mask = '#####-####';
				elseif (strpos($a, '00') === 0) {
					if ($len == 15)
						$mask = '+## (###) ####-####';
					elseif ($len == 16)
						$mask = '+## (###) #####-####';
					$a = substr($a, 2);
				} elseif (strpos($a, '+') === 0) {
					if ($len == 14)
						$mask = '+## (###) ####-####';
					elseif ($len == 15)
						$mask = '+## (###) #####-####';
				} elseif (strpos($a, '0') === 0) {
					if ($len == 11)
						$mask = '(###) ####-####';
					elseif ($len == 12)
						$mask = '(###) #####-####';
				} else {
					if ($len == 10)
						$mask = '(##) ####-####';
					elseif ($len == 11)
						$mask = '(##) #####-####';
					else $mask = '';
				}
				
				$valor = self::mask($a, $mask);
				break;
			case 'fcpfcnpj':
				if (strlen($a) == 11)
					$valor = self::mask($a, '###.###.###-##');
				else
					$valor = self::mask($a, '##.###.###/####-##');
				break;
			case 'fchoice':
				$valor = '';//FIX para o caso do campo permitir null e não haver algum valor
				preg_match('/^{(.*)},{(.*)},(.*)$/', $a, $brakets);
				foreach (explode(',', $brakets[1]) as $key => $val) {
					if ($brakets[3] == $val) {
						$valor = self::getValueFormat(explode(',', $brakets[2])[$key], '', $formatOnly, $level + 1);
						break;
					}
				}
				break;
			case 'nil':
				$valor = ' ';
				break;
			case 'tableNameValue':
			case 'tableNameValueCache':
				$args = self::splitArgs($format);
				$sql =  "SELECT {$args[2]} FROM {$args[0]} WHERE {$args[1]} = ?";
				$query = new query();
				$valor = $query->runPrepared($sql, array($args[3]))->fetch(\PDO::FETCH_NUM)[0];
				break;
			case '--REMOVE_FUNCTIONS--':
				$valor = self::getValueFormat(self::splitArgs($format)[0], '', $formatOnly, $level + 1);
				break;
			default://<Value>
				$valor = $a;
				break;
		}
		return $valor;
	}

	static function splitArgs ($string) {
		$ret = array();
		$index = 0;//índice do item atual
		$stateP = 0;//0 capture - maior que 0 skip "Nível de recursão"
		$stateS = false;//Está dentro de uma string?
		$t = strlen($string);
		for ($i = (strpos($string, '(') + 1); $i < $t; $i++) {
			if (!isset($ret[$index]))//Se não estiver setado, seta
				$ret[$index] = '';
			if ($string[$i] == '"' && $string[$i - 1] != '\\')//Achou aspas duplas e não tem uma \ antes, nega o bool que indica se está dentro de uma string ou não
				$stateS = !$stateS;
			if (!$stateS && $string[$i] == ',' && $stateP == 0) {
				$index++;
				continue;
			}
			if (!$stateS && $string[$i] == ')') $stateP--;
			$ret[$index] .= $string[$i];
			if (!$stateS && $string[$i] == '(') $stateP++;
			//echo "CHAR: ".$string[$i]."<br>stateP: ".$stateP."<br>";
		}
		$ret[$index] = substr($ret[$index], 0, strlen($ret[$index]) - 1);
		return $ret;
	}

	static function mask($val, $mask, $delimiter = '#') {
		$maskared = '';
		$k = 0;
		$tM = strlen($mask) - 1;
		$tV = strlen($val) - 1;
		for ($i = 0; ($i <= $tM && $k <= $tV); $i++) {
			if($mask[$i] == $delimiter) {
				if(isset($val[$k]))
					$maskared .= $val[$k++];
			} else {
				if(isset($mask[$i]))
					$maskared .= $mask[$i];
			}
		}
		return $maskared;
	}
}
