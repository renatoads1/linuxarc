<?php
	/**
	 * Class qTipo_entidade | files\database\qTipo_entidade.php
	 */
	namespace files\database;

	use application\libs\query;
	use application\libs\form;

	/**
	 *  Class para realizar controlar a tabela tipo_entidade do banco
	 */
	class qTipo_entidade extends query {

		/**
		 * Último erro que ocorreu durante algum processo
		 * 
		 * @var string Padrão: ""
		 */
		public $errorStr = "";
		/**
		 * Nome do arquivo de dataset relativo à tabela tipo_entidade
		 * 
		 * @var string Padrão: "qTipo_entidade"
		 */
		public $queryName = "qTipo_entidade";

		/**
		 * Instancia um novo objeto da classe qTipo_entidade
		 * 
		 * @param array $params Parâmetros que serão enviados para o query
		 */
		public function __construct ($params = []) {
			parent::__construct($params);
			$this->setNameTable("tipo_entidade");
			$this->setFieldIndex("tpentidade");
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
			$campos["tpentidade"] = $form->createBasic_text("tpentidade", "", "tpentidade");
			$campos["descricao"] = $form->createBasic_text("descricao", "", "descricao");
			// FIM Listar Campos
			// Personalizar
            $campos["tpentidade"]["options"]["maxlength"] = 3;//até 3 numeros
            $campos["tpentidade"]["options"]["pattern"] = "[a-zA-Z]+";
            $campos["descricao"]["options"]["maxlength"] = 50;
            $campos["descricao"]["options"]["pattern"] = "[a-zA-Z]+";//apenas letras
			
            //fim
            $this->processModel($campos, $dados);
			return $campos;
		}
	}