/* TaskProxy
*/
function TaskUpdate(
	taskid,
	name,
	setupdate,
	devicetype,
	taskstatus,
	assignedto,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&taskid=" + taskid;

	if (name != null)
	{
		paramsString += "&name=" + encodeURIComponent(name);
	}

	if (setupdate != null)
	{
		paramsString += "&setupdate=" + encodeURIComponent(setupdate);
	}

	if (devicetype != null)
	{
		paramsString += "&devicetype=" + encodeURIComponent(devicetype);
	}

	if (taskstatus != null)
	{
		paramsString += "&status=" + encodeURIComponent(taskstatus);
	}

	if (assignedto != null)
	{
		paramsString += "&assignedto=" + encodeURIComponent(assignedto);
	}

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Task::Update",
				data: paramsString,
				dataType: "html",
				cache: false,
				success: function(data, textStatus, jqXHR)
				{
					jsonResponse = JSON.parse(data);
					var responder = jsonResponse['results']['responder'];
					var response = jsonResponse['results']['response'];
					var returnval = jsonResponse['results']['returnval'];

					callback(
							context,
							textStatus,
							response,
							returnval.resultstring
					);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function TaskDelete(
	taskid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&taskid=" + taskid;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Task::Delete",
				data: paramsString,
				dataType: "html",
				cache: false,
				success: function(data, textStatus, jqXHR)
				{
					jsonResponse = JSON.parse(data);
					var responder = jsonResponse['results']['responder'];
					var response = jsonResponse['results']['response'];
					var returnval = jsonResponse['results']['returnval'];

					callback(
							context,
							textStatus,
							response,
							returnval.resultstring
							);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function TaskCreateJobSite(
	taskid,
	jobid,
	sitecode,
	studytype,
	latitude,
	longitude,
	setupdate,
	durations,
	timeblocks,
	taskstatus,
	description,
	notes,
	reportformat,
	direction,
	oneway,
	countpriority,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&taskid=" + taskid;
	paramsString += "&jobid=" + jobid;
	paramsString += "&sitecode=" + encodeURIComponent(sitecode);
	paramsString += "&studytype=" + encodeURIComponent(studytype);
	paramsString += "&latitude=" + encodeURIComponent(latitude);
	paramsString += "&longitude=" + encodeURIComponent(longitude);
	paramsString += "&setupdate=" + encodeURIComponent(setupdate);
	paramsString += "&status=" + encodeURIComponent(taskstatus);

	if (durations != null)
	{
		for (i=0; i<durations.length; i++)
		{
			paramsString += "&duration_start_" + i + "=" + encodeURIComponent(durations[i].start);
			paramsString += "&duration_end_" + i + "=" + encodeURIComponent(durations[i].end);
		}
	}

	if (timeblocks != null)
	{
		for (i=0; i<timeblocks.length; i++)
		{
			paramsString += "&timeblock_start_" + i + "=" + encodeURIComponent(timeblocks[i].start);
			paramsString += "&timeblock_end_" + i + "=" + encodeURIComponent(timeblocks[i].end);
		}
	}

	if (description != null)
	{
		paramsString += "&description=" + encodeURIComponent(description);
	}

	if (notes != null)
	{
		paramsString += "&notes=" + encodeURIComponent(notes);
	}

	if (reportformat != null)
	{
		paramsString += "&reportformat=" + encodeURIComponent(reportformat);
	}

	if (direction != null)
	{
		paramsString += "&direction=" + encodeURIComponent(direction);
	}

	if (oneway != null)
	{
		paramsString += "&oneway=" + encodeURIComponent(oneway);
	}

	if (countpriority != null)
	{
		paramsString += "&countpriority=" + encodeURIComponent(countpriority);
	}

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Task::CreateJobSite",
				data: paramsString,
				dataType: "html",
				cache: false,
				success: function(data, textStatus, jqXHR)
				{
					jsonResponse = JSON.parse(data);
					var responder = jsonResponse['results']['responder'];
					var response = jsonResponse['results']['response'];
					var returnval = jsonResponse['results']['returnval'];

					callback(
							context,
							textStatus,
							response,
							returnval.resultstring,
							returnval.jobsiteid
							);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function TaskGetJobSites(
	taskid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&taskid=" + taskid;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Task::GetJobSites",
				data: paramsString,
				dataType: "html",
				cache: false,
				success: function(data, textStatus, jqXHR)
				{
					jsonResponse = JSON.parse(data);
					var responder = jsonResponse['results']['responder'];
					var response = jsonResponse['results']['response'];
					var returnval = jsonResponse['results']['returnval'];

					callback(
							context,
							textStatus,
							response,
							returnval.resultstring,
							returnval.jobsites
							);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}
