/* TubeReportManagerProxy
*/
function TubeReportManagerCreateReportFormat(
	name,
	fields,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&name=" + encodeURIComponent(name);

	for (var i=0; i<fields.length; i++)
	{
		paramsString += "&field_" + i + "=" + encodeURIComponent(fields[i]);
	}

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/TubeReportFormatManager::CreateReportFormat",
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

function TubeReportManagerGetReportFormats(
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/TubeReportFormatManager::GetReportFormats",
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
							returnval.reportformats
							);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}
