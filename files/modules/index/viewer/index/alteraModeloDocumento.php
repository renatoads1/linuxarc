<?php

use files\viewerModel\html;
use application\libs\widgets;    
use files\viewerModel\padrao;
use application\libs\form;
use \files\menu\padrao as menu_padrao;
use files\database\qModelo;
use files\staGrid\gModelo;
use files\lib\auth;

    $f = new form();
	$viewer = new padrao();
	$m = new menu_padrao();
    $qmod = new qModelo();
    $Model = $qmod->model();
    $gm = new gModelo();
    $viewer->menu = $m->menu;
	$viewer->addLogoDefault();
   
$body="";
$body .="<div id='telaAlteraModeloDocumento'  class='col-lg-12'>";
$body .="<div id='cabecalio' class='col-lg-12'><h3>Altera de Modelo de Documento</h3></div>";
$body .="<div id='gridModeloDoc' class='row  col-lg-12'>";
$body .= $gm->run();
$body .="<hr>";
$body .="</div>";
$body .="<div id='escForm'>";
$body .="<form id='frmAlteraModeloDocumento' class='col-lg-12'>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("modcod",$Model["modcod"],"col-lg-12");
$body .= $f->divCampo("moddesc",$Model["moddesc"],"col-lg-12");
$body .= $f->divCampo("garantia",$Model["garantia"],"col-lg-12");
$body .= $f->divCampo("vlr_mao_obra",$Model["vlr_mao_obra"],"col-lg-12");
$body .="</div>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("vlr_visita",$Model["vlr_visita"],"col-lg-12");
$body .= $f->divCampo("hierarquia",$Model["hierarquia"],"col-lg-12");
$body .= $f->divCampo("origem",$Model["origem"],"col-lg-12");
$body .="</div>";
$body .= "<div class='row col-lg-12' id='footer'>";
$body .="<hr>";
$body .="<span id='btn_login_arquiv' onclick='#(`frmAlteraModeloDocumento`);' class='btn btn-primary'>Salvar</span>";
$body .="<span id='btn_limpa_arquiv' onclick='#(`frmAlteraModeloDocumento`);' class='btn btn-sulasuccess'>Limpar</span>";
$body .="</div>";
$body .="</form><br></div>";
$body .="</div>";
$body .="</div>";
    

    
	$viewer->content .= $body;
	$viewer->addClasses('scrollable docked');
	$viewer->run();