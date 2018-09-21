<?php namespace files\staGrid;

	use application\widgets\staGrid;
	use files\database\qDepartamento;

	class gDepartamento extends staGrid {
		public function __construct ($id = null) {
			parent::__construct(is_null($id)?"gDepartamento":$id);
			$this->setQueryDB(new qDepartamento());
			$this->style_default();
		}
		public function style_default () {
			$this->setDataQuery("departamento", NULL, NULL, "qDepartamento");

			$this->addCol("id", "id", "id", "<Value>", "NONE");
			$this->addCol("departamento", "departamento", "departamento", "<Value>", "NONE");

			$this->setResultPerPage(20);
			$this->setUrlQueryData("#");
		}
	}