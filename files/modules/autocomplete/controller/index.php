<?php
	namespace files\modules\autocomplete\controller;

	use application\libs\controllerRest;
	use application\libs\query;
	use application\widgets\staGrid;

	class index extends controllerRest {

		const SEARCH_ID = 0;
		const SEARCH_DESC = 1;
		const SEARCH_BOTH = 2;

		public function action_default() {
			if (!isset($_GET['campo'])) {
				$this->returnError();
			}

			$this->addInput("numResults");
			$this->addInput("value");
			$this->addInput("label");

			$searchMode = $this->addInput("searchMode", '/^[012]$/', false, self::SEARCH_BOTH);
			$concat = $this->addInput("concat", '/^(true|false)$/', false, 'true') == 'true';
			$where = json_decode($this->addInput("where", null, false, '[]'), true);
			$distinct = $this->addInput("distinct", '/^(true|false)$/', false, 'false') == 'true';

			if (in_array($_GET['campo'], ['tipo', 'entidade', 'user', 'username', 'funcionario', 'supervisor', 'departamento', 'statusAlter', 'rota', 'perfil'])) {
				try {
					$opt = $this->getAutoComboOptions($_GET['campo']);
					if (isset($opt['labelReturn'])) {
						$this->pushInput("labelReturn", $opt["labelReturn"]);
					}
					if (isset($opt['concat'])) {
						$concat = $opt['concat'];
					}
					if ($distinct) {
						$this->returnAutoCompleteGenericoDistinct($this->getInputs(), $opt['table'], $opt['name'], $opt['cod'], $searchMode, $concat);
					}
					else {
						if (isset($opt['where'])) {
							$where = array_merge($where, $opt['where']);
						}
						$this->returnAutoCompleteGenerico($this->getInputs(), $opt['table'], $opt['name'], $opt['cod'], $searchMode, $concat, $where);
					}
				}
				catch (\Exception $e) {
					$this->returnError();
				}
			}
			// Adicionar os else if para campos específicos aqui
			else {
				$this->returnError();
			}
			$this->printReturn();
		}

		/**
		 * Cria a estrutura para ser usada no options de um autoCombo
		 *
		 * @param string $campo nome do campo que será enviado no GET pelo autoCombo
		 * @param array $js Informações extra para serem adicionadas no autoCombo
		 * @return array Array de configuração
		 * @throws \Exception Se o campo informado não existir no switch dispara uma exceção
		 */
		static function getAutoComboOptions($campo = '', $js = null) {
			switch ($campo) {
				case 'tipo':
					$return = ['table' => 'tipo', 'name' => 'descricao', 'cod' => 'id'];
					break;
				case 'entidade':
					$return = ['table' => 'entidade', 'name' => 'nome', 'cod' => 'id'];
					break;
				case 'user':
					$return = ['table' => 'usuario', 'name' => 'nome', 'cod' => 'id'];
				case 'username':
					$return = ['table' => 'usuario', 'name' => 'usuario', 'cod' => 'usuario', 'concat' => false];
					break;
				case 'funcionario':
					$return = ['table' => 'entidade', 'name' => 'nome', 'cod' => 'codFuncionario', 'where' => ['codSupervisor::::'.staGrid::COND_WHERE_X_IS_NOT_NULL]];
					break;
				case 'supervisor':
					$return = ['table' => 'entidade', 'name' => 'nome', 'cod' => 'codFuncionario', 'where' => ['codSupervisor::::'.staGrid::COND_WHERE_X_IS_NULL]];
					break;
				case 'departamento':
					$return = ['table' => 'departamento', 'name' => 'nome', 'cod' => 'id'];
					break;
				case 'status':
					$return = ['table' => 'status', 'name' => 'descricao', 'cod' => 'id'];
					break;
				case 'rota':
					$return = ['table' => 'rota', 'name' => 'nome', 'cod' => 'id'];
					break;
				case 'perfil':
					$return = ['table' => 'perfil_usuarios', 'name' => 'nome', 'cod' => 'id'];
					break;
				case 'statusAlter':
					$return = [
						'table' => 'status',
						'name' => 'descricao',
						'cod' => 'id',
						'labelReturn' => "concat(descricao, \"<br><img style='font-weight: bold;' alt='\", (case when tipo = \"N\" then \"Novo\" when tipo = \"F\" then \"Final\" when tipo = \"O\" then \"Outro\" when tipo = \"P\" then \"Problematico\" end), \"' />\")",
						'where' => [
							'tipo::N::'.staGrid::COND_WHERE_DIFFERENT
						]
					];
					break;
				default:
					throw new \Exception("O campo informado não possui um modelo.");
			}
			$return['url'] = URL_RAIZ_EMPRESA."autocomplete/?campo=".$campo;
			if (!is_null($js)) {
				$return['js'] = $js;
			}
			return $return;
		}

		/**
		 * Retorna os resultados para o autoCombo de forma genérica
		 *
		 * @param $inputs array : inputs do POST e GET
		 * @param $table string : nome da tabela
		 * @param $label string : nome do campo que será exibido
		 * @param $value string : nome do campo PRIMARY da tabela
		 * @param $searchMode int : modo de pesquisa:<br><pre>	SEARCH_ID: pesquisa apenas pelo ID<br>	SEARCH_DESC: pesquisa apenas pelo nome<br>	SEARCH_BOTH: pesquisa pelo id e pelo nome</pre>
		 * @param $concat bool : true se o campo deve ser retornado do formato "concat({$value}, ' - ', {$label})"
		 * @param $where array : Adiciona parâmetros na cláusula WHERE da busca, na forma : array($nomeCampo => $valorCampo)
		 * @param $aux array : Define campos do banco que serão retornados junto com label e value para serem usados no frontend. Forma do array: array($nomeFrontend => $nomeCampoBD)
		 */
		private function returnAutoCompleteGenerico($inputs, $table, $label, $value, $searchMode = self::SEARCH_DESC, $concat = false, $where = array(), $aux = array()) {
			$query = new query();
			$labelReturn = $label;
			if (isset($inputs['labelReturn'])) {
				$labelReturn = $inputs['labelReturn'];
			}
			if ($concat) {
				$fields = preg_split('/ - /', $inputs['value']);
				$inputs['value'] = $fields[0];
				$inputs['label'] = isset($fields[1]) ? $fields[1] : $fields[0];
				$labelReturn = "concat({$value}, ' - ', {$label})";
			}
			switch ($searchMode) {
				case self::SEARCH_ID:
					$search = $value.' LIKE ?';
					$param = array("%".$inputs['value']."%");
					break;
				case self::SEARCH_DESC:
					$search = $label.' LIKE ?';
					$param = array("%".$inputs['label']."%");
					break;
				case self::SEARCH_BOTH:
					$search = "( {$value} LIKE ? OR {$label} LIKE ?)";
					$param = array("%".$inputs['value']."%", "%".$inputs['label']."%");
					break;
				default: return;
			}

			//Adiciona parâmetros na cláusula WHERE
			if (isset($where) && count($where) > 0) {
				$stagrid = new staGrid();
				$stagrid->setParam('w', $where);

				foreach ($where as $key => $v) {
					$a = explode("::", $v);
					$stagrid->addCol($a[0], $a[0], $a[0]);
				}
				$w = $stagrid->condicaoParaWhere(true);
				$search .= " AND ".$w["sql"];
				$param = array_merge($param, $w["param"]);
			}
			//Adiciona dados auxiliares a serem buscados junto com as informações
			$fieldsAux = "";
			if (isset($aux) && count($aux) > 0) {
				$sep = ", ";
				foreach ($aux as $key => $v) {
					$fieldsAux .= $sep.$v." as ".$key;
				}
			}

			$sql = "SELECT {$value} AS 'value', {$labelReturn} AS 'label' {$fieldsAux} FROM {$table} WHERE {$search} LIMIT ?";
			try {
				$param[] = intval($inputs['numResults']);
				$dadosCat = $query->runPrepared($sql, $param);
				$this->addDataAtRoot("itens", $dadosCat->fetchAll(\PDO::FETCH_ASSOC));
			}
			catch (\Exception $e) {
				$this->addError("Erro ".__LINE__, controllerRest::TYPE_ERROR);
			}
		}

		/**
		 * Retorna os resultados para o autoCombo de forma genérica usado distinct
		 *
		 * @param $inputs array : inputs do POST e GET
		 * @param $table string : nome da tabela
		 * @param $label string : nome do campo que será exibido
		 * @param $value string : nome do campo PRIMARY da tabela
		 * @param $searchMode int : modo de pesquisa:<br><pre>	SEARCH_ID: pesquisa apenas pelo ID<br>	SEARCH_DESC: pesquisa apenas pelo nome<br>	SEARCH_BOTH: pesquisa pelo id e pelo nome</pre>
		 * @param $concat bool : true se o campo deve ser retornado do formato "concat({$value}, ' - ', {$label})"
		 * @param $aux array : Define campos do banco que serão retornados junto com label e value para serem usados no frontend. Forma do array: array($nomeFrontend => $nomeCampoBD)
		 */
		private function returnAutoCompleteGenericoDistinct($inputs, $table, $label, $value, $searchMode = self::SEARCH_DESC, $concat = false, $aux = array()) {
			$query = new query();
			$labelReturn = $label;
			if (isset($inputs['labelReturn'])) {
				$labelReturn = $inputs['labelReturn'];
			}
			if ($concat) {
				$fields = preg_split('/ - /', $inputs['value']);
				$inputs['value'] = $fields[0];
				$inputs['label'] = isset($fields[1]) ? $fields[1] : $fields[0];
				$labelReturn = "concat({$value}, ' - ', {$label})";
			}
			switch ($searchMode) {
				case self::SEARCH_ID:
					$search = $value.' LIKE ?';
					$param = array("%".$inputs['value']."%");
					break;
				case self::SEARCH_DESC:
					$search = $label.' LIKE ?';
					$param = array("%".$inputs['label']."%");
					break;
				case self::SEARCH_BOTH:
					$search = "({$value} LIKE ? OR {$label} LIKE ?)";
					$param = array("%".$inputs['value']."%", "%".$inputs['label']."%");
					break;
				default: return;
			}

			//Adiciona dados auxiliares a serem buscados junto com as informações
			$fieldsAux = "";
			if (isset($aux) && count($aux) > 0) {
				$sep = ", ";
				foreach ($aux as $key => $v) {
					$fieldsAux .= $sep.$v." as ".$key;
				}
			}

			$sql = "SELECT DISTINCT {$value} AS 'value', {$labelReturn} AS 'label' {$fieldsAux} FROM {$table} WHERE {$search} LIMIT ?";
			try {
				$param[] = intval($inputs['numResults']);
				$dadosCat = $query->runPrepared($sql, $param);
				$this->addDataAtRoot("itens", $dadosCat->fetchAll(\PDO::FETCH_ASSOC));
			}
			catch (\Exception $e) {
				$this->addError("Erro ".__LINE__, controllerRest::TYPE_ERROR);
			}
		}

		/**
		 * Retorna uma mensagem de erro padrão caso esja faltando algum input ou a categoria do autocombo selecionada não exista
		 */
		private function returnError() {
			$this->addError("<a class='list-group-item autoCombo' style='border: none;' >Campo não informado ou inválido!</a>", controllerRest::TYPE_ERROR);
			$this->printReturn();
		}
	}
