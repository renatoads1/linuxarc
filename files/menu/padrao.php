<?php
	namespace files\menu;

	use application\libs\menu;
	use files\lib\auth;

	class padrao extends menu {

		public function __construct(){
//			se o usuário estiver logado
            
            if(auth::isAuth()){				
                //menu dropdown
                $this->addmenu('home',URL_RAIZ_EMPRESA.'index/index/home', 'Home', NULL, NULL,'fa fa-fw fa-home fa-lg');
				$a = 0;
                $this->addmenu('cadastro', null, 'Cadastros', NULL, NULL,'fa fa-fw fa-plus fa-lg');
				$this->addmenu('cadastro'.$a++, URL_RAIZ_EMPRESA.'index/index/cadDocumento', 'Documentos', 'cadastro');
				$this->addmenu('cadastro'.$a++, URL_RAIZ_EMPRESA.'index/index/cadEntidade', 'Entidades', 'cadastro');
				$this->addmenu('cadastro'.$a++, URL_RAIZ_EMPRESA.'index/index/cadModeloDocumento', 'Modelo Documentos', 'cadastro');
				$this->addmenu('cadastro'.$a++, URL_RAIZ_EMPRESA.'index/index/cadTipoEntidade', 'Tipo de Entidade', 'cadastro');
                $this->addmenu('cadastro'.$a++, URL_RAIZ_EMPRESA.'index/index/cadDepartamento', 'Departamento', 'cadastro');
                
                $b = 0;
                $this->addmenu('alteracao', null, 'Alteracao/manutenção', NULL, NULL,'fa fa-fw fa-pencil fa-lg');
				$this->addmenu('alteracao'.$b++, URL_RAIZ_EMPRESA.'index/index/home', 'Documentos', 'alteracao');
                $this->addmenu('alteracao'.$b++, URL_RAIZ_EMPRESA.'index/index/home', 'Entidades', 'alteracao');
                
                $c = 0;
                $this->addmenu('consultas', null, 'Consulta', NULL, NULL,'fa fa-fw fa-search fa-lg');
				$this->addmenu('consultas'.$c++, URL_RAIZ_EMPRESA.'index/index/home', 'Documentos/Status', 'consultas');
                $this->addmenu('consultas'.$c++, URL_RAIZ_EMPRESA.'index/index/home', 'Documentos', 'consultas');
                $this->addmenu('consultas'.$c++, URL_RAIZ_EMPRESA.'index/index/home', 'Entidades', 'consultas');
                
                $this->addmenu('index', URL_RAIZ_EMPRESA.'index/index/index', 'Início', NULL, NULL, 'fa fa-fw fa-home');
			}
			if(MODE_DEBUG && ((auth::isAuth() && auth::getInfoUser()['id'] == -100) || AMBIENTE === 0)) {
				$this->addmenu('index', URL_RAIZ_EMPRESA."varsession", "VAR_SESSION", null, null, "fa fa-fw fa-fw fa-bug", null, null, true);
			}
			if (auth::isAuth()){
				$this->addmenu('index', null, "Sair - <i>".auth::getInfoUser()["nome"]."</i>", null, "logout();", "fa fa-fw fa-fw fa-sign-out fa-flip-horizontal");
			}
		}
	}
