<?php
use files\viewerModel\padrao;
use application\libs\form;
use \files\menu\padrao as menu_padrao;
use files\database\qTipo_entidade;
use files\lib\auth;
    $qTEnt = new qTipo_entidade();
    $f = new form();
	$viewer = new padrao();
	$m = new menu_padrao();
    
    $viewer->menu = $m->menu;
	$viewer->addLogoDefault();
    
$sql ="select *from tipo_entidade";
$result = $qTEnt->runPrepared($sql)->fetchAll(\PDO::FETCH_ASSOC);
    
$entidade_tipo  = $f->createBasic_select("entidade_tipo","","Tipo de Entidade:&nbsp;");
//carrega o select de tipos de entidade
foreach ($result as $key=>$value){
        $qTEnt->addFieldOptionOpt($entidade_tipo,$value['descricao'],$value['tpentidade']);
    }
$fisicojuridico = $f->createBasic_select("fisicojuridico ","","Tipo Pessoa:&nbsp;");
$qTEnt->addFieldOptionOpt($fisicojuridico,'Físico','F');
$qTEnt->addFieldOptionOpt($fisicojuridico,'Jurídico','J');
$nome           = $f->createBasic_text("nome","","Nome:&nbsp;");
$nomefantasia   = $f->createBasic_text("nomefantasia","","Nome Fantasia:&nbsp;");
$endereco       = $f->createBasic_text("endereco","","Endereço:&nbsp;");
$bairro         = $f->createBasic_text("bairro","","Bairro:&nbsp;");
$cep =          $f->createBasic_cep("cep","","CEP","endereco","bairro","cidade","codmunicipio","uf");
$cidade         = $f->createBasic_text("cidade","","Cidade:&nbsp;");
$uf             = $f->createBasic_text("uf","","Estado:&nbsp;");
$tel1           = $f->createBasic_text("tel1","","Telefone:&nbsp;");
$tel1["options"]["pattern"] = "[0-9]+";
$dt_nasc		= $f->createBasic_date("dt_nasc","","Data de Nasc / Cadastro:&nbsp;");
$cgc_cpf		= $f->createBasic_text("cgc_cpf","","CPF/CNPJ:&nbsp;");
$cgc_cpf["options"]["pattern"] = "[0-9]+";
$insc_rg		= $f->createBasic_text("insc_rg","","Nº RG:&nbsp;");
$codmunicipio	= $f->createBasic_text("codmunicipio","","Código Municipal:&nbsp;");
$nro_endereco	= $f->createBasic_number("nro_endereco","","Nº Endereco:&nbsp;");
        
$body="";
$body .="<div  class='row col-xs-12 col-xs-offset-0 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-0 col-lg-4 col-lg-offset-0'>";
$body .="<form id='frmcadEntidade' class='col-lg-12'>";
$body .="<div id='cabecalio' class='col-lg-12'><h3>Cadastro de Entidade</h3></div>";
$body .="<div class='row col-lg-12'>";
$body .="<hr>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("entidade_tipo",$entidade_tipo,"col-lg-12");
$body .= $f->divCampo("fisicojuridico",$fisicojuridico,"col-lg-12");
$body .= $f->divCampo("nome",$nome,"col-lg-12");
$body .= $f->divCampo("nomefantasia",$nomefantasia,"col-lg-12");
$body .= $f->divCampo("endereco",$endereco,"col-lg-12");
$body .= $f->divCampo("bairro",$bairro,"col-lg-12");
$body .= $f->divCampo("cep",$cep,"col-lg-12");
$body .= $f->divCampo("cidade",$cidade,"col-lg-12");
$body .="</div>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("uf",$uf,"col-lg-12");
$body .= $f->divCampo("tel1",$tel1,"col-lg-12");
$body .= $f->divCampo("dt_nasc",$dt_nasc,"col-lg-12");
$body .= $f->divCampo("cgc_cpf",$cgc_cpf,"col-lg-12");
$body .= $f->divCampo("insc_rg",$insc_rg,"col-lg-12");
$body .= $f->divCampo("codmunicipio",$codmunicipio,"col-lg-12");
$body .= $f->divCampo("nro_endereco",$nro_endereco,"col-lg-12");
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