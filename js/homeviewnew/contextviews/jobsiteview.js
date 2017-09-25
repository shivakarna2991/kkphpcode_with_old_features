///////////////////////
/* Job Site View */
function JobSiteView(jobSite) {
    this.name = "Job Site";
    this.jobSite = jobSite;
    this.isCreating = (jobSite.jobsiteid == undefined || jobSite.jobsiteid == null);
    this.mapMarker = null;

    this.getUIValues = function() {
        var setUpdate = getFieldValue("SetUpdate");
        var siteCode = getFieldValue("SiteCode");
        var durations = getFieldValue("Durations");
        var timeBlocks = getFieldValue("TimeBlocks");
        var description = getFieldValue("Description");
        var notes = getFieldValue("Notes");

        if (setUpdate == null || description == null || notes == null || durations == null || timeBlocks == null) {
            displayError("Cannot fetch UI input.");
            return null;
        }

        return {
            setUpdate: setUpdate,
            siteCode: siteCode,
            timeBlocks: timeBlocks,
            durations: durations,
            description: description,
            notes: notes
        };
    };
    
    this.updateJobSite = function() {
        var enteredData = this.getUIValues();
        if (enteredData == null) {
            return;
        }
        
        JobSiteUpdate(this.jobSite.jobsiteid, enteredData.siteCode, this.jobSite.latitude, 
                      this.jobSite.longitude, enteredData.setUpdate, enteredData.durations, 
                      enteredData.timeBlocks, this.jobSite.taskstatus, enteredData.description, 
                      enteredData.notes, this.jobSite.direction, 
                      this.jobSite.oneway, this.jobSite.countpriority, this.jobSite.reportformat,
                      this.jobSite.reportparameters, function (context, textStatus, response, resultStr) {
            if (textStatus == "success" && response == "success") {
                displaySuccess("Values updated.");
            }
            else {
                displayError("Could not update values");
            }
        }, null);
    }

    this.createJobSite = function(onProgress) {
        var enteredData = this.getUIValues();
        if (enteredData == null) {
            return;
        }

        // Hide all of the input fields.
        $("#inputEnterArea").hide();
        // Show the loading section.
        $("#loadingSection").show();

        var self = this;
        TaskCreateJobSite(g_selectedTask.taskid, g_selectedJob.jobid,
                                 enteredData.siteCode, 0, 
                                 this.jobSite.lat, this.jobSite.lng, 
                                 enteredData.setUpdate, enteredData.durations, enteredData.timeBlocks, 1, enteredData.description, 
                                 enteredData.notes, "", null, null, null, 
                                 function(context, textStatus, response, resultStr, jobSiteId) {
            if (textStatus == "success" && response == "success") {
                // For permanent access the marker on the map for this job site is now accessed by its jobSiteId.
                changeMapMarkerId(self.mapMarker.get('markerId'), jobSiteId);
                onProgress();
            }
            else {
                if (resultStr.indexOf("1064") > -1) {
                    displayError("Site code already exists in job");
                }
                else {
                    // Hide the loading section.
                    $("#loadingSection").hide();
                    // Show the input section.
                    $("#inputEnterArea").show();
                    displayFatalError("Cannot create job site.", resultStr);
                }
            }
        }, null);
    };

    this.onFinishedClick = function() {
        // No longer listen for the click events.
        this.createJobSite(function () {
            if (g_mapClickEventHandler !== null) {
                g_mapClickEventHandler.remove();
            }
            // Navigate to the top level view.
            enableSearchFunc();
            // Re-render the map markers.
            renderMapMarkers();
            navToFinishJobSitesView();
        });
    };

    this.onDoneClick = function() {
        this.createJobSite(function () {
            navToPlaceJobSitesView();
        });
    };

    this.onDeleteClick = function () {
        var jobSiteId = this.jobSite.jobsiteid;
        JobSiteDelete(jobSiteId, function(context, textStatus, response, resultStr) {
            if (textStatus == "success" && response == "success") {
                removeMapMarker(jobSiteId);
                navToJobSitesView();
            } 
            else {
                displayError("Could not delete job site.");
            }
        });
    };
}
JobSiteView.prototype = new ViewFiller();
JobSiteView.prototype.fillContent = function(fillView) {
    var setHtml = "";

    if (!this.isCreating) {
        if (g_viewableTasks == null) {
            setHtml += "<button onclick='navToJobView(" + this.jobSite.jobid + ");'>Back</button>";
        }
        else {
            setHtml += "<button onclick='navToTaskView(" + g_selectedTask.taskid + ");'>Back</button>"; 
        }
    }
    else {
        this.mapMarker = createMapIcon({
            lat: this.jobSite.lat, 
            lng: this.jobSite.lng
        }, g_selectedJob.studytype, this.jobSite.jobid);
    }
    
    setHtml += "<div id='inputEnterArea'>";
    
    setHtml += "<div id='controlButtonsContainer'>";
      if (this.isCreating) {
        // No longer place the done button.
//        setHtml += "<button id='doneBtn'>Done</button>";
    }
    else {
        // For now the user is not allowed to update job sites once they have been created.
        setHtml += "<button id='updateBtn'>Update</button>";
        setHtml += "<button class='delete-btn' id='deleteBtn'>Delete</button>";
    }
    
    setHtml += "<p class='error-msg' style='display: none;'></p>";
    setHtml += "<p class='success-msg' style='display: none;'></p>";         

    if (this.isCreating) {
        setHtml += "<p>Or double click on another location on the map</p>";
        setHtml += "<button id='finishedBtn'>Stop Placing</button>";
    }
    setHtml += "</div>";
    setHtml += createValueField("Set Update", "datepicker", this.jobSite.setUpdate, "SetUpdate");
    setHtml += createValueField("Site Code", "text", this.jobSite.siteCode, "SiteCode");
    setHtml += createValueField("Durations", "datepickers", this.jobSite.durations, "Durations");
    setHtml += createValueField("Time Blocks", "durationpickers", this.jobSite.timeBlocks, "TimeBlocks");
    setHtml += createValueField("Description", "text", this.jobSite.description, "Description");
    setHtml += createValueField("Notes", "textarea", this.jobSite.notes, "Notes");

  

    setHtml += "</div>";

    setHtml += "<div id='loadingSection' style='display: none;'>";
    setHtml += "<p>Loading</p>";
    setHtml += "</div>";

    fillView.html(setHtml);
    
    if (this.isCreating) {
        // Update the site code value.
        var studyTypeIndex = g_selectedJob.studytype;
        var jobId = this.jobSite.jobId;
        
        // Get the number of job sites under this job. 
        JobSiteManagerGetJobSites(INFO_LEVEL_BASIC, null, null, jobId, null, null, 
                                  null, null, null, null, null, function(context, textStatus, response, resultStr, jobSites) {
            if (textStatus != "success" || response != "success") {
                displayFatalError("Could not get all of the job sites under the job.");
                return;
            }
            
            $("#inputSiteCode").val(studyTypes[studyTypeIndex] + " #" + jobSites.length);
        }, null);
    }
    
    var self = this;
//    $("#doneBtn").click(function () {
//        self.onDoneClick();
//    });
    $("#deleteBtn").click(function () {
        displayConfirmDialog(function () {
            self.onDeleteClick();
        });
    });
    
    $("#finishedBtn").click(function () {
        self.onFinishedClick(); 
    });
    
    $("#updateBtn").click(function () {
         self.updateJobSite();
    });
    
    $(".date-picker-input").datepicker({
        dateFormat: "yy-mm-dd"
    });
};