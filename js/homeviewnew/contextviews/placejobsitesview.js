//////////////////////
/* Place Job Sites View */

var g_mapClickEventHandler = null;

function PlaceJobSitesView() {
    this.name = "Place job sites";
}
PlaceJobSitesView.prototype = new ViewFiller();
PlaceJobSitesView.prototype.fillContent = function(fillView) { 
    fillView.html("<p>Double Click on the map to place a job site.");

    // Disable search functionality.
    disableSearchFunc();
    
    if (g_mapClickEventHandler == null)
        g_mapClickEventHandler = g_map.addListener("dblclick", onMapDblClick);
};