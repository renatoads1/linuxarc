<?php
	/**
	 * Class qArquivo | files\database\qArquivo.php
	 */
	namespace files\database;

	use application\libs\query;
	use application\libs\form;

	/**
	 *  Class para realizar controlar a tabela arquivo do banco
	 */
	class qArquivo extends query {

		/**
		 * Último erro que ocorreu durante algum processo
		 * 
		 * @var string Padrão: ""
		 */
		public $errorStr = "";
		/**
		 * Nome do arquivo de dataset relativo à tabela arquivo
		 * 
		 * @var string Padrão: "qArquivo"
		 */
		public $queryName = "qArquivo";

		/**
		 * Instancia um novo objeto da classe qArquivo
		 * 
		 * @param array $params Parâmetros que serão enviados para o query
		 */
		public function __construct ($params = []) {
			parent::__construct($params);
			$this->setNameTable("arquivo");
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
			$campos["dtarquivo"] = $form->createBasic_date("dtarquivo", "", "dtarquivo", "", "");
			$campos["cliente"] = $form->createBasic_number("cliente", "", "cliente", true, "", "", "");
			$campos["tpentidade"] = $form->createBasic_text("tpentidade", "", "tpentidade");
			$campos["documento_de_quem"] = $form->createBasic_text("documento_de_quem", "", "documento_de_quem");
			$campos["modelo"] = $form->createBasic_number("modelo", "", "modelo", true, "", "", "");
			$campos["numero_doc"] = $form->createBasic_text("numero_doc", "", "numero_doc");
			$campos["obs"] = $form->createBasic_text("obs", "", "obs");
			$campos["usuario_qarquivou"] = $form->createBasic_text("usuario_qarquivou", "", "usuario_qarquivou");
			$campos["nivelacesso"] = $form->createBasic_number("nivelacesso", "", "nivelacesso", true, "", "", "");
			$campos["caminho"] = $form->createBasic_text("caminho", "", "caminho");
			$campos["dtemissao"] = $form->createBasic_date("dtemissao", "", "dtemissao", "", "");
			$campos["dtvencimento"] = $form->createBasic_date("dtvencimento", "", "dtvencimento", "", "");
			$campos["dtpagamento"] = $form->createBasic_date("dtpagamento", "", "dtpagamento", "", "");
			$campos["valor"] = $form->createBasic_number("valor", "", "valor", true, "", "", "");
			$campos["desconto"] = $form->createBasic_number("desconto", "", "desconto", true, "", "", "");
			$campos["juros"] = $form->createBasic_number("juros", "", "juros", true, "", "", "");
			$campos["valorfinal"] = $form->createBasic_number("valorfinal", "", "valorfinal", true, "", "", "");
			$campos["localizacao"] = $form->createBasic_text("localizacao", "", "localizacao");
			$campos["iddepartamento"] = $form->createBasic_number("iddepartamento", "", "iddepartamento", true, "", "", "");
			$campos["contabilizado"] = $form->createBasic_text("contabilizado", "N", "contabilizado");
			// FIM Listar Campos

			// Personalizar

			$this->processModel($campos, $dados);
			return $campos;
		}
	}