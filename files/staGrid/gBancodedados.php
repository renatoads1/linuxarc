<?php namespace files\staGrid;

	use application\widgets\staGrid;
	use files\database\qBancodedados;

	class gBancodedados extends staGrid {
		public function __construct ($id = null) {
			parent::__construct(is_null($id)?"gBancodedados":$id);
			$this->setQueryDB(new qBancodedados());
			$this->style_default();
		}
		public function style_default () {
			$this->setDataQuery("bancodedados", NULL, NULL, "qBancodedados");

			$this->addCol("idbancos", "idbancos", "idbancos", "<Value>", "NONE");
			$this->addCol("razao_social", "razao_social", "razao_social", "<Value>", "NONE");
			$this->addCol("nomefantasia", "nomefantasia", "nomefantasia", "<Value>", "NONE");
			$this->addCol("banco", "banco", "banco", "<Value>", "NONE");
			$this->addCol("usuario_banco", "usuario_banco", "usuario_banco", "<Value>", "NONE");
			$this->addCol("senha_banco", "senha_banco", "senha_banco", "<Value>", "NONE");
			$this->addCol("porta", "porta", "porta", "<Value>", "NONE");
			$this->addCol("ipservidor", "ipservidor", "ipservidor", "<Value>", "NONE");
			$this->addCol("cnpj", "cnpj", "cnpj", "<Value>", "NONE");
			$this->addCol("inscricao", "inscricao", "inscricao", "<Value>", "NONE");

			$this->setResultPerPage(20);
			$this->setUrlQueryData("#");
		}
	}