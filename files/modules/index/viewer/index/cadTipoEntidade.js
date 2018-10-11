//modal
function incluiTPEntidade(row){
    //pega as linhas selecionadas e carrega os dados dos inputs.
   let linha = $.staGridConfig.staGridgTipo_entidade.data.body[row];

    //$(".staGrid").staGridSelectedRows();
//$("#footer").css("display","none");
  //console.log(linha);  
 $.staDialog({
 text: $("#frmcadTipoEntidade").html(),
 title: '-',
 // showTitle: false
 type: 'primary',
 open:true,
 buttons: {
//  btOk: {
//   text: 'Salvar',
//   iconLeft: 'fa fa-refresh',
//   action: () => alteraModeloDocumento(),
//   type: 'success',
//   iconRight: 'fa fa-save'
//  },
//   btClose: {
//   text: 'Fechar',
//   iconLeft: 'fa fa-close',
//   action: () => $.staDialog('close'),
//   type: 'danger'
////   ,
////   iconRight: 'fa fa-truck'
//  }
 }
});
//carrega os dados no modal
//    $(".panel-body #field_modcod").val(linha.modcod);
//    $(".panel-body #dv_modcod").css("display", "none");
//    $(".panel-body #field_moddesc").val(linha.moddesc);
//    $(".panel-body #field_garantia").val(linha.garantia);
//    $(".panel-body #field_vlr_mao_obra").val(linha.vlr_mao_obra);
//    $(".panel-body #field_vlr_visita").val(linha.vlr_visita);
//    $(".panel-body #field_hierarquia").val(linha.hierarquia);
//    $(".panel-body #field_origem").val(linha.origem); 

}
//fim modal
//funcoes
function cadTPEntidmodal(frm){
//        var action = frm.slice(3);
        var tpentidade = $(".panel-body #field_tpentidade").val();
        var descricao = $(".panel-body #field_descricao").val();
        var dados = "tpentidade="+tpentidade+"&descricao="+descricao+"&chave=insert";
  
        $.ajax({  
            url:url_raiz_empresa+"index/index/cadTipoEntidade",
            dataType: 'json',
            type:'post',
            data:dados,
            success:function(s){
              location.reload();
            },
            error:function(e){
                location.reload();
            }
        });
}
function editTPEntidmodal(row){
    let linha = $.staGridConfig.staGridgTipo_entidade.data.body[row];
//    aqui
//$(".staGrid").staGridSelectedRows();
$("#footer").css("display","none");
  //console.log(linha);  
 $.staDialog({
 text: $("#frmcadTipoEntidade").html(),
 title: '-',
 // showTitle: false
 type: 'primary',
 open:true,
 buttons: {
  btOk: {
   text: 'Salvar',
//   iconLeft: 'fa fa-refresh',
//      action: () => alteraModeloDocumento(),
   action: function(){
        var tpentidade = $(".panel-body #field_tpentidade").val();
        var descricao = $(".panel-body #field_descricao").val();
        var dados = "tpentidade="+tpentidade+"&descricao="+descricao+"&chave=update";

        $.ajax({  
            url:url_raiz_empresa+"index/index/cadTipoEntidade",
            dataType: 'json',
            type:'post',
            data:dados,
            success:function(s){
              location.reload();
            },
            error:function(e){
                location.reload();
            }
        });
   },
   type: 'primary',
   iconRight: 'fa fa-save'
  },
   btClose: {
   text: 'Fechar',
   iconLeft: 'fa fa-close',
   action: () => $.staDialog('close'),
   type: 'danger'
//   ,
//   iconRight: 'fa fa-truck'
  }
 }
});

//carrega os inputs para edição
$(".panel-body #field_tpentidade").val(linha.tpentidade);
$(".panel-body #field_descricao").val(linha.descricao);

}
function excluirTPEntidmodal(row){
    var linha = $.staGridConfig.staGridgTipo_entidade.data.body[row];
    var dados = "tpentidade="+linha.tpentidade+"&descricao="+linha.descricao+"&chave=delete";

        $.ajax({  
            url:url_raiz_empresa+"index/index/cadTipoEntidade",
            dataType: 'json',
            type:'post',
            data:dados,
            success:function(s){
              location.reload();
            },
            error:function(e){
                location.reload();
            }
        });
    
}
function btn_limpa_form(frm){
    $( "#"+frm ).append($( "input" ).map(function(){
    $(this).val("");
  }).get().join( ", " ) );
}

function btn_save_form(frm){
    var action = frm.slice(3);
    var inputs = $("#"+frm).serializeArray();
  
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

}





