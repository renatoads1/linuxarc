<?php
use application\libs\widgets;
use application\libs\form;
use files\viewerModel\padrao;
use \files\menu\padrao as menu_padrao;
use files\viewerModel\html;
use files\staGrid\gCvf;
use files\staGrid\gArquivo;
use files\database\qArquivo;
use files\database\qModelo;
use files\database\qDepartamento;
    
    $qcvf = new qArquivo();
    $qmod = new qModelo();
    $qdep = new qDepartamento();
    //dropdown
    $ModArq = new qArquivo();
    $model = $ModArq->model();
    $viewer = new padrao();
	$m = new menu_padrao();
    $gd = new gArquivo();
    $ht = new html();
    $f = new form();
     //joga grid na out
    $viewer->menu = $m->menu;
	$viewer->addLogoDefault();
    
    $sqlm ="SELECT modcod,moddesc FROM modelo order by moddesc";
    $resultm = $qmod->runPrepared($sqlm)->fetchAll(\PDO::FETCH_ASSOC);
    $modelo = $f->createBasic_select("modelo","","Modelo de Documento:&nbsp;");
    foreach ($resultm as $key=>$value){
            $qmod->addFieldOptionOpt($modelo,$value['moddesc'],$value['modcod']);
        }
        
    $sqlm ="SELECT *FROM departamento ";
    $resultd = $qdep->runPrepared($sqlm)->fetchAll(\PDO::FETCH_ASSOC);
    $iddepartamento	= $f->createBasic_select("iddepartamento","","Departamento:&nbsp;");
    foreach ($resultd as $key=>$value){
        $qdep->addFieldOptionOpt($iddepartamento,$value['departamento'],$value['id']);
    }
    
    $sql ="select codigo,nome,cgc_cpf from cvf order by nome";
    $result = $qcvf->runPrepared($sql)->fetchAll(\PDO::FETCH_ASSOC);
    $documento_de_quem   = $f->createBasic_select("documento_de_quem","","Dono do Documento:&nbsp;");
    foreach ($result as $key=>$value){
        $qcvf->addFieldOptionOpt($documento_de_quem,$value['cgc_cpf']." - ". $value['nome'],$value['cgc_cpf']);
    }
    
    
    
    
//seta os filtros que vão aparecer no grid
$out = $gd->setFilters([['nome'=>'dtarquivo','type'=>'date','label'=>'Data de Envio'],
    ['nome' => 'id','label' => 'Id','type' => 'int']
    ,['nome' => 'documento_de_quem','type' => 'text','label' => 'Dono do documento']
    ,['nome' => 'caminho','type' => 'text','label' => 'Nome do arquivo']
    ,['nome' => 'modelo','type' => 'int','label' => 'Modelo']
    ,['nome' => 'numero_doc','label' => 'Nº documento','type' => 'text']
    ,['nome' => 'usuario_qarquivou','label' => 'Usuário','type' => 'text']
    ,['nome' => 'dtvencimento','label' => 'Data de Vencimento','type' => 'date']
    ,['nome' => 'dtemissao','label' => 'Data de Emissão','type' => 'date']
    ,['nome' => 'valor','label' => 'Valor','type' => 'text']]);
    
$viewer->content .= $out;
$viewer->content = $gd->run();

$body = "";
$body .="<form id='frmalteraDocumento' class='row col-lg-12'>";
$body .="<div id='cabecalio' class='col-lg-12'><h3>Altera Documento</h3></div>";
$body .="<hr>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("id",$model["id"],"col-lg-12");
//$body .= $f->divCampo("cliente",$model["cliente"],"col-lg-12");
//$body .= $f->divCampo("tpentidade",$ModArq["tpentidade"],"col-lg-12");
$body .= $f->divCampo("documento_de_quem",$documento_de_quem,"col-lg-12");
$body .= $f->divCampo("modelo",$modelo,"col-lg-12");
$body .= $f->divCampo("numero_doc",$model["numero_doc"],"col-lg-12");
$body .= $f->divCampo("obs",$model["obs"],"col-lg-12");
$body .="</div>";
$body .="<div class='row col-lg-6'>";
$body .= $f->divCampo("usuario_qarquivou",$model["usuario_qarquivou"],"col-lg-12");
$body .= $f->divCampo("caminho",$model["caminho"],"col-lg-12");
$body .= $f->divCampo("valor",$model["valor"],"col-lg-12");
$body .= $f->divCampo("iddepartamento",$iddepartamento,"col-lg-12");
$body .= $f->divCampo("dtemissao",$model["dtemissao"],"col-lg-12");
$body .="</div>";
$body .="</form>";

