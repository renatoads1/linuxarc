<?php
	namespace files\viewerModel;

	use application\libs\menuNovo;
	use application\libs\controllerRest;
	use files\lib\auth;

	class padrao extends html {

		public $menu = array();
		public $menuLateral = array();
		public $titleMenuLateral = "";
		private $pathName;
		private $fileName;

		public function __construct($url = '/') {
			parent::__construct($url);
			try {
				throw new \Exception("");
			}
			catch (\Exception $e) {
				$this->pathName = dirname($e->getTrace()[0]["file"]); // Gambiarra para pegar o path da viewer
				$this->fileName = basename($e->getTrace()[0]["file"]);
				$this->fileName = preg_replace("/.php$/", "", $this->fileName);
				$this->body->js = '';
				$this->body->jsOnload = '';
				$this->body->style = '';
			}
		}

		static function simpleViewer($title, $content, $js = null, $jsOnLoad = null) {
			$viewer = new padrao();
			$viewer->addLogoDefault();

			$menu = new \files\menu\padrao();
			$viewer->menu = $menu->menu;

			$viewer->document->title = $title;
			$out = "<h3>{$title}</h3>".$content;

			$viewer->content = "<div class=\"row\" style=\"overflow:hidden;\">{$out}</div>";
			!is_null($js) && ($viewer->body->js = $js);
			!is_null($jsOnLoad) && ($viewer->body->jsOnLoad = $jsOnLoad);

			$viewer->run();
		}

		public function addLogoDefault() {
			$this->outrosParams['linkLogo'] = URL_RAIZ;
			$this->outrosParams['textLogo'] = "<div class=\"logo-default no-select\" ></div><div class=\"name-default\" style=\"font-size: 20px;\" >".NOME_PROJETO."</div>";
		}

		public function custom() {
			$infoUser = auth::getInfoUser();
			if (empty($this->document->title)) {
				$this->document->title = "".NOME_PROJETO." ".SUFIXO_PROJETO;
			}
			$this->body->addParam('data-target', 'navbar-scroll');
			$this->body->addParam('data-spy', 'scroll');
			$menu = new menuNovo();
			$navLateral = '';
			if (!isset($_GET['navLeftHide']) or $_GET['navLeftHide'] == 'no') {
				if (!empty($this->menuLateral)) {
					$navLateral .= $menu->getMenuLateral($this->menuLateral, null, $this->titleMenuLateral);
					$this->addClasses('has-sidebar');
				}
			}

			$nav = '<nav id="navbar-scroll" class="navbar navbar-inverse navbar-fixed-top no-select" role="navigation">';
			$nav .= "<script>menuTop.itens = ".json_encode($this->menu).";</script>";
			$nav .= '<div class="container" >';
			$nav .= '<div class="navbar-header">';
			$nav .= '<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse" style="width: 30px;height: 30px;top: -3px;right: -9px;padding: 0px 3px;">';
			$nav .= '<span class="sr-only">Menu</span>';
			$nav .= '<span class="icon-bar"></span>';
			$nav .= '<span class="icon-bar"></span>';
			$nav .= '<span class="icon-bar"></span>';
			$nav .= '</button>';
			$nav .= '<div class="btn-group pull-right pwa-only">';
			$nav .= '<button style="margin-right: 0;" class="btn btn-sm btn-primary navbar-toggle" onclick="history.back();">';
			$nav .= '<i class="fa fa-fw fa-arrow-left"></i>';
			$nav .= '</button>';
			$nav .= '<button style="margin-right: 0;" class="btn btn-sm btn-primary navbar-toggle" onclick="location.reload();">';
			$nav .= '<i class="fa fa-fw fa-undo fa-flip-horizontal"></i>';
			$nav .= '</button>';
			$nav .= '</div>';
			$nav .= isset($this->outrosParams['linkLogo']) ? '<a class="navbar-brand" href="'.$this->outrosParams['linkLogo'].'" >' : "";
			if (isset($this->outrosParams['urlLogo'])) {
				$nav .= '<img class="logo-default" src="'.$this->outrosParams['urlLogo'].'"';
				$nav .= isset($this->outrosParams['descLogo']) ? ' alt="'.$this->outrosParams['descLogo'].'"' : "";
				$nav .= ' />';
			}
			elseif (isset($this->outrosParams['textLogo'])) {
				$nav .= $this->outrosParams['textLogo'];
			}
			$nav .= isset($this->outrosParams['linkLogo']) ? '</a>' : "";
			$nav .= '</div>';
			// $menu	 = new menuNovo();
			if ($infoUser != false) {
				$nav .= '<div class="btn-group pull-right hidden-xs" id="dvUsuarioConectado" onclick="showDadosUsuario();" ><button type="button" class="btn btn-primary btn-xs" ><i class="fa fa-fw fa-user"></i></button> <button type="button" class="btn btn-info btn-xs" ><i class="fa fa-caret-down"></i></button></div>';
			}
			$nav .= '<div class="collapse navbar-collapse navbar-ex1-collapse" style="float: right;">'.$menu->getMenu($this->menu).'</div>';
			$nav .= '</div>';
			$nav .= '</nav>';
			$nav .= "<div class=\"submenuTopNovo\" id=\"menuTop\" style=\"display: none;\" >";
			$nav .= "<div class=\"submenuTopNovo-title\">";
			$nav .= "<div class=\"submenuTopNovo-title-text no-select\"></div>";
			$nav .= "<div class=\"submenuTopNovo-title-button pull-right\"><button class=\"btn btn-danger btn-sm\" onclick=\"menuTop.hideMenu();\"><span class=\"fa fa-times\"></span> Fechar menu</button></div>";
			$nav .= "</div>";
			$nav .= "<div class=\"submenuTopNovo-body\">";
			//Titles
			$nav .= "<ul class=\"submenuTopNovo-body-titles\"></ul>";
			//Itens
			$nav .= "<ul class=\"submenuTopNovo-body-list\"></ul>";
			$nav .= "</div>";

			$nav .= "</div>";
			$nav .= "</div>";
			$nav .= '<div class="'.$this->getClasses().'" >';

			if (MODE_DEBUG && AMBIENTE == 0) {
				if (isset($_GET['showRuler'])) {
					controllerRest::getSession()['showRuler'] = $_GET['showRuler'];
				}
				if (isset(controllerRest::getSession()['showRuler']) && controllerRest::getSession()['showRuler'] == 'yes') {
					$nav = '
						<div style="position: fixed;background-color: white;color: #33F;border-radius: 15px;border: 1px solid #666;bottom: 10px;right: 10px;z-index: 9999;width: 30px;padding: 4px 0;text-align: center;font-weight: bold;opacity: 0.5;pointer-events: none;" >
							<div class="visible-xs" >XS</div>
							<div class="visible-sm" >SM</div>
							<div class="visible-md" >MD</div>
							<div class="visible-lg" >LG</div>
							<div class="visible-xl" >XL</div>
							<div class="visible-print" >
								<i class="fa fa-print fa-fw" ></i>
							</div>
						</div>'.$nav;
				}
			}

			$script = "
				$('body').on('click', function (e) {//Esconde o popover se clicar fora dele
					$('[data-toggle=\"popover\"]').each(function () {
						if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
							$(this).popover('hide');
						}
					});
					var aux = e.target, achouSubMenuTop = false, achouMenuMobile = false;
					do {
						if (aux.classList.contains('submenuTop') || aux.classList.contains('navbar-ex1-collapse')) {
							achouSubMenuTop = true;
							break;
						}
						if (aux.tagName == 'NAV' && aux.parentElement.tagName == 'BODY') {
							achouMenuMobile = true;
							break;
						}
						aux = aux.parentElement;
					} while (aux != null);
					if ($('.submenuTop').css('display') == 'block' && !achouSubMenuTop) {
						$('.submenuTop').slideToggle(300);
						$('.submenu-item').removeClass('active');
					}
				});
			";

			$bottom = "<script>$(document).ready(function(){ $('[data-toggle=popover]').popover(); $('button:not(.btn)').addClass('btn btn-primary');".$script." });</script>";
			$bottom .= "<script src=\"".URL_SITE."files\\public\\js\\todos.js?v=".REVISION_SISTEMA."\" ></script>";

			if (is_dir($this->pathName)) {
				if (is_file($this->pathName."/".$this->fileName.".js")) {
					$this->body->js .= "\r\n;".file_get_contents($this->pathName."/".$this->fileName.".js").";\r\n";
				}
				if (is_file($this->pathName."/".$this->fileName.".onload.js")) {
					$this->body->jsOnLoad .= "\r\n;".file_get_contents($this->pathName."/".$this->fileName.".onload.js").";\r\n";
				}
				if (is_file($this->pathName."/".$this->fileName.".css")) {
					$this->body->style .= "\r\n".file_get_contents($this->pathName."/".$this->fileName.".css")."\r\n";
				}
			}

			$this->body->style = str_replace(["\t", "\r", "\n"], "", $this->body->style);

			$this->body->content = $nav.$navLateral.$this->content."</div>".$bottom;
		}
	}
