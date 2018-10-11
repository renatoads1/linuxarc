var keymap = {
	DOWN: 40,
	UP: 38,
	LEFT: 37,
	RIGHT: 39,

	END: 35,
	BEGIN: 36,

	BACK_TAB: 8,
	TAB: 9,
	SH_TAB: 16,
	ENTER: 13,
	ESC: 27,
	SPACE: 32,
	DEL: 46,

	A: 65,
	B: 66,
	C: 67,
	D: 68,
	E: 69,
	F: 70,
	G: 71,
	H: 72,
	I: 73,
	J: 74,
	K: 75,
	L: 76,
	M: 77,
	N: 78,
	O: 79,
	P: 80,
	Q: 81,
	R: 82,
	S: 83,
	T: 84,
	U: 85,
	V: 86,
	W: 87,
	X: 88,
	Y: 89,
	Z: 90,
	
	TNUM_0: 96,
	TNUM_1: 97,
	TNUM_2: 98,
	TNUM_3: 99,
	TNUM_4: 100,
	TNUM_5: 101,
	TNUM_6: 102,
	TNUM_7: 103,
	TNUM_8: 104,
	TNUM_9: 105,
		
	NUM_0: 48,
	NUM_1: 49,
	NUM_2: 50,
	NUM_3: 51,
	NUM_4: 52,
	NUM_5: 53,
	NUM_6: 54,
	NUM_7: 55,
	NUM_8: 56,
	NUM_9: 57,	
	
	TMINUS: 109,
	MINUS: 189,
	PLUS: 107,
	EQUAL: 187,
	TBAR: 111,
	BAR:193,
	ASTERISK: 106,
	DOT: 190,
	TDOT: 194,
	TCOMMA: 110,
	COMMA: 188,
	
	CTRL: 17,
	ALT: 18,
	SHIFT: 16,
	
	F1: 112,
	F2: 113,
	F3: 114,
	F4: 115,
	F5: 116,
	F6: 117,
	F7: 118,
	F8: 119,
	F9: 120,
	F10: 121,
	F11: 122,
	F12: 123
};
var atalhos = {}; 
var keystrokes = {};
var wgCalc = {
		current: "",//wgCalc.current
		expressao: "",//wgCalc.expressao
		operador: "",//wgCalc.operador
		showError: function(strError){
			wgCalc.btnC();
			wgCalc.expressao = strError;
		},
		calcExpressao: function(){
			if(wgCalc.expressao=="")
				return 0;
			
			return eval( wgCalc.expressao );
		},
		calcView: function(){//wgCalc.calcView
			if(wgCalc.current=="")
				return wgCalc.calcExpressao();
			
			return parseFloat(wgCalc.current);			
		},
		getCurrent: function(){//wgCalc.getCurrent
			if(wgCalc.current=="")
				return 0;

			var regExp = new RegExp('^(([-])?[0-9.]*)[.]([0-9]*)|([0-9]*)$');
			var value = wgCalc.current+"";
			var res = value.match(regExp);
			
			if(typeof res[1]!="undefined" && typeof res[2]!="undefined"){//12.1
				wgCalc.current = res[1].replace(/\./g,'')+'.'+res[2];
			} else if(typeof res[3]!="undefined"){//12
				wgCalc.current = res[3]+"";
			}		
			
			
			return parseFloat(wgCalc.current);

		},
		btnBackspace: function(){
			var str = wgCalc.current;
			if(str!="")
				wgCalc.current = str.substr(0, str.length-1);
			
			wgCalc.setVisualizacao();
		},
		btnInsert: function(number){//wgCalc.btnInsert
			wgCalc.current += number+"";
			wgCalc.setVisualizacao();
		},
		btnCE: function(){//wgCalc.btnCE
			wgCalc.current = "";	
			wgCalc.setVisualizacao();
		},
		btnC: function(){//wgCalc.btnC
			wgCalc.current = "";
			wgCalc.expressao = "";
			wgCalc.operador = "";
			wgCalc.setVisualizacao();
		},
		addExpressao: function(operador){//wgCalc.addExpressao
			var expressao = wgCalc.expressao;
			if(wgCalc.current==""){
				if(expressao==''){
					wgCalc.expressao = wgCalc.getCurrent();
					wgCalc.operador = operador;
					wgCalc.current = "";
				} else {
					wgCalc.expressao =  wgCalc.expressao;
					wgCalc.operador = operador;
					wgCalc.current = "";
				}
			} else {
				if(expressao==''){
					wgCalc.expressao = wgCalc.getCurrent();
					wgCalc.operador = operador;
					wgCalc.current = "";
				} else {
					wgCalc.expressao =  wgCalc.expressao+ wgCalc.operador + wgCalc.getCurrent();
					wgCalc.operador = operador;
					wgCalc.current = "";
				}
			}
			wgCalc.setVisualizacao();
		},
		btnABS: function(){
			var value = wgCalc.getCurrent();
			
			wgCalc.current = 0 - value;
			wgCalc.setVisualizacao();
		},
		btnSQRT: function(){
			var value = wgCalc.getCurrent();
			wgCalc.current = Math.sqrt(value);
			wgCalc.setVisualizacao();
		},
		btnDiv: function(){
			wgCalc.addExpressao('/');	
		},
		btnPercento: function(){
			var value1 = wgCalc.calcExpressao();//100%
			var value2 = wgCalc.getCurrent();//x%
			var value = 0;
			if(value1!=0)
				value = value1*value2/100;
			
			console.log(value1,value2,value);
			wgCalc.current = ""+value;
			wgCalc.setVisualizacao();
		},
		btnMult: function(){
			wgCalc.addExpressao('*');		
		},
		btnReciproc: function(){
			var value = wgCalc.getCurrent();
			if(value==0){				
				wgCalc.showError("Impossivel dividir por 0!");
			} else {
				wgCalc.current = 1/value;
			}
			wgCalc.setVisualizacao();
		},
		btnSub: function(){
			wgCalc.addExpressao('-');
		},
		btnEQUAL: function(){
			wgCalc.addExpressao("");
			
			var value = wgCalc.calcExpressao();		
			wgCalc.expressao = "";
			wgCalc.current = value;
			wgCalc.setVisualizacao();
		},	
		btnSum: function(){
			wgCalc.addExpressao('+');	
		},	
		btnSign: function(){
			
			
			wgCalc.current = wgCalc.getCurrent()+".";
			wgCalc.setVisualizacao();			
		},
		setVisualizacao: function(){//wgCalc.setVisualizacao()
			if(wgCalc.current==""){
				var display1 = "";
				var display2 = wgCalc.expressao+ wgCalc.operador;
			} else {
				if(wgCalc.expressao=="")
					wgCalc.expressao = 0;
				
				var display1 = wgCalc.expressao + wgCalc.operador;
				var display2 = wgCalc.current;
			}
			$('#wgCalcDisplay1').text(display1); 
			$('#wgCalcDisplay2').text(display2);
		},
		open: function(elemento){//wgCalc.open
			var isModal = (typeof elemento=='undefined');
			var html = "";
			$.addAtalho("TNUM_0",function(){ wgCalc.btnInsert(0); });
			$.addAtalho("TNUM_1",function(){ wgCalc.btnInsert(1); });
			$.addAtalho("TNUM_2",function(){ wgCalc.btnInsert(2); });
			$.addAtalho("TNUM_3",function(){ wgCalc.btnInsert(3); });
			$.addAtalho("TNUM_4",function(){ wgCalc.btnInsert(4); });
			$.addAtalho("TNUM_5",function(){ wgCalc.btnInsert(5); });
			$.addAtalho("TNUM_6",function(){ wgCalc.btnInsert(6); });
			$.addAtalho("TNUM_7",function(){ wgCalc.btnInsert(7); });
			$.addAtalho("TNUM_8",function(){ wgCalc.btnInsert(8); });
			$.addAtalho("TNUM_9",function(){ wgCalc.btnInsert(9); });
			
			$.addAtalho("NUM_0",function(){ wgCalc.btnInsert(0); });
			$.addAtalho("NUM_1",function(){ wgCalc.btnInsert(1); });
			$.addAtalho("NUM_2",function(){ wgCalc.btnInsert(2); });
			$.addAtalho("NUM_3",function(){ wgCalc.btnInsert(3); });
			$.addAtalho("NUM_4",function(){ wgCalc.btnInsert(4); });
			$.addAtalho("NUM_5",function(){ wgCalc.btnInsert(5); });
			$.addAtalho("NUM_6",function(){ wgCalc.btnInsert(6); });
			$.addAtalho("NUM_7",function(){ wgCalc.btnInsert(7); });
			$.addAtalho("NUM_8",function(){ wgCalc.btnInsert(8); });
			$.addAtalho("NUM_9",function(){ wgCalc.btnInsert(9); });
			//Operadores:
			$.addAtalho("PLUS",			function(){ wgCalc.btnSum(); });// +
			$.addAtalho("SHIFT+EQUAL",	function(){ wgCalc.btnSum(); });// +
			$.addAtalho("TMINUS",		function(){ wgCalc.btnSub(); });// -
			$.addAtalho("MINUS",		function(){ wgCalc.btnSub(); });// -
			$.addAtalho("TBAR",			function(){ wgCalc.btnDiv(); });// /
			$.addAtalho("BAR",			function(){ wgCalc.btnDiv(); });// /
			$.addAtalho("ASTERISK",		function(){ wgCalc.btnMult(); });// *
			$.addAtalho("SHIFT+NUM_8",	function(){ wgCalc.btnMult(); });// *			
			$.addAtalho("SHIFT+NUM_5",	function(){ wgCalc.btnPercento(); });// %
			$.addAtalho("TDOT",			function(){ wgCalc.btnSign(); });// .
			$.addAtalho("DOT",			function(){ wgCalc.btnSign(); });// .
			$.addAtalho("TCOMMA",		function(){ wgCalc.btnSign(); });// ,
			$.addAtalho("COMMA",		function(){ wgCalc.btnSign(); });// ,
			$.addAtalho("EQUAL",		function(){ wgCalc.btnEQUAL(); });// =
			$.addAtalho("ENTER",		function(){ wgCalc.btnEQUAL(); });// =
			$.addAtalho("BACK_TAB",		function(){ wgCalc.btnBackspace(); });
			$.addAtalho("ESC",			function(){ wgCalc.btnC(); });
			$.addAtalho("DEL",			function(){ wgCalc.btnCE(); });
			
			html += "<table style=\"width:300px;margin:0 auto;\">";
			html += "<tr>";
			html += "<td colspan=\"5\" style=\"text-align:right;\">";
			html += "<div class=\"alert alert-info\" style=\"width:100%;\"><span id=\"wgCalcDisplay1\" style=\"font-size:1em\"></span><br />";
			html += "<span id=\"wgCalcDisplay2\" style=\"font-size:1.5em\">0</span></div>";
			html += "</td>";
			html += "</tr><tr>";
			html += "<td style=\"width:60px;\"><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnBackspace();\" style=\"width:100%;height:60px;\">&larr;</div></td>";
			html += "<td style=\"width:60px;\"><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnCE();\" style=\"width:100%;height:60px;\">CE</div></td>";
			html += "<td style=\"width:60px;\"><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnC();\" style=\"width:100%;height:60px;\">C</div></td>";
			html += "<td style=\"width:60px;\"><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnABS();\" style=\"width:100%;height:60px;\">&#8723;</div></td>";
			html += "<td style=\"width:60px;\"><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnSQRT();\" style=\"width:100%;height:60px;\">&radic;</div></td>";
			html += "</tr><tr>";
			html += "<td><div class=\"btn btn-default btn-lg\" onclick=\"wgCalc.btnInsert(7);\" style=\"width:100%;height:60px;\">7</div></td>";
			html += "<td><div class=\"btn btn-default btn-lg\" onclick=\"wgCalc.btnInsert(8);\" style=\"width:100%;height:60px;\">8</div></td>";
			html += "<td><div class=\"btn btn-default btn-lg\" onclick=\"wgCalc.btnInsert(9);\" style=\"width:100%;height:60px;\">9</div></td>";
			html += "<td><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnDiv();\" style=\"width:100%;height:60px;\">/</div></td>";
			html += "<td><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnPercento();\" style=\"width:100%;height:60px;\">%</div></td>";
			html += "</tr><tr>";
			html += "<td><div class=\"btn btn-default btn-lg\" onclick=\"wgCalc.btnInsert(4);\" style=\"width:100%;height:60px;\">4</div></td>";
			html += "<td><div class=\"btn btn-default btn-lg\" onclick=\"wgCalc.btnInsert(5);\" style=\"width:100%;height:60px;\">5</div></td>";
			html += "<td><div class=\"btn btn-default btn-lg\" onclick=\"wgCalc.btnInsert(6);\" style=\"width:100%;height:60px;\">6</div></td>";
			html += "<td><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnMult();\" style=\"width:100%;height:60px;\">*</div></td>";
			html += "<td><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnReciproc();\" style=\"width:100%;height:60px;\">1/x</div></td>";
			html += "</tr><tr>";
			html += "<td><div class=\"btn btn-default btn-lg\" onclick=\"wgCalc.btnInsert(1);\" style=\"width:100%;height:60px;\">1</div></td>";
			html += "<td><div class=\"btn btn-default btn-lg\" onclick=\"wgCalc.btnInsert(2);\" style=\"width:100%;height:60px;\">2</div></td>";
			html += "<td><div class=\"btn btn-default btn-lg\" onclick=\"wgCalc.btnInsert(3);\" style=\"width:100%;height:60px;\">3</div></td>";
			html += "<td><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnSub();\" style=\"width:100%;height:60px;\">-</div></td>";
			html += "<td rowspan=\"2\"><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnEQUAL();\" style=\"width:100%;height:120px;\">=</div></td>";
			html += "</tr><tr>";
			html += "<td colspan=\"2\"><div class=\"btn btn-default btn-lg\" onclick=\"wgCalc.btnInsert(0);\" style=\"width:100%;height:60px;\">0</div></td>";
			html += "<td><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnSign();\" style=\"width:100%;height:60px;\">,</div></td>";
			html += "<td><div class=\"btn btn-primary btn-lg\" onclick=\"wgCalc.btnSum();\" style=\"width:100%;height:60px;\">+</div></td>";
			html += "</tr></table>";
			
			$.staDialog({
				'title': "Calculadora",
				'text': html,
				'type': "default",
				'buttons': {
					btClose:{
						iconLeft: "fa fa-times",
						text: "Fechar",
						action: "$.staDialog('close')",
						type: "danger"
					}
				},
				'showTitle': true,
				'open': true
			});
			
		}
		
};
var wgEmail = {//Inicio wgEmail
	idMsg: null,
	url: "query/wgEmail",
	isModal: true,
	idDiv: null,
	data: {},
	open: function(id,idDiv){//Inicio wgEmail.open
		var dataSend = {wgEmail_formatReturn: 'yes',wgEmail_acao: 'open'};
		
		if(typeof id!='undefined'){
			wgEmail.idMsg = id;			
			dataSend['wgEmail_idMsg'] = id;
		} else if(wgEmail.idMsg!=null) {
			dataSend['wgEmail_idMsg'] = wgEmail.idMsg;
		}
		if(typeof idDiv!='undefined'){
			wgEmail.isModal = false;
			wgEmail.idDiv = idDiv;
		}
		
		wgEmail.sendAjax(dataSend);
		
	},//Fim wgEmail.open
	create: function(){//INICIO wgEmail.create
		var dataSend = {wgEmail_formatReturn: 'yes',wgEmail_acao: 'create'};
		
		wgEmail.sendAjax(dataSend);		
	},//FIM wgEmail.create
	cancel: function(){//INICIO wgEmail.cancel
		var dataSend = {wgEmail_formatReturn: 'yes',wgEmail_acao: 'cancel'};
		if(wgEmail.idMsg!=null) {
			dataSend['wgEmail_idMsg'] = wgEmail.idMsg;
		}
		
		$.staAjaxJSON(wgEmail.url,dataSend,{
			fncSuccess: function(dataReturn){ 
				wgEmail.idMsg = null;
				if(wgEmail.isModal || wgEmail.idDiv==null){
					$.staDialog('close');
				} else {
					$('#'+wgEmail.idDiv).html('');//Close
				}				
			},
			fncError: function(dataReturn){ wgEmail.returnError(dataReturn.strError,dataReturn.data); },
			fncFailed: function(xhr, ajaxOptions, thrownError){ wgEmail.returnFailed(xhr.status,xhr.statusText); }
		});
		
		
	},//FIM wgEmail.cancel
	addAttachment: function(){//INICIO wgEmail.addAttachment
		var formData = new FormData(document.getElementById('form_novofilewgEmail'));		
		$.ajax({
			url: wgEmail.url,
	        type: 'POST',
	        data: formData,
			dataType: 'json',
	        //async: false,
	        cache: false,
	        contentType: false,
	        processData: false,
	        enctype: 'multipart/form-data',
			success: function(dataReturn){ wgEmail.readData(dataReturn.data); },
			xhr: function(){
			    var xhr = new window.XMLHttpRequest();
				//Upload progress

			    xhr.upload.addEventListener("progress", function(evt){
			      if (evt.lengthComputable) {
			    	  var percentComplete = evt.loaded / evt.total;
			    	  //Do something with upload progress
			    	  if(percentComplete==1){
			    		  $('#dvprogbarWgEmail').progbar({
			    			  'current':100,
			    			  'min': 0,
			    			  'max':100,
			    			  'viewOnlyPercent':false,
			    			  'style': 'progress-bar-success',
			    			  'open':true
			    		  });
					} else {
						$('#dvprogbarWgEmail').progbar({
			    			  'current':percentComplete*100,
			    			  'min': 0,
			    			  'max':100,
			    			  'viewOnlyPercent':true,
			    			  'style': '',
			    			  'open':true
			    		  });
			      	}
				}
			   }, false);

			    return xhr;
			  },
	        error: wgEmail.returnFailed
	    });

	    return false;
	    //---//

	},//FIM wgEmail.addAttachment
	delAttachment: function(id){//INICIO wgEmail.delAttachment
		var dataSend = {wgEmail_formatReturn: 'yes',wgEmail_acao: 'delAttachment'};
		if(wgEmail.idMsg!=null) {
			dataSend['wgEmail_idMsg'] = wgEmail.idMsg;
		}
		if(typeof id!='undefined'){
			dataSend['wgEmail_idAttachment'] = id;
		}
		
		wgEmail.sendAjax(dataSend);
	},//FIM wgEmail.delAttachment
	addRecipient: function(email,name){//INICIO wgEmail.addRecipient
		var dataSend = {wgEmail_formatReturn: 'yes',wgEmail_acao: 'addRecipient'};
		if(wgEmail.idMsg!=null) {
			dataSend['wgEmail_idMsg'] = wgEmail.idMsg;
		}
		if(typeof email!='undefined'){
			dataSend['wgEmail_email'] = email;
		}
		if(typeof name!='undefined'){
			dataSend['wgEmail_name'] = name;
		}
		
		wgEmail.sendAjax(dataSend);
	},//FIM wgEmail.addRecipient
	delRecipient: function(indexRecipient){//INICIO wgEmail.delRecipient
		var dataSend = {wgEmail_formatReturn: 'yes',wgEmail_acao: 'delRecipient'};
		if(wgEmail.idMsg!=null) {
			dataSend['wgEmail_idMsg'] = wgEmail.idMsg;
		}
		if(typeof indexRecipient!='undefined'){
			dataSend['wgEmail_indexRecipient'] = indexRecipient;
		}
		
		wgEmail.sendAjax(dataSend);
	},//FIM wgEmail.delRecipient
	setRecipient: function(indexRecipient,email,name){//INICIO wgEmail.setRecipient
		var dataSend = {wgEmail_formatReturn: 'yes',wgEmail_acao: 'setRecipient'};
		if(wgEmail.idMsg!=null) {
			dataSend['wgEmail_idMsg'] = wgEmail.idMsg;
		}
		if(typeof indexRecipient!='undefined'){
			dataSend['wgEmail_indexRecipient'] = indexRecipient;
		}
		if(typeof email!='undefined'){
			dataSend['wgEmail_email'] = email;
		}
		if(typeof name!='undefined'){
			dataSend['wgEmail_name'] = name;
		}
		
		wgEmail.sendAjax(dataSend);
	},//FIM wgEmail.setRecipient
	subject: function(subject){//INICIO wgEmail.subject
		var dataSend = {wgEmail_formatReturn: 'yes',wgEmail_acao: 'subject'};
		if(wgEmail.idMsg!=null) {
			dataSend['wgEmail_idMsg'] = wgEmail.idMsg;
		}
		if(typeof subject!='undefined'){
			dataSend['wgEmail_subject'] = subject;
		}
				
		wgEmail.sendAjax(dataSend,false);
	},//FIM wgEmail.subject
	msg: function(msg){//INICIO wgEmail.msg
		var dataSend = {wgEmail_formatReturn: 'yes',wgEmail_acao: 'msg'};
		
		if(wgEmail.idMsg!=null) {
			dataSend['wgEmail_idMsg'] = wgEmail.idMsg;
		}
		if(typeof msg!='undefined'){
			dataSend['wgEmail_msg'] = msg;
		}
				
		wgEmail.sendAjax(dataSend,false);
	},//FIM wgEmail.msg
	send: function(){//INICIO wgEmail.send
			
		//var loading = '<h2 style="font-size:1.5em;text-align:center;" class="text-primary"><span class="fa fa-refresh"></span> Aguarde</h2>';
		var loading = '<h2 style="font-size:1.5em;text-align:center;" class="text-primary"><span class="fa fa-refresh fa-spin"></span> Aguarde</h2>';
		
		if(wgEmail.isModal || wgEmail.idDiv==null){
			$.staDialog({
				'text': loading,
				'type': "default",
				'showTitle': false,
				'open': true,
				'isOpen': false,
				'buttons': {},
				'onOpen': wgEmail.sendEmail
			});
		} else {
			$('#'+wgEmail.idDiv).html(loading);//Close
			wgEmail.sendEmail();
		}
	},//Fim wgEmail.send	
	sendEmail: function(){//INICIO wgEmail.sendEmail	
		var dataSend = {wgEmail_formatReturn: 'yes',wgEmail_acao: 'send'};
		if(wgEmail.idMsg!=null) {
			dataSend['wgEmail_idMsg'] = wgEmail.idMsg;
		}
		$.staAjaxJSON(wgEmail.url,dataSend,{
			fncSuccess: function(dataReturn){ 
				wgEmail.idMsg = null;
				if(wgEmail.isModal || wgEmail.idDiv==null){
					$.staDialog({
						'title': "Email enviado com sucesso",
						'text': '<h1 class="text-success" style="text-align:center;">E-mail foi enviado com sucesso!</h1>',
						'type': "default",
						'buttons': {
							btClose:{
								iconLeft: "fa fa-check",
								text: "OK",
								action: "$.staDialog('close')",
								type: "success"
							}
						},
						'showTitle': false,
						'open': true
					});
				} else {
					$('#'+wgEmail.idDiv).html('');//Close
				}
			},
			fncError: function(dataReturn){ wgEmail.returnError(dataReturn.strError,dataReturn.data); },
			fncFailed: function(xhr, ajaxOptions, thrownError){ wgEmail.returnFailed(xhr.status,xhr.statusText); }
		});
	},//FIM wgEmail.sendEmail
	sendAjax: function(dataSend,refreshData){//INICIO wgEmail.sendAjax
		if(typeof refreshData=='undefined'){
			var refreshData = true;
		}
		
		$.staAjaxJSON(wgEmail.url,dataSend,{
			fncSuccess: function(dataReturn){ if(refreshData){ wgEmail.readData(dataReturn.data); } },
			fncError: function(dataReturn){ wgEmail.returnError(dataReturn.strError,dataReturn.data); },
			fncFailed: function(xhr, ajaxOptions, thrownError){ wgEmail.returnFailed(xhr.status,xhr.statusText); }
		});
	},//FIM wgEmail.sendAjax
	refresh: function(){//INICIO wgEmail.refresh
		var dataSend = {wgEmail_formatReturn: 'yes',wgEmail_acao: 'refresh'};
		if(wgEmail.idMsg!=null) {
			dataSend['wgEmail_idMsg'] = wgEmail.idMsg;
		}
		
		wgEmail.sendAjax(dataSend);
	},//FIM wgEmail.refresh
	readData: function(data){//Inicio wgEmail.readData
		if(typeof wgEmail.idMsg!='undefined' && typeof data=='undefined'){
			var data = wgEmail.data;
		}
		wgEmail.idMsg = data.id;
		wgEmail.data = data;
		
		attachmentSizeMax = 26214300;//Aprox. 25MB
		//data.attachmentSize;
		
		var percAttc = parseInt(data.attachmentSize * 100 / attachmentSizeMax);  
		
		var html ='';
		//html = html+'<button class="btn btn-default" onclick="wgEmail.refresh();">Atualizar</button>';
		html = html+'<form id="form_novofilewgEmail" enctype="multipart/form-data">';
		html = html+'<input type="hidden" name="wgEmail_idMsg" value="'+data.id+'" />';
		html = html+'<input type="hidden" name="wgEmail_formatReturn" value="yes" />';
		html = html+'<input type="hidden" name="wgEmail_acao" value="addAttachment" />';
		html = html+'<input type="file" name="arquivo" id="input_arquivowgEmail" onchange="wgEmail.addAttachment();return false;" style="display:none;" />';
		html = html+'</form>';
		
		html = html+'<table class="table">';
		html = html+'<tr>';
		html = html+'<th>Para:</th>';
		html = html+'<td>';
		var indexRcpt = 0;
		
		
		$.each(data.recipients,function(index,recipient){
			
			if(recipient.name=='' || recipient.name==null){
				html = html+'<div class="label label-default" onclick="wgEmail.delRecipient('+indexRcpt+');">'+recipient.email+' [X]</div> ';
			} else {
				html = html+'<div class="label label-default" onclick="wgEmail.delRecipient('+indexRcpt+');">'+recipient.name+' &lt;'+recipient.email+'&gt; [X]</div> ';
			}
			indexRcpt++;
		});
		
		html = html+' <input type="text" class="form-control" onchange="wgEmail.addRecipient(this.value);" />';
		html = html+'</td>';
		html = html+'</tr>';
		html = html+'<tr>';
		html = html+'<th>Assunto:</th>';
		html = html+'<td>';
		if(data.subject==null)
			data.subject = '';		
		html = html+'<input type="text" class="form-control" onchange="wgEmail.subject(this.value);" value="'+data.subject+'" />';
		html = html+'</td>';
		html = html+'</tr>';
		html = html+'<tr>';
		html = html+'<th>Mensagem:</th>';
		html = html+'<td>';
		if(data.msg==null)
			data.msg = '';
		html = html+'<textarea class="form-control" style="width:100%;height:100px;" onchange="wgEmail.msg(this.value);">'+data.msg+'</textarea>';
		html = html+'</td>';
		html = html+'</tr>';
		html = html+'<tr>';
		html = html+'<th>Anexos:</th>';
		html = html+'<td>';
		html = html+'<button onclick="$(\'#input_arquivowgEmail\').click();" class="btn btn-success btn-sm"><span class="fa fa-plus"></span></button>';
		html = html+'<div id="dvprogbarWgEmail"></div>';
		
		$.each(data.attachment,function(index,attachment){
			html = html+'<div class="label label-default" onclick="wgEmail.delAttachment('+attachment.id+');">'+attachment.user_filename+' [X]</div> ';
		});
		
		if(percAttc<100){
			if(percAttc>80){
				html = html+'<div class="progress">';
				html = html+'<div class="progress-bar progress-bar-warning" aria-valuenow="'+percAttc+'" aria-valuemin="0" aria-valuemax="100" style="width: '+percAttc+'%">';
				html = html+'</div>';
				html = html+'</div>';
				html = html+'<div class="text-warning">';
			} else {
				html = html+'<div class="progress">';
				html = html+'<div class="progress-bar" aria-valuenow="'+percAttc+'" aria-valuemin="0" aria-valuemax="100" style="width: '+percAttc+'%">';
				html = html+'</div>';
				html = html+'</div>';
				html = html+'<div class="text-success">';
			}
		} else {
			html = html+'<div><h5 class="text-danger"><span class="fa fa-exclamation-triangle"></span> Atenção: Você excedeu o limite para os anexos, retire alguns anexos para poder enviar a mensagem.</h5></div>';
			html = html+'<div class="text-danger">';
		}
		
		html = html+'Usado '+percAttc+'% limite permitido para tamanho dos anexos.<br />';
		
		var printCotaSize = data.attachmentSize;
		var printCotaTipo = "B";
		if(printCotaSize>1024){
			printCotaSize = printCotaSize/1024;
			printCotaTipo = "KB";
		}
		if(printCotaSize>1024){
			printCotaSize = printCotaSize/1024;
			printCotaTipo = "MB";
		}
		if(printCotaSize>1024){
			printCotaSize = printCotaSize/1024;
			printCotaTipo = "GB";
		}
		
		html = html+'Usado '+parseInt(printCotaSize)+printCotaTipo+' de 25MB<br />';
		html = html+'</div>';
		
		
		html = html+'</td>';
		html = html+'</tr>';
		html = html+'</table>';
		
		var actionCancel = {
			iconLeft: "fa fa-times",
			text: "Cancelar",
			action: "wgEmail.cancel()",
			type: "danger"
		};
		
		if(percAttc<100){
			var actionOK = {
					iconLeft: "fa fa-check",
					text: "Enviar",
					action: "wgEmail.send()",
					type: "success"
				};
		} else {
			var actionOK = {
					iconLeft: "fa fa-exclamation-triangle",
					text: "Enviar",
					action: "alert('Limite para anexo excedido, retire alguns arquivos e continue.')",
					type: "warning"
				};
		}
		
		if(wgEmail.isModal || wgEmail.idDiv==null){
			$.staDialog({
				'title': "Enviar Email",
				'text': html,
				'type': "default",
				'buttons': {
					btOK: actionOK,
					btClose: actionCancel
				},
				'showTitle': true,
				'open': true/*,
				'isOpen': false*/
			});
		} else {
			
			html += '<button onclick="'+actionOK.action+';return false;" class="btn btn-'+actionOK.type+'"><span class="'+actionOK.iconLeft+'"></span> '+actionOK.text+'</button>';
			html += '<button onclick="'+actionCancel.action+';return false;" class="btn btn-'+actionCancel.type+'"><span class="'+actionCancel.iconLeft+'"></span> '+actionCancel.text+'</button>';
			
			$('#'+wgEmail.idDiv).show(200).html(html);
		}	
	},//Fim wgEmail.readData
	returnError: function(strError, data){//Inicio wgEmail.returnError
		if(wgEmail.idMsg!=null){
			
			actionOnClose = "wgEmail.readData()";
		} else {
			actionOnClose = "$.staDialog('close')";
		}
		$.staDialog({
			'title': "Error",
			'text': strError,
			'type': "default",
			'buttons': {
				btClose:{
					iconLeft: "fa fa-times",
					text: "Cancelar",
					action: actionOnClose,
					type: "danger"
				}
			},
			'showTitle': true,
			'open': true
		});
	},//Fim wgEmail.returnError
	returnFailed: function(codeStatus,textStatus){//Inicio wgEmail.returnFailed
		var strError = '';
		switch (codeStatus) {
		  case 404:
			strError = 'Conteúdo não encontrado!';
			break;
		  case 200:
			strError = 'Erro inesperado';
			break;
		  default:
			strError = textStatus;
			console.log(codeStatus,textStatus);
		}
		$.staDialog({
			'title': "Error",
			'text': strError,
			'type': "default",
			'buttons': {
				btClose:{
					iconLeft: "fa fa-times",
					text: "Cancelar",
					action: "$.staDialog('close')",
					type: "danger"
				}
			},
			'showTitle': true,
			'open': true
		});
	}//Fim wgEmail.returnFailed
};//Fim wgEmail


