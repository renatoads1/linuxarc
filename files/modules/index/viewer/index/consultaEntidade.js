$(document).ready(function(){
    
});
//funcoes
function onclikGrid(){
    //pega as linhas selecionadas e carrega os dados dos inputs.
    let linha = $(".staGrid").staGridSelectedRows();
//console.log(linha[0].id);
//$("#fild_id").val()=linha[0].id;
//$(document).attr("#fild_id","","renato");
//$("#field_id").val(linha[0].id);
//$("#field_nome").val(linha[0].nome);
//$("#field_Sobrenome").val(linha[0].sobrenome);
//$("#field_CPF").val(linha[0].cpf);
//$("#field_Telefone").val(linha[0].telefone);
//$("#field_Email").val(linha[0].email);
    console.log(linha);
}

function btn_limpa_arquiv(frm){
    $( "#"+frm ).append($( "input" ).map(function(){
    $(this).val("");
  }).get().join( ", " ) );
}

function btn_login_arquiv(frm){
    let field_senha = $("#field_senha").val();
    let field_usuario = $("#field_usuario").val();
    let field_clienteOpt = $("#field_cliente option:selected").html();
    let field_clienteId = $("#field_cliente option:selected").val();
    let inputs = {senha:field_senha,
        usuario:field_usuario,
        clienteId:field_clienteId,
        clienteOpt:field_clienteOpt};
        $.staAjaxJSON(url_raiz_empresa+"index/index/loginArquiv",{dados:inputs},{
            fncSuccess: data => location.href = url_raiz_empresa+"index/index/novapagina",
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
				}),fncFailed: (xhr, ajaxOptions, thrownError) => $.staDialog({
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
        });
        /*$.staDialog({
  title: 'a',
  text: 'b',
  type: 'success',
  open: true,
  buttons:{
   btnOk: {
     text:'omg',
     type: 'danger',
     action: ()=>alert('qwe')
   } 
  }*/
}





