/* JobProxy
*/
function JobUpdate(
	jobid,
	number,
	name,
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
	paramsString += "&jobid=" + encodeURIComponent(jobid);

	if (number != null)
	{
		paramsString += "&number=" + encodeURIComponent(number);
	}

	if (name != null)
	{
		paramsString += "&name=" + encodeURIComponent(name);
	}

	if (nickname != null)
	{
		paramsString += "&nickname=" + encodeURIComponent(nickname);
	}

	if (studytype != null)
	{
		paramsString += "&studytype=" + encodeURIComponent(studytype);
	}

	if (office != null)
	{
		paramsString += "&office=" + encodeURIComponent(office);
	}

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
				url: "MethodCall.php/Job::Update",
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

function JobGetInfo(
	jobid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&jobid=" + jobid;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Job::GetInfo",
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
							returnval.job
							);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function JobDelete(
	jobid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&jobid=" + jobid;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Job::Delete",
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

function JobClose(
	jobid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&jobid=" + jobid;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Job::Close",
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

function JobCreateJobSite(
	jobid,
	sitecode,
	latitude,
	longitude,
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
	status,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&jobid=" + encodeURIComponent(jobid);
	paramsString += "&sitecode=" + encodeURIComponent(sitecode);

	if (latitude != null)
	{
		paramsString += "&latitude=" + encodeURIComponent(latitude);
	}

	if (longitude != null)
	{
		paramsString += "&longitude=" + encodeURIComponent(longitude);
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
	if (status != null)
	{
		paramsString += "&status=" + encodeURIComponent(status);
	}

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Job::CreateJobSite",
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

function JobGetJobSites(
	jobid,
	infolevel,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&jobid=" + jobid;
	paramsString += "&infolevel=" + infolevel;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Job::GetJobSites",
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

function JobCreateTask(
	jobid,
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
	paramsString += "&jobid=" + encodeURIComponent(jobid);
	paramsString += "&name=" + encodeURIComponent(name);
	paramsString += "&setupdate=" + encodeURIComponent(setupdate);
	paramsString += "&devicetype=" + encodeURIComponent(devicetype);
	paramsString += "&status=" + encodeURIComponent(taskstatus);
	paramsString += "&assignedto=" + encodeURIComponent(assignedto);

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Job::CreateTask",
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
							returnval.taskid
							);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function JobGetTasks(
	jobid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&jobid=" + jobid;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Job::GetTasks",
				data: paramsString,
				dataType: "html",
				cache: false,
				success: function(data, textStatus, jqXHR)
				{
					jsonResponse = JSON.parse(data);
					var responder = jsonResponse['results']['responder'];
					var response = jsonResponse['results']['response'];
					var returnval = jsonResponse['results']['returnval'];

					callback(context, textStatus, response, returnval.resultstring, returnval.tasks);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}
