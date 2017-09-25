/* KapturrKamProxy
*/
function KapturrKamQueryStatus(
	kamid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&kamid=" + kamid;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/VideoKapturrKam::QueryStatus",
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
							returnval.deviceresponded,
							returnval.resultstring,
							returnval.status,
							returnval.time
							);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function KapturrKamGetSessions(
	kamid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&kamid=" + kamid;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/VideoKapturrKam::GetSessions",
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
							returnval.deviceresponded,
							returnval.resultstring,
							returnval.sessions,
							returnval.time
							);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function KapturrKamGetCachedSessions(
	kamid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&kamid=" + kamid;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/VideoKapturrKam::GetCachedSessions",
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
							returnval.sessions,
							returnval.time
							);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function KapturrKamAddScheduledCaptureSession(
	kamid,
	startTime,
	endTime,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&kamid=" + kamid;
	paramsString += "&starttime=" + startTime;
	paramsString += "&endtime=" + endTime;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/VideoKapturrKam::AddScheduledCaptureSession",
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
							returnval.deviceresponded,
							returnval.resultstring,
							returnval.session,
							returnval.time
							);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function KapturrKamUpdateScheduledCaptureSession(
	kamid,
	sessionId,
	startTime,
	endTime,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&kamid=" + kamid;
	paramsString += "&sessionid=" + sessionId;
	paramsString += "&starttime=" + startTime;
	paramsString += "&endtime=" + endTime;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/VideoKapturrKam::UpdateScheduledCaptureSession",
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
							returnval.deviceresponded,
							returnval.resultstring,
							returnval.session
							);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function KapturrKamDeleteSessions(
	kamid,
	sessionIds,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&kamid=" + kamid;

	for (i=0; i<sessionIds.length; i++)
	{
		paramsString += "&sessionid_" + i + "=" + sessionIds[i];
	}

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/VideoKapturrKam::DeleteSessions",
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
							returnval.deviceresponded,
							returnval.resultstring,
							returnval.sessions,
							returnval.time
							);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function KapturrKamUploadVideos(
	kamid,
	sessionIds,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&kamid=" + kamid;

	for (i=0; i<sessionIds.length; i++)
	{
		paramsString += "&sessionid_" + i + "=" + sessionIds[i];
	}

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/VideoKapturrKam::UploadVideos",
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
							returnval.deviceresponded,
							returnval.resultstring,
							returnval.sessions
							);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function KapturrKamControlLiveStreaming(
	kamid,
	startStream,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&kamid=" + kamid;
	paramsString += "&startstream=";

	if (startStream)
	{
		paramsString += "1";
	}
	else
	{
		paramsString += "0";
	}

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/VideoKapturrKam::ControlLiveStreaming",
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
							returnval.deviceresponded,
							returnval.resultstring,
							returnval.active,
							returnval.url
							);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function KapturrKamGetDeviceLog(
	kamid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&kamid=" + kamid;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/VideoKapturrKam::GetDeviceLog",
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
							returnval.deviceresponded,
							returnval.resultstring,
							returnval.log
							);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}
