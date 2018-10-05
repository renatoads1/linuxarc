<?php
namespace files\modules\index\controller;

//import dataset para manitulação dos dados
use files\database\qArquivo;
use files\database\qUsuario;
use files\database\qModelo;
use files\database\qCvf;
use files\database\qTipo_entidade;
use files\database\qDepartamento;
use files\database\qBancodedados;
use application\libs\controllerRest;
use files\lib\auth;

class index extends controllerRest{
    
     public function __construct($module = null, $controller = null, $action = null, $opq = null, $autoLoad = true) {
        parent::__construct($module, $controller, $action, $opq, $autoLoad);
	}
        

		public function action_default() {
			$this->getControllerAction("index");
    }

    public function action_home(){
         $this->getControllerAction();
    }
        
    public function action_loginArquiv() {
        $input = $this->addInput("dados");     
        $u = $input["usuario"];
        $s = md5($input["senha"]);

        $retorno = $this->validaUser($u,$s);
        if($retorno!=false){
            

            $this->gravaSessao($retorno,$input['clienteId']);
            $this->addData("id", self::getSession()['aut']['id']);
            $this->addData("nome",self::getSession()['aut']['nome']);
            $this->addData("usuario",self::getSession()['aut']['usuario']);
            $this->printReturn();
			}
			else {
            self::deleteSession();
            controllerRest::getSession()['connected'] = false;
            $this->returnRestError('Usuário e ou Senha inválido');
            }
		}
       
