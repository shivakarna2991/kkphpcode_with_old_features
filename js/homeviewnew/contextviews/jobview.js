//////////////////////
/* Job View Filler */
// Displays the information for only a single job.
function JobViewFiller(forJob) {
    this.forJob = forJob;
    this.isCreatingJob = (forJob === undefined || forJob === null);
    this.name = this.isCreatingJob ? "Create New Job" : forJob.name;

    this.getUIValues = function() {
        // Get the data from the UI.
        var area = getFieldValue("Area");
        var name = this.isCreatingJob ? getFieldValue("Name") : "";
        var nickname = getFieldValue("Nickname");
        var deliveryDate = getFieldValue("DeliveryDate");
        var number = getFieldValue("Number");
        var studyType = this.isCreatingJob ? getFieldValue("StudyType") : -1;
        var office = getFieldValue("Office");
        var notes = getFieldValue("Notes");
        var orderDate = getFieldValue("OrderDate");

        if (area == null || deliveryDate == null || 
            number == null || office == null || 
            notes == null || orderDate == null || this.name == null) {
            displayError("Server error. Could not fetch values.");
            return null;
        }
        
        if (orderDate != "" && !verifyDate(orderDate)) {
            displayError("Invalid order date.");
            return null;
        }
        
        if (deliveryDate != "" && !verifyDate(deliveryDate)) {
            displayError("Invalid delivery date.");
        }

        return {
            name: name,
            area: area,
            nickname: nickname, 
            number: number,
            deliveryDate: deliveryDate,
            office: convertIndexToOfficeType(office),
            notes: notes,
            studytype: convertIndexToStudyType(studyType),
            orderDate: orderDate
        };
    }

    this.jobUpdateBtnClicked = function() {
        enteredJob = this.getUIValues();
        if (enteredJob == null) {
            return;
        }

        JobUpdate(g_selectedJob.jobid, enteredJob.number, g_selectedJob.name, enteredJob.nickname, g_selectedJob.studytype,
                            enteredJob.office, enteredJob.area, enteredJob.notes, 
                            enteredJob.orderDate, enteredJob.deliveryDate, 
                            function(context, textStatus, response, resultStr) {
            if (textStatus == "success" && response == "success") {
                // Display success message to the user.
                displaySuccess("Values updated!");
            }
            else {
                displayError("Server error. Could not update job.");
            }
        });
    };

    this.jobDeleteBtnClicked = function () {
        
        // First delete all markers associated with this job.
        var jobId = this.forJob.jobid;
        JobSiteManagerGetJobSites(INFO_LEVEL_BASIC, null, null, jobId, 
                                  null, null, null, null, null, null, null, 
                                  function(context, textStatus, response, resultStr, jobSites) {
            if (textStatus != "success" || response != "success") {
                displayFatalError("Could not get job sites for job.", resultStr);
                return;
            }
            
            for (var i = 0; i < jobSites.length; ++i) {
                removeMapMarker(jobSites[i].jobsiteid);
            }
            
            JobDelete(jobId, function(context, textStatus, response, resultStr) {
                if (textStatus == "success" && response == "success") {
                    // The job was deleted.
                    navToJobsView();
                }
                else {
                    // There was a server error deleting the job.
                    displayFatalError("Could not delete job.", resultStr);
                }
            }, null);
        }, null);
        
    };

    this.jobCreateBtnClicked = function () {
        enteredJob = this.getUIValues();
        if (enteredJob == null) {
            return;
        }

        JobManagerCreateJob(enteredJob.number, enteredJob.name, enteredJob.nickname, enteredJob.studytype, enteredJob.office, 
                            enteredJob.area, enteredJob.notes, enteredJob.orderDate, enteredJob.deliveryDate, 
                            function(context, textStatus, response, resultStr, jobId) {
            if (textStatus == "success" && response == "success") {
                // Not all of the data is needed for the task view.
                g_selectedJob = enteredJob;
                g_selectedJob.jobid = jobId;
                 
                navToTasksView();
            }
            else {
                displayFatalError("Could not create job.", resultStr);
            }
        }, null);  
    };
}
JobViewFiller.prototype = new ViewFiller();
JobViewFiller.prototype.fillContent = function(fillView) { 
    
    var setHtml = "";
    // To allow an empty job to be used in the case of creating a job. (As all of the data will be empty.)
    var useJob = null;
    if (this.isCreatingJob) {
        useJob = {
            area: "",
            creationdate: "",
            number: "",
            lastupdatetime: "",
            deliverydate: "",
            office: -1,
            notes: "",
            orderdate: "",
            studyType: -1,
            nickname: "",
        };
    }
    else {
        g_selectedJob = this.forJob;
        useJob = this.forJob;
    }

    setHtml += "<button onclick='navToJobsView();'>Back</button>";
    setHtml += "<div id='inputEnterArea'>";
    
    setHtml += "<div id='controlButtonsContainer'>";
    if (this.isCreatingJob) {
        setHtml += "<button id='createBtn'>Create Job</button>";
        setHtml += "<button onclick='navToJobsView();'>Cancel</button>"
    }
    else {
        setHtml += "<button id='updateBtn'>Update</button>";
        setHtml += "<button onclick='navToTasksView();'>Tasks</button>";
        setHtml += "<button id='deleteBtn' class='delete-btn'>Delete</button>";
    }
    setHtml += "</div>";
    
    if (this.isCreatingJob) {
        setHtml += createValueField("Name", "text", "", "Name");
        setHtml += createValueField("StudyType", "select", useJob.studyType, "StudyType", getStudyTypes());
    }
    else {
        setHtml += "<p class='study-type-display'>" + studyTypes[useJob.studytype] + " study type</p>";
    }
    setHtml += createValueField("Area", "text", useJob.area, "Area");
    setHtml += createValueField("Nickname", "text", useJob.nickname, "Nickname");
    setHtml += createValueField("Delivery Date", "datepicker", useJob.deliverydate, "DeliveryDate");
    setHtml += createValueField("Number", "text", useJob.number, "Number");
    setHtml += createValueField("Office", "select", useJob.office, "Office", getOfficeTypes());
    setHtml += createValueField("Notes", "textarea", useJob.notes, "Notes");
    setHtml += createValueField("Order Date", "datepicker", useJob.orderdate, "OrderDate");
    
    
    setHtml += "<p class='error-msg' style='display: none;'></p>";
    setHtml += "<p class='success-msg' style='display: none;'></p>";
    setHtml += "</div>";

    fillView.html(setHtml);
    
    // Enable the date picker controls.
    $(".date-picker-input").datepicker({
        dateFormat: "yy-mm-dd"
    });
    
    // Set the appropriate event handlers.
    var self = this;
    $("#updateBtn").click(function () {
        self.jobUpdateBtnClicked();
    });
    $("#createBtn").click(function () {
        self.jobCreateBtnClicked();         
    });
    $("#deleteBtn").click(function () {
        displayConfirmDialog(function () {
            self.jobDeleteBtnClicked();
        });
    });
    
    $("#inputName").keyup(function () {
        var nickname;
        var nameVal = $("#inputName").val();
        if (nameVal.length >= 5) {
            nickname = nameVal.substring(0, 4);
        }
        else {
            nickname = nameVal.substring(0, nameVal.length);
        }
        
        $("#inputNickname").val(nickname);
    });
}
