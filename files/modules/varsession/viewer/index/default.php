<?php

	use files\viewerModel\padrao;
	use \files\menu\padrao as menu_padrao;

$viewer = new padrao();
	$viewer->addLogoDefault();

	$menu = new menu_padrao();
	$viewer->menu = $menu->menu;

	if (!isset($_GET['origin'])) {
		$session = $_SESSION;
		$enableControllers = true;
	}
	else {
		switch ($_GET['origin']) {
			case 'session':
				$session = $_SESSION;
				$enableControllers = true;
				break;
			case 'constants':
				$aux = get_defined_constants(true)['user'];
				$session = array();
				$enableControllers = false;
				foreach ($aux as $key => $value) {
					if (!startsWith($key, "MYSQLSTA_")) {
						$session[$key] = $value;
					}
				}
				break;
			default: exit;
		}
	}
	$root = "";
	$monitorando = isset($_GET['monitor']) && $_GET['monitor'] == "true";

	foreach ($toAction['path'] as $key => $value) {
		if (!($key == 0 && $value == "root")) {
			if (isset($session[$value])) {
				$session = $session[$value];
				$root .= "[\"{$value}\"]";
			}
			else {
				$session = "Caminho inválido!";
				break;
			}
		}
	}

	$vardump = vardump($session);
	$md5 = md5($vardump);

	$js = "
		let md5 = '{$md5}';
		function sendAjax (campo, tipo) {
			$.staAjaxJSON(\"#\",
				{campo: $('#'+campo).val(),tipo: tipo},
				{method: \"POST\",
				fncSuccess: function(data){ location.reload(); },
				fncFailed: function(xhr, ajaxOptions, thrownError){ console.log(xhr.status, thrownError); }
			});
		}
		function use (txt) {
			var aux = '{$root}';
			txt.forEach(function (e) {
				aux += '[\"' + e + '\"]';
			});
			$('input[type=text]').each(function () {
				$(this).val($(this).val() + aux);
			});
		}
		function compress (elem) {
			let div = elem.nextElementSibling.nextElementSibling.nextElementSibling;
			$(elem).toggleClass('fa-compress fa-expand');
			if (elem.title == 'Escoder') {
				elem.title = 'Mostrar';
				$(div).show(400);
			} else {
				elem.title = 'Escoder';
				$(div).hide(400);
			}
		}
		function addToPath () {
			let path = '', sep = '', finalPath;
			pathList.querySelectorAll('li:not(.active) > a').forEach(function (e, i) {
				path += sep+e.innerHTML;
				sep = ',';
			});
			finalPath = prompt('Digite o caminho \"root\" separando os índices por vírgulas', path);
			location.href = addGetParam('path', finalPath);
		}
		function setPath (finalPath, name) {
			let path = getGetParam('path') || 'root';
			finalPath.forEach(function (e, i) {
				path += ','+e;
			});
			if (name != '')
				path += ','+name;
			location.href = addGetParam('path', path);
		}
		function monitorar () {
			let isMonitoring = getGetParam('monitor') == 'true', url = addGetParam('monitor', !isMonitoring);
			localStorage.removeItem('md5_varsession');
			location.href = url;
		}
		function changeOrigin (elem) {
			location.href = addGetParam('origin', elem.getAttribute('data-value'));
		}";

	$jsOnLoad = "
		$('input[type=text]').val('');
		$('#pathList > li:not(.active)').click(function () {
			let aux = this, path = '', sep = '';
			while (aux != null) {
				path = aux.childNodes[0].innerHTML + sep + path;
				sep = ',';
				aux = aux.previousElementSibling;
			}
			location.href = addGetParam('path', path);
		});
		let html = '\
			<div>\
				<h1 style=\"font-size: 12px;white-space: nowrap;margin-top: 0;\" >Selecione a fonte para o varsession:</h1>\
				<ul style=\"padding-left: 20px;margin: 0;\" >\
					<li onclick=\"changeOrigin(this);\" data-value=\"session\" >\$_SESSION</li>\
					<li onclick=\"changeOrigin(this);\" data-value=\"constants\" >get_defined_constants()</li>\
				</ul>\
			</div>';
		$(selectSource).popover({
			content: html,
			html: true,
			placement: 'top'
		});";

	if ($monitorando) {
		$jsOnLoad .= "
			let md5LS = localStorage.getItem('md5_varsession');
			localStorage.setItem('md5_varsession', md5);
			if (md5LS != md5 && md5LS != null) {
				alert();
			}
			setTimeout(function () {
				location.reload();
			}, 1000);";
	}

	$style = "
		input, button:not(.btn-xs) {
			height: 34px !important;
		}
		#session:before {
			content: 'Clique no ícone ao lado do nome do campo para coloca-lo nos inputs acima. F5 para limpar os campos.';
			position: relative;
			background-color: #d1d1d1;
			top: -10px;
			left: -10px;
			padding: 3px 7px 3px 15px;
			display: block;
			width: calc(100% + 20px);
		}
		i {
			position: relative;
			top: 2px;
		}
		i.fade {
			transition: opacity .4s;
			opacity: .5;
		}
		i.fade:hover {
			opacity: 1;
		}
		@media (max-width: 991px) {
			.well > div:not(:first-child) {
				margin-top: 12px;
			}
		}
		pre {
			line-height: 16px;
			padding-left: 0;
		}
		footer {
			position: fixed;
			bottom: 0;
			left: 0;
			width: 100vw;
			height: 32px;
			background-color: #ccc;
			padding: 5px;
		}
		footer .breadcrumb {
			background-color: rgba(0,0,0,.15);
			padding: 1px 6px;
		}
		footer * {
			color: #0072bc;
		}
		.popover-content li {
			cursor: pointer;
		}
		.well {
			margin-top: 0px;
			margin-bottom: 6px;
			padding: 7.5px 0;
		}
		pre > i.fa {
			display: none;
		}";

	$out = "";

	if ($enableControllers) {
		$out .= "
			<div class=\"well col-xs-12 half-padding\" >
				<div class=\"col-md-5 col-sm-12 col-xs-12 half-padding\" >
					<div class=\"input-group col-md-12\" >
						<span class=\"input-group-addon\" >unset(\$_SESSION</span>
						<input type=\"text\" class=\"form-control\" placeholder=\"['campo']\" id=\"campo\" onkeydown=\"function (event) { if (event.keyCode == 13) { sendAjax('campo','unset'); } }\"/>
						<span class=\"input-group-addon\" >);</span>
						<span class=\"input-group-btn\">
							<button class=\"btn btn-default\" type=\"button\" onclick=\"sendAjax('campo','unset');\" >Executar</button>
						</span>
					</div>
				</div>
				<div class=\"col-md-5 col-sm-12 col-xs-12 half-padding\" >
					<div class=\"input-group col-md-12\" >
						<span class=\"input-group-addon\" >SET: \$_SESSION</span>
						<input type=\"text\" class=\"form-control\" placeholder=\"['campo'] = ''\" id=\"campo1\" onkeydown=\"function (event) { if (event.keyCode == 13) { sendAjax('campo1','set'); } }\"/>
						<span class=\"input-group-addon\" >;</span>
						<span class=\"input-group-btn\">
							<button class=\"btn btn-default\" type=\"button\" onclick=\"sendAjax('campo1','set');\" >Executar</button>
						</span>
					</div>
				</div>
				<div class=\"col-md-2 col-sm-6 col-xs-12 half-padding\" >
					<input type=\"hidden\" value=\"unsetAll\" id=\"campo3\" />
					<div class=\"input-group col-xs-12\" >
						<span class=\"input-group-btn\" >
							<button class=\"btn btn-default\" type=\"button\" onclick=\"sendAjax('campo3','unsetAll');\" style=\"width: 100%;\" >session_unset();</button>
						</span>
						<span class=\"input-group-btn\" >
							<button class=\"btn btn-default\" type=\"button\" onclick=\"monitorar();\" style=\"width: 100%;\" >
								<i class=\"fa fa-fw fa-refresh".($monitorando ? " fa-spin" : "")."\" ></i>
							</button>
						</span>
					</div>
				</div>
			</div>
			<div class=\"col-xs-12\" style=\"margin: 12px 0px 36px 0;padding: 0px;\" >";
	}
	else {
		$out .= "
			<div class=\"col-xs-12\" style=\"margin: 0px;margin-bottom: 36px;padding: 0px;\" >";
	}
	$out .= "
			<pre id=\"session\" style=\"font-family: Consolas,monospace !important;\" >".$vardump."</pre>
		</div>
		<footer>
			<ol class=\"breadcrumb\" id=\"pathList\" style=\"width: calc(100% - 30px);float: left;\" >
				<li><a href=\"#\">".implode("</a></li><li><a href=\"#\">", $toAction['path'])."</a></li>
				<li class=\"active\" title=\"Adicionar\" onclick=\"addToPath();\" ><i class=\"fa fa-fw fa-plus\" style=\"top: 1px;\" ></i></li>
			</ol>
			<div style=\"width: 25px;padding-bottom: 2px;background-color: rgba(0,0,0,.15);border-radius: 4px;text-align: center;float: right;cursor: pointer;\" id=\"selectSource\" >
				<i class=\"fa fa-fw fa-cogs\" title=\"Selecionar origem\" ></i>
			</div>
		</footer>";

	$viewer->body->style = $style;
	$viewer->body->js = $js;
	$viewer->body->jsOnLoad = $jsOnLoad;
	$viewer->content = $out;
	$viewer->addClasses("scrollable full");
	$viewer->run();

	function vardump($var, $level = 0, $keys = []) {
		$type = gettype($var);
		$return = "";
		if ($type == "array") {
			$eye = "";
			if (count($keys) > 0) {
				$eye = "<i class='fa fa-fw fa-eye fade' title='Exibir apenas isso' onclick='setPath(".json_encode($keys).", \"\")' ></i>";
			}
			$return .= "array(".count($var).") {<i class='fa fa-fw fa-compress text-primary fade' title='Esconder' onclick='compress(this)' ></i>{$eye}<br><div style='margin-left: 25px;border-left: 1px solid lightgray;' >";
			foreach ($var as $key => $value) {
				$aux = $keys;
				$aux[] = $key;
				$aspas = (gettype($key) == 'integer') ? '' : '"';
				$eye = "";
				if (gettype($value) != 'array') {
					$eye = "<i class='fa fa-fw fa-eye fade' title='Exibir apenas isso' onclick='setPath(".json_encode($keys).", \"{$key}\")' ></i>";
				}
				$return .= "[{$aspas}{$key}{$aspas}]=> <i class='fa fa-fw fa-clipboard text-primary fade' title='Usar' onclick='use(".json_encode($aux).")' ></i>{$eye}<br>".vardump($var[$key], $level++, $aux);
			}
			$return .= "</div>}<br>";
		} elseif ($type == "boolean") {
			$return .= "bool(".( ($var) ? "true" : "false" ).")<br>";
		}
		elseif ($type == "string") {
			$return .= "string(".strlen($var).") \"{$var}\"<br>";
		}
		elseif ($type == "integer") {
			$return .= "int({$var})<br>";
		}
		elseif ($type == "double") {
			$return .= "double({$var})<br>";
		}
		else {
			$return .= $type."<br>";
		}
		return $return;
	}

	function startsWith($haystack, $needle) {
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}