//feitoUpload = false;
$(document).ready(function(){
    $("#field_Empresa").parent().parent().css("display","none");
});
function uploadok(){
    setTimeout($.staDialog,3000,"close");
     $("#btn_cadDocumento").attr("class","btn btn-primary");
}
function initUpload(){
   $.staDialog({
		title: 'Enviando arquivo',
		text:`
			<div id="uploadStatusLabel" style="text-align: center;height: 0;" >Enviando arquivo (0%)</div>
			<div class="progress" >
				<div class="progress-bar progress-bar-success" id="uploadProgressBar" style="width: 0%;" ></div>
			</div>
			<span id="uploadMsgStatus" >Aguarde enquanto o arquivo é enviado e processado.</span><br>
			<small id="uploadMsgSubtitle" >Esse procedimento pode demorar dependendo do número de documentos que deverão ser escaneados.</small>`,
		type: 'default',
		buttons: {
			btClose: {
				text: 'Ok',
				action: function () {
					$.staDialog('close');
				},
				type: 'default disabled'
			}
		},
		open: true
	});
    return false;
}
function sendScannedDocsCbError(addError, XHR, exception) {
    
	uploadProgressBar.style.width = '100%';
    uploadProgressBar.style.height = '300px';
	uploadStatusLabel.innerHTML = 'Erro';
	uploadMsgSubtitle.innerHTML = ``;
	uploadProgressBar.classList.remove('active');
	uploadProgressBar.classList.remove('progress-bar-success');
	uploadProgressBar.classList.add('progress-bar-danger');
	strDialogbtn_btClose.classList.remove('disabled');
	if (XHR != null && exception != null) {// upload
		uploadMsgStatus.innerHTML = `Ocorreu um erro durante o envio do arquivo.`;
	}
	if (XHR == null && exception != null) {// PHP
		uploadMsgStatus.innerHTML = `Ocorreu um erro ao processar o arquivo.`;
	}
	else if (addError != null) {// addError
		benchIt(addError.data.count, addError.data.elapsed);
		let errorCodes = {
			'40': 'nenhum código QR foi encontrado na imagem',
			'50': 'o código lido não aponta para nenhum documento existente',
			'60': 'o código lido aponta para um documento com status final',
			'70': 'não foi possível copiar o arquivo para a pasta de destino',
			'80': 'não foi possível salvar o arquivo no banco de dados'
		};
		if (addError.errors.length > 0) {
			uploadMsgStatus.innerHTML = '';
			for (let err of addError.errors) {
				let elem = JSON.parse(err.strError), name = elem.id;
				uploadMsgStatus.innerHTML += `O arquivo ${name} não pode ser salvo pois ${errorCodes[err.code]}.<br>`;
			}
			uploadMsgStatus.innerHTML += `Verifique os documentos listados acima e tente novamente.`;
		}
	}
    
}

function sendScannedDocsCbProgress(per) {
	uploadProgressBar.style.width = per + '%';
	uploadStatusLabel.innerHTML = 'Enviando arquivo (' + per + '%)';
	if (per == 100) {
		uploadProgressBar.classList.add('progress-bar-striped');
		uploadProgressBar.classList.add('active');
		uploadStatusLabel.innerHTML = 'Procesando arquivo';
	}
}
//funcoes
function btn_limpa_form(frm){
    $( "#"+frm ).append($( "input" ).map(function(){
    $(this).val("");
  }).get().join( ", " ) );
}

function btn_save_form(frm){
    var action = frm.slice(3);
    var inputs = $("#"+frm).serializeArray();
    var arquivocaminho = field_caminho.files[0].name;
    var tam =  inputs.length;
    inputs[tam+1] = {'name':'caminho','value':arquivocaminho};

  $.staAjaxJSON(url_raiz_empresa+"index/index/"+action,{dados:inputs},{
            fncSuccess: data => location.href = url_raiz_empresa+"index/index/"+action,
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
                fncFailed: function(e){
                    //alert(e.statusText);
                    location.reload();
                }
        });

}





