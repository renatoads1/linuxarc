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

class index extends controllerRest {

    public function __construct($module = null, $controller = null, $action = null, $opq = null, $autoLoad = true) {
        parent::__construct($module, $controller, $action, $opq, $autoLoad);
    }

    public function action_default() {
        $this->getControllerAction("index");
    }

    public function action_home() {
        $this->getControllerAction();
    }

    public function action_loginArquiv() {
        $input = $this->addInput("dados");
        $u = $input["usuario"];
        $s = md5($input["senha"]);

        $retorno = $this->validaUser($u, $s);
        if ($retorno != false) {

            $this->gravaSessao($retorno, $input['clienteId']);
            $this->addData("id", self::getSession()['aut']['id']);
            $this->addData("nome", self::getSession()['aut']['nome']);
            $this->addData("usuario", self::getSession()['aut']['usuario']);
            $this->printReturn();
        } else {
            self::deleteSession();
            controllerRest::getSession()['connected'] = false;
            $this->returnRestError('Usuário e ou Senha inválido');
        }
    }

    private function validaUser($u, $s) {
        $arrwere = [$u, $s];
        //instancia o modelo usuarios
        $users = new qUsuario();
        $query = "select count(usuario),id from usuario where usuario= ? and senha = ?";
        $ret = $users->runPrepared($query, $arrwere)->fetch();
        if ($ret[0] == 1) {
            return $ret["id"];
        } else {
            return false;
        }
    }

    private function gravaSessao($idUser, $cliente) {
        $users = new qUsuario();
        $query = "select *from usuario where id = ? ";
        $ret = $users->runPrepared($query, [$idUser])->fetch(\PDO::FETCH_ASSOC);

        controllerRest::getSession()['aut'] = $ret;
        controllerRest::getSession()['connected'] = true;
        controllerRest::getSession()['nome_cliente'] = $cliente;
    }

//i incluir
//a alterar
//e excluir
//c consultar
    public function action_cadDocumento() {
        //começo checa permissao
        $documento_i = controllerRest::getSession()['aut']['documento_i'];
        $documento_a = controllerRest::getSession()['aut']['documento_a'];

        $id = $this->addInput("id", null, FALSE, null);
        if (isset($_POST["dados"])) {
            //instancia mmodel modelo de documento
            $qArq = new qArquivo();
            $model = $qArq->model();
            //pega os dados preenche o modelo e grava
            foreach ($_POST["dados"] as $val) {
                if (isset($model[$val["name"]])) {
                    $model[$val["name"]]["value"] = $val["value"];
                }
            }
            if ($documento_i == "S") {
                $qArq->insertModel($model);
                $this->printReturn();
            } else {
                $this->getErrors();
            }
        } else {
            $this->getControllerAction();
        }

        //fim
    }

    public function action_cadEntidade() {
        //começo checa permiiss
        $cvf_i = controllerRest::getSession()['aut']['cvf_i'];
        $cvf_a = controllerRest::getSession()['aut']['cvf_a'];

        if (count($_POST) > 0) {
            $id = null;
            $input = $this->addInput("dados");
            $id = (int) $input[0]["value"];
            if (is_null($id)) {
                $qCvf = new qCvf();
                $model = $qCvf->model();
            } else {
                $qCvf = new qCvf();
                $model = $qCvf->model($id);
            }
            foreach ($input as $val) {
                //se o obj nao estiver vasio joga o valor do objajax dentro do obj $model
                if (isset($model[$val['name']])) {
                    $model[$val['name']]['value'] = $val['value'];
                }
            }
            if (is_null($id) || $id == "" || $id == false || $id == 0) {
                if ($cvf_i == "S") {
                    $qCvf->insertModel($model);
                    $this->printReturn();
                } else {
                    $this->getErrors();
                }
            } else {
                if ($cvf_a == "S") {
                    $qCvf->updateModel($model);
                    $this->printReturn();
                } else {
                    $this->getErrors();
                }
            }
        } else {
            $this->getControllerAction();
        }
    }

