/* file upload management functions
*/
var fileUploadJobs;
var fileUploadEventListeners;
var fileUploadNextJobId = 0;

function openFileUploads()
{
	$("#fileUploadsTarget").load(
			"htmlpages/fileuploads.htm",
			function()
			{
				initFileUploads();
			}
			);
}

function fileUploadsInitialize()
{
	fileUploadJobs = [];
	fileUploadEventListeners = [];
}

function fileUploadSendNotification(
	type,
	fileUploadJob
	)
{
	var event = { 'type': type, 'fileUploadJob': fileUploadJob };

	for (var i=0; i<fileUploadEventListeners.length; i++)
	{
		fileUploadEventListeners[i](event);
	}
}

function fileUploadAddEventListener(
	listener
	)
{
	fileUploadEventListeners.push(listener);
}

function fileUploadGetJobDetails(id)
{
	for (var i=0; i<fileUploadJobs.length; i++)
	{
		if (id == fileUploadJobs[i].id)
		{
			return fileUploadJobs[i];
		}
	}

	return null;
}

function fileUploadAddJob(
	name,
	context
	)
{
	fileUploadNextJobId += 1;

	var fileUploadJob =
			{
				id: fileUploadNextJobId,
				name: name,
				starttime: new Date(),
				bytestotransfer: 0,
				bytestranferred: 0,
				lastratetime: 0,
				transferrate: 0,
				progress: 0,
				conpleted: false,
				successful: null,
				errorString: null,
				context: context,
			};

	fileUploadJobs.push(fileUploadJob);

	fileUploadSendNotification('fileUploadJobAdded', fileUploadJob);

	return fileUploadNextJobId;
}

function fileUploadUpdateJob(
	id,
	totalBytes,
	bytesTransferred
	)
{
	for (var i=0; i<fileUploadJobs.length; i++)
	{
		if (id == fileUploadJobs[i].id)
		{
			var currenttime = new Date();

			// Calculuate transfer rate.  Skip calculation first time thru.
			if (fileUploadJobs[i].lastratetime)
			{
				var elapsedtime = (currenttime - fileUploadJobs[i].lastratetime) / 1000; // convert from milliseconds to decimal seconds

				// transferrate in bytes / second.
				if (elapsedtime > 0)
				{
					fileUploadJobs[i].transferrate = (bytesTransferred - fileUploadJobs[i].bytestranferred) / elapsedtime;
				}
			}

			fileUploadJobs[i].bytestotransfer = totalBytes;
			fileUploadJobs[i].bytestranferred = bytesTransferred;
			fileUploadJobs[i].progress = bytesTransferred / totalBytes;
			fileUploadJobs[i].lastratetime = currenttime;

			fileUploadSendNotification('fileUploadJobUpdated', fileUploadJobs[i]);

			break;
		}
	}
}

function fileUploadCompleteJob(
	id,
	success,
	errorString
	)
{
	for (var i=0; i<fileUploadJobs.length; i++)
	{
		if (id == fileUploadJobs[i].id)
		{
			var fileUploadJob = fileUploadJobs[i];

			fileUploadJob.completed = true;
			fileUploadJob.successful = success;

			if (!success)
			{
				fileUploadJob.errorString = errorString;
			}

			fileUploadJobs.splice(i, 1);

			fileUploadSendNotification('fileUploadJobCompleted', fileUploadJob);

			break;
		}
	}
}
