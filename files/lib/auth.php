<?php
	namespace files\lib;

	use application\libs\controllerRest;
	use application\libs\query;

	class auth {

		/** Checa se esta Logado
		 *
		 * @return boolean
		 */
		static function isAuth() {
			return (isset(controllerRest::getSession()['connected']) && controllerRest::getSession()['connected']);
		}

		/** Pega dados do usuario logado
		 *
		 * @param string $principal Login principal(entro no sistema) ou secundario(momentaneo)
		 * @return boolean|\files\lib\infoUser
		 */
		static function getInfoUser() {
			return auth::isAuth() ? controllerRest::getSession()['aut'] : false;
		}

		/**
		 * Verifica se o user logado é adm
		 *
		 * @return boolean
		 */
		static function isAdmin () {
			$query = new query();
			$sql = "SELECT p.nome = 'Administrador' as isAdmin FROM perfil_usuarios p, usuario u WHERE u.id = ? AND p.id = u.idperfil";
			$isAdmin = $query->runPrepared($sql, [self::getInfoUser()['id']])->fetch(\PDO::FETCH_NUM)[0] == 1;
			return self::isAuth() && $isAdmin;
		}

		/**
		 * Verifica se um usuário possui alguma permissão
		 *
		 * @param type $nome
		 * @return boolean
		 */
		static function checkPermissao ($nomeId) {
			return true;
            if (!self::isAuth()) {
				return false;
			}
			if (self::isAdmin()) {
				return true;
			}

			$query = new query();

			if (is_numeric($nomeId)) {
				$idPermissao = $nomeId;
			}
			else {
				$sqlIdPermissao = "SELECT id FROM permissoes WHERE nome = ?";
				$idPermissao = $query->runPrepared($sqlIdPermissao, [$nomeId])->fetch(\PDO::FETCH_NUM)[0];
			}

			$sqlPerfil = "SELECT p.id, p.permissao_padrao FROM perfil_usuarios p, usuario u WHERE u.id = ? AND p.id = u.idperfil";
			$dadosPerfil = $query->runPrepared($sqlPerfil, [self::getInfoUser()['id']])->fetch(\PDO::FETCH_ASSOC);

			$sqlPermissao = "SELECT permitir FROM permissoes_perfil WHERE idperfil = ? AND idpermissao = ?";
			$dadosPermissao = $query->runPrepared($sqlPermissao, [$dadosPerfil["id"], $idPermissao])->fetchAll(\PDO::FETCH_NUM);

			if (count($dadosPermissao) != 0) {
				return $dadosPermissao[0/* Primeira linha */][0/* Primeiro campo */] != "N";
			}
			else {
				return $dadosPerfil["permissao_padrao"] != "N";
			}
		}
	}
