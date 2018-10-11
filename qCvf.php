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
			$campos["codigo"] = $form->createBasic_hidden("codigo", "", "codigo", true, "", "", "");
			$campos["entidade_tipo"] = $form->createBasic_select("entidade_tipo", "N", "Tipo de Entidade:&nbsp;");
			$campos["fisicojuridico"] = $form->createBasic_select("fisicojuridico", "", "Tipo Pessoa:&nbsp;");
			$campos["nome"] = $form->createBasic_text("nome", "", "Nome:&nbsp;");
			$campos["nomefantasia"] = $form->createBasic_text("nomefantasia", "", "Nome Fantasia:&nbsp;");
			$campos["endereco"] = $form->createBasic_text("endereco", "", "Endereço:&nbsp;");
			$campos["bairro"] = $form->createBasic_text("bairro", "", "Bairro:&nbsp;");
			$campos["cep"] = $form->createBasic_cep("cep","","CEP","endereco","bairro","cidade","codmunicipio","uf");
			$campos["cidade"] = $form->createBasic_text("cidade", "", "Cidade:&nbsp;");
			$campos["uf"] = $form->createBasic_text("uf", "", "Estado:&nbsp;");
			$campos["tel1"] = $form->createBasic_text("tel1", "", "Telefone:&nbsp;");
            //veridicar format
			$campos["dt_nasc"] = $form->createBasic_date("dt_nasc", "0001-01-01", "Data de Nasc / Cadastro:&nbsp;", "", "");
			$campos["cgc_cpf"] = $form->createBasic_number("cgc_cpf", "", "CPF/CNPJ:&nbsp;");
			$campos["insc_rg"] = $form->createBasic_text("insc_rg", "", "Nº RG:&nbsp;");
			$campos["codmunicipio"] = $form->createBasic_text("codmunicipio", "0", "Código Municipal:&nbsp;");
			$campos["nro_endereco"] = $form->createBasic_number("nro_endereco", "", "Nº Endereco:&nbsp;");
			// FIM Listar Campos

			// Personalizar
            $sql ="select *from tipo_entidade";
            $result = $this->runPrepared($sql)->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($result as $key=>$value){
                $this->addFieldOption($campos["entidade_tipo"],$value['descricao'],$value['tpentidade']);
            }
            
            $this->addFieldOption($campos["fisicojuridico"],'Físico','F');
            $this->addFieldOption($campos["fisicojuridico"],'Jurídico','J');
            $campos["tel1"]["options"]["pattern"] = "[0-9]+";
            $campos["dt_nasc"]["options"]["pattern"] = "[0-9]+";
            //fim person
			$this->processModel($campos, $dados);
			return $campos;
		}
	}