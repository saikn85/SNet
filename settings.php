<?php
include_once("php_includes/check_login_status.php");
// Initialize any variables that the page might echo
$u = "";
if(isset($_GET["u"])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
    header("location: index.php");
    exit();	
}?>
<?php
include_once("php_includes/db_conx.php");
$sql = "SELECT * FROM useroptions WHERE username='$u' LIMIT 1";
$row = mysqli_fetch_array(mysqli_query($db_conx, $sql), MYSQLI_ASSOC);
echo mysqli_error($db_conx);
$updated = $row['updated'];
if($updated == 0){
	$n = $alt = $ph = $ab = $passout = null;
}else{
	$n = $row['name'];
	$alt = $row['altemail'];
	$ph = $row['phone'];
	$ab = $row['about'];
	$passout = $row['passoutyr'];
}
//mysqli_close($db_conx);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<title> <?php  echo ucwords($u); ?>'s Settings</title>
<link rel="icon" href="favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="style/style.css" />
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<style type="text/css">
	input[type="text"], input[type="password"], input[type="email"], textarea, 
	select, input[type="tel"], input[type="month"]{
    	width: 90%; color: #3300cc; padding: 5px;
    	margin: 5px;
	}
	label, legend{
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
</style>
<script type="text/javascript">
	function updateProfile(type, user){
		var n = _("name").value;
		var alt = _("altemail").value;
		var p = _("phone").value;
		console.log(p);
		var psout = _("passout").value;
		var ab = _("about").value;
		if(n == "" || alt == "" || p == "" || psout == "" || ab == ""){
			alert("Fill Out Form Details");
		}
		else{
			_("update").style.display = "none";
			alert("Please Wait");
			var ajax = ajaxObj("POST", "php_parsers/update.php");
        	ajax.onreadystatechange = function() {
	        	if(ajaxReturn(ajax) == true) {
	            	if(ajax.responseText == ""){
						alert("Something Went Wrong");
						_("update").style.display = "block";
					} else {
						alert("Updated Successfully");
					}	
	        	}
        	}
			ajax.send("type="+type+"&user="+user+
					"&n="+n+"&alt="+alt+"&ph="+p+"&pout="+psout+"&ab="+ab);
		}
	}
	window.onload = function(){
		var updated = 0;
		var phpup = <?php echo $updated; ?>;
		//console.log(phpup);
		if(updated !== phpup){
		   _("name").readOnly = true; 
		   _("altemail").readOnly = true;
		   _("phone").readOnly = true; 
		   _("passout").style.display = "none";
		   _("about").readOnly = true;
		   _("update").style.visibility = "hidden";
		   _("reset").style.visibility = "hidden";
	   	   _("edit").style.visibility = "visible";
			
		}
	};
	function edit(){
		   _("name").readOnly = false; 
		   _("altemail").readOnly = false;
		   _("phone").readOnly = false;
		   _("passout").style.display = "inline-block";
		   _("about").readOnly = false;
		   _("update").style.visibility = "visible";
		   _("reset").style.visibility = "visible";
	       _("yr").style.display = "none";
	       _("edit").style.visibility = "hidden";
	}
	function changePass(type, user){
		var p1 = _("curpass").value;
		var p2 = _("newpass").value;
		var p3 = _("confpass").value;
		if( p1 == "" || p2 == "" || p3 == ""){
			alert("Fill Out Form Data");
		}
		else if(p2 != p3){
			alert("Passwords Don't Match");
		}
		else{

			_("update").style.display = "none";
			alert("Please Wait");
			var ajax = ajaxObj("POST", "php_parsers/update.php");
        	ajax.onreadystatechange = function() {
	        	if(ajaxReturn(ajax) == true) {
	            	if(ajax.responseText == ""){
						alert("Something Went Wrong or Your Current Password Does Not Match");
						_("update").style.display = "block";
					} else {
						alert("Password Successfully");
					}	
	        	}
        	}
			ajax.send("type="+type+"&user="+user+"&p1="+p1+"&p2="+p2+"&p3="+p3);

		}
	}
</script>
</head>
<body>
<?php include_once("template_pageTop.php"); ?>
<div id="pageMiddle" style="height: 500px;">
<div style="width:450px; border: 2px #CCC solid; float: left; margin: 10px; padding: 10px; display:inline-block;">
	<form onSubmit="return false" id="updateform">
		<fieldset>
			<legend>Update Profile</legend>
			<label>Name:</label>
			<input type="text"  placeholder="Enter Your Name" id="name"
			value="<?php echo $n;?>"/>
			<label>Alt-E-Mail:</label>
			<input id="altemail" type="text" value="<?php echo $alt;?>" placeholder="Enter Alternate E-Mail Address"/>
			<label>Contact Number:</label>
			<input id="phone" type="tel" value="<?php echo $ph;?>"
			maxlength="10"  placeholder="Enter Contact Number"/>
			<label>Pass Out Year:&nbsp;</label>&nbsp;<span id="yr"><?php echo $passout;?></span>
			<input type="date" id="passout" /><br/>
			<label>About:</label>
			<textarea id="about" maxlength="450" placeholder="Write About Yourself" rows="8"><?php echo $ab;?></textarea>
			<button id="update" type="button" class="btn" onClick="updateProfile('update','<?php echo $u;?>');">Update</button>
			<a href="#" id="edit" class="btn" onClick="edit()" style="text-decoration: none; text-align: center;" >Edit Profile</a>
			<button id="reset" type="reset" class="btn" style="float: right">Reset</button>
		</fieldset>
	</form>
</div>
<div style="width:450px; border: 2px #CCC solid; float: left; margin: 10px; padding: 10px; display:inline-block;">
	<form onSubmit="return false" id="changepassform">
		<fieldset>
			<legend>Update Profile</legend>
			<label>Current Password:</label>
			<input type="password"  placeholder="Enter Your Current Password" id="curpass"/>
			<label>New Password:</label>
			<input id="newpass" type="password" placeholder="Enter New Password"/>
			<label>Confirm Password:</label>
			<input id="confpass" type="password" placeholder="Confirm New Password"/>
			<button id="update" type="button" class="btn" style="width: 100%;" onClick="changePass('changepass','<?php echo $u;?>');">Change Password</button>
			<p style="color: red;" align="center">Login Once Again After the Password Is Changed</p>
		</fieldset>
	</form>
</div>
</div>
<?php include_once("template_pageBottom.php"); ?>
</body>
</html>