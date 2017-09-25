/* open issueReporting form
*/
function openIssueReporting() {
    $("#issueTarget").load("htmlpages/issuereportingform.htm", function() {                                            
        // hide currently open form
        hideOpenForm();                    
        // initalize accountManager form functions
        initIssueReportingForm();                    
        // set current navigation position
        updateMenuLocation("issuereporting");
        // display accountmanager form
        $(".issue-content").animate({width:'toggle'},350);
    });                
}
