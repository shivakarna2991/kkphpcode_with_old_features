/* JobManagerProxy
*/
function DeviceUpdate(
	deviceid,
	type,
	manufacturer,
	model,
	serialnumber,
	latitude,
	longitude,
	ipv4address,
	port,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&deviceid=" + encodeURIComponent(deviceid);

	if (type != null)
	{
		paramsString += "&type=" + encodeURIComponent(type);
	}

	if (manufacturer != null)
	{
		paramsString += "&manufacturer=" + encodeURIComponent(manufacturer);
	}

	if (model != null)
	{
		paramsString += "&model=" + encodeURIComponent(model);
	}

	if (serialnumber != null)
	{
		paramsString += "&serialnumber=" + encodeURIComponent(serialnumber);
	}

	if (latitude != null)
	{
		paramsString += "&latitude=" + encodeURIComponent(latitude);
	}

	if (longitude != null)
	{
		paramsString += "&longitude=" + encodeURIComponent(longitude);
	}

	if (ipv4address != null)
	{
		paramsString += "&ipv4address=" + encodeURIComponent(ipv4address);
	}

	if (port != null)
	{
		paramsString += "&port=" + encodeURIComponent(port);
	}

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Device::Update",
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

function DeviceGetInfo(
	deviceid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&deviceid=" + encodeURIComponent(deviceid);

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Device::GetInfo",
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
							returnval.device
							);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					callback(context, textStatus);
				}
			}
			);
}

function DeviceDelete(
	deviceid,
	callback,
	context
	)
{
	var authToken = readCookie("authToken");
	var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
	paramsString += "&deviceid=" + encodeURIComponent(deviceid);

	return $.ajax(
			{
				type: "GET",
				url: "MethodCall.php/Device::Delete",
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
