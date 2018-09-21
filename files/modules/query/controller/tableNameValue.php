<?php namespace files\modules\query\controller;

use application\libs\controllerRest;
use application\libs\query;

class tableNameValue extends controllerRest {
	public function action_default () {
		$this->addInput('table', '/^[a-zA-Z\d_]+$/');
		$this->addInput('fieldIndex', '/^[a-zA-Z\d_]+$/');
		$this->addInput('fieldLabel', '/^[a-zA-Z\d_]+$/');
		$this->addInput('valueSaida', '/^[a-zA-Z\d_ -]*$/', false, '');
		$this->addInput('showIndex', '/^\d$/', false, 1);
		$this->addInput('instanceName', '/^[a-zA-Z\d]+$/', false, 'default');
		extract($this->getInputs());

		$query = new query();
		$value = $query->getVarSaida("tableNameValue({$table},{$fieldIndex},{$fieldLabel},<Value>,{$showIndex},{$instanceName})", $valueSaida);

		$this->addDataAtRoot('value', $value);
		$this->printReturn();
	}
}
