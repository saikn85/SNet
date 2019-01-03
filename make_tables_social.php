<?php
	
	include_once("php_includes/db_conx.php");

	$tbl_users = "CREATE TABLE IF NOT EXISTS users (
	              `id` INT(11) NOT NULL AUTO_INCREMENT,
				  `username` VARCHAR(16) NOT NULL UNIQUE,
				  `email` VARCHAR(255) NOT NULL UNIQUE,
				  `password` VARCHAR(255) NOT NULL,
				  `gender` ENUM('m','f') NOT NULL,
				  `country` VARCHAR(255) NULL,
				  `userlevel` ENUM('A','B','C','D') NOT NULL DEFAULT 'A',
				  `avatar` VARCHAR(255) NULL,
				  `ip` VARCHAR(255) NOT NULL,
				  `signup` DATETIME NOT NULL,
				  `lastlogin` DATETIME NOT NULL,
				  `notescheck` DATETIME NOT NULL,
				  `activated` ENUM('0','1') NOT NULL DEFAULT '1',
				  `seccode` INT(5) NOT NULL,
	               PRIMARY KEY (id)
	              )ENGINE=INNODB";
	
	$query = mysqli_query($db_conx, $tbl_users) or die(mysqli_error($db_conx));
	
	if ($query === TRUE) {
		
		echo "<h3>user table created OK :) </h3>"; 
	
	} else {
		
		echo "<h3>user table NOT created :( </h3>";
		 
	}
	
	////////////////////////////////////
	$tbl_useroptions = "CREATE TABLE IF NOT EXISTS useroptions ( 
	                `id` INT(11) NOT NULL,
	                `username` VARCHAR(16) NOT NULL UNIQUE,
					`background` VARCHAR(255) NOT NULL,
					`question` VARCHAR(255) NULL,
					`answer` VARCHAR(255) NULL,
					`name` varchar(255) NOT NULL,
  					`altemail` varchar(255) NOT NULL,
  					`phone` varchar(10) NOT NULL,
					`about` text  NOT NULL,
					`passoutyr` date NOT NULL,
					`updated` enum('0','1') NOT NULL DEFAULT '0',
	                 PRIMARY KEY (id) 
	                )ENGINE=INNODB"; 
	
	$query = mysqli_query($db_conx, $tbl_useroptions); 
	
	if ($query === TRUE) {
		
		echo "<h3>useroptions table created OK :) </h3>"; 
	
	} else {
		
		echo "<h3>useroptions table NOT created :( </h3>"; 
	
	}
	
	////////////////////////////////////
	$tbl_friends = "CREATE TABLE IF NOT EXISTS friends ( 
	                `id` INT(11) NOT NULL AUTO_INCREMENT,
	                `user1` VARCHAR(16) NOT NULL,
	                `user2` VARCHAR(16) NOT NULL,
	                `datemade` DATETIME NOT NULL,
	                `accepted` ENUM('0','1') NOT NULL DEFAULT '0',
	                 PRIMARY KEY (id)
	                )ENGINE=INNODB"; 
	
	$query = mysqli_query($db_conx, $tbl_friends); 
	
	if ($query === TRUE) {
		
		echo "<h3>friends table created OK :) </h3>"; 
	
	} else {
		
		echo "<h3>friends table NOT created :( </h3>"; 
	
	}
	
	////////////////////////////////////
	$tbl_blockedusers = "CREATE TABLE IF NOT EXISTS blockedusers ( 
	                `id` INT(11) NOT NULL AUTO_INCREMENT,
	                `blocker` VARCHAR(16) NOT NULL,
	                `blockee` VARCHAR(16) NOT NULL,
	                `blockdate` DATETIME NOT NULL,
	                `blocked` ENUM('0', '1') NOT NULL DEFAULT '1',
	                 PRIMARY KEY (id)
	                )ENGINE=INNODB"; 
	
	$query = mysqli_query($db_conx, $tbl_blockedusers); 
	
	if ($query === TRUE) {
		
		echo "<h3>blockedusers table created OK :) </h3>"; 
	
	} else {
	
		echo "<h3>blockedusers table NOT created :( </h3>"; 
	
	}
	
	////////////////////////////////////
	$tbl_status = "CREATE TABLE IF NOT EXISTS status ( 
	                `id` INT(11) NOT NULL AUTO_INCREMENT,
	                `osid` INT(11) NOT NULL,
	                `account_name` VARCHAR(16) NOT NULL,
	                `author` VARCHAR(16) NOT NULL,
	                `type` ENUM('a','b','c') NOT NULL,
	                `data` TEXT NOT NULL,
	                `postdate` DATETIME NOT NULL,
	                 PRIMARY KEY (id) 
	                )ENGINE=INNODB"; 
	
	$query = mysqli_query($db_conx, $tbl_status); 
	
	if ($query === TRUE) {
	
		echo "<h3>status table created OK :) </h3>"; 

	} else {
	
		echo "<h3>status table NOT created :( </h3>"; 
	
	}
	
	////////////////////////////////////
	$tbl_photos = "CREATE TABLE IF NOT EXISTS photos ( 
	                `id` INT(11) NOT NULL AUTO_INCREMENT,
	                `user` VARCHAR(16) NOT NULL,
	                `gallery` VARCHAR(16) NOT NULL,
					`filename` VARCHAR(255) NOT NULL,
	                `description` VARCHAR(255) NULL,
	                `uploaddate` DATETIME NOT NULL,
	                 PRIMARY KEY (id) 
	                )ENGINE=INNODB"; 
	
	$query = mysqli_query($db_conx, $tbl_photos); 
	
	if ($query === TRUE) {
		
		echo "<h3>photos table created OK :) </h3>"; 

	} else {
	
		echo "<h3>photos table NOT created :( </h3>"; 

	}
	
	////////////////////////////////////
	$tbl_notifications = "CREATE TABLE IF NOT EXISTS notifications ( 
	                `id` INT(11) NOT NULL AUTO_INCREMENT,
	                `username` VARCHAR(16) NOT NULL,
	                `initiator` VARCHAR(16) NOT NULL,
	                `app` VARCHAR(255) NOT NULL,
	                `note` VARCHAR(255) NOT NULL,
	                `did_read` ENUM('0','1') NOT NULL DEFAULT '0',
	                `date_time` DATETIME NOT NULL,
	                 PRIMARY KEY (id) 
	                )ENGINE=INNODB"; 
	
	$query = mysqli_query($db_conx, $tbl_notifications); 

	if ($query === TRUE) {
		
		echo "<h3>notifications table created OK :) </h3>";

	} else {
		
		echo "<h3>notifications table NOT created :( </h3>"; 

	}

	$groups = "CREATE TABLE IF NOT EXISTS groups(
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

	$gmembers = "CREATE TABLE IF NOT EXISTS gmembers(
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

	$grouppost = "CREATE TABLE IF NOT EXISTS grouppost(
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