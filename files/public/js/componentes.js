function newNameElement(prexif){
	nameElement = '';
	var i = 0;
	if($('#'+prexif).size()==0){
		nameElement = prexif;	
	}
	while(nameElement==''){
		if($('#'+prexif+"_"+i).size()==0){
			nameElement = prexif+"_"+i;	
		}
			
		i = parseInt(i+1);
		
	}
	return nameElement;
}
var componentes = {
	none: {
		name: 'None',
		params: {},
		getElement: function(){
			return '';
		}
	},
	alert: {
		name: 'Alert',
		params: {
			_id: {
				type:'text',
				value: '',
			},
			type: {
				type:'select',
				value: 'success',
				options:['danger','info','success','warning']
			},
			content: {
				type:'textarea',
				value: 'Insira o conteudo aqui'
			}
		},
		getElement: function(isNew){
			var type = 'alert';
			if(typeof isNew!='undefined' && isNew==true){
				componentes[type].params._id.value = newNameElement(type);
				componentes[type].params.type.value = 'success';
				componentes[type].params.content.value = 'Insira o conteudo aqui';
			}
			var ret = '';
						
			ret = ret+'<div class="alert alert-'+componentes[type].params.type.value+' componentbox" data-componentType="alert">';
			ret = ret+componentes[type].params.content.value;
			
			$.each(componentes[type].params, function(indice,value){
				ret = ret+'<div class="componentbox-param" data-name="'+indice+'">'+value.value+'</div>';
			});
			ret = ret+'</div>';
			
			return ret;
		}
	},
	div: {
		name: 'Div',
		params: {
			_id: {
				type:'text',
				value: '',
			},
			_class: {
				type:'text',
				value: '',
			},
			_style: {
				type:'text',
				value: '',
			},
			content: {
				type:'textarea',
				value: 'Insira o conteudo aqui'
			}
		},
		getElement: function(isNew){
			var type = 'div';
			_class = ' selected';
			if(typeof isNew!='undefined' && isNew==true){
				componentes[type].params._id.value = newNameElement(type);
				componentes[type].params._class.value = '';
				componentes[type].params._style.value = '';
				componentes[type].params.content.value = 'Insira o conteudo aqui';
				_class = '';
			}
			var ret = '';
						
			ret = ret+'<div id="'+componentes[type].params._id.value+'" data-componentType="div" class="componentbox'+_class+' '+componentes[type].params._class.value+'" style="'+componentes[type].params._style.value+'">';
			ret = ret+componentes[type].params.content.value;
			
			$.each(componentes[type].params, function(indice,value){
				ret = ret+'<div class="componentbox-param" data-name="'+indice+'">'+value.value+'</div>';
			});
			ret = ret+'</div>';
			
			return ret;
		}
	},
	labelInput: {
		name: 'Label+Input',
		params: {
			_id: {
				type:'text',
				value: '',
			},
			_class: {
				type:'text',
				value: '',
			},
			_style: {
				type:'text',
				value: '',
			},
			_type: {
				type:'text',
				value: '',
				options: ['text','password','email']
			},
			_name: {
				type:'text',
				value: 'name'
			},
			_value: {
				type:'text',
				value: 'Value'
			},
			_label: {
				type:'text',
				value: 'Label'
			}
		},
		getElement: function(isNew){
			var type = 'labelInput';
			_class = ' selected';
			if(typeof isNew!='undefined' && isNew==true){
				componentes[type].params._id.value = newNameElement(type);
				componentes[type].params._class.value = '';
				componentes[type].params._style.value = 'width:100%;margin:5px auto;';
				componentes[type].params._type.value = 'text';
				componentes[type].params._name.value = 'name';
				componentes[type].params._value.value = 'Value';
				componentes[type].params._label.value = 'Label';
				_class = '';
			}
			var ret = '';
						
			ret = ret+'<div id="'+type+'_'+componentes[type].params._id.value+'" data-componentType="'+type+'" class="componentbox'+_class+' '+componentes[type].params._class.value+'" style="'+componentes[type].params._style.value+'">';
			ret = ret+'<label for=\"'+componentes[type].params._id.value+'\">'+componentes[type].params._label.value+':</label> ';
			ret = ret+'<input id=\"'+componentes[type].params._id.value+'\" type=\"'+componentes[type].params._type.value+'\" value=\"'+componentes[type].params._value.value+'\" name=\"'+componentes[type].params._name.value+'\" />';			
			
			$.each(componentes[type].params, function(indice,value){
				ret = ret+'<div class="componentbox-param" data-name="'+indice+'">'+value.value+'</div>';
			});
			ret = ret+'</div>';
			
			return ret;
		}
	}
};
/*
$(document).keydown(function(e){
	console.log('down',e.wich,e.keyCode);
});
$(document).keyup(function(e){
	console.log('up',e.wich,e.keyCode);
});*/