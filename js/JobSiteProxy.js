/* JobSiteProxy
*/

function JobSiteUpdate(
	jobsiteid,
	sitecode,
	latitude,
	longitude,
	setupdate,
	durations,
	timeblocks,
	taskstatus,
	description,
	notes,
	n_street,
	s_street,
	e_street,
	w_street,
	ne_street,
	nw_street,
	se_street,
	sw_street,
	direction,
	oneway,
	countpriority,
	reportformat,
	reportparameters,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&jobsiteid=" + jobsiteid;

	if (jobsiteid != null)
	{
		paramsString += "&sitecode=" + encodeURIComponent(sitecode);
	}

	if (latitude != null)
	{
		paramsString += "&latitude=" + encodeURIComponent(latitude);
	}

	if (longitude != null)
	{
		paramsString += "&longitude=" + encodeURIComponent(longitude);
	}

	if (setupdate != null)
	{
		paramsString += "&setupdate=" + encodeURIComponent(setupdate);
	}

	if (durations != null)
	{
		for (i=0; i<durations.length; i++)
		{
			paramsString += "duration_start_" + i + "=" + encodeURIComponent(durations[i].start);
			paramsString += "duration_end_" + i + "=" + encodeURIComponent(durations[i].end);
		}
	}

	if (timeblocks != null)
	{
		for (i=0; i<timeblocks.length; i++)
		{
			paramsString += "timeblock_start_" + i + "=" + encodeURIComponent(timeblocks[i].start);
			paramsString += "timeblock_end_" + i + "=" + encodeURIComponent(timeblocks[i].end);
		}
	}

	if (taskstatus != null)
	{
		paramsString += "&status=" + encodeURIComponent(taskstatus);
	}

	if (description != null)
	{
		paramsString += "&description=" + encodeURIComponent(description);
	}

	if (notes != null)
	{
		paramsString += "&notes=" + encodeURIComponent(notes);
	}

	if (n_street != null)
	{
		paramsString += "&n_street=" + encodeURIComponent(n_street);
	}

	if (s_street != null)
	{
		paramsString += "&s_street=" + encodeURIComponent(s_street);
	}

	if (e_street != null)
	{
		paramsString += "&e_street=" + encodeURIComponent(e_street);
	}

	if (w_street != null)
	{
		paramsString += "&w_street=" + encodeURIComponent(w_street);
	}

	if (ne_street != null)
	{
		paramsString += "&ne_street=" + encodeURIComponent(ne_street);
	}

	if (nw_street != null)
	{
		paramsString += "&nw_street=" + encodeURIComponent(nw_street);
	}

	if (se_street != null)
	{
		paramsString += "&se_street=" + encodeURIComponent(se_street);
	}

	if (sw_street != null)
	{
		paramsString += "&sw_street=" + encodeURIComponent(sw_street);
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

	if (reportformat != null)
	{
		paramsString += "&reportformat=" + encodeURIComponent(reportformat);
	}

	if (reportparameters != null)
	{
		paramsString += "&reportparameters=" + encodeURIComponent(reportparameters);
	}

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/JobSite::Update",
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
							returnval.jobsiteid,
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

function JobSiteGetInfo(
	jobsiteid,
	infolevel,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&jobsiteid=" + jobsiteid;
	paramsString += "&infolevel=" + infolevel;

	return $.ajax(
		{
			type: "GET",
			url: "MethodCall.php/JobSite::GetInfo",
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
						returnval.jobinfo,
						returnval.jobsiteinfo
						);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				callback(context, textStatus);
			}
		}
		);
}

function JobSiteDelete(
	jobsiteid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&jobsiteid=" + jobsiteid;

	return $.ajax(
		{
			type: "GET",
			url: "MethodCall.php/JobSite::Delete",
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
			error: function (jqXHR, textStatus, errorThrown)
			{
				callback(context, textStatus);
			}
		}
		);
}

function JobSiteAssignDevice(
	jobsiteid,
	deviceid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&jobsiteid=" + jobsiteid;
	paramsString += "&deviceid=" + deviceid;

	return $.ajax(
		{
			type: "GET",
			url: "MethodCall.php/JobSite::AssignDevice",
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

function JobSiteUnassignDevice(
	jobsiteid,
	deviceid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&jobsiteid=" + jobsiteid;
	paramsString += "&deviceid=" + deviceid;

	return $.ajax(
		{
			type: "GET",
			url: "MethodCall.php/JobSite::UnassignDevice",
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
