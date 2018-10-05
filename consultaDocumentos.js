//globais
$(document).ready(function () {
    usr = new Object();
    usr.id = id_userx;
    usr.nome = name_user;
//checa dados usuários
    checa_user_contador();
    usr.contador = sessionStorage.getItem("contador");

});
function alteraclassesta(){
//   var sta = $.staGridConfig.staGridgArquivo.data.body;
//        $(sta).each(function(index,element){
//                if(element.contabilizado=='N'){
//                    console.log($(this));
//                }else{
//                    console.log($(this));
//                }
//          });
}

function dow_arq(nomearq) {
    var nome = nomearq.parents('td').attr('data-value');
    var idarq = nomearq.parents('tr').attr('data-fieldindexvalue');

    window.open(url_raiz_empresa + "index/index/arq_down?arq=" + nome + "&idarq=" + idarq, "_blank");
    setTimeout(function () {
        $('.staGrid').staGrid({forceRefresh: true});
    }, 3000);
}
function desmarcarContabilizado(row) {
    //update contabilizado no banco para sim N 
    var idarq2 = $.staGridConfig.staGridgArquivo.data.body[row];
    dados = {id: idarq2.id};
//    if (usr.contador == 'S')
//    {
        $.ajax({
            url: url_raiz_empresa + "index/index/desmarca_arq_contab",
            data: dados,
            dataType: 'json',
            beforeSend: function () {
            },
            success: function (a) {
                setTimeout(function () {
                    $('.staGrid').staGrid({forceRefresh: true});
                }, 1000);
            },
            error: function (e) {
                console.log('erro:' + e);
            }
        });
//    } else {
//        alert('Não autorizado');
//    }
}
function callbackGrid(data) {
    //console.log(data);
    var cont = data.data.body.length;
    //alert(cont);
//recebe todos os elementos tr
    trs = $("tbody tr");
    for (i = 0; i < cont; i++) {

        if (data.data.body[i]['contabilizado'] == 'N')
        {
            //eq fala que o indice do arrai vai ser o que vc mandar
            trs.eq(i).find('span').eq(1).attr("class", "	fa fa-close fa-2x");
            trs.eq(i).find('span').eq(1).attr("style", "color:red");
            trs.eq(i).find('span').eq(1).attr("title", " ");
        } else {
            trs.eq(i).find('span').eq(1).attr("class", "	fa fa-check fa-2x");
            trs.eq(i).find('span').eq(1).attr("style", "color:green");
            trs.eq(i).find('span').eq(1).attr("title", "Clique para Desmarcar");
        }
    }

}
function checa_user_contador() {
    dados = {id_user: usr.id};
    $.ajax({
        url: url_raiz_empresa + "index/index/verificaContador",
        data: dados,
        dataType: 'json',
        beforeSend: function () {
        },
        success: function (a) {
            if (a.isError == false)
            {
                //console.log(a.data.dadosUser.contador);
                sessionStorage.setItem("contador", a.data.dadosUser.contador);
            } else {
                alert('erro');
            }
        },
        error: function (e) {
            console.log('erro' + e);
        }
    });//fim ajax   
}

