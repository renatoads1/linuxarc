<?php
use files\viewerModel\padrao;
use application\libs\form;
use \files\menu\padrao as menu_padrao;
use files\database\qModelo;
use files\staGrid\gModelo;
use files\lib\auth;

    $f = new form();
	$viewer = new padrao();
	$m = new menu_padrao();
    $gm = new gModelo();
    $viewer->menu = $m->menu;
	$viewer->addLogoDefault();
        
      $modcod = $f->createBasic_text("modcod","","modcod");              
      $moddesc = $f->createBasic_text("moddesc","","Descrição:&nbsp;");//varchar
      $moddesc["options"]["maxlength"] = 50;
      $moddesc["options"]["pattern"] = "^[a-zA-Z]+";
      $garantia = $f->createBasic_text("garantia","","Garantia:&nbsp;");//char 3
      $garantia["options"]["maxlength"] = 3;
      $garantia["options"]["pattern"] = "^[0-9]+";
      $vlr_mao_obra = $f->createBasic_number("vlr_mao_obra","","Valor de Mão de Obra:&nbsp;",true,null,null,0.01);//float
      $vlr_mao_obra["options"]["pattern"] = "[^0-9]";
      $vlr_visita = $f->createBasic_number("vlr_visita","","Valor Visita:&nbsp;",true,null,null,0.01);//float
      $vlr_visita["options"]["pattern"] = "[^0-9]";
      $hierarquia = $f->createBasic_text("hierarquia","","Hierarquia:&nbsp;");//varchar 10
      $hierarquia["options"]["maxlength"] = 10;
      $origem = $f->createBasic_text("origem","","Origem:&nbsp;"); //char 1
      $origem["options"]["maxlength"] = 1;
      $viewer->addLogoDefault();

$body ="<div id='todo' class='row col-lg-12'>";
$body .="<div id='cabecalio' class='col-lg-12'><h3>Cadastro de Modelo de Documento</h3></div>";
$body .="<div id='grid' class=' col-lg-12 col-lg-offset-0'>";
$body .=$gm->run();
$body .="</div>";
$body .="";$body .="<form id='frmcadModeloDocumento' class='row col-lg-12'>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("modcod",$modcod,"col-lg-12");
$body .= $f->divCampo("moddesc",$moddesc,"col-lg-12");
$body .= $f->divCampo("garantia",$garantia,"col-lg-12");
$body .= $f->divCampo("vlr_mao_obra",$vlr_mao_obra,"col-lg-12");
$body .="</div>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("vlr_visita",$vlr_visita,"col-lg-12");
$body .= $f->divCampo("hierarquia",$hierarquia,"col-lg-12");
$body .= $f->divCampo("origem",$origem,"col-lg-12");
$body .="</div>";
$body .= "<div class='row col-lg-12' id='footer'>";
$body .="<hr>";
$body .="<span id='btn_login_arquiv' onclick='btn_save_form(`frmcadModeloDocumento`);' class='btn btn-primary'>Salvar</span>";
$body .="<span id='btn_limpa_arquiv' onclick='btn_limpa_form(`frmcadModeloDocumento`);' class='btn btn-sulasuccess'>Limpar</span>";
$body .="</div>";
$body .="</form>";
$body .="</div>";

	$viewer->content = $body;
	$viewer->addClasses('scrollable docked');
	$viewer->run();