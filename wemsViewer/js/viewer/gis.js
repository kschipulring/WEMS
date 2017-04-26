/**
 * PURPOSE: update the top legend in the header every 30 seconds
 */

function getTally(){
	var xhrGet1 = dojo.xhrGet({
		url: "getTally.php",
		handleAs: "json",
		handle: function(response){
			var states = ["dirty", "inProgress", "clean", "halfClean"];
			var shapes = {"s": "circle", "i": "triangle", "p": "rect"};

			for(var k in shapes ){
				var v = shapes[k];
				
				for(var i in states){
					var cappedState = states[i].replace(/\b\w/g, l => l.toUpperCase());
					var tempStr = "label_"+ states[i] +"_"+ v;

					if( response[ k + cappedState ] && dojo.byId(tempStr) ){

						//update the correct dom element for the variable
						dojo.byId(tempStr).innerHTML = response[ k + cappedState ];
					}
				}
			}
		}
	});
}

require(["dojo/ready"], function(ready){
	/*BUG!!!!!
	// get the config file from the url if present
	var file = 'config/viewer', s = window.location.search, q = s.match(/config=([^&]*)/i);
	if (q && q.length > 0) {
		file = q[1];
		if(file.indexOf('/') < 0) {
			file = 'config/' + file;
		}
	}
	
	require(['viewer/Controller', file], function(Controller, config){
		Controller.startup(config);
	});
	
	require(["dojo/dom"], function(dom){
		updater = setInterval(function(){
			getTally();
		}, 30000);
	});
	
	ready(function(){
    // This function won't run until the DOM has loaded and other modules that register have run.

	});
	*/
});