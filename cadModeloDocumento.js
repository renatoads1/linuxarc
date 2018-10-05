$(document).ready(function(){
    
});
//funcoes
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
//modal

function cadModeloDocumento(row){
//let linha = $.staGridConfig.staGridgModelo.data.body[row];
//console.log(linha.modcod);
//console.log(linha.moddesc);
  //console.log(linha);
 
 $.staDialog({
 text: $("#frmcadModeloDocumento").html(),
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
var moddesc = $(".panel-body #field_moddesc").val();
var modcod = $(".panel-body #field_modcod").val();
var dados = "moddesc="+moddesc+"&modcod="+modcod+"&chave=insert";
        
        $.ajax({  
            url:url_raiz_empresa+"index/index/cadModeloDocumento",
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
//$(".panel-body #field_tpentidade").val(linha.tpentidade);
//$(".panel-body #field_descricao").val(linha.descricao);

}
//altera
function altModeloDocumento(row){
let linha = $.staGridConfig.staGridgModelo.data.body[row];
var modcod = linha.modcod;
 
 $.staDialog({
 text: $("#frmcadModeloDocumento").html(),
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
var moddesc = $(".panel-body #field_moddesc").val();
var dados = "moddesc="+moddesc+"&modcod="+modcod+"&chave=update";

        $.ajax({  
            url:url_raiz_empresa+"index/index/cadModeloDocumento",
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
   type: 'success',
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
//$(".panel-body #field_tpentidade").val(linha.tpentidade);
//$(".panel-body #field_descricao").val(linha.descricao);

}
function deleteModeloDocumento(row){
    var linha = $.staGridConfig.staGridgModelo.data.body[row];
    var dados = "modcod="+linha.modcod+"&chave=delete";

        $.ajax({  
            url:url_raiz_empresa+"index/index/cadModeloDocumento",
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

function preenche(row){
   var linha = $.staGridConfig.staGridgModelo.data.body[row];
  $(".panel-body #field_moddesc").val(linha.moddesc);
  $(".panel-body #field_modcod").val(linha.modcod);

}



