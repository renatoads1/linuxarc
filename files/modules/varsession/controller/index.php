<?php namespace files\modules\varsession\controller;

use application\libs\controllerRest;
use files\lib\auth;

class index extends controllerRest {
	public function action_default() {
		//Se estiver com o MODE_DEBUG por causa do ambiente e não por causa ?mode_debug=yes na URL
		//para evitar que alguém descubra isso e mude o nível de acesso ou alguma permissão
		if (!auth::isAuth() || (AMBIENTE != LOCAL && auth::getInfoUser()->id != -100)) {//Se não está logado como suporte e o AMBIENTE não é local
			return new controllerRest('error', 'index', 'error404');
		}//Se não estiver no mode debug, fala que a página não existe

		if ((!isset(controllerRest::getSession()['mode_debug']) || !controllerRest::getSession()['mode_debug']) && isset($_POST['campo']) && strlen($_POST['campo']) > 0) {
			if ($_POST['tipo'] == 'unset') {
				eval("unset(\$_SESSION" . $_POST['campo'] . ");");
			} elseif ($_POST['tipo'] == 'set') {
				eval("\$_SESSION" . $_POST['campo'] . ";");
			} elseif ($_POST['tipo'] == 'unsetAll') {
				foreach ($_SESSION as $key => $val) {
					unset($_SESSION[$key]);
				}
			}
			$this->printReturn(true);
		}
		$toAction = array();
		$toAction['path'] = explode(",", $this->addInput("path", '/^([a-zA-Z\d_]+,?)*$/', false, 'root'));

		$this->toAction = $toAction;
		$this->getControllerAction();
	}
}
