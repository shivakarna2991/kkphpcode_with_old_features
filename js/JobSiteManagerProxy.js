/* JobSiteManager
*/


function JobSiteManagerGetJobSites(
	infolevel,
	studytypes,
	activeonly,
	jobid,
	taskid,
	keywords,
	nwlatitude,
	nwlongitude,
	selatitude,
	selongitude,
	since,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&infolevel=" + infolevel;

	if (studytypes != null)
	{
		for (i=0; i<studytypes.length; i++)
		{
			paramsString += "&studytype_" + i + "=" +  encodeURIComponent(studytypes[i]);
		}
	}

	if (activeonly != null)
	{
		paramsString += "&activeonly=" + activeonly;
	}

	if (jobid != null)
	{
		paramsString += "&jobid=" + jobid;
	}

	if (taskid != null)
	{
		paramsString += "&taskid=" + taskid;
	}

	if (keywords != null)
	{
		for (i=0; i<keywords.length; i++)
		{
			paramsString += "&keyword_" + i + "=" + encodeURIComponent(keywords[i]);
		}
	}

	if (nwlatitude != null)
	{
		paramsString += "&nwlatitude=" + encodeURIComponent(nwlatitude);
	}

	if (nwlongitude != null)
	{
		paramsString += "&nwlongitude=" + encodeURIComponent(nwlongitude);
	}

	if (selatitude != null)
	{
		paramsString += "&selatitude=" + encodeURIComponent(selatitude);
	}

	if (selongitude != null)
	{
		paramsString += "&selongitude=" + encodeURIComponent(selongitude);
	}

	if (since != null)
	{
		paramsString += "&since=" + encodeURIComponent(since);
	}

	$.ajax(
		{
			type: "GET",
			url: "MethodCall.php/JobSiteManager::GetJobSites",
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
			error: function (jqXHR, textStatus, errorThrown)
			{
				callback(context, textStatus);
			}
		}
		);
}

function JobSiteManagerSearch(
	studytype,
	taskid,
	jobid,
	keywords,
	jobsitestatus,
	nwlatitude,
	nwlongitude,
	selatitude,
	selongitude,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;

	if (studytype != null)
	{
		paramsString += "&studytype=" + studytype;
	}

	if (taskid != null)
	{
		paramsString += "&taskid=" + taskid;
	}

	if (jobid != null)
	{
		paramsString += "&jobid=" + jobid;
	}

	if (keywords != null)
	{
		paramsString += "&keywords=" + encodeURIComponent(keywords);
	}

	if (jobsitestatus != null)
	{
		paramsString += "&status=" + encodeURIComponent(jobsitestatus);
	}

	if (nwlatitude != null)
	{
		paramsString += "&nwlatitude=" + encodeURIComponent(nwlatitude);
	}

	if (nwlongitude != null)
	{
		paramsString += "&nwlongitude=" + encodeURIComponent(nwlongitude);
	}

	if (selatitude != null)
	{
		paramsString += "&selatitude=" + encodeURIComponent(selatitude);
	}

	if (selongitude != null)
	{
		paramsString += "&selongitude=" + encodeURIComponent(selongitude);
	}

	$.ajax(
		{
			type: "GET",
			url: "MethodCall.php/JobSiteManager::Search",
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
			error: function (jqXHR, textStatus, errorThrown)
			{
				callback(context, textStatus);
			}
		}
		);
}
