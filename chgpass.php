<?php
include_once("php_includes/check_login_status.php");
if(!isset($_SESSION)){
	session_start();
}
// If user is logged in, header them away
if(isset($_SESSION["username"])){
	header("location: message.php?msg=NO to that weenis");
    exit();
}
?>
<?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["u"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$u = mysqli_real_escape_string($db_conx, $_POST['u']);
	$p1 = mysqli_real_escape_string($db_conx, $_POST['p1']);
	$p2 = mysqli_real_escape_string($db_conx, $_POST['p2']);
	$s = mysqli_real_escape_string($db_conx, $_POST['s']);
	$p_hash = md5($p1);

	if ($p1 != $p2) {
		echo "error";
	}
	else{
		
		$sql = "UPDATE users SET password='$p_hash' WHERE username='$u' and seccode='$s' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        echo mysqli_error($db_conx);
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Sign Up</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="style/style.css">
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<style type="text/css">
#changepass{
	margin:24px;	
}
#changepass > div {
	margin-top: 12px;	
}
#changepass > fieldset{
	width: 100%;
}
label{
	font-weight: bold;
}
.btn {
  font-family: Arial;
  color: #666770;
  font-size: 15px;
  background: #ffffff;
  padding: 10px 10px 10px 10px;
  border: solid #000000 2px;
  text-decoration: none;
}

.btn:hover {
  background: #C0E73D;
  text-decoration: none;
  cursor: pointer;
}
#status{
	font-weight: bold;;
	text-align: center;
	font-size: 25px;
	color: red;
	padding: 0px 5px; margin: 0px 5px;
}
input[type="text"], input[type="password"], input[type="email"], textarea, 
select{
    width: 25%; color: #3300cc; padding: 10px 10px 10px 10px;
    margin: 10px 10px 10px 10px;
</style>
<script type="text/javascript">
	function emptyElement(x){
		_(x).innerHTML = "";
	}
	
	function chgpass(){
		var u = _("username").value;
		var p1 = _("pass1").value;
		var p2 = _("pass2").value;
		var s = _("seccode").value;
		if(u == "" || p1 == "" || p2 == "" || s == ""){
			_("status").innerHTML = "Fill out all of the form data";
		} else if(p1 != p2) {
			_("status").innerHTML = "Your password fields do not match. "+p1+" | "+p2;
		} else {
			_("chgbtn").style.display = "none";
			status.innerHTML = 'please wait ...';
			var ajax = ajaxObj("POST", "chgpass.php");
        	ajax.onreadystatechange = function() {
	        	if(ajaxReturn(ajax) == true) {
	            	if(ajax.responseText == "error"){
						_("status").innerHTML = "Passwords Don't Match or Security Code Entered is Wrong";
						_("signupbtn").style.display = "block";
					} else {
						window.scrollTo(0,0);
						_("changepass").innerHTML = "Password has been successfully updated: "+ u +" if you're still facing a login problem, after Password Reset,"						+" Then Mostly the Security Code Submitted is Wrong!";
					}
	        	}
        	}
        	ajax.send("u="+u+"&p1="+p1+"&p2="+p2+"&s="+s);
		}
	}
</script>
</head>
<body>
<?php include_once("template_pageTop.php"); ?>
<div id="pageMiddle" style="height: 476px;">
  <form name="changepass" id="changepass" onsubmit="return false;">
  	<fieldset>
  	<legend><h3>Forgot Password?</h3></legend>
    <div>
	    <label>Username: </label>
	    <input id="username" type="text" onfocus="emptyElement('status')">
    </div>
    <div>
    	<label>New Password: </label>
    	<input id="pass1" type="password" onfocus="emptyElement('status')" maxlength="16">
    </div>
    <div>
    	<label>Confirm Password: </label>
    	<input id="pass2" type="password" onfocus="emptyElement('status')" maxlength="16">
    </div>
    <div>
    	<label>Security Code: </label>
    	<input id="seccode" type="password" onfocus="emptyElement('status')" maxlength="5">
    </div>
    <br/><hr/>
    <p id="status"></p>
    <br /><hr /><br />
    <div>
    	<button id="chgbtn" class="btn" style="float: left; width:100%;" onclick="chgpass()">
    		Change Password
    	</button>
    </div>
    </fieldset>
  </form>  
</div>
<?php include_once("template_pageBottom.php"); ?>
</body>
</html>