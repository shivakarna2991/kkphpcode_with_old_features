function openNewHomeView() {   
    // load homeview page
    $("#homeTarget").load("htmlpages/homeviewnew.htm", function() {                                     
        // define current navigation position
        updateMenuLocation("homeviewnew");
        
        // Display the new home view.
        $(".home-content").show();
    });
}