    public function action_cadModeloDocumento() {
        /* inicio */
        $modelo_i = controllerRest::getSession()['aut']['modelo_i'];
        $modelo_a = controllerRest::getSession()['aut']['modelo_a'];
        $modelo_e = controllerRest::getSession()['aut']['modelo_e'];

        if (isset($_POST["chave"]) && $_POST["chave"] == "insert") {

            $qModelo = new qModelo();
            $model = $qModelo->model();
            $model["moddesc"]["value"] = $this->addInput("moddesc", null, FALSE, null);

            if ($modelo_i == "S") {
                $qModelo->insertModel($model);
            } else {
                $this->getErrors();
            }
        } elseif (isset($_POST["chave"]) && $_POST["chave"] == "update") {

            $modcod = $this->addInput("modcod", null, FALSE, null);
            $qModelo = new qModelo();
            $model = $qModelo->model($modcod);
            $model["moddesc"]["value"] = $this->addInput("moddesc", null, FALSE, null);

            if ($modelo_a == "S") {
                $qModelo->updateModel($model);
            } else {
                $this->getErrors();
            }
        } elseif (isset($_POST["chave"]) && $_POST["chave"] == "delete") {

            $modcod = $this->addInput("modcod", null, FALSE, null);
            $qModelo = new qModelo();
            $model = $qModelo->model($modcod);

            if ($modelo_e == "S") {
                $qModelo->deleteModel($model);
            } else {
                $this->getErrors();
            }
        } else {
            //consulta
            $this->getControllerAction();
        }
    }

//fim do methodo

    public function action_cadTipoEntidade() {
        //nao tem colunas de permissao nao tem como verificar usar cvf
        $usuario_i = controllerRest::getSession()['aut']['usuario_i'];
        $usuario_a = controllerRest::getSession()['aut']['usuario_a'];
        $usuario_e = controllerRest::getSession()['aut']['usuario_e'];

        if (isset($_POST["chave"]) && $_POST["chave"] == "insert") {
            //insert
            $qTipo_entidade = new qTipo_entidade();
            $model = $qTipo_entidade->model();
            $model["tpentidade"]["value"] = $this->addInput("tpentidade", null, FALSE, null);
            $model["descricao"]["value"] = $this->addInput("descricao", null, FALSE, null);
            if ($usuario_i == "S") {
                $qTipo_entidade->insertModel($model);
            } else {
                $this->getErrors();
            }
        } elseif (isset($_POST["chave"]) && $_POST["chave"] == "update") {
            //update
            $tpent = $this->addInput("tpentidade", null, FALSE, null);
            $qTipo_entidade = new qTipo_entidade();
            $model = $qTipo_entidade->model($tpent);
            $model["tpentidade"]["value"] = $this->addInput("tpentidade", null, FALSE, null);
            $model["descricao"]["value"] = $this->addInput("descricao", null, FALSE, null);
            if ($usuario_a == "S") {
                $qTipo_entidade->updateModel($model);
            } else {
                $this->getErrors();
            }
        } elseif (isset($_POST["chave"]) && $_POST["chave"] == "delete") {
            //delete
            $tpent = $this->addInput("tpentidade", null, FALSE, null);
            $qTipo_entidade = new qTipo_entidade();
            $model = $qTipo_entidade->model($tpent);
            if ($usuario_e == "S") {
                $qTipo_entidade->deleteModel($model);
            } else {
                $this->getErrors();
            }
        } else {
            //abre pagina
            $this->getControllerAction();
        }
    }

    public function action_cadDepartamento() {
        $usuario_i = controllerRest::getSession()['aut']['usuario_i'];
        $usuario_a = controllerRest::getSession()['aut']['usuario_a'];
        $usuario_e = controllerRest::getSession()['aut']['usuario_e'];

        if (isset($_POST["chave"]) && $_POST["chave"] == "insere") {
            $qDep = new qDepartamento();
            $model = $qDep->model();
            $model["departamento"]["value"] = $_POST["departamento"];
            if ($usuario_i == "S") {
                $qDep->insertModel($model);
                $this->printReturn();
            } else {
                $this->getErrors();
            }
        } else if (isset($_POST["chave"]) && $_POST["chave"] == "update") {
            $id = $this->addInput("id", null, FALSE, null);
            $qDep = new qDepartamento();
            $model = $qDep->model($id);
            $model["departamento"]["value"] = $_POST["departamento"];
            if ($usuario_a == "S") {
                $qDep->updateModel($model);
                $this->printReturn();
            } else {
                $this->getErrors();
            }
        } else if (isset($_POST["chave"]) && $_POST["chave"] == "delete") {
            $id = $this->addInput("id", null, FALSE, null);
            $qDep = new qDepartamento();
            if ($usuario_e == "S") {
                $model = $qDep->model($id);
                $qDep->deleteModel($model);
            } else {
                $this->getErrors();
            }
        } else {
            $this->getControllerAction();
        }
    }

//fim da função

