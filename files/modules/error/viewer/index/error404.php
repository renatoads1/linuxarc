<?php

use files\viewerModel\padrao;
use files\modules\query\controller\ajax;
use application\widgets\bootstrap;

$viewer = new padrao();
$viewer->addLogoDefault();
$menu = new \files\menu\padrao();
$viewer->menu = $menu->menu;

$viewer->document->title = "ERROR 404(PAGE NOT FOUND)";

$out = "";
$out .= "
		<div class=\"col-md-8 col-md-offset-2 well\">
			<div class=\"alert alert-warning\" >
				<p class=\"text-warning\" >
					<i class=\"fa fa-exclamation-triangle\"></i>
					Erro 404!<br><br>
					Página não encontrada: {$_SERVER['REQUEST_URI']}
				</p>
			</div>
			<br>
			<button onclick=\"window.location='".URL_RAIZ_EMPRESA."';\">Voltar para o inicio.</button>
			<button onclick=\"history.back();\">Voltar para a página anterior.</button>
		</div>";

$viewer->content = "<div class=\"container\">{$out}</div>";
$viewer->run();
?>
