<?php
include_once("php_includes/check_login_status.php");
// If user is already logged in, header that weenis away
if($user_ok == true){
	header("location: user.php?u=".$_SESSION["username"]);
    exit();
}
?><?php
// AJAX CALLS THIS LOGIN CODE TO EXECUTE
if(isset($_POST["u"]) && isset($_POST["s"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES AND SANITIZE
	$u = mysqli_real_escape_string($db_conx, $_POST['u']);
	$s = mysqli_real_escape_string($db_conx, $_POST['s']);;
	$p = md5($_POST['p']);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// FORM DATA ERROR HANDLING
	if($u == "" || $p == "" || $s == ""){
		echo "login_failed";
        exit();
	} else {
	// END FORM DATA ERROR HANDLING
		$sql = "SELECT id, username, password FROM users WHERE username='$u' AND seccode='$s' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
		$db_id = $row[0];
		$db_username = $row[1];
        $db_pass_str = $row[2];
		if($p != $db_pass_str){
			echo "login_failed";
            exit();
		} else {
			// CREATE THEIR SESSIONS AND COOKIES
			$_SESSION['userid'] = $db_id;
			$_SESSION['username'] = $db_username;
			$_SESSION['password'] = $db_pass_str;
			setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
			setcookie("user", $db_username, strtotime( '+30 days' ), "/", "", "", TRUE);
    		setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE); 
			// UPDATE THEIR "IP" AND "LASTLOGIN" FIELDS
			$sql = "UPDATE users SET ip='$ip', lastlogin=now() WHERE username='$db_username' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
			echo $db_username;
		    exit();
		}
	}
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Log In</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="style/style.css">
<style type="text/css">
#loginform{
	margin:24px;	
}
#loginform > fieldset {
	margin-top: 12px;	
}
/*#loginform > input {
	width: 200px;
	padding: 3px;
	background: #F3F9DD;
}
#loginbtn {
	font-size:15px;
	padding: 10px;
}*/
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
#sifnupform > fieldset{
	width: 35%;
}
label{
	font-weight: bold;
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
    width: 25%; color: #3300cc; padding: 5px;
    margin: 5px;
}
</style>
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script>
function emptyElement(x){
	_(x).innerHTML = "";
}
function login(){
	var u = _("username").value;
	var p = _("password").value;
	var s = _("seccode").value;
	if(u == "" || p == "" || s == ""){
		_("status").innerHTML = "Fill out all of the form data";
	} else {
		_("loginbtn").style.display = "none";
		_("status").innerHTML = 'please wait ...';
		var ajax = ajaxObj("POST", "login.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            if(ajax.responseText == "login_failed"){
					_("status").innerHTML = "Login unsuccessful, please try again.";
					_("loginbtn").style.display = "block";
				} else {
					window.location = "user.php?u="+ajax.responseText;
				}
	        }
        }
        ajax.send("u="+u+"&p="+p+"&s="+s);
	}
}
</script>
</head>
<body>
<?php include_once("template_pageTop.php"); ?>
<div id="pageMiddle" style="height: 476px;">
  <!-- LOGIN FORM -->
  <form id="loginform" onsubmit="return false;">
  <fieldset>
  	<legend><h3>Login Here</h3></legend>
  	<div>
  		<label>Username:</label>
    	<input type="text" id="username" onfocus="emptyElement('status')" maxlength="88">
  	</div>
    <div>
    	<label>Password:</label>
    	<input type="password" id="password" onfocus="emptyElement('status')" maxlength="100">	
    </div>
    <div>
    	<label>Security Code:</label>
    	<input type="password" id="seccode" onfocus="emptyElement('status')" maxlength="5">	
    </div>
    <br /><hr /><br/>
    <div>
    	<button id="loginbtn" class="btn" 
    		style="float: left;"onclick="login()">Log In</button>
    	<a href="chgpass.php" style="float: right;" class="btn">Forgot Your Password?</a>	
    </div>
    <br /><br /><br /><hr/>
    <p id="status"></p>
    </fieldset>
  </form>
  <!-- LOGIN FORM -->
</div>
<?php include_once("template_pageBottom.php"); ?>
</body>
</html>