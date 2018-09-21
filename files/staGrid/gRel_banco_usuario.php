<?php namespace files\staGrid;

	use application\widgets\staGrid;
	use files\database\qRel_banco_usuario;

	class gRel_banco_usuario extends staGrid {
		public function __construct ($id = null) {
			parent::__construct(is_null($id)?"gRel_banco_usuario":$id);
			$this->setQueryDB(new qRel_banco_usuario());
			$this->style_default();
		}
		public function style_default () {
			$this->setDataQuery("rel_banco_usuario", NULL, NULL, "qRel_banco_usuario");

			$this->addCol("idbancos", "idbancos", "idbancos", "<Value>", "NONE");
			$this->addCol("idusuario_ub", "idusuario_ub", "idusuario_ub", "<Value>", "NONE");

			$this->setResultPerPage(20);
			$this->setUrlQueryData("#");
		}
	}