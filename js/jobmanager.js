/* job management functions
*/
function openJobManager() {
    // call server to get list of active jobs
    var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + readCookie("authToken") + "&infolevel=" + INFO_LEVEL_BASIC + "&activeonly=1";
    $.ajax({
        type: "GET",
        url: "MethodCall.php/JobManager::GetJobs",
        data: paramsString,
        contentType: "application/json; charset=utf-8",
        dataType: "html",
        cache: false,
        success: function (result) {
            jsonResponse = JSON.parse(result);
            var response = jsonResponse['results']['response'];
            if (response == "success") {
                var row;
                var cell;

                // load selectprojectform
                $("#jobsTarget").load("htmlpages/jobmanager.htm", function() {
                    // load project values from server into newly loaded form
                    var jobsArr = jsonResponse['results']['returnval']['jobs'];

                    if (jobsArr.length) {
                        var jobstable = document.getElementById('jobstablerows');
                        for (i=0; i<jobsArr.length; i++)
                        {
                            jobId = jobsArr[i]['jobid'];
                            jobNumber = jobsArr[i]['number'];
                            jobName = jobsArr[i]['name'];
                            jobNickname = jobsArr[i]['nickname'];
                            jobOffice = jobsArr[i]['office'];
                            jobCreated = jobsArr[i]['creationdate'];
                            jobUpdated = jobsArr[i]['lastupdatetime'];
                            jobStudyType = jobsArr[i]['studytype'];

                            // populate each column in jobs table
                            row = jobstable.insertRow(0);
                            cell = row.insertCell(0);

                            if (jobsArr[i].testdata == "1")
                            {
                                cell.innerHTML = "*" + jobNumber;
                            }
                            else
                            {
                                cell.innerHTML = jobNumber;
                            }

                            cell.setAttribute("id", jobId);
                            cell.setAttribute("data-selected", false);
                            cell = row.insertCell(1);
                            cell.innerHTML = jobName;
                            cell = row.insertCell(2);
                            cell.innerHTML = jobNickname;
                            cell = row.insertCell(3);
                            cell.innerHTML = jobOffice;
                            cell = row.insertCell(4);
                            cell.innerHTML = studyTypes[jobStudyType];
                            cell.setAttribute("studytype", jobStudyType);
                            cell = row.insertCell(5);

                            if (typeof jobCreated !== 'undefined' && jobCreated !== "") {
                                cell.innerHTML = jobCreated.split(" ",1);
                            } else {
                                cell.innerHTML = "-";
                            }
                            cell = row.insertCell(6);
                            if (typeof jobUpdated !== 'undefined' && jobUpdated !== "") {
                                cell.innerHTML = jobUpdated.split(" ",1);
                            } else {
                                cell.innerHTML = "-";
                            }
                        }
                    }

                    // initalize jobManager form functions
                    initJobManagerForm();

                    // if a currentJob has been selected, show it highlighted and at the top of the form
                    var jobNumber = readCookie('currentjobnumber');
                    var jobName = readCookie('currentjobname');
                    var jobId = readCookie('currentjobid');

                    if (typeof jobId === 'undefined' || jobId === null
                            || typeof jobNumber === 'undefined' || jobNumber === null
                            || typeof jobName === 'undefined' || jobName === null)
                    {
                        // disable processdata & closejob buttons until a job has been selected
                        document.getElementById('processdatabtn').disabled = true;
                        document.getElementById('closejobbtn').disabled = true;
                    }
                    else
                    {
                        document.getElementById('processdatabtn').disabled = false;
                        document.getElementById('closejobbtn').disabled = false;

                        // set current Job header
                        document.getElementById('jobnumber').innerHTML = jobNumber+" - "+jobName;

                        // highlight selected row and unhighlight previous selected row
                        var table = document.getElementById("jobstablerows");

                        for (var i = 0; i<table.rows.length; i++)
                        {
                            row = table.rows[i];
                            cell = row.cells[0];

                            var cellid = cell.getAttribute("id");

                            // if not a match
                            if (cellid != jobId)
                            {
                                cell.setAttribute("data-selected", false);

                                row.style.color = "#000000";
                                row.style.backgroundColor = "";
                            }
                            // else if a match
                            else
                            {
                                cell.setAttribute("data-selected", true);

                                // highlight selected row
                                row.style.color = "#ffffff";
                                row.style.backgroundColor = "#0065ca";
                            }
                        }
                    }

                    // hide currently open form
                    hideOpenForm();

                    // define current navigation position
                    updateMenuLocation("jobmanager");

                    // display jobManager
                    $(".jobs-content").animate({width:'toggle'},350);
                });
            }
            // else handle error for user 
            else {
                if (jsonResponse['results']['returnval']['resultstring'] == "login required") {
                    // close this form, inform user and logout
                    loginRequired();
                } else {
                    showErrorDialog("Unknown server error");
                }
            }
        },
        error: function (request, status, error) {
            showErrorDialog("Server error: " + status);
        }
    });
}

