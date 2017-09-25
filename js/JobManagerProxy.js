/* JobManagerProxy
*/
function JobManagerCreateJob(
	jobnumber,
	jobname,
	nickname,
	studytype,
	office,
	area,
	notes,
	orderdate,
	deliverydate,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&number=" + encodeURIComponent(jobnumber);
	paramsString += "&name=" + encodeURIComponent(jobname);
	paramsString += "&nickname=" + encodeURIComponent(nickname);
	paramsString += "&studytype=" + encodeURIComponent(studytype);
	paramsString += "&office=" + encodeURIComponent(office);

	if (area != null)
	{
		paramsString += "&area=" + encodeURIComponent(area);
	}

	if (notes != null)
	{
		paramsString += "&notes=" + encodeURIComponent(notes);
	}

	if (orderdate != null)
	{
		paramsString += "&orderdate=" + encodeURIComponent(orderdate);
	}

	if (deliverydate != null)
	{
		paramsString += "&deliverydate=" + encodeURIComponent(deliverydate);
	}

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/JobManager::CreateJob",
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
							returnval.jobid
							);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function JobManagerGetJobs(
	infolevel,
	activeonly,
	studytypes,
	since,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&infolevel=" + infolevel;

	if (activeonly != null)
	{
		paramsString += "&activeonly=" + encodeURIComponent(activeonly);
	}

	if (studytypes != null)
	{
		for (i=0; i<studytypes.length; i++)
		{
			paramsString += "&studytype_=" + i + encodeURIComponent(studytypes[i]);
		}
	}

	if (since != null)
	{
		paramsString += "&since=" + encodeURIComponent(since);
	}

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/JobManager::GetJobs",
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
							returnval.jobs
							);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}
