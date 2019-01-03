<?php

$thisPage = basename($_SERVER['PHP_SELF']);
$thisGroup = "";
$agList = "";
$mgList = "";
$_SESSION['group'] = "notSet";
if ($thisPage == "group.php"){
  if(isset($_GET["g"])){
    $thisGroup = preg_replace('#[^a-z0-9_]#i', '', $_GET['g']);
    $_SESSION['group'] = $thisGroup;
  }
}
if (isset($_SESSION['username'])) {
// All groups list  
  $query = mysqli_query($db_conx, "SELECT name,logo FROM groups") or die (mysqli_error($db_conx));
  $g_check = mysqli_num_rows($query) ;
  if ($g_check > 0){
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
      $agList .= '<a href="group.php?g='.$row["name"].'"><img src="groups/'.$row["name"].'/'.$row["logo"].'" alt="'.$row["name"].'" title="'.$row["name"].'" width="50" height="50" border="0" /></a>';
    }
  }
// My groups list 
  $sql = "SELECT gm.gname, gp.logo
      FROM gmembers AS gm
      LEFT JOIN groups AS gp ON gp.name = gm.gname
      WHERE gm.mname = '$log_username'";
  $query = mysqli_query($db_conx, $sql);
  $g_check = mysqli_num_rows($query);
  if ($g_check > 0){
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
      $mgList .= '<a href="group.php?g='.$row['gname'].'"><img src="groups/'.$row['gname'].'/'.$row['logo'].'" alt="'.$row['gname'].'" title="'.$row['gname'].'" width="50" height="50" border="0" /></a>';
    }
  }
}
// It is important for any file that includes this file, to have
// check_login_status.php included at its very top.
$envelope = '<img src="images/note_dead.jpg" width="22" height="12" alt="Notes" title="This envelope is for logged in members">';
$loginLink = '<a href="login.php">Log In</a> &nbsp; | &nbsp; <a href="signup.php">Sign Up</a>';
if($user_ok == true) {
  $sql = "SELECT notescheck FROM users WHERE username='$log_username' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $row = mysqli_fetch_row($query);
  $notescheck = $row[0];
  $sql = "SELECT id FROM notifications WHERE username='$log_username' AND date_time > '$notescheck' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $numrows = mysqli_num_rows($query);
  $sql = "SELECT id FROM friends WHERE user2='$log_username' AND accepted='0'";
  $query = mysqli_query($db_conx, $sql);
  $numrows2 = mysqli_num_rows($query);
    if ($numrows == 0 && $numrows2 == 0) {
        $envelope = '<a href="notifications.php" title="Your notifications and friend requests"><img src="images/note_still.jpg" width="22" height="12" alt="Notes"></a>';
    } else {
    $envelope = '<a href="notifications.php" title="You have new notifications"><img src="images/notifications.gif" width="22" height="12" alt="Notes"></a>';
  }
    $loginLink = '<a href="user.php?u='.$log_username.'">'.ucwords($log_username).'</a> &nbsp; | &nbsp; <a href="logout.php">Log Out</a>';
}
?>
<div id="pageTop">
  <div id="pageTopWrap">
    <div id="pageTopLogo">
      <a href="/snet/index.php">
        <img src="images/logo.png" alt="logo" title="S-Net - The People's Network">
      </a>
    </div>
    <div id="pageTopRest">
      <div id="menu1">
        <div>
          <?php echo $envelope; ?> &nbsp; &nbsp; <?php echo $loginLink; ?>
        </div>
      </div>
      <div id="menu2">
        <div>
        <?php if(isset($_SESSION['username'])) { ?>
          <a href="/snet/settings.php?u=<?php echo $log_username; ?>">
            <img src="images/settings.png" alt="settings" title="Settings" width="22" height="22" />
          </a>
          <a href="#"><img src="images/group3.png" width="22" height="22" alt="groups" border="0" title="Groups" onclick="return false" onmousedown="showGroups()"></a>
        <a href="/snet/search.php?u=<?php echo $log_username; ?>">
        	<img src="images/search1.png" alt="search" title="Alumni/Freind Search" width="22" height="22" />
        </a>
        <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript">
var isShowing = "no";
function showGroups() {
  if(isShowing == "no"){
    _("groupModule").innerHTML = '<div id="groupWrapper" style="height:510px;"><div id="groupList"><h2>My Groups</h2><hr /><?php echo $mgList; ?><h2>All Groups</h2><hr /><?php echo $agList; ?></div><div id="groupForm"><h2>Create New Group</h2><hr /><p>Group Name:<br /><input type="text" id="gname" onBlur="checkGname()" ><span id="gnamestatus"></span></p><p>How do people join your group?<br /><select name="invite" id="invite"><option value="null" selected>&nbsp;</option><option value="1">By requesting to join.</option><option value="2">By simply joining.</option></select></p><button id="newGroupBtn" onClick="createGroup()">Create Group</button><span id="status"></span></div></div><div class="clear"></div>';
    _("pageMiddle").style.display = "none";
    isShowing = "yes";
  } else {
    _("groupModule").innerHTML = '';
    _("pageMiddle").style.display = "block";
    _("pageMiddle").style.visibility = "hidden";
    _("pageMiddle").style.height = "510px";
    isShowing = "no";
  }
}
function checkGname(){
  var u = _("gname").value;
  var rx = new RegExp;
  rx = /[^a-z 0-9_]/gi;
  u = u.replace(rx, "");
  var rxx = new RegExp;
  rxx = /[ ]/g;
  u = u.replace(rxx, "_");
  
  if(u != ""){
    _("gnamestatus").innerHTML = 'checking ...';
    var ajax = ajaxObj("POST", "php_parsers/group_parser.php");
        ajax.onreadystatechange = function() {
          if(ajaxReturn(ajax) == true) {
              _("gnamestatus").innerHTML = ajax.responseText;
          }
        }
        ajax.send("gnamecheck="+u);
  }
}
function createGroup(){
  var name = _("gname").value;
  var inv = _("invite").value;
  if(name == "" || inv == "null"){
    alert("Fill all fields");
    return false;
  } else {
    status.innerHTML = 'please wait ...';
    var ajax = ajaxObj("POST", "php_parsers/group_parser.php");
    ajax.onreadystatechange = function() {
      if(ajaxReturn(ajax) == true) {
        var datArray = ajax.responseText.split("|");
        if(datArray[0] == "group_created"){
        var sid = datArray[1];
          window.location = "group.php?g="+sid;
        } else {
          alert(ajax.responseText);
        }
      }
    }
    ajax.send("action=new_group&name="+name+"&inv="+inv);
  } 
}
</script>
<div id="groupModule"></div>