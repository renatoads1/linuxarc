<?php
	/**
	 * Class qCidade | files\database\qCidade.php
	 */
	namespace files\database;

	use application\libs\query;
	use application\libs\form;

	/**
	 *  Class para realizar controlar a tabela cidade do banco
	 */
	class qCidade extends query {

		/**
		 * Último erro que ocorreu durante algum processo
		 * 
		 * @var string Padrão: ""
		 */
		public $errorStr = "";
		/**
		 * Nome do arquivo de dataset relativo à tabela cidade
		 * 
		 * @var string Padrão: "qCidade"
		 */
		public $queryName = "qCidade";

		/**
		 * Instancia um novo objeto da classe qCidade
		 * 
		 * @param array $params Parâmetros que serão enviados para o query
		 */
		public function __construct ($params = []) {
			parent::__construct($params);
			$this->setNameTable("cidade");
			$this->setFieldIndex("id");
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
			$campos["uf"] = $form->createBasic_text("uf", "", "uf");
			$campos["nome"] = $form->createBasic_text("nome", "", "nome");
			$campos["codmunicipio"] = $form->createBasic_number("codmunicipio", "0", "codmunicipio", true, "", "", "");
			// FIM Listar Campos

			// Personalizar

			$this->processModel($campos, $dados);
			return $campos;
		}
	}