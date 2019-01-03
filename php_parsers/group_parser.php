<?php
session_start();
if(!isset($_SESSION["username"])){
    exit();
}
if(!isset($_SESSION['group'])){
    exit();
}
$uS = $_SESSION['username'];
$gS = $_SESSION['group'];
include_once("../php_includes/db_conx.php");
?><?php
// check group name
if(isset($_POST["gnamecheck"])){
	$gname = preg_replace('#[^a-z 0-9_]#i', '', $_POST['gnamecheck']);
	$sql = "SELECT id FROM groups WHERE name='$gname' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $gname_check = mysqli_num_rows($query);
    if (strlen($gname) < 3 || strlen($gname) > 50) {
	    echo '<strong style="color:#F00;">3 - 50 characters please</strong>';
	    exit();
    }
	if (is_numeric($gname[0])) {
	    echo '<strong style="color:#F00;">Group names must begin with a letter</strong>';
	    exit();
    }
    if ($gname_check < 1) {
	    echo '<strong style="color:#009900;">' . $gname . ' is OK</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">' . $gname . ' is taken</strong>';
	    exit();
    }
}
?><?php
// Create new group
if(isset($_POST["action"]) && $_POST['action'] == "new_group"){
	
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$name = preg_replace('#[^a-z 0-9]#i', '', $_POST['name']);
	$name = str_replace(" ","_",$name);
    $inv = preg_replace('#[^0-9.]#', '', $_POST['inv']);
	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
	if ($inv == "1"){$inv = "0";}
	if ($inv == "2"){$inv = "1";}
	$sql = "SELECT id FROM groups WHERE name='$name' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$n_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if($name == "" || $inv == ""){
		echo "The form submission is missing values.";
        exit();
	} else if ($n_check > 0){ 
        echo "The group name you entered is alreay taken";
        exit();
	} else if (strlen($name) < 3 || strlen($name) > 50) {
        echo "Group name must be between 3 and 50 characters";
        exit(); 
    } else if (is_numeric($name[0])) {
        echo 'Group name cannot begin with a number';
        exit();
    } else {
		// END FORM DATA ERROR HANDLING
	    // Begin Insertion of data into the database		
		// Add group to database
		$sql = "INSERT INTO groups (name, creation, logo, invrule, creator)       
		        VALUES('$name',now(),'gLogo.jpg','$inv','$uS')";
		$query = mysqli_query($db_conx, $sql); 
		// Add to group member to database
		$sql = "INSERT INTO gmembers (gname, mname, approved, admin)       
		        VALUES('$name','$uS','1','1')";
		$query = mysqli_query($db_conx, $sql);
		if (!file_exists("../groups")) {
			mkdir("../groups", 0755);
		}
		// Create directory(folder) to hold each user's files(pics, MP3s, etc.)
		if (!file_exists("../groups/$name")) {
			mkdir("../groups/$name", 0755);
		}
		$gLogo = '../images/gLogo.jpg'; 
		$gLogo2 = "../groups/$name/gLogo.jpg"; 
		if (!copy($gLogo, $gLogo2)) {
			echo "failed to create logo.";
		}
		echo "group_created|$name";
		exit();
	}
	exit();
}
?><?php
// Join Group Request
if(isset($_POST["action"]) && $_POST['action'] == "join_group"){
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$name = $uS;
	$group = $gS;
	// Empty check
	if($name == "" || $group == ""){
        exit();
	}
	// Make sure that group exists
	$query = mysqli_query($db_conx, "SELECT id, invrule FROM groups WHERE name='$group' LIMIT 1");
	$numrows = mysqli_num_rows($query);
	if($numrows < 1){
    	exit();
	} else {
		// Get data about group
		while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
			$rule = $row["invrule"];
		}
	}
	// Add request to database
	$sql = "INSERT INTO gmembers (gname, mname, approved)       
		        VALUES('$group','$name','$rule')";
	$query = mysqli_query($db_conx, $sql);
	
	if ($rule == 0){
		echo "pending_approval";
		exit();	
	} else {
		echo "refresh_now";
		exit();		
	}
}
?><?php
// Approve member
if(isset($_POST["action"]) && $_POST['action'] == "approve_member"){
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$g = $gS;
	$u = preg_replace('#[^a-z 0-9]#i', '', $_POST['u']);

	// Empty check
	if($g == "" || $u == ""){
        exit();
	}
	
	// Make sure request exists
	$query = mysqli_query($db_conx, "SELECT id FROM gmembers WHERE gname='$g' AND mname='$u' AND approved='0' LIMIT 1");
	$numrows = mysqli_num_rows($query);
	if($numrows < 1){
    	exit();
	}

	// Add request to database
	$sql = "UPDATE gmembers SET approved='1' WHERE mname='$u' AND gname='$g' LIMIT 1";;
	$query = mysqli_query($db_conx, $sql);
	echo "member_approved";
	exit;
}
?><?php
// Decline member
if(isset($_POST["action"]) && $_POST['action'] == "decline_member"){
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$g = $gS;
	$u = preg_replace('#[^a-z 0-9]#i', '', $_POST['u']);

	// Empty check
	if($g == "" || $u == ""){
        exit();
	}
	
	// Make sure request exists
	$query = mysqli_query($db_conx, "SELECT id FROM gmembers WHERE gname='$g' AND mname='$u' AND approved='0' LIMIT 1");
	$numrows = mysqli_num_rows($query);
	if($numrows < 1){
    	exit();
	}

	// Remove from database
		$sql = "DELETE FROM gmembers WHERE mname='$u' AND gname='$g' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		echo "member_declined";
		exit();
}
?><?php
// Quit Group
if(isset($_POST["action"]) && $_POST['action'] == "quit_group"){

	// Empty check
	if($gS == "" || $uS == ""){
        exit();
	}
	
	// Make sure already member
	$query = mysqli_query($db_conx, "SELECT id FROM gmembers WHERE gname='$gS' AND mname='$uS' AND approved='1' LIMIT 1");
	$numrows = mysqli_num_rows($query);
	if($numrows < 1){
    	exit();
	}

	// Remove from database
	$sql = "DELETE FROM gmembers WHERE mname='$uS' AND gname='$gS' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	// maybe redirect them to another page if you want
	echo "was_removed";
	exit();		
}
?><?php
// Add admin
if(isset($_POST["action"]) && $_POST['action'] == "add_admin"){
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$n = preg_replace('#[^a-z 0-9]#i', '', $_POST['n']);

	// Empty check
	if($gS == "" || $uS == "" || $n == ""){
        exit();
	}
	
	// Make sure already member
	$query = mysqli_query($db_conx, "SELECT id FROM gmembers WHERE gname='$gS' AND mname='$n' AND approved='1' LIMIT 1");
	$numrows = mysqli_num_rows($query);
	if($numrows < 1){
    	exit();
	}
	
	// Verify your admin status
	$query = mysqli_query($db_conx, "SELECT id FROM gmembers WHERE gname='$gS' AND mname='$uS' AND admin='1' LIMIT 1");
	$numrows = mysqli_num_rows($query);
	if($numrows < 1){
    	exit();
	}

	// Set as admin
	$sql = "UPDATE gmembers SET admin='1' WHERE gname='$gS' AND mname='$n' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	echo "admin_added";
	exit();
}
?><?php
//Change logo
if (isset($_FILES["avatar"]["name"]) && $_FILES["avatar"]["tmp_name"] != ""){
	$fileName = $_FILES["avatar"]["name"];
    $fileTmpLoc = $_FILES["avatar"]["tmp_name"];
	$fileType = $_FILES["avatar"]["type"];
	$fileSize = $_FILES["avatar"]["size"];
	$fileErrorMsg = $_FILES["avatar"]["error"];
	$kaboom = explode(".", $fileName);
	$fileExt = end($kaboom);
	list($width, $height) = getimagesize($fileTmpLoc);
	if($width < 10 || $height < 10){
		header("location: ../message.php?msg=ERROR: That image has no dimensions");
        exit();	
	}
	$db_file_name = rand(100000000000,999999999999).".".$fileExt;
	if($fileSize > 1048576) {
		header("location: ../message.php?msg=ERROR: Your image file was larger than 1mb");
		exit();	
	} else if (!preg_match("/.(gif|jpg|png)$/i", $fileName) ) {
		header("location: ../message.php?msg=ERROR: Your image file was not jpg, gif or png type");
		exit();
	} else if ($fileErrorMsg == 1) {
		header("location: ../message.php?msg=ERROR: An unknown error occurred");
		exit();
	}
	//
	$sql = "SELECT logo FROM groups WHERE name='$gS' AND creator='$uS' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	$avatar = $row[0];
	if($avatar != ""){
		//
		$picurl = "../groups/$gS/$avatar"; 
	    if (file_exists($picurl)) { unlink($picurl); }
	}
	//
	$moveResult = move_uploaded_file($fileTmpLoc, "../groups/$gS/$db_file_name");
	if ($moveResult != true) {
		header("location: ../message.php?msg=ERROR: File upload failed");
		exit();
	}
	include_once("../php_includes/image_resize.php");
	//
	$target_file = "../groups/$gS/$db_file_name";
	//
	$resized_file = "../groups/$gS/$db_file_name";
	$wmax = 200;
	$hmax = 300;
	img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
	//
	$sql = "UPDATE groups SET logo='$db_file_name' WHERE name='$gS' AND creator='$uS'LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	mysqli_close($db_conx);
	//
	header("location: ../group.php?g=$gS");
	exit();
}
?><?php
// Add new post
if (isset($_POST['action']) && $_POST['action'] == "new_post"){
	// Make sure post data is not empty
	if(strlen($_POST['data']) < 1){
	    exit();
	}

	// Clean all of the $_POST vars that will interact with the database
	$data = htmlentities($_POST['data']);
	$data = mysqli_real_escape_string($db_conx, $data);

	// Insert the status post into the database now
	$sql = "INSERT INTO grouppost(pid, gname, author, type, data, pdate) 
			VALUES('0','$gS','$uS','0','$data',now())";
	$query = mysqli_query($db_conx, $sql);
	$id = mysqli_insert_id($db_conx);

	mysqli_close($db_conx);
	echo "post_ok|$id";
	exit();
}
?><?php
// Reply to post
if (isset($_POST['action']) && $_POST['action'] == "post_reply"){
	// Make sure post data is not empty
	$sid = preg_replace('#[^0-9]#i', '', $_POST['sid']);
	if(strlen($_POST['data']) < 1){
	    exit();
	}

	// Clean all of the $_POST vars that will interact with the database
	$data = htmlentities($_POST['data']);
	$data = mysqli_real_escape_string($db_conx, $data);
	
	// Empty check
	if($sid == ""){
        exit();
	}

	// Insert the status post into the database now
	$sql = "INSERT INTO grouppost(pid, gname, author, type, data, pdate) 
			VALUES('$sid','$gS','$uS','1','$data',now())";
	$query = mysqli_query($db_conx, $sql);
	//$id = mysqli_insert_id($db_conx);

	mysqli_close($db_conx);
	echo "reply_ok|$sid";
	exit();
}
?>