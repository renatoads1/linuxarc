<?php
	/**
	 * Class qEstado | files\database\qEstado.php
	 */
	namespace files\database;

	use application\libs\query;
	use application\libs\form;

	/**
	 *  Class para realizar controlar a tabela estado do banco
	 */
	class qEstado extends query {

		/**
		 * Último erro que ocorreu durante algum processo
		 * 
		 * @var string Padrão: ""
		 */
		public $errorStr = "";
		/**
		 * Nome do arquivo de dataset relativo à tabela estado
		 * 
		 * @var string Padrão: "qEstado"
		 */
		public $queryName = "qEstado";

		/**
		 * Instancia um novo objeto da classe qEstado
		 * 
		 * @param array $params Parâmetros que serão enviados para o query
		 */
		public function __construct ($params = []) {
			parent::__construct($params);
			$this->setNameTable("estado");
			$this->setFieldIndex("estcod");
			$this->setInstanceName("doc");
		}

		/**
		 * Busca no banco a linha cuja PK é igual a $valueIndex
		 * 
		 * Se não existir nenhuma linha com a PK informada ou ela não for informada,
		 * apenas o esqueleto do model será retornado
		 * 
		 * @param mixed $valueIndex Valor da PK
		 */
		public function model ($valueIndex = null) {
			// Pegar dados
			$dados = [];
			$form = new form();
			if (!is_null($valueIndex)) {
				$dados = $this->getDados($valueIndex);
				if (!$dados) {
					$dados = [];
				}
			}
			// Fim Pegar dados
			// Listar Campos
			$campos["estcod"] = $form->createBasic_number("estcod", "", "estcod", true, "", "", "");
			$campos["estado"] = $form->createBasic_text("estado", "", "estado");
			$campos["icms"] = $form->createBasic_number("icms", "", "icms", true, "", "", "");
			$campos["cod_ibge"] = $form->createBasic_number("cod_ibge", "0", "cod_ibge", true, "", "", "");
			// FIM Listar Campos

			// Personalizar

			$this->processModel($campos, $dados);
			return $campos;
		}
	}