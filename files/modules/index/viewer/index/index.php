<?php
//importe
use application\libs\widgets;
use files\viewerModel\padrao;
use files\viewerModel\html;
use application\libs\form;
use files\database\qUsuario;
use \files\menu\padrao as menu_padrao;
//objs//
$que = new qUsuario();
$htm = new html();
$f = new form();

	$viewer = new padrao();
//    colocar css 
//    $viewer->body->style = "nav{display:none;}body{padding:0;}";
//    menu
//	$viewer->addLogoDefault();
//	$menu = new menu_padrao();
//	$viewer->menu = $menu->menu;
    
//chama grid
//joga grid na out
//colocar chamada javascript na pagina
//criando form
$usuario = $f->createBasic_text("usuario","","Usuário:&nbsp;","pattern");
$senha = $f->createBasic_password("senha","","Senha:&nbsp;");
$cliente = $f->createBasic_select("cliente","","Cliente:&nbsp;");
$sql = "select * from bancodedados";
//$sql = "select idbancos,razao_social from bancodedados";
$result = $que->runPrepared($sql)->fetchAll(\PDO::FETCH_ASSOC);

foreach ($result as $key=>$value){
    $que->addFieldOptionOpt($cliente,$value['razao_social'],$value['idbancos']);
}
$out ="";
$nomeempresa=strtoupper(ID_EMPRESA);
//coloca itens no body
$body="";
$body .= $out;
$body .="<div  class='row col-xs-12 col-xs-offset-0 col-md-8 col-sm-6 col-sm-offset-3 col-md-8 col-md-offset-2 col-lg-4 col-lg-offset-4'>";
$body .="<div id='cabecalio' class='col-lg-12'><h3>".$nomeempresa."</h3></div>";
$body .="<form id='frm_login_arqui' class='col-lg-12'>";
$body .= $f->divCampo("usuario",$usuario,"col-lg-12");
$body .= $f->divCampo("senha",$senha,"col-lg-12");
$body .= $f->divCampo("cliente",$cliente,"col-lg-12");
$body .="<div class='text-primary' style='font-weight: bold;'><div id='caixabotoes' class='col-lg-12'>";
$body .="<span id='btn_login_arquiv' onclick='btn_login_arquiv(`frm_login_arqui`);' class='btn btn-primary'>Login</span>";
$body .="<span id='btn_limpa_arquiv' onclick='btn_limpa_arquiv(`frm_login_arqui`);' class='btn btn-sulasuccess'>Limpar</span>";
$body .="</div></form>";
$body .="</div>";
$body .="</div>";

	$viewer->content = $body;
	$viewer->run();
