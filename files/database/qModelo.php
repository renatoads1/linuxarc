<?php
	/**
	 * Class qModelo | files\database\qModelo.php
	 */
	namespace files\database;

	use application\libs\query;
	use application\libs\form;

	/**
	 *  Class para realizar controlar a tabela modelo do banco
	 */
	class qModelo extends query {

		/**
		 * Último erro que ocorreu durante algum processo
		 * 
		 * @var string Padrão: ""
		 */
		public $errorStr = "";
		/**
		 * Nome do arquivo de dataset relativo à tabela modelo
		 * 
		 * @var string Padrão: "qModelo"
		 */
		public $queryName = "qModelo";

		/**
		 * Instancia um novo objeto da classe qModelo
		 * 
		 * @param array $params Parâmetros que serão enviados para o query
		 */
		public function __construct ($params = []) {
			parent::__construct($params);
			$this->setNameTable("modelo");
			$this->setFieldIndex("modcod");
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
			$campos["modcod"] = $form->createBasic_text("modcod", "", "modcod");
			$campos["moddesc"] = $form->createBasic_text("moddesc", "", "moddesc");
			$campos["garantia"] = $form->createBasic_text("garantia", "", "garantia");
			$campos["vlr_mao_obra"] = $form->createBasic_number("vlr_mao_obra", "0.00", "vlr_mao_obra", true, "", "", "");
			$campos["vlr_visita"] = $form->createBasic_number("vlr_visita", "0.00", "vlr_visita", true, "", "", "");
			$campos["hierarquia"] = $form->createBasic_text("hierarquia", "", "hierarquia");
			$campos["origem"] = $form->createBasic_text("origem", "", "origem");
			// FIM Listar Campos
			
            // Personalizar

            $campos["moddesc"]["options"]["maxlength"] = 50;
            $campos["moddesc"]["options"]["pattern"] = "^[a-zA-Z]+";
            $campos["garantia"]["options"]["maxlength"] = 3;
            $campos["garantia"]["options"]["pattern"] = "^[0-9]+";
            $campos["vlr_mao_obra"]["options"]["pattern"] = "[^0-9]";
            $campos["vlr_visita"]["options"]["pattern"] = "[^0-9]";
            $campos["hierarquia"]["options"]["maxlength"] = 10;
            $campos["origem"]["options"]["maxlength"] = 1;

			$this->processModel($campos, $dados);
			return $campos;
		}
	}