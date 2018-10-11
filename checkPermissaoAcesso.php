<?php

	use files\lib\auth;
//    arquivos acessiveis para usuarios deslogados
	$geralDeslogado = ["index/index/default","index/index/loginArquiv", "query", "error"];

	$geralLogado = array_merge($geralDeslogado, ["autocomplete", "index","varsession"]);

	if (!auth::isAuth()) {
		if (!$this->verificaArray($geralDeslogado, $module, $controller, $action, $opq)) {
			self::$errors[] = "Você precisa estar logado para acessar essa página";
					
		}
	}
	else {
		if (!$this->verificaArray($geralLogado, $module, $controller, $action, $opq)) {
			self::$errors[] = "Você não tem permissão para acessar esta página";
		}
	}