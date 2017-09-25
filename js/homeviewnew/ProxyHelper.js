function ProxyHelperGetTasks(jobid, callback, context) {
    JobGetTasks(jobid, function(context, textStatus, response, resultStr, tasks) {
        if (textStatus != "success" || response != "success") {
            callback(context, textStatus, response, resultStr, tasks);
            return;
        }
        callback(context, textStatus, response, resultStr, tasks);
    });
}

function ProxyHelperGetTask(jobid, taskid, callback, context)
{
    ProxyHelperGetTasks(
			jobid,
			function (context, textStatus, response, resultStr, tasks)
			{
				if (textStatus == "success" && response == "success")
				{
					for (var i = 0; i < tasks.length; ++i)
					{
						if (tasks[i].taskid == taskid)
						{
							callback(tasks[i]);
                            return;
						}
					}
                    
                    // The matching task was never found.
                    callback(null);
				}
				else
				{
					callback(null);
				}
			},
			null
		);
}

function fixStartEndArray(arrayToFix) {
    var fixedArray;
    if (!(arrayToFix instanceof Array)) {
        // Just a single value.
        fixedArray = [arrayToFix];
    }
    else {
        fixedArray = arrayToFix;
    }
    
    for (var i = 0; i < fixedArray.length; ++i) {
        if (fixedArray[i].start !== undefined) 
            continue;
        var parts = fixedArray[i].split(",");
        fixedArray[i] = {start: parts[0], end: parts[1]};
    }
    
    return fixedArray;
}

function FixJobSiteValues(jobSite) {
    if (jobSite.setUpdate === undefined) {
        jobSite.setUpdate = jobSite.setupdate;
    }
    
    if (jobSite.timeBlocks === undefined) {
        jobSite.timeBlocks = jobSite.timeblocks;
    }
    
    if (jobSite.siteCode === undefined) {
        jobSite.siteCode = jobSite.sitecode;
    }
    
    jobSite.timeBlocks = fixStartEndArray(jobSite.timeBlocks);
    jobSite.durations = fixStartEndArray(jobSite.durations);
    
    return jobSite;
}