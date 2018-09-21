<?php
use files\viewerModel\padrao;
use application\libs\form;
use files\database\qCvf;
use files\database\qModelo;
use files\database\qDepartamento;
use files\database\qBancodedados;
use \files\menu\padrao as menu_padrao;
use files\lib\auth;
$usuarioLogado = $this->getSession()['aut']['usuario'];
$nomefantasia = ID_EMPRESA;
$qBan = new qBancodedados();
$qcvf = new qCvf();
$qmod = new qModelo();
$qdep = new qDepartamento();

$sqlBan ="select *from bancodedados where nomefantasia = '".$nomefantasia."'";
$resultBan = $qBan->runPrepared($sqlBan)->fetchAll(\PDO::FETCH_ASSOC);

$sql ="select codigo,nome,cgc_cpf from cvf order by nome";
$result = $qcvf->runPrepared($sql)->fetchAll(\PDO::FETCH_ASSOC);
 
$sqlm ="SELECT modcod,moddesc FROM modelo order by moddesc";
$resultm = $qmod->runPrepared($sqlm)->fetchAll(\PDO::FETCH_ASSOC);

$sqlm ="SELECT *FROM departamento ";
$resultd = $qdep->runPrepared($sqlm)->fetchAll(\PDO::FETCH_ASSOC);

    $f = new form();
	$viewer = new padrao();
	$m = new menu_padrao();
       
    $viewer->menu = $m->menu;
	$viewer->addLogoDefault();
    $data = date("Y-m-d");

$dtarquivo  = $f->createBasic_hidden("dtarquivo",$data,"Data do Arquivo:&nbsp;");
foreach ($resultBan as $key=>$value){
       $idBancos = $value["idbancos"];
    }
$idBancos = intval($idBancos);
$cliente = $f->createBasic_number("cliente",$idBancos,"Empresa:&nbsp;");
//$qdep->addFieldOption($cliente,["disable"=>"disabled"]);
//puchar tabela tipo entidades
$tpentidade           = $f->createBasic_select("tpentidade","","Tipo Entidade:&nbsp;");
//puchar tabela cvf
$documento_de_quem   = $f->createBasic_select("documento_de_quem","","Dono do Documento:&nbsp;");
//carrega o select de tipos de entidade
foreach ($result as $key=>$value){
        $qcvf->addFieldOptionOpt($documento_de_quem,$value['cgc_cpf']." - ". $value['nome'],$value['cgc_cpf']);
    }
$modelo       = $f->createBasic_select("modelo","","Modelo de Documento:&nbsp;");
foreach ($resultm as $key=>$value){
        $qmod->addFieldOptionOpt($modelo,$value['moddesc'],$value['modcod']);
    }
$numero_doc         = $f->createBasic_number("numero_doc","","Nº Documento:&nbsp;");
$obs            = $f->createBasic_textArea("obs","","OBS.:&nbsp;");
$usuario_qarquivou         = $f->createBasic_text("usuario_qarquivou","","Usuarioarquivo?????:&nbsp;");
$nivelacesso             = $f->createBasic_text("nivelacesso","","Nível de acesso??????:&nbsp;");
//input upload de documentos
$caminho           = $f->createBasic_fileupload("caminho","","Caminho do Arquivo:&nbsp;",URL_RAIZ_EMPRESA."/index/index/cadDocumentoFileUpload","{initUpload}");
$caminho['callBackOk'] = "uploadok";
$caminho['callBackError'] = "sendScannedDocsCbError";
$caminho['callBackProgress'] = "sendScannedDocsCbProgress";
$dtemissao		= $f->createBasic_date("dtemissao","","Data de Emissão:&nbsp;");
$dtvencimento		= $f->createBasic_text("dtvencimento","","Data de Vencimento:&nbsp;");
$dtpagamento		= $f->createBasic_text("dtpagamento","","Data de Pagamento:&nbsp;");
$valor	= $f->createBasic_money("valor","","Valor:&nbsp;");
$desconto	= $f->createBasic_text("desconto","","Desconto:&nbsp;");
$juros	= $f->createBasic_text("juros","","Júros:&nbsp;");
$valorfinal	= $f->createBasic_text("valorfinal","","Valor Final:&nbsp;");
$localizacao	= $f->createBasic_text("localizacao","","Localização Física do Documento:&nbsp;");
$iddepartamento	= $f->createBasic_select("iddepartamento","","Departamento:&nbsp;");

$usuario_qarquivou	= $f->createBasic_hidden("usuario_qarquivou",$usuarioLogado,"usuario_qarquivou:&nbsp;");

foreach ($resultd as $key=>$value){
        $qdep->addFieldOptionOpt($iddepartamento,$value['departamento'],$value['id']);
    }
$contabilizado	= $f->createBasic_select("contabilizado","","Contabilizado:&nbsp;");
$qdep->addFieldOptionOpt($contabilizado,"Sim","S");
$qdep->addFieldOptionOpt($contabilizado,"Não","N");
$body="";
$body .="<div  class='row col-xs-12 col-xs-offset-0 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-0 col-lg-4 col-lg-offset-0'>";
$body .="<form id='frmcadDocumento' class='col-lg-12'>";
$body .="<div id='cabecalio' class='col-lg-12'><h3>Cadastro de Documento</h3></div>";
$body .="<div class='row col-lg-12'>";
$body .="<hr>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("documento_de_quem",$documento_de_quem,"col-lg-12");
$body .= $f->divCampo("modelo",$modelo,"col-lg-12");
$body .= $f->divCampo("numero_doc",$numero_doc,"col-lg-12");
$body .= $f->divCampo("obs",$obs,"col-lg-12");
$body .= $f->divCampo("caminho",$caminho,"col-lg-12");
$body .= $f->divCampo("usuario_qarquivou",$usuario_qarquivou,"col-lg-12");
$body .="</div>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("dtarquivo",$dtarquivo,"col-lg-12");
$body .= $f->divCampo("dtemissao",$dtemissao,"col-lg-12");
$body .= $f->divCampo("cgc_cpf",$valor,"col-lg-12");
$body .= $f->divCampo("localizacao",$localizacao,"col-lg-12");
$body .= $f->divCampo("iddepartamento",$iddepartamento,"col-lg-12");
$body .= $f->divCampo("contabilizado",$contabilizado,"col-lg-12");
$body .= $f->divCampo("Empresa",$cliente,"col-lg-12");
$body .="</div>";
$body .="<div class='row col-lg-12' id='footer'>";//btn_save_form(`frmcadDocumento`)
$body .="<hr>";
$body .="<span id='btn_cadDocumento' onclick='btn_save_form(`frmcadDocumento`);' class='btn btn-primary disabled' >Salvar</span>";
$body .="<span id='btn_limpaDocumento' onclick='btn_limpa_form(`frmcadDocumento`);' class='btn btn-sulasuccess'>Limpar</span>";
$body .="</div>";
$body .="</div>";
$body .="</form><br></div>";
$body .="</div>";

	$viewer->content = $body;
	$viewer->addClasses('scrollable docked');
	$viewer->run();