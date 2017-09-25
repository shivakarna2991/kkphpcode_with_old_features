///////////////////////
/* Task view filler */
// For displaying the information for one task.
function TaskViewFiller(forTask) {
    this.forTask = forTask;
    this.isCreatingTask = (forTask == undefined || forTask == null);
    this.name = this.isCreatingTask ? "Task" : this.forTask.name;

    this.getUIValues = function() {
        var deviceType = getFieldValue("DeviceType");
        var setUpdate = getFieldValue("SetUpdate");
        var name = this.isCreatingTask ? getFieldValue("Name") : "";
        var durations = getFieldValue("Durations");
        var timeBlocks = getFieldValue("TimeBlocks");
        var description = getFieldValue("Description");
        var notes = getFieldValue("Notes");

        if (deviceType == null || setUpdate == null || name == null || 
            durations == null || timeBlocks == null || 
            description == null || notes == null) {
            
            displayError("Could not read values from the user interface.");
            return null;
        }
        
        if (this.isCreatingTask && name == "") {
            displayError("Must give a name.");
            return null;
        }
        
        for (var i = 0; i < durations.length; ++i) {
            var startVerify = verifyDate(durations[i].start);
            var endVerify = verifyDate(durations[i].end);
            if ((durations[i].start != "" && !startVerify) ||
                (durations[i].end != "" && !endVerify)){
                displayError("Invalid date value.");
                return null;
            }
        }
        
        for (var i = 0; i < timeBlocks.length; ++i) {
            var timeStartVerify = verifyTime(timeBlocks[i].start);
            var timeEndVerify = verifyTime(timeBlocks[i].end);
            
            if ((timeBlocks[i].start != "" && !timeStartVerify) || 
                (timeBlocks[i].end != "" && !timeEndVerify)) {
                displayError("Invalid time value.");
                return null;
            }
        }

        return {
            deviceType: deviceType,
            setUpdate: setUpdate,
            name: name,
            siteCode: "",
            durations: durations,
            timeBlocks: timeBlocks,
            deviceType: convertIndexToDeviceType(deviceType),
            description: description,
            notes: notes
        };
    }

    this.taskCreateBtnClicked = function() {
        enteredTask = this.getUIValues();
        if (enteredTask == null) {
            return null;
        }
        enteredTask.jobId = g_selectedJob.jobid;
        
        JobCreateTask(g_selectedJob.jobid, enteredTask.name, 
                             enteredTask.setUpdate, enteredTask.deviceType, 
                             1, 0, function(context, textStatus, response, resultStr, taskId) {
            if (textStatus == "success" && response == "success") {
                // Go into placing job sites on the map.

                // Set the defaults that will be used throughout the program.
                g_selectedTask = enteredTask;
                g_selectedTask["taskid"] = taskId;

                navToPlaceJobSitesView();
            }
            else {
                displayFatalError("Could not create task.", resultStr);
                return;
            }
        }, null);
    };

    this.taskUpdateJobSitesBtnClicked = function() {
        // Navigate to the job sites view to display all of the child job sites.
        g_selectedTask = this.forTask;
        navToJobSitesView();
    };

    this.taskDeleteBtnClicked = function() {
        // Get the job site ids for this task so the markers can later be deleted.
        var taskId = this.forTask.taskid;
        
        JobSiteManagerGetJobSites(INFO_LEVEL_BASIC, null, null, null, taskId, 
                                  null, null, null, null, null, null, 
                                  function(context, textStatus, response, resultStr, jobSites) {
            if (textStatus != "success" || response != "success") {
                displayFatalError("Cannot fetch job sites for task.");
                return;
            }
            
            TaskDelete(taskId, function(context, textStatus, response, resultStr) {
                if (textStatus == "success" && response == "success") {
                    // The task was deleted.
                    // Remove all of the markers on the map that are under this task.
                    for (var i = 0; i < jobSites.length; ++i) {
                        removeMapMarker(jobSites[i].jobsiteid);
                    }
                    navToTasksView();
                }
                else {
                    displayFatalError("Could not delete task.", resultStr);
                }
            }, null);
        }, null);
    };
}
TaskViewFiller.prototype = new ViewFiller();
TaskViewFiller.prototype.fillContent = function(fillView) {
    var useTask = null;
    if (this.isCreatingTask) {
        useTask = {
            setupdate: "",
            devicetype: -1
        };
    }
    else {
        useTask = this.forTask;
    }

    var setHtml = "";
    if (!this.isCreatingTask) {
        setHtml += "<button onclick='navToJobView(" + this.forTask.jobid + ")'>Back</button>";
    }
    
    setHtml += "<div id='inputEnterArea'>";
    
    setHtml += "<div id='controlButtonsContainer'>"; 
    if (this.isCreatingTask) {
        setHtml += "<button id='createBtn'>Create</button>";
        setHtml += "<button onclick='navToTasksView();'>Back</button>";
    }
    else {
        setHtml += "<button id='seeJobSitesBtn'>Job Sites</button>";
        setHtml += "<button id='deleteBtn' class='delete-btn'>Delete</button>";
    }
    setHtml += "<p class='error-msg' style='display: none;'></p>";
    setHtml += "<p class='success-msg' style='display: none;'></p>";  
    setHtml += "</div>";
    
    if (this.isCreatingTask) {
        setHtml += createValueField("Name", "text", "", "Name");

        // These are properties that are defined on the task level.
        setHtml += createValueField("Set Update", "datepicker", useTask.setupdate, "SetUpdate");
        var allowedDevices = getAllowedDevicesForStudyType(g_selectedJob.studytype);
        setHtml += createValueField("Device Type", "select", -1, "DeviceType", allowedDevices);

        // These are properties that are defined for the job site these are setting the defaults. 
        setHtml += createValueField("Durations", "datepickers", null, "Durations");
        setHtml += createValueField("Time Blocks", "durationpickers", null, "TimeBlocks");
        setHtml += createValueField("Description", "text", "", "Description");
        setHtml += createValueField("Notes", "textarea", "", "Notes");
    }
    else {
        setHtml += createValueField("Name", "text", this.forTask.name, "Name");
    }
    
    setHtml += "</div>";

    fillView.html(setHtml);
    
    var self = this;
    $("#createBtn").click(function () {
        self.taskCreateBtnClicked();
    });

    $("#deleteBtn").click(function () {
        displayConfirmDialog(function () {
            self.taskDeleteBtnClicked();
        });
    });
    
    $("#seeJobSitesBtn").click(function () {
        self.taskUpdateJobSitesBtnClicked();
    });

    $(".date-picker-input").datepicker({
        dateFormat: "yy-mm-dd"
    });
    
    $(".expandable-list-time").focusin(function () {
        prevEle = $(this).prev();
        if (!prevEle.hasClass("popup-prompt")) {
            prevEle = prevEle.prev();
        }
        
        prevEle.show();
    });
    
    $(".expandable-list-time").focusout(function () {
        prevEle = $(this).prev();
        if (!prevEle.hasClass("popup-prompt")) {
            prevEle = prevEle.prev();
        }
        
        prevEle.hide();
    });
};
