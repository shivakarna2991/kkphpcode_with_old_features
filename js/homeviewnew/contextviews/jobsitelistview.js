///////////////////////
/* Job sites view */
function JobSitesView(forTask) {
    this.name = "Job Sites";
    this.forTask = forTask;
}
JobSitesView.prototype = new ViewFiller();
JobSitesView.prototype.fillContent = function(fillView) {

    var taskid = this.forTask.taskid;
    TaskGetJobSites(taskid, function(context, textStatus, response, resultStr, returnval) {
        if (textStatus == "success" && response == "success") {
            var setHtml = "";

            setHtml += "<button onclick='navToTaskView(" + taskid + ")'>Back</button>";
            setHtml += "<ul>";
            
            g_viewableJobSites = returnval;
            for (var i = 0; i < returnval.length; ++i) {
                g_viewableJobSites[i] = FixJobSiteValues(g_viewableJobSites[i]);
                g_viewableJobSites[i].allowedDevices = g_selectedTask.allowedDevices;
                setHtml += "<li>";
                setHtml += "<button onclick='navToJobSiteView(" + returnval[i].jobsiteid + ");'>" + g_viewableJobSites[i].siteCode + "</button>";
                setHtml += "</li>";
            }
            
            if (returnval.length == 0) {
                setHtml += "<li>There are no job sites for this task.</li>";
            }

            setHtml += "</ul>";
            
            setHtml += "<button id='placeJobSitesBtn'>Place Job Sites</button>";

            fillView.html(setHtml);
        }
        else {
            displayFatalError("Could not fetch job sites for task.", resultStr);
        }
        
        $("#placeJobSitesBtn").click(function () {
            // Going from here there will not be the cached task value.
            // Check if there are any previous job sites in the task to base further job sites on.
            if (returnval.length > 0) {
                // Just get the last element in the array.
                var lastJob = returnval[returnval.length - 1];
                g_selectedTask.setUpdate = lastJob.setUpdate;
                g_selectedTask.siteCode = lastJob.siteCode;
                g_selectedTask.durations = lastJob.durations;
                g_selectedTask.timeBlocks = lastJob.timeBlocks;
                g_selectedTask.studyType = lastJob.studyType;
                g_selectedTask.deviceIds = lastJob.deviceIds;
                g_selectedTask.description = lastJob.description;
                g_selectedTask.notes = lastJob.notes;
            }
            else {
                // There are no other job sites and therefore there is no template to go off of. 
                // Intialize all of the values to nothing so they aren't undefined.
                
                g_selectedTask.setUpdate = "";
                g_selectedTask.siteCode = "";
                g_selectedTask.durations = [];
                g_selectedTask.timeBlocks = [];
                g_selectedTask.studyType = -1;
                g_selectedTask.deviceIds = "";
                g_selectedTask.description = "";
                g_selectedTask.notes = "";
            }
            navToPlaceJobSitesView();
        });
    }, null);
}