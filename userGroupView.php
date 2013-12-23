<?php require_once('Net/IPv4.php'); ?>
<?php require_once('Net/IPv6.php'); ?>
<?php require_once('Connections/subman.php'); ?>
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

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_update_group")) {
  $updateSQL = sprintf("UPDATE usrgroup SET name=%s, descr=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form5")) {
  $updateSQL = sprintf("UPDATE `user` SET firstname=%s, lastname=%s, username=%s, password=%s, usrgroup=%s, email=%s, inactive=%s WHERE id=%s",
                       GetSQLValueString($_POST['firstname'], "text"),
                       GetSQLValueString($_POST['lastname'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['email'], "text"),
					   GetSQLValueString($_POST['inactive'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_observium")) {

	mysql_select_db($database_subman, $subman);
	$query_check_group_observiums = "SELECT * FROM usrgroupobserviumpermissions WHERE usrgroupobserviumpermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupobserviumpermissions.`observium` = '".$_POST['observium']."'";
	$check_group_observiums = mysql_query($query_check_group_observiums, $subman) or die(mysql_error());
	$row_check_group_observiums = mysql_fetch_assoc($check_group_observiums);
	$totalRows_check_group_observiums = mysql_num_rows($check_group_observiums);

	if ($totalRows_check_group_observiums > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupobserviumpermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_observiums['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}

  $insertSQL = sprintf("INSERT INTO usrgroupobserviumpermissions (usrgroup, observium, `level`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['observium'], "int"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form13")) {

	mysql_select_db($database_subman, $subman);
	$query_check_group_damits = "SELECT * FROM usrgroupdamitpermissions WHERE usrgroupdamitpermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupdamitpermissions.`damit` = '".$_POST['damit']."'";
	$check_group_damits = mysql_query($query_check_group_damits, $subman) or die(mysql_error());
	$row_check_group_damits = mysql_fetch_assoc($check_group_damits);
	$totalRows_check_group_damits = mysql_num_rows($check_group_damits);

	if ($totalRows_check_group_damits > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupdamitpermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_damits['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}

  $insertSQL = sprintf("INSERT INTO usrgroupdamitpermissions (usrgroup, damit, `level`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['damit'], "int"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form12")) {

	mysql_select_db($database_subman, $subman);
	$query_check_group_radiators = "SELECT * FROM usrgroupradiatorpermissions WHERE usrgroupradiatorpermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupradiatorpermissions.`radiator` = '".$_POST['radiator']."'";
	$check_group_radiators = mysql_query($query_check_group_radiators, $subman) or die(mysql_error());
	$row_check_group_radiators = mysql_fetch_assoc($check_group_radiators);
	$totalRows_check_group_radiators = mysql_num_rows($check_group_radiators);

	if ($totalRows_check_group_radiators > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupradiatorpermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_radiators['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}

  $insertSQL = sprintf("INSERT INTO usrgroupradiatorpermissions (usrgroup, radiator, `level`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['radiator'], "int"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form11")) {

	mysql_select_db($database_subman, $subman);
	$query_check_group_pages = "SELECT * FROM usrgrouppermissions WHERE usrgrouppermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgrouppermissions.`module` = '".$_POST['module']."'";
	$check_group_pages = mysql_query($query_check_group_pages, $subman) or die(mysql_error());
	$row_check_group_pages = mysql_fetch_assoc($check_group_pages);
	$totalRows_check_group_pages = mysql_num_rows($check_group_pages);

	if ($totalRows_check_group_pages > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgrouppermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_pages['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}

  $insertSQL = sprintf("INSERT INTO usrgrouppermissions (usrgroup, module, `level`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['module'], "int"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form10")) {

	mysql_select_db($database_subman, $subman);
$query_check_group_vpns = "SELECT * FROM usrgroupvpnpermissions WHERE usrgroupvpnpermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupvpnpermissions.`vpn` = '".$_POST['vpn']."'";
$check_group_vpns = mysql_query($query_check_group_vpns, $subman) or die(mysql_error());
$row_check_group_vpns = mysql_fetch_assoc($check_group_vpns);
$totalRows_check_group_vpns = mysql_num_rows($check_group_vpns);

	if ($totalRows_check_group_vpns > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupvpnpermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_vpns['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}

  $insertSQL = sprintf("INSERT INTO usrgroupvpnpermissions (usrgroup, vpn, `level`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['vpn'], "int"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form9")) {

	mysql_select_db($database_subman, $subman);
$query_check_group_providers = "SELECT * FROM usrgroupproviderpermissions WHERE usrgroupproviderpermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupproviderpermissions.`provider` = '".$_POST['provider']."'";
$check_group_providers = mysql_query($query_check_group_providers, $subman) or die(mysql_error());
$row_check_group_providers = mysql_fetch_assoc($check_group_providers);
$totalRows_check_group_providers = mysql_num_rows($check_group_providers);

	if ($totalRows_check_group_providers > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupproviderpermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_providers['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}

  $insertSQL = sprintf("INSERT INTO usrgroupproviderpermissions (usrgroup, provider, `level`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['provider'], "int"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form8")) {

	mysql_select_db($database_subman, $subman);
$query_check_group_devices = "SELECT * FROM usrgroupdevicepermissions WHERE usrgroupdevicepermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupdevicepermissions.`device` = '".$_POST['device']."'";
$check_group_devices = mysql_query($query_check_group_devices, $subman) or die(mysql_error());
$row_check_group_devices = mysql_fetch_assoc($check_group_devices);
$totalRows_check_group_devices = mysql_num_rows($check_group_devices);

	if ($totalRows_check_group_devices > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupdevicepermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_devices['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}

  $insertSQL = sprintf("INSERT INTO usrgroupdevicepermissions (usrgroup, device, `level`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['device'], "int"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form7")) {

	mysql_select_db($database_subman, $subman);
$query_check_group_device_groups = "SELECT * FROM usrgroupdevicegrouppermissions WHERE usrgroupdevicegrouppermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupdevicegrouppermissions.`devicegroup` = '".$_POST['devicegroup']."'";
$check_group_device_groups = mysql_query($query_check_group_device_groups, $subman) or die(mysql_error());
$row_check_group_device_groups = mysql_fetch_assoc($check_group_device_groups);
$totalRows_check_group_device_groups = mysql_num_rows($check_group_device_groups);

	if ($totalRows_check_group_device_groups > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupdevicegrouppermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_device_groups['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}

  $insertSQL = sprintf("INSERT INTO usrgroupdevicegrouppermissions (usrgroup, devicegroup, `level`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['devicegroup'], "int"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form6")) {
	
	mysql_select_db($database_subman, $subman);
	$query_check_username = "SELECT * FROM `user` WHERE `user`.username = '".$_POST['username']."'";
	$check_username = mysql_query($query_check_username, $subman) or die(mysql_error());
	$row_check_username = mysql_fetch_assoc($check_username);
	$totalRows_check_username = mysql_num_rows($check_username);

if ($totalRows_check_username > 0) { ?>
	<p class="text_red">Error: That username is already in use by <?php echo $row_check_username['firstname']; ?> <?php echo $row_check_username['lastname']; ?></p>
<?php
}
else {
	
  $insertSQL = sprintf("INSERT INTO `user` (firstname, lastname, username, password, usrgroup, email) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['firstname'], "text"),
                       GetSQLValueString($_POST['lastname'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['email'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php?browse=users&user=".mysql_insert_id();
 
  header(sprintf("Location: %s", $insertGoTo));
}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form4")) {
	
	mysql_select_db($database_subman, $subman);
$query_check_group_networks = "SELECT * FROM usrgroupnetworkpermissions WHERE usrgroupnetworkpermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupnetworkpermissions.network = '".$_POST['network']."'";
$check_group_networks = mysql_query($query_check_group_networks, $subman) or die(mysql_error());
$row_check_group_networks = mysql_fetch_assoc($check_group_networks);
$totalRows_check_group_networks = mysql_num_rows($check_group_networks);

	if ($totalRows_check_group_networks > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupnetworkpermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_networks['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}
	
  $insertSQL = sprintf("INSERT INTO usrgroupnetworkpermissions (usrgroup, network, `level`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['network'], "int"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
	
	mysql_select_db($database_subman, $subman);
	$query_check_group_netgroups = "SELECT * FROM usrgroupnetgrouppermissions WHERE usrgroupnetgrouppermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupnetgrouppermissions.networkgroup = '".$_POST['networkgroup']."'";
	$check_group_netgroups = mysql_query($query_check_group_netgroups, $subman) or die(mysql_error());
	$row_check_group_netgroups = mysql_fetch_assoc($check_group_netgroups);
	$totalRows_check_group_netgroups = mysql_num_rows($check_group_netgroups);

	if ($totalRows_check_group_netgroups > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupnetgrouppermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_netgroups['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}
	
  $insertSQL = sprintf("INSERT INTO usrgroupnetgrouppermissions (usrgroup, networkgroup, `level`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['networkgroup'], "int"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_radgroups")) {
	
	mysql_select_db($database_subman, $subman);
	$query_check_group_radgroups = "SELECT * FROM usrgroupradgrouppermissions WHERE usrgroupradgrouppermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupradgrouppermissions.`radiator` = '".$_POST['radiator']."' AND usrgroupradgrouppermissions.subscribergrp = '".$_POST['subscribergrp']."'";
	$check_group_radgroups = mysql_query($query_check_group_radgroups, $subman) or die(mysql_error());
	$row_check_group_radgroups = mysql_fetch_assoc($check_group_radgroups);
	$totalRows_check_group_radgroups = mysql_num_rows($check_group_radgroups);

	if ($totalRows_check_group_radgroups > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupradgrouppermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_radgroups['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}
  $insertSQL = sprintf("INSERT INTO usrgroupradgrouppermissions (usrgroup, `radiator`, subscribergrp, `level`) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['radiator'], "int"),
					   GetSQLValueString($_POST['subscribergrp'], "text"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_radfeatures")) {
	
	mysql_select_db($database_subman, $subman);
	$query_check_group_radfeatures = "SELECT * FROM usrgroupradfeaturepermissions WHERE usrgroupradfeaturepermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupradfeaturepermissions.`radiator` = '".$_POST['radiator']."' AND usrgroupradfeaturepermissions.feature = '".$_POST['feature']."'";
	$check_group_radfeatures = mysql_query($query_check_group_radfeatures, $subman) or die(mysql_error());
	$row_check_group_radfeatures = mysql_fetch_assoc($check_group_radfeatures);
	$totalRows_check_group_radfeatures = mysql_num_rows($check_group_radfeatures);

	if ($totalRows_check_group_radfeatures > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupradfeaturepermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_radfeatures['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}
  $insertSQL = sprintf("INSERT INTO usrgroupradfeaturepermissions (usrgroup, `radiator`, feature, `level`) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['radiator'], "int"),
					   GetSQLValueString($_POST['feature'], "text"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	
	mysql_select_db($database_subman, $subman);
$query_check_group_containers = "SELECT * FROM usrgroupcontainerpermissions WHERE usrgroupcontainerpermissions.usrgroup = '".$_POST['usrgroup']."' AND usrgroupcontainerpermissions.`container` = '".$_POST['container']."'";
$check_group_containers = mysql_query($query_check_group_containers, $subman) or die(mysql_error());
$row_check_group_containers = mysql_fetch_assoc($check_group_containers);
$totalRows_check_group_containers = mysql_num_rows($check_group_containers);

	if ($totalRows_check_group_containers > 0) {
		
		$deleteSQL = sprintf("DELETE FROM usrgroupcontainerpermissions WHERE id=%s",
                       GetSQLValueString($row_check_group_containers['id'], "int"));

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}
  $insertSQL = sprintf("INSERT INTO usrgroupcontainerpermissions (usrgroup, `container`, `level`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['usrgroup'], "int"),
                       GetSQLValueString($_POST['container'], "int"),
                       GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST['usrgroup'])) && ($_POST['usrgroup'] != "") && (isset($_POST['delete_group']))) {

	mysql_select_db($database_subman, $subman);
	$query_check_group_users = "SELECT * FROM `user` WHERE `user`.usrgroup = '".$_POST['usrgroup']."'";
	$check_group_users = mysql_query($query_check_group_users, $subman) or die(mysql_error());
	$row_check_group_users = mysql_fetch_assoc($check_group_users);
	$totalRows_check_group_users = mysql_num_rows($check_group_users);

	if ($totalRows_check_group_users > 0 || $_POST['usrgroup'] == 1) { 
    
    	$msg = '<p class="text_red">You cannot delete the Superuser group, or a group that contains users.  You must remove the users from the group first.</p>';
		
	}
	else {
	
  $deleteSQL = sprintf("DELETE FROM usrgroup WHERE id=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());

  $deleteSQL = sprintf("DELETE FROM usrgrouppermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());

  $deleteSQL = sprintf("DELETE FROM usrgroupnetworkpermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM usrgroupnetgrouppermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM usrgroupcontainerpermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());  
  
  $deleteSQL = sprintf("DELETE FROM usrgroupdevicepermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM usrgroupdevicegrouppermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM usrgroupproviderpermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM usrgroupvpnpermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM usrgroupradiatorpermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM usrgroupradgrouppermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM usrgroupradfeaturepermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM usrgroupdamitpermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM usrgroupobserviumpermissions WHERE usrgroup=%s",
                       GetSQLValueString($_POST['usrgroup'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteGoTo = "userGroupView.php?browse=groups";

  header(sprintf("Location: %s", $deleteGoTo));
  
	}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO usrgroup (name, descr) VALUES (%s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['descr'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "userGroupView.php?browse=groups&group=".mysql_insert_id();
  
  header(sprintf("Location: %s", $insertGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_observiums') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupobserviumpermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_damits') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupdamitpermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_radiatorgroups') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupradgrouppermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_radiatorfeatures') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupradfeaturepermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_radiators') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupradiatorpermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_pages') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgrouppermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_vpns') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupvpnpermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_providers') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupproviderpermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}


if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_device_groups') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupdevicegrouppermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_devices') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupdevicepermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}


if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_networks') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupnetworkpermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_netgroups') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupnetgrouppermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_group_containers') {
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM usrgroupcontainerpermissions WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "userGroupView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

mysql_select_db($database_subman, $subman);
$query_usergroups = "SELECT * FROM usrgroup ORDER BY usrgroup.name";
$usergroups = mysql_query($query_usergroups, $subman) or die(mysql_error());
$row_usergroups = mysql_fetch_assoc($usergroups);
$totalRows_usergroups = mysql_num_rows($usergroups);

mysql_select_db($database_subman, $subman);
$query_users = "SELECT * FROM `user` ORDER BY `user`.lastname, `user`.firstname";
$users = mysql_query($query_users, $subman) or die(mysql_error());
$row_users = mysql_fetch_assoc($users);
$totalRows_users = mysql_num_rows($users);
?>
<?php include('includes/standard_functions.php'); ?>
<?php $pageLevel = $ipm->getPageLevel(3,$_SESSION['MM_Username']); ?>
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
<!-- SmartMenus 6 config and script core files -->
<script type="text/javascript" src="c_config.js"></script>
<script type="text/javascript" src="c_smartmenus.js"></script>
<!-- SmartMenus 6 config and script core files -->

<!-- SmartMenus 6 Scrolling for Overlong Menus add-on -->
<script type="text/javascript" src="c_addon_scrolling.js"></script>
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
	<li><a href="?browse=groups"><img src="images/usergroup_icon.gif" alt="User Groups" width="16" height="20" align="absmiddle" border="0" /> User Groups</a>
    	<ul>
			<?php if ($totalRows_usergroups > 0) { // Show if recordset not empty ?>
  <?php do { ?>
    <li><a class="NOLINK"><?php if (strlen($row_usergroups['name']) < 25) { echo $row_usergroups['name']; } else { echo substr_replace($row_usergroups['name'],'...',25); } ?></a>
    	<ul>
        	<li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=users" title="Group user membership">Users</a></li>
        	<li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=pages" title="Page permissions for '<?php echo $row_usergroups['name']; ?>'">Pages</a></li>
            <li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=containers" title="Container permissions for '<?php echo $row_usergroups['name']; ?>'">Containers</a></li>
			<li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=groups" title="Network group permissions for '<?php echo $row_usergroups['name']; ?>'">Network Groups</a></li>
			<li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=networks" title="Network permissions for '<?php echo $row_usergroups['name']; ?>'">Networks</a></li>
			<li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=devicegroups" title="Device group permissions for '<?php echo $row_usergroups['name']; ?>'">Device Groups</a></li>
			<li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=devices" title="Device permissions for '<?php echo $row_usergroups['name']; ?>'">Devices</a></li>
			<li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=providers" title="VPN provider permissions for '<?php echo $row_usergroups['name']; ?>'">VPN Providers</a></li>
			<li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=vpns" title="VPN permissions for '<?php echo $row_usergroups['name']; ?>'">VPNs</a></li>
			<li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=radiators" title="Radiator permissions for '<?php echo $row_usergroups['name']; ?>'">Radiators</a></li>
            <li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=radiatorgroups" title="Radiator subscriber group permissions for '<?php echo $row_usergroups['name']; ?>'">Radiator Subscriber Groups</a></li>
            <li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=radiatorfeatures" title="Radiator features permissions for '<?php echo $row_usergroups['name']; ?>'">Radiator features</a></li>
            <li><a href="?browse=groups&group=<?php echo $row_usergroups['id']; ?>&section=damit" title="DAM-it permissions for '<?php echo $row_usergroups['name']; ?>'">DAM-it</a></li>
        </ul>
	</li>
    <?php 
	} while ($row_usergroups = mysql_fetch_assoc($usergroups)); ?>
    <?php } // Show if recordset not empty ?>        
        </ul>
    </li>
    <li><a href="?browse=users"><img src="images/user_icon.gif" alt="Users" width="15" height="20" align="absmiddle" border="0" /> Users</a>
    	<ul>
        	<?php if ($totalRows_users > 0) { ?>
<?php do {
		$fullname = $row_users['lastname'].', '.$row_users['firstname']; ?>
    <li><a href="?browse=users&user=<?php echo $row_users['id']; ?>" title="<?php echo $row_users['lastname']; ?>, <?php echo $row_users['firstname']; ?> (<?php echo $row_users['email']; ?>)"><?php if (strlen($fullname) < 25) { echo $fullname; } else { echo substr_replace($fullname,'...',25); } ?></a></li>
  <?php } while ($row_users = mysql_fetch_assoc($users)); ?>
<?php } ?>
        </ul>
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
  <?php
if (isset($msg)) {
		echo $msg;
		exit();
}
?>
  <?php if ($_GET['browse'] == "groups") {
  
  	if ($_GET['section'] == "") {
			
		if ($_POST['action'] == "add_group") { ?>
  <form action="<?php echo $editFormAction; ?>" method="post" name="form1" onSubmit="MM_validateForm('name','','R');return document.MM_returnValue">
    <table >
      <tr valign="baseline">
        <td align="right" valign="middle" nowrap>Group<br />
          Name:</td>
        <td valign="middle"><input name="name" type="text" class="input_standard" size="32" maxlength="255" /></td>
      </tr>
      <tr valign="baseline">
        <td align="right" valign="middle" nowrap>Group<br />
          Description:</td>
        <td valign="middle"><textarea name="descr" cols="32" rows="5" class="input_standard"></textarea></td>
      </tr>
      <tr valign="baseline">
        <td align="right" valign="middle" nowrap>&nbsp;</td>
        <td valign="middle"><input type="submit" class="input_standard" value="Add Group" /></td>
      </tr>
    </table>
    <input type="hidden" name="MM_insert" value="form1" />
  </form>
  <?php } 
  }
  	if ($_GET['browse'] == "groups" && $_GET['group'] != "") { 
  	
	mysql_select_db($database_subman, $subman);
	$query_group = "SELECT * FROM usrgroup WHERE usrgroup.id = '".$_GET['group']."'";
	$group = mysql_query($query_group, $subman) or die(mysql_error());
	$row_group = mysql_fetch_assoc($group);
	$totalRows_group = mysql_num_rows($group);
	
	?>
  
  Group &gt;&gt; <strong><?php echo $row_group['name']; ?></strong><br /><br />
		
		<?php if ($_GET['section'] == "") {
			
		if ($_POST['action'] == "add_group") { ?>
  <form action="<?php echo $editFormAction; ?>" method="post" name="form1" onSubmit="MM_validateForm('name','','R');return document.MM_returnValue">
    <table >
      <tr valign="baseline">
        <td align="right" valign="middle" nowrap>Group<br />
          Name:</td>
        <td valign="middle"><input name="name" type="text" class="input_standard" size="32" maxlength="255" /></td>
      </tr>
      <tr valign="baseline">
        <td align="right" valign="middle" nowrap>Group<br />
          Description:</td>
        <td valign="middle"><textarea name="descr" cols="32" rows="5" class="input_standard"></textarea></td>
      </tr>
      <tr valign="baseline">
        <td align="right" valign="middle" nowrap>&nbsp;</td>
        <td valign="middle"><input type="submit" class="input_standard" value="Add Group" /></td>
      </tr>
    </table>
    <input type="hidden" name="MM_insert" value="form1" />
  </form>
  <?php } 
  	elseif ($_POST['action'] == "edit_group") { 
	
	mysql_select_db($database_subman, $subman);
	$query_group = "SELECT * FROM usrgroup WHERE usrgroup.id = '".$_GET['group']."'";
	$group = mysql_query($query_group, $subman) or die(mysql_error());
	$row_group = mysql_fetch_assoc($group);
	$totalRows_group = mysql_num_rows($group);

	mysql_select_db($database_subman, $subman);
	$query_containers = "SELECT * FROM container ORDER BY container.name";
	$containers = mysql_query($query_containers, $subman) or die(mysql_error());
	$row_containers = mysql_fetch_assoc($containers);
	$totalRows_containers = mysql_num_rows($containers);

?>
  <form action="<?php echo $editFormAction; ?>" method="post" name="frm_update_group" id="frm_update_group" onSubmit="MM_validateForm('name','','R');return document.MM_returnValue">
    <table >
      <tr valign="baseline">
        <td align="right" valign="middle" nowrap>Group<br />
          Name:</td>
        <td valign="middle"><input name="name" type="text" class="input_standard" value="<?php echo $row_group['name']; ?>" size="32" maxlength="255" /></td>
      </tr>
      <tr valign="baseline">
        <td align="right" valign="middle" nowrap>Group<br />
          Description:</td>
        <td valign="middle"><textarea name="descr" cols="32" rows="5" class="input_standard"><?php echo $row_group['descr']; ?></textarea></td>
      </tr>
      <tr valign="baseline">
        <td align="right" valign="middle" nowrap>&nbsp;</td>
        <td valign="middle"><input type="submit" class="input_standard" value="Update Group" />
        <input type="hidden" name="MM_update" value="frm_update_group" />
    <input type="hidden" name="id" value="<?php echo $row_group['id']; ?>" />
  </form>
  <br /><br />
  <form action="userGroupView.php" target="_self" name="frm_group_delete" method="post" id="frm_group_delete">
    <input type="hidden" name="browse2" value="<?php echo $_GET['browse']; ?>" />
    <input type="hidden" name="usrgroup" value="<?php echo $_GET['group']; ?>" />
    <input type="hidden" name="delete_group" value="1" />
    <input type="submit" value="Delete Group" class="input_standard" />
  </form></td>
      </tr>
    </table>
  <?php }
		}
		
		elseif ($_GET['section'] == 'users') {
			
			mysql_select_db($database_subman, $subman);
			$query_group_users = "SELECT `user`.* FROM `user` WHERE `user`.usrgroup = '".$_GET['group']."' ORDER BY `user`.lastname, `user`.firstname";
			$group_users = mysql_query($query_group_users, $subman) or die(mysql_error());
			$row_group_users = mysql_fetch_assoc($group_users);
			$totalRows_group_users = mysql_num_rows($group_users); ?>
            
		  <?php if ($totalRows_group_users > 0) { // Show if recordset not empty ?>
          <?php do { ?>
            <a href="?browse=users&amp;user=<?php echo $row_group_users['id']; ?>" title="Modify user"><?php echo $row_group_users['lastname']; ?>, <?php echo $row_group_users['firstname']; ?> (<?php echo $row_group_users['username']; ?>)</a> <?php if ($row_group_users['inactive'] == 1) { ?><span class="text_red">[disabled]</span><?php } ?><br />
            <?php } while ($row_group_users = mysql_fetch_assoc($group_users)); ?>
            <?php } // Show if recordset not empty 
				else { ?>
                	<p>There are no users to display for this group.</p>
            <?php } ?>
<?php	
		}
		
		elseif ($_GET['section'] == 'radiatorgroups') {
		
			mysql_select_db($database_subman, $subman);
			$query_group_radgroups = "SELECT usrgroupradgrouppermissions.*, usrgroupradgrouppermissions.id as pid, radiator.host, radiator.descr FROM usrgroupradgrouppermissions LEFT JOIN radiator ON radiator.id = usrgroupradgrouppermissions.radiator LEFT JOIN usrgroup ON usrgroup.id = usrgroupradgrouppermissions.usrgroup WHERE usrgroup.id = '".$_GET['group']."'";
			$group_radgroups = mysql_query($query_group_radgroups, $subman) or die(mysql_error());
			$row_group_radgroups = mysql_fetch_assoc($group_radgroups);
			$totalRows_group_radgroups = mysql_num_rows($group_radgroups);
?>
<form action="" method="post" target="_self" name="frm_delete_group_radiatorgroups" id="frm_delete_group_radiatorgroups">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_radgroups; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_radiatorgroups">
  <table border="0" width="50%">
    <tr>
      <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_radiatorgroups'), this.checked);" /></td>
      <td><strong>Radiator</strong></td>
      <td><strong>Subscriber Group</strong></td>
      <td><strong>Permissions</strong></td>
    </tr>
    <?php if ($totalRows_group_radgroups > 0) { // Show if recordset not empty ?>
    <?php
		$count = 0;
		do { 

			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			}
	?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_radgroups['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_radgroups['pid']; ?>"></td>
  <td><a title="<?php echo $row_group_radgroups['descr']; ?>"><?php echo $row_group_radgroups['host']; ?></a></td>
  	<td><?php echo $row_group_radgroups['subscribergrp']; ?></td>
        <td><?php echo getPermission($row_group_radgroups['level']); ?></td>
    </tr>
    <?php } while ($row_group_radgroups = mysql_fetch_assoc($group_radgroups)); ?>
     <?php } // Show if recordset not empty 
			else { ?>
    <tr>
      <td>&nbsp;</td>
      <td>There are no Radiator subscriber group permissions for this user group.</td></tr>
    	<?php } ?>
  </table>
  </form>
  <form action="<?php echo $editFormAction; ?>" method="post" name="frm_radgroups" id="frm_radgroups" onSubmit="MM_validateForm('subscribergrp','','R');return document.MM_returnValue">
    <table >
      <tr> </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Radiator:</td>
        <td><select name="radiator" class="input_standard">
          <?php 
		  	mysql_select_db($database_subman, $subman);
			$query_radiators = "SELECT * FROM radiator ORDER BY radiator.host";
			$radiators = mysql_query($query_radiators, $subman) or die(mysql_error());
			$row_radiators = mysql_fetch_assoc($radiators);
			$totalRows_radiators = mysql_num_rows($radiators);
do {  
?>

<option value="<?php echo $row_radiators['id']?>" ><?php echo $row_radiators['host']?></option>
          <?php
} while ($row_radiators = mysql_fetch_assoc($radiators));
?>
        </select></td>
      </tr>
      <tr valign="baseline">
      	<td nowrap="nowrap" align="right">Subscriber Group: </td>
        <td><input name="subscribergrp" type="text" class="input_standard" id="subscribergrp" size="32" maxlength="255" />
        </td>    
      </tr>
      <tr> </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Permissions:</td>
        <td><select name="level" class="input_standard">
          <option value="0" <?php if (!(strcmp(0, ""))) {echo "SELECTED";} ?>>None</option>
          <option value="10" <?php if (!(strcmp(10, ""))) {echo "SELECTED";} ?>>Read Only</option>
          <option value="20" <?php if (!(strcmp(20, ""))) {echo "SELECTED";} ?>>Read, Modify</option>
          <option value="30" <?php if (!(strcmp(30, ""))) {echo "SELECTED";} ?>>Read, Modify, Delete</option>
          <option value="127" <?php if (!(strcmp(127, ""))) {echo "SELECTED";} ?>>Superuser</option>
        </select></td>
      </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">&nbsp;</td>
        <td><input type="submit" class="input_standard" value="Set permissions" />
          <input name="usrgroup" type="hidden" id="usrgroup" value="<?php echo $_GET['group']; ?>" /></td>
      </tr>
    </table>
    <input type="hidden" name="MM_insert" value="frm_radgroups" />
  </form>
<?php		}
		
		elseif ($_GET['section'] == 'radiatorfeatures') {
		
			mysql_select_db($database_subman, $subman);
			$query_group_radiatorfeatures = "SELECT usrgroupradfeaturepermissions.*, usrgroupradfeaturepermissions.id as pid, radiator.host, radiator.descr FROM usrgroupradfeaturepermissions LEFT JOIN radiator ON radiator.id = usrgroupradfeaturepermissions.radiator LEFT JOIN usrgroup ON usrgroup.id = usrgroupradfeaturepermissions.usrgroup WHERE usrgroup.id = '".$_GET['group']."'";
			$group_radiatorfeatures = mysql_query($query_group_radiatorfeatures, $subman) or die(mysql_error());
			$row_group_radiatorfeatures = mysql_fetch_assoc($group_radiatorfeatures);
			$totalRows_group_radiatorfeatures = mysql_num_rows($group_radiatorfeatures);
?>
<form action="" method="post" target="_self" name="frm_delete_group_radiatorfeatures" id="frm_delete_group_radiatorfeatures">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_radiatorfeatures; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_radiatorfeatures">
  <table border="0" width="50%">
    <tr>
      <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_radiatorfeatures'), this.checked);" /></td>
      <td><strong>Radiator</strong></td>
      <td><strong>Feature</strong></td>
      <td><strong>Permissions</strong></td>
    </tr>
    <?php if ($totalRows_group_radiatorfeatures > 0) { // Show if recordset not empty ?>
    <?php
		$count = 0;
		do { 

			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			}
	?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_radiatorfeatures['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_radiatorfeatures['pid']; ?>"></td>
  <td><a title="<?php echo $row_group_radiatorfeatures['descr']; ?>"><?php echo $row_group_radiatorfeatures['host']; ?></a></td>
  	<td><?php echo $row_group_radiatorfeatures['feature']; ?></td>
        <td><?php echo getPermission($row_group_radiatorfeatures['level']); ?></td>
    </tr>
    <?php } while ($row_group_radiatorfeatures = mysql_fetch_assoc($group_radiatorfeatures)); ?>
     <?php } // Show if recordset not empty 
			else { ?>
    <tr>
      <td>&nbsp;</td>
      <td>There are no Radiator features permissions for this user group.</td></tr>
    	<?php } ?>
  </table>
  </form>
  <form action="<?php echo $editFormAction; ?>" method="post" name="frm_radfeatures" id="frm_radfeatures">
    <table >
      <tr> </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Radiator:</td>
        <td><select name="radiator" class="input_standard">
          <?php 
		  	mysql_select_db($database_subman, $subman);
			$query_radiators = "SELECT * FROM radiator ORDER BY radiator.host";
			$radiators = mysql_query($query_radiators, $subman) or die(mysql_error());
			$row_radiators = mysql_fetch_assoc($radiators);
			$totalRows_radiators = mysql_num_rows($radiators);
do {  
?>

<option value="<?php echo $row_radiators['id']?>" ><?php echo $row_radiators['host']?></option>
          <?php
} while ($row_radiators = mysql_fetch_assoc($radiators));
?>
        </select></td>
      </tr>
      <tr valign="baseline">
      	<td nowrap="nowrap" align="right">Feature: </td>
        <td><select name="feature" class="input_standard">
        	<option value="add_subscriber">Add a subscriber</option>
        	<option value="add_route">Add a subscriber route</option>
            <option value="add_arbitrary_route">Add an arbitrary subscriber route</option>
            <option value="edit_subscriber">Edit a subscriber</option>
            <option value="delete_subscriber">Delete a subscriber</option>
            <option value="delete_routes">Delete subscriber routes</option>
            <option value="delete_arbitrary_routes">Delete arbitrary subscriber routes</option>
            </select>
        </td>    
      </tr>
      <tr> </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Permissions:</td>
        <td><select name="level" class="input_standard">
          <option value="0" <?php if (!(strcmp(0, ""))) {echo "SELECTED";} ?>>None</option>
          <option value="30" <?php if (!(strcmp(30, ""))) {echo "SELECTED";} ?>>Permitted</option>
        </select></td>
      </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">&nbsp;</td>
        <td><input type="submit" class="input_standard" value="Set permissions" />
          <input name="usrgroup" type="hidden" id="usrgroup" value="<?php echo $_GET['group']; ?>" /></td>
      </tr>
    </table>
    <input type="hidden" name="MM_insert" value="frm_radfeatures" />
  </form>
<?php		}


		elseif ($_GET['section'] == 'containers') {
		
			mysql_select_db($database_subman, $subman);
			$query_group_containers = "SELECT container.*, usrgroupcontainerpermissions.level, usrgroupcontainerpermissions.id as pid FROM `container` LEFT JOIN usrgroupcontainerpermissions ON usrgroupcontainerpermissions.container = container.id LEFT JOIN usrgroup ON usrgroup.id = usrgroupcontainerpermissions.usrgroup WHERE usrgroup.id = '".$_GET['group']."'";
			$group_containers = mysql_query($query_group_containers, $subman) or die(mysql_error());
			$row_group_containers = mysql_fetch_assoc($group_containers);
			$totalRows_group_containers = mysql_num_rows($group_containers);
?>
<form action="" method="post" target="_self" name="frm_delete_group_containers" id="frm_delete_group_containers">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_containers; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_containers">
  <table border="0" width="50%">
    <tr>
      <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_containers'), this.checked);" /></td>
      <td><strong>Container</strong></td>
      <td><strong>Permissions</strong></td>
    </tr>
    <?php if ($totalRows_group_containers > 0) { // Show if recordset not empty ?>
    <?php
		$count = 0;
		do { 

			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			}
	?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_containers['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_containers['pid']; ?>"></td>
  <td><a title="<?php echo $row_group_containers['descr']; ?>"><?php echo $row_group_containers['name']; ?></a></td>
        <td><?php echo getPermission($row_group_containers['level']); ?></td>
    </tr>
    <?php } while ($row_group_containers = mysql_fetch_assoc($group_containers)); ?>
     <?php } // Show if recordset not empty 
			else { ?>
    <tr>
      <td>&nbsp;</td>
      <td>There are no container permissions for this user group.</td></tr>
    	<?php } ?>
  </table>
  </form>
  <form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2">
    <table >
      <tr> </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Container:</td>
        <td><select name="container" class="input_standard">
          <?php 
		  	mysql_select_db($database_subman, $subman);
			$query_containers = "SELECT * FROM container ORDER BY container.name";
			$containers = mysql_query($query_containers, $subman) or die(mysql_error());
			$row_containers = mysql_fetch_assoc($containers);
			$totalRows_containers = mysql_num_rows($containers);
do {  
?>

<option value="<?php echo $row_containers['id']?>" ><?php echo $row_containers['name']?></option>
          <?php
} while ($row_containers = mysql_fetch_assoc($containers));
?>
        </select></td>
      </tr>
      <tr> </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Permissions:</td>
        <td><select name="level" class="input_standard">
          <option value="0" <?php if (!(strcmp(0, ""))) {echo "SELECTED";} ?>>None</option>
          <option value="10" <?php if (!(strcmp(10, ""))) {echo "SELECTED";} ?>>Read Only</option>
          <option value="20" <?php if (!(strcmp(20, ""))) {echo "SELECTED";} ?>>Read, Modify</option>
          <option value="30" <?php if (!(strcmp(30, ""))) {echo "SELECTED";} ?>>Read, Modify, Delete</option>
          <option value="127" <?php if (!(strcmp(127, ""))) {echo "SELECTED";} ?>>Superuser</option>
        </select></td>
      </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">&nbsp;</td>
        <td><input type="submit" class="input_standard" value="Set permissions" />
          <input name="usrgroup" type="hidden" id="usrgroup" value="<?php echo $_GET['group']; ?>" /></td>
      </tr>
    </table>
    <input type="hidden" name="MM_insert" value="form2" />
  </form>
<?php		} ?>

<?php if ($_GET['section'] == "groups") { ?>
          <?php
			mysql_select_db($database_subman, $subman);
$query_group_netgroups = "SELECT networkgroup.*, usrgroupnetgrouppermissions.level, usrgroupnetgrouppermissions.id as pid, container.name as containername FROM `networkgroup` LEFT JOIN usrgroupnetgrouppermissions ON usrgroupnetgrouppermissions.networkgroup = networkgroup.id LEFT JOIN usrgroup ON usrgroup.id = usrgroupnetgrouppermissions.usrgroup LEFT JOIN container ON container.id = networkgroup.container WHERE usrgroup.id = '".$_GET['group']."' ORDER BY networkgroup.name";
$group_netgroups = mysql_query($query_group_netgroups, $subman) or die(mysql_error());
$row_group_netgroups = mysql_fetch_assoc($group_netgroups);
$totalRows_group_netgroups = mysql_num_rows($group_netgroups);

mysql_select_db($database_subman, $subman);
$query_networkgroups = "SELECT networkgroup.*, container.name as containername FROM networkgroup LEFT JOIN container ON container.id = networkgroup.container ORDER BY container.name, networkgroup.name";
$networkgroups = mysql_query($query_networkgroups, $subman) or die(mysql_error());
$row_networkgroups = mysql_fetch_assoc($networkgroups);
$totalRows_networkgroups = mysql_num_rows($networkgroups);
			
?>
<form action="" method="post" target="_self" name="frm_delete_group_netgroups" id="frm_delete_group_netgroups">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_netgroups; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_netgroups">
    
    <table width="50%" border="0">
  <tr>
    <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_netgroups'), this.checked);" /></td>
    <td><strong>Network Group</strong></td>
    <td><strong>Permissions</strong></td>
  </tr>
  <?php $count = 0;
		do { 
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
  <?php if ($totalRows_group_netgroups > 0) { // Show if recordset not empty ?>
  <tr bgcolor="<?php echo $bgcolour; ?>">
    <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_netgroups['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_netgroups['pid']; ?>"></td>
    <td><a title="[<?php echo $row_group_netgroups['containername']; ?>] <?php echo $row_group_netgroups['name']; ?>"><?php echo $row_group_netgroups['name']; ?></a></td>
    <td><?php echo getPermission($row_group_netgroups['level']); ?></td>
  </tr>
  <?php } // Show if recordset not empty 
  	else { ?>
    	<tr>
    	  <td>&nbsp;</td>
   	    <td><p>There are no network group permissions for this user group.</p></td></tr>
  <?php } ?>
  <?php } while ($row_group_netgroups = mysql_fetch_assoc($group_netgroups)); ?>
</table>
</form>
    <form action="<?php echo $editFormAction; ?>" method="post" name="form3" id="form3">
  <table >
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Network Group:</td>
      <td><select name="networkgroup" class="input_standard">
        <?php 
do {  
?>
        <option value="<?php echo $row_networkgroups['id']?>" >[<?php echo $row_networkgroups['containername']; ?>] <?php echo $row_networkgroups['name']?></option>
        <?php
} while ($row_networkgroups = mysql_fetch_assoc($networkgroups));
?>
      </select></td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Permissions:</td>
      <td><select name="level" class="input_standard">
        <option value="0" <?php if (!(strcmp(0, ""))) {echo "SELECTED";} ?>>None</option>
        <option value="10" <?php if (!(strcmp(10, ""))) {echo "SELECTED";} ?>>Read Only</option>
        <option value="20" <?php if (!(strcmp(20, ""))) {echo "SELECTED";} ?>>Read, Modify</option>
        <option value="30" <?php if (!(strcmp(30, ""))) {echo "SELECTED";} ?>>Read, Modify, Delete</option>
        <option value="127" <?php if (!(strcmp(127, ""))) {echo "SELECTED";} ?>>Superuser</option>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" class="input_standard" value="Set permissions" /></td>
    </tr>
  </table>
  <input type="hidden" name="usrgroup" value="<?php echo $_GET['group']; ?>" />
  <input type="hidden" name="MM_insert" value="form3" />
</form>
<?php } ?>
<?php if ($_GET['section'] == "networks") { 

	mysql_select_db($database_subman, $subman);
	$query_group_networks = "SELECT networks.*, usrgroupnetworkpermissions.level, usrgroupnetworkpermissions.id as pid, container.name as containername FROM `networks` LEFT JOIN usrgroupnetworkpermissions ON usrgroupnetworkpermissions.network = networks.id LEFT JOIN usrgroup ON usrgroup.id = usrgroupnetworkpermissions.usrgroup LEFT JOIN container ON container.id = networks.container WHERE usrgroup.id = '".$_GET['group']."' ORDER BY container.name, networks.network";
	$group_networks = mysql_query($query_group_networks, $subman) or die(mysql_error());
	$row_group_networks = mysql_fetch_assoc($group_networks);
	$totalRows_group_networks = mysql_num_rows($group_networks);

	mysql_select_db($database_subman, $subman);
	$query_networks = "SELECT networks.*, container.name as containername FROM networks LEFT JOIN container ON container.id = networks.container ORDER BY container.name, networks.network";
	$networks = mysql_query($query_networks, $subman) or die(mysql_error());
	$row_networks = mysql_fetch_assoc($networks);
	$totalRows_networks = mysql_num_rows($networks);

?>
<form action="" method="post" target="_self" name="frm_delete_group_networks" id="frm_delete_group_networks">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_networks; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_networks">
<table width="50%" border="0">
  <tr>
    <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_networks'), this.checked);" /></td>
    <td><strong>Network</strong></td>
    <td><strong>Mask</strong></td>
    <td><strong>Permissions</strong></td>
  </tr>
  <?php $count = 0;
		do { 
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
  <?php if ($totalRows_group_networks > 0) { // Show if recordset not empty ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_networks['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_networks['pid']; ?>"></td>
      <td><a title="[<?php echo $row_group_networks['containername']; ?>] <?php if ($row_group_networks['v6mask'] == "") { echo long2ip($row_group_networks['network']); } else { echo Net_IPv6::compress(long2ipv6($row_group_networks['network'])); } ?><?php if ($row_group_networks['v6mask'] == "") { echo get_slash(long2ip($row_group_networks['maskLong'])); } else { echo "/".$row_group_networks['v6mask']; } ?> : <?php echo $row_group_networks['descr']; ?>"><?php if ($row_group_networks['v6mask'] == "") { echo long2ip($row_group_networks['network']); } else { echo Net_IPv6::compress(long2ipv6($row_group_networks['network'])); } ?></a></td>
      <td><?php if ($row_group_networks['v6mask'] == "") { echo get_slash(long2ip($row_group_networks['maskLong'])); } else { echo "/".$row_group_networks['v6mask']; } ?></td>
      <td><?php echo getPermission($row_group_networks['level']); ?></td>
    </tr>
<?php } // Show if recordset not empty 
		else { ?>
        	<tr>
        	  <td>&nbsp;</td>
       	    <td><p>There are no network permissions for this user group.</p></td></tr>
    <?php } ?>
<?php } while ($row_group_networks = mysql_fetch_assoc($group_networks)); ?>
</table>
</form>
<form action="<?php echo $editFormAction; ?>" method="post" name="form4" id="form4">
  <table >
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Network:</td>
      <td><select name="network" class="input_standard">
        <?php 
do {  
?>

        <option value="<?php echo $row_networks['id']?>" >[<?php echo $row_networks['containername']; ?>] <?php if ($row_networks['v6mask'] == "") { echo long2ip($row_networks['network']); } else { echo Net_IPv6::compress(long2ipv6($row_networks['network'])); } ?><?php if ($row_networks['v6mask'] == "") { echo get_slash(long2ip($row_networks['maskLong'])); } else { echo "/".$row_networks['v6mask']; } ?> : <?php if (strlen($row_networks['descr']) < 16) { echo $row_networks['descr']; } else { echo substr_replace($row_networks['descr'],'...',16); } ?></option>
        <?php
} while ($row_networks = mysql_fetch_assoc($networks));
?>
      </select></td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Permissions:</td>
      <td><select name="level" class="input_standard">
        <option value="0" <?php if (!(strcmp(0, ""))) {echo "SELECTED";} ?>>None</option>
        <option value="10" <?php if (!(strcmp(10, ""))) {echo "SELECTED";} ?>>Read Only</option>
        <option value="20" <?php if (!(strcmp(20, ""))) {echo "SELECTED";} ?>>Read, Modify</option>
        <option value="30" <?php if (!(strcmp(30, ""))) {echo "SELECTED";} ?>>Read, Modify, Delete</option>
        <option value="127" <?php if (!(strcmp(127, ""))) {echo "SELECTED";} ?>>Superuser</option>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" class="input_standard" value="Set permissions" /></td>
    </tr>
  </table>
  <input type="hidden" name="usrgroup" value="<?php echo $_GET['group']; ?>" />
  <input type="hidden" name="MM_insert" value="form4" />
</form>

<?php } ?>

<?php if ($_GET['section'] == "devicegroups") { 

	mysql_select_db($database_subman, $subman);
$query_group_device_groups = "SELECT portgroups.*, usrgroupdevicegrouppermissions.level, usrgroupdevicegrouppermissions.id as pid, container.name as containername FROM `portgroups` LEFT JOIN usrgroupdevicegrouppermissions ON usrgroupdevicegrouppermissions.devicegroup = portgroups.id LEFT JOIN usrgroup ON usrgroup.id = usrgroupdevicegrouppermissions.usrgroup LEFT JOIN container ON container.id = portgroups.container WHERE usrgroup.id = '".$_GET['group']."' ORDER BY portgroups.name";
$group_device_groups = mysql_query($query_group_device_groups, $subman) or die(mysql_error());
$row_group_device_groups = mysql_fetch_assoc($group_device_groups);
$totalRows_group_device_groups = mysql_num_rows($group_device_groups);

	mysql_select_db($database_subman, $subman);
	$query_device_groups = "SELECT portgroups.*, container.name as containername FROM portgroups LEFT JOIN container ON container.id = portgroups.container ORDER BY container.name, portgroups.name";
	$device_groups = mysql_query($query_device_groups, $subman) or die(mysql_error());
	$row_device_groups = mysql_fetch_assoc($device_groups);
	$totalRows_device_groups = mysql_num_rows($device_groups);

?>

<form action="" method="post" target="_self" name="frm_delete_group_device_groups" id="frm_delete_group_device_groups">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_device_groups; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_device_groups">
<table width="50%" border="0">
    <tr>
      <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_device_groups'), this.checked);" /></td>
        <td><strong>Device Group </strong></td>
        <td><strong>Permissions</strong></td>
      </tr>
    <?php $count = 0;
		do { 
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
<?php if ($totalRows_group_device_groups > 0) { // Show if recordset not empty ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_device_groups['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_device_groups['pid']; ?>"></td>
      <td><a title="[<?php echo $row_group_device_groups['containername']; ?>] <?php echo $row_group_device_groups['name']; ?>"><?php echo $row_group_device_groups['name']; ?></a></td>
      <td><?php echo getPermission($row_group_device_groups['level']); ?></td>
    </tr>
	<?php } // Show if recordset not empty 
	else { ?>
		<tr>
    	  <td>&nbsp;</td>
   	    <td><p>There are no device group permissions for this user group.</p></td></tr>
<?php } ?>

    <?php } while ($row_group_device_groups = mysql_fetch_assoc($group_device_groups)); ?>
</table>
</form>

    <form method="post" name="form7" action="<?php echo $editFormAction; ?>">
      <table >
        <tr valign="baseline">
          <td nowrap align="right">Device Group:</td>
          <td>
            <select name="devicegroup" class="input_standard">
              <?php 
do {  
?>
              <option value="<?php echo $row_device_groups['id']?>" >[<?php echo $row_device_groups['containername']; ?>] <?php echo $row_device_groups['name']?></option>
              <?php
} while ($row_device_groups = mysql_fetch_assoc($device_groups));
?>
            </select>
          </td>
        <tr>
        <tr valign="baseline">
          <td nowrap align="right">Permissions:</td>
          <td>
            <select name="level" class="input_standard">
              <option value="0" >None</option>
              <option value="10" >Read Only</option>
              <option value="20" >Read, Modify</option>
              <option value="30" >Read, Modify, Delete</option>
              <option value="127" >Superuser</option>
            </select>
          </td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Set permissions"></td>
        </tr>
      </table>
      <input type="hidden" name="usrgroup" value="<?php echo $_GET['group']; ?>">
      <input type="hidden" name="MM_insert" value="form7">
    </form>
    <p>&nbsp;</p>
    <?php } ?>
	
	<?php if ($_GET['section'] == "devices") { 
		
		mysql_select_db($database_subman, $subman);
		$query_group_devices = "SELECT portsdevices.*, usrgroupdevicepermissions.level, usrgroupdevicepermissions.id as pid, portgroups.name as devicegroupname, container.name as containername FROM portsdevices LEFT JOIN usrgroupdevicepermissions ON usrgroupdevicepermissions.device = portsdevices.id LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup LEFT JOIN container ON container.id = portgroups.container WHERE usrgroupdevicepermissions.usrgroup = ".$_GET['group']." ORDER BY container.name, portsdevices.name";
		$group_devices = mysql_query($query_group_devices, $subman) or die(mysql_error());
		$row_group_devices = mysql_fetch_assoc($group_devices);
		$totalRows_group_devices = mysql_num_rows($group_devices);
		
		mysql_select_db($database_subman, $subman);
		$query_devices = "SELECT portsdevices.*, portgroups.name as devicegroupname, container.name as containername FROM portsdevices LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup LEFT JOIN container ON container.id = portgroups.container ORDER BY container.name, portgroups.name, portsdevices.name";
		$devices = mysql_query($query_devices, $subman) or die(mysql_error());
		$row_devices = mysql_fetch_assoc($devices);
		$totalRows_devices = mysql_num_rows($devices);
		
	?>
	
	<form action="" method="post" target="_self" name="frm_delete_group_devices" id="frm_delete_group_devices">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_devices; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_devices">
    <table width="50%" border="0">
      <tr>
        <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_devices'), this.checked);" /></td>
        <td><strong>Device</strong></td>
        <td><strong>Permissions</strong></td>
      </tr>
          <?php $count = 0;
		do { 
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
      <?php if ($totalRows_group_devices > 0) { // Show if recordset not empty ?>
      <tr bgcolor="<?php echo $bgcolour; ?>">
        <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_devices['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_devices['pid']; ?>"></td>
        <td><a title="[<?php echo $row_group_devices['containername']; ?>] <?php echo $row_group_devices['devicegroupname']; ?> : <?php echo $row_group_devices['name']; ?>"><?php echo $row_group_devices['name']; ?></a></td>
        <td><?php echo getPermission($row_group_devices['level']); ?></td>
      </tr>
      <?php } // Show if recordset not empty 
	  else { ?>
		<tr>
    	  <td>&nbsp;</td>
   	    <td><p>There are no device permissions for this user group.</p></td></tr>
		<?php } ?>
      <?php } while ($row_group_devices = mysql_fetch_assoc($group_devices)); ?>
    </table>
	</form>
	
	    <form method="post" name="form8" action="<?php echo $editFormAction; ?>">
      <table >
        <tr valign="baseline">
          <td nowrap align="right">Device:</td>
          <td>
            <select name="device" class="input_standard">
              <?php 
do {  
?>
              <option value="<?php echo $row_devices['id']?>" >[<?php echo $row_devices['containername']; ?>] <?php echo $row_devices['devicegroupname']; ?> : <?php echo $row_devices['name']?></option>
              <?php
} while ($row_devices = mysql_fetch_assoc($devices));
?>
            </select>
          </td>
        <tr>
        <tr valign="baseline">
          <td nowrap align="right">Permissions:</td>
          <td>
            <select name="level" class="input_standard">
              <option value="0" >None</option>
              <option value="10" >Read Only</option>
              <option value="20" >Read, Modify</option>
              <option value="30" >Read, Modify, Delete</option>
              <option value="127" >Superuser</option>
            </select>
          </td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Set permissions"></td>
        </tr>
      </table>
      <input type="hidden" name="usrgroup" value="<?php echo $_GET['group']; ?>">
      <input type="hidden" name="MM_insert" value="form8">
    </form>
    <p>&nbsp;</p>
    <?php } ?>
	<?php if ($_GET['section'] == "providers") { 
	
		mysql_select_db($database_subman, $subman);
		$query_group_providers = "SELECT provider.*, usrgroupproviderpermissions.level, usrgroupproviderpermissions.id as pid, container.name as containername FROM provider LEFT JOIN usrgroupproviderpermissions ON usrgroupproviderpermissions.provider = provider.id LEFT JOIN container ON container.id = provider.container WHERE usrgroupproviderpermissions.usrgroup = ".$_GET['group']." ORDER BY container.name, provider.name";
		$group_providers = mysql_query($query_group_providers, $subman) or die(mysql_error());
		$row_group_providers = mysql_fetch_assoc($group_providers);
		$totalRows_group_providers = mysql_num_rows($group_providers);

		mysql_select_db($database_subman, $subman);
		$query_providers = "SELECT provider.*, container.name as containername FROM provider LEFT JOIN container ON container.id = provider.container ORDER BY container.name, provider.name";
		$providers = mysql_query($query_providers, $subman) or die(mysql_error());
		$row_providers = mysql_fetch_assoc($providers);
		$totalRows_providers = mysql_num_rows($providers);

		?>

	<form action="" method="post" target="_self" name="frm_delete_group_providers" id="frm_delete_group_providers">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_providers; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_providers">		
    <table width="50%" border="0">
      <tr>
        <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_providers'), this.checked);" /></td>
        <td><strong>Provider</strong></td>
        <td><strong>AS Number </strong></td>
        <td><strong>Permissions</strong></td>
      </tr>
      <?php $count = 0;
		do { 
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
      <?php if ($totalRows_group_providers > 0) { // Show if recordset not empty ?>
      <tr bgcolor="<?php echo $bgcolour; ?>">
        <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_providers['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_providers['pid']; ?>"></td>
        <td><a title="[<?php echo $row_group_providers['containername']; ?>] <?php echo $row_group_providers['name']; ?>"><?php echo $row_group_providers['name']; ?></a></td>
        <td><?php echo $row_group_providers['asnumber']; ?></td>
        <td><?php echo getPermission($row_group_providers['level']); ?></td>
      </tr>
      <?php } // Show if recordset not empty 
	  		else { ?>
		<tr>
		  <td>&nbsp;</td>
   	    <td><p>There are no VPN provider permissions for this user group.</p></td></tr>
		<?php } ?>
      <?php } while ($row_group_providers = mysql_fetch_assoc($group_providers)); ?>
    </table>
	</form>
	
	<form method="post" name="form9" action="<?php echo $editFormAction; ?>">
      <table >
        <tr valign="baseline">
          <td nowrap align="right">Provider:</td>
          <td>
            <select name="provider" class="input_standard">
              <?php 
do {  
?>
              <option value="<?php echo $row_providers['id']?>" >[<?php echo $row_providers['containername']; ?>] <?php echo $row_providers['asnumber']; ?> <?php echo $row_providers['name']?></option>
              <?php
} while ($row_providers = mysql_fetch_assoc($providers));
?>
            </select>
          </td>
        <tr>
        <tr valign="baseline">
          <td nowrap align="right">Permissions:</td>
          <td>
            <select name="level" class="input_standard">
              <option value="0" >None</option>
              <option value="10" >Read Only</option>
              <option value="20" >Read, Modify</option>
              <option value="30" >Read, Modify, Delete</option>
              <option value="127" >Superuser</option>
            </select>
          </td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Set permissions"></td>
        </tr>
      </table>
      <input type="hidden" name="usrgroup" value="<?php echo $_GET['group']; ?>">
      <input type="hidden" name="MM_insert" value="form9">
    </form>
    <p>&nbsp;</p>
	
    <?php } ?>
	
	<?php if ($_GET['section'] == "vpns") { 
		
		mysql_select_db($database_subman, $subman);
		$query_group_vpns = "SELECT vpn.*, usrgroupvpnpermissions.level, usrgroupvpnpermissions.id as pid, container.name as containername, provider.name as providername FROM vpn LEFT JOIN usrgroupvpnpermissions ON usrgroupvpnpermissions.vpn = vpn.id LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN container ON container.id = provider.container WHERE usrgroupvpnpermissions.usrgroup = ".$_GET['group']." ORDER BY vpn.name";
		$group_vpns = mysql_query($query_group_vpns, $subman) or die(mysql_error());
		$row_group_vpns = mysql_fetch_assoc($group_vpns);
		$totalRows_group_vpns = mysql_num_rows($group_vpns);

		mysql_select_db($database_subman, $subman);
		$query_vpns = "SELECT vpn.*, container.name as containername, provider.name as providername FROM vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN container ON container.id = provider.container ORDER BY container.name, provider.name, vpn.name";
		$vpns = mysql_query($query_vpns, $subman) or die(mysql_error());
		$row_vpns = mysql_fetch_assoc($vpns);
		$totalRows_vpns = mysql_num_rows($vpns);
		
		?>
	
	<form action="" method="post" target="_self" name="frm_delete_group_vpns" id="frm_delete_group_vpns">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_vpns; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_vpns">		
    <table width="50%" border="0">
      <tr>
        <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_vpns'), this.checked);" /></td>
        <td><strong>VPN</strong></td>
        <td><strong>Permissions</strong></td>
      </tr>
      <?php $count = 0;
		do { 
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
      <?php if ($totalRows_group_vpns > 0) { // Show if recordset not empty ?>
      <tr bgcolor="<?php echo $bgcolour; ?>">
        <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_vpns['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_vpns['pid']; ?>"></td>
        <td><a title="[<?php echo $row_group_vpns['containername']; ?>] <?php echo $row_group_vpns['providername']; ?> : <?php echo $row_group_vpns['name']; ?>"><?php echo $row_group_vpns['name']; ?></a></td>
        <td><?php echo getPermission($row_group_vpns['level']); ?></td>
      </tr>
      <?php } // Show if recordset not empty 
	  		else { ?>
		<tr>
		  <td>&nbsp;</td>
   	    <td><p>There are no VPN permissions for this user group.</p></td></tr>
		<?php } ?>
      <?php } while ($row_group_vpns = mysql_fetch_assoc($group_vpns)); ?>
    </table>
	</form>
	
	<form method="post" name="form10" action="<?php echo $editFormAction; ?>">
      <table >
        <tr valign="baseline">
          <td nowrap align="right">VPN:</td>
          <td>
            <select name="vpn" class="input_standard">
              <?php 
do {  
?>
              <option value="<?php echo $row_vpns['id']?>" >[<?php echo $row_vpns['containername']; ?>] <?php echo $row_vpns['providername']; ?> : <?php echo $row_vpns['name']?></option>
              <?php
} while ($row_vpns = mysql_fetch_assoc($vpns));
?>
            </select>
          </td>
        <tr>
        <tr valign="baseline">
          <td nowrap align="right">Permissions:</td>
          <td>
            <select name="level" class="input_standard">
              <option value="0" >None</option>
              <option value="10" >Read Only</option>
              <option value="20" >Read, Modify</option>
              <option value="30" >Read, Modify, Delete</option>
              <option value="127" >Superuser</option>
            </select>
          </td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Set permissions"></td>
        </tr>
      </table>
      <input type="hidden" name="usrgroup" value="<?php echo $_GET['group']; ?>">
      <input type="hidden" name="MM_insert" value="form10">
    </form>
    <p>&nbsp;</p>	
	
	<?php } ?>
    
    <?php if ($_GET['section'] == "pages") { 
			
			mysql_select_db($database_subman, $subman);
			$query_group_pages = "SELECT module.*, usrgrouppermissions.level, usrgrouppermissions.id as pid FROM `module` LEFT JOIN usrgrouppermissions ON usrgrouppermissions.module = module.id LEFT JOIN usrgroup ON usrgroup.id = usrgrouppermissions.usrgroup WHERE usrgroup.id = '".$_GET['group']."' AND module.adminonly = 0";
			$group_pages = mysql_query($query_group_pages, $subman) or die(mysql_error());
			$row_group_pages = mysql_fetch_assoc($group_pages);
			$totalRows_group_pages = mysql_num_rows($group_pages);
			
			mysql_select_db($database_subman, $subman);
			$query_pages = "SELECT * FROM `module` WHERE module.adminonly = 0 ORDER BY `module`.name";
			$pages = mysql_query($query_pages, $subman) or die(mysql_error());
			$row_pages = mysql_fetch_assoc($pages);
			$totalRows_pages = mysql_num_rows($pages);
	
		?>
    
    	<form action="" method="post" target="_self" name="frm_delete_group_pages" id="frm_delete_group_pages">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_pages; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_pages">		
    <table width="50%" border="0">
      <tr>
        <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_pages'), this.checked);" /></td>
        <td><strong>Page</strong></td>
        <td><strong>Permissions</strong></td>
      </tr>
      <?php $count = 0;
		do { 
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
      <?php if ($totalRows_group_pages > 0) { // Show if recordset not empty ?>
      <tr bgcolor="<?php echo $bgcolour; ?>">
        <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_pages['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_pages['pid']; ?>"></td>
        <td><a title="<?php echo $row_group_pages['descr']; ?>"><?php echo $row_group_pages['name']; ?></a></td>
        <td><?php echo getPermission($row_group_pages['level']); ?></td>
      </tr>
      <?php } // Show if recordset not empty 
	  		else { ?>
		<tr>
		  <td>&nbsp;</td>
   	    <td><p>There are no page permissions for this user group.</p></td></tr>
		<?php } ?>
      <?php } while ($row_group_pages = mysql_fetch_assoc($group_pages)); ?>
    </table>
	</form>
	
	<form method="post" name="form11" action="<?php echo $editFormAction; ?>">
      <table >
        <tr valign="baseline">
          <td nowrap align="right">Page:</td>
          <td>
            <select name="module" class="input_standard">
              <?php 
do {  
?>
              <option value="<?php echo $row_pages['id']?>" ><?php echo $row_pages['name']?></option>
              <?php
} while ($row_pages = mysql_fetch_assoc($pages));
?>
            </select>
          </td>
        <tr>
        <tr valign="baseline">
          <td nowrap align="right">Permissions:</td>
          <td>
            <select name="level" class="input_standard">
              <option value="0" >None</option>
              <option value="10" >Read Only</option>
              <option value="20" >Read, Modify</option>
              <option value="30" >Read, Modify, Delete</option>
              <option value="127" >Superuser</option>
            </select>
          </td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Set permissions"></td>
        </tr>
      </table>
      <input type="hidden" name="usrgroup" value="<?php echo $_GET['group']; ?>">
      <input type="hidden" name="MM_insert" value="form11">
    </form>
    <p>&nbsp;</p>
    
    <?php } ?>
    
    
    <?php if ($_GET['section'] == "radiators") { 
			
			mysql_select_db($database_subman, $subman);
			$query_group_radiators = "SELECT radiator.*, usrgroupradiatorpermissions.level, usrgroupradiatorpermissions.id as pid FROM `radiator` LEFT JOIN usrgroupradiatorpermissions ON usrgroupradiatorpermissions.radiator = radiator.id LEFT JOIN usrgroup ON usrgroup.id = usrgroupradiatorpermissions.usrgroup WHERE usrgroup.id = '".$_GET['group']."'";
			$group_radiators = mysql_query($query_group_radiators, $subman) or die(mysql_error());
			$row_group_radiators = mysql_fetch_assoc($group_radiators);
			$totalRows_group_radiators = mysql_num_rows($group_radiators);
			
			mysql_select_db($database_subman, $subman);
			$query_radiators = "SELECT * FROM `radiator` ORDER BY `radiator`.host";
			$radiators = mysql_query($query_radiators, $subman) or die(mysql_error());
			$row_radiators = mysql_fetch_assoc($radiators);
			$totalRows_radiators = mysql_num_rows($radiators);
	
		?>
    
    	<form action="" method="post" target="_self" name="frm_delete_group_radiators" id="frm_delete_group_radiators">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_radiators; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_radiators">		
    <table width="50%" border="0">
      <tr>
        <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_radiators'), this.checked);" /></td>
        <td><strong>Host</strong></td>
        <td><strong>Permissions</strong></td>
      </tr>
      <?php $count = 0;
		do { 
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
      <?php if ($totalRows_group_radiators > 0) { // Show if recordset not empty ?>
      <tr bgcolor="<?php echo $bgcolour; ?>">
        <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_radiators['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_radiators['pid']; ?>"></td>
        <td><a title="<?php echo $row_group_radiators['descr']; ?>"><?php echo $row_group_radiators['host']; ?></a></td>
        <td><?php echo getPermission($row_group_radiators['level']); ?></td>
      </tr>
      <?php } // Show if recordset not empty 
	  		else { ?>
		<tr>
		  <td>&nbsp;</td>
   	    <td><p>There are no Radiator permissions for this user group.</p></td></tr>
		<?php } ?>
      <?php } while ($row_group_radiators = mysql_fetch_assoc($group_radiators)); ?>
    </table>
	</form>
	
	<form method="post" name="form12" action="<?php echo $editFormAction; ?>">
      <table >
        <tr valign="baseline">
          <td nowrap align="right">Radiator:</td>
          <td>
            <select name="radiator" class="input_standard">
              <?php 
do {  
?>
              <option value="<?php echo $row_radiators['id']?>" ><?php echo $row_radiators['host']?></option>
              <?php
} while ($row_radiators = mysql_fetch_assoc($radiators));
?>
            </select>
          </td>
        <tr>
        <tr valign="baseline">
          <td nowrap align="right">Permissions:</td>
          <td>
            <select name="level" class="input_standard">
              <option value="0" >None</option>
              <option value="10" >Read Only</option>
              <option value="20" >Read, Modify</option>
              <option value="30" >Read, Modify, Delete</option>
              <option value="127" >Superuser</option>
            </select>
          </td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Set permissions"></td>
        </tr>
      </table>
      <input type="hidden" name="usrgroup" value="<?php echo $_GET['group']; ?>">
      <input type="hidden" name="MM_insert" value="form12">
    </form>
    <p>&nbsp;</p>
    
    <?php } ?>
    
    <?php if ($_GET['section'] == "damit") { 
			
			mysql_select_db($database_subman, $subman);
			$query_group_damits = "SELECT damit.*, usrgroupdamitpermissions.level, usrgroupdamitpermissions.id as pid FROM `damit` LEFT JOIN usrgroupdamitpermissions ON usrgroupdamitpermissions.damit = damit.id LEFT JOIN usrgroup ON usrgroup.id = usrgroupdamitpermissions.usrgroup WHERE usrgroup.id = '".$_GET['group']."'";
			$group_damits = mysql_query($query_group_damits, $subman) or die(mysql_error());
			$row_group_damits = mysql_fetch_assoc($group_damits);
			$totalRows_group_damits = mysql_num_rows($group_damits);
			
			mysql_select_db($database_subman, $subman);
			$query_damits = "SELECT * FROM `damit` ORDER BY `damit`.host";
			$damits = mysql_query($query_damits, $subman) or die(mysql_error());
			$row_damits = mysql_fetch_assoc($damits);
			$totalRows_damits = mysql_num_rows($damits);
	
		?>
    
    	<form action="" method="post" target="_self" name="frm_delete_group_damits" id="frm_delete_group_damits">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_damits; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_damits">		
    <table width="50%" border="0">
      <tr>
        <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_damits'), this.checked);" /></td>
        <td><strong>Host</strong></td>
        <td><strong>Permissions</strong></td>
      </tr>
      <?php $count = 0;
		do { 
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
      <?php if ($totalRows_group_damits > 0) { // Show if recordset not empty ?>
      <tr bgcolor="<?php echo $bgcolour; ?>">
        <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_damits['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_damits['pid']; ?>"></td>
        <td><a title="<?php echo $row_group_damits['descr']; ?>"><?php echo $row_group_damits['host']; ?></a></td>
        <td><?php echo getPermission($row_group_damits['level']); ?></td>
      </tr>
      <?php } // Show if recordset not empty 
	  		else { ?>
		<tr>
		  <td>&nbsp;</td>
   	    <td><p>There are no DAM-it permissions for this user group.</p></td></tr>
		<?php } ?>
      <?php } while ($row_group_damits = mysql_fetch_assoc($group_damits)); ?>
    </table>
	</form>
	
	<form method="post" name="form13" action="<?php echo $editFormAction; ?>">
      <table >
        <tr valign="baseline">
          <td nowrap align="right">DAM-it:</td>
          <td>
            <select name="damit" class="input_standard">
              <?php 
do {  
?>
              <option value="<?php echo $row_damits['id']?>" ><?php echo $row_damits['host']?></option>
              <?php
} while ($row_damits = mysql_fetch_assoc($damits));
?>
            </select>
          </td>
        <tr>
        <tr valign="baseline">
          <td nowrap align="right">Permissions:</td>
          <td>
            <select name="level" class="input_standard">
              <option value="0" >None</option>
              <option value="10" >Read Only</option>
              <option value="20" >Read, Modify</option>
              <option value="30" >Read, Modify, Delete</option>
              <option value="127" >Superuser</option>
            </select>
          </td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Set permissions"></td>
        </tr>
      </table>
      <input type="hidden" name="usrgroup" value="<?php echo $_GET['group']; ?>">
      <input type="hidden" name="MM_insert" value="form13">
    </form>
    <p>&nbsp;</p>
    
    <?php } ?>
    
    <?php if ($_GET['section'] == "observium") { 
			
			mysql_select_db($database_subman, $subman);
			$query_group_observiums = "SELECT observium.*, usrgroupobserviumpermissions.level, usrgroupobserviumpermissions.id as pid FROM `observium` LEFT JOIN usrgroupobserviumpermissions ON usrgroupobserviumpermissions.observium = observium.id LEFT JOIN usrgroup ON usrgroup.id = usrgroupobserviumpermissions.usrgroup WHERE usrgroup.id = '".$_GET['group']."'";
			$group_observiums = mysql_query($query_group_observiums, $subman) or die(mysql_error());
			$row_group_observiums = mysql_fetch_assoc($group_observiums);
			$totalRows_group_observiums = mysql_num_rows($group_observiums);
			
			mysql_select_db($database_subman, $subman);
			$query_observiums = "SELECT * FROM `observium` ORDER BY `observium`.host";
			$observiums = mysql_query($query_observiums, $subman) or die(mysql_error());
			$row_observiums = mysql_fetch_assoc($observiums);
			$totalRows_observiums = mysql_num_rows($observiums);
	
		?>
    
    	<form action="" method="post" target="_self" name="frm_delete_group_observiums" id="frm_delete_group_observiums">
	<input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_group_observiums; ?>">
	<input type="hidden" name="permission_action_type" value="delete_group_observiums">		
    <table width="50%" border="0">
      <tr>
        <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_group_observiums'), this.checked);" /></td>
        <td><strong>Host</strong></td>
        <td><strong>Permissions</strong></td>
      </tr>
      <?php $count = 0;
		do { 
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
      <?php if ($totalRows_group_observiums > 0) { // Show if recordset not empty ?>
      <tr bgcolor="<?php echo $bgcolour; ?>">
        <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_group_observiums['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_group_observiums['pid']; ?>"></td>
        <td><a title="<?php echo $row_group_observiums['descr']; ?>"><?php echo $row_group_observiums['host']; ?></a></td>
        <td><?php echo getPermission($row_group_observiums['level']); ?></td>
      </tr>
      <?php } // Show if recordset not empty 
	  		else { ?>
		<tr>
		  <td>&nbsp;</td>
   	    <td><p>There are no Observium permissions for this user group.</p></td></tr>
		<?php } ?>
      <?php } while ($row_group_observiums = mysql_fetch_assoc($group_observiums)); ?>
    </table>
	</form>
	
	<form method="post" name="frm_add_observium" action="<?php echo $editFormAction; ?>">
      <table >
        <tr valign="baseline">
          <td nowrap align="right">Observium:</td>
          <td>
            <select name="observium" class="input_standard">
              <?php 
do {  
?>
              <option value="<?php echo $row_observiums['id']?>" ><?php echo $row_observiums['host']?></option>
              <?php
} while ($row_observiums = mysql_fetch_assoc($observiums));
?>
            </select>
          </td>
        <tr>
        <tr valign="baseline">
          <td nowrap align="right">Permissions:</td>
          <td>
            <select name="level" class="input_standard">
              <option value="0" >None</option>
              <option value="10" >Read Only</option>
              <option value="20" >Read, Modify</option>
              <option value="30" >Read, Modify, Delete</option>
              <option value="127" >Superuser</option>
            </select>
          </td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Set permissions"></td>
        </tr>
      </table>
      <input type="hidden" name="usrgroup" value="<?php echo $_GET['group']; ?>">
      <input type="hidden" name="MM_insert" value="frm_add_observium">
    </form>
    <p>&nbsp;</p>
    
    <?php } ?>
  <?php
}
elseif ($_POST['action'] != 'add_group') { ?>

	<p><a href="#" onClick="document.getElementById('action').value = 'add_group'; document.getElementById('frm_usergroup_action').submit(); return false;">Add a user group</a></p>

<?php }
}
elseif ($_GET['browse'] == 'users') { 

	if ($_POST['action'] == "add_user") { 
	
	mysql_select_db($database_subman, $subman);
	$query_usergroups = "SELECT * FROM usrgroup ORDER BY usrgroup.name";
	$usergroups = mysql_query($query_usergroups, $subman) or die(mysql_error());
	$row_usergroups = mysql_fetch_assoc($usergroups);
	$totalRows_usergroups = mysql_num_rows($usergroups);
	?>
  <form action="<?php echo $editFormAction; ?>" method="post" name="form6" id="form6" onSubmit="MM_validateForm('firstname','','R','lastname','','R','username','','R','password','','R','email','','RisEmail');return document.MM_returnValue">
    <table >
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">First Name:</td>
        <td><input name="firstname" type="text" class="input_standard" id="firstname" value="" size="32" maxlength="255" /></td>
      </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Last Name:</td>
        <td><input name="lastname" type="text" class="input_standard" id="lastname" value="" size="32" maxlength="255" /></td>
      </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Username:</td>
        <td><input name="username" type="text" class="input_standard" id="username" value="" size="20" maxlength="255" /></td>
      </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Password:</td>
        <td><input name="password" type="password" class="input_standard" id="password" value="" size="20" maxlength="255" /></td>
      </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Usrgroup:</td>
        <td><select name="usrgroup" class="input_standard">
          <?php 
do {  
?>
          <option value="<?php echo $row_usergroups['id']?>" ><?php echo $row_usergroups['name']?></option>
          <?php
} while ($row_usergroups = mysql_fetch_assoc($usergroups));
?>
        </select></td>
      </tr>
      <tr> </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">Email:</td>
        <td><input name="email" type="text" class="input_standard" id="email" value="" size="32" maxlength="255" /></td>
      </tr>
      <tr valign="baseline">
        <td nowrap="nowrap" align="right">&nbsp;</td>
        <td><input type="submit" class="input_standard" value="Add user" /></td>
      </tr>
    </table>
    <input type="hidden" name="MM_insert" value="form6" />
  </form>

<?php
	}
	elseif ($_GET['user'] != "") {
		
	mysql_select_db($database_subman, $subman);
	$query_getUser = "SELECT * FROM `user` WHERE `user`.id = ".$_GET['user']."";
	$getUser = mysql_query($query_getUser, $subman) or die(mysql_error());
	$row_getUser = mysql_fetch_assoc($getUser);
	$totalRows_getUser = mysql_num_rows($getUser);
	
	mysql_select_db($database_subman, $subman);
	$query_usergroups = "SELECT * FROM usrgroup ORDER BY usrgroup.name";
	$usergroups = mysql_query($query_usergroups, $subman) or die(mysql_error());
	$row_usergroups = mysql_fetch_assoc($usergroups);
	$totalRows_usergroups = mysql_num_rows($usergroups);

?>
<form action="<?php echo $editFormAction; ?>" method="post" name="form5" id="form5" onSubmit="MM_validateForm('firstname','','R','lastname','','R','user_name','','R','password','','R','email','','RisEmail');return document.MM_returnValue">
  <table >
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">First Name:</td>
      <td><input name="firstname" type="text" class="input_standard" id="firstname" value="<?php echo $row_getUser['firstname']; ?>" size="32" maxlength="255" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Last Name:</td>
      <td><input name="lastname" type="text" class="input_standard" id="lastname" value="<?php echo $row_getUser['lastname']; ?>" size="32" maxlength="255" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Username:</td>
      <td><input name="user_name" type="text" disabled="disabled" class="input_standard" id="user_name" value="<?php echo $row_getUser['username']; ?>" size="20" maxlength="255" /><input type="hidden" name="username" value="<?php echo $row_getUser['username']; ?>" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Password:</td>
      <td><input name="password" type="password" class="input_standard" id="password" value="<?php echo $row_getUser['password']; ?>" size="20" maxlength="255" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">User Group:</td>
      <td><select name="usrgroup" class="input_standard" <?php if ($row_getUser['id'] == 1) { ?> disabled="disabled" <?php } ?>>
        <?php
do {  
?>
        <option value="<?php echo $row_usergroups['id']?>"<?php if (!(strcmp($row_usergroups['id'], htmlentities($row_getUser['usrgroup'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>><?php echo $row_usergroups['name']?></option>
        <?php
} while ($row_usergroups = mysql_fetch_assoc($usergroups));
  $rows = mysql_num_rows($usergroups);
  if($rows > 0) {
      mysql_data_seek($usergroups, 0);
	  $row_usergroups = mysql_fetch_assoc($usergroups);
  }
?>
      </select></td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Email:</td>
      <td><input name="email" type="text" class="input_standard" id="email" value="<?php echo $row_getUser['email']; ?>" size="32" maxlength="255" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><p>
        <label>
        <input <?php if (!(strcmp($row_getUser['inactive'],"0"))) {echo "CHECKED";} ?> type="radio" name="inactive" value="0" <?php if ($row_getUser['id'] == 1) { ?> disabled="disabled" <?php } ?> />
  Enabled</label>
        <label>
        <input <?php if (!(strcmp($row_getUser['inactive'],"1"))) {echo "CHECKED";} ?> type="radio" name="inactive" value="1" <?php if ($row_getUser['id'] == 1) { ?> disabled="disabled" <?php } ?> />
  Disabled</label>
        <br />
      </p></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" class="input_standard" value="Update user" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form5" />
  <input type="hidden" name="id" value="<?php echo $_GET['user']; ?>" />
</form>
<?php } 
elseif ($_POST['action'] != 'add_user') { ?>

	<p><a href="#" onClick="document.getElementById('action').value = 'add_user'; document.getElementById('frm_user_action').submit(); return false;">Add a user</a></p>

<?php }
?>
<?php }

elseif ($_POST['action'] != 'add_group') { ?>

	<p><a href="#" onClick="document.getElementById('action').value = 'add_group'; document.getElementById('frm_usergroup_action').submit(); return false;">Add a user group</a></p>

<?php }
?>

</div>

<div>

<!--HTML for Context Menu 1-->
<ul id="contextmenu1" class="jqcontextmenu">

<?php if ($_GET['browse'] == "" || $_GET['browse'] == "groups") { ?>

  <form action="userGroupView.php?browse=groups&group=<?php echo $_GET['group']; ?>" method="post" name="frm_usergroup_action" target="_self" id="frm_usergroup_action">
      <input type="hidden" name="action" id="action">
        <li><a href="#" onClick="document.getElementById('action').value = 'add_group'; document.getElementById('frm_usergroup_action').submit(); return false;">Add a user group</a></li>
		<?php if ($_GET['group'] != "") { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_group'; document.getElementById('frm_usergroup_action').submit(); return false;">Edit this user group</a></li>
        <li><a href="#" onClick="document.frm_group_delete.submit(); return false;">Delete this user group</a></li>
        <?php } ?>
        <?php if ($totalRows_group_observiums > 0) { ?>
         <li><a href="#" onClick="document.frm_delete_group_observiums.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
        <?php if ($totalRows_group_damits > 0) { ?>
         <li><a href="#" onClick="document.frm_delete_group_damits.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
        <?php if ($totalRows_group_radiators > 0) { ?>
         <li><a href="#" onClick="document.frm_delete_group_radiators.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
        <?php if ($totalRows_group_radiatorfeatures > 0) { ?>
         <li><a href="#" onClick="document.frm_delete_group_radiatorfeatures.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
        <?php if ($totalRows_group_radgroups > 0) { ?>
         <li><a href="#" onClick="document.frm_delete_group_radiatorgroups.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
        <?php if ($totalRows_group_pages > 0) { ?>
         <li><a href="#" onClick="document.frm_delete_group_pages.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
		<?php if ($totalRows_group_vpns > 0) { ?>
         <li><a href="#" onClick="document.frm_delete_group_vpns.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
		<?php if ($totalRows_group_providers > 0) { ?>
         <li><a href="#" onClick="document.frm_delete_group_providers.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
		<?php if ($totalRows_group_device_groups > 0) { ?>
        <li><a href="#" onClick="document.frm_delete_group_device_groups.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
		<?php if ($totalRows_group_devices > 0) { ?>
        <li><a href="#" onClick="document.frm_delete_group_devices.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
        <?php if ($totalRows_group_networks > 0) { ?>
        <li><a href="#" onClick="document.frm_delete_group_networks.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
        <?php if ($totalRows_group_netgroups > 0) { ?>
        <li><a href="#" onClick="document.frm_delete_group_netgroups.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
        <?php if ($totalRows_group_containers > 0) { ?>
         <li><a href="#" onClick="document.frm_delete_group_containers.submit(); return false;">Delete selected permissions</a></li>
        <?php } ?>
 
    </form>
<?php } ?>
<?php if ($_GET['browse'] == "users") { ?>
    	<form action="userGroupView.php?browse=users&user=<?php echo $_GET['user']; ?>" method="post" name="frm_user_action" target="_self" id="frm_user_action">
        	<input type="hidden" name="action" id="action">
            	<li><a href="#" onClick="document.getElementById('action').value = 'add_user'; document.getElementById('frm_user_action').submit(); return false;">Add a user</a></li>
        </form>
<?php } ?>
    </ul>

</div>
</div>
</div>

</body>
</html>
