<?php
    
use files\viewerModel\padrao;
use application\libs\form;
use \files\menu\padrao as menu_padrao;
use files\database\qDepartamento;
use files\database\qUsuario;
use files\lib\auth;

    $f = new form();
	$viewer = new padrao();
	$m = new menu_padrao();
    $viewer->menu = $m->menu;
	$viewer->addLogoDefault();

        
    $departamento = $f->createBasic_text("departamento","","Nome Departamento:&nbsp;");

    
$body="";
$body .="<div  class='row col-xs-12 col-xs-offset-0 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-0 col-lg-4 col-lg-offset-0'>";
$body .="<form id='frmcadDepartamento' class='col-lg-12'>";
$body .="<div id='cabecalio' class='col-lg-12'><h3>Cadastro de Departamento</h3></div>";
$body .="<div class='row col-lg-12'>";
$body .="<hr>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("departamento",$departamento,"col-lg-6");
$body .="</div>";
$body .="<div class='row col-lg-6'>";
$body .="</div>";
$body .= "<div class='row col-lg-12' id='footer'>";
$body .="<hr>";
$body .="<span id='btn_login_arquiv' onclick='btn_save_form(`frmcadDepartamento`);' class='btn btn-primary'>Salvar</span>";
$body .="<span id='btn_limpa_arquiv' onclick='btn_limpa_form(`frmcadDepartamento`);' class='btn btn-sulasuccess'>Limpar</span>";
$body .="</div>";
$body .="</div>";
$body .="</form><br></div>";
$body .="</div>";

	$viewer->content = $body;
	$viewer->addClasses('scrollable docked');
	$viewer->run();