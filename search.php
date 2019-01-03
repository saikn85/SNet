<?php
include_once("php_includes/check_login_status.php");
if(isset($_GET["u"])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
    header("location: index.php");
    exit();	
}
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
	input[type="search"]{
		width: 80%;
		margin: 10px; 
		padding: 10px;
	}
	label{
		font-weight: bold;
	}
</style>
<script type="text/javascript">
	function show(term){
		//var term = _("term").value;
		if(term == ""){
			_("term").innerHTML = "";
		}
		alert("Please Wait");
		var ajax = ajaxObj("POST", "php_parsers/fasearch.php");
        ajax.onreadystatechange = function() {
	       	if(ajaxReturn(ajax) == true) {
	           	if(ajax.responseText == ""){
					_("status").innerHTML = "No Records Found";
					//_("update").style.display = "block";
				} else {
					_("status").innerHTML = ajax.responseText;
				}	
	       	}
        }
		ajax.send("term="+term);
	}
</script>
</head>
<body>
<?php include_once("template_pageTop.php"); ?>
<div id="pageMiddle" style="height: 500px;">
	<form onsubmit="return false">
		<label>Alumni/Friend Search:</label>
		<input type="search" id="term" onkeyup="show(this.value)" placeholder="Enter Name or Username to Search" />
		<!--<button type="button" onclick="show();">search</button>-->
	</form>
	<div id="status"></div>
</div>
<?php include_once("template_pageBottom.php"); ?>
</body>
</html>