<?php require_once('Connections/subman.php'); ?>
<?php include('includes/standard_functions.php'); ?>
<?php require_once('Net/IPv4.php'); ?>
<?php include('includes/ipm_class.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}

if ($_GET['errstr'] != 1) {
	
	$_SESSION['errstr'] = "";
	
}

$ipm = new ipm;

$pageLevel = $ipm->getPageLevel(5,$_SESSION['MM_Username']);

if ($_GET['submit'] == "_search") {
	$_SESSION['search'] = $_GET['search'];
	$_SESSION['dd'] = $_GET['dd'];
	$_SESSION['mm'] = $_GET['mm'];
	$_SESSION['yyyy'] = $_GET['yyyy'];
	$_SESSION['dd1'] = $_GET['dd1'];
	$_SESSION['mm1'] = $_GET['mm1'];
	$_SESSION['yyyy1'] = $_GET['yyyy1'];
	$_SESSION['accttype'] = $_GET['accttype'];
}
elseif ($_GET['submit'] == "_cancel_search") {
	$_SESSION['search'] = "";
	$_SESSION['dd'] = "";
	$_SESSION['mm'] = "";
	$_SESSION['yyyy'] = "";
	$_SESSION['dd1'] = "";
	$_SESSION['mm1'] = "";
	$_SESSION['yyyy1'] = "";
	$_SESSION['accttype'] = "";
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

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_addfixedpool")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_POST['radiator']."";
	$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
	$row_getRadiator = mysql_fetch_assoc($getRadiator);
	$totalRows_getRadiator = mysql_num_rows($getRadiator);

	$hostname_radiator = $row_getRadiator['host'];
	$database_radiator = $row_getRadiator['dbname'];
	$username_radiator = $row_getRadiator['username'];
	$password_radiator = $row_getRadiator['pwd'];
	$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR);
	
	if ($_POST['startaddress'] != "0.0.0.0" && $_POST['endaddress'] != "0.0.0.0" && (ip2long($_POST['endaddress']) >= ip2long($_POST['startaddress'])) && (abs(ip2long($_POST['endaddress']) - ip2long($_POST['startaddress'])) <= 256)) {
		
		if (Net_IPv4::validateIP($_POST['startaddress']) && Net_IPv4::validateIP($_POST['endaddress'])) {
		
			for ($i = ip2long($_POST['startaddress']); $i < ip2long($_POST['endaddress'])+1; $i++) {
				
				mysql_select_db($database_radiator, $radiator);
				$query_check_framedip = "SELECT * FROM RADIP WHERE SUBSCRIBERGROUP = '".$_POST['subscribergroup']."' AND FRAMEDIP = '".long2ip($i)."'";
				$check_framedip = mysql_query($query_check_framedip, $radiator) or die(mysql_error());
				$row_check_framedip = mysql_fetch_assoc($check_framedip);
				$totalRows_check_framedip = mysql_num_rows($check_framedip);
				
				if ($totalRows_check_framedip == 0) {
					
					$insertSQL = sprintf("INSERT INTO RADIP (SUBSCRIBERGROUP, FRAMEDIP) VALUES (%s, %s)",
										   GetSQLValueString($_POST['subscribergroup'], "text"),
										   GetSQLValueString(long2ip($i), "text"));
					
					mysql_select_db($database_radiator, $radiator);
					$Result1 = mysql_query($insertSQL, $radiator) or die(mysql_error());
				  
				}

			}
			
		}
		
	}
	else {
		
		$_SESSION['errstr'] .= "The pool was not created because you entered an invalid IP address, the end address was greater than the start address, or you tried to add more than 256 addresses at once.";
			$errstrflag = 1;
			
	}
	
  $insertGoTo = "radiatorMgmtView.php?errstr=".$errstrflag;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO radiator (`host`, descr, username, pwd, dbname) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['host'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['pwd'], "text"),
                       GetSQLValueString($_POST['dbname'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "radiatorMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST['username'])) && ($_POST['username'] != "") && (isset($_POST['frmDeleteSubscriber']))) {
	
	mysql_select_db($database_subman, $subman);
	$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_POST['radiator']."";
	$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
	$row_getRadiator = mysql_fetch_assoc($getRadiator);
	$totalRows_getRadiator = mysql_num_rows($getRadiator);

	$hostname_radiator = $row_getRadiator['host'];
	$database_radiator = $row_getRadiator['dbname'];
	$username_radiator = $row_getRadiator['username'];
	$password_radiator = $row_getRadiator['pwd'];
	$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR);

	mysql_select_db($database_radiator, $radiator);
	$query_getSubscriber = "SELECT SUBSCRIBERS.*, RADIP.SUBSCRIBERGROUP AS RADIPSUBSCRIBERGRP FROM SUBSCRIBERS LEFT JOIN RADIP ON RADIP.CIRCUITID = SUBSCRIBERS.CIRCUITID WHERE SUBSCRIBERS.USERNAME = '".$_POST['username']."'";
	$getSubscriber = mysql_query($query_getSubscriber, $radiator) or die(mysql_error());
	$row_getSubscriber = mysql_fetch_assoc($getSubscriber);
	$totalRows_getSubscriber = mysql_num_rows($getSubscriber);
	
	$random = generatePassword();
	
	$updateSQL = sprintf("UPDATE RADIP SET CIRCUITID='' WHERE FRAMEDIP=%s AND SUBSCRIBERGROUP=%s AND CIRCUITID=%s",
                       GetSQLValueString($row_getSubscriber['FRAMEDIP'], "text"),
                       GetSQLValueString($row_getSubscriber['RADIPSUBSCRIBERGRP'], "text"),
					   GetSQLValueString($row_getSubscriber['CIRCUITID'], "text"));

  		mysql_select_db($database_radiator, $radiator);
  		$Result1 = mysql_query($updateSQL, $radiator) or die(mysql_error());
		
  $updateSQL = sprintf("UPDATE SUBSCRIBERS SET SUBSCRIBERGROUP = 'JT-CLOSED', RETIREDDATE = UNIX_TIMESTAMP(now()), USERNAME = '".$_POST['username']."-".$random."', CIRCUITID = '".$row_getSubscriber['CIRCUITID']."-".$random."', ROUTES = NULL WHERE USERNAME=%s",
                       GetSQLValueString($_POST['username'], "text"));

  mysql_select_db($database_radiator, $radiator);
  $Result1 = mysql_query($updateSQL, $radiator) or die(mysql_error());
  
#  $deleteSQL = sprintf("DELETE FROM ACCOUNTING WHERE USERNAME=%s",
 #                      GetSQLValueString($_POST['username'], "text"));

  #mysql_select_db($database_radiator, $radiator);
  #$Result1 = mysql_query($deleteSQL, $radiator) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM routes WHERE subscriber=%s",
                       GetSQLValueString($_POST['username'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM arbitraryroutes WHERE subscriber=%s",
                       GetSQLValueString($_POST['username'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $deleteGoTo = "radiatorMgmtView.php?browse=radiators&radiator=".$_POST['radiator'];
  
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form4")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
	$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
	$row_getRadiator = mysql_fetch_assoc($getRadiator);
	$totalRows_getRadiator = mysql_num_rows($getRadiator);

	$hostname_radiator = $row_getRadiator['host'];
	$database_radiator = $row_getRadiator['dbname'];
	$username_radiator = $row_getRadiator['username'];
	$password_radiator = $row_getRadiator['pwd'];
	$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR);
		
	if ($_POST['FRAMEDIP'] == "") {
		$framedIP = 'NULL';
	}
	else {
		$framedIP = GetSQLValueString($_POST['FRAMEDIP'], "text");
	}
	
	if ($_POST['FRAMEDIP'] != "") { 
	
		mysql_select_db($database_radiator, $radiator);
		$query_check_framedip = "SELECT * FROM RADIP WHERE SUBSCRIBERGROUP = '".$_POST['IPSUBSCRIBERGROUP']."' AND FRAMEDIP = '".$_POST['FRAMEDIP']."' AND CIRCUITID = ''";
		$check_framedip = mysql_query($query_check_framedip, $radiator) or die(mysql_error());
		$row_check_framedip = mysql_fetch_assoc($check_framedip);
		$totalRows_check_framedip = mysql_num_rows($check_framedip);
		
		if ($totalRows_check_framedip > 0) {
			
  			$insertSQL = sprintf("INSERT INTO SUBSCRIBERS (USERNAME, PASSWORD, CIRCUITID, FRAMEDIP, SUBSCRIBERGROUP) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['USERNAME'], "text"),
                       GetSQLValueString($_POST['PASSWORD'], "text"),
                       GetSQLValueString($_POST['CIRCUITID'], "text"),
                       $framedIP,
                       GetSQLValueString($_POST['SUBSCRIBERGROUP'], "text"));

	
		  mysql_select_db($database_radiator, $radiator);
		  $Result1 = mysql_query($insertSQL, $radiator) or die(mysql_error());
		  
		  $updateSQL = sprintf("UPDATE RADIP SET CIRCUITID=%s WHERE FRAMEDIP=%s AND SUBSCRIBERGROUP=%s",
                       GetSQLValueString($_POST['CIRCUITID'], "text"),
                       GetSQLValueString($_POST['FRAMEDIP'], "text"),
                       GetSQLValueString($_POST['IPSUBSCRIBERGROUP'], "text"));
		  
		  mysql_select_db($database_radiator, $radiator);
  		$Result1 = mysql_query($updateSQL, $radiator) or die(mysql_error());

		}
		else {
			
			$_SESSION['errstr'] .= "The IP address is already in use or does not exist within the selected subscriber group.  The user was not created.";
			$errstrflag = 1;
			
		}
		
	}
	
	else {
		
		$insertSQL = sprintf("INSERT INTO SUBSCRIBERS (USERNAME, PASSWORD, CIRCUITID, FRAMEDIP, SUBSCRIBERGROUP) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['USERNAME'], "text"),
                       GetSQLValueString($_POST['PASSWORD'], "text"),
                       GetSQLValueString($_POST['CIRCUITID'], "text"),
                       $framedIP,
                       GetSQLValueString($_POST['SUBSCRIBERGROUP'], "text"));
		
		mysql_select_db($database_radiator, $radiator);
		  $Result1 = mysql_query($insertSQL, $radiator) or die(mysql_error());
		  
	}
	
  $insertGoTo = "radiatorMgmtView.php?errstr=".$errstrflag;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form5")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
	$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
	$row_getRadiator = mysql_fetch_assoc($getRadiator);
	$totalRows_getRadiator = mysql_num_rows($getRadiator);

	$hostname_radiator = $row_getRadiator['host'];
	$database_radiator = $row_getRadiator['dbname'];
	$username_radiator = $row_getRadiator['username'];
	$password_radiator = $row_getRadiator['pwd'];
	$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR);
	
	if (($_POST['FRAMEDIP'] != $_POST['OLD_FRAMEDIP']) || ($_POST['IPSUBSCRIBERGROUP'] != $_POST['OLD_IPSUBSCRIBERGROUP'])) {
		
		mysql_select_db($database_radiator, $radiator);
		$query_check_framedip = "SELECT * FROM RADIP WHERE SUBSCRIBERGROUP = '".$_POST['IPSUBSCRIBERGROUP']."' AND FRAMEDIP = '".$_POST['FRAMEDIP']."' AND (CIRCUITID = '' OR CIRCUITID IS NULL)";
		$check_framedip = mysql_query($query_check_framedip, $radiator) or die(mysql_error());
		$row_check_framedip = mysql_fetch_assoc($check_framedip);
		$totalRows_check_framedip = mysql_num_rows($check_framedip);
		
		if ($totalRows_check_framedip > 0 || $_POST['FRAMEDIP'] == "") {
			
			$updateSQL = sprintf("UPDATE SUBSCRIBERS SET PASSWORD=%s, CIRCUITID=%s, FRAMEDIP=%s, SUBSCRIBERGROUP=%s WHERE USERNAME=%s",
                       GetSQLValueString($_POST['PASSWORD'], "text"),
                       GetSQLValueString($_POST['CIRCUITID'], "text"),
                       GetSQLValueString($_POST['FRAMEDIP'], "text"),
                       GetSQLValueString($_POST['SUBSCRIBERGROUP'], "text"),
                       GetSQLValueString($_POST['USERNAME'], "text"));

  		mysql_select_db($database_radiator, $radiator);
  		$Result1 = mysql_query($updateSQL, $radiator) or die(mysql_error());
			
			$updateSQL = sprintf("UPDATE RADIP SET CIRCUITID='' WHERE FRAMEDIP=%s AND SUBSCRIBERGROUP=%s",
                       GetSQLValueString($_POST['OLD_FRAMEDIP'], "text"),
                       GetSQLValueString($_POST['OLD_IPSUBSCRIBERGROUP'], "text"));

  		mysql_select_db($database_radiator, $radiator);
  		$Result1 = mysql_query($updateSQL, $radiator) or die(mysql_error());
		
		if ($_POST['FRAMEDIP'] != "") {
			
			$updateSQL = sprintf("UPDATE RADIP SET CIRCUITID=%s WHERE FRAMEDIP=%s AND SUBSCRIBERGROUP=%s",
                       GetSQLValueString($_POST['CIRCUITID'], "text"),
                       GetSQLValueString($_POST['FRAMEDIP'], "text"),
                       GetSQLValueString($_POST['IPSUBSCRIBERGROUP'], "text"));

  			mysql_select_db($database_radiator, $radiator);
  			$Result1 = mysql_query($updateSQL, $radiator) or die(mysql_error());
			
		}
			
		}
		else {
			
			$_SESSION['errstr'] .= "The IP address is already in use or does not exist within the selected subscriber group.  The user was not updated.";
			$errstrflag = 1;
			
		}
		
	}
	
	else {
		
		$updateSQL = sprintf("UPDATE SUBSCRIBERS SET PASSWORD=%s, CIRCUITID=%s, FRAMEDIP=%s, SUBSCRIBERGROUP=%s WHERE USERNAME=%s",
                       GetSQLValueString($_POST['PASSWORD'], "text"),
                       GetSQLValueString($_POST['CIRCUITID'], "text"),
                       GetSQLValueString($_POST['FRAMEDIP'], "text"),
                       GetSQLValueString($_POST['SUBSCRIBERGROUP'], "text"),
                       GetSQLValueString($_POST['USERNAME'], "text"));

  		mysql_select_db($database_radiator, $radiator);
  		$Result1 = mysql_query($updateSQL, $radiator) or die(mysql_error());

	}
	
  $updateGoTo = "radiatorMgmtView.php?errstr=".$errstrflag;
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
	
	mysql_select_db($database_subman, $subman);
$query_check_routes = "SELECT * FROM routes WHERE radiator = ".$_POST['radiator']." AND subscriber = '".$_POST['subscriber']."' AND network = ".$_POST['network']."";
$check_routes = mysql_query($query_check_routes, $subman) or die(mysql_error());
$row_check_routes = mysql_fetch_assoc($check_routes);
$totalRows_check_routes = mysql_num_rows($check_routes);

	if ($totalRows_check_routes == 0) {
		
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_POST['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);
	
		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR);
	
  		$insertSQL = sprintf("INSERT INTO routes (network, subscriber, radiator) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['network'], "int"),
                       GetSQLValueString($_POST['subscriber'], "text"),
					   GetSQLValueString($_POST['radiator'], "int"));

		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
  
		mysql_select_db($database_subman, $subman);
		$query_getRoutes = "SELECT routes.*, networks.network, networks.maskLong FROM routes LEFT JOIN networks ON networks.id = routes.network WHERE subscriber = '".$_POST['subscriber']."'";
		$getRoutes = mysql_query($query_getRoutes, $subman) or die(mysql_error());
		$row_getRoutes = mysql_fetch_assoc($getRoutes);
		$totalRows_getRoutes = mysql_num_rows($getRoutes);
		
		mysql_select_db($database_subman, $subman);
		$query_getArbRoutes = "SELECT * FROM arbitraryroutes WHERE subscriber = '".$_POST['subscriber']."'";
		$getArbRoutes = mysql_query($query_getArbRoutes, $subman) or die(mysql_error());
		$row_getArbRoutes = mysql_fetch_assoc($getArbRoutes);
		$totalRows_getArbRoutes = mysql_num_rows($getArbRoutes);
	
		$count = 0;
		
		do {
			if ($count > 0) {
				$routes .= ", ";
			}
			$routes .= 'cisco-avpair="ip:route='.long2ip($row_getRoutes['network']).' '.long2ip($row_getRoutes['maskLong']).'"';
			$count++;
		} while ($row_getRoutes = mysql_fetch_assoc($getRoutes));
		
		if ($totalRows_getArbRoutes > 0) {
			do {
				if ($count > 0) {
					$routes .= ", ";
				}
				$routes .= 'cisco-avpair="ip:route='.$row_getArbRoutes['network'].' '.$row_getArbRoutes['mask'].'"';
				$count++;
			} while ($row_getArbRoutes = mysql_fetch_assoc($getArbRoutes));
		}
		
  		$updateSQL = sprintf("UPDATE SUBSCRIBERS SET ROUTES = %s WHERE USERNAME = %s",
                       GetSQLValueString($routes, "text"),
					   GetSQLValueString($_POST['subscriber'], "text"));

		mysql_select_db($database_radiator, $radiator);
  		$Result1 = mysql_query($updateSQL, $radiator) or die(mysql_error());
	}
	
  $insertGoTo = "radiatorMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "add_arbitraryroute")) {
	
	if ($_POST['network'] != "0.0.0.0" && $_POST['mask'] != "0.0.0.0") {
		
		$ip_calc = new Net_IPv4();
		$ip_calc->ip = $_POST['network'];
		$ip_calc->netmask = $_POST['mask'];
		$ip_calc->calculate();
		
		if (Net_IPv4::validateIP($_POST['network'])) {
				
			mysql_select_db($database_subman, $subman);
		$query_check_routes = "SELECT * FROM arbitraryroutes WHERE radiator = ".$_POST['radiator']." AND subscriber = '".$_POST['subscriber']."' AND network = '".$_POST['network']."' AND mask = '".$_POST['mask']."'";
		$check_routes = mysql_query($query_check_routes, $subman) or die(mysql_error());
		$row_check_routes = mysql_fetch_assoc($check_routes);
		$totalRows_check_routes = mysql_num_rows($check_routes);
		
			if ($totalRows_check_routes == 0) {
				
				mysql_select_db($database_subman, $subman);
				$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_POST['radiator']."";
				$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
				$row_getRadiator = mysql_fetch_assoc($getRadiator);
				$totalRows_getRadiator = mysql_num_rows($getRadiator);
			
				$hostname_radiator = $row_getRadiator['host'];
				$database_radiator = $row_getRadiator['dbname'];
				$username_radiator = $row_getRadiator['username'];
				$password_radiator = $row_getRadiator['pwd'];
				$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR);
			
				$insertSQL = sprintf("INSERT INTO arbitraryroutes (network, mask, subscriber, radiator) VALUES (%s, %s, %s, %s)",
							   GetSQLValueString($_POST['network'], "text"),
							   GetSQLValueString($_POST['mask'], "text"),
							   GetSQLValueString($_POST['subscriber'], "text"),
							   GetSQLValueString($_POST['radiator'], "int"));
		
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
				
				mysql_select_db($database_radiator, $radiator);
				$query_getVrf = "SELECT SUBSCRIBERS.*, SUBSCRIBERGROUPS.VRFID FROM SUBSCRIBERS LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE SUBSCRIBERS.USERNAME = '".$_POST['subscriber']."'";
				$getVrf = mysql_query($query_getVrf, $radiator) or die(mysql_error());
				$row_getVrf = mysql_fetch_assoc($getVrf);
				$totalRows_getVrf = mysql_num_rows($getVrf);
				
				mysql_select_db($database_subman, $subman);
				$query_getVrfName = "SELECT vrf.name FROM vrf WHERE vrf.id = '".$row_getVrf['VRFID']."'";
				$getVrfName = mysql_query($query_getVrfName, $subman) or die(mysql_error());
				$row_getVrfName = mysql_fetch_assoc($getVrfName);
				$totalRows_getVrfName = mysql_num_rows($getVrfName);
				
				mysql_select_db($database_subman, $subman);
				$query_getRoutes = "SELECT routes.*, networks.network, networks.maskLong FROM routes LEFT JOIN networks ON networks.id = routes.network WHERE subscriber = '".$_POST['subscriber']."'";
				$getRoutes = mysql_query($query_getRoutes, $subman) or die(mysql_error());
				$row_getRoutes = mysql_fetch_assoc($getRoutes);
				$totalRows_getRoutes = mysql_num_rows($getRoutes);
				
				mysql_select_db($database_subman, $subman);
				$query_getArbRoutes = "SELECT * FROM arbitraryroutes WHERE subscriber = '".$_POST['subscriber']."'";
				$getArbRoutes = mysql_query($query_getArbRoutes, $subman) or die(mysql_error());
				$row_getArbRoutes = mysql_fetch_assoc($getArbRoutes);
				$totalRows_getArbRoutes = mysql_num_rows($getArbRoutes);
			
				$count = 0;
				
				if ($row_getVrf['VRFID'] != "") {
					$vrf = "vrf ".$row_getVrfName['name']." ";
				}
				else {
					$vrf = "";
				}
				
				if ($totalRows_getRoutes > 0) {
					do {
						if ($count > 0) {
							$routes .= ", ";
						}
						$routes .= 'cisco-avpair="ip:route='.$vrf.long2ip($row_getRoutes['network']).' '.long2ip($row_getRoutes['maskLong']).'"';
						$count++;
					} while ($row_getRoutes = mysql_fetch_assoc($getRoutes));
				}
				
				do {
					if ($count > 0) {
						$routes .= ", ";
					}
					$routes .= 'cisco-avpair="ip:route='.$vrf.$row_getArbRoutes['network'].' '.$row_getArbRoutes['mask'].'"';
					$count++;
				} while ($row_getArbRoutes = mysql_fetch_assoc($getArbRoutes));
				
				$updateSQL = sprintf("UPDATE SUBSCRIBERS SET ROUTES = %s WHERE USERNAME = %s",
							   GetSQLValueString($routes, "text"),
							   GetSQLValueString($_POST['subscriber'], "text"));
		
				mysql_select_db($database_radiator, $radiator);
				$Result1 = mysql_query($updateSQL, $radiator) or die(mysql_error());
			}
		
		}
	
	}
	
  $insertGoTo = "radiatorMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_POST['radiator']."";
	$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
	$row_getRadiator = mysql_fetch_assoc($getRadiator);
	$totalRows_getRadiator = mysql_num_rows($getRadiator);
	
	$hostname_radiator = $row_getRadiator['host'];
	$database_radiator = $row_getRadiator['dbname'];
	$username_radiator = $row_getRadiator['username'];
	$password_radiator = $row_getRadiator['pwd'];
	$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
	
	if ($_POST['VPN'] != "") { 

		mysql_select_db($database_subman, $subman);
		$query_vpnvrf = "SELECT vrf.*, vpn.name as vpnname, provider.asnumber, provider.name as providername, rd.rd as rdnumber FROM vrf LEFT JOIN vpnvrf ON vpnvrf.vrf = vrf.id LEFT JOIN vpn ON vpn.id = vpnvrf.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN rd ON rd.id = vrf.rd WHERE vrf.id = ".$_POST['VPN']." ORDER BY provider.name, vpn.name, vrf.name";
		$vpnvrf = mysql_query($query_vpnvrf, $subman) or die(mysql_error());
		$row_vpnvrf = mysql_fetch_assoc($vpnvrf);
		$totalRows_vpnvrf = mysql_num_rows($vpnvrf);
		
	}
		if ($totalRows_vpnvrf > 0) {
			$vpn_config = 'cisco-avpair="lcp:interface-config=ip vrf forwarding '.$row_vpnvrf['name'].'", cisco-avpair="lcp:interface-config=ip unnumbered loopback '.$row_vpnvrf['asnumber'].$row_vpnvrf['rdnumber'].'"';
		}
		else {
			$vpn_config = "";
		}
		
  $insertSQL = sprintf("INSERT INTO SUBSCRIBERGROUPS (GRPNAME, POOLHINT, VPN, VRFID, SIMULTANEOUSLOGIN) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['GRPNAME'], "text"),
                       GetSQLValueString($_POST['POOLHINT'], "text"),
                       GetSQLValueString($vpn_config, "text"),
					   GetSQLValueString($_POST['VPN'], "int"),
					   GetSQLValueString($_POST['logins'], "int"));

  mysql_select_db($database_radiator, $radiator);
  $Result1 = mysql_query($insertSQL, $radiator) or die(mysql_error());

  $insertGoTo = "radiatorMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_edit_group")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_POST['radiator']."";
	$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
	$row_getRadiator = mysql_fetch_assoc($getRadiator);
	$totalRows_getRadiator = mysql_num_rows($getRadiator);
	
	$hostname_radiator = $row_getRadiator['host'];
	$database_radiator = $row_getRadiator['dbname'];
	$username_radiator = $row_getRadiator['username'];
	$password_radiator = $row_getRadiator['pwd'];
	$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
	
	if ($_POST['VPN'] != "") { 

		mysql_select_db($database_subman, $subman);
		$query_vpnvrf = "SELECT vrf.*, vpn.name as vpnname, provider.asnumber, provider.name as providername, rd.rd as rdnumber FROM vrf LEFT JOIN vpnvrf ON vpnvrf.vrf = vrf.id LEFT JOIN vpn ON vpn.id = vpnvrf.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN rd ON rd.id = vrf.rd WHERE vrf.id = ".$_POST['VPN']." ORDER BY provider.name, vpn.name, vrf.name";
		$vpnvrf = mysql_query($query_vpnvrf, $subman) or die(mysql_error());
		$row_vpnvrf = mysql_fetch_assoc($vpnvrf);
		$totalRows_vpnvrf = mysql_num_rows($vpnvrf);
		
	}
		if ($totalRows_vpnvrf > 0) {
			$vpn_config = 'cisco-avpair="lcp:interface-config=ip vrf forwarding '.$row_vpnvrf['name'].'", cisco-avpair="lcp:interface-config=ip unnumbered loopback '.$row_vpnvrf['asnumber'].$row_vpnvrf['rdnumber'].'"';
		}
		else {
			$vpn_config = "";
		}
		
  $insertSQL = sprintf("UPDATE SUBSCRIBERGROUPS SET POOLHINT=%s, VPN=%s, VRFID=%s, SIMULTANEOUSLOGIN=%s WHERE SUBSCRIBERGROUPS.GRPNAME=%s",
                       GetSQLValueString($_POST['POOLHINT'], "text"),
                       GetSQLValueString($vpn_config, "text"),
					   GetSQLValueString($_POST['VPN'], "int"),
					   GetSQLValueString($_POST['logins'], "int"),
					   GetSQLValueString($_POST['GRPNAME'], "text"));

  mysql_select_db($database_radiator, $radiator);
  $Result1 = mysql_query($insertSQL, $radiator) or die(mysql_error());

  $insertGoTo = "radiatorMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_subscriber_routes') {
	
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
			
			mysql_select_db($database_subman, $subman);
			$query_getRoute = "SELECT * FROM routes WHERE id = ".$_POST['id_'.($i+1)]."";
			$getRoute = mysql_query($query_getRoute, $subman) or die(mysql_error());
			$row_getRoute = mysql_fetch_assoc($getRoute);
			$totalRows_getRoute = mysql_num_rows($getRoute);
			
			mysql_select_db($database_subman, $subman);
			$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$row_getRoute['radiator']."";
			$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
			$row_getRadiator = mysql_fetch_assoc($getRadiator);
			$totalRows_getRadiator = mysql_num_rows($getRadiator);
			
			$hostname_radiator = $row_getRadiator['host'];
			$database_radiator = $row_getRadiator['dbname'];
			$username_radiator = $row_getRadiator['username'];
			$password_radiator = $row_getRadiator['pwd'];
			$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
			
  			$deleteSQL = sprintf("DELETE FROM routes WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			
			mysql_select_db($database_subman, $subman);
			$query_getRoutes = "SELECT routes.*, networks.network, networks.maskLong FROM routes LEFT JOIN networks ON networks.id = routes.network WHERE subscriber = '".$row_getRoute['subscriber']."'";
			$getRoutes = mysql_query($query_getRoutes, $subman) or die(mysql_error());
			$row_getRoutes = mysql_fetch_assoc($getRoutes);
			$totalRows_getRoutes = mysql_num_rows($getRoutes);
			
			mysql_select_db($database_subman, $subman);
			$query_getArbRoutes = "SELECT * FROM arbitraryroutes WHERE subscriber = '".$row_getRoute['subscriber']."'";
			$getArbRoutes = mysql_query($query_getArbRoutes, $subman) or die(mysql_error());
			$row_getArbRoutes = mysql_fetch_assoc($getArbRoutes);
			$totalRows_getArbRoutes = mysql_num_rows($getArbRoutes);
		
			$count = 0;
			
			if ($totalRows_getRoutes > 0) {
				do {
					if ($count > 0) {
						$routes .= ", ";
					}
					$routes .= 'cisco-avpair="ip:route='.long2ip($row_getRoutes['network']).' '.long2ip($row_getRoutes['maskLong']).'"';
					$count++;
				} while ($row_getRoutes = mysql_fetch_assoc($getRoutes));
			}
			
			if ($totalRows_getArbRoutes > 0) {
				do {
					if ($count > 0) {
						$routes .= ", ";
					}
					$routes .= 'cisco-avpair="ip:route='.$row_getArbRoutes['network'].' '.$row_getArbRoutes['mask'].'"';
					$count++;
				} while ($row_getArbRoutes = mysql_fetch_assoc($getArbRoutes));
			}
			
			$updateSQL = sprintf("UPDATE SUBSCRIBERS SET ROUTES = %s WHERE USERNAME = %s",
						   GetSQLValueString($routes, "text"),
						   GetSQLValueString($row_getRoute['subscriber'], "text"));
	
			mysql_select_db($database_radiator, $radiator);
			$Result1 = mysql_query($updateSQL, $radiator) or die(mysql_error());
		
		}
	}
	
  $deleteGoTo = "radiatorMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_subscriber_arbroutes') {
	
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
			
			mysql_select_db($database_subman, $subman);
			$query_getRoute = "SELECT * FROM arbitraryroutes WHERE id = ".$_POST['id_'.($i+1)]."";
			$getRoute = mysql_query($query_getRoute, $subman) or die(mysql_error());
			$row_getRoute = mysql_fetch_assoc($getRoute);
			$totalRows_getRoute = mysql_num_rows($getRoute);
			
			mysql_select_db($database_subman, $subman);
			$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$row_getRoute['radiator']."";
			$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
			$row_getRadiator = mysql_fetch_assoc($getRadiator);
			$totalRows_getRadiator = mysql_num_rows($getRadiator);
			
			$hostname_radiator = $row_getRadiator['host'];
			$database_radiator = $row_getRadiator['dbname'];
			$username_radiator = $row_getRadiator['username'];
			$password_radiator = $row_getRadiator['pwd'];
			$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
			
  			$deleteSQL = sprintf("DELETE FROM arbitraryroutes WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			
			mysql_select_db($database_subman, $subman);
			$query_getRoutes = "SELECT routes.*, networks.network, networks.maskLong FROM routes LEFT JOIN networks ON networks.id = routes.network WHERE subscriber = '".$row_getRoute['subscriber']."'";
			$getRoutes = mysql_query($query_getRoutes, $subman) or die(mysql_error());
			$row_getRoutes = mysql_fetch_assoc($getRoutes);
			$totalRows_getRoutes = mysql_num_rows($getRoutes);
			
			mysql_select_db($database_subman, $subman);
			$query_getArbRoutes = "SELECT * FROM arbitraryroutes WHERE subscriber = '".$row_getRoute['subscriber']."'";
			$getArbRoutes = mysql_query($query_getArbRoutes, $subman) or die(mysql_error());
			$row_getArbRoutes = mysql_fetch_assoc($getArbRoutes);
			$totalRows_getArbRoutes = mysql_num_rows($getArbRoutes);
		
			$count = 0;
			
			if ($totalRows_getRoutes > 0) {
				do {
					if ($count > 0) {
						$routes .= ", ";
					}
					$routes .= 'cisco-avpair="ip:route='.long2ip($row_getRoutes['network']).' '.long2ip($row_getRoutes['maskLong']).'"';
					$count++;
				} while ($row_getRoutes = mysql_fetch_assoc($getRoutes));
			}
			
			if ($totalRows_getArbRoutes > 0) {
				do {
					if ($count > 0) {
						$routes .= ", ";
					}
					$routes .= 'cisco-avpair="ip:route='.$row_getArbRoutes['network'].' '.$row_getArbRoutes['mask'].'"';
					$count++;
				} while ($row_getArbRoutes = mysql_fetch_assoc($getArbRoutes));
			}
			
			$updateSQL = sprintf("UPDATE SUBSCRIBERS SET ROUTES = %s WHERE USERNAME = %s",
						   GetSQLValueString($routes, "text"),
						   GetSQLValueString($row_getRoute['subscriber'], "text"));
	
			mysql_select_db($database_radiator, $radiator);
			$Result1 = mysql_query($updateSQL, $radiator) or die(mysql_error());
		
		}
	}
	
  $deleteGoTo = "radiatorMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_subscriber_groups') {
	
	mysql_select_db($database_subman, $subman);
	$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_POST['radiator']."";
	$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
	$row_getRadiator = mysql_fetch_assoc($getRadiator);
	$totalRows_getRadiator = mysql_num_rows($getRadiator);
	
	$hostname_radiator = $row_getRadiator['host'];
	$database_radiator = $row_getRadiator['dbname'];
	$username_radiator = $row_getRadiator['username'];
	$password_radiator = $row_getRadiator['pwd'];
	$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
	
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
			
			mysql_select_db($database_radiator, $radiator);
			$query_check_group_users = "SELECT * FROM SUBSCRIBERS WHERE SUBSCRIBERS.SUBSCRIBERGROUP = '".$_POST['id_'.($i+1)]."'";
			$check_group_users = mysql_query($query_check_group_users, $radiator) or die(mysql_error());
			$row_check_group_users = mysql_fetch_assoc($check_group_users);
			$totalRows_check_group_users = mysql_num_rows($check_group_users);
	
			if ($totalRows_check_group_users > 0) {
				
			}
			else {
				$deleteSQL = sprintf("DELETE FROM SUBSCRIBERGROUPS WHERE GRPNAME=%s",
						   GetSQLValueString($_POST['id_'.($i+1)], "text"));
	
				mysql_select_db($database_radiator, $radiator);
				$Result1 = mysql_query($deleteSQL, $radiator) or die(mysql_error());
			}
		}
	}
	
  $deleteGoTo = "radiatorMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

