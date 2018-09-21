
/* global url_raiz, id_user, nome_user, dvUsuarioConectado, menuTop */

function login(user, pass) {
	$.staAjaxJSON(
		url_raiz + 'index/index/login', {
			user,
			pass
		},
		{
			fncSuccess: data => location.href = data.data.href,
			fncError: data => $.staDialog({
					'title': 'Erro',
					'text': data.strError,
					'type': 'danger',
					'buttons': {
						btClose: {
							text: 'OK',
							action: () => $.staDialog('close'),
							type: 'default'
						}
					},
					'showTitle': false,
					'open': true
				}),
			fncFailed: (xhr, ajaxOptions, thrownError) => $.staDialog({
					'title': 'Erro',
					'text': 'Não foi possível entrar em contato com o servidor',
					'type': 'danger',
					'buttons': {
						btClose: {
							text: 'OK',
							action: () => $.staDialog('close'),
							type: 'default'
						}
					},
					'showTitle': false,
					'open': true
				})
		}
	);
}

function logout() {
	$.staAjaxJSON(
		url_raiz + 'index/index/logout', {},
		{
			fncSuccess: data => location.href = data.data.href,
			fncError: data => $.staDialog({
					'title': 'Erro',
					'text': data.strError,
					'type': 'danger',
					'buttons': {
						btClose: {
							text: 'OK',
							action: () => $.staDialog('close'),
							type: 'default'
						}
					},
					'showTitle': false,
					'open': true
				}),
			fncFailed: (xhr, ajaxOptions, thrownError) => $.staDialog({
					'title': 'Erro',
					'text': 'Não foi possível entrar em contato com o servidor',
					'type': 'danger',
					'buttons': {
						btClose: {
							text: 'OK',
							action: () => $.staDialog('close'),
							type: 'default'
						}
					},
					'showTitle': false,
					'open': true
				})
		}
	);
}

function showDadosUsuario() {
	$(dvUsuarioConectado).popover({
		title: 'Usuário conectado',
		html: true,
		placement: 'bottom',
		content: `
			<p class="text-primary">#${id_user} - ${nome_user}</p>
			<br />
			<button class="btn btn-danger btn-block btn-xs" onclick="logout();" ><i class="fa fa-times"></i> Sair</button>`,
		trigger: 'focus',
		template: `
			<div class="popover" role="tooltip" style="min-width: 200px;" >
				<div class="arrow"></div>
				<h3 class="popover-title"></h3>
				<div class="popover-content"></div>
			</div>`
	}).popover('show');
}

menuTop.showMenu = (id) => {
	let list;

	list = `<ul class='nav menuTopList' >`;
	Object.forEach(menuTop.itens[id].child, (e, i) => {
		if (e.type == 'divider') {
			list += `<li id='liMenu_${id}_${i}' class='divider' ><hr style='margin: 3px 0px;border-width: 2px;' ></li>`;
			return;
		}
		let icon = '',
			href = '#';
		if (e.icon && e.icon.length > 0) {
			icon = `<i class='${e.icon}'></i> `;
		}
		if (e.href && e.href.length > 0) {
			href = e.href;
		}
		list += `
			<li id='liMenu_${id}_${i}' onclick='$(liMenu_${id}).popover("hide");' >
				<a href='${href}' >${icon} ${e.text}</a>
			</li>`;
	});
	list += '</ul>';

	$('#liMenu_' + id).popover({
		html: true,
		placement: 'bottom',
		content: list,
		trigger: 'focus',
		template: `
			<div class="popover menuTopPopover" role="tooltip" >
				<div class="popover-content" style="padding: 0;" ></div>
			</div>`,
		container: 'body'
	}).popover('show');
};

/**
 * Roda um ajax para CRUD
 *
 * @param {string} action Action que será chamada
 * @param {array} label Verbo da ação [no infinitivo, no passado]
 * @param {Object} data Parâmetros para o POST
 * @param {string} type Nome do que está sendo alterado
 * @returns {undefined}
 */
