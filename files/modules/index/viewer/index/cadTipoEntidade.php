<?php
use files\viewerModel\padrao;
use application\libs\form;
use \files\menu\padrao as menu_padrao;
use files\database\qTipo_entidade;
use files\lib\auth;
    $qEnti = new qTipo_entidade();
    $f = new form();
	$viewer = new padrao();
	$m = new menu_padrao();
    
    $sql ="select *from tipo_entidade";
    $result = $qEnti->runPrepared($sql)->fetchAll(\PDO::FETCH_ASSOC);
    
    $tipoentidade = $f->createBasic_text("tpentidade","","Tipo de Entidade:&nbsp;");
    $descricao = $f->createBasic_text("descricao","","Modelo:&nbsp;");
    //VALIDAÇÃO
    $tipoentidade["options"]["maxlength"] = 3;//até 3 numeros
    $tipoentidade["options"]["pattern"] = "[a-zA-Z]+";
    $descricao["options"]["maxlength"] = 50;
    $descricao["options"]["pattern"] = "[a-zA-Z]+";//apenas letras
	
    
    $viewer->menu = $m->menu;
	$viewer->addLogoDefault();
    
$body="";
$body .="<div  class='row col-xs-12 col-xs-offset-0 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-0 col-lg-4 col-lg-offset-0'>";
$body .="<form id='frmcadTipoEntidade' class='col-lg-12'>";
$body .="<div id='cabecalio' class='col-lg-12'><h3>renato</h3></div>";
$body .="<div class='row col-lg-12'>";
$body .="<hr>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("tpentidade",$tipoentidade,"col-lg-12");
$body .="</div>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("descricao",$descricao,"col-lg-12");
$body .="</div>";
$body .= "<div class='row col-lg-12' id='footer'>";
$body .="<hr>";
$body .="<span id='btn_login_arquiv' onclick='btn_save_form(`frmcadTipoEntidade`);' class='btn btn-primary'>Salvar</span>";
$body .="<span id='btn_limpa_arquiv' onclick='btn_limpa_form(`frmcadTipoEntidade`);' class='btn btn-sulasuccess'>Limpar</span>";
$body .="</div>";
$body .="</div>";
$body .="</form><br></div>";
$body .="</div>";

	$viewer->content = $body;
	$viewer->addClasses('scrollable docked');
	$viewer->run();