(function( $, undefined ) {
	
	$.addAtalho = function(atalho,func){
		var regExp = new RegExp('([0-9a-zA-Z_+]+)', 'g');
		valueArray = atalho.match(regExp);
		var regExp = new RegExp('([0-9a-zA-Z_]+)', 'g');
		valueArray = atalho.match(regExp);
		atalhoArray = new Array();
		
		for (var i in valueArray) {			
			atalhoArray.push(keymap[ valueArray[i] ]);
		}
		atalhoArray.sort();
		
		atalho = 'A'+atalhoArray.join('_');
		
		atalhos[atalho] = func;
	};
	$.removeAtalho = function(atalho){
		var regExp = new RegExp('([0-9a-zA-Z+]+)', 'g');
		valueArray = atalho.match(regExp);
		var regExp = new RegExp('([0-9a-zA-Z]+)', 'g');
		valueArray = atalho.match(regExp);
		atalhoArray = new Array();
		
		for (var i in valueArray) {			
			atalhoArray.push(keymap[ valueArray[i] ]);
		}
		atalhoArray.sort();
		atalho = 'A'+atalhoArray.join('_');
				
		atalhos[atalho] = '';
		
	};
	$.execAtalho = function(){
		var novoKeystrokes = {};
		
		for (var i in keystrokes) {
			if(keystrokes[i]){
				novoKeystrokes[i] = true;				
			}			
		}
		valueArray = Object.keys(novoKeystrokes).sort();
		var atalho = 'A';
		for (var i in valueArray) {
			if(atalho=='A'){
				atalho = ''+atalho+valueArray[i];
			} else {
				atalho = ''+atalho+'_'+valueArray[i];
			}
		}
		keystrokes = novoKeystrokes;
		if(typeof atalhos[atalho]=='function' ){
			atalhos[atalho]();
			
			return false;
		} else {
			return true;
		}
		
	}
	$.keysIsPress = function(atalho){
		var regExp = new RegExp('([0-9a-zA-Z]+)', 'g');
		valueArray = atalho.match(regExp);
		atalhoArray = new Array();
		
		for (var i in valueArray) {			
			atalhoArray.push(keymap[ valueArray[i] ]);
		}
		atalhoArray.sort();
		atalho = 'A'+atalhoArray.join('_');
		var novoKeystrokes = {};
		
		for (var i in keystrokes) {
			if(keystrokes[i]){
				novoKeystrokes[i] = true;				
			}			
		}
		valueArray = Object.keys(novoKeystrokes).sort();
		
		var atalho1 = 'A';
		for (var i in valueArray) {
			if(atalho1=='A'){
				atalho1 = ''+atalho1+valueArray[i];
			} else {
				atalho1 = ''+atalho1+'_'+valueArray[i];
			}
		}
		keystrokes = novoKeystrokes;
		
		return (atalho1==atalho);
			
	};
	$.atalhoClearKeys = function(){
		keystrokes = {};		
	}

	$.fn.loccencusPesquisar = function (bannum,recdtvenc,recvalor){
		var id = $(this).attr('id');
		var elemento = $(this);
		var dataSend = {
			bannum: bannum,
			recdtvenc: recdtvenc,
			recvalor: recvalor
		};
		
		$.staAjaxJSON(url_raiz_empresa + 'cadastros/cencus/loccencus', dataSend,
			{
				fncSuccess: function(dataResponse){
					elemento.val( dataResponse.valorJuros );
				},
				fncError: function(dataResponse){
					elemento.val( 0 );
					$.staDialog({
						'title': 'Alerta/Erro',
						'text': dataResponse.strError,
						'type': 'default',
						'buttons': {
							btClose:{
								iconLeft: 'fa fa-check',
								text: 'OK',
								action: "$.staDialog('close')",
								type: 'success'
							}
						},
						'showTitle': true,
						'open': true
					});
				},
				fncFailed: function(xhr, ajaxOptions, thrownError){
					$('#divSelectcencus_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+thrownError+'</div>');
				}
			}			
		);
	};	

	$.loccondpagParams = {
		configs: {},//$.loccondpagParams.configs
		paramsDefault: {
			titulo : 'Selecione CondPag',
			codentsai: 'A',
			inativo: 'A',			
			fncReturn : function (id,pagcod,pagdesc) {//params.fncReturn
				 $('#'+id).val( pagcod );				 
				console.log(pagcod,pagdesc);
				$.staDialog('close');
				return pagcod;
			}
		}
	};
	$.fn.loccondpagChange = function(pagcod,pagdesc){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccondpagParams.configs[id] == 'undefined'){
			 $.loccondpagParams.configs[id] = $.loccondpagParams.paramsDefault;		 		
		 }
		 params = $.loccondpagParams.configs[id];
		 
		 if(typeof params.fncReturn =='function'){
			 return params.fncReturn(id,pagcod,pagdesc);
		 } else {
			 console.error('fncReturn is not Function');
			 console.log(id,pagcod,pagdesc);
			 return null;
		 }
	},
	$.fn.loccondpagPesquisar = function (page){
		
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccondpagParams.configs[id] == 'undefined'){
			 $.loccondpagParams.configs[id] = $.loccondpagParams.paramsDefault;		 		
		 }
		 params = $.loccondpagParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		if (typeof page == 'undefined')
			page = 0;
		
		var dataSend = {
			page: 0,
			codentsai: params.codentsai,
			inativo: params.inativo
		};
		
		$.staAjaxJSON(url_raiz_empresa + 'cadastros/condpag/loccondpag', dataSend,
			{
				fncSuccess: function(dataResponse){ 
					//dataResponse.page
					//dataResponse.numPages
					//dataResponse.numTotalResults
					//dataResponse.itens
					var html = '';
					//Plano
					html += "<div style=\"height:300px;overflow:auto;\">";
					html += "<table class=\"table\">";
					html += "<tr>";
					html += "<th>Codigo.</th>";
					html += "<th>Nome</th>";
					html += "<th>Venda/Compra</th>";
					html += "<th>Inativo</th>";
					html += "</tr>";
					$.each(dataResponse.itens, function(index,item){
						//pagcod,pagdesc, codentsai, inativo
						html += "<tr";
						//codentsai						
						html += " onclick=\"$('#"+id+"').loccondpagChange('"+item.pagcod+"','"+item.pagdesc+"');$.staDialog('close');\">";
						
						html += "<td>"+item.pagcod+"</td>";
						html += "<td>"+item.pagdesc+"</td>";
						html += "<td>"+item.codentsai+"</td>";
						html += "<td>"+item.inativo+"</td>";						
						html += "</tr>";
					});
					html += "</table>";
					html += "</div>";					
					//Fim plano
					$('#divSelectcondpag_resultado').html(html);
				},
				fncError: function(dataResponse){ 
					$('#divSelectcondpag_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+dataResponse.strError+'</div>');
				},
				fncFailed: function(xhr, ajaxOptions, thrownError){
					$('#divSelectcondpag_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+thrownError+'</div>');
				}
			}			
		);
		$.loccondpagParams.configs[id] = params;
	};	
	$.fn.loccondpagSetFiltro= function(parametros){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccondpagParams.configs[id] == 'undefined'){
			 $.loccondpagParams.configs[id] = $.loccondpagParams.paramsDefault;		 		
		 }
		 params = $.loccondpagParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);		
	
		$.loccondpagParams.configs[id] = params;
	};
	$.fn.loccondpagSelecionar= function (parametros) {	
		 id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccondpagParams.configs[id] == 'undefined'){
			 $.loccondpagParams.configs[id] = $.loccondpagParams.paramsDefault;		 		
		 }
		 params = $.loccondpagParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		 var html = '';		
		html += '<div style="position:relative;margin:5px;" id="divSelectcondpag_resultado"></div>';
		
		$.staDialog({
			'title' : params.titulo,
			'text' : html,
			'type' : 'default',
			'buttons' : {
				btClose : {
					iconLeft : 'fa fa-times',
					text : 'Cancelar',
					action : "$.staDialog('close');",
					type : 'danger'
				}
			},
			'showTitle' : true,
			'onOpen' : function () {
				$('#'+id).loccondpagPesquisar();
			},
			'open' : true
		});
		$.loccondpagParams.configs[id] = params;
	 };

	$.loccontratoParams = {
		configs: {},//$.loccontratoParams.configs
		paramsDefault: {
			titulo : 'Selecione contrato',			
			fncReturn : function (id,idContrado,numcontrato,sequencia) {//params.fncReturn
				 $('#'+id).val( codigo );				 
				console.log(id,idContrado,numcontrato,sequencia);
				$.staDialog('close');
				return codigo;
			}
		}
	};
	$.fn.loccontratoChange = function(id,idContrado,numcontrato,sequencia){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccontratoParams.configs[id] == 'undefined'){
			 $.loccontratoParams.configs[id] = $.loccontratoParams.paramsDefault;		 		
		 }
		 params = $.loccontratoParams.configs[id];
		 
		 if(typeof params.fncReturn =='function'){
			 return params.fncReturn(id,idContrado,numcontrato,sequencia);
		 } else {
			 console.error('fncReturn is not Function');
			 console.log(id,idContrado,numcontrato,sequencia);
			 return null;
		 }
	},
	$.fn.loccontratoPesquisar = function (page){
		
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccontratoParams.configs[id] == 'undefined'){
			 $.loccontratoParams.configs[id] = $.loccontratoParams.paramsDefault;		 		
		 }
		 params = $.loccontratoParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		if (typeof page == 'undefined')
			page = 0;
		
		var dataSend = {
			page: 0,
			
		};
		
		$.staAjaxJSON(url_raiz_empresa + 'cadastros/contratos/loccontrato', dataSend,
			{
				fncSuccess: function(dataResponse){ 
					//dataResponse.page
					//dataResponse.numPages
					//dataResponse.numTotalResults
					//dataResponse.itens
					var html = '';
					//Plano
					html += "<div style=\"height:300px;overflow:auto;\">";
					html += "<table class=\"table\">";
					html += "<tr>";
					html += "<th>ID.</th>";
					html += "<th>Num.Contrato</th>";
					html += "<th>Seq/Aditivo</th>";					
					html += "<th>Nome.</th>";
					html += "<th>Dt.Ini</th>";
					html += "<th>Dt.Fim</th>";					
					html += "</tr>";
					$.each(dataResponse.itens, function(index,item){
						html += "<tr";					
						html += " onclick=\"$('#"+id+"').loccontratoChange('"+id+"','"+item.id+"','"+item.numcontrato+"');$.staDialog('close');\">";
						html += "<td>"+item.id+"</td>";
						html += "<td>"+item.numcontrato+"</td>";	
						html += "<td>"+item.sequencia+"</td>";
						html += "<td>"+item.nome+"</td>";
						html += "<td>"+item.dtini+"</td>";	
						html += "<td>"+item.dtfin+"</td>";									
						html += "</tr>";
					});
					html += "</table>";
					html += "</div>";					
					//Fim contrato
					$('#divSelectcontrato_resultado').html(html);
				},
				fncError: function(dataResponse){ 
					$('#divSelectcontrato_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+dataResponse.strError+'</div>');
				},
				fncFailed: function(xhr, ajaxOptions, thrownError){
					$('#divSelectcontrato_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+thrownError+'</div>');
				}
			}			
		);
		$.loccontratoParams.configs[id] = params;
	};	
	$.fn.loccontratoSetFiltro= function(parametros){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccontratoParams.configs[id] == 'undefined'){
			 $.loccontratoParams.configs[id] = $.loccontratoParams.paramsDefault;		 		
		 }
		 params = $.loccontratoParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);		
	
		$.loccontratoParams.configs[id] = params;
	};
	$.fn.loccontratoSelecionar= function (parametros) {	
		 id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccontratoParams.configs[id] == 'undefined'){
			 $.loccontratoParams.configs[id] = $.loccontratoParams.paramsDefault;		 		
		 }
		 params = $.loccontratoParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		 var html = '';		
		html += '<div style="position:relative;margin:5px;" id="divSelectcontrato_resultado"></div>';
		
		$.staDialog({
			'title' : params.titulo,
			'text' : html,
			'type' : 'default',
			'buttons' : {
				btClose : {
					iconLeft : 'fa fa-times',
					text : 'Cancelar',
					action : "$.staDialog('close');",
					type : 'danger'
				}
			},
			'showTitle' : true,
			'onOpen' : function () {
				$('#'+id).loccontratoPesquisar();
			},
			'open' : true
		});
		$.loccontratoParams.configs[id] = params;
	 };

	$.locmoedaParams = {
		configs: {},//$.locmoedaParams.configs
		paramsDefault: {
			titulo : 'Selecione moeda',
			travalimitecredito: 'A',
			inativo: 'N',			
			fncReturn : function (id,codmoeda,descmoeda) {//params.fncReturn
				 $('#'+id).val( codmoeda );				 
				console.log(codmoeda,descmoeda);
				$.staDialog('close');
				return codmoeda;
			}
		}
	};
	$.fn.locmoedaChange = function(codmoeda,descmoeda){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locmoedaParams.configs[id] == 'undefined'){
			 $.locmoedaParams.configs[id] = $.locmoedaParams.paramsDefault;		 		
		 }
		 params = $.locmoedaParams.configs[id];
		 
		 if(typeof params.fncReturn =='function'){
			 return params.fncReturn(id,codmoeda,descmoeda);
		 } else {
			 console.error('fncReturn is not Function');
			 console.log(id,codmoeda,descmoeda);
			 return null;
		 }
	},
	$.fn.locmoedaPesquisar = function (page){
		
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locmoedaParams.configs[id] == 'undefined'){
			 $.locmoedaParams.configs[id] = $.locmoedaParams.paramsDefault;		 		
		 }
		 params = $.locmoedaParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		if (typeof page == 'undefined')
			page = 0;
		
		var dataSend = {
			page: 0,
			travalimitecredito: params.travalimitecredito,
			inativo: params.inativo
		};
		
		$.staAjaxJSON(url_raiz_empresa + 'cadastros/moeda/locmoeda', dataSend,
			{
				fncSuccess: function(dataResponse){ 
					//dataResponse.page
					//dataResponse.numPages
					//dataResponse.numTotalResults
					//dataResponse.itens
					var html = '';
					//Plano
					html += "<div style=\"height:300px;overflow:auto;\">";
					html += "<table class=\"table\">";
					html += "<tr>";
					html += "<th>Codigo.</th>";
					html += "<th>Moeda</th>";
					html += "<th>TravaLimiteCredito</th>";
					html += "<th>Inativo</th>";
					html += "</tr>";
					$.each(dataResponse.itens, function(index,item){
						//codmoeda,descmoeda, travalimitecredito, inativo
						html += "<tr";
						//travalimitecredito						
						html += " onclick=\"$('#"+id+"').locmoedaChange('"+item.codmoeda+"','"+item.descmoeda+"');$.staDialog('close');\">";
						
						html += "<td>"+item.codmoeda+"</td>";
						html += "<td>"+item.descmoeda+"</td>";
						html += "<td>"+item.travalimitecredito+"</td>";
						html += "<td>"+item.inativo+"</td>";						
						html += "</tr>";
					});
					html += "</table>";
					html += "</div>";					
					//Fim plano
					$('#divSelectmoeda_resultado').html(html);
				},
				fncError: function(dataResponse){ 
					$('#divSelectmoeda_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+dataResponse.strError+'</div>');
				},
				fncFailed: function(xhr, ajaxOptions, thrownError){
					$('#divSelectmoeda_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+thrownError+'</div>');
				}
			}			
		);
		$.locmoedaParams.configs[id] = params;
	};	
	$.fn.locmoedaSetFiltro= function(parametros){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locmoedaParams.configs[id] == 'undefined'){
			 $.locmoedaParams.configs[id] = $.locmoedaParams.paramsDefault;		 		
		 }
		 params = $.locmoedaParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);		
	
		$.locmoedaParams.configs[id] = params;
	};
	$.fn.locmoedaSelecionar= function (parametros) {	
		 id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locmoedaParams.configs[id] == 'undefined'){
			 $.locmoedaParams.configs[id] = $.locmoedaParams.paramsDefault;		 		
		 }
		 params = $.locmoedaParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		 var html = '';		
		html += '<div style="position:relative;margin:5px;" id="divSelectmoeda_resultado"></div>';
		
		$.staDialog({
			'title' : params.titulo,
			'text' : html,
			'type' : 'default',
			'buttons' : {
				btClose : {
					iconLeft : 'fa fa-times',
					text : 'Cancelar',
					action : "$.staDialog('close');",
					type : 'danger'
				}
			},
			'showTitle' : true,
			'onOpen' : function () {
				$('#'+id).locmoedaPesquisar();
			},
			'open' : true
		});
		$.locmoedaParams.configs[id] = params;
	 };

	$.loccencusParams = {
		configs: {},//$.locobraParams.configs
		paramsDefault: {
			titulo : 'Selecione Obra',			
			fncReturn : function (cencus,descricao) {//params.fncReturn
				 $('#'+id).val( cencus );				 
				console.log(cencus,descricao);
				$.staDialog('close');
				return codigo;
			}
		}
	};
	$.fn.loccencusChange = function(cencus,descricao){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccencusParams.configs[id] == 'undefined'){
			 $.loccencusParams.configs[id] = $.loccencusParams.paramsDefault;		 		
		 }
		 params = $.loccencusParams.configs[id];
		 
		 if(typeof params.fncReturn =='function'){
			 return params.fncReturn(cencus,descricao);
		 } else {
			 console.error('fncReturn is not Function');
			 console.log(cencus,descricao);
			 return null;
		 }
	},
	$.fn.loccencusPesquisar = function (page){
		
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccencusParams.configs[id] == 'undefined'){
			 $.loccencusParams.configs[id] = $.loccencusParams.paramsDefault;		 		
		 }
		 params = $.loccencusParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		if (typeof page == 'undefined')
			page = 0;
		
		var dataSend = {
			page: 0,
			//travalimitecredito: params.travalimitecredito,
			//inativo: params.inativo
		};
		
		$.staAjaxJSON(url_raiz_empresa + 'cadastros/cencus/loccencus', dataSend,
			{
				fncSuccess: function(dataResponse){ 
					dataResponse.page
					dataResponse.numPages
					dataResponse.numTotalResults
					dataResponse.itens
					var html = '';
					//Plano
					html += "<div style=\"height:300px;overflow:auto;\">";
					html += "<table class=\"table\">";
					html += "<tr>";
					html += "<th>Codigo.</th>";
					html += "<th>Obra</th>";
					html += "</tr>";
					$.each(dataResponse.itens, function(index,item){
						//codigo,descricao, travalimitecredito, inativo
						html += "<tr";
						//travalimitecredito						
						html += " onclick=\"$('#"+id+"').loccencusChange('"+item.cencus+"','"+item.descricao+"');$.staDialog('close');\">";
						
						html += "<td>"+item.cencus+"</td>";
						html += "<td>"+item.descricao+"</td>";																
						html += "</tr>";
					});
					html += "</table>";
					html += "</div>";					
					//Fim plano
					$('#divSelectcencus_resultado').html(html);
				},
				fncError: function(dataResponse){ 
					$('#divSelectcencus_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+dataResponse.strError+'</div>');
				},
				fncFailed: function(xhr, ajaxOptions, thrownError){
					$('#divSelectcencus_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+thrownError+'</div>');
				}
			}			
		);
		$.loccencusParams.configs[id] = params;
	};	
	$.fn.loccencusSetFiltro= function(parametros){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccencusParams.configs[id] == 'undefined'){
			 $.loccencusParams.configs[id] = $.loccencusParams.paramsDefault;		 		
		 }
		 params = $.loccencusParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);		
	
		$.loccencusParams.configs[id] = params;
	};
	$.fn.loccencusSelecionar= function (parametros) {	
		 id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loccencusParams.configs[id] == 'undefined'){
			 $.loccencusParams.configs[id] = $.loccencusParams.paramsDefault;		 		
		 }
		 params = $.loccencusParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		 var html = '';		
		html += '<div style="position:relative;margin:5px;" id="divSelectcencus_resultado"></div>';
		
		$.staDialog({
			'title' : params.titulo,
			'text' : html,
			'type' : 'default',
			'buttons' : {
				btClose : {
					iconLeft : 'fa fa-times',
					text : 'Cancelar',
					action : "$.staDialog('close');",
					type : 'danger'
				}
			},
			'showTitle' : true,
			'onOpen' : function () {
				$('#'+id).loccencusPesquisar();
			},
			'open' : true
		});
		$.loccencusParams.configs[id] = params;
	 };

	
	$.locplanocontasParams = {
		configs: {},//$.locplanocontasParams.configs
		paramsDefault: {
			titulo : 'Selecione Plano de contas',
			SinteticaAnalitica: 0,
			DebitoCredito: 0,
			CentroCusto: '',
			ApareceNoResult: 0,
			selectCentroCusto: true,
			page: 0,
			fncReturn : function (id,plancod,plandesc) {//params.fncReturn
				 $('#'+id).val( plancod );
				 
				console.log(plancod,plandesc);
				$.staDialog('close');
				return plancod;
			}
		}
	};
	$.fn.locplanocontasChange = function(plancod,plandesc){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locplanocontasParams.configs[id] == 'undefined'){
			 $.locplanocontasParams.configs[id] = $.locplanocontasParams.paramsDefault;		 		
		 }
		 params = $.locplanocontasParams.configs[id];
		 
		 if(typeof params.fncReturn =='function'){
			 return params.fncReturn(id,plancod,plandesc);
		 } else {
			 console.error('fncReturn is not Function');
			 console.log(id,plancod,plandesc);
			 return null;
		 }
	},
	$.fn.locplanocontasPesquisar = function (page){
		
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locplanocontasParams.configs[id] == 'undefined'){
			 $.locplanocontasParams.configs[id] = $.locplanocontasParams.paramsDefault;		 		
		 }
		 params = $.locplanocontasParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		if (typeof page == 'undefined')
			page = 0;
		if(params.selectCentroCusto){
			params.CentroCusto = $('#SelectCentroCusto > option:selected').val();
		}
		var dataSend = {
			page: page,
			SinteticaAnalitica: params.SinteticaAnalitica,
			DebitoCredito: params.DebitoCredito,
			CentroCusto: params.CentroCusto,
			ApareceNoResult: params.ApareceNoResult,
			q: $('#editSelectplanocontas').val()
		};
		
		$.staAjaxJSON(url_raiz_empresa + 'cadastros/plano/locplanocontas', dataSend,
			{
				fncSuccess: function(dataResponse){ 
					//dataResponse.page
					//dataResponse.numPages
					//dataResponse.numTotalResults
					//dataResponse.itens
					var html = '';
					/*if(dataResponse.sql!="")
						html += '<div class="panel panel-default"><div class="panel-body">'+dataResponse.sql+'</div></div>';
					*/
					/////////////////////// plano
					html += "<div style=\"height:300px;overflow:auto;\">";
					html += "<table class=\"table\">";
					 html += "<tr>";
					html += "<th>Codigo.</th>";
					html += "<th>Nome</th>";
					html += "<th>Sin/Ana</th>";
					html += "<th>Deb/Cred</th>";
					//html += "<th>Previsão</th>";
					html += "<th>Ap. Result</th>";
					html += "</tr>";
					$.each(dataResponse.itens, function(index,item){
						//plancod,plandesc,sinteticaanalitica,deditocredito,aparecenoresult
						html += "<tr";
						//DebitoCredito
						if(item.deditocredito=="C"){
							html += " class=\"text-success\"";
						} else {
							html += " class=\"text-danger\"";
						}
						//SinteticaAnalitica
						if(item.sinteticaanalitica=="S"){
							html += " style=\"font-weight:bold;\"";
						}
						html += " onclick=\"$('#"+id+"').locplanocontasChange('"+item.plancod+"','"+item.plandesc+"');$.staDialog('close');\">";
						
						html += "<td>"+item.plancod+"</td>";
						html += "<td>"+item.plandesc+"</td>";
						html += "<td>"+item.sinteticaanaliticaDesc+"</td>";
						html += "<td>"+item.deditocreditoDesc+"</td>";
						html += "<td>"+item.aparecenoresultDesc+"</td>";						
						html += "</tr>";
					});
					html += "</table>";
					html += "</div>";
					
					/////////////////////////fim plano///////////////////


					if(dataResponse.numPages>1){
						var i = dataResponse.page-3;
						if(i<0)
							i = 0;
						
						var pAnterior = parseInt(dataResponse.page)-1;
						if(pAnterior<0)
							pAnterior = 0;
						
						html += '<nav>';
						html += '<ul class="pagination">';
						//Anterior---
						html += '<li>';
						html += '<a href="#" onclick="$(\'#'+id+'\').locplanocontasPesquisar('+pAnterior+');return false;" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>';
						html += '</li>';
						
						//Paginas---
						for (ii = 0; ii < 7; ii++) {
							pV = parseInt(i+1);
							if(i<dataResponse.numPages){
								if(i==dataResponse.page){
									html += '<li class="active"><a href="#" onclick="return false;">'+pV+'</a></li>';
								} else {
									html += '<li><a href="#" onclick="$(\'#'+id+'\').locplanocontasPesquisar('+i+');return false;">'+pV+'</a></li>';									
								}							
								i++;
							} else {
								ii = 7;
							}
						}
						
						//Proximo---
						var pProximo = parseInt(dataResponse.page)+1;
						if(pProximo<dataResponse.numPages){
							html += '<li>';
							html += '<a href="#" onclick="$(\'#'+id+'\').locplanocontasPesquisar('+pProximo+');return false;" aria-label="Next">';
						} else {
							html += '<li class="disabled">';
							html += '<a href="#" onclick="return false;" aria-label="Next">';
						}						
						html += '<span aria-hidden="true">&raquo;</span>';
						html += '</a>';
						html += '</li>';
						
						html += '</ul>';
						html += '</nav> ';
						//html += parseInt(dataResponse.page+1)+' / '+dataResponse.numPages;						
					}
					$('#divSelectEntidade_resultado').html(html);
				},
				fncError: function(dataResponse){ 
					$('#divSelectEntidade_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+dataResponse.strError+'</div>');
				},
				fncFailed: function(xhr, ajaxOptions, thrownError){
					$('#divSelectEntidade_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+thrownError+'</div>');
				}
			}			
		);
		$.locplanocontasParams.configs[id] = params;
	};
	/////////////////////////montar select do centro de custo
	$.fn.locplanocontasmontaselect = function (){
		
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locplanocontasParams.configs[id] == 'undefined'){
			 $.locplanocontasParams.configs[id] = $.locplanocontasParams.paramsDefault;		 		
		 }
		 params = $.locplanocontasParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		if (typeof page == 'undefined')
			page = 0;
			
		var dataSend = {
			SomenteCentroCusto:1,
			page: page,
			SinteticaAnalitica: params.SinteticaAnalitica,
			DebitoCredito: params.DebitoCredito,
			CentroCusto: params.CentroCusto,
			ApareceNoResult: params.ApareceNoResult,
			q: ''
		};
		
		$.staAjaxJSON(url_raiz_empresa + 'cadastros/plano/locplanocontas', dataSend,
			{
				fncSuccess: function(dataResponse){ 
					//dataResponse.page
					//dataResponse.numPages
					//dataResponse.numTotalResults
					//dataResponse.itens
					var html = '';
					/*if(dataResponse.sql!="")
						html += '<div class="panel panel-default"><div class="panel-body">'+dataResponse.sql+'</div></div>';
					*/
					/////////////////////// plano
					$.each(dataResponse.itens, function(index,item){
						//plancod,plandesc,sinteticaanalitica,deditocredito,aparecenoresult
						html += '<option value = "'+item.plancod+'">'+item.plancod+' - '+item.plandesc+'</option>';
						
					});
					$('#SelectCentroCusto').html(html);
					/////////////////////////fim plano///////////////////				
				},
				fncError: function(dataResponse){ 
					$('#divSelectEntidade_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+dataResponse.strError+'</div>');
				},
				fncFailed: function(xhr, ajaxOptions, thrownError){
					$('#divSelectEntidade_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+thrownError+'</div>');
				}
			}			
		);
		$.locplanocontasParams.configs[id] = params;
	};
	
	
	
	
	//////////////////////////////
	$.fn.locplanocontasSetFiltro= function(parametros){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locplanocontasParams.configs[id] == 'undefined'){
			 $.locplanocontasParams.configs[id] = $.locplanocontasParams.paramsDefault;		 		
		 }
		 params = $.locplanocontasParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		
		//-----Tipo Entidades----//
		$('#btnGroupSelectEntidade_tipoEntidades > .btn-primary').removeClass('btn-primary').addClass('btn-default');
		$('#btnGroupSelectEntidade_tipoEntidades > .btn[data-value='+params.entidade+']').removeClass('btn-default').addClass('btn-primary');
		
		//-----Consulta por ------//
		$('#btnGroupSelectEntidade_consultaPor > .btn-primary').removeClass('btn-primary').addClass('btn-default');
		$('#btnGroupSelectEntidade_consultaPor > .btn[data-value='+params.consultaPor+']').removeClass('btn-default').addClass('btn-primary');
		
		$.locplanocontasParams.configs[id] = params;
	};
	/*$('#').locplanocontasSelecionar();*/
	$.fn.locplanocontasSelecionar= function (parametros) {	
		 id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locplanocontasParams.configs[id] == 'undefined'){
			 $.locplanocontasParams.configs[id] = $.locplanocontasParams.paramsDefault;		 		
		 }
		 params = $.locplanocontasParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		 var html = '';
	
		//-----Tipo Entidades----//
		if(params.selectCentroCusto){
			html += '<div style="float:left;margin:5px;">';
			html += '<span style="font-weight:bold;">Centro de Custo:</span>';
			html += '<select id="SelectCentroCusto" class="">';//
			
			html += '</select>';
			html += '</div>';
		}
		//--------------------//
		
		//
		//-----Consulta ------//
		//html += '<div style="position:relative;"><input type="text" id="edformcod_cvf" /></div>';
		html += '<div style="clear:both;"></div>';
		html += '<div style="position:relative;">';
		html += '<div class="input-group">';
		html += '<input id="editSelectplanocontas" type="text" class="form-control" placeholder="Digite o código ou Nome">';
		html += '<span class="input-group-btn">';
		html += '<button class="btn btn-default" onClick="$(\'#'+id+'\').locplanocontasPesquisar();return false;" type="button">Pesquisar</button>';
		html += '</span>';
		html += '</div>';
		html += '</div>';
		//---------------------//
		html += '<div style="clear:both;"></div>';
		html += '<div style="position:relative;margin:5px;" id="divSelectEntidade_resultado"></div>';
		$.staDialog({
			'title' : params.titulo,
			'text' : html,
			'type' : 'default',
			'buttons' : {
				btClose : {
					iconLeft : 'fa fa-times',
					text : 'Cancelar',
					action : "$.staDialog('close');",
					type : 'danger'
				}
			},
			'showTitle' : true,
			'onOpen' : function () {
				var valor = $('#'+id).val();
				console.log(valor,id);
				$('#editSelectplanocontas').val( valor );
				if(valor!='')
					$('#'+id).locplanocontasPesquisar();
				if(params.selectCentroCusto){
				  $('#'+id).locplanocontasmontaselect();
				}
			},
			'open' : true
		});
		$.locplanocontasParams.configs[id] = params;
	 };

	$.locserieParams = {
		configs: {},//$.locserieParams.configs
		paramsDefault: {
			titulo : 'Selecione serie',			
			fncReturn : function (id,codigo,sigla,descricao,cod_modelo) {//params.fncReturn
				 $('#'+id).val( codigo );				 
				console.log(codigo,descricao);
				$.staDialog('close');
				return codigo;
			}
		}
	};
	$.fn.locserieChange = function(codigo,sigla,descricao,cod_modelo){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locserieParams.configs[id] == 'undefined'){
			 $.locserieParams.configs[id] = $.locserieParams.paramsDefault;		 		
		 }
		 params = $.locserieParams.configs[id];
		 
		 if(typeof params.fncReturn =='function'){
			 return params.fncReturn(id,codigo,sigla,descricao,cod_modelo);
		 } else {
			 console.error('fncReturn is not Function');
			 console.log(id,codigo,sigla,descricao,cod_modelo);
			 return null;
		 }
	},
	$.fn.locseriePesquisar = function (page){
		
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locserieParams.configs[id] == 'undefined'){
			 $.locserieParams.configs[id] = $.locserieParams.paramsDefault;		 		
		 }
		 params = $.locserieParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		if (typeof page == 'undefined')
			page = 0;
		
		var dataSend = {
			page: 0,
			travalimitecredito: params.travalimitecredito,
			inativo: params.inativo
		};
		
		$.staAjaxJSON(url_raiz_empresa + 'cadastros/serie/locserie', dataSend,
			{
				fncSuccess: function(dataResponse){ 
					//dataResponse.page
					//dataResponse.numPages
					//dataResponse.numTotalResults
					//dataResponse.itens
					var html = '';
					//Plano
					html += "<div style=\"height:300px;overflow:auto;\">";
					html += "<table class=\"table\">";
					html += "<tr>";
					html += "<th>Serie.</th>";
					html += "<th>Modelo</th>";
					html += "<th>Descrição</th>";					
					html += "</tr>";
					$.each(dataResponse.itens, function(index,item){
						//codigo,descricao, travalimitecredito, inativo
						html += "<tr";
						//travalimitecredito						
						html += " onclick=\"$('#"+id+"').locserieChange('"+item.codigo+"','"+item.sigla+"','"+item.descricao+"','"+item.cod_modelo+"');$.staDialog('close');\">";
						
						html += "<td>"+item.sigla+"</td>";
						html += "<td>"+item.cod_modelo+"</td>";	
						html += "<td>"+item.descricao+"</td>";									
						html += "</tr>";
					});
					html += "</table>";
					html += "</div>";					
					//Fim plano
					$('#divSelectserie_resultado').html(html);
				},
				fncError: function(dataResponse){ 
					$('#divSelectserie_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+dataResponse.strError+'</div>');
				},
				fncFailed: function(xhr, ajaxOptions, thrownError){
					$('#divSelectserie_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+thrownError+'</div>');
				}
			}			
		);
		$.locserieParams.configs[id] = params;
	};	
	$.fn.locserieSetFiltro= function(parametros){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locserieParams.configs[id] == 'undefined'){
			 $.locserieParams.configs[id] = $.locserieParams.paramsDefault;		 		
		 }
		 params = $.locserieParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);		
	
		$.locserieParams.configs[id] = params;
	};
	$.fn.locserieSelecionar= function (parametros) {	
		 id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.locserieParams.configs[id] == 'undefined'){
			 $.locserieParams.configs[id] = $.locserieParams.paramsDefault;		 		
		 }
		 params = $.locserieParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		 var html = '';		
		html += '<div style="position:relative;margin:5px;" id="divSelectserie_resultado"></div>';
		
		$.staDialog({
			'title' : params.titulo,
			'text' : html,
			'type' : 'default',
			'buttons' : {
				btClose : {
					iconLeft : 'fa fa-times',
					text : 'Cancelar',
					action : "$.staDialog('close');",
					type : 'danger'
				}
			},
			'showTitle' : true,
			'onOpen' : function () {
				$('#'+id).locseriePesquisar();
			},
			'open' : true
		});
		$.locserieParams.configs[id] = params;
	 };

	
	$.loctableParams = {		
		configs: {},//$.loctableParams.configs
		paramsDefault: {
			url: '#',
			titulo : 'Selecione',
			params: {
				/*q: {
					value: '',
					valueByFieldId: null, //Id do campo para pegar o valor
					label: '',
					visivel: true,
					placeholder: "Digite o Código ou Descrição",//Somente quando tipo == text
					tipo: 'text'// text / Switch / search
					/* caso Switch:
					 * options: {
					 * 		label: 'Sim'
					 * 		value: 'S'
					 * } 		
					 
				}*/
			},
			page: 0,
			fncReturn : function (id,dados,value) {//params.fncReturn
				 $('#'+id).val( value );
				 
				console.log(dados);
				$.staDialog('close');
				return dados;
			},
			data: {}
		}
	};
	$.fn.loctableChange = function(idRow){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loctableParams.configs[id] == 'undefined'){
			 $.loctableParams.configs[id] = $.loctableParams.paramsDefault;		 		
		 }
		 params = $.loctableParams.configs[id];
		 
		 var dados = {};
		 $.each(params.data.header, function(idCol,col){
			 dados[col.field] = params.data.body[idRow][idCol]; 
		 });
		 
		 $.staDialog('close');
		 if(typeof params.fncReturn =='function'){
			 return params.fncReturn(id,dados,params.data.body[idRow][0]);
		 } else {
			 console.error('fncReturn is not Function');
			 console.log(id,dados,params.data.body[idRow][0]);
			 return null;
		 }
	},
	$.fn.loctablePesquisar = function (page){
		
		id = $(this).attr('id');//id do elemento
		valueLoctable = $(this).val();
		
		 if (typeof $.loctableParams.configs[id] == 'undefined'){
			 $.loctableParams.configs[id] = $.loctableParams.paramsDefault;		 		
		 }
		 params = $.loctableParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		if (typeof page == 'undefined')
			page = 0;
		
		var dataSend = {page: page,loctable: id};
		
		$.each(params.params, function(nameParam,infoParam){
			if(infoParam.valueByFieldId!=null && $('#'+infoParam.valueByFieldId).size()==1){
				infoParam.value = $('#'+infoParam.valueByFieldId).val();
			}
			
			dataSend['p_'+nameParam ] = infoParam.value;
		});
		
		$.staAjaxJSON(params.url, dataSend,
			{
				fncSuccess: function(dataResponse){ 
					params.data = dataResponse;
					var html = '';
					
					html += "<div style=\"height:300px;overflow:auto;\">";
					html += "<table class=\"table\">";
					html += "<tr>";
					/* col[idCol] {
					 * 		field
					 * 		label
					 * 		saida
					 * 		visivel
					 * }
					 * */
					$.each(dataResponse.header, function(idCol,col){
						if(col.visivel){
							html += '<th data-idCol="'+idCol+'" data-field="'+col.field+'">'+col.label+'</th>';
						}
					});
					
					html += "</tr>";
					$.each(dataResponse.body, function(idRow,row){
						html += "<tr";
						if(valueLoctable==row[0]){
							html += ' class="active"';
						}
						html += " onclick=\"$('#"+id+"').loctableChange("+idRow+");\">";
						
						$.each(dataResponse.header, function(idCol,col){
							if(col.visivel){
								html += '<td data-value="'+row[idCol]+'" data-idRow="'+idRow+'" data-idCol="'+idCol+'">';
								html += $.getFormat(col.saida,row[idCol]); ;
								html += '</td>';
							}
						});
						html += "</tr>";
					});
					html += "</table>";
					html += "</div>";
					
					


					if(dataResponse.numPages>1){
						var i = dataResponse.page-3;
						if(i<0)
							i = 0;
						
						var pAnterior = parseInt(dataResponse.page)-1;
						if(pAnterior<0)
							pAnterior = 0;
						
						html += '<nav>';
						html += '<ul class="pagination">';
						//Anterior---
						html += '<li>';
						html += '<a href="#" onclick="$(\'#'+id+'\').loctablePesquisar('+pAnterior+');return false;" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>';
						html += '</li>';
						
						//Paginas---
						for (ii = 0; ii < 7; ii++) {
							pV = parseInt(i+1);
							if(i<dataResponse.numPages){
								if(i==dataResponse.page){
									html += '<li class="active"><a href="#" onclick="return false;">'+pV+'</a></li>';
								} else {
									html += '<li><a href="#" onclick="$(\'#'+id+'\').loctablePesquisar('+i+');return false;">'+pV+'</a></li>';									
								}							
								i++;
							} else {
								ii = 7;
							}
						}
						
						//Proximo---
						var pProximo = parseInt(dataResponse.page)+1;
						if(pProximo<dataResponse.numPages){
							html += '<li>';
							html += '<a href="#" onclick="$(\'#'+id+'\').loctablePesquisar('+pProximo+');return false;" aria-label="Next">';
						} else {
							html += '<li class="disabled">';
							html += '<a href="#" onclick="return false;" aria-label="Next">';
						}						
						html += '<span aria-hidden="true">&raquo;</span>';
						html += '</a>';
						html += '</li>';
						
						html += '</ul>';
						html += '</nav> ';
						//html += parseInt(dataResponse.page+1)+' / '+dataResponse.numPages;						
					}
					$('#divSelect'+id+'_resultado').html(html);
				},
				fncError: function(dataResponse){ 
					$('#divSelect'+id+'_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+dataResponse.strError+'</div>');
				},
				fncFailed: function(xhr, ajaxOptions, thrownError){
					$('#divSelect'+id+'_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+thrownError+'</div>');
				}
			}			
		);
		$.loctableParams.configs[id] = params;
	};
	
	$.fn.loctableSetFiltro= function(parametros){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.loctableParams.configs[id] == 'undefined'){
			 $.loctableParams.configs[id] = $.loctableParams.paramsDefault;		 		
		 }
		 params = $.loctableParams.configs[id];
		 		
		 if (typeof parametros != 'undefined'){
			 $.each(parametros, function(nameParam,valueParam){
				 params.params[nameParam].value = valueParam;
			 });
		 }
		
		//-----Tipo Entidades----//		 
		$.each(parametros, function(nameParam,valueParam){
			if (typeof params.params[ nameParam ]!= 'undefined'){
				var param = params.params[ nameParam ];				
				if(param.tipo=='switch'){
					$('#btnGSelect'+id+'_'+nameParam+' > .btn-primary').removeClass('btn-primary').addClass('btn-default');
					$('#btnGSelect'+id+'_'+nameParam+' > .btn[data-value='+valueParam+']').removeClass('btn-default').addClass('btn-primary');					
				} else if(param.tipo=='text'){
					
					$('#edSelect'+id+'_'+nameParam+'').val( valueParam );
				}
			}
		});		
		
		$.loctableParams.configs[id] = params;
	};
	
	$.fn.loctableSelecionar= function (parametros) {	
		 id = $(this).attr('id');//id do elemento
		 valueLoctable = $(this).value;
		 
		 if (typeof $.loctableParams.configs[id] == 'undefined'){
			 $.loctableParams.configs[id] = $.loctableParams.paramsDefault;		 		
		 }
		 params = $.loctableParams.configs[id];
		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		 var html = '';
	
		//-----Consulta ------//
		var cParam = 0;
		
		console.log(typeof params.params.q);
		
		$.each(params.params, function(nameParam,dadosParam){
			html += '<div style="clear:both;"></div>';
			html += '<div style="position:relative;">';
			
			if(dadosParam.tipo=='switch'){
				cParam += 1;
				html += '<span style="font-weight:bold;">'+dadosParam.label+':</span><br /> ';
				html += '<div id="btnGSelect'+id+'_'+nameParam+'" class="btn-group">';
				$.each(dadosParam.options, function(iOption,option){
					html += '<div class="btn btn-'+((dadosParam.value==option.value)?'primary':'default')+'" ';
					if(typeof option.value=='number'){
						html += 'onclick="$(\'#'+id+'\').loctableSetFiltro({'+nameParam+': '+option.value+'});" ';
					} else {
						html += 'onclick="$(\'#'+id+'\').loctableSetFiltro({'+nameParam+': '+"'"+option.value+"'"+'});" ';
					}
					html += 'data-value="'+option.value+'">'+option.label+'</div>';
				});
				html += '</div>';
			} else if(dadosParam.tipo=='text'){
				cParam += 1;
				
				if(dadosParam.label!=''){
					html += '<div class="input-group">';
					html += '<span class="input-group-addon">'+dadosParam.label+'</span>';
					html += '<input id="edSelect'+id+'_'+nameParam+'" value="'+dadosParam.value+'" onChange=\"$(\'#'+id+'\').loctableSetFiltro({'+nameParam+': this.value});\" type="text" class="form-control" placeholder="'+dadosParam.placeholder+'">';
					html += '</div>';
				} else {
					html += '<input id="edSelect'+id+'_'+nameParam+'" value="'+dadosParam.value+'" onChange=\"$(\'#'+id+'\').loctableSetFiltro({'+nameParam+': this.value});\" type="text" class="form-control" placeholder="'+dadosParam.placeholder+'">';
				}
			}
			html += '</div>';
		});
		
		//---------------------//
		btn = {
				btClose : {
					iconLeft : 'fa fa-times',
					text : 'Cancelar',
					action : "$.staDialog('close');",
					type : 'danger'
			}
		};
		if(cParam!=0){
			btn['btPesquisar'] = {
				iconLeft : 'fa fa-search',
				text : 'Pesquisar',
				action : "$('#"+id+"').loctablePesquisar();return false;",
				type : 'primary'
			};
		}
		
		html += '<div style="clear:both;"></div>';
		html += '<div style="position:relative;margin:5px;" id="divSelect'+id+'_resultado"></div>';
		$.staDialog({
			'title' : params.titulo,
			'text' : html,
			'type' : 'default',
			'buttons' : btn,
			'showTitle' : true,
			'onOpen' : function () {
				var valor = $('#'+id).val();
				if(cParam==0 || valor!='')
					$('#'+id).loctablePesquisar();
			},
			'open' : true
		});
		$.loctableParams.configs[id] = params;
	 };

	
	$.selectentidadesParams = {
		configs: {},//$.selectentidadesParams.configs
		paramsDefault: {
			titulo : 'Selecione a Entidade',
			entidade : 1,//0 - Todos / 1 - Clientes / 2 - Fonec. / 3 - Func. / 4 - Parceiro / 5 - Transportadores
			consultaPor: 0,//0 - Nome / 1 - NomeFantasia
			selectEntidade: true,
			selectConsulta: true,
			page: 0,
			fncReturn : function (id,codigo,nome,nomefantasia) {//params.fncReturn
				 $('#'+id).val( codigo );
				 
				console.log(codigo,nome,nomefantasia);
				$.staDialog('close');
				return codigo;
			}
		}
	};
	$.fn.selectentidadesChange = function(codigo,nome,nomefantasia){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.selectentidadesParams.configs[id] == 'undefined'){
			 $.selectentidadesParams.configs[id] = $.selectentidadesParams.paramsDefault;		 		
		 }
		 params = $.selectentidadesParams.configs[id];
		 
		 if(typeof params.fncReturn =='function'){
			 return params.fncReturn(id,codigo,nome,nomefantasia);
		 } else {
			 return null;
		 }
	},
	$.fn.selectentidadesPesquisar = function (page){
		
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.selectentidadesParams.configs[id] == 'undefined'){
			 $.selectentidadesParams.configs[id] = $.selectentidadesParams.paramsDefault;		 		
		 }
		 params = $.selectentidadesParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		if (typeof page == 'undefined')
			page = 0;
			
		var dataSend = {
			page: page,
			tipoEntidade: params.entidade,
			tipoConsulta: params.consultaPor,
			q: $('#editSelectEntidade_consulta').val()
		};
		
		$.staAjaxJSON(url_raiz_empresa + 'cadastros/entidades/selecionaentidade', dataSend,
			{
				fncSuccess: function(dataResponse){ 
					//dataResponse.page
					//dataResponse.numPages
					//dataResponse.numTotalResults
					//dataResponse.itens
					var html = '';
					/*if(dataResponse.sql!="")
						html += '<div class="panel panel-default"><div class="panel-body">'+dataResponse.sql+'</div></div>';
					*/
					html += '<table class="table">';
					html += '<th>Cod.</th>';
					html += '<th>Nome</th>';
					html += '<th>Nome fantasia</th>';
					html += '<th title="Cliente">Clie.</th>';
					html += '<th title="Fornecedor">Forn.</th>';
					html += '<th title="Funcionário">Func.</th>';
					html += '<th title="Transportes">Transp.</th>';
					$.each(dataResponse.itens, function(index,item){						
						html += '<tr onclick="$(\'#'+id+'\').selectentidadesChange(\''+item.codigo+'\',\''+item.nome+'\',\''+item.nomefantasia+'\');">';
						html += '<td>'+item.codigo+'</td>';
						html += '<td class="text-primary" style="font-weight:bold;">'+item.nome+'</td>';
						html += '<td><span>'+item.nomefantasia+'</span></td>';
						html += '<td>';
						if(item.cliente)
							html += '<span class="label label-success" title="Cliente"><span class="fa fa-check"></span></span> ';
						else
							html += '<span class="label label-danger" title="Cliente"><span class="fa fa-times"></span></span> ';
						html += '</td><td>';
						if(item.fornecedor)
							html += '<span class="label label-success" title="Fornecedor"><span class="fa fa-check"></span></span> ';
						else
							html += '<span class="label label-danger" title="Fornecedor"><span class="fa fa-times"></span></span> ';
						html += '</td><td>';
						if(item.funcioario)
							html += '<span class="label label-success" title="Funcionário"><span class="fa fa-check"></span></span> ';
						else
							html += '<span class="label label-danger" title="Funcionário"><span class="fa fa-times"></span></span> ';
						html += '</td><td>';
						if(item.transporte)
							html += '<span class="label label-success" title="Transportes"><span class="fa fa-check"></span></span> ';
						else
							html += '<span class="label label-danger" title="Transportes"><span class="fa fa-times"></span></span> '
						
						html += '</td>';
						html += '</tr>';
					});
					html += '</table>';
					if(dataResponse.numPages>1){
						var i = dataResponse.page-3;
						if(i<0)
							i = 0;
						
						var pAnterior = parseInt(dataResponse.page)-1;
						if(pAnterior<0)
							pAnterior = 0;
						
						html += '<nav>';
						html += '<ul class="pagination">';
						//Anterior---
						html += '<li>';
						html += '<a href="#" onclick="$(\'#'+id+'\').selectentidadesPesquisar('+pAnterior+');return false;" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>';
						html += '</li>';
						
						//Paginas---
						for (ii = 0; ii < 7; ii++) {
							pV = parseInt(i+1);
							if(i<dataResponse.numPages){
								if(i==dataResponse.page){
									html += '<li class="active"><a href="#" onclick="return false;">'+pV+'</a></li>';
								} else {
									html += '<li><a href="#" onclick="$(\'#'+id+'\').selectentidadesPesquisar('+i+');return false;">'+pV+'</a></li>';									
								}							
								i++;
							} else {
								ii = 7;
							}
						}
						
						//Proximo---
						var pProximo = parseInt(dataResponse.page)+1;
						if(pProximo<dataResponse.numPages){
							html += '<li>';
							html += '<a href="#" onclick="$(\'#'+id+'\').selectentidadesPesquisar('+pProximo+');return false;" aria-label="Next">';
						} else {
							html += '<li class="disabled">';
							html += '<a href="#" onclick="return false;" aria-label="Next">';
						}						
						html += '<span aria-hidden="true">&raquo;</span>';
						html += '</a>';
						html += '</li>';
						
						html += '</ul>';
						html += '</nav> ';
						//html += parseInt(dataResponse.page+1)+' / '+dataResponse.numPages;						
					}
					$('#divSelectEntidade_resultado').html(html);
				},
				fncError: function(dataResponse){ 
					$('#divSelectEntidade_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+dataResponse.strError+'</div>');
				},
				fncFailed: function(xhr, ajaxOptions, thrownError){
					$('#divSelectEntidade_resultado').html('<div class="alert alert-danger"><strong>Error: </strong> '+thrownError+'</div>');
				}
			}			
		);
		$.selectentidadesParams.configs[id] = params;
	};
	$.fn.selectentidadesSetFiltro= function(parametros){
		id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.selectentidadesParams.configs[id] == 'undefined'){
			 $.selectentidadesParams.configs[id] = $.selectentidadesParams.paramsDefault;		 		
		 }
		 params = $.selectentidadesParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		
		//-----Tipo Entidades----//
		$('#btnGroupSelectEntidade_tipoEntidades > .btn-primary').removeClass('btn-primary').addClass('btn-default');
		$('#btnGroupSelectEntidade_tipoEntidades > .btn[data-value='+params.entidade+']').removeClass('btn-default').addClass('btn-primary');
		
		//-----Consulta por ------//
		$('#btnGroupSelectEntidade_consultaPor > .btn-primary').removeClass('btn-primary').addClass('btn-default');
		$('#btnGroupSelectEntidade_consultaPor > .btn[data-value='+params.consultaPor+']').removeClass('btn-default').addClass('btn-primary');
		
		$.selectentidadesParams.configs[id] = params;
	};
	/*$('#').selectentidadesSelecionar();*/
	$.fn.selectentidadesSelecionar= function (parametros) {	
		 id = $(this).attr('id');//id do elemento
		 
		 if (typeof $.selectentidadesParams.configs[id] == 'undefined'){
			 $.selectentidadesParams.configs[id] = $.selectentidadesParams.paramsDefault;		 		
		 }
		 params = $.selectentidadesParams.configs[id];
		 		 
		 if (typeof parametros != 'undefined')
			 $.extend(params, parametros);
		 
		 var html = '';
	
		//-----Tipo Entidades----//
		if(params.selectEntidade){
			html += '<div style="float:left;margin:5px;">';
			html += '<span style="font-weight:bold;">Tipo de entidade:</span><br /> ';
			html += '<div id="btnGroupSelectEntidade_tipoEntidades" class="btn-group">';//
			html += '<div class="btn btn-'+((params.entidade==0)?'primary':'default')+'" onclick="$(\'#'+id+'\').selectentidadesSetFiltro({entidade: 0 });" data-value="0">Todos</div>';
			html += '<div class="btn btn-'+((params.entidade==1)?'primary':'default')+'" onclick="$(\'#'+id+'\').selectentidadesSetFiltro({entidade: 1 });" data-value="1">Clientes</div>';
			html += '<div class="btn btn-'+((params.entidade==2)?'primary':'default')+'" onclick="$(\'#'+id+'\').selectentidadesSetFiltro({entidade: 2 });" data-value="2">Fornecedores</div>';
			html += '<div class="btn btn-'+((params.entidade==3)?'primary':'default')+'" onclick="$(\'#'+id+'\').selectentidadesSetFiltro({entidade: 3 });" data-value="3">Funcionários</div>';
			//html += '<div class="btn btn-'+((params.entidade==4)?'primary':'default')+'" onclick="$(\'#'+id+'\').selectentidadesSetFiltro({entidade: 4 });" data-value="4">Parceiros</div>';
			html += '<div class="btn btn-'+((params.entidade==5)?'primary':'default')+'" onclick="$(\'#'+id+'\').selectentidadesSetFiltro({entidade: 5 });" data-value="5">Transportadores</div>';
			html += '</div>';
			html += '</div>';
		}
		//--------------------//
		
		//-----Consulta por ------//
		if(params.selectConsulta){
			html += '<div style="float:left;margin:5px;\">';
			html += '<span style="font-weight:bold;">Consultar por:</span><br /> ';
			html += ' <div id="btnGroupSelectEntidade_consultaPor" class="btn-group">';
			html += '<div class="btn btn-'+((params.consultaPor==0)?'primary':'default')+'" onclick="$(\'#'+id+'\').selectentidadesSetFiltro({consultaPor: 0 });" data-value="0">Nome</div>';
			html += '<div class="btn btn-'+((params.consultaPor==1)?'primary':'default')+'" onclick="$(\'#'+id+'\').selectentidadesSetFiltro({consultaPor: 1 });" data-value="1">Nome Fantasia</div>';
			html += '</div>';
			html += '</div>';
		}
		//--------------------//
		
		//-----Consulta ------//
		//html += '<div style="position:relative;"><input type="text" id="edformcod_cvf" /></div>';
		html += '<div style="clear:both;"></div>';
		html += '<div style="position:relative;">';
		html += '<div class="input-group">';
		html += '<input id="editSelectEntidade_consulta" type="text" class="form-control" placeholder="Digite o código ou Nome">';
		html += '<span class="input-group-btn">';
		html += '<button class="btn btn-default" onClick="$(\'#'+id+'\').selectentidadesPesquisar();return false;" type="button">Pesquisar</button>';
		html += '</span>';
		html += '</div>';
		html += '</div>';
		//---------------------//
		html += '<div style="clear:both;"></div>';
		html += '<div style="position:relative;margin:5px;" id="divSelectEntidade_resultado"></div>';
		$.staDialog({
			'title' : params.titulo,
			'text' : html,
			'type' : 'default',
			'buttons' : {
				btClose : {
					iconLeft : 'fa fa-times',
					text : 'Cancelar',
					action : "$.staDialog('close');",
					type : 'danger'
				}
			},
			'showTitle' : true,
			'onOpen' : function () {
				var valor = $('#'+id).val();
				console.log(valor,id);
				$('#editSelectEntidade_consulta').val( valor );
				if(valor!='')
					$('#'+id).selectentidadesPesquisar();				
			},
			'open' : true
		});
		$.selectentidadesParams.configs[id] = params;
	 };
})(jQuery);

var lastTimeAtalhoKeyDown = 0;
$(document).keydown(function(e){
	var segTimeout = 1;
	var d = new Date();
	if(lastTimeAtalhoKeyDown<d.getTime()-(segTimeout*1000)){
		$.atalhoClearKeys();
	}
	
	lastTimeAtalhoKeyDown = d.getTime();
	if(typeof e.keyCode != 'undefined'){
		keyCode = e.keyCode;
	} else {
		keyCode = e.wich;
	}
	keystrokes[keyCode] = true;
	
	var retorno = $.execAtalho();
	
	return retorno;
});
$(document).keyup(function(e){
	if(typeof e.keyCode != 'undefined'){
		keyCode = e.keyCode;
	} else {
		keyCode = e.wich;
	}
	keystrokes[keyCode] = false;
});
//renato
function excluirEntidade(id){
    $.get(url_raiz_empresa+"index/index/excluirEntidade", {id:id}, function(resposta){
        window.location.reload();
    });
}
//renato






