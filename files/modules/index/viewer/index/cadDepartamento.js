$(document).ready(function(){
    
});
//funcoes
function btn_limpa_form(frm){
    $( "#"+frm ).append($( "input" ).map(function(){
    $(this).val("");
  }).get().join( ", " ) );
}



function cadDepartamento(row){
    let linha = $.staGridConfig.staGridgDepartamento.data.body[row];
    var id = linha.id;
//    aqui
//$(".staGrid").staGridSelectedRows();
$("#footer").css("display","none");
  //console.log(linha);  
 $.staDialog({
 text: $("#frmcadDepartamento").html(),
 title: '-',
 // showTitle: false
 type: 'primary',
 open:true,
 buttons: {
  btOk: {
   text: 'Salvar',
   action: function(){
        var departamento = $(".panel-body #field_departamento").val();
        var dados = "id="+id+"&departamento="+departamento+"&chave=insere";

        $.ajax({  
            url:url_raiz_empresa+"index/index/cadDepartamento",
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
//altera departamento
function altDepartamento(row){
    let linha = $.staGridConfig.staGridgDepartamento.data.body[row];
    var id = linha.id;

 $.staDialog({
 text: $("#frmcadDepartamento").html(),
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
        var departamento = $(".panel-body #field_departamento").val();
        var dados = "id="+id+"&departamento="+departamento+"&chave=update";

        $.ajax({  
            url:url_raiz_empresa+"index/index/cadDepartamento",
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


}
//excluir linha
function delDepartamento(row){
    let linha = $.staGridConfig.staGridgDepartamento.data.body[row];
    
    var dados = "id="+linha.id+"&departamento="+linha.departamento+"&chave=delete";

        $.ajax({  
            url:url_raiz_empresa+"index/index/cadDepartamento",
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
    
}//fim função deletar

function preenche(row){
    let linha = $.staGridConfig.staGridgDepartamento.data.body[row];
//    var id = linha.id;
    $(".panel-body #field_id").val(linha.id);
    $(".panel-body #field_departamento").val(linha.departamento);

}



