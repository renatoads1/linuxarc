<?php
	/**
	 * Class qCvf | files\database\qCvf.php
	 */
	namespace files\database;

	use application\libs\query;
	use application\libs\form;

	/**
	 *  Class para realizar controlar a tabela cvf do banco
	 */
	class qCvf extends query {

		/**
		 * Último erro que ocorreu durante algum processo
		 * 
		 * @var string Padrão: ""
		 */
		public $errorStr = "";
		/**
		 * Nome do arquivo de dataset relativo à tabela cvf
		 * 
		 * @var string Padrão: "qCvf"
		 */
		public $queryName = "qCvf";

		/**
		 * Instancia um novo objeto da classe qCvf
		 * 
		 * @param array $params Parâmetros que serão enviados para o query
		 */
		public function __construct ($params = []) {
			parent::__construct($params);
			$this->setNameTable("cvf");
			$this->setFieldIndex("codigo");
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
			$campos["codigo"] = $form->createBasic_number("codigo", "", "codigo", true, "", "", "");
			$campos["entidade_tipo"] = $form->createBasic_text("entidade_tipo", "N", "entidade_tipo");
			$campos["fisicojuridico"] = $form->createBasic_text("fisicojuridico", "", "fisicojuridico");
			$campos["nome"] = $form->createBasic_text("nome", "", "nome");
			$campos["nomefantasia"] = $form->createBasic_text("nomefantasia", "", "nomefantasia");
			$campos["endereco"] = $form->createBasic_text("endereco", "", "endereco");
			$campos["bairro"] = $form->createBasic_text("bairro", "", "bairro");
			$campos["cep"] = $form->createBasic_text("cep", "", "cep");
			$campos["cidade"] = $form->createBasic_text("cidade", "", "cidade");
			$campos["uf"] = $form->createBasic_text("uf", "", "uf");
			$campos["tel1"] = $form->createBasic_text("tel1", "", "tel1");
			$campos["dt_nasc"] = $form->createBasic_date("dt_nasc", "0001-01-01", "dt_nasc", "", "");
			$campos["cgc_cpf"] = $form->createBasic_text("cgc_cpf", "", "cgc_cpf");
			$campos["insc_rg"] = $form->createBasic_text("insc_rg", "", "insc_rg");
			$campos["codmunicipio"] = $form->createBasic_text("codmunicipio", "0", "codmunicipio");
			$campos["nro_endereco"] = $form->createBasic_text("nro_endereco", "", "nro_endereco");
			// FIM Listar Campos

			// Personalizar

			$this->processModel($campos, $dados);
			return $campos;
		}
	}