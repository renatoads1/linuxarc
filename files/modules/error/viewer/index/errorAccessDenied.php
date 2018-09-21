<?php
	use application\libs\application;
	use files\viewerModel\padrao;

	$viewer = new padrao();
	$viewer->addLogoDefault();
	$menu = new \files\menu\padrao();
	$viewer->menu = $menu->menu;

	$viewer->document->title = "ERRO 403 (ACESSO NEGADO)";
	if (isset($this->opq)) {
		$arr = json_decode($this->opq);
		if (gettype($arr) == 'array') {
			foreach ($arr as $key => $value) {
				application::$errors[] = $value;
			}
		}
	}

	$out = "
		<div class=\"col-md-8 col-md-offset-2 well\">
			<div class=\"alert alert-warning\" >
				<p class=\"text-warning\" >
					<i class=\"fa fa-exclamation-triangle\"></i>
					Acesso negado!<br><br>";
	foreach (application::$errors as $erro) {
		$out .= "<strong class=\"text-danger\">{$erro}</strong><br>";
	}
	$out .= "
				</p>
			</div>
			<br>
			<button onclick=\"window.location='".URL_RAIZ_EMPRESA."';\">Voltar para o inicio.</button>
			<button onclick=\"history.back();\">Voltar para a página anterior.</button>
		</div>";

	$viewer->content = "<div class=\"container\">{$out}</div>";
	$viewer->addClasses("scrollable");
	$viewer->run();
?>
