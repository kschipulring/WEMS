/**
 * PURPOSE: update the top legend in the header every 30 seconds
 */

function getTally(){
	var xhrGet1 = dojo.xhrGet({
		url: "getTally.php",
		handleAs: "json",
		handle: function(response)
		{
			
			//stations
			dojo.byId("label_dirty_circle").innerHTML = response.sDirty;
			dojo.byId("label_inprogress_circle").innerHTML = response.sInProgress;
			dojo.byId("label_clean_circle").innerHTML = response.sClean;
			
			//interlocking
			dojo.byId("label_dirty_triangle").innerHTML = response.iDirty;
			dojo.byId("label_inprogress_triangle").innerHTML = response.iInProgress;
			dojo.byId("label_clean_triangle").innerHTML = response.iClean;
			
			//parking
			dojo.byId("label_dirty_rect").innerHTML = response.pDirty;
			dojo.byId("label_inprogress_rect").innerHTML = response.pInProgress;
			dojo.byId("label_clean_rect").innerHTML = response.pClean;
		}
	});
}

require(["dojo/dom"], function(dom){
	updater = setInterval(function(){
		getTally();
	}, 30000);
});