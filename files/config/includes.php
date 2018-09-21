<?php

	use files\lib\auth;

	$cod = -1;
	$nome = '';
	if (auth::isAuth()) {
		$cod = auth::getInfoUser()['id'];
		$nome = auth::getInfoUser()['nome'];
	}
	$includes = ["BS3", "font-awesome", "pacoteJS", "default"];
	$HTMLIncludes = "
				<script>
					const url_raiz = '".URL_RAIZ."',
						  id_empresa = '".ID_EMPRESA."',
						  url_raiz_empresa = url_raiz + id_empresa + '/',
						  id_user = {$cod},
						  nome_user  = '{$nome}';
				</script>
				<link href='".URL_RAIZ."files/public/css/arquivamento.css' rel='stylesheet' />
				<script src=\"".URL_RAIZ."files/public/js/arquivamento.js?v=".REVISION_SISTEMA."\" ></script>
				<meta name='theme-color' content='#e2e2e2'>";