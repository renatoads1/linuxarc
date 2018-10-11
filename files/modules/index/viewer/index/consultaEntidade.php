<?php

	use files\viewerModel\padrao;
    use files\viewerModel\html;
    use application\libs\widgets;
	use \files\menu\padrao as menu_padrao;
    use files\lib\auth;
    use files\staGrid\gCvf;

	$viewer = new padrao();
	$m = new menu_padrao();
    $gc = new gCvf();
    $ht = new html();
    //joga grid na out
    $viewer->menu = $m->menu;
	$viewer->addLogoDefault();
//seta os filtros que vão aparecer no grid
$out = $gc->setFilters([['nome'=>'codigo','type'=>'int','label'=>'Código'],
   ['nome' => 'entidade_tipo','type' => 'text','label' => 'Tipo de Entidade']
    ,['nome' => 'fisicojuridico','type' => 'text','label' => 'Fisico ou Juridico']
    ,['nome' => 'nome','type' => 'text','label' => 'Nome']
    ,['nome' => 'nomefantasia','type' => 'text','label' => 'Nome fantasia']
    ,['nome' => 'endereco','type' => 'text','label' => 'Endereço']
    ,['nome' => 'bairro','type' => 'text','label' => 'Bairro']]);
    
$viewer->content .= $out;

$viewer->content = $gc->run();
	$viewer->addClasses('scrollable docked');
	$viewer->run();