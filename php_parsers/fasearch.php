<?php
include_once("../php_includes/check_login_status.php");
if($user_ok != true || $log_username == "") {
	exit();
}

if(isset($_POST['term'])){
		
		include_once("../php_includes/db_conx.php");

		$term = mysqli_real_escape_String($db_conx, $_POST['term']);

		$sql = "SELECT * FROM useroptions WHERE `name` LIKE '%$term%' OR `username` LIKE '%$term%'";

		$result = mysqli_query($db_conx, $sql);

		$profilelink = "";

		if($result){

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

					$profilelink .= '<a href="user.php?u='.$row["username"].'" title="'.$row['name'].'">'.$row["username"].'</a>&nbsp; | &nbsp;';
			
			}

			echo $profilelink;

		}else{

			echo "";

		}

		echo mysqli_error($db_conx);

	}

	mysqli_close($db_conx);
?>