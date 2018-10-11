<?php
	/**
	 * Class qRel_banco_usuario | files\database\qRel_banco_usuario.php
	 */
	namespace files\database;

	use application\libs\query;
	use application\libs\form;

	/**
	 *  Class para realizar controlar a tabela rel_banco_usuario do banco
	 */
	class qRel_banco_usuario extends query {

		/**
		 * Último erro que ocorreu durante algum processo
		 * 
		 * @var string Padrão: ""
		 */
		public $errorStr = "";
		/**
		 * Nome do arquivo de dataset relativo à tabela rel_banco_usuario
		 * 
		 * @var string Padrão: "qRel_banco_usuario"
		 */
		public $queryName = "qRel_banco_usuario";

		/**
		 * Instancia um novo objeto da classe qRel_banco_usuario
		 * 
		 * @param array $params Parâmetros que serão enviados para o query
		 */
		public function __construct ($params = []) {
			parent::__construct($params);
			$this->setNameTable("rel_banco_usuario");
			$this->setFieldIndex("idusuario_ub");
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
			$campos["idusuario_ub"] = $form->createBasic_number("idusuario_ub", "", "idusuario_ub", true, "", "", "");
			// FIM Listar Campos

			// Personalizar

			$this->processModel($campos, $dados);
			return $campos;
		}
	}