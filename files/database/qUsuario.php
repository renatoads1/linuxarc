<?php
	/**
	 * Class qUsuario | files\database\qUsuario.php
	 */
	namespace files\database;

	use application\libs\query;
	use application\libs\form;

	/**
	 *  Class para realizar controlar a tabela usuario do banco
	 */
	class qUsuario extends query {

		/**
		 * Último erro que ocorreu durante algum processo
		 * 
		 * @var string Padrão: ""
		 */
		public $errorStr = "";
		/**
		 * Nome do arquivo de dataset relativo à tabela usuario
		 * 
		 * @var string Padrão: "qUsuario"
		 */
		public $queryName = "qUsuario";

		/**
		 * Instancia um novo objeto da classe qUsuario
		 * 
		 * @param array $params Parâmetros que serão enviados para o query
		 */
		public function __construct ($params = []) {
			parent::__construct($params);
			$this->setNameTable("usuario");
			$this->setFieldIndex("id");
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
			$campos["id"] = $form->createBasic_number("id", "", "id", true, "", "", "");
			$campos["nome"] = $form->createBasic_text("nome", "0", "nome");
			$campos["usuario"] = $form->createBasic_text("usuario", "0", "usuario");
			$campos["senha"] = $form->createBasic_password("senha", "0", "senha");
			$campos["documento_i"] = $form->createBasic_text("documento_i", "N", "documento_i");
			$campos["documento_a"] = $form->createBasic_text("documento_a", "N", "documento_a");
			$campos["documento_e"] = $form->createBasic_text("documento_e", "N", "documento_e");
			$campos["documento_c"] = $form->createBasic_text("documento_c", "N", "documento_c");
			$campos["cvf_i"] = $form->createBasic_text("cvf_i", "N", "cvf_i");
			$campos["cvf_a"] = $form->createBasic_text("cvf_a", "N", "cvf_a");
			$campos["cvf_e"] = $form->createBasic_text("cvf_e", "N", "cvf_e");
			$campos["cvf_c"] = $form->createBasic_text("cvf_c", "N", "cvf_c");
			$campos["usuario_i"] = $form->createBasic_text("usuario_i", "N", "usuario_i");
			$campos["usuario_a"] = $form->createBasic_text("usuario_a", "N", "usuario_a");
			$campos["usuario_c"] = $form->createBasic_text("usuario_c", "N", "usuario_c");
			$campos["usuario_e"] = $form->createBasic_text("usuario_e", "N", "usuario_e");
			$campos["modelo_i"] = $form->createBasic_text("modelo_i", "N", "modelo_i");
			$campos["modelo_a"] = $form->createBasic_text("modelo_a", "N", "modelo_a");
			$campos["modelo_c"] = $form->createBasic_text("modelo_c", "N", "modelo_c");
			$campos["modelo_e"] = $form->createBasic_text("modelo_e", "N", "modelo_e");
			$campos["contador"] = $form->createBasic_text("contador", "N", "contador");
			// FIM Listar Campos
            $campos["usuario"]["options"]=["pattern"=>"[a-zA-Z\d]+"];
			// Personalizar
			$this->processModel($campos, $dados);
			return $campos;
		}
	}