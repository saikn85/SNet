<?php
include_once("../php_includes/check_login_status.php");
if($user_ok != true || $log_username == "") {
	exit();
}
?><?php
if (isset($_POST['type']) && isset($_POST['user'])){
	$user = preg_replace('#[^a-z0-9]#i', '', $_POST['user']);
	include_once("../php_includes/db_conx.php");
	$n = mysqli_real_escape_string($db_conx, $_POST['n']);
	$alt = mysqli_real_escape_string($db_conx, $_POST['alt']);
	$phone = mysqli_real_escape_string($db_conx, $_POST['ph']);
	$passoutyear = mysqli_real_escape_string($db_conx, $_POST['pout']);
	$about = mysqli_real_escape_string($db_conx, $_POST['ab']);
	if($_POST['type'] == "update"){
		$sql = "UPDATE useroptions SET name='$n', altemail='$alt', phone='$phone', about='$about',
				passoutyr='$passoutyear', updated='1' where username='$user'";

		$result = mysqli_query($db_conx, $sql);

		echo mysqli_error($db_conx);

		if($result){

			echo "updated";

		}else{
			echo "";
		}
	}
	else if($_POST['type'] == "changepass"){

		$p1 = mysqli_real_escape_string($db_conx, $_POST['p1']);

		$p2 = md5(mysqli_real_escape_string($db_conx, $_POST['p2']));

		$p3 = md5(mysqli_real_escape_string($db_conx, $_POST['p3']));

		$sql = "SELECT password FROM users WHERE username='$user'";

		$row = mysqli_fetch_row(mysqli_query($db_conx, $sql));

		if(md5($p1) == $row[0] && $p2 == $p3){

			$sql = "UPDATE users SET password='$p2' WHERE username='$user' LIMIT 1";

			$result = mysqli_query($db_conx, $sql);

			if($result){
				
				echo "password successfully updated";

			}
			else{
				
				echo "";

			}

		}

		else{
			echo "password Don't match";
		}
		echo mysqli_error($db_conx);
	}

	mysqli_close($db_conx);
}
?>