<?php

	use files\viewerModel\padrao;
	use \files\menu\padrao as menu_padrao;
    use files\lib\auth;

	$viewer = new padrao();
	$m = new menu_padrao();

	$viewer->menu = $m->menu;
	$viewer->addLogoDefault();
    
	$viewer->content = "";
	$viewer->addClasses('scrollable docked');
	$viewer->run();