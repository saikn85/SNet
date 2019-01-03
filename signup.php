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
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["usernamecheck"])){
	include_once("php_includes/db_conx.php");
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
	$sql = "SELECT id FROM users WHERE username='$username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $uname_check = mysqli_num_rows($query);
    if (strlen($username) < 3 || strlen($username) > 16) {
	    echo '<strong style="color:#F00;">3 - 16 characters please</strong>';
	    exit();
    }
	if (is_numeric($username[0])) {
	    echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
	    exit();
    }
    if ($uname_check < 1) {
	    echo '<strong style="color:#009900;">' . $username . ' is Available.</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">' . $username . ' is Taken.</strong>';
	    exit();
    }
}
?><?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["u"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = $_POST['p'];
	$g = preg_replace('#[^a-z]#', '', $_POST['g']);
	$c = preg_replace('#[^a-z ]#i', '', $_POST['c']);
	$otp = substr(mt_rand(0,99999), 0, 5);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
	$sql = "SELECT id FROM users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$u_check = mysqli_num_rows($query);
	// -------------------------------------------
	$sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$e_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if($u == "" || $e == "" || $p == "" || $g == "" || $c == ""){
		echo "The form submission is missing values.";
        exit();
	} else if ($u_check > 0){ 
        echo "The username you entered is alreay taken";
        exit();
	} else if ($e_check > 0){ 
        echo "That email address is already in use in the system";
        exit();
	} else if (strlen($u) < 3 || strlen($u) > 16) {
        echo "Username must be between 3 and 16 characters";
        exit(); 
    } else if (is_numeric($u[0])) {
        echo 'Username cannot begin with a number';
        exit();
    } else {
	// END FORM DATA ERROR HANDLING
	    // Begin Insertion of data into the database
		// Hash the password and apply your own mysterious unique salt
		
		$p_hash = md5($p);
		// Add user info into the database table for the main site table
		$sql = "INSERT INTO users (username, email, password, gender, country, ip, signup, lastlogin, notescheck, seccode)       
		        VALUES('$u','$e','$p_hash','$g','$c','$ip',now(),now(),now(), '$otp')";
		$query = mysqli_query($db_conx, $sql); 
		$uid = mysqli_insert_id($db_conx);
		// Establish their row in the useroptions table
		$sql = "INSERT INTO useroptions (id, username, background) VALUES ('$uid','$u','original')";
		$query = mysqli_query($db_conx, $sql);
		// Create directory(folder) to hold each user's files(pics, MP3s, etc.)
		if (!file_exists("user/$u")) {
			mkdir("user/$u", 0755);
		}
		// Email the user their activation link
		/*$to = "$e";							 
		$from = "auto_responder@yoursitename.com";
		$subject = 'yoursitename Account Activation';
		$message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>yoursitename Message</title></head><body style="margin:0px; font-family:Tahoma, Geneva, sans-serif;"><div style="padding:10px; background:#333; font-size:24px; color:#CCC;"><a href="http://www.yoursitename.com"><img src="http://www.yoursitename.com/images/logo.png" width="36" height="30" alt="yoursitename" style="border:none; float:left;"></a>yoursitename Account Activation</div><div style="padding:24px; font-size:17px;">Hello '.$u.',<br /><br />Click the link below to activate your account when ready:<br /><br /><a href="http://www.yoursitename.com/activation.php?id='.$uid.'&u='.$u.'&e='.$e.'&p='.$p_hash.'">Click here to activate your account now</a><br /><br />Login after successful activation using your:<br />* E-mail Address: <b>'.$e.'</b></div></body></html>';
		$headers = "From: $from\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";
		mail($to, $subject, $message, $headers);*/
		echo $otp;
		exit();
	}
	exit();
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
#signupform{
	margin:24px;	
}
#signupform > div {
	margin-top: 12px;	
}
/*#signupform > input,select {
	width: 200px;
	padding: 3px;
	background: #F3F9DD;
}*/
#sifnupform > fieldset{
	width: 35%;
}
label{
	font-weight: bold;
}
#signupbtn {
	font-size:15px;
	padding: 10px;
}
#terms {
	border:#CCC 1px solid;
	background: #F5F5F5;
	padding: 12px;
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
<script>
function restrict(elem){
	var tf = _(elem);
	var rx = new RegExp;
	if(elem == "email"){
		rx = /[' "]/gi;
	} else if(elem == "username"){
		rx = /[^a-z0-9]/gi;
	}
	tf.value = tf.value.replace(rx, "");
}
function emptyElement(x){
	_(x).innerHTML = "";
}
function checkusername(){
	var u = _("username").value;
	if(u != ""){
		_("unamestatus").innerHTML = 'checking ...';
		var ajax = ajaxObj("POST", "signup.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            _("unamestatus").innerHTML = ajax.responseText;
	        }
        }
        ajax.send("usernamecheck="+u);
	}
}
function signup(){
	var u = _("username").value;
	var e = _("email").value;
	var p1 = _("pass1").value;
	var p2 = _("pass2").value;
	var c = _("country").value;
	var g = _("gender").value;
	var status = _("status");
	if(u == "" || e == "" || p1 == "" || p2 == "" || c == "" || g == ""){
		status.innerHTML = "Fill out all of the form data";
	} else if(p1 != p2){
		status.innerHTML = "Your password fields do not match";
	} else if( _("terms").style.display == "none"){
		status.innerHTML = "Please view the terms of use";
	} else {
		_("signupbtn").style.display = "none";
		status.innerHTML = 'please wait ...';
		var ajax = ajaxObj("POST", "signup.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            if(ajax.responseText == ""){
					status.innerHTML = "Something Went Wrong";
					_("signupbtn").style.display = "block";
				} else {
					window.scrollTo(0,0);
					/*_("signupform").innerHTML = "OK "+u+", check your email inbox and junk mail box at <u>"+e+"</u> in a moment to complete the sign up process by activating your account. You will not be able to do anything on the site until you successfully activate your account.<br/> Here is Your <strong>Security Code: <span style=\"color: red;\">"+ajax.responseText+"</span> Remeber it as it'll be asked everytime you login.</strong>";*/
					_("signupform").innerHTML = "OK "+u+", Here is Your <strong>Security Code: <span style=\"color: red;\">"+ajax.responseText+"</span> Remeber it as it'll be asked everytime you login.</strong>";
				}
	        }
        }
        ajax.send("u="+u+"&e="+e+"&p="+p1+"&c="+c+"&g="+g);
	}
}
function openTerms(){
	_("terms").style.display = "block";
	emptyElement("status");
}
</script>
</head>
<body>
<?php include_once("template_pageTop.php"); ?>
<div id="pageMiddle">
  <form name="signupform" id="signupform" onsubmit="return false;">
  	<fieldset>
  	<legend><h3>Sign Up Here</h3></legend>
    <div>
	    <label>Username: </label>
	    <input id="username" type="text" onblur="checkusername()" onkeyup="restrict('username')" maxlength="16">
	    <span id="unamestatus"></span>
    </div>
    <div>
    	<label>Email Address: </label>
    	<input id="email" type="text" onfocus="emptyElement('status')" onkeyup="restrict('email')" maxlength="88">
    </div>
    <div>
    	<label>Create Password: </label>
    	<input id="pass1" type="password" onfocus="emptyElement('status')" maxlength="16">
    </div>
    <div>
    	<label>Confirm Password: </label>
    	<input id="pass2" type="password" onfocus="emptyElement('status')" maxlength="16">
    </div>
    <div>
    	<label>Gender: </label>
    	<select id="gender" onfocus="emptyElement('status')">
      		<option value=""></option>
		    <option value="m">Male</option>
		    <option value="f">Female</option>
    	</select>
    </div>
    <div>
    	<label>Country: </label>
    	<select id="country" onfocus="emptyElement('status')">
      		<?php include_once("template_country_list.php"); ?>
    	</select>
    </div>
    <br/><hr/>
    <p id="status"></p>
    <br /><hr /><br />
    <div id="terms" style="display:none;">
	    <h3>S-Net Terms Of Use:</h3>
	    <p>1. Play nice here.</p>
	    <p>2. Take a bath before you visit.</p>
	    <p>3. Brush your teeth before bed.</p>
    </div>
    <br /><hr /><br />
    <div>
    	<button id="signupbtn" class="btn" style="float: right;" onclick="signup()">
    		Create Account
    	</button>
      	<a href="#" class="btn" style=" float: left;"onclick="return false" onmousedown="openTerms()">
        	View the Terms Of Use
      	</a>
    </div>
    </fieldset>
  </form>  
</div>
<?php include_once("template_pageBottom.php"); ?>
</body>
</html>