mysql_select_db($database_subman, $subman);
$query_radiators = "SELECT * FROM radiator ORDER BY radiator.`host`, radiator.descr";
$radiators = mysql_query($query_radiators, $subman) or die(mysql_error());
$row_radiators = mysql_fetch_assoc($radiators);
$totalRows_radiators = mysql_num_rows($radiators);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IP Manager</title>
<link href="css/default.css" rel="stylesheet" type="text/css" />
<link href="dropdowntabfiles/bluetabs.css" rel="stylesheet" type="text/css" />
<link href="css/rounded.css" rel="stylesheet" type="text/css" />
<link href="css/template.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="css/jqueryslidemenu.css" />

<script type="text/javascript">
<!--
function MM_validateForm() { //v4.0
  if (document.getElementById){
    var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
    for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=document.getElementById(args[i]);
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
} }

function checkAll(theForm, status) {
for (i=0,n=theForm.elements.length;i<n;i++)
theForm.elements[i].checked = status;
}
function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>
<!-- SmartMenus 6 config and script core files -->
<script type="text/javascript" src="c_config.js"></script>
<script type="text/javascript" src="c_smartmenus.js"></script>
<!-- SmartMenus 6 config and script core files -->

<!-- SmartMenus 6 Scrolling for Overlong Menus add-on -->
<script type="text/javascript" src="c_addon_scrolling.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="jqueryslidemenu.js"></script>

<script type="text/javascript">
function showCustomers(str, end)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("customers" + str + end).innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/customers.php?container=<?php echo $_GET['container']; ?>&start="+str+"&end="+end,true);
xmlhttp.send();
}

function showLinks(str, end)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("links" + str + end).innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/links.php?container=<?php echo $_GET['container']; ?>&start="+str+"&end="+end,true);
xmlhttp.send();
}

function showTemplates(str)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("template" + str).innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/templates.php?container=<?php echo $_GET['container']; ?>&link="+str,true);
xmlhttp.send();
}

function searchQry(str,limit)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("searchQ").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/search.php?container=<?php echo $_GET['container']; ?>&search="+str+"&limit="+limit,true);
xmlhttp.send();
}

</script>
</head>

<body onload="<?php if ($_GET['print'] == 1) { ?>window.print(); window.close();<?php } ?> <?php if ($_SESSION['errstr'] != "") { echo "alert('".$_SESSION['errstr']."');"; } ?>">

<?php include('includes/ipm_header.php'); ?>

<div id="content-box">
		<div class="border">
			<div class="padding">
				<div id="toolbar-box">
				<div class="t">
				<div class="t">
					<div class="t"></div>
				</div>

			</div>
			<div class="m">
				<div class="toolbar-list" id="toolbar">

<div class="clr"></div>
</div>
					<div class="pagetitle">
					<h2>Radiator Management <?php if ($_GET['print'] != 1) { ?> <a href="?<?php echo $_SERVER['QUERY_STRING']; ?>&amp;print=1" target="_blank"><img src="images/icon_print.gif" border="0" alt="Print page" align="absmiddle" style="text-decoration:none" /></a><?php } ?></h2>
					
					
<?php if ($_GET['print'] != 1) { ?>

<div id="myslidemenu" class="jqueryslidemenu">
<ul>

	<?php include('includes/standard_nav.php'); ?>
	<li><a class="NOLINK"><img src="images/radiator_icon.gif" alt="Radiator Servers" align="absmiddle" width="20" height="20" border="0" /> Radiator Servers</a>
        	<?php if ($totalRows_radiators > 0) { // Show if recordset not empty ?>
            <ul>
    <?php do { ?>
      <?php if (getRadiatorLevel($row_radiators['id'],$_SESSION['MM_Username']) > 0 || ($pageLevel > 0 && getRadiatorLevel($row_radiators['id'],$_SESSION['MM_Username']) == "")) { ?>      
      <li><a href="?browse=radiators&amp;radiator=<?php echo $row_radiators['id']; ?>" title="<?php echo $row_radiators['host']; ?>"><?php echo $row_radiators['descr']; ?></a></li>
      <?php } ?>
      <?php } while ($row_radiators = mysql_fetch_assoc($radiators)); ?>
      		</ul>
    <?php } // Show if recordset not empty ?>
    </li>
  <?php include('includes/standard_nav_footer.php'); ?>
</ul>
</div>
<?php } ?>
					</div>
				<div class="clr"></div>
			</div>
			<div class="b">

				<div class="b">
					<div class="b"></div>
				</div>
			</div>
		</div>
		<div class="clr"></div>
				
				
		<div id="element-box">
			<div class="t">
				<div class="t">

					<div class="t"></div>
				</div>
			</div>
			<div class="m">
				<div class="toolbar-list" id="toolbar">

<div class="clr"></div>
</div>
					<div>
					
<p>&nbsp;</p>

<?php if (!($pageLevel > 0)) { ?>
<p class="text_red">Error: You are not authorised to view the selected content.</p>
<?php 
	exit();
} ?>