    public function action_cadDocumentoFileUpload() {
        $cvf_i = controllerRest::getSession()['aut']['cvf_i'];
        $cvf_a = controllerRest::getSession()['aut']['cvf_a'];
        //pega os dados do arquivo
        foreach ($_FILES as $key => $value) {
            $name = $value["name"];
            $type = $value["type"];
            $tmp_name = $value["tmp_name"];
            $error = $value["error"];
            $size = $value["size"];
//inicio upload
//pega a extenção do arquivo
            $ext = pathinfo($name, PATHINFO_EXTENSION);

            //testa extençoes permitidas
            $extpermit = ["txt", "pdf", "zip", "docx"];
            if (in_array($ext, $extpermit)) {
                $i = "";
                $nomeorig = $name;
                while (file_exists(DIR_EMPRESA . "" . $name)) {
                    $name = $i . "" . $nomeorig;
                    $i++;
                }
                $uploadfile = DIR_EMPRESA . basename($name);
                if ($cvf_i == "S") {
                    if (move_uploaded_file($tmp_name, $uploadfile)) {
                        //$this->printReturn();
                    } else {
                        //$this->returnRestError("erro de Upload");
                    }
                } else {
                    //$this->returnRestError("erro de Permissão");
                }
            } else {
                //$this->returnRestError("extenção inválida");
            }

            //fim pega files
        }
        $this->printReturn();
        
    }//fim da função

    public function action_consultaEntidade() {
        $this->getControllerAction();
    }

    public function action_excluirEntidade() {
        $cvf_e = controllerRest::getSession()['aut']['cvf_e'];

        $id = $this->addInput("id");
        $qCvf = new qCvf();
        $model = $qCvf->model($id);
        if ($cvf_e == "S") {
            $qCvf->deleteModel($model);
            $this->printReturn();
        } else {
            $this->getErrors();
        }
    }

    public function action_consultaDocumentos() {
        $this->getControllerAction();
    }

    public function action_excluirDocumentos() {
        $documento_e = controllerRest::getSession()['aut']['documento_e'];

        $id = $this->addInput("id");
        $qArquivo = new qArquivo();
        $model = $qArquivo->model($id);
        if ($documento_e == "S") {
            $qArquivo->deleteModel($model);
            $this->printReturn();
        } else {
            $this->getErrors();
        }
    }

    public function action_alteraDocumento() {
        $arq_i = controllerRest::getSession()['aut']['documento_i'];
        $arq_a = controllerRest::getSession()['aut']['documento_a'];
        $arq_e = controllerRest::getSession()['aut']['documento_e'];
        if (isset($_POST["chave"]) && $_POST["chave"] == "update") {
            $id = $this->addInput("id", null, FALSE, null);
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
            if ($arq_a == "S") {
                $qArq->updateModel($model);
                $this->printReturn();
            } else {
                $this->getErrors("sem permissão");
            }
        } else if (isset($_POST["chave"]) && $_POST["chave"] == "delete") {
            $id = $this->addInput("id", null, FALSE, null);
            $qArq = new qArquivo();
            $model = $qArq->model($id);
                if($arq_e == "S"){
                         $qArq->deleteModel($model);
                         $this->printReturn();
                }else{
                        $this->getErrors("sem permissão");
                }
           
        } else {
            $this->getControllerAction();
        }
    }

    public function action_alteraModeloDocumento() {
        $modelo_i = controllerRest::getSession()['aut']['modelo_i'];
        $modelo_a = controllerRest::getSession()['aut']['modelo_a'];
        if (isset($_POST["modcod"])) {
            $qMod = new qModelo();
            $model = $qMod->model();
            $model["modcod"]["value"] = $_POST["modcod"];
            $model["moddesc"]["value"] = $_POST["moddesc"];
            if ($modelo_a == "S") {
                $qMod->updateModel($model);
                $this->printReturn();
            } else {
                $this->getErrors();
            }
        } else {
            $this->getControllerAction();
        }
    }

