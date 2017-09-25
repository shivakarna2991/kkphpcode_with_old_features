////////////////////////
/* Tasks view filler */
function TasksViewFiller(forJob) {
    this.forJob = forJob;
    this.name = "Tasks of " + forJob.name;
}
TasksViewFiller.prototype = new ViewFiller();
TasksViewFiller.prototype.fillContent = function (fillView) {
    // Get all of the tasks for this job.
    var tmpForJob = this.forJob;
    JobGetTasks(this.forJob.jobid, function(context, textStatus, response, resultStr, returnval) {
        if (textStatus == "success" && response == "success") {
            var setHtml = "";
            setHtml += "<button onclick='navToJobView(" + tmpForJob.jobid + ");'>Back</button>";
            
            
            setHtml += "<div id='controlButtonsContainer'>";
            setHtml += "<button onclick='navToTaskView();'>Create Task</button";
            setHtml += "<p class='error-msg' style='display: none;'></p>";
            setHtml += "<p class='success-msg' style='display: none;'></p>"; 
            setHtml += "</div>";
            
            setHtml += "<ul>";

            g_viewableTasks = returnval;
            for (var i = 0; i < returnval.length; ++i) {
                setHtml += "<li>";
                setHtml += "<button onclick='navToTaskView(" + returnval[i].taskid + ")'>" + returnval[i].name + "</button>";
                setHtml += "</li>";
            }
            
            if (returnval.length == 0) {
                setHtml += "<li>There are no tasks for this job.</li>";
            }
                                     
            setHtml += "</ul>";     

            fillView.html(setHtml);
        }
        else {
            displayFatalError("Could not populate jobs list.", resultStr);
        }
    }, null); 
}

