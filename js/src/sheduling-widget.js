var CountryfestShedulingWidget = {
	WIDGET_CLASS: 'js-sheduling-widget',

	SHOWBOX_FORM_CLASS: 'js-sheduling-widget--new-item',
	SHOWBOX_NEW_CLASS: 'new',
	SHOWBOX_FORM_FIELD_CLASS: 'js-sheduling-widget--new-item--field',
	SHOWBOX_FORM_ADD_BUTTON_CLASS: 'js-sheduling-widget--add-button',

	SHOWBOX_LIST_CLASS: 'js-sheduling-widget--item-wrapper',
	SHOWBOX_CLASS: 'js-sheduling-widget--item',
	SHOWBOX_DELETE_BUTTON_CLASS: 'js-sheduling-widget--item--delete-button',
	SHOWBOX_HIDDEN_CONTAINER: 'js-sheduling-widget--hidden-container',
	SHOWBOX_SHOW_ON_PROGRAM_ICON: 'js-sheduling-widget--item--yesno',

	YES_CLASS: 'dashicons-yes',
	NO_CLASS: 'dashicons-no',

	EVENT_HANDLER: {
		addNewShowbox: function(event){
			// find closest this.SHOWBOX and execute its create_new_showbox event.
			var fields = this.closest(
				'.' + CountryfestShedulingWidget.SHOWBOX_FORM_CLASS
			).getElementsByClassName(CountryfestShedulingWidget.SHOWBOX_FORM_FIELD_CLASS);

			var fieldValueMap = {};

			for(var i = fields.length - 1; i >= 0; i--){
				if(!fields[i].hasAttribute('data-name')){
					continue;
				}

				var fieldName = fields[i].getAttribute('data-name');

				if(fields[i].type === 'checkbox'){
					// show_on_program_page
					fieldValueMap[fieldName] = fields[i].checked;
					continue;
				}

				fieldValueMap[fieldName] = Number(fields[i].value);
			}

			var duration = {
				minutes: Number(fieldValueMap.duration__minutes),
				hours: Number(fieldValueMap.duration__hours),
				days: Number(fieldValueMap.duration__days)
			};

			duration.totalMinutes = duration.minutes + duration.hours * 60 + duration.days * 60 * 24;

			if(duration.minutes === 0 && duration.hours === 0 && duration.days === 0){
				alert('Duration cannot be zero');
				return;
			}

			var date = new Date();
			date.setUTCFullYear(fieldValueMap.start__year);
			date.setUTCMonth(fieldValueMap.start__month - 1);  // setUTCMonth expect month index to start from 0 instead 1!
			date.setUTCDate(fieldValueMap.start__day);  // setUTCDate - Date not Day!!!
			date.setUTCHours(fieldValueMap.start__hour);
			date.setUTCMinutes(fieldValueMap.start__minute)
			date.setUTCSeconds(0);
			date.setUTCMilliseconds(0);

			var event = new CustomEvent(
				'create_new_showbox', {'detail': {
						'startDate': date,
						'duration': duration,
						'showOnProgramPage': fieldValueMap.show_on_program_page
					}
				}
			);
			this.closest('.' + CountryfestShedulingWidget.WIDGET_CLASS).dispatchEvent(event);
		},
		deleteShowbox: function(event){
			// launches showbox delete
			var showbox = this.closest('.' + CountryfestShedulingWidget.SHOWBOX_CLASS);
			var showboxList = showbox.closest('.' + CountryfestShedulingWidget.SHOWBOX_LIST_CLASS);
			showbox.parentNode.removeChild(showbox);

			// Dispatch recalculate_field_indexes event.
			showboxList.dispatchEvent(new CustomEvent('recalculate_field_indexes'));
		},
		createNewShowbox: function(event){	
			// Lets assume we will always get correct args here
			// Lets create dummy copy of showbox, which will be later polluted by fields.
			var newShowBox = this.getElementsByClassName(
				CountryfestShedulingWidget.SHOWBOX_HIDDEN_CONTAINER
			)[0].getElementsByClassName(CountryfestShedulingWidget.SHOWBOX_CLASS)[0].cloneNode(true);

			newShowBox.className += ' ' + CountryfestShedulingWidget.SHOWBOX_NEW_CLASS;

			var hiddenFieldsMap = {
				simple_lineup__start: event.detail.startDate.toISOString(),  // Backend friendly standardized format.
				simple_lineup__duration: event.detail.duration.totalMinutes,
				simple_lineup__show_on_program_page: Number(event.detail.showOnProgramPage)
			};

			// Creates blank fake hidden field
			var blankHiddenField = document.createElement('input');
			blankHiddenField.type = 'hidden';

        	// Append all blank fields to newly created Showbox. So form will reflect added element.
        	for(var fieldName in hiddenFieldsMap){
        		var newHiddenField = blankHiddenField.cloneNode(true);
        		newHiddenField.name = fieldName;
        		newHiddenField.value = hiddenFieldsMap[fieldName];
				newShowBox.appendChild(newHiddenField);        		
        	}

        	CountryfestShedulingWidget.setUpShowbox(newShowBox, event.detail.startDate, event.detail.duration, event.detail.showOnProgramPage);

        	// Now append showbox to showbox wrapper.
        	var showBoxList = this.getElementsByClassName(CountryfestShedulingWidget.SHOWBOX_LIST_CLASS)[0];
			showBoxList.insertBefore(newShowBox, showBoxList.firstChild);
			showBoxList.dispatchEvent(new CustomEvent('recalculate_field_indexes'));
		},
		recalculateFieldIndexes: function(event){
			// This assigns indexes to all showboxes

			var showboxes = this.getElementsByClassName(CountryfestShedulingWidget.SHOWBOX_CLASS);

			var fieldRE = /^[\w_\d]+/;

			for (var i = showboxes.length - 1; i >= 0; i--) {
				var shoboxInputs = showboxes[i].getElementsByTagName('input');

				for (var x = shoboxInputs.length - 1; x >= 0; x--){
					// We have two possible formats here - `fieldname` and `fieldname[i]`.
					var fieldName = shoboxInputs[x].getAttribute('name').match(fieldRE);
					shoboxInputs[x].setAttribute('name', fieldName + '[' + i + ']');
				}

			}			
		}
	},
	formatShowBoxStartDate: function(date){
		return date;
	}, 
	numberWithPlaces: function(num, places){
		for(var output = String(num); output.length < places; output = '0' + output){};
		return output;
	},
	assignEventToAll: function(className, eventName, eventHandler){
		Array.from(document.getElementsByClassName(className)).forEach(function(item){
			item.addEventListener(eventName, eventHandler);
		});
	},
	setUpShowbox: function(showbox, startDate, duration, showOnProgramPage){
		showbox.getElementsByClassName(
			CountryfestShedulingWidget.SHOWBOX_DELETE_BUTTON_CLASS
		)[0].addEventListener('click', CountryfestShedulingWidget.EVENT_HANDLER.deleteShowbox);
		
		var formattedTime = String(startDate.getUTCHours()) + ':' + String(startDate.getUTCMinutes());
		var formattedDate = [startDate.getUTCDate(), startDate.getUTCMonth() + 1, startDate.getUTCFullYear()].join('.');
		var outputDuration = [];

		if(duration.days){
			outputDuration.push(String(duration.days) + 'd');
		}

		if(duration.hours){
			outputDuration.push(String(duration.hours) + 'h');
		}

		outputDuration.push(String(duration.minutes) + 'm');

		showbox.getElementsByClassName('js-sheduling-widget--item--duration')[0].innerHTML = outputDuration.join(' ');
		showbox.getElementsByClassName('js-sheduling-widget--item--start')[0].innerHTML = formattedTime + ' - ' + formattedDate;

		var showOnProgramPageIcon = showbox.getElementsByClassName(CountryfestShedulingWidget.SHOWBOX_SHOW_ON_PROGRAM_ICON)[0];
		var iconClasses = showOnProgramPageIcon.className.split(' ');
		var classesToRemove = [CountryfestShedulingWidget.YES_CLASS, CountryfestShedulingWidget.NO_CLASS];

		for(var i = classesToRemove.length - 1; i >= 0; i--){
			var classIndex = iconClasses.indexOf(classesToRemove[i]);
			
			if(classIndex === -1){
				continue;
			}

			iconClasses.splice(classIndex, 1);
		}

		iconClasses.push(showOnProgramPage ? CountryfestShedulingWidget.YES_CLASS : CountryfestShedulingWidget.NO_CLASS); 
		showOnProgramPageIcon.className = iconClasses.join(' ');

	},
	registerEvents: function(){
		this.assignEventToAll(this.SHOWBOX_FORM_ADD_BUTTON_CLASS, 'click', this.EVENT_HANDLER.addNewShowbox);
		this.assignEventToAll(this.SHOWBOX_DELETE_BUTTON_CLASS, 'click', this.EVENT_HANDLER.deleteShowbox);
		this.assignEventToAll(this.WIDGET_CLASS, 'create_new_showbox', this.EVENT_HANDLER.createNewShowbox);
		this.assignEventToAll(this.SHOWBOX_LIST_CLASS, 'recalculate_field_indexes', this.EVENT_HANDLER.recalculateFieldIndexes);
	},
	init: function(){
		this.registerEvents();
	}
};

window.addEventListener('DOMContentLoaded', function(event){
	CountryfestShedulingWidget.init();
});