    private function validaUser($u,$s){
        $arrwere = [$u,$s];
        //instancia o modelo usuarios
        $users = new qUsuario();
        $query = "select count(usuario),id from usuario where usuario= ? and senha = ?";    
        $ret = $users->runPrepared($query,$arrwere)->fetch();
        if($ret[0]==1){
            return $ret["id"];
			}
			else {
            return false;
        }
    }    
    private function gravaSessao($idUser,$cliente){
        $users = new qUsuario();
        $query = "select *from usuario where id = ? ";    
        $ret = $users->runPrepared($query,[$idUser])->fetch(\PDO::FETCH_ASSOC);
        controllerRest::getSession()['aut'] = $ret;
        controllerRest::getSession()['connected'] = true;
        controllerRest::getSession()['nome_cliente'] = $cliente;
        
    }
    public function action_cadDocumento(){
         $id = $this->addInput("id",null,FALSE,null);   
         if(count($_POST)>0){
            //instancia mmodel modelo de documento
            $qArq = new qArquivo();
            $model = $qArq->model(); 
                //pega os dados preenche o modelo e grava
               foreach($_POST["dados"] as $val){
                    if (isset($model[$val["name"]])) {
                        $model[$val["name"]]["value"] = $val["value"];
                    }
                }
                if(is_null($id)){
                    $qArq->insertModel($model);
                }else{
//                    $model["id"];
//                    $qModelo->updateModel($model);
                }

              $this->printReturn();
            }else{
               $this->getControllerAction();
            }
        
    }
    public function action_cadEntidade(){
            if(count($_POST)>0){
               $id=null;
               $input = $this->addInput("dados");
               $id = (int) $input[0]["value"];
               if(is_null($id)){
                    $qCvf = new qCvf();
                    $model = $qCvf->model();
               }else{
                    $qCvf = new qCvf();
                    $model = $qCvf->model($id);
               }    
              foreach ($input as $val) {
                //se o obj nao estiver vasio joga o valor do objajax dentro do obj $model
                    if (isset($model[$val['name']])) {
                        $model[$val['name']]['value'] = $val['value'];
                    }
                }               
                if(is_null($id)||$id==""||$id==false||$id==0){                    
                   $qCvf->insertModel($model);
                }else{
                   $qCvf->updateModel($model);
                }
                $this->printReturn();

  
            }else{
               $this->getControllerAction();
            }
    }
    public function action_cadModeloDocumento(){
        /*renato*/
        if(isset($_POST["chave"])&&$_POST["chave"]=="insert"){

            $qModelo = new qModelo();
            $model = $qModelo->model(); 
            $model["moddesc"]["value"]= $this->addInput("moddesc",null,FALSE,null);
            $qModelo->insertModel($model);  
            
        }elseif(isset($_POST["chave"])&&$_POST["chave"]=="update"){

            $modcod = $this->addInput("modcod",null,FALSE,null);
            $qModelo = new qModelo();
            $model = $qModelo->model($modcod); 
            $model["moddesc"]["value"]= $this->addInput("moddesc",null,FALSE,null);
            $qModelo->updateModel($model);
            
        }elseif(isset($_POST["chave"])&&$_POST["chave"]=="delete"){

            $modcod = $this->addInput("modcod",null,FALSE,null);
            $qModelo = new qModelo();
            $model = $qModelo->model($modcod); 
            $qModelo->deleteModel($model);
        }else{

             $this->getControllerAction();
        }

       
    }//fim do methodo
    public function action_cadTipoEntidade(){
        if(isset($_POST["chave"])&&$_POST["chave"]=="insert"){
            //insert
            $qTipo_entidade = new qTipo_entidade();
            $model = $qTipo_entidade->model();
            $model["tpentidade"]["value"]= $this->addInput("tpentidade",null,FALSE,null);
            $model["descricao"]["value"]= $this->addInput("descricao",null,FALSE,null);
            $qTipo_entidade->insertModel($model);  
            
        }elseif(isset($_POST["chave"])&&$_POST["chave"]=="update"){
            //update
            $tpent = $this->addInput("tpentidade",null,FALSE,null);
            $qTipo_entidade = new qTipo_entidade();
            $model = $qTipo_entidade->model($tpent);
            $model["tpentidade"]["value"]= $this->addInput("tpentidade",null,FALSE,null);
            $model["descricao"]["value"]= $this->addInput("descricao",null,FALSE,null);
            $qTipo_entidade->updateModel($model);
            
        }elseif(isset($_POST["chave"])&&$_POST["chave"]=="delete"){
            //delete
            $tpent = $this->addInput("tpentidade",null,FALSE,null);
            $qTipo_entidade = new qTipo_entidade();
            $model = $qTipo_entidade->model($tpent);
            $qTipo_entidade->deleteModel($model);
        }else{
            //abre pagina
             $this->getControllerAction();
        }
            
        $id = $this->addInput("id",null,FALSE,null);     

//            if(isset($_POST['dados'])){
//                $qTipo_entidade = new qTipo_entidade();
//                
//                $model = $qTipo_entidade->model(); 
//               foreach($_POST["dados"] as $val){
//                   
//                    if (isset($model[$val["name"]])) {
//                        $model[$val["name"]]["value"] = $val["value"];
//                    }
//                }
//                if(is_null($id)){
//                    $qTipo_entidade->insertModel($model);
//                }else{
////                    $model["id"];
////                    $qTipo_entidade->updateModel($model);
//                }
//              $this->printReturn();
//            }else{
//               $this->getControllerAction();
//            }
       
    }
    public function action_cadDepartamento(){
        if(isset($_POST["chave"])&&$_POST["chave"]=="insere"){
            $qDep = new qDepartamento();
            $model = $qDep->model();
            $model["departamento"]["value"] = $_POST["departamento"];
            $qDep->insertModel($model);
            $this->printReturn();
            
        }else if(isset($_POST["chave"])&&$_POST["chave"]=="update"){
            $id = $this->addInput("id",null,FALSE,null); 
            $qDep = new qDepartamento();
            $model = $qDep->model($id);
            $model["departamento"]["value"] = $_POST["departamento"];
            $qDep->updateModel($model);
            $this->printReturn();
            
        }else if(isset($_POST["chave"])&&$_POST["chave"]=="delete"){
            $id = $this->addInput("id",null,FALSE,null);
            $qDep = new qDepartamento();
            $model = $qDep->model($id);
            $qDep->deleteModel($model);
            
        }else{
            $this->getControllerAction();
        }
      
    }//fim da função
    
