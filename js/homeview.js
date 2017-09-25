function openHomeView() {    
    // load homeview page
    $("#homeTarget").load("htmlpages/homeview.htm", function() {                                           
        // define current navigation position
        updateMenuLocation("homeview");

        // set appropriate nav buttons by user type
        var userrole = readCookie("userrole");
        document.getElementById('managejobsbtn').disabled = (userrole == "projectmanager" || userrole == "admin")?false:true;
        document.getElementById('manageusersbtn').disabled =(userrole == "admin")?false:true;

        // display jobManager
        $(".home-content").show();
    });
}
