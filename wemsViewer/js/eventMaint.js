/*
 * FUNCTIONS
 * 
 * */ 

function StationsMap(map){
	var openUrl = '';
	
 	if(map == 'sta') openUrl = 'WEMS_GIS.php';
 	if(map == 'sen') openUrl = 'http://www.Sentinelfm.com/login.aspx';
 	
 	window.open(openUrl);
}

function getDBtype(pre){
	var type = "S";
	
	switch(pre){
		case "l":
			type = "S";
		break;
		case "i":
			type = "I";
		break;
		case "pl":
			type = "P";
		break;
		case "si":
			type = "T";
		break;
	}
	
	return type;
}

//------------------------------------------------------------------------------------------------------------------------------

function getEmployees(pre){
	var conponent = document.getElementById(pre+'Conponent').value;
	var loc = document.getElementById(pre+'Loc').value;
	var eventId = window.eventId;
	var locCD = 0;
	
	var type = getDBtype(pre);

	if (window.XMLHttpRequest){
		// If IE7, Mozilla, Safari, etc: Use native object
		var client = new XMLHttpRequest();
	}else{
		if (window.ActiveXObject){
			// ...otherwise, use the ActiveX control for IE5.x and IE6
			var client = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
  
	client.onreadystatechange = function() {GangListDetailhandler(client, pre)};
	client.open("GET", "getGangList.php?loc=" + loc + "&eventId=" + eventId + "&conponent="+conponent+"&locCD="+locCD);
	
	//client.open("GET", "getGangListNoConponent.php?loc=" + loc + "&eventId=" + eventId);
	client.send("");
} //getData() 


/*
 * GANGS OF NEW YORK MTA
 * */

function GangListDetailhandler(obj, pre){
	var forman = document.getElementById(pre+'Forman');
	var location = document.getElementById(pre+'Loc').value;
	var conponent = document.getElementById(pre+'Conponent').value;
   
	forman.options.length = 0;
  
	if(obj.readyState == 4 && obj.status == 200){
		var val = JSON.parse( obj.responseText );

		for(var i = 0; i < val.length; i++){
			var opt = document.createElement('option');
			  
			opt.innerHTML = val[i].NAME;
			opt.value = val[i].FORMANID;

			var assignLoc = document.createElement('text');
			var assignLoc = val[i].LOCATION;
  		  
			if(assignLoc == conponent){
				opt.setAttribute("selected","selected");
				forman.appendChild(opt);
			}else{
				forman.appendChild(opt);  	
			}
		} //end for(var i = 0; i < val.length; i++)
	} // end if(obj.readyState == 4 && obj.status == 200)
} //GangListDetailhandler(obj)

function getGangData(){
	var param = document.getElementById('forman').value;
	 
	var eventId = window.eventId;
	
	if(window.XMLHttpRequest){
		// If IE7, Mozilla, Safari, etc: Use native object
		var client = new XMLHttpRequest();
	}else{
		if (window.ActiveXObject){
			// ...otherwise, use the ActiveX control for IE5.x and IE6
			var client = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	
	client.onreadystatechange = function() {gangHandler(client)};
	client.open("GET", "getGangInfo.php?param=" + param + "&eventId=" + eventId);
	client.send("");
} //getData() 

function gangHandler(obj){
	var empNum = document.getElementById('gEmpNum');
	var comments = document.getElementById('gHistory');
	var status = document.getElementById('gStatus');
	var dteTm = document.getElementById('gStartTm');
	var gangButton = document.getElementById('gangEnterUpdate');
  
	empNum.value = "";
	comments.value = "";
	//gangButton.value = "Enter Gang";
	  
	if(obj.readyState == 4 && obj.status == 200){
		var val = JSON.parse( obj.responseText );

		for(var i = 0; i < val.length; i++){
			var txtNew = document.createElement('text');
			
			//alert(val[i].EMP_ASSIGNED);
			//alert(val[i].COMMENTS);
			
			txtNew.text = val[i].EMP_ASSIGNED;
			empNum.value = txtNew.text;
			
			txtNew.text = val[i].COMMENTS;
			comments.value = txtNew.text;
			
			txtNew.text = val[i].BUTTON;
			gangButton.value = txtNew.text;
			
			txtNew.text = val[i].STATUS;
			status.value = txtNew.text;

			// txtNew.text = val[i].DATETIME;
			// dteTm.value = txtNew.text;
     	} //end for(var i = 0; i < val.length; i++)
	} // end if(obj.readyState == 4 && obj.status == 200)
} //handler(obj)


/*
 * COMPONENTS
 * */

function getConponentDetails(pre){	
	switch(pre){
		case "pl":
		case "i":
			//url = "getInterlockingInfo.php";
			url = "getStationInfoNoConponent.php";
		break;
		case "l":
		default:
			url = "getStationInfo.php";
		break;
	}
	
	var param = document.getElementById( pre+'Conponent' ).value;
	
	var eventId = window.eventId;
	 
	if(window.XMLHttpRequest){
		// If IE7, Mozilla, Safari, etc: Use native object
		var client = new XMLHttpRequest();
	}else{
		if(window.ActiveXObject){
			// ...otherwise, use the ActiveX control for IE5.x and IE6
			var client = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
     
	client.onreadystatechange = function() {conponentDetailhandler(pre, client)};
	client.open("GET", url + "?param=" + param + "&eventId=" + eventId);
	client.send("");
} //getData() 

function conponentDetailhandler(pre, obj) {
	var status = document.getElementById(pre+'Status');
	var comments = document.getElementById(pre+'History');
	var forman = document.getElementById(pre+'Forman');
	//forman.options.length = 0;
	var pass = document.getElementById(pre+'PassNum');
	//var bags = document.getElementById(pre+'NumBags');

	var gangButton = document.getElementById('gangEnterUpdate');

	var downloadFile = document.getElementById(pre+'DownloadFile');
	downloadFile.options.length = 0;

	if(obj.readyState == 4 && obj.status == 200) {
		var val = JSON.parse( obj.responseText );

		for(var i = 0; i < val.length; i++) {
			var txtNew = document.createElement('text');

			txtNew.text = val[i].STATUS;
			status.value = txtNew.text;

			txtNew.text = val[i].COMMENTS;
			comments.value = txtNew.text;

			txtNew.text = val[i].PASS;
			
			if(pass){
				pass.value = txtNew.text;
			}

			var doctxt = txtNew.text;

			//-------------------------------
			//Grab all the attached documents and list in a option	
			txtNew.text = val[i].SUPPORTDOCS;

			var doctxt = txtNew.text;

			var docArray = new Array();

			if(doctxt && doctxt.length > 0){
				docArray = doctxt.split(",");
			}

			for(var x = 0; x < docArray.length; x++) {
				var opt = docArray[x];
				var el = document.createElement("option");

				el.innerHTML = opt;
				el.value = opt;
				downloadFile.appendChild(el);
			}
			
			//---------------------------------

			txtNew.text = val[i].GANG;
			forman.value = txtNew.text;

			// txtNew.text = val[i].BAGS;
			// bags.value = txtNew.text;  
		} //end for(var i = 0; i < val.length; i++)
	} // end if(obj.readyState == 4 && obj.status == 200)
} //conponentDetailhandler(obj)

//THIS WILL FILL THE SELECT BOX WITH THE CONPONENT LIST.  DATA FILL WILL BE ON THE CONPONENT CLICK EVENT 
function getConponentData(pre){	
	var type = getDBtype(pre);

	var param = document.getElementById(pre + 'Loc').value;

	if (window.XMLHttpRequest){
		var client = new XMLHttpRequest();
	}else{
		if (window.ActiveXObject){
			var client = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}

	client.onreadystatechange = function() {conponentHandler(client, pre)};
	client.open("GET", "getConponentInfo.php?param=" + param + "&type=" + type);
	client.send("");
} // getConponentData() 

function conponentHandler(obj, pre){
	var status = document.getElementById(pre + 'Status');
	var forman = document.getElementById(pre + 'Forman');
	var pass = document.getElementById(pre + 'PassNum');
	//var bags = document.getElementById('lNumBags');
	var comments = document.getElementById(pre + 'History');
	   
	var chkBox = document.getElementById(pre + 'AllConponents');
	chkBox.checked = false;

	var txtNew = document.createElement('text');

	txtNew.text = "";
	status.value = txtNew.text;
	
	txtNew.text = "";
	forman.value = txtNew.text;

	txtNew.text = "";
	
	if( pass && pass !== undefined && pass !== null ){
		pass.value = txtNew.text;
	}
	
	// txtNew.text = "";
	// bags.value = txtNew.text;
	
	txtNew.text = "";
	comments.value = txtNew.text;

	var lConponent = document.getElementById(pre + 'Conponent');
	
	document.getElementById(pre + 'Conponent').options.length = 0;
	  
	var length = lConponent.options.length;
	for (i = 0; i < length; i++) {
		lConponent.options[i] = null;
	}

	if(obj.readyState == 4 && obj.status == 200){
		var val = JSON.parse( obj.responseText );
  	
		for (var i = 0; i < val.length; i++){
			var opt = document.createElement('option');
			
			opt.innerHTML = val[i].FULLNAME;
			opt.value = val[i].CTID;
			lConponent.appendChild(opt);
		}
	} // end if(obj.readyState == 4 && obj.status == 200)
} //handler conponentHandler(obj)


/*
 * INTERLOCKINGS
 * */

function getILEmployees(){
	//var loc = document.getElementById('iConponent').value;
	var loc = document.getElementById('iLoc').value;
	//alert(loc);
	var eventId = window.eventId;

	if(window.XMLHttpRequest) {
		// If IE7, Mozilla, Safari, etc: Use native object
		var client = new XMLHttpRequest();
	} else {
		if(window.ActiveXObject) {
			// ...otherwise, use the ActiveX control for IE5.x and IE6
			var client = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}

	// var url = "getCatInfo.php?param=" + param;
	// var url = "getCatInfo.php";
	//client.open("GET", url, true);

	client.onreadystatechange = function () {
		ilGangListDetailhandler(client)
	};
	//client.open("GET", "getGangList.php?loc=" + loc + "&eventId=" + eventId + "&locCd=" + locCD);
	client.open("GET", "getGangListNoConponent.php?loc=" + loc + "&eventId=" + eventId);
	client.send("");
} //getData getILEmployees() 

function ilGangListDetailhandler(obj) {
	var iforman = document.getElementById('iForman');
	var location = document.getElementById('iLoc').value;

	iforman.options.length = 0;

	if(obj.readyState == 4 && obj.status == 200) {

		var val = JSON.parse( obj.responseText );

		for(var i = 0; i < val.length; i++) {
			var opt = document.createElement('option');

			opt.innerHTML = val[i].NAME;
			opt.value = val[i].FORMANID;
			var assignLoc = document.createElement('text');
			var assignLoc = val[i].LOCATION;

			if(assignLoc == location) {
				opt.setAttribute("selected", "selected");
				iforman.appendChild(opt);

				//alert(assignLoc);
				//alert(location);
			} else {
				iforman.appendChild(opt);
				//alert(assignLoc);
			}
		} //end for(var i = 0; i < val.length; i++)
	} // end if(obj.readyState == 4 && obj.status == 200)
} //handler ilGangListDetailhandler(obj)

function getILConponentDetails() {
	var loc = document.getElementById('iLoc').value;

	var eventId = window.eventId;

	if(window.XMLHttpRequest) {
		// If IE7, Mozilla, Safari, etc: Use native object
		var client = new XMLHttpRequest();
	} else {
		if(window.ActiveXObject) {
			// ...otherwise, use the ActiveX control for IE5.x and IE6
			var client = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}

	// var url = "getCatInfo.php?param=" + param;
	// var url = "getCatInfo.php";
	//client.open("GET", url, true);
	//alert(param);
	client.onreadystatechange = function () {
		ILconponentDetailhandler(client)
	};
	client.open("GET", "getStationInfoNoConponent.php?loc=" + loc + "&eventId=" + eventId);
	client.send("");
} //getData getILConponentDetails()
                      
function ILconponentDetailhandler(obj) {
	var status = document.getElementById('iStatus');
	var comments = document.getElementById('iHistory');

	//var forman = document.getElementById('iForman');
	//forman.options.length = 0;
	//var pass = document.getElementById('lPassNum');
	//var bags = document.getElementById('lNumBags');

	var downloadFile = document.getElementById('ilDownloadFile');
	downloadFile.options.length = 0;

	if(obj.readyState == 4 && obj.status == 200) {
		var val = JSON.parse( obj.responseText );

		for(var i = 0; i < val.length; i++) {

			var txtNew = document.createElement('text');

			//alert(val[i].STATUS);
			//alert(val[i].COMMENTS);

			txtNew.text = val[i].STATUS;
			status.value = txtNew.text;

			txtNew.text = val[i].COMMENTS;
			comments.value = txtNew.text;

			//txtNew.text = val[i].GANG;
			//forman.value = txtNew.text;


			//-------------------------------
			//Grab all the attached documents and list in a option	
			// alert(val[i].SUPPORTDOCS);
			txtNew.text = val[i].SUPPORTDOCS;

			var doctxt = txtNew.text;

			var docArray = new Array();

			docArray = doctxt.split(",");

			for(var x = 0; x < docArray.length; x++) {

				var opt = docArray[x];
				var el = document.createElement("option");

				el.innerHTML = opt;
				el.value = opt;
				downloadFile.appendChild(el);
			}
			//---------------------------------
		} //end for(var i = 0; i < val.length; i++)
	} // end if(obj.readyState == 4 && obj.status == 200)
} //handler ILconponentDetailhandler(obj)


 /*
  * PARKING LOTS
  * */

function getPLEmployees() {
	var loc = document.getElementById('plLoc').value;

	var eventId = window.eventId;

	if(window.XMLHttpRequest) {
		// If IE7, Mozilla, Safari, etc: Use native object
		var client = new XMLHttpRequest();
	} else {
		if(window.ActiveXObject) {
			// ...otherwise, use the ActiveX control for IE5.x and IE6
			var client = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}

	// var url = "getCatInfo.php?param=" + param;
	// var url = "getCatInfo.php";
	//client.open("GET", url, true);

	client.onreadystatechange = function() {
		plGangListDetailhandler(client)
	};
	client.open("GET", "getGangListNoConponent.php?loc=" + loc + "&eventId=" + eventId);
	client.send("");
} //getData getPLEmployees()
         
function plGangListDetailhandler(obj) {
	var plforman = document.getElementById('plForman');
	var location = document.getElementById('plLoc').value;

	plforman.options.length = 0;

	if(obj.readyState == 4 && obj.status == 200) {
		var val = JSON.parse( obj.responseText );

		for(var i = 0; i < val.length; i++) {
			var opt = document.createElement('option');

			opt.innerHTML = val[i].NAME;
			opt.value = val[i].FORMANID;
			var assignLoc = document.createElement('text');
			var assignLoc = val[i].LOCATION;

			if(assignLoc == location) {
				opt.setAttribute("selected", "selected");
				plForman.appendChild(opt);

				//alert(assignLoc);
				//alert(location);
			} else {
				plForman.appendChild(opt);
				//alert(assignLoc);
			}
		} //end for(var i = 0; i < val.length; i++)
	} // end if(obj.readyState == 4 && obj.status == 200)
} //handler plGangListDetailhandler(obj)
                
function getPLConponentDetails(){
	var loc = document.getElementById('plLoc').value;

	var eventId = window.eventId;

	var downloadFile = document.getElementById('plDownloadFile');
	downloadFile.options.length = 0;

	if(window.XMLHttpRequest) {
		// If IE7, Mozilla, Safari, etc: Use native object
		var client = new XMLHttpRequest();
	} else {
		if(window.ActiveXObject) {
			// ...otherwise, use the ActiveX control for IE5.x and IE6
			var client = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}

	// var url = "getCatInfo.php?param=" + param;
	// var url = "getCatInfo.php";
	//client.open("GET", url, true);

	client.onreadystatechange = function(){
		PLconponentDetailhandler(client)
	};
	client.open("GET", "getStationInfoNoConponent.php?loc=" + loc + "&eventId=" + eventId);
	client.send("");
} //getData getPLConponentDetails()

function PLconponentDetailhandler(obj){
	var plStatus = document.getElementById('plStatus');
	var plHistory = document.getElementById('plHistory');
	var plForman = document.getElementById('plForman');
	//forman.options.length = 0;
	//var pass = document.getElementById('lPassNum');
	//var bags = document.getElementById('lNumBags');
	    
	if(obj.readyState == 4 && obj.status == 200){
		var val = JSON.parse(obj.responseText);

		for(var i = 0; i < val.length; i++){
			var txtNew = document.createElement('text');

			// alert(val[i].STATUS);
			txtNew.text = val[i].STATUS;
			plStatus.value = txtNew.text;
			   
			// alert(val[i].COMMENTS);
			txtNew.text = val[i].COMMENTS;
			plHistory.value = txtNew.text;
			   
			// alert(val[i].GANG);
			txtNew.text = val[i].GANG;
			plForman.value = txtNew.text;
			
			
			//-------------------------------
			//Grab all the attached documents and list in a option	
			
			txtNew.text = val[i].SUPPORTDOCS;
			   
			var doctxt = txtNew.text;
			var docArray = new Array();
			docArray = doctxt.split(",");
                  					  
			for(var x=0; x<docArray.length; x++){
				var opt = docArray[x];
				var el = document.createElement("option");
				// alert(el);		
				el.innerHTML= opt;
				el.value = opt;
				downloadFile.appendChild(el);
			}
			//---------------------------------
		} //end for(var i = 0; i < val.length; i++)
	} // end if(obj.readyState == 4 && obj.status == 200)
} //handler PLconponentDetailhandler(obj)
               

/*
 * LOCATIONS
 * */

function getLocationByEvent() {	
	var eventId = document.eventPDFForm.eventId.value;    			   
	if (window.XMLHttpRequest){                         
		var xRequest = new XMLHttpRequest();
	} else if (window.ActiveXObject){
		var xRequest = new ActiveXObject("Microsoft.XMLHTTP");                       
	}
	 
	xRequest.onreadystatechange = function() {
		var location = document.eventPDFForm.locationId;
		
		document.eventPDFForm.locationId.options.length = 0;
		document.eventPDFForm.locationId.size = 6;
		var length = location.options.length;
	      
		if(xRequest.readyState == 4 && xRequest.status == 200){
			var val = JSON.parse( xRequest.responseText );            		  	
			for (var i = 0; i < val.length; i++){
				var opt = document.createElement('option');                		  
				opt.innerHTML = val[i].MARKERNAME;
				opt.value = val[i].MARKERID;              		   	  
				location.appendChild(opt);
			}
		}
	};
   
	xRequest.open("GET", "getLocationList.php?eventId=" + eventId + "&random=" + Math.random());
	xRequest.send(null);
}

function validateEventLocation(){
	if(document.eventPDFForm.eventId.value == '' || document.eventPDFForm.eventId.value == 0){
		if(document.eventPDFForm.locationId.value == ''){
			alert("Please select Event & Location for Report");
			return false;
		} else {
			alert("Please select Event for Report");
			return false;
		}
	} else {
		if(document.eventPDFForm.locationId.value == ''){
			alert("Please select Location for Report");
			return false;
		}
	}
	return true;
}
function validateEvent(){    			   
	if(document.gangPDFForm.eventId.value == '' || document.gangPDFForm.eventId.value == 0){
		alert("Please select Event for Report");
		return false;          			   
	}
	return true;
}


/*
 * TAB stuff
 * */
function clickedTab(){
	//find out if there is a valid hash related to the vertical tabs for location
	if( window.location.hash.length > 0 && window.location.hash.indexOf("lview") !== -1 ){
		var hashget = window.location.hash;
	}else{
		var hashget = "#lview0"; //if not, go to the default, which is for stations
	}
	
	//get the li element within the tab buttons, based on current URL hash tag
	var element = $('#tabs-titles li').has("a[href*='"+hashget+"']");
	
	var contents = window.contents;
	var tabs = window.tabs;
	
	contents.hide(); //hide all contents
	tabs.removeClass('current'); //remove 'current' classes
	
	$(contents[$(element).index()]).show(); //show tab content that matches tab title index
	$(element).addClass('current'); //add current class on clicked tab title
	
	//for changing the action parameter of each and every little form
	$("form").each(function(){
	
		//get the form action url minus the hash variable
		var clean_url = $(this).attr("action").match(/[^\#]*/i)[0];
	
		//assign the form action the cleaned url plus the desired hash
		$(this).attr("action", clean_url + window.location.hash);
	});
}

/*
 * using this instead of jQuery's 'document ready' method.
 * This is because jQuery does not play as nice for initialization in .js files, compared to inline js
*/
window.onload = function() {

	//'Calendar' is for the datepicker fields 
	Calendar.setup({
		inputField : "opentime",
		ifFormat   : "%m/%d/%Y",
		displayArea: "start_display",
		daFormat   : "%m/%d/%Y",
		button     : "startCalbutton",
		weekNumbers: false
    });

	Calendar.setup({
		inputField : "gStartTm",
		ifFormat   : "%m/%d/%Y",
		displayArea: "start_display",
		daFormat   : "%m/%d/%Y",
		button     : "gangStartTm",
		weekNumbers: false
    });
			
	Calendar.setup({
		inputField : "lNoteTime",
		ifFormat   : "%m/%d/%Y",
		displayArea: "start_display",
		daFormat   : "%m/%d/%Y",
		button     : "locationStartTime",
		weekNumbers: false
	});
			
	Calendar.setup({
		inputField : "iNoteTime",
		ifFormat   : "%m/%d/%Y",
		displayArea: "start_display",
		daFormat   : "%m/%d/%Y",
		button     : "interlockingStartTime",
		weekNumbers: false
	}); 
	
	Calendar.setup({			       
			inputField : "reportFromDate",			       
			ifFormat   : "%m/%d/%Y",			       
			displayArea: "start_display",			       
			daFormat   : "%m/%d/%Y",			       
			button     : "reportFromTime",			       
			weekNumbers: false
	});
			        
	Calendar.setup({			       
			inputField : "reportToDate",			       
			ifFormat   : "%m/%d/%Y",			       
			displayArea: "start_display",			       
			daFormat   : "%m/%d/%Y",			       
			button     : "reportToTime",			       
			weekNumbers: false
	});

	
	//Prevent the backspace button from being used to go back in web page navigation
	$(document).on("keydown", function (e){
		if (e.which === 8 && !$(e.target).is("input:not([readonly]):not([type=radio]):not([type=checkbox]), textarea, [contentEditable], [contentEditable=true]")) {
			e.preventDefault();
		}
	});
	
	window.tabs = $('#tabs-titles li'); //grab tabs
	window.contents = $('#tabs-contents li'); //grab contents


  	// on load of the page: switch to the currently selected tab
  	var hash = window.location.hash;
  	
  	//for the tabs
  	window.contents.hide(); //hide all contents
  	window.tabs.removeClass('current'); //remove 'current' classes

	var tabindex = window.tabindex; 

  	$(contents[tabindex]).show(); //show tab content that matches tab title index
	
	/*
	 * performs the tab action by default on the window load, same function as clicking a tab.
	 * But if the URL has a hash in it, it will go to the correct contents, as if one of the tab buttons were hit
	*/
  	clickedTab();
};

/*
occurs each time there is a hash change.
But only meaningful if one goes to a link from the left tabs
*/
window.onhashchange = clickedTab;


function validateDatewiseEvent()
{      
	if((document.datewiseEventPDFForm.reportFromDate.value == '' || document.datewiseEventPDFForm.reportFromDate.value == 0) || (document.datewiseEventPDFForm.reportToDate.value == '' || document.datewiseEventPDFForm.reportToDate.value == 0)){
		alert("Please select Date Range for Report");
		return false; 
	} else {
		if(document.datewiseEventPDFForm.locationId.value === '0'){
			alert("Please select Location for Report");
			return false;
	    }
	}	
	return true;
}
