<?php
    
use files\viewerModel\padrao;
use application\libs\form;
use files\viewerModel\html;
use application\libs\widgets; 
use \files\menu\padrao as menu_padrao;
use files\database\qDepartamento;
use files\database\qUsuario;
use files\lib\auth;
use files\staGrid\gDepartamento;

    $g = new gDepartamento();
    $f = new form();
	$viewer = new padrao();
	$m = new menu_padrao();
    $viewer->menu = $m->menu;
	$viewer->addLogoDefault();
    
    $iddep = $f->createBasic_text("id","","dep");
    $departamento = $f->createBasic_text("departamento","","Nome Departamento:&nbsp;");

    
$body ="<div id='todo' class='row col-lg-12'>";
$body .="<div id='cabecalio' class='col-lg-12'><h3>Cadastro de Departamento</h3></div>";
$body .="<div id='grid' class=' col-lg-12 col-lg-offset-0'>";
$body .=$g->run();
$body .="</div>";
$body .="<form id='frmcadDepartamento' class='col-lg-12'>";
$body .="<div class='row col-lg-12'>";
$body .="<hr>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("id",$iddep,"col-lg-6");
$body .= $f->divCampo("departamento",$departamento,"col-lg-6");
$body .="</div>";
$body .="<div class='row col-lg-6'>";
$body .="</div>";
$body .= "<div class='row col-lg-12' id='footer'>";
$body .="<hr>";
$body .="<span id='btn_login_arquiv' onclick='alert(`frmcadDepartamento`);' class='btn btn-primary'>Salvar</span>";
$body .="<span id='btn_limpa_arquiv' onclick='alert(`frmcadDepartamento`);' class='btn btn-sulasuccess'>Limpar</span>";
$body .="</div>";
$body .="</div>";
$body .="</form><br></div>";
$body .="</div>";

	$viewer->content = $body;
	$viewer->addClasses('scrollable docked');
	$viewer->run();