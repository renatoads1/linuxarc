<?php
use files\viewerModel\padrao;
use application\libs\form;
use \files\menu\padrao as menu_padrao;
use files\database\qTipo_entidade;
use files\lib\auth;
use files\database\qCvf;

    $qTEnt = new qTipo_entidade();
    $f = new form();
	$viewer = new padrao();
	$m = new menu_padrao();
    
    $viewer->menu = $m->menu;
	$viewer->addLogoDefault();
    
    $id = '-';
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
	}
    $objqcvf = new qCvf();
    $Modelcvf = $objqcvf->model($id);
  
       
$body="";
$body .="<div  class='row col-xs-12 col-xs-offset-0 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-0 col-lg-4 col-lg-offset-0'>";
$body .="<form id='frmcadEntidade' class='col-lg-12'>";
$body .="<div id='cabecalio' class='col-lg-12'><h3>Cadastro de Entidade</h3></div>";
$body .="<div class='row col-lg-12'>";
$body .="<hr>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("codigo",$Modelcvf["codigo"],"col-lg-12");
$body .= $f->divCampo("entidade_tipo",$Modelcvf["entidade_tipo"],"col-lg-12");
$body .= $f->divCampo("fisicojuridico",$Modelcvf["fisicojuridico"],"col-lg-12");
$body .= $f->divCampo("nome",$Modelcvf["nome"],"col-lg-12");
$body .= $f->divCampo("nomefantasia",$Modelcvf["nomefantasia"],"col-lg-12");
$body .= $f->divCampo("endereco",$Modelcvf["endereco"],"col-lg-12");
$body .= $f->divCampo("bairro",$Modelcvf["bairro"],"col-lg-12");
$body .= $f->divCampo("cep",$Modelcvf["cep"],"col-lg-12");
$body .= $f->divCampo("cidade",$Modelcvf["cidade"],"col-lg-12");
$body .="</div>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("uf",$Modelcvf["uf"],"col-lg-12");
$body .= $f->divCampo("tel1",$Modelcvf["tel1"],"col-lg-12");
$body .= $f->divCampo("dt_nasc",$Modelcvf["dt_nasc"],"col-lg-12");
$body .= $f->divCampo("cgc_cpf",$Modelcvf["cgc_cpf"],"col-lg-12");
$body .= $f->divCampo("insc_rg",$Modelcvf["insc_rg"],"col-lg-12");
$body .= $f->divCampo("codmunicipio",$Modelcvf["codmunicipio"],"col-lg-12");
$body .= $f->divCampo("nro_endereco",$Modelcvf["nro_endereco"],"col-lg-12");
$body .="</div>";
$body .="<div class='row col-lg-12' id='footer'>";
$body .="<hr>";
$body .="<span id='btn_login_arquiv' onclick='btn_save_form(`frmcadEntidade`);' class='btn btn-primary'>Salvar</span>";
$body .="<span id='btn_limpa_arquiv' onclick='btn_limpa_form(`frmcadEntidade`);' class='btn btn-sulasuccess'>Limpar</span>";
$body .="</div>";
$body .="</div>";
$body .="</form><br></div>";
$body .="</div>";

	$viewer->content = $body;
	$viewer->addClasses('scrollable docked');
	$viewer->run();