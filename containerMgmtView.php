<?php require_once('Connections/subman.php'); ?>
<?php include('includes/standard_functions.php'); ?>
<?php include('includes/ipm_class.php'); ?>
<?php

$ipm = new ipm;

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

$MM_restrictGoTo = "index.php";
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

$pageLevel = $ipm->getPageLevel(4,$_SESSION['MM_Username']);
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE `container` SET name=%s, descr=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO `container` (name, descr) VALUES (%s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['descr'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

	$lastID = mysql_insert_id();
	
  $insertGoTo = "containerMgmtView.php?browse=containers&container=".$lastID;
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_subman, $subman);
$query_containers1 = "SELECT * FROM `container` ORDER BY `container`.name";
$containers1 = mysql_query($query_containers1, $subman) or die(mysql_error());
$row_containers1 = mysql_fetch_assoc($containers1);
$totalRows_containers1 = mysql_num_rows($containers1);

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

<link rel="stylesheet" type="text/css" href="css/jqcontextmenu.css" />

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>

<script type="text/javascript" src="jqcontextmenu.js">

/***********************************************
* jQuery Context Menu- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for this script and 100s more
***********************************************/

</script>

<script type="text/javascript">

//Usage: $(elementselector).addcontextmenu('id_of_context_menu_on_page')
//To apply context menu to entire document, use: $(document).addcontextmenu('id_of_context_menu_on_page')

jQuery(document).ready(function($){
	$('div.rightclick').addcontextmenu('contextmenu1') //apply context menu to links with class="mylinks"
})



</script>

<script type="text/javascript" src="jqueryslidemenu.js"></script>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}

function checkAll(theForm, status) {
for (i=0,n=theForm.elements.length;i<n;i++)
theForm.elements[i].checked = status;
}
//-->
</script>
</head>

<body>

<div class="rightclick">
<div class="banner">
  <img src="images/ipm_banner.gif" alt="IPM Home" name="ipm_banner" width="296" height="59" id="ipm_banner" />
</div>

<div class="ipm_body">

<div class="ipm_nav">
<!-- Sample menu definition -->
<div id="myslidemenu" class="jqueryslidemenu" style="z-index:10000">

<ul>
	<?php include('includes/standard_nav.php'); ?>
	<li><a href="?browse=containers"><img src="images/containermgmt_icon.gif" alt="Container Management" align="absmiddle" width="26" height="20" border="0" /> Container Management</a>
        	<?php if ($totalRows_containers1 > 0) { // Show if recordset not empty ?>
        <ul>
  <?php do { 
	?>
    <li><a href="?browse=containers&container=<?php echo $row_containers1['id']; ?>" title="<?php echo $row_containers1['descr']; ?>"><?php if (strlen($row_containers1['name']) < 25) { echo $row_containers1['name']; } else { echo substr_replace($row_containers1['name'],'...',25); } ?></a></li>
    <?php 
	} while ($row_containers1 = mysql_fetch_assoc($containers1)); ?>
    	</ul>
    <?php } // Show if recordset not empty ?>
    </li>
    <?php include('includes/standard_nav_footer.php'); ?>
</ul>
</div>
</div>

<div class="rightclick">

<?php if ($pageLevel < 127) { ?>
<p class="text_red">Error: You are not authorised to view the selected content.</p>
<?php 
	exit();
} ?>

<div id="containerView">
  <div id="containerView_body">
  <?php if ($_GET['browse'] == "containers") { ?>
  
  <?php if ($_POST['action'] == "add_container") { ?>
  <form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2" onsubmit="MM_validateForm('name','','R','descr','','R');return document.MM_returnValue">
    <table >
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Container Name:</td>
        <td><input name="name" type="text" class="input_standard" id="name" value="" size="32" maxlength="255" /></td>
      </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right" valign="top">Description:</td>
        <td><textarea name="descr" cols="32" rows="5" class="input_standard" id="descr"></textarea></td>
      </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">&nbsp;</td>
        <td><input type="submit" class="input_standard" value="Add container" /></td>
      </tr>
    </table>
    <input type="hidden" name="MM_insert" value="form2" />
  </form>
  <p>&nbsp;</p>
<?php } ?>
  
  <?php if ($_POST['action'] == "" && $_GET['container'] != "") { 
  
  	mysql_select_db($database_subman, $subman);
	$query_getContainer = "SELECT * FROM `container` WHERE `container`.id = ".$_GET['container']."";
	$getContainer = mysql_query($query_getContainer, $subman) or die(mysql_error());
	$row_getContainer = mysql_fetch_assoc($getContainer);
	$totalRows_getContainer = mysql_num_rows($getContainer); 
	
	?>

<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" onsubmit="MM_validateForm('name','','R','descr','','R');return document.MM_returnValue">
  <table >
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Container Name:</td>
      <td><input name="name" type="text" class="input_standard" id="name" value="<?php echo htmlentities($row_getContainer['name'], ENT_COMPAT, 'utf-8'); ?>" size="32" maxlength="255" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right" valign="top">Description:</td>
      <td><textarea name="descr" cols="32" rows="5" class="input_standard" id="descr"><?php echo htmlentities($row_getContainer['descr'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" class="input_standard" value="Update container" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="id" value="<?php echo $row_getContainer['id']; ?>" />
</form>
<p>&nbsp;</p>
<?php }
	
	elseif ($_POST['action'] != "add_container") { ?>
	
		<p><a href="#" onClick="document.getElementById('action').value = 'add_container'; document.getElementById('frm_container_action').submit(); return false;">Add a container</a></p>
		
	<?php } ?>
	
  <?php } ?>

<?php
if ($_GET['browse'] == "" && $_POST['action'] != "add_container") { ?>
	
		<p><a href="#" onClick="document.getElementById('action').value = 'add_container'; document.getElementById('frm_container_action').submit(); return false;">Add a container</a></p>
		
	<?php } ?>
	
</div>

<div>


<!--HTML for Context Menu 1-->
<ul id="contextmenu1" class="jqcontextmenu">

    	<form action="containerMgmtView.php?browse=containers&container=<?php echo $_GET['container']; ?>" method="post" name="frm_container_action" target="_self" id="frm_container_action">
        	<input type="hidden" name="action" id="action">
        <li><a href="#" onClick="document.getElementById('action').value = 'add_container'; document.getElementById('frm_container_action').submit(); return false;">Add a container</a></li>
        </form>
    
    </ul>

</div>
</div>
</div>

</body>
</html>
