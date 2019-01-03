<?php
include_once("php_includes/check_login_status.php");
if(!isset($_SESSION["username"])){
	echo "You must be logged in to view this. Press back button.";
    exit();
}
// Initialize any variables that the page might echo
$g = "";
$gName = "";
$gCreation = "";
$gLogo = "";
$invRule = "";
$privRule = "";
$creator = "";
$gMembers = "";
$moderators = array();
$approved = array();
$pending = array();
$all = array();
$joinBtn = "";
$addMembers = "";
$addAdmin = "";
$profile_pic_btn = "";
$avatar_form = "";
$mainPosts = "";
// Make sure the _GET username is set, and sanitize it
if(isset($_GET["g"])){
	$g = preg_replace('#[^a-z0-9_]#i', '', $_GET['g']);
} else {
    header("location: index.php");
    exit();
}
// Select the group from the groups table
$query = mysqli_query($db_conx, "SELECT * FROM groups WHERE name='$g' LIMIT 1");
// Make sure that group exists and get group data
$numrows = mysqli_num_rows($query);
if($numrows < 1){
	echo "That group does not exist, press back";
    exit();	
} else {
// Get data about group
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$gName = $row["name"];
		$gCreation = $row["creation"];
		$gLogo = $row["logo"];
		$invRule = $row["invrule"];
		$creator = $row["creator"];
	}
}
$profile_pic = '<img src="groups/'.$g.'/'.$gLogo.'" alt="'.$g.'">';
// Set session for group
$_SESSION['group'] = $gName;
// Get Member data
$sql = "SELECT g.mname, g.approved, g.admin, u.avatar
		FROM gmembers AS g
		LEFT JOIN users AS u ON u.username = g.mname
		WHERE g.gname = '$g'" ;
$query = mysqli_query($db_conx, $sql);
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	$mName = $row['mname'];
	$app = $row['approved'];
	$admin = $row['admin'];
	$avatar = $row['avatar'];
		
	// Set user image
	if($avatar == ""){
		$member_pic = 'images/avatardefault.jpg';
	}
	else{
		$member_pic = 'user/'.$mName.'/'.$avatar;
	}
	// Determine if approved
	switch ($app){
		case 0:
		array_push($pending, $mName);
		array_push($all, $mName);
        break;
    	case 1:
		array_push($approved, $mName);
		array_push($all, $mName);
        break;
	}
		
	// Determine if admin
	if ($admin == 1){
		array_push($moderators, $mName);
	}
	// Get all counts
	$mod_count = count($moderators);
	$app_count = count($approved);
	$pend_count = count($pending);
	
	// Output
	if ($app == 1){
		$gMembers .= '<a href="user.php?u='.$mName.'"><img src="'.$member_pic.'" alt="'.$mName.'" title="'.$mName.'" width="70" height="70" ></a>';
	}
}
// Join group button
if ((isset($_SESSION['username'])) && (!in_array($_SESSION['username'],$all))){
	$joinBtn = '<button id="joinBtn" onClick="joinGroup()">Join Group</button>';
}
// Pending members section for admin
if (in_array($_SESSION['username'],$moderators)){
	$addMembers = "<h3>Peniding members</h3>";
	for($x=0;$x<$pend_count;$x++){		
		$addMembers .= '<a href="user.php?u='.$pending[$x].'">'.$pending[$x].'</a>';
		$addMembers .= '<button id="appBtn" onClick="approveMember(\''.$pending[$x].'\')">Approve</button>';
		$addMembers .= '<button id="appBtn" onClick="declineMember(\''.$pending[$x].'\')">Decline</button><br />';
	}		
}
// Add admin
if (in_array($_SESSION['username'],$moderators)){
	$addAdmin = '<h3>Add admin</h3>';
	$addAdmin .= '<input type="text" name="new_admin" id="new_admin" />';
	$addAdmin .= '<button id="addAdm" onClick="addAdmin()">Add</button>';	
}
// Change logo for group creator only
if($_SESSION['username'] == $creator){
	$profile_pic_btn = '<a href="#" onclick="return false;" onmousedown="toggleElement(\'avatar_form\')">Toggle Avatar Form</a>';
	$avatar_form  = '<form id="avatar_form" enctype="multipart/form-data" method="post" action="php_parsers/group_parser.php">';
	$avatar_form .=   '<h4>Change logo</h4>';
	$avatar_form .=   '<input type="file" name="avatar" required>';
	$avatar_form .=   '<p><input type="submit" value="Upload"></p>';
	$avatar_form .= '</form>';
}
// Build posting mechanism
// Get all thread starting posts
$sql = "SELECT g.*, u.avatar
		FROM grouppost AS g
		LEFT JOIN users AS u ON u.username = g.author
		WHERE g.gname = '$g' AND type='0' ORDER BY pdate DESC";
