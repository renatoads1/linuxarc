<?php
	/**
	 * Class qBancodedados | files\database\qBancodedados.php
	 */
	namespace files\database;

	use application\libs\query;
	use application\libs\form;

	/**
	 *  Class para realizar controlar a tabela bancodedados do banco
	 */
	class qBancodedados extends query {

		/**
		 * Último erro que ocorreu durante algum processo
		 * 
		 * @var string Padrão: ""
		 */
		public $errorStr = "";
		/**
		 * Nome do arquivo de dataset relativo à tabela bancodedados
		 * 
		 * @var string Padrão: "qBancodedados"
		 */
		public $queryName = "qBancodedados";

		/**
		 * Instancia um novo objeto da classe qBancodedados
		 * 
		 * @param array $params Parâmetros que serão enviados para o query
		 */
		public function __construct ($params = []) {
			parent::__construct($params);
			$this->setNameTable("bancodedados");
			$this->setFieldIndex("idbancos");
			$this->setInstanceName("bancos");
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
			$campos["idbancos"] = $form->createBasic_number("idbancos", "", "idbancos", true, "", "", "");
			$campos["razao_social"] = $form->createBasic_text("razao_social", "", "razao_social");
			$campos["nomefantasia"] = $form->createBasic_text("nomefantasia", "", "nomefantasia");
			$campos["banco"] = $form->createBasic_text("banco", "", "banco");
			$campos["usuario_banco"] = $form->createBasic_text("usuario_banco", "", "usuario_banco");
			$campos["senha_banco"] = $form->createBasic_text("senha_banco", "", "senha_banco");
			$campos["porta"] = $form->createBasic_text("porta", "", "porta");
			$campos["ipservidor"] = $form->createBasic_text("ipservidor", "", "ipservidor");
			$campos["cnpj"] = $form->createBasic_text("cnpj", "", "cnpj");
			$campos["inscricao"] = $form->createBasic_text("inscricao", "", "inscricao");
			// FIM Listar Campos

			// Personalizar

			$this->processModel($campos, $dados);
			return $campos;
		}
	}