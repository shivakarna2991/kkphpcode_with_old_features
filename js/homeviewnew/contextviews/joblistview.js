///////////////////////
/* Jobs view filler */
// Displays the list of all jobs.
function JobsViewFiller() {
    this.name = "Jobs";
    this.renderAll = false;
    
    this.renderJobs = function (jobs, fillView) {
        // Cache result.
        g_viewableJobs = jobs;
        var htmlListStr = "";
        htmlListStr += "<div class='checkbox-container'>";
        htmlListStr += "<input type='checkbox' " + (this.renderAll ? "checked" : "") + " id='renderAllCheckBox' />";
        htmlListStr += "<span>Display All Jobs</span>";
        htmlListStr += "</div>";
        htmlListStr += "<ul>";
        for (var i = 0; i < jobs.length; ++i) {
            htmlListStr += "<li>";
            htmlListStr += "<button onclick='navToJobView(" + jobs[i].jobid + ");'>" + jobs[i].name + "</button>";
            htmlListStr += "</li>";
        }
        if (jobs.length == 0) {
            htmlListStr += "<li>No jobs</li>";
        }
        htmlListStr += "</ul>";

        htmlListStr += "<button onclick='navToCreateJobView();'>Create Job</button>";

        fillView.html(htmlListStr);
        
        $("#renderAllCheckBox").change(function () {
            if (this.checked) {
                navToJobsViewRenderAll();
            } 
            else {
                navToJobsView();
            }
        });
        
        
        $("#sinceTextBox").datepicker({
            dateFormat: "yy-mm-dd"
        });
    };
}
JobsViewFiller.prototype = new ViewFiller();
JobsViewFiller.prototype.fillContent = function (fillView) {
    var self = this;
    
    if (this.renderAll) {
        // Render all of the jobs.
        JobManagerGetJobs(INFO_LEVEL_BASIC, null, null, null, function (context, textStatus, response, resultStr, returnVal) {
            if (textStatus == "success" && response == "success") {
                self.renderJobs(returnVal, fillView);
            }
            else {
                displayFatalError("Could not populate jobs list.", resultStr);
            }
        }, null);
    }
    else {
        // Only render the jobs that have markers on the map.
        var allJobIds = [];
        for (var i = 0; i < g_markers.length; ++i) {
            var jobId = Number(g_markers[i].get('jobId'));
            // Check that the job id is not already in the array.
            if ($.inArray(jobId, allJobIds) === -1) {
                allJobIds.push(jobId);
            }
        }
        
        // Get all of the jobs that have the job ids.
        JobManagerGetJobs(INFO_LEVEL_BASIC, null, null, null, function(context, textStatus, response, resultStr, returnVal) {
            if (textStatus == "success" && response == "success") {
                // Check which of the jobs are in the array of jobs that are being rendered on the map.
                var displayJobs = [];
                for (var i = 0; i < returnVal.length; ++i) {
                    var job = returnVal[i];
                    
                    if ($.inArray(Number(job.jobid), allJobIds) > -1) {
                        displayJobs.push(job);
                    }
                }
                
                self.renderJobs(displayJobs, fillView);
            }
            else {
                displayFatalError("Could not get jobs.", resultStr);
            }
        }, null);
    }
};
