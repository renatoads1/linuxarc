$(document).ready(function(){
    
});
function excluiModeloDocumento(row){
   let linha = $.staGridConfig.staGridgModelo.data.body[row];
    var dados = "modcod="+linha.modcod;
    $.ajax({  
                    url:url_raiz_empresa+"index/index/excluiModeloDocumento",
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

function onclikGrid(row){
    //pega as linhas selecionadas e carrega os dados dos inputs.
   let linha = $.staGridConfig.staGridgModelo.data.body[row];

    //$(".staGrid").staGridSelectedRows();
$("#footer").css("display","none");
  //console.log(linha);  
 $.staDialog({
 text: $("#frmAlteraModeloDocumento").html(),
 title: '-',
 // showTitle: false
 type: 'primary',
 open:true,
 buttons: {
  btOk: {
   text: 'Salvar',
   iconLeft: 'fa fa-refresh',
   action: () => alteraModeloDocumento(),
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
//carrega os dados no modal
    $(".panel-body #field_modcod").val(linha.modcod);
    $(".panel-body #dv_modcod").css("display", "none");
    $(".panel-body #field_moddesc").val(linha.moddesc);
//    $(".panel-body #field_garantia").val(linha.garantia);
//    $(".panel-body #field_vlr_mao_obra").val(linha.vlr_mao_obra);
//    $(".panel-body #field_vlr_visita").val(linha.vlr_visita);
//    $(".panel-body #field_hierarquia").val(linha.hierarquia);
//    $(".panel-body #field_origem").val(linha.origem); 

}
function alteraModeloDocumento(){
    
    var field_modcod =  $(".panel-body #field_modcod").val();
    var field_moddesc = $(".panel-body #field_moddesc").val();
//    var field_garantia = $(".panel-body #field_garantia").val();
//    var field_vlr_mao_obra = $(".panel-body #field_vlr_mao_obra").val();
//    var field_vlr_visita = $(".panel-body #field_vlr_visita").val();
//    var field_hierarquia = $(".panel-body #field_hierarquia").val();
//    var field_origem = $(".panel-body #field_origem").val();
       
    var dados = "modcod="+field_modcod+"&moddesc="+field_moddesc;
            //+
//            "&garantia="+field_garantia+
//            "&vlr_mao_obra="+field_vlr_mao_obra+
//            "&hierarquia="+field_hierarquia+
//            "&origem="+field_origem;
                $.ajax({  
                    url:url_raiz_empresa+"index/index/alteraModeloDocumento",
                    dataType: 'json',
                    type:'post',
                    data:dados,
                    success:function(s){
                       $.staDialog('close');
                       location.reload();
                    },
                    error:function(e){
                        $.staDialog('close');
                        location.reload();
                    }
                });  

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