/* jobsitemanager functions 
*/
function openJobsiteManager() {
    var jobNumber = readCookie('currentjobnumber');
    var jobName = readCookie('currentjobname');
    var jobId = readCookie('currentjobid');
    var jobStudyType = readCookie('currentjobstudytype');

    if (jobStudyType == STUDY_TYPE_ROADWAY) {
        openTubeDataJobsites(jobNumber, jobName, jobId);
    } else if ((jobStudyType == STUDY_TYPE_TMC) || (jobStudyType == STUDY_TYPE_ADT)) {
        openVideoDataJobsites(jobNumber, jobName, jobId);
    } else {
        openJobsites(jobNumber, jobName, jobId);
    }
}
/* Tube Data jobsites
*/
function openTubeDataJobsites(jobNumber, jobName, jobId)
{
    // call server to get list of available projects
    var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + readCookie("authToken") + "&jobid=" + jobId + "&infolevel=" + INFO_LEVEL_BASIC;
    $.ajax({
        type: "GET",
        url: "MethodCall.php/Job::GetJobSites",
        data: paramsString,
        contentType: "application/json; charset=utf-8",
        dataType: "html",
        cache: false,
        success: function (result) {
            jsonResponse = JSON.parse(result);
            var response = jsonResponse['results']['response'];
            if (response == "success") {
                var row;
                var cell;

                // load selectprojectform
                $("#jobsitesTarget").load("htmlpages/tubedatajobsites.htm", function() {
                    // load project values from server into newly loaded form
                    var jobsitesArr = jsonResponse['results']['returnval']['jobsites'];

                    if (jobsitesArr.length)
                    {
                        var jobsitestable = document.getElementById('jobsitestablerows');

                        // if a currentJobsite has been selected, show it highlighted and at the top of the form
                        var selectedJobSiteId = readCookie('currentjobsiteid');
                        var foundSelectedJobSiteId = false;

                        for (i=0; i<jobsitesArr.length; i++)
                        {
                            jobsiteupdated = jobsitesArr[i]['lastupdatetime'];

                            // populate each column in jobs table
                            row = jobsitestable.insertRow(0);

                            cell = row.insertCell(0);

                            if (selectedJobSiteId === jobsitesArr[i].jobsiteid)
                            {
                                foundSelectedJobSiteId = true;

                                cell.setAttribute("data-selected", true);

                                // highlight selected row
                                row.style.color = "#ffffff";
                                row.style.backgroundColor = "#0065ca";
                            }
                            else
                            {
                                cell.setAttribute("data-selected", false);

                                row.style.color = "#000000";
                            }

                            if (jobsitesArr[i].testdata == "1")
                            {
                                cell.innerHTML = "*" + jobsitesArr[i].sitecode;
                            }
                            else
                            {
                                cell.innerHTML = jobsitesArr[i].sitecode;
                            }

                            cell.setAttribute("id", jobsitesArr[i].jobsiteid);
                            cell.setAttribute("data-selected", false);
                            cell.setAttribute("data-reportformat", jobsitesArr[i].reportformat);
                            cell.setAttribute("data-reportparameters", jobsitesArr[i].reportparameters);

                            cell = row.insertCell(1);
                            cell.innerHTML = jobsitesArr[i].description;

                            cell = row.insertCell(2);

                            if (typeof jobsiteupdated !== 'undefined' && jobsiteupdated !== "") {
                                cell.innerHTML = jobsiteupdated.split(" ",1);
                            } else {
                                cell.innerHTML = "-";
                            }

                            cell = row.insertCell(3);
                            cell.innerHTML = jobsitesArr[i].direction;

                            cell = row.insertCell(4);
                            cell.innerHTML = "<input name='reverse' type='checkbox' id='reverse'/><label for='reverse'><span><span></span></span></label>";

                            cell = row.insertCell(5);
                            cell.innerHTML = "-";
                        }
                    }
                    // if no jobsites for this job, disable attach files buttons 
                    else {
                        document.getElementById('associatefilesbtn').disabled = true;
                    }

                    // initalize jobsite process data form 
                    initProcessDataForm();

                    if (foundSelectedJobSiteId)
                    {
                        document.getElementById('deletejobsitebtn').disabled = false;
                    }
                    else
                    {
                        // erase current jobsite cookies
                        eraseCookie("currentjobsiteid");
                        document.getElementById('deletejobsitebtn').disabled = true;
                    }

                    // disable upload files by default
                    document.getElementById('uploadfilesbtn').disabled = true;

                    // set current Job header
                    document.getElementById('jobsitesidentifiername').innerHTML = jobNumber+" - "+jobName;

                    // hide open form
                    hideOpenForm();

                    // define current navigation position
                    updateMenuLocation("jobsitemanager");

                    // display jobsiteManager
                    $(".jobsites-content").animate({width:'toggle'},350);
                });
            }
            // else handle error for user 
            else
            {
                if (jsonResponse['results']['returnval']['resultstring'] == "login required")
                {
                    // close this form, inform user and logout
                    loginRequired();
                }
                else
                {
                    showErrorDialog("Unknown server error");
                }
            }
        },
        error: function (request, status, error) {
            showErrorDialog("Server error: " + status);
        }
    });
}
/* Video Jobsites
*/
function openVideoDataJobsites(
    jobNumber,
    jobName,
    jobId
    )
{
    JobGetJobSites(
            jobId,
            INFO_LEVEL_SUMMARY,
            function(context, textStatus, response, resultstring, jobsitesArr)
            {
                if ((textStatus === "success") && (response === "success"))
                {
                    // load selectprojectform
                    $("#jobsitesTarget").load(
                            "htmlpages/videodatajobsites.htm",
                            function()
                            {
                                // if a currentJobsite has been selected, show it highlighted and at the top of the form
                                var selectedJobSiteId = readCookie('currentjobsiteid');
                                var foundSelectedJobSiteId = false;

                                eraseCookie("currentjobsiteid");

                                if (jobsitesArr.length)
                                {
                                    var jobsitestable = document.getElementById('videojobsitestablerows');

                                    for (i=0; i<jobsitesArr.length; i++)
                                    {
                                        jobsiteupdated = jobsitesArr[i]['lastupdatetime'];

                                        // get number of videos and layouts for the jobsite
                                        var videosArr = jobsitesArr[i].videodata;
                                        var numdevices = jobsitesArr[i].numdevices;
                                        var numvideos = videosArr.length;
                                        var numlayouts = 0;

                                        for (j=0; j<numvideos; j++) {
                                            numlayouts += parseInt(videosArr[j].numlayouts, 10);
                                        }

                                        // populate each column in jobs table
                                        var row = jobsitestable.insertRow(0);
                                        var cell = row.insertCell(0);

                                        if (jobsitesArr[i].testdata == "1")
                                        {
                                            cell.innerHTML = "*" + jobsitesArr[i].sitecode;
                                        }
                                        else
                                        {
                                            cell.innerHTML = jobsitesArr[i].sitecode;
                                        }

                                        cell.setAttribute("id", jobsitesArr[i].jobsiteid);

                                        cell.setAttribute("data-selected", false);

                                        row.style.color = "#000000";
                                        row.style.backgroundColor = "";

                                        cell.setAttribute("data-jobsite", JSON.stringify(jobsitesArr[i]));

                                        cell = row.insertCell(1);
                                        cell.innerHTML = jobsitesArr[i].description;
                                        cell = row.insertCell(2);

                                        if ((typeof jobsiteupdated !== 'undefined') && (jobsiteupdated !== ""))
                                        {
                                            cell.innerHTML = jobsiteupdated.split(" ",1);
                                        }
                                        else
                                        {
                                            cell.innerHTML = "-";
                                        }

                                        cell = row.insertCell(3);
                                        cell.innerHTML = jobsitesArr[i].countpriority;

                                        // cell = row.insertCell(4);
                                        // cell.innerHTML = numdevices;

                                        cell = row.insertCell(4);
                                        cell.innerHTML = numvideos;

                                        cell = row.insertCell(5);
                                        cell.innerHTML = numlayouts;

                                        cell = row.insertCell(6);
                                        if (jobsitesArr[i].status== "COMPLETE" || jobsitesArr[i].status== "PROCESSING" || jobsitesArr[i].status== "UPLOADED" ||jobsitesArr[i].status== "READY" || jobsitesArr[i].status== "COUNT_PAUSED" || jobsitesArr[i].status== "REQUIRES FEEDBACK")
                                        {
                                            cell.innerHTML = jobsitesArr[i].status;
                                        }
                                        else
                                        {
                                            cell.innerHTML = "NO LAYOUTS";
                                        }    
                                    }
                                }

                                // initalize jobsite manager form 
                                initVideoJobsites();

                                // disable actions
                                document.getElementById('uploadvideobtn').disabled = true;
                                document.getElementById('getusercountsbtn').disabled = true;
                                document.getElementById('editvideojobsitebtn').disabled = true;
                                document.getElementById('deletevideojobsitebtn').disabled = true;

                                // set current Job header
                                document.getElementById('videojobsitesidentifiername').innerHTML = jobNumber+" - "+jobName;

                                // hide open form
                                hideOpenForm();

                                // define current navigation position
                                updateMenuLocation("jobsitemanager");

                                // display jobsiteManager
                                $(".jobsites-content").animate({width:'toggle'},350);
                            }
                            );
                }
                // else handle error for user
                else
                {
                    if (resultstring === "login required")
                    {
                        // close this form, inform user and logout
                        loginRequired();
                    }
                    else
                    {
                        showErrorDialog("Unknown server error");
                    }
                }
            },
            null
            );

}
