var g_viewableJobs;
var g_viewableTasks;
var g_viewableJobSites;
var g_selectedJob;
var g_selectedTask;


function ViewFiller(name) {
    this.name = name;
}
ViewFiller.prototype.getTitle = function () {
    return this.name;    
};
ViewFiller.prototype.fillContent = function (fillView) {
    fillView.html("No content");
};

