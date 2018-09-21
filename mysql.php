<?php
	use application\libs\controllerRest;
	use application\libs\database;
	use application\libs\query;

	if (AMBIENTE == LOCAL) {
		database::addInstance(ID_EMPRESA."_bancos", "bancos", "mysql", "192.168.15.50", "3306", "nave", "nave");
	}
	else if (AMBIENTE == TESTE) {// TODO
		// database::addInstance(ID_EMPRESA."_bancos", "bancos", "mysql", "192.168.15.50", "3306", "nave", "nave");
	} else if (AMBIENTE == OFICIAL) {
		database::addInstance(ID_EMPRESA."_bancos", "bancos", "mysql", "sics1.cffh0q8tsxc9.sa-east-1.rds.amazonaws.com", "3306", "sics1", "Z4(3dE#0w");
	}
	
	$query = new query();
	$session = controllerRest::getSession();
	if (isset($session["nome_cliente"])) {
		$sqlbd = "SELECT banco, usuario_banco, senha_banco, porta, ipservidor FROM bancodedados WHERE idbancos = ?";
		$con = $query->runPrepared($sqlbd, [$session["nome_cliente"]],"bancos")->fetch(PDO::FETCH_ASSOC);
        database::addInstance($con["banco"], "doc", "mysql", $con["ipservidor"], $con["porta"], $con["usuario_banco"], $con["senha_banco"]);
      
    }
