var g_filter;

function initFilter() {
    g_filter = {
        keywords: null,
        studyType: null,
        jobSiteStatus: null,
        taskId: null,
        jobId: null,
        sinceDate: null
    };
}

function setFilter(keywords, studyType, sinceDate, jobId, taskId, jobSiteStatus) {
    // Split the keywords by spaces.
    var keywordParts = (keywords === null) ? null : keywords.split(" ");
    g_filter = {
        keywords: keywordParts,
        studyType: studyType, 
        jobSiteStatus: (jobSiteStatus === undefined) ? null : jobSiteStatus,
        jobId: (jobId === undefined) ? null : jobId,
        taskId: (taskId === undefined) ? null : taskId,
        sinceDate: (sinceDate === undefined) ? null : sinceDate
    };
}

function clearFilter() {
    initFilter();
}