$query = mysqli_query($db_conx, $sql) or die(mysqli_error($db_conx));
$numrows = mysqli_num_rows($query);
if($numrows > 0){
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$post_id = $row["id"];
		$post_auth = $row["author"];
		$post_type = $row["type"];
		$post_data = $row["data"];
		$post_date = $row["pdate"];
		$post_avatar = $row["avatar"];
		$avatar_pic = 'user/'.$post_auth.'/'.$post_avatar;
		$user_image = '<img src="'.$avatar_pic.'" alt="'.$post_auth.'" title="'.$post_auth.'" width="50" height="50" >';
		// Build threads	  
		$mainPosts .= '<div id="pB_'.$post_id.'" class="postsWrapper">';
		$mainPosts .= '<div class="postsHead">';
		$mainPosts .= 'Posted by: '.$post_auth.' ---- '.date('F d, Y - g:ia', strtotime($post_date));
		$mainPosts .= '</div>';
		$mainPosts .= '<div class="postsBody">';
		$mainPosts .= '<div class="postsPic">';
		$mainPosts .= $user_image;
		$mainPosts .= '</div>';
		$mainPosts .= '<div class="postsWords">';
		$mainPosts .= $post_data;
		$mainPosts .= '</div>';		
		$mainPosts .= '<div class="clear"></div>';
		$mainPosts .= '</div>';
		
		
		// Get replies and user images using inner loop
		$sql2 = "SELECT g.author, g.data, g.pdate, u.avatar
				 FROM grouppost AS g
				 LEFT JOIN users AS u ON u.username = g.author
		         WHERE pid='$post_id'";
		$query2 = mysqli_query($db_conx, $sql2);
	    $numrows2 = mysqli_num_rows($query2);
	 	if($numrows2 > 0){
			while ($row2 = mysqli_fetch_array($query2, MYSQLI_ASSOC)) {
				$reply_auth = $row2["author"];
				$reply_data = $row2["data"];
				$reply_date = $row2["pdate"];
				$reply_avatar = $row2["avatar"];
				$re_avatar_pic = 'user/'.$reply_auth.'/'.$reply_avatar;
			  	$reply_image = '<img src="'.$re_avatar_pic.'" alt="'.$reply_auth.'" title="'.$reply_auth.'" width="50" height="50" >';
				//divider
				$mainPosts .= '<hr/>';
				// Build replies
			 	$mainPosts .= '<div class="postsBody">';
			    $mainPosts .= '<div class="postsPic">';
			  	$mainPosts .= $reply_image;
			  	$mainPosts .= '</div>';
			  	$mainPosts .= '<div class="postsWords">';
				$mainPosts .= $reply_auth.' replied on '.date('F d, Y - g:ia', strtotime($reply_date)).'<br /><br />';
			  	$mainPosts .= $reply_data;
			  	$mainPosts .= '</div>';		
			  	$mainPosts .= '<div class="clear"></div>';
			  	$mainPosts .= '</div>';
			}
		}
		$mainPosts .= '</div>';
		// Time to build the Reply To section
		$mainPosts .= '<textarea id="reply_post_'.$post_id.'" class="repost" placeholder="Reply to this..."></textarea>';
    	$mainPosts .= '<button id="reBtn" onClick="replyPost(\''.$post_id.'\')">Post</button>';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $g; ?></title>
<link rel="stylesheet" href="style/style.css">
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script>
function joinGroup(){
	var ajax = ajaxObj("POST", "php_parsers/group_parser.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText;
			if(datArray == "pending_approval"){
				alert ("Your request is awaiting approval");
			}
				
			if(datArray == "refresh_now"){
				alert ("Your are now a member, refresh your browser to join in");
			}
		}
	}
	ajax.send("action=join_group");
}
function approveMember(u){
	var ajax = ajaxObj("POST", "php_parsers/group_parser.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText;
			if(datArray == "member_approved"){
				alert ("Member Approved");
			}
		}
	}
	ajax.send("action=approve_member&u="+u);
}
function declineMember(u){
	var ajax = ajaxObj("POST", "php_parsers/group_parser.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText;
			if(datArray == "member_declined"){
				alert ("Member Declined");
			}
		}
	}
	ajax.send("action=decline_member&u="+u);
}
function quitGroup(){
	var conf = confirm("Press OK to confirm that you want to quit group");
	if(conf != true){
		return false;
	}
	var ajax = ajaxObj("POST", "php_parsers/group_parser.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "was_removed"){
				alert("you have been removed");
			}
		}
	}
	ajax.send("action=quit_group");
}
function addAdmin(){
	var n = _("new_admin").value;
	var ajax = ajaxObj("POST", "php_parsers/group_parser.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText;
			if(datArray == "admin_added"){				
				alert ("Admin Created");
			}
		}
	}
	ajax.send("action=add_admin&n="+n);
}
function newPost(){
	var data = _('new_post').value;
	if(data == ""){
		alert("Type something first weenis");
		return false;
	}
	_("postBtn").disabled = true;
	var ajax = ajaxObj("POST", "php_parsers/group_parser.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText.split("|");
			if(datArray[0] == "post_ok"){
				var sid = datArray[1];
				data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/n/g,"<br />").replace(/r/g,"<br />");
				var currentHTML = _("listBlabs").innerHTML;				
				_("listBlabs").innerHTML = '<div id="pB_'+sid+'" class="postsWrapper"><div class="postsHead"><b>Posted by you just now</b></div><div class="postsBody"><div class="postsWords">'+data+'</div><div class="clear"></div></div></div>'+currentHTML;		
				_("postBtn").disabled = false;
				
				_('new_post').value = "";
			} else {
				alert(ajax.responseText);
			}
		}
	}
	ajax.send("action=new_post&data="+data);
}
function replyPost(sid){
	var ta = "reply_post_"+sid;
	var data = _(ta).value;
	if(data == ""){
		alert("Type something first weenis");
		return false;
	}
	var ajax = ajaxObj("POST", "php_parsers/group_parser.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var datArray = ajax.responseText.split("|");
			if(datArray[0] == "reply_ok"){
				var rid = datArray[1];
				data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/n/g,"<br />").replace(/r/g,"<br />");
				_("pB_"+rid).innerHTML += '<div class="postsBody"><hr><b>Reply by you just now:</b><br />'+data+'</div>';
				_(ta).value = "";
			} else {
				alert(ajax.responseText);
			}
		}
	}
	ajax.send("action=post_reply&sid="+sid+"&data="+data);
}
</script>
</head>
<body>
<?php include_once("template_pageTop.php"); ?>
<div id="pageMiddle">
  <div id="profile_pic_box" ><?php echo $profile_pic_btn; ?><?php echo $avatar_form; ?><?php echo $profile_pic; ?></div>
  <h2><?php echo $g; ?></h2>
  <p>Created by: <?php echo $creator; ?></p>
  <p>Established: <?php echo date('F d, Y', strtotime($gCreation)); ?></p>
  <p>This group has <?php echo $mod_count; ?> moderators, <?php echo $app_count; ?> members, and <?php echo $pend_count; ?> members pending approval</p>
  <?php if (in_array($_SESSION['username'],$approved)){ ?>
  <p><button id="quitBtn" onClick="quitGroup()">Quit Group</button></p>
  <?php } ?>
  <?php echo $joinBtn; ?> <?php echo $addMembers; ?><br /><?php echo $addAdmin; ?>
  <hr />
  <div id="groupWrapper">
  	<div id="groupPosts">
    	<div id="mainTa">
        <?php if (in_array($_SESSION['username'],$approved)){ ?>
  	  	<textarea id="new_post" class="newpost" placeholder="Add New Post"></textarea><br />
      	<button id="postBtn" onClick="newPost()">Post</button>
        <?php } ?>
        </div>
        <div id="listBlabs">
		<?php
        if (in_array($_SESSION['username'],$approved)){
			echo $mainPosts;
		} ?>
        </div>
    </div>
    <div id="groupmembers"><?php echo $gMembers; ?></div>
    <div class="clear"></div>  
  </div>
</div>
<?php include_once("template_pageBottom.php"); ?>
</body>
</html>