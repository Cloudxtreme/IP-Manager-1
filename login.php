<?php require_once('Connections/subman.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "index.php";
  $MM_redirectLoginFailed = "login.php?status=authenticationfail";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_subman, $subman);
  
  $LoginRS__query=sprintf("SELECT username, password FROM `user` WHERE username=%s AND password=%s AND inactive=0",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $subman) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IP Manager</title>
<link href="css/ipm5.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="banner">
  <img src="images/ipm_banner.gif" alt="IPM Home" name="ipm_banner" width="296" height="59" id="ipm_banner" />
</div>
<div class="ipm_body">

	<h2>Welcome to IP Manager 5</h2>


					
<?php if (isset($_GET['status']) && $_GET['status'] == 'authenticationfail') { ?>
<p class="text_red">Error: The username/password entered is not correct.</p>
<?php } ?>
<?php if (isset($_GET['status']) && $_GET['status'] == 'permissionfail') { ?>
<p class="text_red">Please authenticate to view the requested content.</p>
<?php } ?>
<table width="50%" border="0" padding="10">
<tr>
<td width="100"><img src="images/j_login_lock.png" alt="Login"></td>
<td>
<form name="frm_login" method="POST" action="<?php echo $loginFormAction; ?>">
  <p>
    <label><strong>Username</strong><br />
      <input name="username" type="text" class="input_standard" id="username" size="24" maxlength="255" />
    </label>
  </p>
  <p>
    <label><strong>Password</strong><br />
<input name="password" type="password" class="input_standard" id="password" size="24" maxlength="255" />
    </label>
  </p>
  <p>
<input name="log_in" type="submit" class="input_standard" id="log_in" value="Submit" />
  </p>
</form>
</td>
</tr>
</table>

</div>
<?php include('includes/ipm_footer.php'); ?>
</body>
</html>