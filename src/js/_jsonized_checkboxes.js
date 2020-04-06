// This is very bad hack for checkboxes... but using WordPress requires the most harshest methods available.
export var JsonizedCheckboxes = {
	
	WRAPPER_CLASS: 'js-frontend-jsonizer',
	OPTION_FIELD_CLASS: 'js-frontend-jsonizer--option',
	JSON_FIELD_CLASS: 'js-frontend-jsonizer--json-field',

	serializeOptions: function(wrapper){
		var options = wrapper.getElementsByClassName(this.OPTION_FIELD_CLASS);
		var output = [];
		for(var i = options.length - 1; i >= 0; i--){
			if(options[i].checked){
				output.push(options[i].value);
			}
		}
		wrapper.getElementsByClassName(this.JSON_FIELD_CLASS)[0].value = JSON.stringify(output);
	},
	registeEvents: function(){
		var doc = document;
		var options = doc.getElementsByClassName(this.OPTION_FIELD_CLASS);
		for(var i = options.length - 1; i >= 0; i--){
			options[i].addEventListener('change', function($event){
				JsonizedCheckboxes.serializeOptions(this.closest('.' + JsonizedCheckboxes.WRAPPER_CLASS));
			});
		}
	},
	init: function(){
		window.addEventListener('load', function(event) {
			JsonizedCheckboxes.registeEvents()
		});
	}
};
