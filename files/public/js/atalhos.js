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
var atalhosTemp = {};
var keystrokes = {};
(function( $, undefined ) {
	$.clearAtalhos = function(){
		var indicec = Object.keys(atalhosTemp).length;
		atalhosTemp[indicec] = atalhos;
		atalhos = {};
		//console.log('clearAtalhos',indicec);
	};
	$.resetAtalhos = function(){
		var newAtalhosTemp = {};
		var indicec = Object.keys(atalhosTemp).length;
		if(indicec==0){
			atalhos = {};
		} else {
			atalhos = atalhosTemp[indicec-1];
			delete atalhosTemp[indicec-1];
		}
		console.log('clearAtalhos',indicec-1,atalhosTemp[indicec-1]);
	};
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