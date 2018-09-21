<?php
	namespace files\lib;

	use application\libs\controllerRest;
	use application\libs\database;

	class oauth2 {

		static $Apis = array();
		static $Authorizations = array();

		static function addAuthorization($id_api, $uid, $account_id, $token_type, $access_token) {
			try {
				$idOnly = [$id_api];
				$params = [$id_api, $uid, $account_id, $token_type, $access_token];
				database::runPrepared("DELETE FROM oauth2 WHERE id_api = ?", $idOnly);
				database::runPrepared("INSERT INTO oauth2 (id_api,uid,account_id,token_type,access_token) VALUES (?, ?, ?, ?, ?)", $params);
				oauth2::listAuthorizations(true);
				return true;
			}
			catch (\Exception $e) {
				throw new \Exception("Não foi salvar conta de integração");
			}
		}

		static function delAuthorization($id_api) {
			try {
				$param = [$id_api];
				database::runPrepared("DELETE FROM oauth2 WHERE id_api = ?", $param);
				oauth2::listAuthorizations(true);
				return true;
			}
			catch (\Exception $e) {
				throw new \Exception("Não foi retirar conta de integração");
			}
		}

		static function getAuthorizarion($id_api) {
			oauth2::listAuthorizations();

			if (!isset(oauth2::$Apis[$id_api])) {
				throw new \Exception("API não encontrada");
			}

			if (!isset(oauth2::$Authorizations[$id_api])) {
				throw new \Exception("Conta não vinculada", 1);
			}

			return oauth2::$Authorizations[$id_api];
		}

		static function listApis($forceRefresh = false) {
			if (!$forceRefresh && isset(controllerRest::getSession()['oauth2']['apis']) && !empty(controllerRest::getSession()['oauth2']['apis'])) {
				oauth2::$Apis = controllerRest::getSession()['oauth2']['apis'];
				return true;
			}

			oauth2::$Apis = [];
			try {
				$result = database::runPrepared("SELECT id,nome,titulo,icone FROM oauth2_api");
				while (list($id, $nome, $titulo, $icone) = $result->fetch()) {
					oauth2::$Apis[$id] = [
						'id' => $id,
						'titulo' => $titulo,
						'icone' => $icone,
						'authorization' => false
					];
				}
				controllerRest::createSession('apis', oauth2::$Apis, ['oauth2']);
				return true;
			}
			catch (\Exception $e) {
				throw new \Exception("Não foi possivel listar as APIs");
			}
		}

		static function listAuthorizations($forceRefresh = false) {
			if (!oauth2::listApis($forceRefresh)) {
				return false;
			}

			if (!$forceRefresh && isset(controllerRest::getSession()['oauth2']['Authorizations']) && !empty(controllerRest::getSession()['oauth2']['Authorizations'])) {
				oauth2::$Authorizations = controllerRest::getSession()['oauth2']['Authorizations'];
				return true;
			}

			oauth2::$Authorizations = [];
			try {
				$result = database::runPrepared("SELECT id,id_api,uid,account_id,token_type,access_token FROM oauth2");
				while (list($id, $id_api, $uid, $account_id, $token_type, $access_token ) = $result->fetch()) {
					oauth2::$Authorizations[$id_api] = [
						'id' => $id,
						'id_api' => $id_api,
						'uid' => $uid,
						'account_id' => $account_id,
						'token_type' => $token_type,
						'access_token' => $access_token
					];
					if (isset(oauth2::$Apis[$id_api]['authorization'])) {
						oauth2::$Apis[$id_api]['authorization'] = true;
					}
				}

				controllerRest::createSession('apis', oauth2::$Apis, ['oauth2']);
				controllerRest::createSession('Authorizations', oauth2::$Authorizations, ['oauth2']);

				return true;
			}
			catch (\Exception $e) {
				throw new \Exception("Não foi possivel checar suas contas de integração");
			}
		}
	}