function ajaxQuickCRUD(action, label, data, type) {
	let name = type.split("").map((m, i) => i == 0 ? m.toUpperCase() : m).join("");
	$.staAjaxJSON(
		url_raiz + 'listagem/' + type + '/' + action, data, {
			fncSuccess: data => {
				msgDialog(name + ' ' + label[1] + ' com sucesso.', 'default');
				$('.staGrid').staGrid({
					forceRefresh: true
				});
			},
			fncError: data => msgDialog('Não foi possível ' + label[0] + ' o ' + type + '.<br>' + data.strError, 'danger'),
			fncFailed: (xhr, ajaxOptions, thrownError) => msgDialog('Não foi possível entrar em contato com o servidor.', 'danger')
		}
	);
}

function msgDialog(msg, type) {
	$.staDialog({
		'title': 'Erro',
		'text': msg,
		'type': type,
		'buttons': {
			btClose: {
				text: 'OK',
				action: () => $.staDialog('close'),
				type: 'default'
			}
		},
		'showTitle': false,
		'open': true
	});
}

/**
 * Mostra um staDialog de confirmação
 *
 * @param {string} title Título do staDialog
 * @param {string} html Corpo do staDialog
 * @param {function} callback Função de callback para o botão OK
 * @returns {undefined}
 */
function promptDialog(title, html, callback) {
	$.staDialog({
		title: title,
		text: html,
		type: 'default',
		buttons: {
			btOk: {
				text: 'OK',
				action: () => {
					callback();
					$.staDialog('close');
				},
				type: 'default'
			},
			btClose: {
				text: 'Cancelar',
				action: () => $.staDialog('close'),
				type: 'default'
			}
		},
		open: true,
		showTitle: true
	});
}

/**
 * Verifica se apenas uma linhas está selecionada e em caso positivo, a retorna
 *
 * @param {boolean} multiple Default: false
 * @returns {checkSelectedRows.row|Boolean}
 */
function checkSelectedRows(multiple = false) {
	let row = $('.staGrid').staGridSelectedRows();
	if (row.length == 0 || (!multiple && row.length > 1)) {
		msgDialog('Selecione ' + (row.length > 1 ? 'apenas' : '') + ' uma linha', 'danger');
		return [];
	}
	else {
		return row;
}
}


class PermissoesProtocolo {

	static loadPermissoes () {
		let dataLocal = '';
		if (localStorage) {
			let users = JSON.parse(localStorage.getItem(id_empresa+'_permissao_user'));
			if (!users) {
				users = {};
			}
			let keys = Object.keys(users);
			if (users[id_user] == undefined || !users[id_user]) {
				users[id_user] = true;
			}
			keys.forEach(function (i) {
				if (i != id_user && users[i]) {
					users[i] = false;
					localStorage.removeItem(id_empresa+'_permissao_'+i);
				}
			});
			localStorage.setItem(id_empresa+'_permissao_user', JSON.stringify(users));
			dataLocal = JSON.parse(localStorage.getItem(id_empresa+'_permissao_' + id_user));
			if (dataLocal) {
				let now = new Date();
				let expires = new Date(dataLocal.expires);
				if (dataLocal != null && expires.getTime() > now.getTime()) {
					return dataLocal.value;
				} else {
					dataLocal = null;
				}
			}
			if (dataLocal == null) {
				$.ajax({
					url: url_raiz+"index/index/load_permissoes",
					dataType: 'json',
					type: "POST",
					data: {},
					async: false,
					success: function (data) {
						let now = new Date();
						let expires = new Date(now.getTime() + (5 * 24 * 60 * 60 * 1000)); //Expira em 5 dias
						localStorage.setItem(id_empresa+'_permissao_' + id_user, JSON.stringify({value: data.data, expires: expires}));
						dataLocal = data.data;
					},
					error: function (data) {
						console.log(data);
					}
				});
				return dataLocal;

			}
		}
		return {};
	}

	static getPermissao (nome, returnType) {
		let permissoes = this.loadPermissoes();
		if(id_user == -100){
			return true;
		}
		if (returnType == 'bool')
			return permissoes[nome] == 'S';
		else
			return permissoes[nome];
	}

	static checkPermissao (nome) {
		if (!this.getPermissao(nome, 'bool')) {
			$.staMessage({
				type: 'warning',
				title: 'Sem permissão',
				icon: 'fa-ban',
				message: 'Você não tem permissão para fazer isso.',
				action: 'show',
				timeout: 5000
			});
			throw new Error('abort operation');
		}
		return true;
	}

}
