$(document).ready(function(){
    
    url_atual = window.location.href;
        if(url_atual=="http://localhost/novoarquivamento/3a/index/index/index"){
           console.log(url_atual); 
           //$("#navbar-scroll").css("display","none");
        }else{
           //$("#navbar-scroll").css("display","inline");
        }
   

});
//funcoes
function btn_limpa_arquiv(frm){
    $( "#"+frm ).append($( "input" ).map(function(){
    $(this).val("");
  }).get().join( ", " ) );
    //alert('btn_limpa_arquiv'+frm);
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
            fncSuccess: data => location.href = url_raiz_empresa+"index/index/home",
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





