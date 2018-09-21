<?php

	use files\lib\auth;
//    arquivos acessiveis para usuarios deslogados
	$geralDeslogado = ["index", "query", "error", "varsession"];

	$geralLogado = array_merge($geralDeslogado, ["autocomplete", "relatorios"]);

	if (!auth::isAuth()) {
		if (!$this->verificaArray($geralDeslogado, $module, $controller, $action, $opq)) {
			self::$errors[] = "Você precisa estar logado para acessar essa página<script>"
					."$(document).ready(function () {
						let btn = `<button onclick='insertLoginForm(this);' class='btn btn-primary btn-login' >Fazer login</button>`;
						$(btn).insertAfter($('.well').children().last());
					 });
					 let hasForm = false;
					 function insertLoginForm (elem) {
						if (hasForm) {
							return;
						}
						let form = `
							<div class='clearfix' ></div>
							<br>
							<div class='form-group col-md-8 col-md-offset-2 col-xs-12' >
								<input name='login' type='hidden'>
								<label class='text-primary' for='inpUser' title='Usuário:'>Usuário:</label>
								<input class='form-control' id='inpUser' name='inpUser' onkeydown='if (event.keyCode == 13) inpPass.focus()' type='text'>
								<br>
								<label class='text-primary' for='inpPass' title='Senha:'>Senha:</label>
								<input class='form-control' id='inpPass' name='inpPass' onkeydown='if (event.keyCode == 13) loginBtn.click();' type='password'>
								<button id='loginBtn' style='margin-top: 15px;' class='col-md-6 btn btn-primary' type='button' onclick='login(inpUser.value, inpPass.value);'>Entrar</button>
								<div class='clearfix'></div>
							</div>
							<div class='clearfix' ></div>`;
						hasForm = true;
						elem.setAttribute('disabled', 'disabled');
						$(form).insertAfter($('.well').children().last());
						inpUser.focus();
					 }"
					."</script>"
					."<style>@media (min-width: 991px) {.btn-login {margin-left: 4px;}}@media (max-width: 991px) {.btn {margin-top: 4px;}}</style>";
		}
	}
	else {
		if (!$this->verificaArray($geralLogado, $module, $controller, $action, $opq)) {
			self::$errors[] = "Você não tem permissão para acessar esta página";
		}
	}