$viewer->content .= $body;
$viewer->addClasses('scrollable docked');
$viewer->run();




























/*
$out = "";
//$p = new padrao();
$p = new html();
//instancia componentes de formulário
$f = new form();
//instancia componentes de botoes/widgets bootstrap
$b = new widgets();
//criando campos de um formulário
$ga = new gArquivo();
//serve para incluir coisas apenas no head
$htm  = $p->addIncludesHtml("<script src='".URL_RAIZ."/files/modules/index/viewer/index/consultaEntidade.js'></script>");
//.filter_staGridgArquivo{width:auto;}
//$htm  .= $p->addIncludesHtml("<style>body {background-color: #ccc;}.tableheaders{background-color: #ccc;}</style>");
// if(isset($_SESSION['nome_cliente_default'])){
//     $tit = $_SESSION['nome_cliente_default'];
// }else{
//    $tit="";
// }
// if(isset($_SERVER['SERVER_NAME'])){
//     $link = "http://".$_SERVER['SERVER_NAME'].":81/arquivodigital";
// }else{
//     $link = "#";
// }
// $server = "<a href ='".$link."'><span class='btn btn-primary btn-xs'><i class='glyphicon glyphicon-chevron-left'></i>&nbsp;voltar</span></a>";
// $htm  .= $p->addIncludesHtml("<div class='row-fluid col-lg-12' id='ht_titulo'>&nbsp;".$server."&nbsp;".$tit."</div>");
//formulário aqui dentro
if(isset($_SESSION["id_usuario"])&& isset($_SESSION["usu_banco"])){
    //seta dados usuário no js
    $ht = "";
    //carrega os dados do usuário no javascript
    $ht .= "<script>id_userx = ".$_SESSION['id_usuario'].";</script>";
    $ht .= "<script>name_user = ".$_SESSION['usu_banco'].";</script>";
 }else{
    //se o usuário nao estiver logado abre aba de login ***********mudar***********
    echo "<script language='javascript'>window.open('http://192.168.15.31:81/arquivodigital/control/', '_blank');</script>";
    //$ht = "erro: <br>usu_banco = ".$_SESSION["usu_banco"]."<br>id_usuario=".$_SESSION['id_usuario'];
}
//seta os filtros que vão aparecer no grid
$ga->setFilters([['nome'=>'dtarquivo','type'=>'date','label'=>'Data de Envio'],
    ['nome' => 'id','label' => 'Id','type' => 'int']
    ,['nome' => 'documento_de_quem','type' => 'text','label' => 'Dono do documento']
    ,['nome' => 'caminho','type' => 'text','label' => 'Nome do arquivo']
    ,['nome' => 'modelo','type' => 'int','label' => 'Modelo']
    ,['nome' => 'numero_doc','label' => 'Nº documento','type' => 'text']
    ,['nome' => 'usuario_qarquivou','label' => 'Usuário','type' => 'text']
    ,['nome' => 'dtvencimento','label' => 'Data de Vencimento','type' => 'date']
    ,['nome' => 'dtemissao','label' => 'Data de Emissão','type' => 'date']
    ,['nome' => 'valor','label' => 'Valor','type' => 'text']]);

$ht .= $ga->run();
//formulário aqui dentro

$out .= $ht;
$p->content = $out;
//para inserir javascript no final do documento
//$p->body->js = "alert('carregou');";
//para colocar no onload do documento
//$p->body->js = "alert(id_userz);";
$p->run();
*/