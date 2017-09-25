/* functions to help with the uploading videos and showing a progress bar
 */
 
function startUploadingFiles(selectedFiles, fileUploadJobId, context)
{
    var xhr = new XMLHttpRequest();

    xhr.upload.addEventListener(
            "progress",
            function (evt) {
                if (evt.lengthComputable) {
                    fileUploadUpdateJob(fileUploadJobId, evt.total, evt.loaded);
                }
            },
            false
            );

    xhr.addEventListener(
            "load",
            function (evt) {
                var successRes = evt.target.responseText.split(":");
                if (successRes[0] == "success") {
                    var uploadFilenames = successRes[1];
                    finishUpload(uploadFilenames, fileUploadJobId, context);
                }
            },
            false
            );

    xhr.addEventListener(
            "error",
            function uploadFailed(evt) {
                fileUploadCompleteJob(fileUploadJobId, false, "An error occurred during upload");
            },
            false
            );

    xhr.addEventListener(
            "abort",
            function uploadCanceled(evt) {
                fileUploadCompleteJob(fileUploadJobId, false, "Upload was cancelled or the browser dropped the connection.");
            },
            false
            );

    xhr.open("POST", "UploadDownload/uploadvideofiles.php");

    var dataparams = new FormData();

    for (var i = 0; i<selectedFiles.length; i++)
    {
        dataparams.append ('upfile[]', selectedFiles[i]);
    }

    // append authorization token
    dataparams.append(METHODCALL_HEADER_PARAM_AUTHTOKEN, readCookie('authToken'));
    dataparams.append('numfiles', 1);

    xhr.send(dataparams);
}

/* kick off the process
 */
function beginVideoUpload(jobsiteId, selectedFiles, videoName, cameraLocation, startDate, numSegments)
{
    var context = { jobsiteid: jobsiteId, videoname: videoName, cameraLocation: cameraLocation, startdate: startDate, filesPerSegment: numSegments };

    fileUploadJobId = fileUploadAddJob(videoName, context);

    // start upload
    startUploadingFiles(selectedFiles, fileUploadJobId, context);
}

function finishUpload(uploadFilenames, fileUploadJobId, context)
{
    // call server to register the new upload and kick off transcoding in the background
    var authToken = readCookie("authToken");
    var fileUploadDetails = fileUploadGetJobDetails(fileUploadJobId);
    var endTime = new Date();
    var uploadTime = endTime - fileUploadDetails.starttime;
    var filenames = uploadFilenames.split("*;*");
    var filenameparams = "";

    for (var i=0; i<filenames.length; i++) {
        filenameparams += "&filename_" + i + "=" + encodeURIComponent(filenames[i].trim());
    }

    var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
    paramsString += "&jobsiteid=" + context.jobsiteid;
    paramsString += filenameparams;
    paramsString += "&name=" + encodeURIComponent(context.videoname);
    paramsString += "&cameralocation=" + encodeURIComponent(context.cameraLocation);
    paramsString += "&capturestarttime=" + encodeURIComponent(context.startdate + ":00");
    paramsString += "&filespersegment=" + context.filesPerSegment;
    paramsString += "&uploadtime=" + uploadTime;

    $.ajax({
        type: "GET",
        url: "MethodCall.php/VideoJobSite::UploadVideo",
        data: paramsString,
        dataType: "html",
        cache: false,
        success: function(result) {
            jsonResponse = JSON.parse(result);
            var response = jsonResponse['results']['response'];
            if (response == "success") {
                fileUploadCompleteJob(fileUploadJobId, true, "");
            }
            // else handle error for user
            else {
                fileUploadCompleteJob(fileUploadJobId, false, jsonResponse.results.returnval.resultstring);
            }
        },
        error: function (request, status, error) {
            fileUploadCompleteJob(fileUploadJobId, false, "Server error: " + status);
        }
    });
}
