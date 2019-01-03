<?php

	include_once("php_includes/db_conx.php");
	
	$groups = "CREATE TABLE groups(
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(100) NOT NULL,
				`creation` DATETIME NOT NULL,
				`logo` VARCHAR(255) NOT NULL,
				`invrule` ENUM('0','1') NOT NULL,
				`creator` VARCHAR(16) NOT NULL,
				PRIMARY KEY(id)
				)ENGINE=INNODB";

	$query = mysqli_query($db_conx, $groups);

	if ($query === TRUE) {
		
		echo  "<h3>TABLE GROUPS CREATED!</h3>";

	}
	else{

		echo  "<h3>TABLE GROUPS CREATION FAILED!</h3>";

	}

	$gmembers = "CREATE TABLE gmembers(
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`gname` VARCHAR(100) NOT NULL,
				`mname` VARCHAR(16) NOT NULL,
				`approved` ENUM('0', '1') NOT NULL,
				`admin` ENUM('0','1') NOT NULL,
				PRIMARY KEY(id)
				)ENGINE=INNODB";

	$query = mysqli_query($db_conx, $gmembers);

	if ($query === TRUE) {
		
		echo  "<h3>TABLE GROUPS MEMEBERS CREATED!</h3>";

	}
	else{

		echo  "<h3>TABLE GROUPS MEMEBERS CREATION FAILED!</h3>";

	}

	$grouppost = "CREATE TABLE grouppost(
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`pid` VARCHAR(16) NOT NULL,
				`gname` VARCHAR(100) NOT NULL,
				`author` VARCHAR(16) NOT NULL,
				`type` ENUM('0','1') NOT NULL,
				`data` TEXT NOT NULL,
				`pdate` DATETIME NOT NULL,
				PRIMARY KEY(id)
				)ENGINE=INNODB";

	$query = mysqli_query($db_conx, $grouppost);

	if ($query === TRUE) {
		
		echo  "<h3>TABLE GROUPS MEMEBERS CREATED!</h3>";

	}
	else{

		echo  "<h3>TABLE GROUPS MEMEBERS CREATION FAILED!</h3>";
		echo mysqli_error($db_conx);

	}

	mysqli_close($db_conx);
?>