    public function action_excluiModeloDocumento() {
        $modelo_e = controllerRest::getSession()['aut']['modelo_e'];
        $id = $this->addInput("modcod");
        $qModex = new qModelo();
        $model = $qModex->model($id);
        if ($modelo_e == "S") {
            $qModex->deleteModel($model);
            $this->getControllerAction();
        } else {
            $this->getErrors();
            $this->getControllerAction();
        }
    }

    public function action_contabiliza_em_lote(){
        $idarq = $this->addInput("idarq");

        $qArq = new qArquivo();
        foreach ($idarq as $key => $value){
               $modeloarq = $qArq->model($value);
               $modeloarq["contabilizado"]["value"] = "S";
               $qArq->updateModel($modeloarq);
        }
    }
    
    //download de arquivos
    public function action_arq_down_zip() {
        $files = $this->addInput("files");
        $zip = new \ZipArchive();

        $filename = "/files/private/temp/download.zip";
        
         if (!is_dir(DIR_RAIZ . "files/private/temp/"))
                {
                    mkdir(DIR_RAIZ . "files/private/temp/",0777,true);
                }
                
        $pathZIP = DIR_RAIZ."files/private/temp/".rand(1000,9999).".zip";
        
        if ($zip->open($pathZIP, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) == false) {
                  
                  http_response_code(500);
                  return;
        }else{   

                foreach ($files as $value) {

                    if (!$zip->addFile(DIR_ARQUIVOS."/".$value,$value)) {
                        $zip->close();
                        http_response_code(501);
                        return;
                    }
                }
        }//fim else
        $zip->close();
        
        //marca baixado
        $idarq = $this->addInput("idarq");

        $qArq = new qArquivo();
        foreach ($idarq as $key => $value){
               $modeloarq = $qArq->model($value);
               $modeloarq["baixado"]["value"] = "S";
               $qArq->updateModel($modeloarq);
        }
        
        
        //$this->addData("zip", file_get_contents($pathZIP));
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"download.zip\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($pathZIP));
        echo file_get_contents($pathZIP);
        unlink($pathZIP);
        
    }

    public function action_arq_down() {
        //pega o id via Get
        $idarq = $this->addInput("idarq");
        $idUsuario = controllerRest::getSession()['aut']['id'];
        $contador = controllerRest::getSession()['aut']['contador'];

        if ($idUsuario != null) {
            $objarq = new qArquivo();
            $Marq = $objarq->model($idarq);
            if ($contador == "S") {
                $Marq["baixado"]["value"] = "S";
                $objarq->updateModel($Marq);
            } else {
                
            }
        }
        //faz o download
        $nomeArq = $Marq["caminho"]["value"];
        $empresa = $_GET["idempresa"];
        $caminho = DIR_EMPRESA;

        $file = DIR_EMPRESA . "/" . $nomeArq;
        $file = str_replace("\\", "/", $file);
        $file = str_replace("../", "/", $file);
        if (is_file($file)) {
            header("Content-Description: File Transfer");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($file));
            echo file_get_contents($file);
        } else {
            //envia p pag erro
            http_response_code(404);
        }
    }

//fim downloads

    public function action_desmarca_arq_contab() {
        //função que vai enviar
        $idUsuario = controllerRest::getSession()['aut']['id'];
        $contador = controllerRest::getSession()['aut']['contador'];
        $id = $this->addInput("id");
        $objArq = new qArquivo();
        $model = $objArq->model($id);
        if ($contador == "S") {
            $model["contabilizado"]["value"] = "N";
            $objArq->updateModel($model);
            $this->printReturn();
        } else {
            $this->getErrors();
            $this->printReturn();
        }
    }

    public function action_verificaContador() {
        $id_user = $this->addInput("id_user");
        if ($id_user != null) {
            $sqlcont = "select *from 3a_bancos.usuario where id = ?";
            $objUser = new qUsuario();
            $dados = $objUser->runPrepared($sqlcont, [$id_user])->fetch();
            $this->addData('dadosUser', $dados);
            $this->printReturn();
        } else {
            $this->addError();
        }
    }

    public function action_marca_arq_contab() {
        //função que vai enviar 
        $objArq = new qArquivo();
        $id = $this->addInput("id");

        $sqlUp = "update doc_3a.arquivo set contabilizado ='S' where id = ?";
        $objArq->runPrepared($sqlUp, [$id]);
        $this->printReturn();
    }

    public function action_logout() {
//        unset($_SESSION);
        self::unsetSession();
    }

}

//fim da classe

