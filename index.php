<?php require_once('Connections/subman.php'); ?>
<?php include('includes/ipm_class.php');

$ipm = new ipm;


//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

?>
<?php include('includes/standard_functions.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php?status=permissionfail";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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

mysql_select_db($database_subman, $subman);
$query_containers = "SELECT * FROM `container` ORDER BY `container`.name";
$containers = mysql_query($query_containers, $subman) or die(mysql_error());
$row_containers = mysql_fetch_assoc($containers);
$totalRows_containers = mysql_num_rows($containers);

$pageLevel = $ipm->getPageLevel(1,$_SESSION['MM_Username']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IP Manager</title>
<link href="css/ipm5.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/jqueryslidemenu.css" />

<!--[if lte IE 7]>
<style type="text/css">
html .jqueryslidemenu{height: 1%;} /*Holly Hack for IE7 and below*/
</style>
<![endif]-->

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
<script type="text/javascript" src="jqueryslidemenu.js"></script>

<!-- SmartMenus 6 config and script core files -->
<script type="text/javascript" src="c_config.js"></script>
<script type="text/javascript" src="c_smartmenus.js"></script>
<!-- SmartMenus 6 config and script core files -->

<!-- SmartMenus 6 Scrolling for Overlong Menus add-on -->
<script type="text/javascript" src="c_addon_scrolling.js"></script>
</head>


<body>
<div class="banner">
  <img src="images/ipm_banner.gif" alt="IPM Home" name="ipm_banner" width="296" height="59" id="ipm_banner" />
</div>
<div class="ipm_body">


					<h1>Welcome to IP Manager 5</h1>
					
                    <div class="ipm_nav">
					<div id="myslidemenu" class="jqueryslidemenu">
<ul>

<?php include('includes/standard_nav.php'); ?>
<?php include('includes/standard_nav_footer.php'); ?>
</ul>
</div>
</div>

<p>Please select an option from the menu above to begin.</p>
					
</div>
<?php if (isset($_GET['status']) && $_GET['status'] == 'permissionfail') { ?>
<p class="text_red">Error: You are not authorised to view the selected content.</p>
<?php } ?>
<?php if ($pageLevel < 1) { ?>
<p class="text_red">Error: You are not authorised to view the selected content.</p>
<?php 
	exit();
} ?>

</div></div>

<?php include('includes/ipm_footer.php'); ?>
</body>
</html>
<?php
mysql_free_result($containers);
?>