    public function action_cadDocumentoFileUpload(){
        //pega os dados do arquivo
       foreach ($_FILES as $key=>$value){          
            $name = $value["name"];
            $type = $value["type"];
            $tmp_name = $value["tmp_name"];
            $error = $value["error"];
            $size = $value["size"];         
       }
            //pega a extenção do arquivo
            $ext  = pathinfo($name, PATHINFO_EXTENSION);
 
            //testa extençoes permitidas
            $extpermit = ["txt","pdf","zip","docx"];
            if(in_array($ext,$extpermit))
            {//verifica se arquivo já existe em diretório
                $i="";
                $nomeorig = $name;
             while (file_exists(DIR_EMPRESA."".$name)){
                 $name = $i."".$nomeorig;
                 $i++;
             }
                $uploadfile = DIR_EMPRESA.basename($name);
                if (move_uploaded_file($tmp_name,$uploadfile)) {
                    $this->printReturn();
                }else{
                    $this->returnRestError("erro de Upload");
                }
         
        }else{
            $this->returnRestError("extenção inválida");
        }
    }
    public function action_consultaEntidade() {
			$this->getControllerAction();
    }
    public function action_excluirEntidade() {
        $id = $this->addInput("id");
        $qCvf = new qCvf();
        $model = $qCvf->model($id);
        $qCvf->deleteModel($model);
        $this->printReturn();
    }
    public function action_consultaDocumentos() {
			$this->getControllerAction();
    }
    public function action_excluirDocumentos() {
        $id = $this->addInput("id");
        $qArquivo = new qArquivo();
        $model = $qArquivo->model($id);
        $qArquivo->deleteModel($model);
        $this->printReturn();
    }
    public function action_alteraDocumento(){
        if(isset($_POST["chave"])&&$_POST["chave"]=="update"){
            $id = $this->addInput("id",null,FALSE,null); 
            $qArq = new qArquivo();
            $model = $qArq->model($id);
       
            $model["documento_de_quem"]["value"] = $_POST["documento_de_quem"];
            $model["modelo"]["value"] = $_POST["modelo"];
            $model["numero_doc"]["value"] = $_POST["numero_doc"];
            $model["obs"]["value"] = $_POST["obs"];
            $model["usuario_qarquivou"]["value"] = $_POST["usuario_qarquivou"];
            $model["valor"]["value"] = $_POST["valor"];
            $model["iddepartamento"]["value"] = $_POST["iddepartamento"];
            $model["dtemissao"]["value"] = $_POST["dtemissao"];
            
            $qArq->updateModel($model);
            $this->printReturn();
            
        }else if(isset($_POST["chave"])&&$_POST["chave"]=="delete"){
            $id = $this->addInput("id",null,FALSE,null);
            $qArq = new qArquivo();
            $model = $qArq->model($id);
            $qArq->deleteModel($model);
            
        }else{
            $this->getControllerAction();
        }

    }
    public function action_alteraModeloDocumento(){
          if(isset($_POST["modcod"])){
                
            $qMod = new qModelo();
            $model = $qMod->model(); 
            $model["modcod"]["value"] = $_POST["modcod"];
            $model["moddesc"]["value"] = $_POST["moddesc"];
            $qMod->updateModel($model);
            
          }else{
              $this->getControllerAction();
          }
    }
    public function action_excluiModeloDocumento(){
        $id = $this->addInput("modcod");
        $qModex = new qModelo();
        $model = $qModex->model($id); 
        $qModex->deleteModel($model);
        $this->getControllerAction();
    }
    //download de arquivos
    public function action_arq_down_zip() {
   $files = $this->addInput("files");
   //var_dump($files);
  //verifica se diretorio existe 
  if(!is_dir(DIR_RAIZ."files/private/temp/"))
    {
       mkdir(DIR_RAIZ."files/private/temp/", 0777, true);
    }
   $pathZIP = DIR_RAIZ."files/private/temp/".rand(1000, 9999).".zip";
   $zip = new \ZipArchive();
    //onde colocar  a variavel de sessao $_SESSION["scriptcase"]["control"]["glo_nm_path_doc"];
   if ($zip->open($pathZIP,\ZipArchive::CREATE | \ZipArchive::OVERWRITE) != true) {
    http_response_code(500);
    return;
   }
    foreach ($files as $value) 
    {
     if (!$zip->addFile($_SESSION["scriptcase"]["control"]["glo_nm_path_doc"]."/".$value, $value))
        {
            http_response_code(500);
            return;
        }
     
    }
			$zip->close();
            //$this->addData("zip",file_get_contents($pathZIP));
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"download.zip\"");
    		header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($pathZIP));
			echo file_get_contents($pathZIP);
            unlink($pathZIP);
		}
        public function action_arq_down(){
        //pega o id via post
        $idarq = $this->addInput("idarq");
        /*resgatar o id do arquivo, ido do user para alterar a coluna status*/
//        echo(json_encode($_SESSION)); 
//        $usuario = $_SESSION["id_usuario"];
        $usuario = $_SESSION['3a']['arquivamento']['aut']['id'];

        if($usuario!=null){
             $objUser = new qUsuario();
             $sql = "select count(id) from 3a_bancos.usuario where id = ? and contador = 'S'";
             
        //roda o select para buscar 
            $retorno = $objUser->runPrepared($sql,[$usuario])->fetch()[0];
            if($retorno){
        //aqui fazer o update do status do arquivo
               $sqlarq2 =  "update doc_3a.arquivo set contabilizado = 'S' where id = ?";
        //roda o sql no banco
               $objUser->runPrepared($sqlarq2,[$idarq]);
            }
                         
         }
        /*se o caboclo logado é contador faz o update*/
           if(isset($_SESSION['scriptcase']['control']['glo_nm_path_doc'])&& $_SESSION['scriptcase']['control']['glo_nm_path_doc']!=null)
            {
                $usr_section = $_SESSION['scriptcase']['control']['glo_nm_path_doc'];
            }else{
                $usr_section = "";echo("<script>alert(`faca login`);</script>");                
            }
            
            $file = $usr_section."/".$this->addInput("arq");
            $file = str_replace("\\","/",$file);
            $file = str_replace("../","/",$file);
            if(is_file($file)){        
                header("Content-Description: File Transfer");
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($file));
                echo file_get_contents($file);
            }else{
            //envia p pag erro
                http_response_code(404);
            }
            
        }//fim downloads
            public function action_desmarca_arq_contab(){
            //função que vai enviar             
            $id = $this->addInput("id");
            $objArq = new qArquivo();
            $model = $objArq->model($id);
            $model["contabilizado"]["value"]= "N";
            $objArq->updateModel($model);
            $this->printReturn();
            
        }
            public function action_verificaContador(){
            $id_user = $this->addInput("id_user");
            if($id_user!=null){
            $sqlcont = "select *from 3a_bancos.usuario where id = ?";
            $objUser = new qUsuario();
            $dados = $objUser->runPrepared($sqlcont,[$id_user])->fetch();
            $this->addData('dadosUser',$dados);
            $this->printReturn();
            
              }else{
                   $this->addError();
             }
        }
            public function action_marca_arq_contab(){
            //função que vai enviar 
            $objArq = new qArquivo();
            $id = $this->addInput("id");
            $sqlUp = "update doc_3a.arquivo set contabilizado ='S' where id = ?";
            $objArq->runPrepared($sqlUp,[$id]);
            $this->printReturn();
            
        }
        
        
    //fim da function

}//fim da classe

