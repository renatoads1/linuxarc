<?php

	use files\viewerModel\padrao;
	use \files\menu\padrao as menu_padrao;
    use files\lib\auth;

	$viewer = new padrao();
	$m = new menu_padrao();

	$viewer->menu = $m->menu;
	$viewer->addLogoDefault();
    

	//$viewer->content = "<div id='home'class='row col-lg-12'>". print_r($v)."</div>";
    $viewer->content = "<div id='home' class='row col-lg-12'></div>";
    $viewer->addClasses('scrollable docked');
	$viewer->run();