<?php
    // automatically extrct $urlkey=value for use in documentready
    parse_str($_SERVER['QUERY_STRING']);    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta charset="utf-8" />

    <title>IDAX</title>

    <link href="../css/redir_base.css" rel="stylesheet" type="text/css" />
    <link href="../css/redir_setpassword.css" rel="stylesheet" type="text/css" />
    <style type="text/css" media="screen"></style>
    
    <script src="../js/setpassword.js" type="text/javascript"></script>
    <script src="../js/helperfunctions.js" type="text/javascript"></script>
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

    <script>
        var urlkey = "undefined";
        // autorun on load function
        $(document).ready(function() {
            // load global error dialog
            urlkey = "<?php echo $urlkey; ?>";
        });  
    </SCRIPT>

  </head>
  
  <body>

    <div id="main_page">    

    <!-- placeholder for usermessage dialog -->
    <div class="setpass-overlay" id="setpass-usermessage">
      <div class="setpass-wrapper">
        <div class="setpass-content">
          <h2>Request Completed</h2>
          <br />
          <fieldset>
            <label id="usermessagelabel">New password successfully set.</label>
          </fieldset>
          <br />
          <div id="dlgbuttons">
            <button onclick="cancelUserMessageDialog()">OK</button>
          </div>
        </div>
      </div>
    </div>

    <div class="setpass-overlay" id="setpass-setpassword">
      <div class="setpass-wrapper">
        <div class="setpass-content">
          <h2>IDAX Set Password</h2>
          <br />
          <div id="setpassword-errorfield">Enter your new password below</div>
          <fieldset>
            <label for="newpassword">New Password
              <input type="password" name="newpassword" id="newpassword" placeholder="8-20 chars, at least 1 number and 1 uppercase letter" pattern="(?=\d*)(?=[a-z]*)(?=[A-Z]*).{8,20}" required />
            </label>
            <label for="newpassword2">Retype New Password
              <input type="password" name="newpassword2" id="newpassword2" placeholder="8-20 chars, at least 1 number and 1 uppercase letter" pattern="(?=\d*)(?=[a-z]*)(?=[A-Z]*).{8,20}" required />
            </label>
          </fieldset>
          <br />
          <div id="dlgbuttons">
            <button id="passwordsavebtn" onclick="validateChangePassword()">Submit</button>
          </div>
        </div>
      </div>
    </div>

    </div> <!-- main-page -->
    
  </body>

  <footer> 
    <div class="bar-wrap"> 
      <div class="copyright"><img src="../images/IDAX_2-white-30w.png" width="30" />, Inc. &copy;  2016 All Rights Reserved</div> 
    </div> 
  </footer> 
</html>

