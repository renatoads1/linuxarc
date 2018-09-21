<?php
	/**
	 * Class qTipo_pessoa | files\database\qTipo_pessoa.php
	 */
	namespace files\database;

	use application\libs\query;
	use application\libs\form;

	/**
	 *  Class para realizar controlar a tabela tipo_pessoa do banco
	 */
	class qTipo_pessoa extends query {

		/**
		 * Último erro que ocorreu durante algum processo
		 * 
		 * @var string Padrão: ""
		 */
		public $errorStr = "";
		/**
		 * Nome do arquivo de dataset relativo à tabela tipo_pessoa
		 * 
		 * @var string Padrão: "qTipo_pessoa"
		 */
		public $queryName = "qTipo_pessoa";

		/**
		 * Instancia um novo objeto da classe qTipo_pessoa
		 * 
		 * @param array $params Parâmetros que serão enviados para o query
		 */
		public function __construct ($params = []) {
			parent::__construct($params);
			$this->setNameTable("tipo_pessoa");
			$this->setFieldIndex("tppessoa");
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
			$campos["tppessoa"] = $form->createBasic_text("tppessoa", "", "tppessoa");
			$campos["descricao"] = $form->createBasic_text("descricao", "", "descricao");
			// FIM Listar Campos

			// Personalizar

			$this->processModel($campos, $dados);
			return $campos;
		}
	}