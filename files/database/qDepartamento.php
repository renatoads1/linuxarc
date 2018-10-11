<?php
	/**
	 * Class qDepartamento | files\database\qDepartamento.php
	 */
	namespace files\database;

	use application\libs\query;
	use application\libs\form;

	/**
	 *  Class para realizar controlar a tabela departamento do banco
	 */
	class qDepartamento extends query {

		/**
		 * Último erro que ocorreu durante algum processo
		 * 
		 * @var string Padrão: ""
		 */
		public $errorStr = "";
		/**
		 * Nome do arquivo de dataset relativo à tabela departamento
		 * 
		 * @var string Padrão: "qDepartamento"
		 */
		public $queryName = "qDepartamento";

		/**
		 * Instancia um novo objeto da classe qDepartamento
		 * 
		 * @param array $params Parâmetros que serão enviados para o query
		 */
		public function __construct ($params = []) {
			parent::__construct($params);
			$this->setNameTable("departamento");
			$this->setFieldIndex("id");
//			$this->setInstanceName("bancos");
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
			$campos["id"] = $form->createBasic_number("id", "", "id", true, "", "", "");
			$campos["departamento"] = $form->createBasic_text("departamento", "", "departamento");
			// FIM Listar Campos

			// Personalizar

			$this->processModel($campos, $dados);
			return $campos;
		}
	}