//////////////////////
/* Finish Job Sites View */

var g_mapClickEventHandler = null;

function FinishJobSitesView() {
    this.name = "Summary";
}
FinishJobSitesView.prototype = new ViewFiller();
FinishJobSitesView.prototype.fillContent = function(fillView) { 
    // Need to get the number of devices to be used under this task.
    // It can be assumed to be one per job site.
    
    var taskId = g_selectedTask.taskid;
    
    JobSiteManagerGetJobSites(INFO_LEVEL_BASIC, null, null, null, taskId, 
                              null, null, null, null, null, null, 
                              function(context, statusText, response, resultStr, jobSites) {
        if (statusText != "success" || response != "success") {
            displayFatalError("Cannot get job sites for task!", resultStr);
            return;
        }
        
        var requiredDeviceCount = jobSites.length;
        var html = "";
        html += "<table>";
        html += "<tr>";
        html += "<td><label style='margin-right: 8px;'>Required Devices</label></td>";
        html += "<td><label>Extra Devices</label></td>";
        html += "</tr>";
        html += "<tr>";
        html += "<td style='text-align: right;'><span>" + requiredDeviceCount + "+</span></td>";
        html += "<td style='text-align: left;'><input type='text' id='additionalInputField' value='0' /></td>";
        html += "</tr>";
        html += "</table>";
        html += "<br />";
        html += "<label style='display: inline; margin-right: 10px;'>Total Devices</label>";
        html += "<span id='totalDeviceCountSpan'>" + requiredDeviceCount + "</span>";
        html += "<br />";
        html += "<button onclick='navToJobsView();'>Ok</button"
        fillView.html(html);

        $("#additionalInputField").keyup(function () {
            var inputVal = $(this).val();
            if (inputVal == "") {
                inputVal = 0;
            }

            inputVal = Number(inputVal);
            if (!isNaN(inputVal)) {

                var total = inputVal + requiredDeviceCount;
                $("#totalDeviceCountSpan").text(total);
            }
            else {
                $("#totalDeviceCountSpan").text("");
            }
        });
    }, null);
};