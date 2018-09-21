<?php
namespace files\modules\index\controller;

//import dataset para manitulação dos dados
use files\database\qArquivo;
use files\database\qUsuario;
use files\database\qModelo;
use files\database\qCvf;
use files\database\qTipo_entidade;
use files\database\qDepartamento;
////importa dataset da tabela arquivos
//use files\database\qArquivo;
//use files\database\qBancodedados;
/*para poder extender a classe é preciso importar a classe
a ser extendida*/
use application\libs\controllerRest;

//use application\libs\viewer;
//class index extends controler rest que tem os metodos para chamar a view e outras
class index extends controllerRest{
    
        //esta action define que a ação deve ser a 
    public function __construct($module = null, $controller = null, $action = null, $opq = null, $autoLoad = true) {
        parent::__construct($module, $controller, $action, $opq, $autoLoad);
        //testar se o usuário esta logado se nao header location inicio
		}
        
    //view default a msg nao será impressa
		public function action_default() {
//chama viewer 
			$this->getControllerAction("index");
    }

    public function action_home(){
         $this->getControllerAction();
    }
        
    public function action_loginArquiv() {
        //pega dados vindos do post e constroi query
        $input = $this->addInput("dados");     
        $u = $input["usuario"];
        $s = md5($input["senha"]);

        $retorno = $this->validaUser($u,$s);
        if($retorno!=false){
            
            //gravar a sessao do user
            $this->gravaSessao($retorno,$input['clienteId']);
            $this->addData("id", self::getSession()['aut']['id']);
            $this->addData("nome",self::getSession()['aut']['nome']);
            $this->addData("usuario",self::getSession()['aut']['usuario']);
            $this->printReturn();
			}
			else {
            //limpa sessao
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
         /*renato*/
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
            }/*renato fim*/
        
    }
    public function action_cadEntidade(){
         /*renato*/
         $id = $this->addInput("id",null,FALSE,null);   
         if(count($_POST)>0){
//instancia mmodel modelo de documento
$qCvf = new qCvf();
$model = $qCvf->model(); 
                //pega os dados preenche o modelo e grava
               foreach($_POST["dados"] as $val){

                    if (isset($model[$val["name"]])) {
                        $model[$val["name"]]["value"] = $val["value"];
                    }
                }
                if(is_null($id)){
                 
                    $qCvf->insertModel($model);
                    
                }else{
//                    $model["id"];
//                    $qModelo->updateModel($model);
                }

              $this->printReturn();
  
            }else{
               $this->getControllerAction();
            }/*renato fim*/
    }
    public function action_cadModeloDocumento(){
        /*renato*/
         $id = $this->addInput("id",null,FALSE,null);   
         if(count($_POST)>0){
//instancia mmodel modelo de documento
$qModelo = new qModelo();
$model = $qModelo->model(); 
                //pega os dados preenche o modelo e grava
               foreach($_POST["dados"] as $val){

                    if (isset($model[$val["name"]])) {
                        $model[$val["name"]]["value"] = $val["value"];
                    }
                }
                if(is_null($id)){
                 
                    $qModelo->insertModel($model);
                    
                }else{
//                    $model["id"];
//                    $qModelo->updateModel($model);
                }

              $this->printReturn();
  
            }else{
               $this->getControllerAction();
            }/*renato fim*/
       
    }//fim do methodo
    public function action_cadTipoEntidade(){
       //se isset post faça se nao carregue a pagina
            $id = $this->addInput("id",null,FALSE,null);   
        
           if(count($_POST)>0){
               
$qTipo_entidade = new qTipo_entidade();
$model = $qTipo_entidade->model(); 

               foreach($_POST["dados"] as $val){
                   
                    if (isset($model[$val["name"]])) {
                        $model[$val["name"]]["value"] = $val["value"];
                    }
                }
                if(is_null($id)){
                   // var_dump(\application\libs\database::$configInstances);
                    $qTipo_entidade->insertModel($model);
                    
                }else{
//                    $model["id"];
//                    $qTipo_entidade->updateModel($model);
                }

              $this->printReturn();
  
            }else{
               $this->getControllerAction();
            }
       
    }
    public function action_cadDepartamento(){
         //se isset post faça se nao carregue a pagina
            $id = $this->addInput("id",null,FALSE,null);   
        
           if(count($_POST)>0){
               
$qDep = new qDepartamento();
$model = $qDep->model(); 

               foreach($_POST["dados"] as $val){
                   
                    if (isset($model[$val["name"]])) {
                        $model[$val["name"]]["value"] = $val["value"];
                    }
                }
                if($id==NULL){
                   // var_dump(\application\libs\database::$configInstances);
                    $qDep->insertModel($model);
                
                }else{
                    //$model["id"];
                    //$qTipo_entidade->updateModel($model);
                }
              $this->printReturn();
  
            }else{
               $this->getControllerAction();
            }
    }
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
                    //echo("<br>status:enviado<br>nome:".$name."<br>type:".$type."<br>temp_name:".$tmp_name."<br>erro:".$error."<br>size".$size."<br>ext".$ext."<br>");
                }else{
                    $this->returnRestError("erro de Upload");
                }
         
        }else{
            $this->returnRestError("extenção inválida");
           
        }
       


    }//fim da function
    

}//fim da classe

