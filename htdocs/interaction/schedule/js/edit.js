/**
 * Javascript for the survey/questionnaire tabbed form
 * @source: http://gitorious.org/mahara/mahara
 *
 * @licstart
 * Copyright (C) 2006-2011  Catalyst IT Ltd
 *
 * The JavaScript code in this page is free software: you can
 * redistribute it and/or modify it under the terms of the GNU
 * General Public License (GNU GPL) as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option)
 * any later version.  The code is distributed WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.
 *
 * As additional permission under GNU GPL version 3 section 7, you
 * may distribute non-source (e.g., minimized or compacted) forms of
 * that code without the copy of the GNU GPL normally required by
 * section 4, provided you include this license notice and a URL
 * through which recipients can access the Corresponding Source.
 * @licend
 */


function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}
function onDateChange(){
	var newdate;
	var	enddate;
	if(document.getElementById('addevent_startdate')){
		newdate = new Date(document.getElementById('addevent_startdate').value);
		enddate = new Date(document.getElementById('addevent_enddate').value);
	}
	if(document.getElementById('editevent_startdate')){
		newdate = new Date(document.getElementById('editevent_startdate').value);
		enddate = new Date(document.getElementById('editevent_enddate').value);
	}
	//if the newdate is greater than the enddate then we set a new end date
	if(newdate.getTime() > enddate.getTime()){
		newdate.setHours(newdate.getHours()+3);
		var newstring = newdate.getFullYear()+'/'+String(addZero(newdate.getMonth()+1))+'/'+addZero(newdate.getDate())+' '+String(addZero(newdate.getHours()))+':'+String(addZero(newdate.getMinutes()));
		if(document.getElementById('addevent_startdate')){
			document.getElementById('addevent_enddate').value = newstring;
		}
		if(document.getElementById('editevent_startdate')){
			document.getElementById('editevent_enddate').value = newstring;
		}	
	}
}


	function addOnChange(){
/*		var input = jQuery("input#addevent_startdate");
		
		alert(input);
/*		connect($('addevent_startdate'), 'onSelect', function(e) {
			alert('bob');
		});
		$('addevent_startdate').click(function() {
  alert('bob');
});
		$('addevent_startdate').innerHTML = 'bob';
/*		$('addevent_startdate').change(function(){
			alert('bob');
		});*/
	}


// Stuff

addLoadEvent(function() {
    	addOnChange();


});

