<?php namespace files\modules\query\controller;


use application\libs\controllerRest;
use application\libs\query;

class listTableValueLabel extends controllerRest{
	public function action_default(){
		$table = $this->addInput('table');
		$fieldValue = $this->addInput('fieldValue');
		$fieldLabel = $this->addInput('fieldLabel');
		$order = $this->addInput('order');
		$where = $this->addInput('where');
		$instanceName = $this->addInput('instanceName');
		
		$table = str_replace("'", "\\'", $table);
		$fieldValue = str_replace("'", "\\'", $fieldValue);
		$fieldLabel = str_replace("'", "\\'", $fieldLabel);
		$where = str_replace("'", "\\'", $where);
		
		if($order==''){
			$order = "{$fieldLabel} ASC";
		}
		
		$sql = "SELECT {$fieldValue},{$fieldLabel} FROM {$table}";				
		if($where!=''){
			$sql .= " WHERE {$where}";
		}
		$sql .= " ORDER BY {$order}";
		
		try {
			$query = new query();
			$result = $query->run($sql,$instanceName);
		} catch (\Exception $e) {
			$this->addError("Erro ao Listar".(MODE_DEBUG?"\r\n{$sql};\r\n".$e->getMessage():""),0,controllerRest::TYPE_FATALERROR);
		}
		
		$list = array();
		while(list($value,$label) = $result->fetch()){
			$list[$value] = $label;
		}
		
		$this->addData('list', $list);
		$this->printReturn();
	}
}