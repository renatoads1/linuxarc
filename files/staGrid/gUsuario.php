<?php namespace files\staGrid;

	use application\widgets\staGrid;
	use files\database\qUsuario;

	class gUsuario extends staGrid {
		public function __construct ($id = null) {
			parent::__construct(is_null($id)?"gUsuario":$id);
			$this->setQueryDB(new qUsuario());
			$this->style_default();
		}
		public function style_default () {
			$this->setDataQuery("usuario", NULL, NULL, "qUsuario");

			$this->addCol("id", "id", "id", "<Value>", "NONE");
			$this->addCol("nome", "nome", "nome", "<Value>", "NONE");
			$this->addCol("usuario", "usuario", "usuario", "<Value>", "NONE");
			$this->addCol("senha", "senha", "senha", "<Value>", "NONE");
			$this->addCol("documento_i", "documento_i", "documento_i", "<Value>", "NONE");
			$this->addCol("documento_a", "documento_a", "documento_a", "<Value>", "NONE");
			$this->addCol("documento_e", "documento_e", "documento_e", "<Value>", "NONE");
			$this->addCol("documento_c", "documento_c", "documento_c", "<Value>", "NONE");
			$this->addCol("cvf_i", "cvf_i", "cvf_i", "<Value>", "NONE");
			$this->addCol("cvf_a", "cvf_a", "cvf_a", "<Value>", "NONE");
			$this->addCol("cvf_e", "cvf_e", "cvf_e", "<Value>", "NONE");
			$this->addCol("cvf_c", "cvf_c", "cvf_c", "<Value>", "NONE");
			$this->addCol("usuario_i", "usuario_i", "usuario_i", "<Value>", "NONE");
			$this->addCol("usuario_a", "usuario_a", "usuario_a", "<Value>", "NONE");
			$this->addCol("usuario_c", "usuario_c", "usuario_c", "<Value>", "NONE");
			$this->addCol("usuario_e", "usuario_e", "usuario_e", "<Value>", "NONE");
			$this->addCol("modelo_i", "modelo_i", "modelo_i", "<Value>", "NONE");
			$this->addCol("modelo_a", "modelo_a", "modelo_a", "<Value>", "NONE");
			$this->addCol("modelo_c", "modelo_c", "modelo_c", "<Value>", "NONE");
			$this->addCol("modelo_e", "modelo_e", "modelo_e", "<Value>", "NONE");
			$this->addCol("contador", "contador", "contador", "<Value>", "NONE");

			$this->setResultPerPage(20);
			$this->setUrlQueryData("#");
		}
	}