<div id="containerView">
  <div id="containerView_body">
  
    <?php 
  	if ($_GET['browse'] = "radiators") { 
		
		if (!(getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 0 || ($pageLevel > 0 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
        
		if ($_POST['action'] == "add_radiator") { 
		
			if (!($pageLevel == 127)) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				?>
    <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" onsubmit="MM_validateForm('host','','R','username','','R','pwd','','R','dbname','','R','descr','','R');return document.MM_returnValue">
      <table align="left">
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Host IP Address:</td>
          <td><input name="host" type="text" class="input_standard" id="host" value="" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right" valign="top">Description:</td>
          <td><textarea name="descr" cols="32" rows="5" class="input_standard" id="descr"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Username:</td>
          <td><input name="username" type="text" class="input_standard" id="username" value="" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Password:</td>
          <td><input name="pwd" type="password" class="input_standard" id="pwd" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">DB Name:</td>
          <td><input name="dbname" type="text" class="input_standard" id="dbname" value="" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add Radiator server" /></td>
        </tr>
      </table>
      <input type="hidden" name="MM_insert" value="form1" />
    </form>
    <p>&nbsp;</p>
<?php }
	
	elseif( $_POST['action'] == "add_fixedpool") {
		
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);
		
		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR);
		
		mysql_select_db($database_radiator, $radiator);
		$query_subscriberGroups = "SELECT * FROM SUBSCRIBERGROUPS";
		$subscriberGroups = mysql_query($query_subscriberGroups, $radiator) or die(mysql_error());
		$row_subscriberGroups = mysql_fetch_assoc($subscriberGroups);
		$totalRows_subscriberGroups = mysql_num_rows($subscriberGroups);
		
		if (!(getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == 127 || ($pageLevel == 127 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == ""))) { ?>
        
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				} ?>
			
		Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server"><?php echo $row_getRadiator['descr']; ?></a><br />
   <br />
   		
        <form action="<?php echo $editFormAction; ?>" method="post" name="frm_addfixedpool" id="frm_addfixedpool" onsubmit="MM_validateForm('poolname','','R','startaddress','','R','endaddress','','R');return document.MM_returnValue">
        
        	<table>
            	
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Subscriber Group:</td>
                  <td><select name="subscribergroup" class="input_standard">
						<?php 
                do {  
                ?>
                        <option value="<?php echo $row_subscriberGroups['GRPNAME']?>" <?php if (!(strcmp($row_subscriberGroups['GRPNAME'], htmlentities($row_getSubscriber1['SUBSCRIBERGROUP'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_subscriberGroups['GRPNAME']?></option>
                        <?php
                } while ($row_subscriberGroups = mysql_fetch_assoc($subscriberGroups));
                ?>
                      </select>
      			</td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Start IP Address:</td>
                  <td><input type="text" name="startaddress" id="startaddress" class="input_standard" maxlength="35" size="20" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">End IP Address:</td>
                  <td><input type="text" name="endaddress" id="endaddress" class="input_standard" maxlength="35" size="20" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" value="Add pool" class="input_standard" /></td>
                </tr>
        	</table>
            <input type="hidden" name="MM_insert" value="frm_addfixedpool" />
            <input type="hidden" name="radiator" id="radiator" value="<?php echo $_GET['radiator']; ?>" />
        </form>
   <?php
	}
	
	elseif ($_POST['action'] == "edit_subscriber") {
		
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);
		
		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getSubscriber1 = "SELECT SUBSCRIBERS.*,RADIP.SUBSCRIBERGROUP AS RADIPSUBSCRIBERGRP FROM SUBSCRIBERS LEFT JOIN RADIP ON RADIP.CIRCUITID = SUBSCRIBERS.CIRCUITID WHERE SUBSCRIBERS.USERNAME = '".$_GET['username']."'";
		$getSubscriber1 = mysql_query($query_getSubscriber1, $radiator) or die(mysql_error());
		$row_getSubscriber1 = mysql_fetch_assoc($getSubscriber1);
		$totalRows_getSubscriber1 = mysql_num_rows($getSubscriber1);
		
		if (!(getRadiatorFeatureLevel($_GET['radiator'],'edit_subscriber',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber1['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'edit_subscriber',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'edit_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber1['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == "") || ($pageLevel > 10 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'edit_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber1['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == ""))) { ?>
        
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
		
		mysql_select_db($database_radiator, $radiator);
		$query_subscriberGroups = "SELECT * FROM SUBSCRIBERGROUPS ORDER BY SUBSCRIBERGROUPS.GRPNAME";
		$subscriberGroups = mysql_query($query_subscriberGroups, $radiator) or die(mysql_error());
		$row_subscriberGroups = mysql_fetch_assoc($subscriberGroups);
		$totalRows_subscriberGroups = mysql_num_rows($subscriberGroups);
		
		mysql_select_db($database_radiator, $radiator);
		$query_radip = "SELECT SUBSCRIBERGROUP FROM RADIP GROUP BY SUBSCRIBERGROUP ORDER BY RADIP.SUBSCRIBERGROUP";
		$radip = mysql_query($query_radip, $radiator) or die(mysql_error());
		$row_radip = mysql_fetch_assoc($radip);
		$totalRows_radip = mysql_num_rows($radip);
		
		?>
        
        Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server <?php echo $row_getRadiator['host']; ?>"><?php echo $row_getRadiator['descr']; ?></a> >> Subscriber >> <strong><?php echo $_GET['username']; ?></strong><br />
   <br />
   
<form action="<?php echo $editFormAction; ?>" method="post" name="form5" id="form5" onsubmit="MM_validateForm('CIRCUITID','','R');return document.MM_returnValue">
  <table>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Username:</td>
      <td><strong><?php echo $row_getSubscriber1['USERNAME']; ?></strong></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Password:</td>
      <td><input name="PASSWORD" type="text" class="input_standard" value="<?php echo htmlentities($row_getSubscriber1['PASSWORD'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Circuit ID:</td>
      <td><input name="CIRCUITID" type="text" class="input_standard" value="<?php echo htmlentities($row_getSubscriber1['CIRCUITID'], ENT_COMPAT, 'utf-8'); ?>" size="20" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Framed IP Address:</td>
      <td>
      <select name="IPSUBSCRIBERGROUP" class="input_standard">
      	<option value="">Dynamically assigned from pool</option>
        <?php 
do {  
?>
        <option value="<?php echo $row_radip['SUBSCRIBERGROUP']?>" <?php if (!(strcmp($row_radip['SUBSCRIBERGROUP'], htmlentities($row_getSubscriber1['RADIPSUBSCRIBERGRP'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_radip['SUBSCRIBERGROUP']?></option>
        <?php
} while ($row_radip = mysql_fetch_assoc($radip));
?>
      </select>
      <input name="FRAMEDIP" type="text" class="input_standard" value="<?php echo htmlentities($row_getSubscriber1['FRAMEDIP'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <?php if (!(getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == 127 || ($pageLevel == 127 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == ""))) { ?>
    	<input type="hidden" name="SUBSCRIBERGROUP" value="<?php echo $row_getSubscriber1['SUBSCRIBERGROUP']; ?>" />
        <?php
				}
				else {
					?>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Subscriber Group:</td>
      <td><select name="SUBSCRIBERGROUP" class="input_standard">
        <?php 
do {  
?>
        <option value="<?php echo $row_subscriberGroups['GRPNAME']?>" <?php if (!(strcmp($row_subscriberGroups['GRPNAME'], htmlentities($row_getSubscriber1['SUBSCRIBERGROUP'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_subscriberGroups['GRPNAME']?></option>
        <?php
} while ($row_subscriberGroups = mysql_fetch_assoc($subscriberGroups));
?>
      </select></td>
    </tr>
    <?php } ?>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" class="input_standard" value="Update subscriber" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form5" />
  <input type="hidden" name="USERNAME" value="<?php echo $row_getSubscriber1['USERNAME']; ?>" />
  <input type="hidden" name="OLD_FRAMEDIP" value="<?php echo $row_getSubscriber1['FRAMEDIP']; ?>" />
  <input type="hidden" name="OLD_IPSUBSCRIBERGROUP" value="<?php echo $row_getSubscriber1['RADIPSUBSCRIBERGRP']; ?>" />
  <input type="hidden" name="OLD_SUBSCRIBERGROUP" value="<?php echo $row_getSubscriber1['SUBSCRIBERGROUP']; ?>" />
</form>
<p>&nbsp;</p>
<?php
    }
	elseif ($_POST['action'] == "add_subscriber") { 
		
		if (!(getRadiatorFeatureLevel($_GET['radiator'],'add_subscriber',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$_GET['subscribergroup'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_subscriber',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$_GET['subscribergroup'],$_SESSION['MM_Username']) == "") || ($pageLevel > 10 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'add_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$_GET['subscribergroup'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);
		
		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR);
		
		mysql_select_db($database_radiator, $radiator);
		$query_subscriberGroups = "SELECT * FROM SUBSCRIBERGROUPS ORDER BY SUBSCRIBERGROUPS.GRPNAME";
		$subscriberGroups = mysql_query($query_subscriberGroups, $radiator) or die(mysql_error());
		$row_subscriberGroups = mysql_fetch_assoc($subscriberGroups);
		$totalRows_subscriberGroups = mysql_num_rows($subscriberGroups);
		
		mysql_select_db($database_radiator, $radiator);
		$query_radip = "SELECT SUBSCRIBERGROUP FROM RADIP GROUP BY SUBSCRIBERGROUP ORDER BY RADIP.SUBSCRIBERGROUP";
		$radip = mysql_query($query_radip, $radiator) or die(mysql_error());
		$row_radip = mysql_fetch_assoc($radip);
		$totalRows_radip = mysql_num_rows($radip);
		?>
	
		 Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server <?php echo $row_getRadiator['host']; ?>"><?php echo $row_getRadiator['descr']; ?></a> >> Subscriber Group >> <strong><?php echo $_GET['subscribergroup']; ?></strong><br /><br />

<form action="<?php echo $editFormAction; ?>" method="post" name="form4" id="form4" onsubmit="MM_validateForm('USERNAME','','R','PASSWORD','','R','CIRCUITID','','R');return document.MM_returnValue">
  <table>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Username:</td>
      <td><input name="USERNAME" type="text" class="input_standard" id="USERNAME" value="" size="32" maxlength="50" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Password:</td>
      <td><input name="PASSWORD" type="text" class="input_standard" id="PASSWORD" value="" size="32" maxlength="50" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Circuit ID:</td>
      <td><input name="CIRCUITID" type="text" class="input_standard" id="CIRCUITID" value="" size="20" maxlength="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Framed IP Address:</td>
      <td>
      <select name="IPSUBSCRIBERGROUP" class="input_standard">
      	<option value="">Dynamically assigned from pool</option>
      
        <?php 
do {  
?>
        <option value="<?php echo $row_radip['SUBSCRIBERGROUP']?>"><?php echo $row_radip['SUBSCRIBERGROUP']?></option>
        <?php
} while ($row_radip = mysql_fetch_assoc($radip));
?>
      </select>
      <input name="FRAMEDIP" type="text" class="input_standard" value="" size="32" maxlength="15" /></td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" class="input_standard" value="Add subscriber" /></td>
    </tr>
  </table>
  <input type="hidden" name="SUBSCRIBERGROUP" value="<?php echo $_GET['subscribergroup']?>" />
  <input type="hidden" name="MM_insert" value="form4" />
</form>
<p>&nbsp;</p>
<?php
	}
	elseif ($_POST['action'] == "delete_subscriber") { 
		
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);
		
		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getSubscriber1 = "SELECT * FROM SUBSCRIBERS WHERE SUBSCRIBERS.USERNAME = '".$_GET['username']."'";
		$getSubscriber1 = mysql_query($query_getSubscriber1, $radiator) or die(mysql_error());
		$row_getSubscriber1 = mysql_fetch_assoc($getSubscriber1);
		$totalRows_getSubscriber1 = mysql_num_rows($getSubscriber1);
		
		if (!(getRadiatorFeatureLevel($_GET['radiator'],'delete_subscriber',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber1['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) > 20 && getRadiatorFeatureLevel($_GET['radiator'],'delete_subscriber',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 20 && getRadiatorFeatureLevel($_GET['radiator'],'delete_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber1['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == "") || ($pageLevel > 20 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'delete_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber1['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);
		?>
		
		Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server <?php echo $row_getRadiator['host']; ?>"><?php echo $row_getRadiator['descr']; ?></a> >> Accounting Username >> <strong><?php echo $_GET['username']; ?></strong><br /><br />
		
    <p>Are you sure you want to delete the subscriber <strong><?php echo $_GET['username']; ?></strong>?
   	<ul>
            	<li><strong>The account will be moved to the JT-CLOSED group, no peripheral systems will be affected.</strong></li>
                <li><strong>ALL SUBSCRIBER ROUTES for this account will be deleted.</strong>
                <li><strong>If there is already a session established for this account, it will remain active on the NAS.</strong>
                <li><strong>This action CANNOT be undone.</strong></li>
    </ul>
        </p>
        
    <form name="frmDeleteSubscriber" action="" method="post" target="_self">
        	<input type="submit" class="input_standard" value="Confirm" />
            <input type="hidden" name="username" value="<?php echo $_GET['username']; ?>" />
            <input type="hidden" value="frmDeleteSubscriber" name="frmDeleteSubscriber" />
          <input name="radiator" type="hidden" id="radiator" value="<?php echo $_GET['radiator']; ?>" />
    </form>
    <?php	
	}
	elseif ($_POST['action'] == "add_arbitraryroute") { 
	
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);
		
		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
		
		mysql_select_db($database_radiator, $radiator);
$query_getSubscriber = "SELECT * FROM SUBSCRIBERS WHERE USERNAME = '".$_GET['username']."'";
$getSubscriber = mysql_query($query_getSubscriber, $radiator) or die(mysql_error());
$row_getSubscriber = mysql_fetch_assoc($getSubscriber);
$totalRows_getSubscriber = mysql_num_rows($getSubscriber);
		
		if (!(getRadiatorFeatureLevel($_GET['radiator'],'add_arbitrary_route',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_arbitrary_route',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_arbitrary_route',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == "") || ($pageLevel > 10 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'add_arbitrary_route',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
		mysql_select_db($database_subman, $subman);
		$query_radiators = "SELECT * FROM radiator ORDER BY host";
		$radiators = mysql_query($query_radiators, $subman) or die(mysql_error());
		$row_radiators = mysql_fetch_assoc($radiators);
		$totalRows_radiators = mysql_num_rows($radiators);

		?>
    
    	Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server <?php echo $row_getRadiator['host']; ?>"><?php echo $row_getRadiator['descr']; ?></a> >> Subscriber >> <strong><?php echo $_GET['username']; ?></strong><br />
   <br />
   
   <form action="<?php echo $editFormAction; ?>" method="post" name="add_arbitraryroute" id="add_arbitraryroute">
     <table align="left">
       <tr valign="baseline">
         <td nowrap="nowrap" align="right">Network:</td>
         <td><input type="text" name="network" size="32" maxlength="15" class="input_standard" /></td>
       </tr>
       <tr valign="baseline">
         <td nowrap="nowrap" align="right">Mask:</td>
         <td><input type="text" name="mask" size="32" maxlength="15" class="input_standard" /></td>
       </tr>
       <tr> </tr>
       <tr valign="baseline">
         <td nowrap="nowrap" align="right">&nbsp;</td>
         <td><input type="submit" class="input_standard" value="Add route" /></td>
       </tr>
     </table>
     <input type="hidden" name="radiator" value="<?php echo $_GET['radiator']; ?>" />
     <input type="hidden" name="subscriber" value="<?php echo $_GET['username']; ?>" />
     <input type="hidden" name="MM_insert" value="add_arbitraryroute" />
   </form>
   <p>&nbsp;</p>
<?php
	}
	
	elseif ($_POST['action'] == "add_route") { 
		
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);
		
		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
		
		mysql_select_db($database_radiator, $radiator);
$query_getSubscriber = "SELECT * FROM SUBSCRIBERS WHERE USERNAME = '".$_GET['username']."'";
$getSubscriber = mysql_query($query_getSubscriber, $radiator) or die(mysql_error());
$row_getSubscriber = mysql_fetch_assoc($getSubscriber);
$totalRows_getSubscriber = mysql_num_rows($getSubscriber);
		
		if (!(getRadiatorFeatureLevel($_GET['radiator'],'add_route',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_route',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_route',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == "") || ($pageLevel > 10 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'add_route',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
		mysql_select_db($database_subman, $subman);
		$query_networks = "SELECT networks.*, container.name as containername FROM networks LEFT JOIN container ON container.id = networks.container WHERE (networks.v6mask IS NULL OR networks.v6mask = '') ORDER BY networks.`container`, networks.network";
		$networks = mysql_query($query_networks, $subman) or die(mysql_error());
		$row_networks = mysql_fetch_assoc($networks);
		$totalRows_networks = mysql_num_rows($networks);
		
		mysql_select_db($database_subman, $subman);
		$query_radiators = "SELECT * FROM radiator ORDER BY host";
		$radiators = mysql_query($query_radiators, $subman) or die(mysql_error());
		$row_radiators = mysql_fetch_assoc($radiators);
		$totalRows_radiators = mysql_num_rows($radiators);

		?>
    
    	Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server <?php echo $row_getRadiator['host']; ?>"><?php echo $row_getRadiator['descr']; ?></a> >> Subscriber >> <strong><?php echo $_GET['username']; ?></strong><br />
   <br />
   
   <form action="<?php echo $editFormAction; ?>" method="post" name="form3" id="form3">
     <table align="left">
       <tr valign="baseline">
         <td nowrap="nowrap" align="right">Network:</td>
         <td><select name="network" class="input_standard">
           <?php 
do {  
?>
           <option value="<?php echo $row_networks['id']?>" >[<?php echo $row_networks['containername']?>] <?php echo long2ip($row_networks['network']); ?> : <?php echo long2ip($row_networks['maskLong']);?> : <?php echo $row_networks['descr']?></option>
           <?php
} while ($row_networks = mysql_fetch_assoc($networks));
?>
         </select></td>
       </tr>
       <tr> </tr>
       <tr valign="baseline">
         <td nowrap="nowrap" align="right">&nbsp;</td>
         <td><input type="submit" class="input_standard" value="Add route" /></td>
       </tr>
     </table>
     <input type="hidden" name="radiator" value="<?php echo $_GET['radiator']; ?>" />
     <input type="hidden" name="subscriber" value="<?php echo $_GET['username']; ?>" />
     <input type="hidden" name="MM_insert" value="form3" />
   </form>
   <p>&nbsp;</p>
<?php
	}
	
	elseif ($_POST['action'] == "edit_group") {
		
		if (!(getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == ""))) { ?>
        
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
		
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);
		
		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
		
		mysql_select_db($database_radiator, $radiator);
		$query_getSubscriberGroup = "SELECT * FROM SUBSCRIBERGROUPS WHERE GRPNAME = '".$_GET['subscribergroup']."'";
		$getSubscriberGroup = mysql_query($query_getSubscriberGroup, $radiator) or die(mysql_error());
		$row_getSubscriberGroup = mysql_fetch_assoc($getSubscriberGroup);
		$totalRows_getSubscriberGroup = mysql_num_rows($getSubscriberGroup);
        
        mysql_select_db($database_radiator, $radiator);
		$query_addresspools = "SELECT RADPOOL.POOL FROM RADPOOL GROUP BY RADPOOL.POOL";
		$addresspools = mysql_query($query_addresspools, $radiator) or die(mysql_error());
		$row_addresspools = mysql_fetch_assoc($addresspools);
		$totalRows_addresspools = mysql_num_rows($addresspools);
		
		mysql_select_db($database_subman, $subman);
		$query_vpnvrfs = "SELECT vrf.*, vpn.name as vpnname, provider.asnumber, provider.name as providername FROM vrf LEFT JOIN vpnvrf ON vpnvrf.vrf = vrf.id LEFT JOIN vpn ON vpn.id = vpnvrf.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider ORDER BY provider.name, vpn.name, vrf.name";
		$vpnvrfs = mysql_query($query_vpnvrfs, $subman) or die(mysql_error());
		$row_vpnvrfs = mysql_fetch_assoc($vpnvrfs);
		$totalRows_vpnvrfs = mysql_num_rows($vpnvrfs); ?>
        
<form action="<?php echo $editFormAction; ?>" method="post" name="frm_edit_group" id="frm_edit_group" onsubmit="MM_validateForm('GRPNAME','','R','logins','','RinRange1:100');return document.MM_returnValue">
  <table align="left">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Group Name:</td>
      <td><strong><?php echo $row_getSubscriberGroup['GRPNAME']; ?></strong></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Address Pool:</td>
      <td><select name="POOLHINT" class="input_standard">
        <?php
do {  
?>
        <option value="<?php echo $row_addresspools['POOL']?>"<?php if (!(strcmp($row_addresspools['POOL'], $row_getSubscriberGroup['POOLHINT']))) {echo "selected=\"selected\"";} ?>><?php echo $row_addresspools['POOL']?></option>
        <?php
} while ($row_addresspools = mysql_fetch_assoc($addresspools));
  $rows = mysql_num_rows($addresspools);
  if($rows > 0) {
      mysql_data_seek($addresspools, 0);
	  $row_addresspools = mysql_fetch_assoc($addresspools);
  }
?>
      </select></td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">VPN:</td>
      <td><select name="VPN" class="input_standard">
        <option value="">None</option>
        <?php
do {  
?>
        <option value="<?php echo $row_vpnvrfs['id']?>" <?php if (!(strcmp($row_vpnvrfs['id'], $row_getSubscriberGroup['VRFID']))) {echo "selected=\"selected\"";} ?>><?php echo $row_vpnvrfs['asnumber']?> <?php echo $row_vpnvrfs['providername']?> : <?php echo $row_vpnvrfs['vpnname']?> : <?php echo $row_vpnvrfs['name']?></option>
        <?php
} while ($row_vpnvrfs = mysql_fetch_assoc($vpnvrfs));
  $rows = mysql_num_rows($vpnvrfs);
  if($rows > 0) {
      mysql_data_seek($vpnvrfs, 0);
	  $row_vpnvrfs = mysql_fetch_assoc($vpnvrfs);
  }
?>
      </select></td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Simultanteous Logins:</td>
      <td><input name="logins" type="text" class="input_standard" id="logins" value="<?php echo $row_getSubscriberGroup['SIMULTANEOUSLOGIN']; ?>" size="5" maxlength="3" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" class="input_standard" value="Edit subscriber group" /> <input type="button" class="input_standard" onclick="MM_callJS('history.back();')" value="Cancel" /></td>
    </tr>
  </table>
  <input type="hidden" name="radiator" value="<?php echo $_GET['radiator']; ?>" />
  <input type="hidden" name="GRPNAME" value="<?php echo $row_getSubscriberGroup['GRPNAME']; ?>" />
  <input type="hidden" name="MM_insert" value="frm_edit_group" />
</form>        	
        
    <?php    
	}
	elseif ($_POST['action'] == "add_group") { 
		
		if (!(getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == ""))) { ?>
        
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);
		
		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
		
		mysql_select_db($database_radiator, $radiator);
		$query_addresspools = "SELECT RADPOOL.POOL FROM RADPOOL GROUP BY RADPOOL.POOL";
		$addresspools = mysql_query($query_addresspools, $radiator) or die(mysql_error());
		$row_addresspools = mysql_fetch_assoc($addresspools);
		$totalRows_addresspools = mysql_num_rows($addresspools);
		
		mysql_select_db($database_subman, $subman);
		$query_vpnvrfs = "SELECT vrf.*, vpn.name as vpnname, provider.asnumber, provider.name as providername FROM vrf LEFT JOIN vpnvrf ON vpnvrf.vrf = vrf.id LEFT JOIN vpn ON vpn.id = vpnvrf.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider ORDER BY provider.name, vpn.name, vrf.name";
		$vpnvrfs = mysql_query($query_vpnvrfs, $subman) or die(mysql_error());
		$row_vpnvrfs = mysql_fetch_assoc($vpnvrfs);
		$totalRows_vpnvrfs = mysql_num_rows($vpnvrfs);
		?>
        
        Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server"><?php echo $row_getRadiator['descr']; ?></a><br />
   <br />
<form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2" onsubmit="MM_validateForm('GRPNAME','','R','logins','','RinRange1:100');return document.MM_returnValue">
  <table align="left">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Group Name:</td>
      <td><input name="GRPNAME" type="text" class="input_standard" id="GRPNAME" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Address Pool:</td>
      <td><select name="POOLHINT" class="input_standard">
        <?php 
do {  
?>
        <option value="<?php echo $row_addresspools['POOL']?>" ><?php echo $row_addresspools['POOL']?></option>
        <?php
} while ($row_addresspools = mysql_fetch_assoc($addresspools));
?>
      </select></td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">VPN:</td>
      <td><select name="VPN" class="input_standard">
		<option value="">None</option>  
        <?php 
do {  
?>
        <option value="<?php echo $row_vpnvrfs['id']?>" ><?php echo $row_vpnvrfs['asnumber']?> <?php echo $row_vpnvrfs['providername']?> : <?php echo $row_vpnvrfs['vpnname']?> : <?php echo $row_vpnvrfs['name']?></option>
        <?php
} while ($row_vpnvrfs = mysql_fetch_assoc($vpnvrfs));
?>
      </select></td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Simultanteous Logins:</td>
      <td><input name="logins" type="text" class="input_standard" id="logins" value="2" size="5" maxlength="3" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" class="input_standard" value="Add subscriber group" /></td>
    </tr>
  </table>
  <input type="hidden" name="radiator" value="<?php echo $_GET['radiator']; ?>" />
  <input type="hidden" name="MM_insert" value="form2" />
</form>
<p>&nbsp;</p>
<p>
      <?php 
	}
	
	elseif ($_GET['radiator'] != "" && $_GET['subscribergroup'] == "ALL-SUBSCRIBERS") {
		
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);

		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
		
		if ($_SESSION['search'] != "" && $_SESSION['dd'] != "dd" && $_SESSION['mm'] != "mm" && $_SESSION['yyyy'] != "yyyy") { 
			$maxRows_getGroupSubscribers = 100;
		}
		else {
			$maxRows_getGroupSubscribers = 5;
		}
		
		$pageNum_getGroupSubscribers = 0;
		if (isset($_GET['pageNum_getGroupSubscribers'])) {
		  $pageNum_getGroupSubscribers = $_GET['pageNum_getGroupSubscribers'];
		}
		$startRow_getGroupSubscribers = $pageNum_getGroupSubscribers * $maxRows_getGroupSubscribers;
		
		mysql_select_db($database_radiator, $radiator);
		if ($_SESSION['search'] != "" && $_SESSION['dd'] == "dd" && $_SESSION['mm'] == "mm" && $_SESSION['yyyy'] == "yyyy") { 
			$query_getGroupSubscribers = "SELECT SUBSCRIBERS.*, SUBSCRIBERGROUPS.GRPNAME FROM SUBSCRIBERS LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE (SUBSCRIBERS.USERNAME LIKE '%".$_SESSION['search']."%' OR SUBSCRIBERS.CIRCUITID LIKE '%".$_SESSION['search']."%' OR SUBSCRIBERS.FRAMEDIP LIKE '%".$_SESSION['search']."%') ";
			$query_getGroupSubscribers .= "GROUP BY SUBSCRIBERS.USERNAME ORDER BY SUBSCRIBERS.USERNAME";
		}
		elseif ($_SESSION['search'] != "" && ($_SESSION['dd'] != "" && $_SESSION['mm'] != "" && $_SESSION['yyyy'] != "" && $_SESSION['dd'] != "dd" && $_SESSION['mm'] != "mm" && $_SESSION['yyyy'] != "yyyy")) {
			$query_getGroupSubscribers = "SELECT SUBSCRIBERS.*, ACCOUNTING.ACCTSESSIONID FROM SUBSCRIBERS LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP LEFT JOIN ACCOUNTING ON ACCOUNTING.USERNAME = SUBSCRIBERS.USERNAME WHERE ACCOUNTING.FRAMEDIPADDRESS = '".$_SESSION['search']."' AND ACCOUNTING.TIME_STAMP >= UNIX_TIMESTAMP('".$_SESSION['yyyy']."-".$_SESSION['mm']."-".$_SESSION['dd']."') AND ACCOUNTING.TIME_STAMP <= (UNIX_TIMESTAMP('".$_SESSION['yyyy']."-".$_SESSION['mm']."-".$_SESSION['dd']."')+86400) ";
			#if ($_SESSION['accttype'] != "") {
			#	$query_getGroupSubscribers .= "AND ACCOUNTING.ACCTSTATUSTYPE LIKE '%".$_SESSION['accttype']."%' ";
			#}
			$query_getGroupSubscribers .= "GROUP BY SUBSCRIBERS.USERNAME, ACCOUNTING.ACCTSESSIONID ORDER BY SUBSCRIBERS.USERNAME";
		}
		else {
			$query_getGroupSubscribers = "SELECT SUBSCRIBERS.*, SUBSCRIBERGROUPS.GRPNAME FROM SUBSCRIBERS LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP ORDER BY SUBSCRIBERS.USERNAME";
		}	
		$query_limit_getGroupSubscribers = sprintf("%s LIMIT %d, %d", $query_getGroupSubscribers, $startRow_getGroupSubscribers, $maxRows_getGroupSubscribers);
		$getGroupSubscribers = mysql_query($query_limit_getGroupSubscribers, $radiator) or die(mysql_error());
		$row_getGroupSubscribers = mysql_fetch_assoc($getGroupSubscribers);
		
		#$maxRows_getGroupAccounting = 5;
		#$pageNum_getGroupAccounting = 0;
		#if (isset($_GET['pageNum_getGroupAccounting'])) {
		#  $pageNum_getGroupAccounting = $_GET['pageNum_getGroupAccounting'];
		#}
		#$startRow_getGroupAccounting = $pageNum_getGroupAccounting * $maxRows_getGroupAccounting;
		
		#mysql_select_db($database_radiator, $radiator);
		#if ($_SESSION['search'] != "" && $_SESSION['dd'] == "dd" && $_SESSION['mm'] == "mm" && $_SESSION['yyyy'] == "yyyy" && $_SESSION['dd1'] == "dd" && $_SESSION['mm1'] == "mm" && $_SESSION['yyyy1'] == "yyyy") { 
			#$query_getGroupAccounting = "SELECT ACCOUNTING.*, FROM_UNIXTIME(ACCOUNTING.TIME_STAMP) AS `timestamp` FROM ACCOUNTING RIGHT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = ACCOUNTING.USERNAME RIGHT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE SUBSCRIBERGROUP = '".$_GET['subscribergroup']."' AND (SUBSCRIBERS.USERNAME LIKE '%".$_SESSION['search']."%' OR ACCOUNTING.FRAMEDIPADDRESS LIKE '%".$_SESSION['search']."%') AND ACCOUNTING.ACCTSTATUSTYPE LIKE '%".$_SESSION['accttype']."%'";
		#}
		#elseif ($_SESSION['search'] != "" || ($_SESSION['dd'] != "" && $_SESSION['mm'] != "" && $_SESSION['yyyy'] != "" && $_SESSION['dd'] != "dd" && $_SESSION['mm'] != "mm" && $_SESSION['yyyy'] != "yyyy" && $_SESSION['dd1'] != "" && $_SESSION['mm1'] != "" && $_SESSION['yyyy1'] != "" && $_SESSION['dd1'] != "dd" && $_SESSION['mm1'] != "mm" && $_SESSION['yyyy1'] != "yyyy")) {
			#$query_getGroupAccounting = "SELECT ACCOUNTING.*, FROM_UNIXTIME(ACCOUNTING.TIME_STAMP) AS `timestamp` FROM ACCOUNTING RIGHT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = ACCOUNTING.USERNAME RIGHT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE SUBSCRIBERGROUP = '".$_GET['subscribergroup']."' AND (SUBSCRIBERS.USERNAME LIKE '%".$_SESSION['search']."%' OR ACCOUNTING.FRAMEDIPADDRESS LIKE '%".$_SESSION['search']."%') AND ACCOUNTING.TIME_STAMP BETWEEN UNIX_TIMESTAMP('".$_SESSION['yyyy']."-".$_SESSION['mm']."-".$_SESSION['dd']."') AND UNIX_TIMESTAMP('".$_SESSION['yyyy1']."-".$_SESSION['mm1']."-".$_SESSION['dd1']."') AND ACCOUNTING.ACCTSTATUSTYPE LIKE '%".$_SESSION['accttype']."%'";
		#}
		#else {
		#	$query_getGroupAccounting = "SELECT ACCOUNTING.*, FROM_UNIXTIME(ACCOUNTING.TIME_STAMP) AS `timestamp` FROM ACCOUNTING LEFT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = ACCOUNTING.USERNAME LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE SUBSCRIBERGROUP = '".$_GET['subscribergroup']."' AND ACCOUNTING.ACCTSTATUSTYPE LIKE '%".$_SESSION['accttype']."%'";
		#}
		#$query_limit_getGroupAccounting = sprintf("%s LIMIT %d, %d", $query_getGroupAccounting, $startRow_getGroupAccounting, $maxRows_getGroupAccounting);
		#$getGroupAccounting = mysql_query($query_limit_getGroupAccounting, $radiator) or die(mysql_error());
		#$row_getGroupAccounting = mysql_fetch_assoc($getGroupAccounting);
		
		#if (isset($_GET['totalRows_getGroupAccounting'])) {
		#  $totalRows_getGroupAccounting = $_GET['totalRows_getGroupAccounting'];
		#} else {
		#  $all_getGroupAccounting = mysql_query($query_getGroupAccounting);
		#  $totalRows_getGroupAccounting = mysql_num_rows($all_getGroupAccounting);
		#}
		#$totalPages_getGroupAccounting = ceil($totalRows_getGroupAccounting/$maxRows_getGroupAccounting)-1;
		
		if (isset($_GET['totalRows_getGroupSubscribers'])) {
		  $totalRows_getGroupSubscribers = $_GET['totalRows_getGroupSubscribers'];
		} else {
		  $all_getGroupSubscribers = mysql_query($query_getGroupSubscribers);
		  $totalRows_getGroupSubscribers = mysql_num_rows($all_getGroupSubscribers);
		}
		$totalPages_getGroupSubscribers = ceil($totalRows_getGroupSubscribers/$maxRows_getGroupSubscribers)-1;
		
		$queryString_getGroupSubscribers = "";
		if (!empty($_SERVER['QUERY_STRING'])) {
		  $params = explode("&", $_SERVER['QUERY_STRING']);
		  $newParams = array();
		  foreach ($params as $param) {
			if (stristr($param, "pageNum_getGroupSubscribers") == false && 
				stristr($param, "totalRows_getGroupSubscribers") == false) {
			  array_push($newParams, $param);
			}
		  }
		  if (count($newParams) != 0) {
			$queryString_getGroupSubscribers = "&" . htmlentities(implode("&", $newParams));
		  }
		}
		$queryString_getGroupSubscribers = sprintf("&totalRows_getGroupSubscribers=%d%s", $totalRows_getGroupSubscribers, $queryString_getGroupSubscribers);
		
		#$queryString_getGroupAccounting = "";
		#if (!empty($_SERVER['QUERY_STRING'])) {
		#  $params = explode("&", $_SERVER['QUERY_STRING']);
		#  $newParams = array();
		#  foreach ($params as $param) {
	#		if (stristr($param, "pageNum_getGroupAccounting") == false && 
#				stristr($param, "totalRows_getGroupAccounting") == false) {
		#	  array_push($newParams, $param);
		#	}
		#  }
		#  if (count($newParams) != 0) {
		#	$queryString_getGroupAccounting = "&" . htmlentities(implode("&", $newParams));
		#  }
		#}
		#$queryString_getGroupAccounting = sprintf("&totalRows_getGroupAccounting=%d%s", $totalRows_getGroupAccounting, $queryString_getGroupAccounting);
		
		$maxRows_getActiveSessions = 5;
$pageNum_getActiveSessions = 0;
if (isset($_GET['pageNum_getActiveSessions'])) {
  $pageNum_getActiveSessions = $_GET['pageNum_getActiveSessions'];
}
$startRow_getActiveSessions = $pageNum_getActiveSessions * $maxRows_getActiveSessions;

mysql_select_db($database_radiator, $radiator);
if ($_SESSION['search'] != "" && $_SESSION['dd'] == "dd" && $_SESSION['mm'] == "mm" && $_SESSION['yyyy'] == "yyyy") { 
			$query_getActiveSessions = "SELECT RADONLINE.*, SUBSCRIBERS.FRAMEDIP AS USERFRAMEDIP, SUBSCRIBERGROUPS.GRPNAME, FROM_UNIXTIME(RADONLINE.TIME_STAMP) as timestamp FROM RADONLINE LEFT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = RADONLINE.USERNAME LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE (RADONLINE.USERNAME LIKE '%".$_SESSION['search']."%' OR RADONLINE.NASIDENTIFIER LIKE '%".$_SESSION['search']."%' OR RADONLINE.FRAMEDIPADDRESS LIKE '%".$_SESSION['search']."%' OR SUBSCRIBERS.CIRCUITID LIKE '%".$_SESSION['search']."%') ORDER BY RADONLINE.USERNAME";
		}
		elseif ($_SESSION['search'] != "" && ($_SESSION['dd'] != "" && $_SESSION['mm'] != "" && $_SESSION['yyyy'] != "" && $_SESSION['dd'] != "dd" && $_SESSION['mm'] != "mm" && $_SESSION['yyyy'] != "yyyy")) {
			$query_getActiveSessions = "SELECT RADONLINE.*, SUBSCRIBERS.FRAMEDIP AS USERFRAMEDIP, SUBSCRIBERGROUPS.GRPNAME, FROM_UNIXTIME(RADONLINE.TIME_STAMP) as timestamp FROM RADONLINE LEFT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = RADONLINE.USERNAME LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE (RADONLINE.FRAMEDIPADDRESS = '".$_SESSION['search']."')  ORDER BY RADONLINE.USERNAME";
		}
		else {
			$query_getActiveSessions = "SELECT RADONLINE.*, SUBSCRIBERS.FRAMEDIP AS USERFRAMEDIP, SUBSCRIBERGROUPS.GRPNAME, FROM_UNIXTIME(RADONLINE.TIME_STAMP) as timestamp FROM RADONLINE LEFT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = RADONLINE.USERNAME LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP ORDER BY RADONLINE.USERNAME";
		}
		$query_limit_getActiveSessions = sprintf("%s LIMIT %d, %d", $query_getActiveSessions, $startRow_getActiveSessions, $maxRows_getActiveSessions);
		$getActiveSessions = mysql_query($query_limit_getActiveSessions, $radiator) or die(mysql_error());
		$row_getActiveSessions = mysql_fetch_assoc($getActiveSessions);
		
		if (isset($_GET['totalRows_getActiveSessions'])) {
		  $totalRows_getActiveSessions = $_GET['totalRows_getActiveSessions'];
		} else {
		  $all_getActiveSessions = mysql_query($query_getActiveSessions);
		  $totalRows_getActiveSessions = mysql_num_rows($all_getActiveSessions);
		}
		$totalPages_getActiveSessions = ceil($totalRows_getActiveSessions/$maxRows_getActiveSessions)-1;
		
		$queryString_getActiveSessions = "";
		if (!empty($_SERVER['QUERY_STRING'])) {
		  $params = explode("&", $_SERVER['QUERY_STRING']);
		  $newParams = array();
		  foreach ($params as $param) {
			if (stristr($param, "pageNum_getActiveSessions") == false && 
				stristr($param, "totalRows_getActiveSessions") == false) {
			  array_push($newParams, $param);
			}
		  }
		  if (count($newParams) != 0) {
			$queryString_getActiveSessions = "&" . htmlentities(implode("&", $newParams));
		  }
		}
		$queryString_getActiveSessions = sprintf("&totalRows_getActiveSessions=%d%s", $totalRows_getActiveSessions, $queryString_getActiveSessions);
		?>
        
        Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server <?php echo $row_getRadiator['host']; ?>"><?php echo $row_getRadiator['descr']; ?></a> >> Subscriber Group >> <strong><?php echo $_GET['subscribergroup']; ?></strong></p>
<p><a href="#_active">Active Sessions</a><br />
	<a href="#_subscribers">Subscribers</a>
</p>
<form action="" method="get" name="searchFrm" target="_self" id="searchFrm">
      <div class="searchBoxLeft"><img src="images/semi-left.gif" alt="" /></div>
       <input name="search" type="text" class="searchFormField" value="<?php echo $_SESSION['search']; ?>" />
       <div class="searchBoxCancel">
         <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;subscribergroup=ALL-SUBSCRIBERS&amp;submit=_cancel_search" title="Cancel search"><img src="images/cancel.gif" alt="Cancel search" border="0" /></a>
       </div>
      <div class="searchBoxRight"><img src="images/semi-right.gif" alt="" width="10" height="20" /></div>
       	 &nbsp;
       	   <input type="image" src="images/search.gif" alt="Search"/>
       	 <p class="accounting_search"><strong>Accounting Search</strong><br />
       	   <em>By including a date, you can search for users that were online at specific times using an IP address as the search criterion (only the IP address field will be searched). Leave these fields unchanged if you simply want to find a subscriber. <span class="text_red">Please note that there are typically millions of accounting records in the database and searches may be slow.</span></em><br />
       	   <br />
       	   <input type="hidden" name="browse" value="<?php echo $_GET['browse']; ?>" />
       	   <input type="hidden" name="radiator" value="<?php echo $_GET['radiator']; ?>" />
       	   <input type="hidden" name="subscribergroup" value="ALL-SUBSCRIBERS" />
       	   <select name="dd" class="input_standard">
       	     <option value="dd" <?php if (!(strcmp("DD", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>DD</option>
       	     <option value="01" <?php if (!(strcmp("01", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>01</option>
       	     <option value="02" <?php if (!(strcmp("02", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>02</option>
       	     <option value="03" <?php if (!(strcmp("03", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>03</option>
       	     <option value="04" <?php if (!(strcmp("04", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>04</option>
       	     <option value="05" <?php if (!(strcmp("05", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>05</option>
       	     <option value="06" <?php if (!(strcmp("06", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>06</option>
       	     <option value="07" <?php if (!(strcmp("07", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>07</option>
       	     <option value="08" <?php if (!(strcmp("08", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>08</option>
       	     <option value="09" <?php if (!(strcmp("09", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>09</option>
       	     <option value="10" <?php if (!(strcmp("10", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>10</option>
       	     <option value="11" <?php if (!(strcmp("11", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>11</option>
       	     <option value="12" <?php if (!(strcmp("12", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>12</option>
       	     <option value="13" <?php if (!(strcmp("13", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>13</option>
       	     <option value="14" <?php if (!(strcmp("14", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>14</option>
       	     <option value="15" <?php if (!(strcmp("15", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>15</option>
       	     <option value="16" <?php if (!(strcmp("16", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>16</option>
       	     <option value="17" <?php if (!(strcmp("17", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>17</option>
       	     <option value="18" <?php if (!(strcmp("18", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>18</option>
       	     <option value="19" <?php if (!(strcmp("19", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>19</option>
       	     <option value="20" <?php if (!(strcmp("20", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>20</option>
       	     <option value="21" <?php if (!(strcmp("21", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>21</option>
       	     <option value="22" <?php if (!(strcmp("22", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>22</option>
       	     <option value="23" <?php if (!(strcmp("23", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>23</option>
       	     <option value="24" <?php if (!(strcmp("24", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>24</option>
       	     <option value="25" <?php if (!(strcmp("25", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>25</option>
       	     <option value="26" <?php if (!(strcmp("26", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>26</option>
       	     <option value="27" <?php if (!(strcmp("27", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>27</option>
       	     <option value="28" <?php if (!(strcmp("28", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>28</option>
       	     <option value="29" <?php if (!(strcmp("29", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>29</option>
       	     <option value="30" <?php if (!(strcmp("30", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>30</option>
       	     <option value="31" <?php if (!(strcmp("31", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>31</option>
   	       </select>
       	   <select name="mm" class="input_standard">
       	     <option value="mm" <?php if (!(strcmp("MM", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>MM</option>
       	     <option value="01" <?php if (!(strcmp("01", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>01</option>
       	     <option value="02" <?php if (!(strcmp("02", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>02</option>
       	     <option value="03" <?php if (!(strcmp("03", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>03</option>
       	     <option value="04" <?php if (!(strcmp("04", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>04</option>
       	     <option value="05" <?php if (!(strcmp("05", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>05</option>
       	     <option value="06" <?php if (!(strcmp("06", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>06</option>
       	     <option value="07" <?php if (!(strcmp("07", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>07</option>
       	     <option value="08" <?php if (!(strcmp("08", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>08</option>
       	     <option value="09" <?php if (!(strcmp("09", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>09</option>
       	     <option value="10" <?php if (!(strcmp("10", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>10</option>
       	     <option value="11" <?php if (!(strcmp("11", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>11</option>
       	     <option value="12" <?php if (!(strcmp("12", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>12</option>
   	       </select>
       	   <select name="yyyy" class="input_standard">
       	     <option value="yyyy" <?php if (!(strcmp("YYYY", $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>YYYY</option>
       	     <option value="2005" <?php if (!(strcmp(2005, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2005</option>
       	     <option value="2006" <?php if (!(strcmp(2006, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2006</option>
       	     <option value="2007" <?php if (!(strcmp(2007, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2007</option>
       	     <option value="2008" <?php if (!(strcmp(2008, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2008</option>
       	     <option value="2009" <?php if (!(strcmp(2009, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2009</option>
       	     <option value="2010" <?php if (!(strcmp(2010, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2010</option>
       	     <option value="2011" <?php if (!(strcmp(2011, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2011</option>
       	     <option value="2012" <?php if (!(strcmp(2012, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2012</option>
       	     <option value="2013" <?php if (!(strcmp(2013, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2013</option>
       	     <option value="2014" <?php if (!(strcmp(2014, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2014</option>
       	     <option value="2015" <?php if (!(strcmp(2015, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2015</option>
       	     <option value="2016" <?php if (!(strcmp(2016, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2016</option>
   	       </select>
       	   <input type="hidden" name="submit" value="_search" />
         </p>
</form>

	<h3 id="_active">Active Sessions</h3>
    <?php if ($totalRows_getActiveSessions > 0) { // Show if recordset not empty ?>
        
        <p>Displaying records <?php echo ($startRow_getActiveSessions + 1) ?> to <?php echo min($startRow_getActiveSessions + $maxRows_getActiveSessions, $totalRows_getActiveSessions) ?> of <?php echo $totalRows_getActiveSessions ?></p>
        
      <table border="0">
          <tr>
            <td><?php if ($pageNum_getActiveSessions > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, 0, $queryString_getActiveSessions); ?>"><img src="images/First.gif" border="0" /></a>
                <?php } // Show if not first page ?></td>
            <td><?php if ($pageNum_getActiveSessions > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, max(0, $pageNum_getActiveSessions - 1), $queryString_getActiveSessions); ?>"><img src="images/Previous.gif" border="0" /></a>
                <?php } // Show if not first page ?></td>
            <td><?php if ($pageNum_getActiveSessions < $totalPages_getActiveSessions) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, min($totalPages_getActiveSessions, $pageNum_getActiveSessions + 1), $queryString_getActiveSessions); ?>"><img src="images/Next.gif" border="0" /></a>
                <?php } // Show if not last page ?></td>
            <td><?php if ($pageNum_getActiveSessions < $totalPages_getActiveSessions) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, $totalPages_getActiveSessions, $queryString_getActiveSessions); ?>"><img src="images/Last.gif" border="0" /></a>
                <?php } // Show if not last page ?></td>
          </tr>
      </table>
<table width="100%" border="0">
    <tr>
      <td><strong>Username</strong></td>
      <td><strong>Subscriber Group</strong></td>
      <td><strong>NAS ID</strong></td>
      <td><strong>NAS Port</strong></td>
      <td><strong>Session ID</strong></td>
      <td><strong>Time Stamp (Last Update)</strong></td>
      <td><strong>Framed IP Address</strong></td>
      <td><strong>Port Type</strong></td>
      </tr>
  <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;username=<?php echo $row_getActiveSessions['USERNAME']; ?>" title="Browse user accounting data"><?php echo $row_getActiveSessions['USERNAME']; ?></a></td>
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;subscribergroup=<?php echo $row_getActiveSessions['GRPNAME']; ?>" title="Browse this subscriber group"><?php echo $row_getActiveSessions['GRPNAME']; ?></a></td>
      <td><?php echo $row_getActiveSessions['NASIDENTIFIER']; ?></td>
      <td><?php echo $row_getActiveSessions['NASPORT']; ?></td>
      <td><?php echo $row_getActiveSessions['ACCTSESSIONID']; ?></td>
      <td><?php echo $row_getActiveSessions['timestamp']; ?></td>
      <td><?php echo $row_getActiveSessions['FRAMEDIPADDRESS']; ?> <?php if ($row_getActiveSessions['FRAMEDIPADDRESS'] == $row_getActiveSessions['USERFRAMEDIP']) { echo "<font color=\"red\">(fixed)</font>"; } else { echo "<font color=\"blue\">(dynamic)</font>"; } ?></td>
      <td><?php echo $row_getActiveSessions['NASPORTTYPE']; ?></td>
      </tr>
    <?php } while ($row_getActiveSessions = mysql_fetch_assoc($getActiveSessions)); ?>
</table>
  <?php } // Show if recordset not empty 
  		else { ?>
        	<p>There are no active sessions for this group.</p>
    	<?php } ?>
        
<h3 id="_subscribers">Subscribers</h3>
   
    <?php if ($totalRows_getGroupSubscribers > 0) { // Show if recordset not empty ?>
      
      <p>Displaying records <?php echo ($startRow_getGroupSubscribers + 1) ?> to  <?php echo min($startRow_getGroupSubscribers + $maxRows_getGroupSubscribers, $totalRows_getGroupSubscribers) ?> of <?php echo $totalRows_getGroupSubscribers ?></p>
      
      <table border="0">
        <tr>
          <td><?php if ($pageNum_getGroupSubscribers > 0) { // Show if not first page ?>
              <a href="<?php printf("%s?pageNum_getGroupSubscribers=%d%s", $currentPage, 0, $queryString_getGroupSubscribers); ?>"><img src="images/First.gif" alt="" border="0" /></a>
              <?php } // Show if not first page ?></td>
          <td><?php if ($pageNum_getGroupSubscribers > 0) { // Show if not first page ?>
              <a href="<?php printf("%s?pageNum_getGroupSubscribers=%d%s", $currentPage, max(0, $pageNum_getGroupSubscribers - 1), $queryString_getGroupSubscribers); ?>"><img src="images/Previous.gif" alt="" border="0" /></a>
              <?php } // Show if not first page ?></td>
          <td><?php if ($pageNum_getGroupSubscribers < $totalPages_getGroupSubscribers) { // Show if not last page ?>
              <a href="<?php printf("%s?pageNum_getGroupSubscribers=%d%s", $currentPage, min($totalPages_getGroupSubscribers, $pageNum_getGroupSubscribers + 1), $queryString_getGroupSubscribers); ?>"><img src="images/Next.gif" alt="" border="0" /></a>
              <?php } // Show if not last page ?></td>
          <td><?php if ($pageNum_getGroupSubscribers < $totalPages_getGroupSubscribers) { // Show if not last page ?>
              <a href="<?php printf("%s?pageNum_getGroupSubscribers=%d%s", $currentPage, $totalPages_getGroupSubscribers, $queryString_getGroupSubscribers); ?>"><img src="images/Last.gif" alt="" border="0" /></a>
              <?php } // Show if not last page ?></td>
        </tr>
      </table>
      <table border="0" width="100%">
        <tr>
        <?php if ($_SESSION['search'] != "" && ($_SESSION['dd'] != "" && $_SESSION['mm'] != "" && $_SESSION['yyyy'] != "" && $_SESSION['dd'] != "dd" && $_SESSION['mm'] != "mm" && $_SESSION['yyyy'] != "yyyy")) { ?>
          <td><strong>Session Start Time</strong></td>
          <td><strong>Session End Time</strong></td>
          <?php } ?>
          <td><strong>Username</strong></td>
          <td><strong>Password</strong></td>
          <td><strong>Subscriber Group</strong></td>
          <td><strong>Circuit ID</strong></td>
          <td><strong>Reply Attributes</strong></td>
          <td><strong>Framed IP Address</strong></td>
          <td><strong>Routes</strong></td>
        </tr>
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
								
								if ($_SESSION['search'] != "" && ($_SESSION['dd'] != "" && $_SESSION['mm'] != "" && $_SESSION['yyyy'] != "" && $_SESSION['dd'] != "dd" && $_SESSION['mm'] != "mm" && $_SESSION['yyyy'] != "yyyy")) {
									
									mysql_select_db($database_radiator, $radiator);
									$query_getStartStop = "SELECT FROM_UNIXTIME(MIN(TIME_STAMP)) AS STARTTIME, FROM_UNIXTIME(MAX(TIME_STAMP)) AS STOPTIME FROM ACCOUNTING WHERE ACCTSESSIONID = '".$row_getGroupSubscribers['ACCTSESSIONID']."' AND USERNAME = '".$row_getGroupSubscribers['USERNAME']."'";
									$getStartStop = mysql_query($query_getStartStop, $radiator) or die(mysql_error());
									$row_getStartStop = mysql_fetch_assoc($getStartStop);
									$totalRows_getStartStop = mysql_num_rows($getStartStop);
									
								}
		?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
        <?php if ($_SESSION['search'] != "" && ($_SESSION['dd'] != "" && $_SESSION['mm'] != "" && $_SESSION['yyyy'] != "" && $_SESSION['dd'] != "dd" && $_SESSION['mm'] != "mm" && $_SESSION['yyyy'] != "yyyy")) { ?>
          <td><strong><?php echo $row_getStartStop['STARTTIME']; ?></strong></td>
          <td><strong><?php echo $row_getStartStop['STOPTIME']; ?></strong></td>
          <?php } ?>
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;username=<?php echo $row_getGroupSubscribers['USERNAME']; ?>" title="Browse user accounting data"><?php echo $row_getGroupSubscribers['USERNAME']; ?></a></td>
          <td><?php echo $row_getGroupSubscribers['PASSWORD']; ?></td>
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;subscribergroup=<?php echo $row_getActiveSessions['GRPNAME']; ?>" title="Browse this subscriber group"><?php echo $row_getGroupSubscribers['GRPNAME']; ?></a></td>
          <td><?php echo $row_getGroupSubscribers['CIRCUITID']; ?></td>
          <td><?php echo $row_getGroupSubscribers['REPLYATTR']; ?></td>
          <td><?php echo $row_getGroupSubscribers['FRAMEDIP']; ?></td>
          <td><?php echo $row_getGroupSubscribers['ROUTES']; ?></td>
        </tr>
        <?php } while ($row_getGroupSubscribers = mysql_fetch_assoc($getGroupSubscribers)); ?>
      </table>
      <?php } // Show if recordset not empty 
	  		else { ?>
            
            	<p>There are no subscribers for this group.</p>
            
      <?php } ?>

<?php
	}
	
	elseif ($_GET['radiator'] != "" && $_GET['subscribergroup'] != "") {
		
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);

		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
		
		$maxRows_getGroupSubscribers = 5;
		$pageNum_getGroupSubscribers = 0;
		if (isset($_GET['pageNum_getGroupSubscribers'])) {
		  $pageNum_getGroupSubscribers = $_GET['pageNum_getGroupSubscribers'];
		}
		$startRow_getGroupSubscribers = $pageNum_getGroupSubscribers * $maxRows_getGroupSubscribers;
		
		mysql_select_db($database_radiator, $radiator);
		if ($_SESSION['search'] != "") { 
			$query_getGroupSubscribers = "SELECT SUBSCRIBERS.* FROM SUBSCRIBERS WHERE SUBSCRIBERS.SUBSCRIBERGROUP = '".$_GET['subscribergroup']."' AND (SUBSCRIBERS.USERNAME LIKE '%".$_SESSION['search']."%' OR SUBSCRIBERS.CIRCUITID LIKE '%".$_SESSION['search']."%' OR SUBSCRIBERS.FRAMEDIP LIKE '%".$_SESSION['search']."%') ";
			$query_getGroupSubscribers .= "GROUP BY SUBSCRIBERS.USERNAME ORDER BY SUBSCRIBERS.USERNAME";
		}
		else {
			$query_getGroupSubscribers = "SELECT * FROM SUBSCRIBERS WHERE SUBSCRIBERS.SUBSCRIBERGROUP = '".$_GET['subscribergroup']."' ORDER BY SUBSCRIBERS.USERNAME";
		}	
		$query_limit_getGroupSubscribers = sprintf("%s LIMIT %d, %d", $query_getGroupSubscribers, $startRow_getGroupSubscribers, $maxRows_getGroupSubscribers);
		$getGroupSubscribers = mysql_query($query_limit_getGroupSubscribers, $radiator) or die(mysql_error());
		$row_getGroupSubscribers = mysql_fetch_assoc($getGroupSubscribers);
		
		#$maxRows_getGroupAccounting = 5;
		#$pageNum_getGroupAccounting = 0;
		#if (isset($_GET['pageNum_getGroupAccounting'])) {
		#  $pageNum_getGroupAccounting = $_GET['pageNum_getGroupAccounting'];
		#}
		#$startRow_getGroupAccounting = $pageNum_getGroupAccounting * $maxRows_getGroupAccounting;
		
		#mysql_select_db($database_radiator, $radiator);
		#if ($_SESSION['search'] != "" && $_SESSION['dd'] == "dd" && $_SESSION['mm'] == "mm" && $_SESSION['yyyy'] == "yyyy" && $_SESSION['dd1'] == "dd" && $_SESSION['mm1'] == "mm" && $_SESSION['yyyy1'] == "yyyy") { 
			#$query_getGroupAccounting = "SELECT ACCOUNTING.*, FROM_UNIXTIME(ACCOUNTING.TIME_STAMP) AS `timestamp` FROM ACCOUNTING RIGHT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = ACCOUNTING.USERNAME RIGHT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE SUBSCRIBERGROUP = '".$_GET['subscribergroup']."' AND (SUBSCRIBERS.USERNAME LIKE '%".$_SESSION['search']."%' OR ACCOUNTING.FRAMEDIPADDRESS LIKE '%".$_SESSION['search']."%') AND ACCOUNTING.ACCTSTATUSTYPE LIKE '%".$_SESSION['accttype']."%'";
		#}
		#elseif ($_SESSION['search'] != "" || ($_SESSION['dd'] != "" && $_SESSION['mm'] != "" && $_SESSION['yyyy'] != "" && $_SESSION['dd'] != "dd" && $_SESSION['mm'] != "mm" && $_SESSION['yyyy'] != "yyyy" && $_SESSION['dd1'] != "" && $_SESSION['mm1'] != "" && $_SESSION['yyyy1'] != "" && $_SESSION['dd1'] != "dd" && $_SESSION['mm1'] != "mm" && $_SESSION['yyyy1'] != "yyyy")) {
			#$query_getGroupAccounting = "SELECT ACCOUNTING.*, FROM_UNIXTIME(ACCOUNTING.TIME_STAMP) AS `timestamp` FROM ACCOUNTING RIGHT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = ACCOUNTING.USERNAME RIGHT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE SUBSCRIBERGROUP = '".$_GET['subscribergroup']."' AND (SUBSCRIBERS.USERNAME LIKE '%".$_SESSION['search']."%' OR ACCOUNTING.FRAMEDIPADDRESS LIKE '%".$_SESSION['search']."%') AND ACCOUNTING.TIME_STAMP BETWEEN UNIX_TIMESTAMP('".$_SESSION['yyyy']."-".$_SESSION['mm']."-".$_SESSION['dd']."') AND UNIX_TIMESTAMP('".$_SESSION['yyyy1']."-".$_SESSION['mm1']."-".$_SESSION['dd1']."') AND ACCOUNTING.ACCTSTATUSTYPE LIKE '%".$_SESSION['accttype']."%'";
		#}
		#else {
		#	$query_getGroupAccounting = "SELECT ACCOUNTING.*, FROM_UNIXTIME(ACCOUNTING.TIME_STAMP) AS `timestamp` FROM ACCOUNTING LEFT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = ACCOUNTING.USERNAME LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE SUBSCRIBERGROUP = '".$_GET['subscribergroup']."' AND ACCOUNTING.ACCTSTATUSTYPE LIKE '%".$_SESSION['accttype']."%'";
		#}
		#$query_limit_getGroupAccounting = sprintf("%s LIMIT %d, %d", $query_getGroupAccounting, $startRow_getGroupAccounting, $maxRows_getGroupAccounting);
		#$getGroupAccounting = mysql_query($query_limit_getGroupAccounting, $radiator) or die(mysql_error());
		#$row_getGroupAccounting = mysql_fetch_assoc($getGroupAccounting);
		
		#if (isset($_GET['totalRows_getGroupAccounting'])) {
		#  $totalRows_getGroupAccounting = $_GET['totalRows_getGroupAccounting'];
		#} else {
		#  $all_getGroupAccounting = mysql_query($query_getGroupAccounting);
		#  $totalRows_getGroupAccounting = mysql_num_rows($all_getGroupAccounting);
		#}
		#$totalPages_getGroupAccounting = ceil($totalRows_getGroupAccounting/$maxRows_getGroupAccounting)-1;
		
		if (isset($_GET['totalRows_getGroupSubscribers'])) {
		  $totalRows_getGroupSubscribers = $_GET['totalRows_getGroupSubscribers'];
		} else {
		  $all_getGroupSubscribers = mysql_query($query_getGroupSubscribers);
		  $totalRows_getGroupSubscribers = mysql_num_rows($all_getGroupSubscribers);
		}
		$totalPages_getGroupSubscribers = ceil($totalRows_getGroupSubscribers/$maxRows_getGroupSubscribers)-1;
		
		$queryString_getGroupSubscribers = "";
		if (!empty($_SERVER['QUERY_STRING'])) {
		  $params = explode("&", $_SERVER['QUERY_STRING']);
		  $newParams = array();
		  foreach ($params as $param) {
			if (stristr($param, "pageNum_getGroupSubscribers") == false && 
				stristr($param, "totalRows_getGroupSubscribers") == false) {
			  array_push($newParams, $param);
			}
		  }
		  if (count($newParams) != 0) {
			$queryString_getGroupSubscribers = "&" . htmlentities(implode("&", $newParams));
		  }
		}
		$queryString_getGroupSubscribers = sprintf("&totalRows_getGroupSubscribers=%d%s", $totalRows_getGroupSubscribers, $queryString_getGroupSubscribers);
		
		#$queryString_getGroupAccounting = "";
		#if (!empty($_SERVER['QUERY_STRING'])) {
		#  $params = explode("&", $_SERVER['QUERY_STRING']);
		#  $newParams = array();
		#  foreach ($params as $param) {
	#		if (stristr($param, "pageNum_getGroupAccounting") == false && 
#				stristr($param, "totalRows_getGroupAccounting") == false) {
		#	  array_push($newParams, $param);
		#	}
		#  }
		#  if (count($newParams) != 0) {
		#	$queryString_getGroupAccounting = "&" . htmlentities(implode("&", $newParams));
		#  }
		#}
		#$queryString_getGroupAccounting = sprintf("&totalRows_getGroupAccounting=%d%s", $totalRows_getGroupAccounting, $queryString_getGroupAccounting);
		
		$maxRows_getActiveSessions = 5;
$pageNum_getActiveSessions = 0;
if (isset($_GET['pageNum_getActiveSessions'])) {
  $pageNum_getActiveSessions = $_GET['pageNum_getActiveSessions'];
}
$startRow_getActiveSessions = $pageNum_getActiveSessions * $maxRows_getActiveSessions;

mysql_select_db($database_radiator, $radiator);
if ($_SESSION['search'] != "") { 
			$query_getActiveSessions = "SELECT RADONLINE.*, SUBSCRIBERS.FRAMEDIP AS USERFRAMEDIP, FROM_UNIXTIME(RADONLINE.TIME_STAMP) as timestamp FROM RADONLINE LEFT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = RADONLINE.USERNAME LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE SUBSCRIBERGROUPS.GRPNAME = '".$_GET['subscribergroup']."' AND (RADONLINE.USERNAME LIKE '%".$_SESSION['search']."%' OR RADONLINE.NASIDENTIFIER LIKE '%".$_SESSION['search']."%' OR RADONLINE.FRAMEDIPADDRESS LIKE '%".$_SESSION['search']."%') ORDER BY RADONLINE.USERNAME";
		}
		else {
			$query_getActiveSessions = "SELECT RADONLINE.*, SUBSCRIBERS.FRAMEDIP AS USERFRAMEDIP, FROM_UNIXTIME(RADONLINE.TIME_STAMP) as timestamp FROM RADONLINE LEFT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = RADONLINE.USERNAME LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE SUBSCRIBERGROUPS.GRPNAME = '".$_GET['subscribergroup']."' ORDER BY RADONLINE.USERNAME";
		}
		$query_limit_getActiveSessions = sprintf("%s LIMIT %d, %d", $query_getActiveSessions, $startRow_getActiveSessions, $maxRows_getActiveSessions);
		$getActiveSessions = mysql_query($query_limit_getActiveSessions, $radiator) or die(mysql_error());
		$row_getActiveSessions = mysql_fetch_assoc($getActiveSessions);
		
		if (isset($_GET['totalRows_getActiveSessions'])) {
		  $totalRows_getActiveSessions = $_GET['totalRows_getActiveSessions'];
		} else {
		  $all_getActiveSessions = mysql_query($query_getActiveSessions);
		  $totalRows_getActiveSessions = mysql_num_rows($all_getActiveSessions);
		}
		$totalPages_getActiveSessions = ceil($totalRows_getActiveSessions/$maxRows_getActiveSessions)-1;
		
		$queryString_getActiveSessions = "";
		if (!empty($_SERVER['QUERY_STRING'])) {
		  $params = explode("&", $_SERVER['QUERY_STRING']);
		  $newParams = array();
		  foreach ($params as $param) {
			if (stristr($param, "pageNum_getActiveSessions") == false && 
				stristr($param, "totalRows_getActiveSessions") == false) {
			  array_push($newParams, $param);
			}
		  }
		  if (count($newParams) != 0) {
			$queryString_getActiveSessions = "&" . htmlentities(implode("&", $newParams));
		  }
		}
		$queryString_getActiveSessions = sprintf("&totalRows_getActiveSessions=%d%s", $totalRows_getActiveSessions, $queryString_getActiveSessions);
		?>
        
        Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server <?php echo $row_getRadiator['host']; ?>"><?php echo $row_getRadiator['descr']; ?></a> >> Subscriber Group >> <strong><?php echo $_GET['subscribergroup']; ?></strong></p>
<p><a href="#_active">Active Sessions</a><br />
	<a href="#_subscribers">Group Subscribers</a>
</p>
<form action="" method="get" name="searchFrm" target="_self" id="searchFrm">
      <div class="searchBoxLeft"><img src="images/semi-left.gif" alt="" /></div>
       <input name="search" type="text" class="searchFormField" value="<?php echo $_SESSION['search']; ?>" />
       <div class="searchBoxCancel">
         <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;subscribergroup=<?php echo $_GET['subscribergroup']; ?>&amp;submit=_cancel_search" title="Cancel search"><img src="images/cancel.gif" alt="Cancel search" border="0" /></a>
       </div>
      <div class="searchBoxRight"><img src="images/semi-right.gif" alt="" width="10" height="20" /></div>
       	 &nbsp;
       	   <input type="image" src="images/search.gif" alt="Search"/>
       	   <input type="hidden" name="browse" value="<?php echo $_GET['browse']; ?>" />
       	   <input type="hidden" name="radiator" value="<?php echo $_GET['radiator']; ?>" />
       	   <input type="hidden" name="subscribergroup" value="<?php echo $_GET['subscribergroup']; ?>" />
       	   <input type="hidden" name="submit" value="_search" />
         </p>
</form>

	<h3 id="_active">Active Sessions</h3>
    <?php if ($totalRows_getActiveSessions > 0) { // Show if recordset not empty ?>
        
        <p>Displaying records <?php echo ($startRow_getActiveSessions + 1) ?> to <?php echo min($startRow_getActiveSessions + $maxRows_getActiveSessions, $totalRows_getActiveSessions) ?> of <?php echo $totalRows_getActiveSessions ?></p>
        
      <table border="0">
          <tr>
            <td><?php if ($pageNum_getActiveSessions > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, 0, $queryString_getActiveSessions); ?>"><img src="images/First.gif" border="0" /></a>
                <?php } // Show if not first page ?></td>
            <td><?php if ($pageNum_getActiveSessions > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, max(0, $pageNum_getActiveSessions - 1), $queryString_getActiveSessions); ?>"><img src="images/Previous.gif" border="0" /></a>
                <?php } // Show if not first page ?></td>
            <td><?php if ($pageNum_getActiveSessions < $totalPages_getActiveSessions) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, min($totalPages_getActiveSessions, $pageNum_getActiveSessions + 1), $queryString_getActiveSessions); ?>"><img src="images/Next.gif" border="0" /></a>
                <?php } // Show if not last page ?></td>
            <td><?php if ($pageNum_getActiveSessions < $totalPages_getActiveSessions) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, $totalPages_getActiveSessions, $queryString_getActiveSessions); ?>"><img src="images/Last.gif" border="0" /></a>
                <?php } // Show if not last page ?></td>
          </tr>
      </table>
<table width="100%" border="0">
    <tr>
      <td><strong>Username</strong></td>
      <td><strong>NAS ID</strong></td>
      <td><strong>NAS Port</strong></td>
      <td><strong>Session ID</strong></td>
      <td><strong>Time Stamp (Last Update)</strong></td>
      <td><strong>Framed IP Address</strong></td>
      <td><strong>Port Type</strong></td>
      </tr>
  <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;username=<?php echo $row_getActiveSessions['USERNAME']; ?>" title="Browse user accounting data"><?php echo $row_getActiveSessions['USERNAME']; ?></a></td>
      <td><?php echo $row_getActiveSessions['NASIDENTIFIER']; ?></td>
      <td><?php echo $row_getActiveSessions['NASPORT']; ?></td>
      <td><?php echo $row_getActiveSessions['ACCTSESSIONID']; ?></td>
      <td><?php echo $row_getActiveSessions['timestamp']; ?></td>
      <td><?php echo $row_getActiveSessions['FRAMEDIPADDRESS']; ?> <?php if ($row_getActiveSessions['FRAMEDIPADDRESS'] == $row_getActiveSessions['USERFRAMEDIP']) { echo "<font color=\"red\">(fixed)</font>"; } else { echo "<font color=\"blue\">(dynamic)</font>"; } ?></td>
      <td><?php echo $row_getActiveSessions['NASPORTTYPE']; ?></td>
      </tr>
    <?php } while ($row_getActiveSessions = mysql_fetch_assoc($getActiveSessions)); ?>
</table>
  <?php } // Show if recordset not empty 
  		else { ?>
        	<p>There are no active sessions for this group.</p>
    	<?php } ?>
        
<h3 id="_subscribers">Group Subscribers</h3>
   
    <?php if ($totalRows_getGroupSubscribers > 0) { // Show if recordset not empty ?>
      
      <p>Displaying records <?php echo ($startRow_getGroupSubscribers + 1) ?> to  <?php echo min($startRow_getGroupSubscribers + $maxRows_getGroupSubscribers, $totalRows_getGroupSubscribers) ?> of <?php echo $totalRows_getGroupSubscribers ?></p>
      
      <table border="0">
        <tr>
          <td><?php if ($pageNum_getGroupSubscribers > 0) { // Show if not first page ?>
              <a href="<?php printf("%s?pageNum_getGroupSubscribers=%d%s", $currentPage, 0, $queryString_getGroupSubscribers); ?>"><img src="images/First.gif" alt="" border="0" /></a>
              <?php } // Show if not first page ?></td>
          <td><?php if ($pageNum_getGroupSubscribers > 0) { // Show if not first page ?>
              <a href="<?php printf("%s?pageNum_getGroupSubscribers=%d%s", $currentPage, max(0, $pageNum_getGroupSubscribers - 1), $queryString_getGroupSubscribers); ?>"><img src="images/Previous.gif" alt="" border="0" /></a>
              <?php } // Show if not first page ?></td>
          <td><?php if ($pageNum_getGroupSubscribers < $totalPages_getGroupSubscribers) { // Show if not last page ?>
              <a href="<?php printf("%s?pageNum_getGroupSubscribers=%d%s", $currentPage, min($totalPages_getGroupSubscribers, $pageNum_getGroupSubscribers + 1), $queryString_getGroupSubscribers); ?>"><img src="images/Next.gif" alt="" border="0" /></a>
              <?php } // Show if not last page ?></td>
          <td><?php if ($pageNum_getGroupSubscribers < $totalPages_getGroupSubscribers) { // Show if not last page ?>
              <a href="<?php printf("%s?pageNum_getGroupSubscribers=%d%s", $currentPage, $totalPages_getGroupSubscribers, $queryString_getGroupSubscribers); ?>"><img src="images/Last.gif" alt="" border="0" /></a>
              <?php } // Show if not last page ?></td>
        </tr>
      </table>
      <table border="0" width="100%">
        <tr>
          <td><strong>Username</strong></td>
          <td><strong>Password</strong></td>
          <td><strong>Circuit ID</strong></td>
          <td><strong>Reply Attributes</strong></td>
          <td><strong>Framed IP Address</strong></td>
          <td><strong>Routes</strong></td>
        </tr>
        <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;username=<?php echo $row_getGroupSubscribers['USERNAME']; ?>" title="Browse user accounting data"><?php echo $row_getGroupSubscribers['USERNAME']; ?></a></td>
          <td><?php echo $row_getGroupSubscribers['PASSWORD']; ?></td>
          <td><?php echo $row_getGroupSubscribers['CIRCUITID']; ?></td>
          <td><?php echo $row_getGroupSubscribers['REPLYATTR']; ?></td>
          <td><?php echo $row_getGroupSubscribers['FRAMEDIP']; ?></td>
          <td><?php echo $row_getGroupSubscribers['ROUTES']; ?></td>
        </tr>
        <?php } while ($row_getGroupSubscribers = mysql_fetch_assoc($getGroupSubscribers)); ?>
      </table>
      <?php } // Show if recordset not empty 
	  		else { ?>
            
            	<p>There are no subscribers for this group.</p>
            
      <?php } ?>

<?php
	}
	
	elseif ($_GET['radiator'] != "" && $_GET['nas'] != "") {
		
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);

		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
		
		$maxRows_getActiveSessions = 10;
$pageNum_getActiveSessions = 0;
if (isset($_GET['pageNum_getActiveSessions'])) {
  $pageNum_getActiveSessions = $_GET['pageNum_getActiveSessions'];
}
$startRow_getActiveSessions = $pageNum_getActiveSessions * $maxRows_getActiveSessions;

mysql_select_db($database_radiator, $radiator);
if ($_SESSION['search'] != "") { 
			$query_getActiveSessions = "SELECT RADONLINE.*, SUBSCRIBERGROUPS.GRPNAME, SUBSCRIBERS.FRAMEDIP AS USERFRAMEDIP, FROM_UNIXTIME(RADONLINE.TIME_STAMP) as timestamp FROM RADONLINE LEFT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = RADONLINE.USERNAME LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE RADONLINE.NASIDENTIFIER = '".$_GET['nas']."' AND (RADONLINE.USERNAME LIKE '%".$_SESSION['search']."%' OR RADONLINE.NASIDENTIFIER LIKE '%".$_SESSION['search']."%' OR RADONLINE.FRAMEDIPADDRESS LIKE '%".$_SESSION['search']."%') ORDER BY RADONLINE.USERNAME";
		}
		else {
			$query_getActiveSessions = "SELECT RADONLINE.*, SUBSCRIBERGROUPS.GRPNAME, SUBSCRIBERS.FRAMEDIP AS USERFRAMEDIP, FROM_UNIXTIME(RADONLINE.TIME_STAMP) as timestamp FROM RADONLINE LEFT JOIN SUBSCRIBERS ON SUBSCRIBERS.USERNAME = RADONLINE.USERNAME LEFT JOIN SUBSCRIBERGROUPS ON SUBSCRIBERGROUPS.GRPNAME = SUBSCRIBERS.SUBSCRIBERGROUP WHERE RADONLINE.NASIDENTIFIER = '".$_GET['nas']."' ORDER BY RADONLINE.USERNAME";
		}
		$query_limit_getActiveSessions = sprintf("%s LIMIT %d, %d", $query_getActiveSessions, $startRow_getActiveSessions, $maxRows_getActiveSessions);
		$getActiveSessions = mysql_query($query_limit_getActiveSessions, $radiator) or die(mysql_error());
		$row_getActiveSessions = mysql_fetch_assoc($getActiveSessions);
		
		if (isset($_GET['totalRows_getActiveSessions'])) {
		  $totalRows_getActiveSessions = $_GET['totalRows_getActiveSessions'];
		} else {
		  $all_getActiveSessions = mysql_query($query_getActiveSessions);
		  $totalRows_getActiveSessions = mysql_num_rows($all_getActiveSessions);
		}
		$totalPages_getActiveSessions = ceil($totalRows_getActiveSessions/$maxRows_getActiveSessions)-1;
		
		$queryString_getActiveSessions = "";
		if (!empty($_SERVER['QUERY_STRING'])) {
		  $params = explode("&", $_SERVER['QUERY_STRING']);
		  $newParams = array();
		  foreach ($params as $param) {
			if (stristr($param, "pageNum_getActiveSessions") == false && 
				stristr($param, "totalRows_getActiveSessions") == false) {
			  array_push($newParams, $param);
			}
		  }
		  if (count($newParams) != 0) {
			$queryString_getActiveSessions = "&" . htmlentities(implode("&", $newParams));
		  }
		}
		$queryString_getActiveSessions = sprintf("&totalRows_getActiveSessions=%d%s", $totalRows_getActiveSessions, $queryString_getActiveSessions);
		?>
        
        Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server <?php echo $row_getRadiator['host']; ?>"><?php echo $row_getRadiator['descr']; ?></a> >> NAS >> <strong><?php echo $_GET['nas']; ?></strong></p>

<p>&nbsp;</p>
        
<form action="" method="get" name="searchFrm" target="_self" id="searchFrm">
      <div class="searchBoxLeft"><img src="images/semi-left.gif" alt="" /></div>
       <input name="search" type="text" class="searchFormField" value="<?php echo $_SESSION['search']; ?>" />
       <div class="searchBoxCancel">
         <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;nas=<?php echo $_GET['nas']; ?>&amp;submit=_cancel_search" title="Cancel search"><img src="images/cancel.gif" alt="Cancel search" border="0" /></a>
       </div>
      <div class="searchBoxRight"><img src="images/semi-right.gif" alt="" width="10" height="20" /></div>
       	<input type="hidden" name="browse" value="<?php echo $_GET['browse']; ?>" />
        <input type="hidden" name="radiator" value="<?php echo $_GET['radiator']; ?>" />
        &nbsp;<input type="hidden" name="nas" value="<?php echo $_GET['nas']; ?>" />
       <input type="image" src="images/search.gif" alt="Search" align="absmiddle"/>
         <input type="hidden" name="submit" value="_search" />
    </form>

	<h3 id="_active">Active Sessions</h3>
    <?php if ($totalRows_getActiveSessions > 0) { // Show if recordset not empty ?>
        
        <p>Displaying records <?php echo ($startRow_getActiveSessions + 1) ?> to <?php echo min($startRow_getActiveSessions + $maxRows_getActiveSessions, $totalRows_getActiveSessions) ?> of <?php echo $totalRows_getActiveSessions ?></p>
        
      <table border="0">
          <tr>
            <td><?php if ($pageNum_getActiveSessions > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, 0, $queryString_getActiveSessions); ?>"><img src="images/First.gif" border="0" /></a>
                <?php } // Show if not first page ?></td>
            <td><?php if ($pageNum_getActiveSessions > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, max(0, $pageNum_getActiveSessions - 1), $queryString_getActiveSessions); ?>"><img src="images/Previous.gif" border="0" /></a>
                <?php } // Show if not first page ?></td>
            <td><?php if ($pageNum_getActiveSessions < $totalPages_getActiveSessions) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, min($totalPages_getActiveSessions, $pageNum_getActiveSessions + 1), $queryString_getActiveSessions); ?>"><img src="images/Next.gif" border="0" /></a>
                <?php } // Show if not last page ?></td>
            <td><?php if ($pageNum_getActiveSessions < $totalPages_getActiveSessions) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_getActiveSessions=%d%s", $currentPage, $totalPages_getActiveSessions, $queryString_getActiveSessions); ?>"><img src="images/Last.gif" border="0" /></a>
                <?php } // Show if not last page ?></td>
          </tr>
      </table>
<table width="100%" border="0">
    <tr>
      <td><strong>Username</strong></td>
      <td><strong>Subscriber Group</strong></td>
      <td><strong>NAS Port</strong></td>
      <td><strong>Session ID</strong></td>
      <td><strong>Time Stamp (Last Update)</strong></td>
      <td><strong>Framed IP Address</strong></td>
      <td><strong>Port Type</strong></td>
      </tr>
  <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;username=<?php echo $row_getActiveSessions['USERNAME']; ?>" title="Browse user accounting data"><?php echo $row_getActiveSessions['USERNAME']; ?></a></td>
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;subscribergroup=<?php echo $row_getActiveSessions['GRPNAME']; ?>" title="Browse subscriber group"><?php echo $row_getActiveSessions['GRPNAME']; ?></a></td>
      <td><?php echo $row_getActiveSessions['NASPORT']; ?></td>
      <td><?php echo $row_getActiveSessions['ACCTSESSIONID']; ?></td>
      <td><?php echo $row_getActiveSessions['timestamp']; ?></td>
      <td><?php echo $row_getActiveSessions['FRAMEDIPADDRESS']; ?> <?php if ($row_getActiveSessions['FRAMEDIPADDRESS'] == $row_getActiveSessions['USERFRAMEDIP']) { echo "<font color=\"red\">(fixed)</font>"; } else { echo "<font color=\"blue\">(dynamic)</font>"; } ?></td>
      <td><?php echo $row_getActiveSessions['NASPORTTYPE']; ?></td>
      </tr>
    <?php } while ($row_getActiveSessions = mysql_fetch_assoc($getActiveSessions)); ?>
</table>
  <?php } // Show if recordset not empty 
  		else { ?>
        	<p>There are no active sessions for this NAS.</p>
    	<?php } ?>
        
<?php
	}
	
	elseif ($_GET['radiator'] != "" && $_GET['username'] != "") { 
		
		if (!(getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 0 || ($pageLevel > 0 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == ""))) { ?>
        
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);
		
		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
		
		$maxRows_getRadiatorAccountingData = 5;
		$pageNum_getRadiatorAccountingData = 0;
		if (isset($_GET['pageNum_getRadiatorAccountingData'])) {
		  $pageNum_getRadiatorAccountingData = $_GET['pageNum_getRadiatorAccountingData'];
		}
		$startRow_getRadiatorAccountingData = $pageNum_getRadiatorAccountingData * $maxRows_getRadiatorAccountingData;
		
		mysql_select_db($database_radiator, $radiator);
		if ($_SESSION['search'] != "" && $_SESSION['dd'] == "dd" && $_SESSION['mm'] == "mm" && $_SESSION['yyyy'] == "yyyy" && $_SESSION['dd1'] == "dd" && $_SESSION['mm1'] == "mm" && $_SESSION['yyyy1'] == "yyyy") { 
			$query_getRadiatorAccountingData = "SELECT FROM_UNIXTIME(ACCOUNTING.TIME_STAMP) as `timestamp`, ACCOUNTING.* FROM ACCOUNTING WHERE ACCOUNTING.USERNAME = '".$_GET['username']."' AND ACCOUNTING.FRAMEDIPADDRESS LIKE '%".$_SESSION['search']."%' AND ACCOUNTING.ACCTSTATUSTYPE LIKE '%".$_SESSION['accttype']."%' ORDER BY TIME_STAMP, USERNAME";
		}
		elseif ($_SESSION['search'] != "" || ($_SESSION['dd'] != "" && $_SESSION['mm'] != "" && $_SESSION['yyyy'] != "" && $_SESSION['dd'] != "dd" && $_SESSION['mm'] != "mm" && $_SESSION['yyyy'] != "yyyy" && $_SESSION['dd1'] != "" && $_SESSION['mm1'] != "" && $_SESSION['yyyy1'] != "" && $_SESSION['dd1'] != "dd" && $_SESSION['mm1'] != "mm" && $_SESSION['yyyy1'] != "yyyy")) {
			$query_getRadiatorAccountingData = "SELECT FROM_UNIXTIME(ACCOUNTING.TIME_STAMP) as `timestamp`, ACCOUNTING.* FROM ACCOUNTING WHERE ACCOUNTING.USERNAME = '".$_GET['username']."' AND ACCOUNTING.FRAMEDIPADDRESS LIKE '%".$_SESSION['search']."%' AND ACCOUNTING.TIME_STAMP BETWEEN UNIX_TIMESTAMP('".$_SESSION['yyyy']."-".$_SESSION['mm']."-".$_SESSION['dd']."') AND UNIX_TIMESTAMP('".$_SESSION['yyyy1']."-".$_SESSION['mm1']."-".$_SESSION['dd1']."') AND ACCOUNTING.ACCTSTATUSTYPE LIKE '%".$_SESSION['accttype']."%' ORDER BY TIME_STAMP, USERNAME";
		}
		else {
			$query_getRadiatorAccountingData = "SELECT FROM_UNIXTIME(ACCOUNTING.TIME_STAMP) as `timestamp`, ACCOUNTING.* FROM ACCOUNTING WHERE ACCOUNTING.USERNAME = '".$_GET['username']."' AND ACCOUNTING.ACCTSTATUSTYPE LIKE '%".$_SESSION['accttype']."%' ORDER BY TIME_STAMP, USERNAME";			
		}
		$query_limit_getRadiatorAccountingData = sprintf("%s LIMIT %d, %d", $query_getRadiatorAccountingData, $startRow_getRadiatorAccountingData, $maxRows_getRadiatorAccountingData);
		$getRadiatorAccountingData = mysql_query($query_limit_getRadiatorAccountingData, $radiator) or die(mysql_error());
		$row_getRadiatorAccountingData = mysql_fetch_assoc($getRadiatorAccountingData);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getSubscriber = "SELECT SUBSCRIBERS.* FROM SUBSCRIBERS WHERE SUBSCRIBERS.USERNAME = '".$_GET['username']."'";
		$getSubscriber = mysql_query($query_getSubscriber, $radiator) or die(mysql_error());
		$row_getSubscriber = mysql_fetch_assoc($getSubscriber);
		$totalRows_getSubscriber = mysql_num_rows($getSubscriber);
		
		mysql_select_db($database_subman, $subman);
		$query_getSubscriberRoutes = "SELECT routes.*, radiator.host FROM routes LEFT JOIN radiator ON radiator.id = routes.radiator WHERE routes.subscriber = '".$_GET['username']."'";
		$getSubscriberRoutes = mysql_query($query_getSubscriberRoutes, $subman) or die(mysql_error());
		$row_getSubscriberRoutes = mysql_fetch_assoc($getSubscriberRoutes);
		$totalRows_getSubscriberRoutes = mysql_num_rows($getSubscriberRoutes);
		
		mysql_select_db($database_subman, $subman);
		$query_getSubscriberArbRoutes = "SELECT arbitraryroutes.*, radiator.host FROM arbitraryroutes LEFT JOIN radiator ON radiator.id = arbitraryroutes.radiator WHERE arbitraryroutes.subscriber = '".$_GET['username']."'";
		$getSubscriberArbRoutes = mysql_query($query_getSubscriberArbRoutes, $subman) or die(mysql_error());
		$row_getSubscriberArbRoutes = mysql_fetch_assoc($getSubscriberArbRoutes);
		$totalRows_getSubscriberArbRoutes = mysql_num_rows($getSubscriberArbRoutes);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getVolumeHour = "SELECT USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')), (max(ACCTINPUTOCTETS) + max(ACCTOUTPUTOCTETS)) - (min(ACCTINPUTOCTETS) + min(ACCTOUTPUTOCTETS)) AS octets, (max(ACCTINPUTGIGAWORDS) + max(ACCTOUTPUTGIGAWORDS)) - (min(ACCTINPUTGIGAWORDS) + min(ACCTOUTPUTGIGAWORDS)) AS gigawords FROM ACCOUNTING WHERE TIME_STAMP >= ".mktime(date("H"), date("i")-60, 0)." AND TIME_STAMP < UNIX_TIMESTAMP(now()) AND USERNAME = '".$_GET['username']."' group by USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')) order by TIME_STAMP desc";
		$getVolumeHour = mysql_query($query_getVolumeHour, $radiator) or die(mysql_error());
		$row_getVolumeHour = mysql_fetch_assoc($getVolumeHour);
		$totalRows_getVolumeHour = mysql_num_rows($getVolumeHour);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getVolumeYesterday = "SELECT USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')), (max(ACCTINPUTOCTETS) + max(ACCTOUTPUTOCTETS)) - (min(ACCTINPUTOCTETS) + min(ACCTOUTPUTOCTETS)) AS octets, (max(ACCTINPUTGIGAWORDS) + max(ACCTOUTPUTGIGAWORDS)) - (min(ACCTINPUTGIGAWORDS) + min(ACCTOUTPUTGIGAWORDS)) AS gigawords FROM ACCOUNTING WHERE TIME_STAMP >= ".mktime(0,0,0,date("m"),date("d")-1)." AND TIME_STAMP < ".mktime(0,0,0,date("m"),date("d"))." AND USERNAME = '".$_GET['username']."' group by USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')) order by TIME_STAMP desc";
		$getVolumeYesterday = mysql_query($query_getVolumeYesterday, $radiator) or die(mysql_error());
		$row_getVolumeYesterday = mysql_fetch_assoc($getVolumeYesterday);
		$totalRows_getVolumeYesterday = mysql_num_rows($getVolumeYesterday);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getVolumeMonth = "SELECT USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')), (max(ACCTINPUTOCTETS) + max(ACCTOUTPUTOCTETS)) - (min(ACCTINPUTOCTETS) + min(ACCTOUTPUTOCTETS)) AS octets, (max(ACCTINPUTGIGAWORDS) + max(ACCTOUTPUTGIGAWORDS)) - (min(ACCTINPUTGIGAWORDS) + min(ACCTOUTPUTGIGAWORDS)) AS gigawords FROM ACCOUNTING WHERE TIME_STAMP >= ".mktime(0,0,0,date("m"),1)." AND TIME_STAMP < UNIX_TIMESTAMP(now()) AND USERNAME = '".$_GET['username']."' group by USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')) order by TIME_STAMP desc";
		$getVolumeMonth = mysql_query($query_getVolumeMonth, $radiator) or die(mysql_error());
		$row_getVolumeMonth = mysql_fetch_assoc($getVolumeMonth);
		$totalRows_getVolumeMonth = mysql_num_rows($getVolumeMonth);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getVolumeBillingMonth = "SELECT USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')), (max(ACCTINPUTOCTETS) + max(ACCTOUTPUTOCTETS)) - (min(ACCTINPUTOCTETS) + min(ACCTOUTPUTOCTETS)) AS octets, (max(ACCTINPUTGIGAWORDS) + max(ACCTOUTPUTGIGAWORDS)) - (min(ACCTINPUTGIGAWORDS) + min(ACCTOUTPUTGIGAWORDS)) AS gigawords FROM ACCOUNTING WHERE TIME_STAMP >= ".mktime(0,0,0,date("m")-1,28)." AND TIME_STAMP < ".mktime(0,0,0,date("m"),29)." AND USERNAME = '".$_GET['username']."' group by USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')) order by TIME_STAMP desc";
		$getVolumeBillingMonth = mysql_query($query_getVolumeBillingMonth, $radiator) or die(mysql_error());
		$row_getVolumeBillingMonth = mysql_fetch_assoc($getVolumeBillingMonth);
		$totalRows_getVolumeBillingMonth = mysql_num_rows($getVolumeBillingMonth);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getVolumeBillingMonthBilled = "SELECT USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')), (max(ACCTINPUTOCTETS) + max(ACCTOUTPUTOCTETS)) - (min(ACCTINPUTOCTETS) + min(ACCTOUTPUTOCTETS)) AS octets, (max(ACCTINPUTGIGAWORDS) + max(ACCTOUTPUTGIGAWORDS)) - (min(ACCTINPUTGIGAWORDS) + min(ACCTOUTPUTGIGAWORDS)) AS gigawords FROM ACCOUNTING WHERE TIME_STAMP >= ".mktime(0,0,0,date("m")-1,28)." AND TIME_STAMP < ".mktime(0,0,0,date("m"),29)." AND HOUR(FROM_UNIXTIME(TIME_STAMP)) >= 8 AND HOUR(FROM_UNIXTIME(TIME_STAMP)) <= 23 AND USERNAME = '".$_GET['username']."' group by USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')) order by TIME_STAMP desc";
		$getVolumeBillingMonthBilled = mysql_query($query_getVolumeBillingMonthBilled, $radiator) or die(mysql_error());
		$row_getVolumeBillingMonthBilled = mysql_fetch_assoc($getVolumeBillingMonthBilled);
		$totalRows_getVolumeBillingMonthBilled = mysql_num_rows($getVolumeBillingMonthBilled);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getVolumeLastMonth = "SELECT USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')), (max(ACCTINPUTOCTETS) + max(ACCTOUTPUTOCTETS)) - (min(ACCTINPUTOCTETS) + min(ACCTOUTPUTOCTETS)) AS octets, (max(ACCTINPUTGIGAWORDS) + max(ACCTOUTPUTGIGAWORDS)) - (min(ACCTINPUTGIGAWORDS) + min(ACCTOUTPUTGIGAWORDS)) AS gigawords FROM ACCOUNTING WHERE TIME_STAMP >= ".mktime(0,0,0,date("m")-1,1)." AND TIME_STAMP < ".mktime(0,0,0,date("m"),1)." AND USERNAME = '".$_GET['username']."' group by USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')) order by TIME_STAMP desc";
		$getVolumeLastMonth = mysql_query($query_getVolumeLastMonth, $radiator) or die(mysql_error());
		$row_getVolumeLastMonth = mysql_fetch_assoc($getVolumeLastMonth);
		$totalRows_getVolumeLastMonth = mysql_num_rows($getVolumeLastMonth);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getVolumeLastBillingMonth = "SELECT USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')), (max(ACCTINPUTOCTETS) + max(ACCTOUTPUTOCTETS)) - (min(ACCTINPUTOCTETS) + min(ACCTOUTPUTOCTETS)) AS octets, (max(ACCTINPUTGIGAWORDS) + max(ACCTOUTPUTGIGAWORDS)) - (min(ACCTINPUTGIGAWORDS) + min(ACCTOUTPUTGIGAWORDS)) AS gigawords FROM ACCOUNTING WHERE TIME_STAMP >= ".mktime(0,0,0,date("m")-2,28)." AND TIME_STAMP < ".mktime(0,0,0,date("m")-1,29)." AND USERNAME = '".$_GET['username']."' group by USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')) order by TIME_STAMP desc";
		$getVolumeLastBillingMonth = mysql_query($query_getVolumeLastBillingMonth, $radiator) or die(mysql_error());
		$row_getVolumeLastBillingMonth = mysql_fetch_assoc($getVolumeLastBillingMonth);
		$totalRows_getVolumeLastBillingMonth = mysql_num_rows($getVolumeLastBillingMonth);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getVolumeLastBillingMonthBilled = "SELECT USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')), (max(ACCTINPUTOCTETS) + max(ACCTOUTPUTOCTETS)) - (min(ACCTINPUTOCTETS) + min(ACCTOUTPUTOCTETS)) AS octets, (max(ACCTINPUTGIGAWORDS) + max(ACCTOUTPUTGIGAWORDS)) - (min(ACCTINPUTGIGAWORDS) + min(ACCTOUTPUTGIGAWORDS)) AS gigawords FROM ACCOUNTING WHERE TIME_STAMP >= ".mktime(0,0,0,date("m")-2,28)." AND TIME_STAMP < ".mktime(0,0,0,date("m")-1,29)." AND HOUR(FROM_UNIXTIME(TIME_STAMP)) >= 8 AND HOUR(FROM_UNIXTIME(TIME_STAMP)) <= 23 AND USERNAME = '".$_GET['username']."' group by USERNAME, ACCTSESSIONID, concat(date(from_unixtime(TIME_STAMP)),' ', concat(hour(from_unixtime(TIME_STAMP)),':00:00')) order by TIME_STAMP desc";
		$getVolumeLastBillingMonthBilled = mysql_query($query_getVolumeLastBillingMonthBilled, $radiator) or die(mysql_error());
		$row_getVolumeLastBillingMonthBilled = mysql_fetch_assoc($getVolumeLastBillingMonthBilled);
		$totalRows_getVolumeLastBillingMonthBilled = mysql_num_rows($getVolumeLastBillingMonthBilled);
								
		$maxRows_getUserFailedAuth = 5;
$pageNum_getUserFailedAuth = 0;
if (isset($_GET['pageNum_getUserFailedAuth'])) {
  $pageNum_getUserFailedAuth = $_GET['pageNum_getUserFailedAuth'];
}
$startRow_getUserFailedAuth = $pageNum_getUserFailedAuth * $maxRows_getUserFailedAuth;

mysql_select_db($database_radiator, $radiator);
$query_getUserFailedAuth = "SELECT RADAUTHLOG.*, FROM_UNIXTIME(RADAUTHLOG.TIME_STAMP) AS TS FROM RADAUTHLOG WHERE RADAUTHLOG.USERNAME = '".$_GET['username']."' ORDER BY RADAUTHLOG.TIME_STAMP DESC";
$query_limit_getUserFailedAuth = sprintf("%s LIMIT %d, %d", $query_getUserFailedAuth, $startRow_getUserFailedAuth, $maxRows_getUserFailedAuth);
$getUserFailedAuth = mysql_query($query_limit_getUserFailedAuth, $radiator) or die(mysql_error());
$row_getUserFailedAuth = mysql_fetch_assoc($getUserFailedAuth);

if (isset($_GET['totalRows_getUserFailedAuth'])) {
  $totalRows_getUserFailedAuth = $_GET['totalRows_getUserFailedAuth'];
} else {
  $all_getUserFailedAuth = mysql_query($query_getUserFailedAuth);
  $totalRows_getUserFailedAuth = mysql_num_rows($all_getUserFailedAuth);
}
$totalPages_getUserFailedAuth = ceil($totalRows_getUserFailedAuth/$maxRows_getUserFailedAuth)-1;

		if (isset($_GET['totalRows_getRadiatorAccountingData'])) {
		  $totalRows_getRadiatorAccountingData = $_GET['totalRows_getRadiatorAccountingData'];
		} else {
		  $all_getRadiatorAccountingData = mysql_query($query_getRadiatorAccountingData);
		  $totalRows_getRadiatorAccountingData = mysql_num_rows($all_getRadiatorAccountingData);
		}
		$totalPages_getRadiatorAccountingData = ceil($totalRows_getRadiatorAccountingData/$maxRows_getRadiatorAccountingData)-1;
		
		$queryString_getRadiatorAccountingData = "";
		if (!empty($_SERVER['QUERY_STRING'])) {
		  $params = explode("&", $_SERVER['QUERY_STRING']);
		  $newParams = array();
		  foreach ($params as $param) {
			if (stristr($param, "pageNum_getRadiatorAccountingData") == false && 
				stristr($param, "totalRows_getRadiatorAccountingData") == false) {
			  array_push($newParams, $param);
			}
		  }
		  if (count($newParams) != 0) {
			$queryString_getRadiatorAccountingData = "&" . htmlentities(implode("&", $newParams));
		  }
		}
		$queryString_getRadiatorAccountingData = sprintf("&totalRows_getRadiatorAccountingData=%d%s", $totalRows_getRadiatorAccountingData, $queryString_getRadiatorAccountingData);
		
		$queryString_getUserFailedAuth = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_getUserFailedAuth") == false && 
        stristr($param, "totalRows_getUserFailedAuth") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_getUserFailedAuth = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_getUserFailedAuth = sprintf("&totalRows_getUserFailedAuth=%d%s", $totalRows_getUserFailedAuth, $queryString_getUserFailedAuth);

		?>
        
        Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server <?php echo $row_getRadiator['host']; ?>"><?php echo $row_getRadiator['descr']; ?></a> >> Accounting Username >> <strong><?php echo $_GET['username']; ?></strong><br /><br />
        
        <a href="#_record">Subscriber Record</a><br />
        <a href="#_routes">Subscriber Routes</a><br />
        <a href="#_arbroutes">Subscriber Arbitrary Routes</a><br />
        <a href="#_accounting">Accounting</a><br />
        <a href="#_usage">Usage</a><br />
        <a href="#_failed">Failed Authentication Attempts</a>
        
    <h3 id="_record">Subscriber Record</h3>
    <table border="0" width="100%">
      <tr>
        <td><strong>Username</strong></td>
        <td><strong>Password</strong></td>
        <td><strong>Subscriber Group</strong></td>        
        <td><strong>Circuit ID</strong></td>
        <td><strong>Framed IP</strong></td>
        <td><strong>Framed IPv6</strong></td>
        <td><strong>Routes</strong></td>
      </tr>
      <?php do { ?>
        <tr bgcolor="#EAEAEA">
          <td><?php echo $row_getSubscriber['USERNAME']; ?></td>
          <td><?php echo $row_getSubscriber['PASSWORD']; ?></td>
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;subscribergroup=<?php echo $row_getSubscriber['SUBSCRIBERGROUP']; ?>" title="Browse this subscriber group"><?php echo $row_getSubscriber['SUBSCRIBERGROUP']; ?></a></td>          
          <td><?php echo $row_getSubscriber['CIRCUITID']; ?></td>
          <td><?php echo $row_getSubscriber['FRAMEDIP']; ?></td>
          <td><?php echo $row_getSubscriber['FRAMEDIPV6']; ?></td>
          <td><?php echo $row_getSubscriber['ROUTES']; ?></td>
        </tr>
        <?php } while ($row_getSubscriber = mysql_fetch_assoc($getSubscriber)); ?>
    </table>
  
<h3 id="_routes">Subscriber Routes</h3>
    <?php if ($totalRows_getSubscriberRoutes > 0) { // Show if recordset not empty ?>
      
      <form action="" method="post" target="_self" name="frm_delete_subscriber_routes" id="frm_delete_subscriber_routes">
        <input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_getSubscriberRoutes; ?>">
        <input type="hidden" name="permission_action_type" value="delete_subscriber_routes">
        
        <table width="50%" border="0">
          <tr>
            <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_subscriber_routes'), this.checked);" /></td>
            <td><strong>Network</strong></td>
            <td><strong>Mask</strong></td>
          </tr>
          <?php
			
  	$count = 0;
							do { 
								
								mysql_select_db($database_subman, $subman);
								$query_getSubscriberNetwork = "SELECT * FROM networks WHERE networks.id = ".$row_getSubscriberRoutes['network']."";
								$getSubscriberNetwork = mysql_query($query_getSubscriberNetwork, $subman) or die(mysql_error());
								$row_getSubscriberNetwork = mysql_fetch_assoc($getSubscriberNetwork);
								$totalRows_getSubscriberNetwork = mysql_num_rows($getSubscriberNetwork);
			
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
          <tr bgcolor="<?php echo $bgcolour; ?>">
            <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_getSubscriberRoutes['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_getSubscriberRoutes['id']; ?>"></td>
            <td><a href="containerView.php?browse=networks&amp;container=<?php echo $row_getSubscriberNetwork['container']; ?>&amp;group=&amp;parent=<?php echo $row_getSubscriberNetwork['id']; ?>" title="Browse network"><?php echo long2ip($row_getSubscriberNetwork['network']); ?></a></td>
            <td><?php echo long2ip($row_getSubscriberNetwork['maskLong']); ?></td>
          </tr>
          <?php } while ($row_getSubscriberRoutes = mysql_fetch_assoc($getSubscriberRoutes)); ?>
        </table>
        <input type="hidden" name="username" value="<?php echo $_GET['username']; ?>" />
      </form>
      <?php } // Show if recordset not empty 
  		else { ?>
<p>There are no routes for this subscriber.</p>
  <?php } ?>

<h3 id="_arbroutes">Subscriber Arbitrary Routes</h3>

<?php if ($totalRows_getSubscriberArbRoutes > 0) { // Show if recordset not empty ?>
      
      <form action="" method="post" target="_self" name="frm_delete_subscriber_arbroutes" id="frm_delete_subscriber_arbroutes">
        <input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_getSubscriberArbRoutes; ?>">
        <input type="hidden" name="permission_action_type" value="delete_subscriber_arbroutes">
        
        <table width="50%" border="0">
          <tr>
            <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_subscriber_arbroutes'), this.checked);" /></td>
            <td><strong>Network</strong></td>
            <td><strong>Mask</strong></td>
          </tr>
          <?php
			
  	$count = 0;
							do { 
			
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
          <tr bgcolor="<?php echo $bgcolour; ?>">
            <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_getSubscriberArbRoutes['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_getSubscriberArbRoutes['id']; ?>"></td>
            <td><?php echo $row_getSubscriberArbRoutes['network']; ?></a></td>
            <td><?php echo $row_getSubscriberArbRoutes['mask']; ?></td>
          </tr>
          <?php } while ($row_getSubscriberArbRoutes = mysql_fetch_assoc($getSubscriberArbRoutes)); ?>
        </table>
        <input type="hidden" name="username" value="<?php echo $_GET['username']; ?>" />
      </form>
      <?php } // Show if recordset not empty 
  		else { ?>
<p>There are no arbitrary routes for this subscriber.</p>
  <?php } ?>
  
<h3 id="_accounting">Accounting Data</h3>

   <form action="" method="get" name="searchFrm" target="_self" id="searchFrm">
       <div class="searchBoxLeft"><img src="images/semi-left.gif" alt="" /></div>
       <input name="search" type="text" class="searchFormField" value="<?php echo $_SESSION['search']; ?>" />
       <div class="searchBoxCancel">
       <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;username=<?php echo $_GET['username']; ?>&amp;submit=_cancel_search" title="Cancel search"><img src="images/cancel.gif" alt="Cancel search" border="0" /></a>
       </div>
       <div class="searchBoxRight"><img src="images/semi-right.gif" alt="" width="10" height="20" /></div>
       	<input type="hidden" name="browse" value="<?php echo $_GET['browse']; ?>" />
        <input type="hidden" name="radiator" value="<?php echo $_GET['radiator']; ?>" />
        <input type="hidden" name="username" value="<?php echo $_GET['username']; ?>" />
        &nbsp;From >> 
        <select name="dd" class="input_standard">
          <option value="dd" <?php if (!(strcmp("DD", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>DD</option>
          <option value="01" <?php if (!(strcmp("01", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>01</option>
          <option value="02" <?php if (!(strcmp("02", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>02</option>
          <option value="03" <?php if (!(strcmp("03", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>03</option>
          <option value="04" <?php if (!(strcmp("04", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>04</option>
          <option value="05" <?php if (!(strcmp("05", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>05</option>
          <option value="06" <?php if (!(strcmp("06", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>06</option>
          <option value="07" <?php if (!(strcmp("07", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>07</option>
          <option value="08" <?php if (!(strcmp("08", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>08</option>
          <option value="09" <?php if (!(strcmp("09", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>09</option>
          <option value="10" <?php if (!(strcmp("10", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>10</option>
          <option value="11" <?php if (!(strcmp("11", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>11</option>
          <option value="12" <?php if (!(strcmp("12", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>12</option>
          <option value="13" <?php if (!(strcmp("13", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>13</option>
          <option value="14" <?php if (!(strcmp("14", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>14</option>
          <option value="15" <?php if (!(strcmp("15", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>15</option>
          <option value="16" <?php if (!(strcmp("16", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>16</option>
          <option value="17" <?php if (!(strcmp("17", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>17</option>
          <option value="18" <?php if (!(strcmp("18", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>18</option>
          <option value="19" <?php if (!(strcmp("19", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>19</option>
          <option value="20" <?php if (!(strcmp("20", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>20</option>
          <option value="21" <?php if (!(strcmp("21", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>21</option>
          <option value="22" <?php if (!(strcmp("22", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>22</option>
          <option value="23" <?php if (!(strcmp("23", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>23</option>
          <option value="24" <?php if (!(strcmp("24", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>24</option>
          <option value="25" <?php if (!(strcmp("25", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>25</option>
          <option value="26" <?php if (!(strcmp("26", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>26</option>
          <option value="27" <?php if (!(strcmp("27", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>27</option>
          <option value="28" <?php if (!(strcmp("28", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>28</option>
          <option value="29" <?php if (!(strcmp("29", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>29</option>
          <option value="30" <?php if (!(strcmp("30", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>30</option>
          <option value="31" <?php if (!(strcmp("31", $_SESSION['dd']))) {echo "selected=\"selected\"";} ?>>31</option>
        </select>
        <select name="mm" class="input_standard">
          <option value="mm" <?php if (!(strcmp("MM", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>MM</option>
          <option value="01" <?php if (!(strcmp("01", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>01</option>
          <option value="02" <?php if (!(strcmp("02", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>02</option>
          <option value="03" <?php if (!(strcmp("03", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>03</option>
          <option value="04" <?php if (!(strcmp("04", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>04</option>
          <option value="05" <?php if (!(strcmp("05", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>05</option>
          <option value="06" <?php if (!(strcmp("06", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>06</option>
          <option value="07" <?php if (!(strcmp("07", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>07</option>
          <option value="08" <?php if (!(strcmp("08", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>08</option>
          <option value="09" <?php if (!(strcmp("09", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>09</option>
          <option value="10" <?php if (!(strcmp("10", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>10</option>
          <option value="11" <?php if (!(strcmp("11", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>11</option>
          <option value="12" <?php if (!(strcmp("12", $_SESSION['mm']))) {echo "selected=\"selected\"";} ?>>12</option>
		</select>
       <select name="yyyy" class="input_standard">
         <option value="yyyy" <?php if (!(strcmp("YYYY", $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>YYYY</option>
         <option value="2005" <?php if (!(strcmp(2005, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2005</option>
         <option value="2006" <?php if (!(strcmp(2006, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2006</option>
         <option value="2007" <?php if (!(strcmp(2007, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2007</option>
         <option value="2008" <?php if (!(strcmp(2008, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2008</option>
         <option value="2009" <?php if (!(strcmp(2009, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2009</option>
         <option value="2010" <?php if (!(strcmp(2010, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2010</option>
         <option value="2011" <?php if (!(strcmp(2011, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2011</option>
         <option value="2012" <?php if (!(strcmp(2012, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2012</option>
         <option value="2013" <?php if (!(strcmp(2013, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2013</option>
         <option value="2014" <?php if (!(strcmp(2014, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2014</option>
         <option value="2015" <?php if (!(strcmp(2015, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2015</option>
         <option value="2016" <?php if (!(strcmp(2016, $_SESSION['yyyy']))) {echo "selected=\"selected\"";} ?>>2016</option>
       </select>
       To >> 
       <select name="dd1" class="input_standard">
  <option value="dd" <?php if (!(strcmp("dd", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>DD</option>
  <option value="01" <?php if (!(strcmp("01", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>01</option>
  <option value="02" <?php if (!(strcmp("02", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>02</option>
  <option value="03" <?php if (!(strcmp("03", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>03</option>
  <option value="04" <?php if (!(strcmp("04", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>04</option>
  <option value="05" <?php if (!(strcmp("05", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>05</option>
  <option value="06" <?php if (!(strcmp("06", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>06</option>
  <option value="07" <?php if (!(strcmp("07", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>07</option>
  <option value="08" <?php if (!(strcmp("08", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>08</option>
  <option value="09" <?php if (!(strcmp("09", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>09</option>
  <option value="10" <?php if (!(strcmp("10", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>10</option>
  <option value="11" <?php if (!(strcmp("11", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>11</option>
  <option value="12" <?php if (!(strcmp("12", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>12</option>
  <option value="13" <?php if (!(strcmp("13", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>13</option>
  <option value="14" <?php if (!(strcmp("14", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>14</option>
  <option value="15" <?php if (!(strcmp("15", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>15</option>
  <option value="16" <?php if (!(strcmp("16", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>16</option>
  <option value="17" <?php if (!(strcmp("17", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>17</option>
  <option value="18" <?php if (!(strcmp("18", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>18</option>
  <option value="19" <?php if (!(strcmp("19", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>19</option>
  <option value="20" <?php if (!(strcmp("20", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>20</option>
  <option value="21" <?php if (!(strcmp("21", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>21</option>
  <option value="22" <?php if (!(strcmp("22", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>22</option>
  <option value="23" <?php if (!(strcmp("23", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>23</option>
  <option value="24" <?php if (!(strcmp("24", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>24</option>
  <option value="25" <?php if (!(strcmp("25", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>25</option>
  <option value="26" <?php if (!(strcmp("26", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>26</option>
  <option value="27" <?php if (!(strcmp("27", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>27</option>
  <option value="28" <?php if (!(strcmp("28", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>28</option>
  <option value="29" <?php if (!(strcmp("29", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>29</option>
  <option value="30" <?php if (!(strcmp("30", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>30</option>
  <option value="31" <?php if (!(strcmp("31", $_SESSION['dd1']))) {echo "selected=\"selected\"";} ?>>31</option>
      </select>
        <select name="mm1" class="input_standard">
          <option value="mm" <?php if (!(strcmp("mm", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>MM</option>
          <option value="01" <?php if (!(strcmp("01", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>01</option>
          <option value="02" <?php if (!(strcmp("02", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>02</option>
          <option value="03" <?php if (!(strcmp("03", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>03</option>
          <option value="04" <?php if (!(strcmp("04", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>04</option>
          <option value="05" <?php if (!(strcmp("05", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>05</option>
          <option value="06" <?php if (!(strcmp("06", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>06</option>
          <option value="07" <?php if (!(strcmp("07", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>07</option>
          <option value="08" <?php if (!(strcmp("08", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>08</option>
          <option value="09" <?php if (!(strcmp("09", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>09</option>
          <option value="10" <?php if (!(strcmp("10", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>10</option>
          <option value="11" <?php if (!(strcmp("11", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>11</option>
          <option value="12" <?php if (!(strcmp("12", $_SESSION['mm1']))) {echo "selected=\"selected\"";} ?>>12</option>
		</select>
       <select name="yyyy1" class="input_standard">
         <option value="yyyy" <?php if (!(strcmp("yyyy", $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>YYYY</option>
         <option value="2005" <?php if (!(strcmp(2005, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2005</option>
         <option value="2006" <?php if (!(strcmp(2006, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2006</option>
         <option value="2007" <?php if (!(strcmp(2007, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2007</option>
         <option value="2008" <?php if (!(strcmp(2008, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2008</option>
         <option value="2009" <?php if (!(strcmp(2009, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2009</option>
         <option value="2010" <?php if (!(strcmp(2010, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2010</option>
         <option value="2011" <?php if (!(strcmp(2011, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2011</option>
         <option value="2012" <?php if (!(strcmp(2012, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2012</option>
         <option value="2013" <?php if (!(strcmp(2013, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2013</option>
         <option value="2014" <?php if (!(strcmp(2014, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2014</option>
         <option value="2015" <?php if (!(strcmp(2015, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2015</option>
         <option value="2016" <?php if (!(strcmp(2016, $_SESSION['yyyy1']))) {echo "selected=\"selected\"";} ?>>2016</option>
       </select>
       <select name="accttype" class="input_standard">
      <option value="" <?php if (!(strcmp("", $_SESSION['accttype']))) {echo "selected=\"selected\"";} ?>>All</option>
   <option value="Start" <?php if (!(strcmp("Start", $_SESSION['accttype']))) {echo "selected=\"selected\"";} ?>>Start</option>
<option value="Alive" <?php if (!(strcmp("Alive", $_SESSION['accttype']))) {echo "selected=\"selected\"";} ?>>Alive</option>
<option value="Stop" <?php if (!(strcmp("Stop", $_SESSION['accttype']))) {echo "selected=\"selected\"";} ?>>Stop</option>
       </select>
       	<input type="image" src="images/search.gif" alt="Search" align="absmiddle"/>
         <input type="hidden" name="submit" value="_search" />
    </form>
       
<p>Displaying records <?php echo ($startRow_getRadiatorAccountingData + 1) ?> to <?php echo min($startRow_getRadiatorAccountingData + $maxRows_getRadiatorAccountingData, $totalRows_getRadiatorAccountingData) ?> of <?php echo $totalRows_getRadiatorAccountingData ?></p>
<table border="0">
  <tr>
    <td><?php if ($pageNum_getRadiatorAccountingData > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_getRadiatorAccountingData=%d%s", $currentPage, 0, $queryString_getRadiatorAccountingData); ?>"><img src="images/First.gif" alt="" border="0" /></a>
      <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_getRadiatorAccountingData > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_getRadiatorAccountingData=%d%s", $currentPage, max(0, $pageNum_getRadiatorAccountingData - 1), $queryString_getRadiatorAccountingData); ?>"><img src="images/Previous.gif" alt="" border="0" /></a>
      <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_getRadiatorAccountingData < $totalPages_getRadiatorAccountingData) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_getRadiatorAccountingData=%d%s", $currentPage, min($totalPages_getRadiatorAccountingData, $pageNum_getRadiatorAccountingData + 1), $queryString_getRadiatorAccountingData); ?>"><img src="images/Next.gif" alt="" border="0" /></a>
      <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_getRadiatorAccountingData < $totalPages_getRadiatorAccountingData) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_getRadiatorAccountingData=%d%s", $currentPage, $totalPages_getRadiatorAccountingData, $queryString_getRadiatorAccountingData); ?>"><img src="images/Last.gif" alt="" border="0" /></a>
      <?php } // Show if not last page ?></td>
  </tr>
</table>

    	<?php if ($totalRows_getRadiatorAccountingData > 0) { // Show if recordset not empty ?>
    	  <table border="0" width="100%">
    	    <tr>
    	      <td><strong>Time Stamp</strong></td>
    	      <td><strong>Accounting Type</strong></td>
              <td><strong>Input Gigawords</strong></td>
    	      <td><strong>Output Gigawords</strong></td>
    	      <td><strong>Input Octets</strong></td>
    	      <td><strong>Output Octets</strong></td>
    	      <td><strong>Session ID</strong></td>
    	      <td><strong>Session Up Time</strong></td>
    	      <td><strong>NAS ID</strong></td>
    	      <td><strong>Framed IP</strong></td>
    	      <td><strong>Cisco AV Pair</strong></td>
   	        </tr>
    	    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    	    <tr bgcolor="<?php echo $bgcolour; ?>">
    	      <td><?php echo $row_getRadiatorAccountingData['timestamp']; ?></td>
    	      <td><?php echo $row_getRadiatorAccountingData['ACCTSTATUSTYPE']; ?></td>
              <td><?php echo $row_getRadiatorAccountingData['ACCTINPUTGIGAWORDS']; ?></td>
			  <td><?php echo $row_getRadiatorAccountingData['ACCTOUTPUTGIGAWORDS']; ?></td>
    	      <td><?php echo $row_getRadiatorAccountingData['ACCTINPUTOCTETS']; ?></td>
    	      <td><?php echo $row_getRadiatorAccountingData['ACCTOUTPUTOCTETS']; ?></td>
    	      <td><?php echo $row_getRadiatorAccountingData['ACCTSESSIONID']; ?></td>
    	      <td><?php echo $row_getRadiatorAccountingData['ACCTSESSIONTIME']; ?></td>
    	      <td><?php echo $row_getRadiatorAccountingData['NASIDENTIFIER']; ?></td>
    	      <td><?php echo $row_getRadiatorAccountingData['FRAMEDIPADDRESS']; ?></td>
    	      <td><?php echo $row_getRadiatorAccountingData['CISCOAVPAIR']; ?></td>
  	      </tr>
    	    <?php } while ($row_getRadiatorAccountingData = mysql_fetch_assoc($getRadiatorAccountingData)); ?>
</table>
    	  <?php } // Show if recordset not empty 
  		else { ?>
<p>There are no accounting data for this subscriber.</p>
       <?php } ?>

<h3 id="_usage">Usage</h3>

<?php
	do {
		
		$volume_hour += $row_getVolumeHour['octets'];
		$volume_hour += $row_getVolumeHour['gigawords'] * 4294967296;
		
	} while ($row_getVolumeHour = mysql_fetch_assoc($getVolumeHour));
	
	do {
		
		$volume_yesterday += $row_getVolumeYesterday['octets'];
		$volume_yesterday += $row_getVolumeYesterday['gigawords'] * 4294967296;
		
	} while ($row_getVolumeYesterday = mysql_fetch_assoc($getVolumeYesterday));
	
	do {
		
		$volume_month += $row_getVolumeMonth['octets'];
		$volume_month += $row_getVolumeMonth['gigawords'] * 4294967296;
		
	} while ($row_getVolumeMonth = mysql_fetch_assoc($getVolumeMonth));
	
	do {
		
		$volume_billingMonthBilled += $row_getVolumeBillingMonthBilled['octets'];
		$volume_billingMonthBilled += $row_getVolumeBillingMonthBilled['gigawords'] * 4294967296;
		
	} while ($row_getVolumeBillingMonthBilled = mysql_fetch_assoc($getVolumeBillingMonthBilled));
	
	do {
		
		$volume_billingMonth += $row_getVolumeBillingMonth['octets'];
		$volume_billingMonth += $row_getVolumeBillingMonth['gigawords'] * 4294967296;
		
	} while ($row_getVolumeBillingMonth = mysql_fetch_assoc($getVolumeBillingMonth));
	
	do {
		
		$volume_lastMonth += $row_getVolumeLastMonth['octets'];
		$volume_lastMonth += $row_getVolumeLastMonth['gigawords'] * 4294967296;
		
	} while ($row_getVolumeLastMonth = mysql_fetch_assoc($getVolumeLastMonth));
	
	do {
		
		$volume_lastBillingMonth += $row_getVolumeLastBillingMonth['octets'];
		$volume_lastBillingMonth += $row_getVolumeLastBillingMonth['gigawords'] * 4294967296;
		
	} while ($row_getVolumeLastBillingMonth = mysql_fetch_assoc($getVolumeLastBillingMonth));
	
	do {
		
		$volume_lastBillingMonthBilled += $row_getVolumeLastBillingMonthBilled['octets'];
		$volume_lastBillingMonthBilled += $row_getVolumeLastBillingMonthBilled['gigawords'] * 4294967296;
		
	} while ($row_getVolumeLastBillingMonthBilled = mysql_fetch_assoc($getVolumeLastBillingMonthBilled));
	
	if ($volume_hour < 1048576) {
			$volume_hour = $volume_hour / 1024;
			$vh_unit = "KB";
	}
	elseif ($volume_hour < 1073741824) {
			$volume_hour = $volume_hour / 1048576;
			$vh_unit = "MB";
	}
	else {
			$volume_hour = $volume_hour / 1073741824;
			$vh_unit = "GB";
	}
	
	if ($volume_yesterday < 1048576) {
			$volume_yesterday = $volume_yesterday / 1024;
			$vy_unit = "KB";
	}
	elseif ($volume_yesterday < 1073741824) {
			$volume_yesterday = $volume_yesterday / 1048576;
			$vy_unit = "MB";
	}
	else {
			$volume_yesterday = $volume_yesterday / 1073741824;
			$vy_unit = "GB";
	}
	
	if ($volume_month < 1048576) {
			$volume_month = $volume_month / 1024;
			$vm_unit = "KB";
	}
	elseif ($volume_month < 1073741824) {
			$volume_month = $volume_month / 1048576;
			$vm_unit = "MB";
	}
	else {
			$volume_month = $volume_month / 1073741824;
			$vm_unit = "GB";
	}
	
	if ($volume_billingMonth < 1048576) {
			$volume_billingMonth = $volume_billingMonth / 1024;
			$vbm_unit = "KB";
	}
	elseif ($volume_billingMonth < 1073741824) {
			$volume_billingMonth = $volume_billingMonth / 1048576;
			$vbm_unit = "MB";
	}
	else {
			$volume_billingMonth = $volume_billingMonth / 1073741824;
			$vbm_unit = "GB";
	}
	
	if ($volume_billingMonthBilled < 1048576) {
			$volume_billingMonthBilled = $volume_billingMonthBilled / 1024;
			$vbmb_unit = "KB";
	}
	elseif ($volume_billingMonthBilled < 1073741824) {
			$volume_billingMonthBilled = $volume_billingMonthBilled / 1048576;
			$vbmb_unit = "MB";
	}
	else {
			$volume_billingMonthBilled = $volume_billingMonthBilled / 1073741824;
			$vbmb_unit = "GB";
	}
	
	if ($volume_lastMonth < 1048576) {
			$volume_lastMonth = $volume_lastMonth / 1024;
			$vlm_unit = "KB";
	}
	elseif ($volume_lastMonth < 1073741824) {
			$volume_lastMonth = $volume_lastMonth / 1048576;
			$vlm_unit = "MB";
	}
	else {
			$volume_lastMonth = $volume_lastMonth / 1073741824;
			$vlm_unit = "GB";
	}
	
	if ($volume_lastBillingMonth < 1048576) {
			$volume_lastBillingMonth = $volume_lastBillingMonth / 1024;
			$vlbm_unit = "KB";
	}
	elseif ($volume_lastBillingMonth < 1073741824) {
			$volume_lastBillingMonth = $volume_lastBillingMonth / 1048576;
			$vlbm_unit = "MB";
	}
	else {
			$volume_lastBillingMonth = $volume_lastBillingMonth / 1073741824;
			$vlbm_unit = "GB";
	}
	
	if ($volume_lastBillingMonthBilled < 1048576) {
			$volume_lastBillingMonthBilled = $volume_lastBillingMonthBilled / 1024;
			$vlbmb_unit = "KB";
	}
	elseif ($volume_lastBillingMonthBilled < 1073741824) {
			$volume_lastBillingMonthBilled = $volume_lastBillingMonthBilled / 1048576;
			$vlbmb_unit = "MB";
	}
	else {
			$volume_lastBillingMonthBilled = $volume_lastBillingMonthBilled / 1073741824;
			$vlbmb_unit = "GB";
	}
?>
  <table width="50%" border="0">
    <tr>
      <td><strong>Time Frame</strong></td>
      <td><strong>Volume Transferred</strong></td>
    </tr>
    <tr bgcolor="#EAEAEA">
    	<td><strong>Last 60 Minutes</strong></td>
        <td><strong><?php printf("%.2f",$volume_hour); ?><?php echo $vh_unit; ?></strong></td>
   </tr>
   <tr bgcolor="#F5F5F5">
    	<td><strong>Yesterday</strong></td>
        <td><strong><?php printf("%.2f",$volume_yesterday); ?><?php echo $vy_unit; ?></strong></td>
   </tr>
   <tr bgcolor="#EAEAEA">
    	<td><strong>This Calendar Month</strong></td>
        <td><strong><?php printf("%.2f",$volume_month); ?><?php echo $vm_unit; ?></strong></td>
   </tr>
   <tr bgcolor="#F5F5F5">
    	<td><strong>This Billing Month</strong></td>
        <td><strong><?php printf("%.2f",$volume_billingMonth); ?><?php echo $vbm_unit; ?></strong></td>
   </tr>
   <tr bgcolor="#EAEAEA">
    	<td><strong>This Billing Month (Billed Hours Only)</strong></td>
        <td><strong><?php printf("%.2f",$volume_billingMonthBilled); ?><?php echo $vbmb_unit; ?></strong></td>
   </tr>
   <tr bgcolor="#F5F5F5">
    	<td><strong>Last Calendar Month</strong></td>
        <td><strong><?php printf("%.2f",$volume_lastMonth); ?><?php echo $vlm_unit; ?></strong></td>
   </tr>
   <tr bgcolor="#EAEAEA">
    	<td><strong>Last Billing Month</strong></td>
        <td><strong><?php printf("%.2f",$volume_lastBillingMonth); ?><?php echo $vlbm_unit; ?></strong></td>
   </tr>
   <tr bgcolor="#F5F5F5">
    	<td><strong>Last Billing Month (Billed Hours Only)</strong></td>
        <td><strong><?php printf("%.2f",$volume_lastBillingMonthBilled); ?><?php echo $vlbmb_unit; ?></strong></td>
   </tr>
 </table>
 
<h3 id="_failed">Failed Authentication Attempts</h3>
<?php if ($totalRows_getUserFailedAuth > 0) { // Show if recordset not empty ?>
  <p>Displaying records <?php echo ($startRow_getUserFailedAuth + 1) ?> to <?php echo min($startRow_getUserFailedAuth + $maxRows_getUserFailedAuth, $totalRows_getUserFailedAuth) ?> of <?php echo $totalRows_getUserFailedAuth ?></p>
  <table border="0">
    <tr>
      <td><?php if ($pageNum_getUserFailedAuth > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_getUserFailedAuth=%d%s", $currentPage, 0, $queryString_getUserFailedAuth); ?>"><img src="images/First.gif" border="0" /></a>
        <?php } // Show if not first page ?></td>
      <td><?php if ($pageNum_getUserFailedAuth > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_getUserFailedAuth=%d%s", $currentPage, max(0, $pageNum_getUserFailedAuth - 1), $queryString_getUserFailedAuth); ?>"><img src="images/Previous.gif" border="0" /></a>
        <?php } // Show if not first page ?></td>
      <td><?php if ($pageNum_getUserFailedAuth < $totalPages_getUserFailedAuth) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_getUserFailedAuth=%d%s", $currentPage, min($totalPages_getUserFailedAuth, $pageNum_getUserFailedAuth + 1), $queryString_getUserFailedAuth); ?>"><img src="images/Next.gif" border="0" /></a>
        <?php } // Show if not last page ?></td>
      <td><?php if ($pageNum_getUserFailedAuth < $totalPages_getUserFailedAuth) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_getUserFailedAuth=%d%s", $currentPage, $totalPages_getUserFailedAuth, $queryString_getUserFailedAuth); ?>"><img src="images/Last.gif" border="0" /></a>
        <?php } // Show if not last page ?></td>
    </tr>
  </table>
  <br />
  <table width="50%" border="0">
    <tr>
      <td><strong>Time Stamp</strong></td>
      <td><strong>Reason</strong></td>
    </tr>
    <?php 						do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><?php echo $row_getUserFailedAuth['TS']; ?></td>
      <td><?php echo $row_getUserFailedAuth['REASON']; ?></td>
    </tr>
    <?php } while ($row_getUserFailedAuth = mysql_fetch_assoc($getUserFailedAuth)); ?>
  </table>
  <?php } // Show if recordset not empty 
  	else { ?>
    <p>There are no failed authentication attempts to display for this user.
  <?php } ?>
<?php
	}
	
  	elseif ($_GET['radiator'] != "" && $_GET['addresspool'] != "") {
  		
		if (!(getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 0 || ($pageLevel > 0 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == ""))) { ?>
        
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);

		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
		
		$maxRows_getRadiatorAddressesInUse = 24;
		$pageNum_getRadiatorAddressesInUse = 0;
		if (isset($_GET['pageNum_getRadiatorAddressesInUse'])) {
		  $pageNum_getRadiatorAddressesInUse = $_GET['pageNum_getRadiatorAddressesInUse'];
		}
		$startRow_getRadiatorAddressesInUse = $pageNum_getRadiatorAddressesInUse * $maxRows_getRadiatorAddressesInUse;
		
		mysql_select_db($database_radiator, $radiator);
		if ($_GET['expired'] == 1) {
			
			$query_getRadiatorAddressesInUse = "SELECT FROM_UNIXTIME(RADPOOL.EXPIRY) AS exp, RADPOOL.* FROM RADPOOL WHERE RADPOOL.`STATE` = 1 AND RADPOOL.POOL = '".$_GET['addresspool']."' AND RADPOOL.EXPIRY < UNIX_TIMESTAMP(now())";
			
		}
		else {
			
			$query_getRadiatorAddressesInUse = "SELECT FROM_UNIXTIME(RADPOOL.EXPIRY) AS exp, RADPOOL.* FROM RADPOOL WHERE RADPOOL.`STATE` = 1 AND RADPOOL.POOL = '".$_GET['addresspool']."'";
			
		}
		$query_limit_getRadiatorAddressesInUse = sprintf("%s LIMIT %d, %d", $query_getRadiatorAddressesInUse, $startRow_getRadiatorAddressesInUse, $maxRows_getRadiatorAddressesInUse);
		$getRadiatorAddressesInUse = mysql_query($query_limit_getRadiatorAddressesInUse, $radiator) or die(mysql_error());
		$row_getRadiatorAddressesInUse = mysql_fetch_assoc($getRadiatorAddressesInUse);
		
		if (isset($_GET['totalRows_getRadiatorAddressesInUse'])) {
		  $totalRows_getRadiatorAddressesInUse = $_GET['totalRows_getRadiatorAddressesInUse'];
		} else {
		  $all_getRadiatorAddressesInUse = mysql_query($query_getRadiatorAddressesInUse);
		  $totalRows_getRadiatorAddressesInUse = mysql_num_rows($all_getRadiatorAddressesInUse);
		}
		$totalPages_getRadiatorAddressesInUse = ceil($totalRows_getRadiatorAddressesInUse/$maxRows_getRadiatorAddressesInUse)-1;
		
		$queryString_getRadiatorAddressesInUse = "";
		if (!empty($_SERVER['QUERY_STRING'])) {
		  $params = explode("&", $_SERVER['QUERY_STRING']);
		  $newParams = array();
		  foreach ($params as $param) {
			if (stristr($param, "pageNum_getRadiatorAddressesInUse") == false && 
				stristr($param, "totalRows_getRadiatorAddressesInUse") == false) {
			  array_push($newParams, $param);
			}
		  }
		  if (count($newParams) != 0) {
			$queryString_getRadiatorAddressesInUse = "&" . htmlentities(implode("&", $newParams));
		  }
		}
		$queryString_getRadiatorAddressesInUse = sprintf("&totalRows_getRadiatorAddressesInUse=%d%s", $totalRows_getRadiatorAddressesInUse, $queryString_getRadiatorAddressesInUse);

		?>

Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server <?php echo $row_getRadiator['host']; ?>"><?php echo $row_getRadiator['descr']; ?></a> >> Address Pool >> <strong><?php echo $_GET['addresspool']; ?></strong><br />
<br />

<?php if ($_GET['expired'] != 1) { ?>
<table width="50%" border="0">
        <tr>
          <td><strong>Total Addresses</strong></td>
          <td><strong>In Use</strong></td>
          <td><strong>% Free</strong></td>
        </tr>
        <?php
  	$count = 0;
							
								mysql_select_db($database_radiator, $radiator);
								$query_getUsedAddrCount = "SELECT COUNT(*) FROM RADPOOL WHERE RADPOOL.POOL = '".$_GET['addresspool']."' AND RADPOOL.STATE = 1";
								$getUsedAddrCount = mysql_query($query_getUsedAddrCount, $radiator) or die(mysql_error());
								$row_getUsedAddrCount = mysql_fetch_assoc($getUsedAddrCount);
								$totalRows_getUsedAddrCount = mysql_num_rows($getUsedAddrCount);
								
								mysql_select_db($database_radiator, $radiator);
								$query_getTotalAddrCount = "SELECT COUNT(*) FROM RADPOOL WHERE RADPOOL.POOL = '".$_GET['addresspool']."'";
								$getTotalAddrCount = mysql_query($query_getTotalAddrCount, $radiator) or die(mysql_error());
								$row_getTotalAddrCount = mysql_fetch_assoc($getTotalAddrCount);
								$totalRows_getTotalAddrCount = mysql_num_rows($getTotalAddrCount);
		
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><?php echo $row_getTotalAddrCount['COUNT(*)']; ?></td>
          <td><?php echo $row_getUsedAddrCount['COUNT(*)']; ?></td>
          <td><font color="<?php if ((100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)) > 30) { echo "green"; } elseif ((100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)) > 10) { echo "orange"; } else { echo "red"; } ?>"><strong><?php printf ('%.2f', 100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)); ?></strong></font></td>
        </tr>
    </table>
<?php } 
	else { ?>
	
    	<p class="text_red">Showing expired addresses only.</p>
        
<?php } ?>      
<?php if ($totalRows_getRadiatorAddressesInUse > 0) { // Show if recordset not empty ?>
  
  <p>Displaying records <?php echo ($startRow_getRadiatorAddressesInUse + 1) ?> to <?php echo min($startRow_getRadiatorAddressesInUse + $maxRows_getRadiatorAddressesInUse, $totalRows_getRadiatorAddressesInUse) ?> of <?php echo $totalRows_getRadiatorAddressesInUse ?><br />
  </p>
  <table border="0">
    <tr>
      <td><?php if ($pageNum_getRadiatorAddressesInUse > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_getRadiatorAddressesInUse=%d%s", $currentPage, 0, $queryString_getRadiatorAddressesInUse); ?>"><img src="images/First.gif" alt="" border="0" /></a>
          <?php } // Show if not first page ?></td>
      <td><?php if ($pageNum_getRadiatorAddressesInUse > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_getRadiatorAddressesInUse=%d%s", $currentPage, max(0, $pageNum_getRadiatorAddressesInUse - 1), $queryString_getRadiatorAddressesInUse); ?>"><img src="images/Previous.gif" alt="" border="0" /></a>
          <?php } // Show if not first page ?></td>
      <td><?php if ($pageNum_getRadiatorAddressesInUse < $totalPages_getRadiatorAddressesInUse) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_getRadiatorAddressesInUse=%d%s", $currentPage, min($totalPages_getRadiatorAddressesInUse, $pageNum_getRadiatorAddressesInUse + 1), $queryString_getRadiatorAddressesInUse); ?>"><img src="images/Next.gif" alt="" border="0" /></a>
          <?php } // Show if not last page ?></td>
      <td><?php if ($pageNum_getRadiatorAddressesInUse < $totalPages_getRadiatorAddressesInUse) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_getRadiatorAddressesInUse=%d%s", $currentPage, $totalPages_getRadiatorAddressesInUse, $queryString_getRadiatorAddressesInUse); ?>"><img src="images/Last.gif" alt="" border="0" /></a>
          <?php } // Show if not last page ?></td>
    </tr>
  </table>
  <table width="100%" border="0">
    <tr>
      <td><strong>Username</strong></td>
      <td><strong>Address Expiry</strong></td>
      <td><strong>Address</strong></td>
      <td><strong>Mask</strong></td>
    </tr>
    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;username=<?php echo $row_getRadiatorAddressesInUse['USERNAME']; ?>" title="Browse user accounting data"><?php echo $row_getRadiatorAddressesInUse['USERNAME']; ?></a></td>
      <td><?php echo $row_getRadiatorAddressesInUse['exp']; ?></td>
      <td><?php echo $row_getRadiatorAddressesInUse['YIADDR']; ?></td>
      <td><?php echo $row_getRadiatorAddressesInUse['SUBNETMASK']; ?></td>
    </tr>
    <?php } while ($row_getRadiatorAddressesInUse = mysql_fetch_assoc($getRadiatorAddressesInUse)); ?>
  </table>
  <?php } // Show if recordset not empty 
  		else { ?>
<p>There are no addresses for this address pool.</p>
            
  <?php } ?>
<?php  
	}

  	elseif ($_GET['radiator'] != "" && $_GET['addresspoolgroup'] != "") {
  		
		if (!(getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 0 || ($pageLevel > 0 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == ""))) { ?>
        
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);

		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
		
		$maxRows_getRadiatorAddressesInUse = 24;
		$pageNum_getRadiatorAddressesInUse = 0;
		if (isset($_GET['pageNum_getRadiatorAddressesInUse'])) {
		  $pageNum_getRadiatorAddressesInUse = $_GET['pageNum_getRadiatorAddressesInUse'];
		}
		$startRow_getRadiatorAddressesInUse = $pageNum_getRadiatorAddressesInUse * $maxRows_getRadiatorAddressesInUse;
		
		mysql_select_db($database_radiator, $radiator);
		$query_getRadiatorAddressesInUse = "SELECT RADIP.* FROM RADIP WHERE RADIP.SUBSCRIBERGROUP = '".$_GET['addresspoolgroup']."'";
		$query_limit_getRadiatorAddressesInUse = sprintf("%s LIMIT %d, %d", $query_getRadiatorAddressesInUse, $startRow_getRadiatorAddressesInUse, $maxRows_getRadiatorAddressesInUse);
		$getRadiatorAddressesInUse = mysql_query($query_limit_getRadiatorAddressesInUse, $radiator) or die(mysql_error());
		$row_getRadiatorAddressesInUse = mysql_fetch_assoc($getRadiatorAddressesInUse);
		
		if (isset($_GET['totalRows_getRadiatorAddressesInUse'])) {
		  $totalRows_getRadiatorAddressesInUse = $_GET['totalRows_getRadiatorAddressesInUse'];
		} else {
		  $all_getRadiatorAddressesInUse = mysql_query($query_getRadiatorAddressesInUse);
		  $totalRows_getRadiatorAddressesInUse = mysql_num_rows($all_getRadiatorAddressesInUse);
		}
		$totalPages_getRadiatorAddressesInUse = ceil($totalRows_getRadiatorAddressesInUse/$maxRows_getRadiatorAddressesInUse)-1;
		
		$queryString_getRadiatorAddressesInUse = "";
		if (!empty($_SERVER['QUERY_STRING'])) {
		  $params = explode("&", $_SERVER['QUERY_STRING']);
		  $newParams = array();
		  foreach ($params as $param) {
			if (stristr($param, "pageNum_getRadiatorAddressesInUse") == false && 
				stristr($param, "totalRows_getRadiatorAddressesInUse") == false) {
			  array_push($newParams, $param);
			}
		  }
		  if (count($newParams) != 0) {
			$queryString_getRadiatorAddressesInUse = "&" . htmlentities(implode("&", $newParams));
		  }
		}
		$queryString_getRadiatorAddressesInUse = sprintf("&totalRows_getRadiatorAddressesInUse=%d%s", $totalRows_getRadiatorAddressesInUse, $queryString_getRadiatorAddressesInUse);

		?>

Radiator >> <a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>" title="Browse Radiator server <?php echo $row_getRadiator['host']; ?>"><?php echo $row_getRadiator['descr']; ?></a> >> Fixed Address Pool >> <strong><?php echo $_GET['addresspoolgroup']; ?></strong><br />
<br />

<table width="50%" border="0">
        <tr>
          <td><strong>Total Addresses</strong></td>
          <td><strong>In Use</strong></td>
          <td><strong>% Free</strong></td>
        </tr>
        <?php
  	$count = 0;
							
								mysql_select_db($database_radiator, $radiator);
								$query_getUsedAddrCount = "SELECT COUNT(*) FROM RADIP WHERE RADIP.SUBSCRIBERGROUP = '".$_GET['addresspoolgroup']."' AND (RADIP.CIRCUITID IS NOT NULL AND RADIP.CIRCUITID != '')";
								$getUsedAddrCount = mysql_query($query_getUsedAddrCount, $radiator) or die(mysql_error());
								$row_getUsedAddrCount = mysql_fetch_assoc($getUsedAddrCount);
								$totalRows_getUsedAddrCount = mysql_num_rows($getUsedAddrCount);
								
								mysql_select_db($database_radiator, $radiator);
								$query_getTotalAddrCount = "SELECT COUNT(*) FROM RADIP WHERE RADIP.SUBSCRIBERGROUP = '".$_GET['addresspoolgroup']."'";
								$getTotalAddrCount = mysql_query($query_getTotalAddrCount, $radiator) or die(mysql_error());
								$row_getTotalAddrCount = mysql_fetch_assoc($getTotalAddrCount);
								$totalRows_getTotalAddrCount = mysql_num_rows($getTotalAddrCount);
		
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><?php echo $row_getTotalAddrCount['COUNT(*)']; ?></td>
          <td><?php echo $row_getUsedAddrCount['COUNT(*)']; ?></td>
          <td><font color="<?php if ((100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)) > 30) { echo "green"; } elseif ((100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)) > 10) { echo "orange"; } else { echo "red"; } ?>"><strong><?php printf ('%.2f', 100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)); ?></strong></font></td>
        </tr>
    </table>
     
<?php if ($totalRows_getRadiatorAddressesInUse > 0) { // Show if recordset not empty ?>
  
  <p>Displaying records <?php echo ($startRow_getRadiatorAddressesInUse + 1) ?> to <?php echo min($startRow_getRadiatorAddressesInUse + $maxRows_getRadiatorAddressesInUse, $totalRows_getRadiatorAddressesInUse) ?> of <?php echo $totalRows_getRadiatorAddressesInUse ?><br />
  </p>
  <table border="0">
    <tr>
      <td><?php if ($pageNum_getRadiatorAddressesInUse > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_getRadiatorAddressesInUse=%d%s", $currentPage, 0, $queryString_getRadiatorAddressesInUse); ?>"><img src="images/First.gif" alt="" border="0" /></a>
          <?php } // Show if not first page ?></td>
      <td><?php if ($pageNum_getRadiatorAddressesInUse > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_getRadiatorAddressesInUse=%d%s", $currentPage, max(0, $pageNum_getRadiatorAddressesInUse - 1), $queryString_getRadiatorAddressesInUse); ?>"><img src="images/Previous.gif" alt="" border="0" /></a>
          <?php } // Show if not first page ?></td>
      <td><?php if ($pageNum_getRadiatorAddressesInUse < $totalPages_getRadiatorAddressesInUse) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_getRadiatorAddressesInUse=%d%s", $currentPage, min($totalPages_getRadiatorAddressesInUse, $pageNum_getRadiatorAddressesInUse + 1), $queryString_getRadiatorAddressesInUse); ?>"><img src="images/Next.gif" alt="" border="0" /></a>
          <?php } // Show if not last page ?></td>
      <td><?php if ($pageNum_getRadiatorAddressesInUse < $totalPages_getRadiatorAddressesInUse) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_getRadiatorAddressesInUse=%d%s", $currentPage, $totalPages_getRadiatorAddressesInUse, $queryString_getRadiatorAddressesInUse); ?>"><img src="images/Last.gif" alt="" border="0" /></a>
          <?php } // Show if not last page ?></td>
    </tr>
  </table>
  <table width="100%" border="0">
    <tr>
      <td><strong>Address</strong></td>
      <td><strong>Circuit ID</strong></td>
    </tr>
    <?php
  	$count = 0;

							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><?php echo $row_getRadiatorAddressesInUse['FRAMEDIP']; ?></td>
      <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;subscribergroup=<?php echo $_GET['addresspoolgroup']; ?>&amp;search=<?php echo $row_getRadiatorAddressesInUse['CIRCUITID']; ?>&amp;dd=dd&amp;mm=mm&amp;yyyy=yyyy&amp;dd1=dd&amp;mm1=mm&amp;yyyy1=yyyy&amp;accttype=&amp;submit=_search" title="Search subscriber group for this circuit ID"><?php echo $row_getRadiatorAddressesInUse['CIRCUITID']; ?></a></td>
    </tr>
    <?php } while ($row_getRadiatorAddressesInUse = mysql_fetch_assoc($getRadiatorAddressesInUse)); ?>
  </table>
  <?php } // Show if recordset not empty 
  		else { ?>
<p>There are no addresses for this fixed address pool.</p>
            
  <?php } ?>
<?php  
	}
	
  	elseif ($_GET['radiator'] != "") { 
  		
		if (!(getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 0 || ($pageLevel > 0 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == ""))) { ?>
        
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
		mysql_select_db($database_subman, $subman);
		$query_getRadiator = "SELECT * FROM radiator WHERE id = ".$_GET['radiator']."";
		$getRadiator = mysql_query($query_getRadiator, $subman) or die(mysql_error());
		$row_getRadiator = mysql_fetch_assoc($getRadiator);
		$totalRows_getRadiator = mysql_num_rows($getRadiator);

		$hostname_radiator = $row_getRadiator['host'];
		$database_radiator = $row_getRadiator['dbname'];
		$username_radiator = $row_getRadiator['username'];
		$password_radiator = $row_getRadiator['pwd'];
		$radiator = mysql_connect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 

		mysql_select_db($database_radiator, $radiator);
		$query_getRadiatorGroups = "SELECT * FROM SUBSCRIBERGROUPS ORDER BY SUBSCRIBERGROUPS.GRPNAME";
		$getRadiatorGroups = mysql_query($query_getRadiatorGroups, $radiator) or die(mysql_error());
		$row_getRadiatorGroups = mysql_fetch_assoc($getRadiatorGroups);
		$totalRows_getRadiatorGroups = mysql_num_rows($getRadiatorGroups);

		mysql_select_db($database_radiator, $radiator);
		$query_getRadiatorAddressPools = "SELECT RADPOOL.POOL FROM RADPOOL GROUP BY RADPOOL.POOL ORDER BY RADPOOL.POOL";
		$getRadiatorAddressPools = mysql_query($query_getRadiatorAddressPools, $radiator) or die(mysql_error());
		$row_getRadiatorAddressPools = mysql_fetch_assoc($getRadiatorAddressPools);
		$totalRows_getRadiatorAddressPools = mysql_num_rows($getRadiatorAddressPools);
		
		mysql_select_db($database_radiator, $radiator);
		$query_getRadiatorFixedPools = "SELECT RADIP.SUBSCRIBERGROUP FROM RADIP GROUP BY RADIP.SUBSCRIBERGROUP ORDER BY RADIP.SUBSCRIBERGROUP";
		$getRadiatorFixedPools = mysql_query($query_getRadiatorFixedPools, $radiator) or die(mysql_error());
		$row_getRadiatorFixedPools = mysql_fetch_assoc($getRadiatorFixedPools);
		$totalRows_getRadiatorFixedPools = mysql_num_rows($getRadiatorFixedPools);
		
		$maxRows_getNASs = 25;
$pageNum_getNASs = 0;
if (isset($_GET['pageNum_getNASs'])) {
  $pageNum_getNASs = $_GET['pageNum_getNASs'];
}
$startRow_getNASs = $pageNum_getNASs * $maxRows_getNASs;

mysql_select_db($database_radiator, $radiator);
$query_getNASs = "SELECT DISTINCT(NASIDENTIFIER) FROM RADONLINE ORDER BY NASIDENTIFIER";
$query_limit_getNASs = sprintf("%s LIMIT %d, %d", $query_getNASs, $startRow_getNASs, $maxRows_getNASs);
$getNASs = mysql_query($query_limit_getNASs, $radiator) or die(mysql_error());
$row_getNASs = mysql_fetch_assoc($getNASs);

if (isset($_GET['totalRows_getNASs'])) {
  $totalRows_getNASs = $_GET['totalRows_getNASs'];
} else {
  $all_getNASs = mysql_query($query_getNASs);
  $totalRows_getNASs = mysql_num_rows($all_getNASs);
}
$totalPages_getNASs = ceil($totalRows_getNASs/$maxRows_getNASs)-1;
		?>
  		
Radiator >> <strong><?php echo $row_getRadiator['descr']; ?></strong><br /><br />
        
        <a href="#_groups">Subscriber Groups</a><br />
        <a href="#_pools">Dynamic IP Address Pools</a><br />
        <a href="#_fixedpools">Fixed IP Address Pools</a><br />
        <a href="#_nass">Network Access Servers</a><br />
        <a href="#_unknown">Unknown User Requests</a><br />
        
    <h3 id="_groups">Subscriber Groups</h3>
    
    <?php if ($totalRows_getRadiatorGroups > 0) { // Show if recordset not empty ?>
      
      <form action="" method="post" target="_self" name="frm_delete_subscriber_groups" id="frm_delete_subscriber_groups">
        <input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_getRadiatorGroups; ?>">
        <input type="hidden" name="permission_action_type" value="delete_subscriber_groups">
        
        <table width="100%" border="0">
          <tr>
            <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_subscriber_groups'), this.checked);" /></td>
            <td><strong>Subscriber Group Name</strong></td>
            <td><strong>Address Pool</strong></td>
            <td><strong>VPN VRF</strong></td>
            <td><strong>VPN Configuration</strong></td>
            <td><strong>Simultaneous Logins</strong></td>
            <td><strong>DNS Servers</strong></td>
          </tr>
          
          <tr bgcolor="#F5F5F5">
            <td>&nbsp;</td>
            <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;subscribergroup=ALL-SUBSCRIBERS" title="Browse all subscribers">ALL SUBSCRIBERS</a></td>
            <td>N/A</td>
            <td>N/A</td>
            <td>N/A</td>
            <td>N/A</td>
            <td>N/A</td>
          </tr>
          
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

								mysql_select_db($database_subman, $subman);
								$query_getRadiatorGroupVrf = "SELECT vrf.*, vpn.name as vpnname, provider.name as providername, provider.asnumber FROM vrf LEFT JOIN vpnvrf ON vpnvrf.vrf = vrf.id LEFT JOIN vpn ON vpn.id = vpnvrf.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE vrf.id = '".$row_getRadiatorGroups['VRFID']."'";
								$getRadiatorGroupVrf = mysql_query($query_getRadiatorGroupVrf, $subman) or die(mysql_error());
								$row_getRadiatorGroupVrf = mysql_fetch_assoc($getRadiatorGroupVrf);
								$totalRows_getRadiatorGroupVrf = mysql_num_rows($getRadiatorGroupVrf);

		?>
          <tr bgcolor="<?php echo $bgcolour; ?>">
            <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_getRadiatorGroups['GRPNAME']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_getRadiatorGroups['GRPNAME']; ?>"></td>
            <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;subscribergroup=<?php echo $row_getRadiatorGroups['GRPNAME']; ?>" title="Browse subscriber group"><?php echo $row_getRadiatorGroups['GRPNAME']; ?></a></td>
            <td><?php echo $row_getRadiatorGroups['POOLHINT']; ?></td>
            <td><?php echo $row_getRadiatorGroupVrf['asnumber']; ?> : <?php echo $row_getRadiatorGroupVrf['providername']; ?> : <?php echo $row_getRadiatorGroupVrf['vpnname']; ?> : <?php echo $row_getRadiatorGroupVrf['name']; ?></td>
            <td><?php echo $row_getRadiatorGroups['VPN']; ?></td>
            <td><?php echo $row_getRadiatorGroups['SIMULTANEOUSLOGIN']; ?></td>
            <td><?php echo $row_getRadiatorGroups['DNS']; ?></td>
          </tr>
          <?php } while ($row_getRadiatorGroups = mysql_fetch_assoc($getRadiatorGroups)); ?>
        </table>
        <input type="hidden" name="radiator" value="<?php echo $_GET['radiator']; ?>" />
      </form>
      <?php } // Show if recordset not empty 
  		else { ?>
<p>There are no subscriber groups for this Radiator.</p>
            
        <?php } ?>
  
  	<h3 id="_pools">Dynamic IP Address Pools</h3>
    
    <?php if ($totalRows_getRadiatorAddressPools > 0) { // Show if recordset not empty ?>
      <table width="50%" border="0">
        <tr>
          <td><strong>Address Pool Name</strong></td>
          <td><strong>Total Addresses</strong></td>
          <td><strong>In Use</strong></td>
          <td><strong>Expired</strong></td>
          <td><strong>% Free</strong></td>
        </tr>
        <?php
  	$count = 0;
							do { 
							
								mysql_select_db($database_radiator, $radiator);
								$query_getUsedAddrCount = "SELECT COUNT(*) FROM RADPOOL WHERE RADPOOL.POOL = '".$row_getRadiatorAddressPools['POOL']."' AND RADPOOL.STATE = 1";
								$getUsedAddrCount = mysql_query($query_getUsedAddrCount, $radiator) or die(mysql_error());
								$row_getUsedAddrCount = mysql_fetch_assoc($getUsedAddrCount);
								$totalRows_getUsedAddrCount = mysql_num_rows($getUsedAddrCount);
								
								mysql_select_db($database_radiator, $radiator);
								$query_getTotalAddrCount = "SELECT COUNT(*) FROM RADPOOL WHERE RADPOOL.POOL = '".$row_getRadiatorAddressPools['POOL']."'";
								$getTotalAddrCount = mysql_query($query_getTotalAddrCount, $radiator) or die(mysql_error());
								$row_getTotalAddrCount = mysql_fetch_assoc($getTotalAddrCount);
								$totalRows_getTotalAddrCount = mysql_num_rows($getTotalAddrCount);
								
								mysql_select_db($database_radiator, $radiator);
								$query_getExpiredAddrCount = "SELECT COUNT(*) FROM RADPOOL WHERE RADPOOL.POOL = '".$row_getRadiatorAddressPools['POOL']."' AND RADPOOL.STATE = 1 AND RADPOOL.EXPIRY < UNIX_TIMESTAMP(now())";
								$getExpiredAddrCount = mysql_query($query_getExpiredAddrCount, $radiator) or die(mysql_error());
								$row_getExpiredAddrCount = mysql_fetch_assoc($getExpiredAddrCount);
								$totalRows_getExpiredAddrCount = mysql_num_rows($getExpiredAddrCount);
		
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;addresspool=<?php echo $row_getRadiatorAddressPools['POOL']; ?>" title="Browse address pool"><?php echo $row_getRadiatorAddressPools['POOL']; ?></a></td>
          <td><?php echo $row_getTotalAddrCount['COUNT(*)']; ?></td>
          <td><?php echo $row_getUsedAddrCount['COUNT(*)']; ?></td>
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;addresspool=<?php echo $row_getRadiatorAddressPools['POOL']; ?>&amp;expired=1" title="Browse expired addresses for this address pool"><?php echo $row_getExpiredAddrCount['COUNT(*)']; ?></a></td>
          <td><font color="<?php if ((100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)) > 30) { echo "green"; } elseif ((100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)) > 10) { echo "orange"; } else { echo "red"; } ?>"><strong><?php printf ('%.2f', 100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)); ?></strong></font></td>
        </tr>
        <?php } while ($row_getRadiatorAddressPools = mysql_fetch_assoc($getRadiatorAddressPools)); ?>
      </table>
      <p class="text_red">Note: Expired addresses will be recovered by Radiator periodically.</p>
      <?php } // Show if recordset not empty 
  		else { ?>
<p>There are no address pools for this Radiator.</p>
            
        <?php } ?>


<h3 id="_pools">Fixed IP Address Pools</h3>
    
    <?php if ($totalRows_getRadiatorFixedPools > 0) { // Show if recordset not empty ?>
      <table width="50%" border="0">
        <tr>
          <td><strong>Subscriber Group</strong></td>
          <td><strong>Total Addresses</strong></td>
          <td><strong>In Use</strong></td>
          <td><strong>% Free</strong></td>
        </tr>
        <?php
  	$count = 0;
							do { 
							
								mysql_select_db($database_radiator, $radiator);
								$query_getUsedAddrCount = "SELECT COUNT(*) FROM RADIP WHERE RADIP.SUBSCRIBERGROUP = '".$row_getRadiatorFixedPools['SUBSCRIBERGROUP']."' AND (RADIP.CIRCUITID IS NOT NULL AND RADIP.CIRCUITID != '')";
								$getUsedAddrCount = mysql_query($query_getUsedAddrCount, $radiator) or die(mysql_error());
								$row_getUsedAddrCount = mysql_fetch_assoc($getUsedAddrCount);
								$totalRows_getUsedAddrCount = mysql_num_rows($getUsedAddrCount);
								
								mysql_select_db($database_radiator, $radiator);
								$query_getTotalAddrCount = "SELECT COUNT(*) FROM RADIP WHERE RADIP.SUBSCRIBERGROUP = '".$row_getRadiatorFixedPools['SUBSCRIBERGROUP']."'";
								$getTotalAddrCount = mysql_query($query_getTotalAddrCount, $radiator) or die(mysql_error());
								$row_getTotalAddrCount = mysql_fetch_assoc($getTotalAddrCount);
								$totalRows_getTotalAddrCount = mysql_num_rows($getTotalAddrCount);
								
								mysql_select_db($database_radiator, $radiator);
								$query_getExpiredAddrCount = "SELECT COUNT(*) FROM RADIP WHERE RADIP.SUBSCRIBERGROUP = '".$row_getRadiatorFixedPools['SUBSCRIBERGROUP']."' AND (RADIP.CIRCUITID IS NULL OR RADIP.CIRCUITID = '')";
								$getExpiredAddrCount = mysql_query($query_getExpiredAddrCount, $radiator) or die(mysql_error());
								$row_getExpiredAddrCount = mysql_fetch_assoc($getExpiredAddrCount);
								$totalRows_getExpiredAddrCount = mysql_num_rows($getExpiredAddrCount);
		
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;addresspoolgroup=<?php echo $row_getRadiatorFixedPools['SUBSCRIBERGROUP']; ?>" title="Browse fixed address pool"><?php echo $row_getRadiatorFixedPools['SUBSCRIBERGROUP']; ?></a></td>
          <td><?php echo $row_getTotalAddrCount['COUNT(*)']; ?></td>
          <td><?php echo $row_getUsedAddrCount['COUNT(*)']; ?></td>
          <td><font color="<?php if ((100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)) > 30) { echo "green"; } elseif ((100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)) > 10) { echo "orange"; } else { echo "red"; } ?>"><strong><?php printf ('%.2f', 100 - (($row_getUsedAddrCount['COUNT(*)']/$row_getTotalAddrCount['COUNT(*)']) * 100)); ?></strong></font></td>
        </tr>
        <?php } while ($row_getRadiatorFixedPools = mysql_fetch_assoc($getRadiatorFixedPools)); ?>
      </table>
      <?php } // Show if recordset not empty 
  		else { ?>
<p>There are no fixed address pools for this Radiator.</p>
            
        <?php } ?>
        
<h3 id="_nass">Network Access Servers</h3>
<?php if ($totalRows_getNASs > 0) { // Show if recordset not empty ?>
  <table width="50%" border="0">
    <tr>
      <td><strong>NAS IP Address</strong></td>
      <td><strong>Current Session Count</strong></td>
    </tr>
    <?php do { 
  		
		mysql_select_db($database_radiator, $radiator);
		$query_getNASCount = "SELECT COUNT(*) FROM RADONLINE WHERE RADONLINE.NASIDENTIFIER = '".$row_getNASs['NASIDENTIFIER']."'";
		$getNASCount = mysql_query($query_getNASCount, $radiator) or die(mysql_error());
		$row_getNASCount = mysql_fetch_assoc($getNASCount);
		$totalRows_getNASCount = mysql_num_rows($getNASCount);
								
  		$count++;
		if ($count % 2) {
			$bgcolour = "#EAEAEA";
		}
		else {
			$bgcolour = "#F5F5F5";
		} 
		
		$sessiontotal += $row_getNASCount['COUNT(*)']; ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><a href="?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;nas=<?php echo $row_getNASs['NASIDENTIFIER']; ?>" title="Browse NAS subscribers"><?php echo $row_getNASs['NASIDENTIFIER']; ?></a></td>
      <td><?php echo $row_getNASCount['COUNT(*)']; ?></td>
    </tr>
    <?php } while ($row_getNASs = mysql_fetch_assoc($getNASs)); ?>
    <tr><td align="right"><strong>Total session count &nbsp;&nbsp;&nbsp;</strong></td><td><strong><?php echo $sessiontotal; ?></strong></td></tr>
  </table>
  <?php } // Show if recordset not empty 
  	else { ?>
    	<p>There are no NASs to display.</p>
  <?php } ?>
  
<h3 id="_unknown">Unknown User Authentication Requests</h3>

<p>This section shows the last 10 requests that have no matching username on Radiator.</p>

<?php 
mysql_select_db($database_radiator, $radiator);
$query_getUnknownUsers = "SELECT RADAUTHLOG.*, FROM_UNIXTIME(RADAUTHLOG.TIME_STAMP) AS TS FROM RADAUTHLOG WHERE RADAUTHLOG.REASON = 'No such user' ORDER BY RADAUTHLOG.TIME_STAMP DESC LIMIT 10";
$getUnknownUsers = mysql_query($query_getUnknownUsers, $radiator) or die(mysql_error());
$row_getUnknownUsers = mysql_fetch_assoc($getUnknownUsers);
$totalRows_getUnknownUsers = mysql_num_rows($getUnknownUsers);

?>
<?php if ($totalRows_getUnknownUsers > 0) { // Show if recordset not empty ?>
  <table width="50%" border="0">
    <tr>
      <td><strong>Time Stamp</strong></td>
      <td><strong>Username</strong></td>
    </tr>
    <?php do { 
  		
		$count++;
		if ($count % 2) {
			$bgcolour = "#EAEAEA";
		}
		else {
			$bgcolour = "#F5F5F5";
		}
		?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><?php echo $row_getUnknownUsers['TS']; ?></td>
      <td><?php echo $row_getUnknownUsers['USERNAME']; ?></td>
    </tr>
    <?php } while ($row_getUnknownUsers = mysql_fetch_assoc($getUnknownUsers)); ?>
  </table>
  <?php } // Show if recordset not empty 
  	else { ?>
    	<p>There are no unknown user authentication requests to display.</p>
  <?php } ?>
  
<?php } 

	} ?>
  
  </div>

</div>
<div id="containerView_footerLeft">



</div>

  <?php if ($_GET['print'] != 1) { ?>
  
  <div id="containerView_footerRight">
  
  	<b class="spiffy">
  <b class="spiffy1"><b></b></b>
  <b class="spiffy2"><b></b></b>
  <b class="spiffy3"></b>
  <b class="spiffy4"></b>
  <b class="spiffy5"></b></b>

  <div class="spiffyfg">
	
    <br />
    
  	<?php if ($_GET['browse'] == "radiators") { ?>
  
<form action="radiatorMgmtView.php?browse=radiators&amp;radiator=<?php echo $_GET['radiator']; ?>&amp;subscribergroup=<?php echo $_GET['subscribergroup']; ?>&amp;username=<?php echo $_GET['username']; ?>" method="post" name="frm_radiator_action" target="_self" id="frm_radiator_action" onsubmit="if (this.action.value == 'delete_group') { document.frm_delete_subscriber_groups.submit(); return false; } if (this.action.value == 'delete_route') { document.frm_delete_subscriber_routes.submit(); return false; } if (this.action.value == 'delete_arbroute') { document.frm_delete_subscriber_arbroutes.submit(); return false; }">
        	<select name="action" id="action" class="input_standard">
            	<?php if ($_GET['username'] != "") {
					mysql_select_db($database_radiator, $radiator);
					$query_getSubscriber = "SELECT SUBSCRIBERS.* FROM SUBSCRIBERS WHERE SUBSCRIBERS.USERNAME = '".$_GET['username']."'";
					$getSubscriber = mysql_query($query_getSubscriber, $radiator) or die(mysql_error());
					$row_getSubscriber = mysql_fetch_assoc($getSubscriber);
					$totalRows_getSubscriber = mysql_num_rows($getSubscriber);
				} ?>
            	<?php if ($pageLevel == 127) { ?>
            	<option value="add_radiator">Add a Radiator Server</option>
                <?php } ?>
                <?php if ($_GET['radiator'] != "") { ?>
                <?php if (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "")) { ?>
                <option value="add_group">Add a subscriber group</option>
                <option value="add_fixedpool">Add a fixed IP address pool</option>
                <?php if ($_GET['subscribergroup'] != "") { ?>
                <option value="edit_group">Edit subscriber group</option>
                <?php } ?>
                <?php } ?>
                <?php if ($totalRows_getRadiatorGroups > 0 && (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 20 || ($pageLevel > 20 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == ""))) { ?>
                <option value="delete_group">Delete subscriber group</option>
                <?php } ?>
                <?php } ?>
                <?php if ($_GET['username'] != "" && (getRadiatorFeatureLevel($_GET['radiator'],'add_route',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_route',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_route',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == "") || ($pageLevel > 10 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'add_route',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == ""))) { ?>
                <option value="add_route">Add a subscriber route</option>
                <?php } ?>
                <?php if ($_GET['username'] != "" && (getRadiatorFeatureLevel($_GET['radiator'],'add_arbitrary_route',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_arbitrary_route',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_arbitrary_route',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == "") || ($pageLevel > 10 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'add_arbitrary_route',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == ""))) { ?>
                <option value="add_arbitraryroute">Add an arbitrary subscriber route</option>
                <?php } ?>
                <?php if ($_GET['username'] != "" && (getRadiatorFeatureLevel($_GET['radiator'],'edit_subscriber',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'edit_subscriber',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'edit_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == "") || ($pageLevel > 10 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'edit_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == ""))) { ?>
                <option value="edit_subscriber">Edit subscriber</option>
                <?php } ?>
                <?php if ($_GET['subscribergroup'] != "" && $_GET['subscribergroup'] != "ALL-SUBSCRIBERS" && (getRadiatorFeatureLevel($_GET['radiator'],'add_subscriber',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$_GET['subscribergroup'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_subscriber',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 10 && getRadiatorFeatureLevel($_GET['radiator'],'add_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$_GET['subscribergroup'],$_SESSION['MM_Username']) == "") || ($pageLevel > 10 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'add_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$_GET['subscribergroup'],$_SESSION['MM_Username']) == ""))) { ?>
                <option value="add_subscriber">Add a subscriber</option>
                <?php } ?>
                <?php if ($_GET['username'] != "" && (getRadiatorFeatureLevel($_GET['radiator'],'delete_subscriber',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) > 20 && getRadiatorFeatureLevel($_GET['radiator'],'delete_subscriber',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 20 && getRadiatorFeatureLevel($_GET['radiator'],'delete_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == "") || ($pageLevel > 20 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'delete_subscriber',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == ""))) { ?>
                <option value="delete_subscriber">Delete subscriber</option>
                <?php } ?>
                <?php if ($totalRows_getSubscriberArbRoutes > 0 && (getRadiatorFeatureLevel($_GET['radiator'],'delete_arbitrary_routes',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) > 20 && getRadiatorFeatureLevel($_GET['radiator'],'delete_arbitrary_routes',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 20 && getRadiatorFeatureLevel($_GET['radiator'],'delete_arbitrary_routes',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == "") || ($pageLevel > 20 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'delete_arbitrary_routes',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == ""))) { ?>
                <option value="delete_arbroute">Delete arbitrary subscriber route(s)</option>
                <?php } ?>
                <?php if ($totalRows_getSubscriberRoutes > 0 && (getRadiatorFeatureLevel($_GET['radiator'],'delete_routes',$_SESSION['MM_Username']) > 20 || (getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) > 20 && getRadiatorFeatureLevel($_GET['radiator'],'delete_routes',$_SESSION['MM_Username']) == "") || (getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) > 20 && getRadiatorFeatureLevel($_GET['radiator'],'delete_routes',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == "") || ($pageLevel > 20 && getRadiatorLevel($_GET['radiator'],$_SESSION['MM_Username']) == "" && getRadiatorFeatureLevel($_GET['radiator'],'delete_routes',$_SESSION['MM_Username']) == "" && getRadiatorGroupLevel($_GET['radiator'],$row_getSubscriber['SUBSCRIBERGROUP'],$_SESSION['MM_Username']) == ""))) { ?>
                <option value="delete_route">Delete subscriber route(s)</option>
                <?php } ?>
            </select>
            <input type="submit" value="Go" class="input_standard" id="submit" name="submit" />
    </form>
    <?php } ?>

</div>
<?php } ?>

</div>

</div>

</div>
<?php include('includes/ipm_footer.php'); ?>
</body>
</html>
<?php
mysql_free_result($getRadiatorGroups);

mysql_free_result($getRadiatorAddressPools);

mysql_free_result($getNASs);

mysql_free_result($getUnknownUsers);

mysql_free_result($getSubscriber);

mysql_free_result($getUserFailedAuth);

mysql_free_result($getSubscriber1);

mysql_free_result($subscriberGroups);

mysql_free_result($getActiveSessions);

mysql_free_result($check_routes);

mysql_free_result($getGroupAccounting);

mysql_free_result($networks);

mysql_free_result($getSubscriberNetwork);

mysql_free_result($getSubscriberRoutes);

mysql_free_result($getGroupSubscribers);

mysql_free_result($vpnvrfs);

mysql_free_result($addresspools);

mysql_free_result($getRadiatorAccountingData);

mysql_free_result($getRadiatorAccoutingData);

mysql_free_result($getRadiatorAddressesInUse);

mysql_free_result($radiators);

mysql_free_result($getRadiator);
?>