function baixa_pagina() {
//  enviar para action zipa download como um array de nomes de img   
    //console.log($.staGridConfig.staGridgArquivo.data);
    var conta = $.staGridConfig.staGridgArquivo.data.body.length;
    //alert(cont);
//recebe todos os elementos tr
    trs = $("tbody tr");
    imgs = [];
    
    for (i = 0; i < conta; i++) {
        imgs[i] = $.staGridConfig.staGridgArquivo.data.body[i]['caminho'];
    }
    for (a = 0; a < conta; a++) {
        cod = $.staGridConfig.staGridgArquivo.data.body[a]['id'];
        desmarcarContabilizados(cod);
    }
    //console.log(cod);
    //faz o download
    windowOpenWithPost(url_raiz_empresa + "index/index/arq_down_zip", {files: imgs});
        setTimeout(function () {
        $('.staGrid').staGrid({forceRefresh: true});
    }, 1000);
}
function windowOpenWithPost(url, data) {
    let mapForm = document.createElement("form");
    mapForm.target = "NEW_WINDOW";
    mapForm.method = "POST";
    mapForm.action = url;
    mapForm.style.display = "none";

    Object.forEach(data, (e, i) => {
        let arr, sufix = "";
        if (typeof e == "object" && Array.isArray(e)) {
            arr = e;
            sufix = "[]";
        } else {
            arr = [e];
        }
        arr.forEach(elem => {
            let mapInput = document.createElement("input");
            mapInput.type = "text";
            mapInput.name = i + sufix;
            mapInput.value = elem;
            mapForm.appendChild(mapInput);
        });
    });
    document.body.appendChild(mapForm);
    mapForm.submit();
    mapForm.focus();
}
function alteraDocumento(row){
    let linha = $.staGridConfig.staGridgArquivo.data.body[row];
 
$.staDialog({
 text: $("#frmalteraDocumento").html(),
 title: '-',
 // showTitle: false
 type: 'primary',
 open:true,
 buttons: {
  btOk: {
   text: 'Salvar',
   iconLeft: 'fa fa-refresh',
   action: function(){
       salvaAlateracaoDocumento();
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
function carregafrmalteraDocumento(row){
    let linha = $.staGridConfig.staGridgArquivo.data.body[row];
    $(".panel-body #field_id").val(linha.id);
    $(".panel-body #field_documento_de_quem").val(linha.documento_de_quem);
    $(".panel-body #field_modelo").val(linha.modelo);
    $(".panel-body #field_numero_doc").val(linha.numero_doc);
    $(".panel-body #field_obs").val(linha.obs);
    $(".panel-body #field_usuario_qarquivou").val(linha.usuario_qarquivou);
    $(".panel-body #field_caminho").val(linha.caminho);
    $(".panel-body #field_valor").val(linha.valor);
    $(".panel-body #field_iddepartamento").val(linha.iddepartamento);
    $(".panel-body #field_dtemissao").val(linha.dtemissao);
}
function salvaAlateracaoDocumento(){
    var id = $(".panel-body #field_id").val();
    var documento_de_quem = $(".panel-body #field_documento_de_quem").val();
    var modelo = $(".panel-body #field_modelo").val();
    var numero_doc = $(".panel-body #field_numero_doc").val();
    var obs = $(".panel-body #field_obs").val();
    var usuario_qarquivou = $(".panel-body #field_usuario_qarquivou").val();
    var caminho = $(".panel-body #field_caminho").val();
    var valor = $(".panel-body #field_valor").val();
    var iddepartamento = $(".panel-body #field_iddepartamento").val();
    var dtemissao = $(".panel-body #field_dtemissao").val();
    
    var dados ="documento_de_quem="+documento_de_quem
    +"&id="+id
    +"&modelo="+modelo
    +"&numero_doc="+numero_doc
    +"&obs="+obs
    +"&usuario_qarquivou="+usuario_qarquivou
    +"&caminho="+caminho
    +"&valor="+valor
    +"&iddepartamento="+iddepartamento
    +"&dtemissao="+dtemissao+"&chave=update";
    
      $.ajax({  
            url:url_raiz_empresa+"index/index/alteraDocumento",
            dataType: 'json',
            type:'post',
            data:dados,
            success:function(s){
                console.log(s);
              location.reload();
            },
            error:function(e){
                console.log(e);
                location.reload();
            }
        });
    
    
    
}
function delAlateracaoDocumento(row){
     let linha = $.staGridConfig.staGridgArquivo.data.body[row];
    
    var dados ="id="+linha.id+"&chave=delete";
    
      $.ajax({  
            url:url_raiz_empresa+"index/index/alteraDocumento",
            dataType: 'json',
            type:'post',
            data:dados,
            success:function(s){
                console.log(s);
              location.reload();
            },
            error:function(e){
                console.log(e);
                location.reload();
            }
        });
    
    
    
}
