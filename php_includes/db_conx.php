<?php
$db_conx = mysqli_connect("localhost", "root", "madmax", "snet", "3306", null);
// Evaluate the connection
if (mysqli_connect_errno()) {
    echo mysqli_connect_error();
    exit();
} else {
	//echo "Successful database connection, happy coding!!!";
}
?>