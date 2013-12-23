<?php require_once('Connections/subman.php'); ?>
<?php include('includes/ipm_class.php'); ?>

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
?>
<?php include('includes/standard_functions.php'); ?>
<?php require_once('Net/IPv4.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}

if ($_GET['submit'] == "_search") {
	$_SESSION['search'] = $_GET['search'];
}
if ($_GET['type'] != "") {
	$_SESSION['incidenttype'] = $_GET['type'];
}
elseif ($_GET['submit'] == "_cancel_search") {
	$_SESSION['search'] = "";
	$_SESSION['incidenttype'] = "";
}

$ipm = new ipm;

$pageLevel = $ipm->getPageLevel(6,$_SESSION['MM_Username']);

mysql_select_db($database_subman, $subman);
$query_damits = "SELECT * FROM damit ORDER BY damit.`host`, damit.descr";
$damits = mysql_query($query_damits, $subman) or die(mysql_error());
$row_damits = mysql_fetch_assoc($damits);
$totalRows_damits = mysql_num_rows($damits);


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO damit (`host`, descr, username, pwd, dbname) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['host'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['pwd'], "text"),
                       GetSQLValueString($_POST['dbname'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "damitMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form10")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
	
  $insertSQL = sprintf("INSERT INTO bgp (address, descr, username, passwd, enable, type, nexthop, tags) VALUES (%s, %s, %s, %s, %s, %s, %s, %s) ON DUPLICATE KEY UPDATE descr=%s, username=%s, passwd=%s, enable=%s, type=%s, nexthop=%s, tags=%s",
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['passwd'], "text"),
                       GetSQLValueString($_POST['enable'], "text"),
                       GetSQLValueString($_POST['type'], "text"),
                       GetSQLValueString($_POST['nexthop'], "text"),
                       GetSQLValueString($_POST['tags'], "text"),
					   GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['passwd'], "text"),
                       GetSQLValueString($_POST['enable'], "text"),
                       GetSQLValueString($_POST['type'], "text"),
                       GetSQLValueString($_POST['nexthop'], "text"),
                       GetSQLValueString($_POST['tags'], "text"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($insertSQL, $damit) or die(mysql_error());
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_alertgroup")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
		
  $insertSQL = sprintf("INSERT INTO emailgroup (name, alertlevel) VALUES (%s, %s)",
                       GetSQLValueString($_POST['groupname'], "text"),
					   GetSQLValueString($_POST['level'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($insertSQL, $damit) or die(mysql_error());

  $insertGoTo = "damitMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form9")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
		
  $insertSQL = sprintf("INSERT INTO email (email, emailgroup) VALUES (%s, %s) ON DUPLICATE KEY UPDATE email=%s",
                       GetSQLValueString($_POST['email'], "text"),
					   GetSQLValueString($_POST['alertgroup'], "int"),
					   GetSQLValueString($_POST['email'], "text"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($insertSQL, $damit) or die(mysql_error());

  $insertGoTo = "damitMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form8")) {
	
	if (Net_IPv4::validateIP($_POST['prefix'])) {
		
		mysql_select_db($database_subman, $subman);
		$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
		$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
		$row_getDamit = mysql_fetch_assoc($getDamit);
		$totalRows_getDamit = mysql_num_rows($getDamit);
	
		$hostname_damit = $row_getDamit['host'];
		$database_damit = $row_getDamit['dbname'];
		$username_damit = $row_getDamit['username'];
		$password_damit = $row_getDamit['pwd'];
		$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
		mysql_select_db($database_damit, $damit); 
	
	  $insertSQL = sprintf("INSERT INTO prefixexception (`prefix`, mask, descr) VALUES (%s, %s, %s)",
						   GetSQLValueString($_POST['prefix'], "text"),
						   GetSQLValueString($_POST['mask'], "int"),
						   GetSQLValueString($_POST['descr'], "text"));
	
	  mysql_select_db($database_damit, $damit);
	  $Result1 = mysql_query($insertSQL, $damit) or die(mysql_error());
	
	  $insertGoTo = "damitMgmtView.php";
	  if (isset($_SERVER['QUERY_STRING'])) {
		$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
		$insertGoTo .= $_SERVER['QUERY_STRING'];
	  }
	  header(sprintf("Location: %s", $insertGoTo));
	  
	}
	
}

if (isset($_POST['reprofile']) && $_POST['reprofile'] == 1) {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
	
	mysql_select_db($database_damit, $damit);
	$query_interfacepps = "SELECT DAYOFWEEK(FROM_UNIXTIME(ts)) AS dow, HOUR(FROM_UNIXTIME(ts)) AS hod, AVG(ppsin) AS avgppsin, AVG(ppsout) AS avgppsout FROM interfacepps WHERE device = ".$_POST['device']." AND ifindex = ".$_POST['ifindex']." GROUP BY DAYOFWEEK(FROM_UNIXTIME(ts)), HOUR(FROM_UNIXTIME(ts))";
	$interfacepps = mysql_query($query_interfacepps, $damit) or die(mysql_error());
	$row_interfacepps = mysql_fetch_assoc($interfacepps);
	$totalRows_interfacepps = mysql_num_rows($interfacepps);
	
	do {
		
		$insertSQL = sprintf("INSERT INTO dayaverage (device, ifindex, dayofweek, hourofday, ppsin, ppsout) VALUES (%s, %s, %s, %s, %s, %s) ON DUPLICATE KEY UPDATE ppsin=%s, ppsout=%s",
						   GetSQLValueString($_POST['device'], "int"),
						   GetSQLValueString($_POST['ifindex'], "int"),
						   GetSQLValueString($row_interfacepps['dow'], "int"),
						   GetSQLValueString($row_interfacepps['hod'], "int"),
						   GetSQLValueString($row_interfacepps['avgppsin'], "int"),
						   GetSQLValueString($row_interfacepps['avgppsout'], "int"),
						   GetSQLValueString($row_interfacepps['avgppsin'], "int"),
						   GetSQLValueString($row_interfacepps['avgppsout'], "int"));
	
	  mysql_select_db($database_damit, $damit);
	  $Result1 = mysql_query($insertSQL, $damit) or die(mysql_error());
		
	} while ($row_interfacepps = mysql_fetch_assoc($interfacepps));
	
	$insertGoTo = "damitMgmtView.php";
	if (isset($_SERVER['QUERY_STRING'])) {
		$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
		$insertGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $insertGoTo));

}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_bgps') {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
	
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
	
			$deleteSQL = sprintf("DELETE FROM bgp WHERE id=%s",
				   GetSQLValueString($_POST['id_'.($i+1)], "text"));
	
			mysql_select_db($database_damit, $damit);
			$Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "damitMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_alertgroups') {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
	
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
			
			$alertgroupID = $_POST['id_'.($i+1)];
			
			mysql_select_db($database_damit, $damit);
			$query_checkEmails = "SELECT * FROM email WHERE emailgroup = ".$alertgroupID."";
			$checkEmails = mysql_query($query_checkEmails, $damit) or die(mysql_error());
			$row_checkEmails = mysql_fetch_assoc($checkEmails);
			$totalRows_checkEmails = mysql_num_rows($checkEmails);
			
			if ($totalRows_checkEmails == 0) {
				
				$deleteSQL = sprintf("DELETE FROM emailgroup WHERE id=%s",
					   GetSQLValueString($_POST['id_'.($i+1)], "text"));
		
				mysql_select_db($database_damit, $damit);
				$Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());
				
			}
			
		}
		
	}
	
  $deleteGoTo = "damitMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_emails') {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
	
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
	
			$deleteSQL = sprintf("DELETE FROM email WHERE id=%s",
				   GetSQLValueString($_POST['id_'.($i+1)], "text"));
	
			mysql_select_db($database_damit, $damit);
			$Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "damitMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_prefixes') {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
	
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
	
			$deleteSQL = sprintf("DELETE FROM prefixexception WHERE id=%s",
				   GetSQLValueString($_POST['id_'.($i+1)], "text"));
	
			mysql_select_db($database_damit, $damit);
			$Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "damitMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['permission_action_type']) && $_POST['permission_action_type'] == 'delete_asses') {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
	
	for ($i = 0; $i < $_POST['totalRows_permissions']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
	
			$deleteSQL = sprintf("DELETE FROM asses WHERE id=%s",
				   GetSQLValueString($_POST['id_'.($i+1)], "text"));
	
			mysql_select_db($database_damit, $damit);
			$Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "damitMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form7")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
	
  $insertSQL = sprintf("INSERT INTO asses (asnumber) VALUES (%s)",
                       GetSQLValueString($_POST['asnumber'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($insertSQL, $damit) or die(mysql_error());

  $insertGoTo = "damitMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form6")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
			
  $updateSQL = sprintf("UPDATE device SET name=%s, address=%s, community=%s, netflowname=%s, basepps=%s, avgtolerance=%s WHERE id=%s",
                       GetSQLValueString($_POST['devicename'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['community'], "text"),
					   GetSQLValueString($_POST['netflowname'], "text"),
					   GetSQLValueString($_POST['basepps'], "int"),
					   GetSQLValueString($_POST['tolerance'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($updateSQL, $damit) or die(mysql_error());

  $updateGoTo = "damitMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST['device'])) && ($_POST['device'] != "") && (isset($_POST['confirm']))) {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
	
  $deleteSQL = sprintf("DELETE FROM device WHERE id=%s",
                       GetSQLValueString($_POST['device'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());

  $deleteSQL = sprintf("DELETE FROM dayaverage WHERE device=%s",
                       GetSQLValueString($_POST['device'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());
	
	mysql_select_db($database_damit, $damit);
	$query_getIncidents = "SELECT * FROM incident WHERE device = ".$_POST['device']."";
	$getIncidents = mysql_query($query_getIncidents, $damit) or die(mysql_error());
	$row_getIncidents = mysql_fetch_assoc($getIncidents);
	$totalRows_getIncidents = mysql_num_rows($getIncidents);
	
	do {
		
		  $deleteSQL = sprintf("DELETE FROM netflow WHERE id=%s",
							   GetSQLValueString($row_getIncidents['id'], "int"));
		
		  mysql_select_db($database_damit, $damit);
		  $Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());
		  
		  $deleteSQL = sprintf("DELETE FROM netflowanalysis WHERE id=%s",
							   GetSQLValueString($row_getIncidents['id'], "int"));
		
		  mysql_select_db($database_damit, $damit);
		  $Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());

	} while ($row_getIncidents = mysql_fetch_assoc($getIncidents));

  $deleteSQL = sprintf("DELETE FROM incident WHERE device=%s",
                       GetSQLValueString($_POST['device'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());
  
  $deleteSQL = sprintf("DELETE FROM interfacepps WHERE device=%s",
                       GetSQLValueString($_POST['device'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());

  $deleteSQL = sprintf("DELETE FROM interfaces WHERE device=%s",
                       GetSQLValueString($_POST['device'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());

  $deleteSQL = sprintf("DELETE FROM log WHERE device=%s",
                       GetSQLValueString($_POST['device'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());

  $deleteSQL = sprintf("DELETE FROM ppssamples WHERE device=%s",
                       GetSQLValueString($_POST['device'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($deleteSQL, $damit) or die(mysql_error());
  
  $deleteGoTo = "damitMgmtView.php?browse=damits&damit=".$_POST['damit'];
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form5")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
	
  $insertSQL = sprintf("INSERT INTO device (name, address, community, polldate, netflowname, basepps, avgtolerance) VALUES (%s, %s, %s, unix_timestamp(now()), %s, %s, %s)",
                       GetSQLValueString($_POST['devicename'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['community'], "text"),
					   GetSQLValueString($_POST['netflowname'], "text"),
					   GetSQLValueString($_POST['basepps'], "int"),
					   GetSQLValueString($_POST['tolerance'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($insertSQL, $damit) or die(mysql_error());

  $insertGoTo = "damitMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form4")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
			
  $updateSQL = sprintf("UPDATE `system` SET restart=%s WHERE id=%s",
                       GetSQLValueString($_POST['restart'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($updateSQL, $damit) or die(mysql_error());

}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
	
  $updateSQL = sprintf("UPDATE `system` SET timer=%s, profiling=%s, ppsthreshold=%s, mexpiry=%s, emailfrom=%s WHERE id=%s",
                       GetSQLValueString($_POST['timer'], "int"),
                       GetSQLValueString($_POST['profiling'], "int"),
                       GetSQLValueString($_POST['ppsthreshold'], "int"),
					   GetSQLValueString($_POST['mexpiry'], "int"),
					   GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($updateSQL, $damit) or die(mysql_error());

  #$updateGoTo = "damitMgmtView.php?browse=damits&damit=".$_POST['damit'];
  #header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getDamit = "SELECT * FROM damit WHERE id = ".$_POST['damit']."";
	$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
	$row_getDamit = mysql_fetch_assoc($getDamit);
	$totalRows_getDamit = mysql_num_rows($getDamit);

	$hostname_damit = $row_getDamit['host'];
	$database_damit = $row_getDamit['dbname'];
	$username_damit = $row_getDamit['username'];
	$password_damit = $row_getDamit['pwd'];
	$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
	mysql_select_db($database_damit, $damit); 
			
  $updateSQL = sprintf("UPDATE interfaces SET monitored=%s WHERE id=%s",
                       GetSQLValueString(isset($_POST['monitored']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_damit, $damit);
  $Result1 = mysql_query($updateSQL, $damit) or die(mysql_error());

  $updateGoTo = "damitMgmtView.php?browse=damits&damit=".$_POST['damit']."&device=".$_POST['device'];
  header(sprintf("Location: %s", $updateGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="css/default.css" rel="stylesheet" type="text/css" />
<link href="dropdowntabfiles/bluetabs.css" rel="stylesheet" type="text/css" />
<link href="css/rounded.css" rel="stylesheet" type="text/css" />
<link href="css/template.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/jqueryslidemenu.css" />

<!--[if lte IE 7]>
<style type="text/css">
html .jqueryslidemenu{height: 1%;} /*Holly Hack for IE7 and below*/
</style>
<![endif]-->

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IP Manager</title>
<!-- SmartMenus 6 config and script core files -->
<script type="text/javascript" src="c_config.js"></script>
<script type="text/javascript" src="c_smartmenus.js"></script>
<!-- SmartMenus 6 config and script core files -->

<!-- SmartMenus 6 Scrolling for Overlong Menus add-on -->
<script type="text/javascript" src="c_addon_scrolling.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
<script type="text/javascript" src="jqueryslidemenu.js"></script>
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
//-->

function checkAll(theForm, status) {
for (i=0,n=theForm.elements.length;i<n;i++)
theForm.elements[i].checked = status;
}
function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
</script>
</head>

<body onload="<?php if ($_GET['print'] == 1) { ?>window.print(); window.close();<?php } ?><?php if ($_SESSION['errstr'] != "") { echo "alert('".$_SESSION['errstr']."');"; } ?>">

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
					<h2>DAM-it Management<?php if ($_GET['print'] != 1) { ?> <a href="?<?php echo $_SERVER['QUERY_STRING']; ?>&amp;print=1" target="_blank"><img src="images/icon_print.gif" border="0" alt="Print page" align="absmiddle" style="text-decoration:none" /></a><?php } ?></h2>
					
<?php if ($_GET['print'] != 1) { ?>

<div id="myslidemenu" class="jqueryslidemenu">
<ul>
  <?php include('includes/standard_nav.php'); ?>
  <li><a class="NOLINK"><img src="images/damit_icon.gif" alt="DAM-it Servers" align="absmiddle" width="20" height="20" border="0" /> DAM-it Servers</a>
   	  <?php if ($totalRows_damits > 0) { // Show if recordset not empty ?>
        	  <ul>
        	    <?php do { ?>
       	        <?php if (getDamitLevel($row_damits['id'],$_SESSION['MM_Username']) > 0 || ($pageLevel > 0 && getDamitLevel($row_damits['id'],$_SESSION['MM_Username']) == "")) { ?>      
       	        <li><a href="?browse=damits&amp;damit=<?php echo $row_damits['id']; ?>" title="<?php echo $row_damits['host']; ?>"><?php echo $row_damits['descr']; ?></a></li>
       	        <?php } ?>
       	        <?php } while ($row_damits = mysql_fetch_assoc($damits)); ?>
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

<?php if (!($pageLevel > 0)) { ?>
<p class="text_red">Error: You are not authorised to view the selected content.</p>
<?php 
	exit();
} ?>


<p>&nbsp;</p>

<div id="containerView">
  <div id="containerView_body">
  	
<?php 
  	if ($_GET['browse'] = "damits") { 
		
		if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 0 || ($pageLevel > 0 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
        
		if ($_POST['action'] == "add_damit") { 
		
			if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
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
          <td><input type="submit" class="input_standard" value="Add DAM-it server" /></td>
        </tr>
      </table>
      <input type="hidden" name="MM_insert" value="form1" />
    </form>
    <p>&nbsp;</p>
<?php }
		elseif ($_GET['damit'] != "" && $_GET['incident'] != "") {
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit); 
			
			mysql_select_db($database_damit, $damit);
			$query_getIncident = "SELECT incident.*, from_unixtime(ts) as TS, device.name as devicename, interfaces.ifdescr FROM incident LEFT JOIN device ON device.id = incident.device LEFT JOIN interfaces ON interfaces.ifindex = incident.ifindex AND interfaces.device = device.id WHERE incident.id = '".$_GET['incident']."'";
			$getIncident = mysql_query($query_getIncident, $damit) or die(mysql_error());
			$row_getIncident = mysql_fetch_assoc($getIncident);
			$totalRows_getIncident = mysql_num_rows($getIncident);
			
			mysql_select_db($database_damit, $damit);
			$query_netflowdata = "SELECT * FROM netflow WHERE netflow.incident = ".$_GET['incident']."";
			$netflowdata = mysql_query($query_netflowdata, $damit) or die(mysql_error());
			$row_netflowdata = mysql_fetch_assoc($netflowdata);
			$totalRows_netflowdata = mysql_num_rows($netflowdata);

			mysql_select_db($database_damit, $damit);
$query_netflowanalysis = "SELECT * FROM netflowanalysis WHERE netflowanalysis.incident = ".$_GET['incident']."";
$netflowanalysis = mysql_query($query_netflowanalysis, $damit) or die(mysql_error());
$row_netflowanalysis = mysql_fetch_assoc($netflowanalysis);
$totalRows_netflowanalysis = mysql_num_rows($netflowanalysis);

mysql_select_db($database_damit, $damit);
$query_incidentlog = "SELECT log.*, device.name as devicename, interfaces.ifdescr, FROM_UNIXTIME(ts) AS TS FROM log LEFT JOIN device ON device.id = log.device LEFT JOIN interfaces ON interfaces.ifindex = log.ifindex AND interfaces.device = log.device WHERE log.incident = ".$_GET['incident']." ORDER BY id DESC";
$incidentlog = mysql_query($query_incidentlog, $damit) or die(mysql_error());
$row_incidentlog = mysql_fetch_assoc($incidentlog);
$totalRows_incidentlog = mysql_num_rows($incidentlog);
		?>
        
        DAM-it >> <a href="?browse=damits&amp;damit=<?php echo $row_getDamit['id']; ?>" title="Browse DAM-it server"><?php echo $row_getDamit['descr']; ?></a> &gt;&gt; Incident &gt;&gt;<strong> [#<?php echo $row_getIncident['id']; ?>]  <?php echo $row_getIncident['devicename']; ?> <?php echo $row_getIncident['ifdescr']; ?> <?php echo $row_getIncident['TS']; ?></strong>
			
            <br /><br />
            
    <a href="#_netflow">Netflow Data</a><br />
    <a href="#_analysis">Netflow Analysis</a>
    
<h3 id="_netflow">Netflow Data</h3>
    <?php if ($totalRows_netflowdata > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0">
    <tr>
      <td><strong>Analysis Type</strong></td>
      <td><strong>Protocol</strong></td>
      <td><strong>Source IP</strong></td>
      <td><strong>Source Port</strong></td>
      <td><strong>Destination IP</strong></td>
      <td><strong>Destination Port</strong></td>
      <td><strong>PPS</strong></td>
      <td><strong>BPS</strong></td>
      <td><strong>Bytes</strong></td>
      <td><strong>Flows</strong></td>
      <td><strong>Flags</strong></td>
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
      <td><?php echo $row_netflowdata['qrytype']; ?></td>
      <td><?php echo $row_netflowdata['proto']; ?></td>
      <td><?php echo $row_netflowdata['srcip']; ?></td>
      <td><?php echo $row_netflowdata['srcport']; ?></td>
      <td><?php echo $row_netflowdata['dstip']; ?></td>
      <td><?php echo $row_netflowdata['dstport']; ?></td>
      <td><?php echo $row_netflowdata['pps']; ?></td>
      <td><?php echo $row_netflowdata['bps']; ?></td>
      <td><?php echo $row_netflowdata['bytes']; ?></td>
      <td><?php echo $row_netflowdata['flows']; ?></td>
      <td><?php echo $row_netflowdata['flags']; ?></td>
      </tr>
    <?php } while ($row_netflowdata = mysql_fetch_assoc($netflowdata)); ?>
  </table>
<?php } // Show if recordset not empty 
  	else { ?>
    	<p>There are no Netflow data to display. DAM-it may be waiting for Netflow to catch up.</p>
    <?php } ?>
    
    <h3 id="_analysis">Netflow Analysis</h3>
    <?php if ($totalRows_netflowanalysis > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0">
    <tr>
      <td><strong>Flow Count</strong></td>
      <td><strong>Protocol</strong></td>
      <td><strong>Destination IP</strong></td>
      <td><strong>Destination Port</strong></td>
      <td><strong>PPS Total</strong></td>
      <td><strong>Flags</strong></td>
      <td><strong>Points Awarded</strong></td>
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
      <td><?php echo $row_netflowanalysis['flowcount']; ?></td>
      <td><?php echo $row_netflowanalysis['proto']; ?></td>
      <td><?php echo $row_netflowanalysis['dstip']; ?></td>
      <td><?php echo $row_netflowanalysis['dstport']; ?></td>
      <td><?php echo $row_netflowanalysis['pps']; ?></td>
      <td><?php echo $row_netflowanalysis['flags']; ?></td>
      <td><strong><?php echo $row_netflowanalysis['points']; ?></strong></td>
    </tr>
    <?php } while ($row_netflowanalysis = mysql_fetch_assoc($netflowanalysis)); ?>
  </table>
<?php } // Show if recordset not empty 
  	else { ?>
    	<p>There are no Netflow analysis data to display.</p>
     <?php } ?>
    
    <h3 id="_log">Incident Log</h3>
    
    <?php if ($totalRows_incidentlog > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0">
    <tr>
      <td width="40">&nbsp;</td>
      <td><strong>Device</strong></td>
      <td><strong>Timestamp</strong></td>
      <td><strong>Message</strong></td>
    </tr>
    <?php
  	$count = 0;
							do { 
							
								switch($row_incidentlog['msgtype']) {
									case 1: $colour = "orange";
									break;
									case 2: $colour = "red";
									break;
									case 3: $colour = "green";
									break;
								}
								
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td bgcolor="<?php echo $colour; ?>"><img src="images/trbox.gif" /></td>
      <td><?php echo $row_incidentlog['devicename']; ?></td>
      <td><?php echo $row_incidentlog['TS']; ?></td>
      <td><strong><?php echo $row_incidentlog['message']; ?></strong></td>
    </tr>
    <?php } while ($row_incidentlog = mysql_fetch_assoc($incidentlog)); ?>
  </table>
<?php } // Show if recordset not empty 
  	else { ?>
    	<p>There are no log messages to display.</p>
    <?php } ?>
    
<?php
		}
		elseif ($_GET['damit'] != "" && $_GET['device'] != "" && $_GET['interface'] != "" && $_GET['day'] != "" && $_GET['hour']) {
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit); 
			
			mysql_select_db($database_damit, $damit);
			$query_getDevice = "SELECT * FROM device WHERE device.id = '".$_GET['device']."'";
			$getDevice = mysql_query($query_getDevice, $damit) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_damit, $damit);
			$query_getInterface = "SELECT * FROM interfaces WHERE interfaces.device = '".$_GET['device']."' AND interfaces.ifindex = '".$_GET['interface']."'";
			$getInterface = mysql_query($query_getInterface, $damit) or die(mysql_error());
			$row_getInterface = mysql_fetch_assoc($getInterface);
			$totalRows_getInterface = mysql_num_rows($getInterface);
			
			$maxRows_ppssamples = 25;
			$pageNum_ppssamples = 0;
			if (isset($_GET['pageNum_ppssamples'])) {
			  $pageNum_ppssamples = $_GET['pageNum_ppssamples'];
			}
			$startRow_ppssamples = $pageNum_ppssamples * $maxRows_ppssamples;
			
			mysql_select_db($database_damit, $damit);
			$query_ppssamples = "SELECT *, FROM_UNIXTIME(ts) AS TS FROM interfacepps WHERE interfacepps.device = '".$_GET['device']."' AND interfacepps.ifindex = '".$_GET['interface']."' AND DAYOFWEEK(FROM_UNIXTIME(interfacepps.ts)) = '".$_GET['day']."' AND HOUR(FROM_UNIXTIME(interfacepps.ts)) = '".$_GET['hour']."' ORDER BY ts ASC";
			$query_limit_ppssamples = sprintf("%s LIMIT %d, %d", $query_ppssamples, $startRow_ppssamples, $maxRows_ppssamples);
			$ppssamples = mysql_query($query_limit_ppssamples, $damit) or die(mysql_error());
			$row_ppssamples = mysql_fetch_assoc($ppssamples);
			
			if (isset($_GET['totalRows_ppssamples'])) {
			  $totalRows_ppssamples = $_GET['totalRows_ppssamples'];
			} else {
			  $all_ppssamples = mysql_query($query_ppssamples);
			  $totalRows_ppssamples = mysql_num_rows($all_ppssamples);
			}
			$totalPages_ppssamples = ceil($totalRows_ppssamples/$maxRows_ppssamples)-1;
			
			$queryString_ppssamples = "";
			if (!empty($_SERVER['QUERY_STRING'])) {
			  $params = explode("&", $_SERVER['QUERY_STRING']);
			  $newParams = array();
			  foreach ($params as $param) {
				if (stristr($param, "pageNum_ppssamples") == false && 
					stristr($param, "totalRows_ppssamples") == false) {
				  array_push($newParams, $param);
				}
			  }
			  if (count($newParams) != 0) {
				$queryString_ppssamples = "&" . htmlentities(implode("&", $newParams));
			  }
			}
			$queryString_ppssamples = sprintf("&totalRows_ppssamples=%d%s", $totalRows_ppssamples, $queryString_ppssamples);
			 ?>
			
DAM-it &gt;&gt; <a href="?browse=damits&amp;damit=<?php echo $row_getDamit['id']; ?>" title="Browse DAM-it server"><?php echo $row_getDamit['descr']; ?></a> &gt;&gt; Device &gt;&gt; <a href="?browse=damits&amp;damit=<?php echo $row_getDamit['id']; ?>&amp;device=<?php echo $_GET['device']; ?>" title="Browse device"><?php echo $row_getDevice['name']; ?></a> &gt;&gt; Interface &gt;&gt; <a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>&amp;device=<?php echo $_GET['device']; ?>&amp;interface=<?php echo $row_getInterface['ifindex']; ?>" title="Browse device interface"><?php echo $row_getInterface['ifDescr']; ?></a> &gt;&gt; PPS Sample Time &gt;&gt; <strong><?php echo get_day($_GET['day']); ?> (<?php echo $_GET['hour']; ?>hr)</strong>
			
            <br /><br />
            
            <a href="#_samples">PPS Samples</a>
            
    <h3 id="_samples">PPS Samples    </h3>
    <table border="0">
      <tr>
        <td><?php if ($pageNum_ppssamples > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_ppssamples=%d%s", $currentPage, 0, $queryString_ppssamples); ?>"><img src="images/First.gif" border="0" /></a>
        <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_ppssamples > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_ppssamples=%d%s", $currentPage, max(0, $pageNum_ppssamples - 1), $queryString_ppssamples); ?>"><img src="images/Previous.gif" border="0" /></a>
        <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_ppssamples < $totalPages_ppssamples) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_ppssamples=%d%s", $currentPage, min($totalPages_ppssamples, $pageNum_ppssamples + 1), $queryString_ppssamples); ?>"><img src="images/Next.gif" border="0" /></a>
        <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_ppssamples < $totalPages_ppssamples) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_ppssamples=%d%s", $currentPage, $totalPages_ppssamples, $queryString_ppssamples); ?>"><img src="images/Last.gif" border="0" /></a>
        <?php } // Show if not last page ?></td>
      </tr>
    </table>
    </p>
<table width="50%" border="0">
      <tr>
        <td><strong>Timestamp</strong></td>
        <td><strong>PPS In</strong></td>
        <td><strong>PPS Out</strong></td>
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
          <td><?php echo $row_ppssamples['TS']; ?></td>
          <td><?php echo $row_ppssamples['ppsin']; ?></td>
          <td><?php echo $row_ppssamples['ppsout']; ?></td>
      </tr>
        <?php } while ($row_ppssamples = mysql_fetch_assoc($ppssamples)); ?>
    </table>
<?php }
		
		elseif ($_POST['action'] == "add_alertgroup") {
			
			if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit);  ?>
            
            DAM-it &gt;&gt; <strong><?php echo $row_getDamit['descr']; ?></strong><br />
			<br />
            <form action="<?php echo $editFormAction; ?>" method="post" name="frm_add_alertgroup" id="frm_add_alertgroup" onsubmit="MM_validateForm('groupname','','R');return document.MM_returnValue">
              <table>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Group Name:</td>
                  <td><input name="groupname" type="text" class="input_standard" id="groupname" value="" size="32" maxlength="255" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Alert Level:</td>
                  <td><select name="level" class="input_standard" id="level" />
                  		<option value="1"><?php echo get_stage(1); ?></option>
                        <option value="2"><?php echo get_stage(2); ?></option>
                        <option value="3"><?php echo get_stage(3); ?></option>
                        <option value="4"><?php echo get_stage(4); ?></option>
                        </select>
                      </td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Add alert group" /></td>
                </tr>
              </table>
              <input type="hidden" value="<?php echo $_GET['damit']; ?>" name="damit" />
              <input type="hidden" name="MM_insert" value="frm_add_alertgroup" />
    </form>
            <p>&nbsp;</p>
<?php
		}
		
		elseif ($_POST['action'] == "add_email") {
			
			if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit);  
			
			mysql_select_db($database_damit, $damit);
			$query_alertgroups = "SELECT * FROM emailgroup ORDER BY emailgroup.name";
			$alertgroups = mysql_query($query_alertgroups, $damit) or die(mysql_error());
			$row_alertgroups = mysql_fetch_assoc($alertgroups);
			$totalRows_alertgroups = mysql_num_rows($alertgroups);
			?>
            
            DAM-it &gt;&gt; <strong><?php echo $row_getDamit['descr']; ?></strong><br />
			<br />
            <form action="<?php echo $editFormAction; ?>" method="post" name="form9" id="form9" onsubmit="MM_validateForm('email','','RisEmail');return document.MM_returnValue">
              <table>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Email Address:</td>
                  <td><input name="email" type="text" class="input_standard" id="email" value="" size="32" maxlength="255" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Alert Group:</td>
                  <td><select name="alertgroup" class="input_standard" id="alertgroup" />
                  		<?php do { ?>
                        	<option value="<?php echo $row_alertgroups['id']; ?>"><?php echo $row_alertgroups['name']; ?></option>
                        <?php } while ($row_alertgroups = mysql_fetch_assoc($alertgroups)); ?>
                        </select>
                  </td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Add email address" /></td>
                </tr>
              </table>
              <input type="hidden" value="<?php echo $_GET['damit']; ?>" name="damit" />
              <input type="hidden" name="MM_insert" value="form9" />
    </form>
            <p>&nbsp;</p>
<?php
		}
		
		elseif ($_POST['action'] == "add_bgp") {
			
			if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <p>
      <?php 
					exit();
				}
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit);  ?>
      
      DAM-it &gt;&gt; <strong><?php echo $row_getDamit['descr']; ?></strong><br />
    </p>
    <p><span class="text_red"><strong>Warning: DAM-it will attempt to inject routes on all BGP speakers via telnet whenever mitigation is deemed necessary.</strong></span><br />
    </p>
    <form action="<?php echo $editFormAction; ?>" method="post" name="form10" id="form10" onsubmit="MM_validateForm('address','','R');return document.MM_returnValue">
<table>
                <tr valign="baseline">
                  <td align="right" valign="middle" nowrap="nowrap">IP Address:</td>
                  <td valign="middle"><input name="address" type="text" class="input_standard" id="address" value="" size="32" maxlength="40" />
                  *required</td>
        </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="middle">Description:</td>
                  <td valign="middle"><textarea name="descr" cols="50" rows="5" class="input_standard"></textarea>
                  *required</td>
        </tr>
                <tr valign="baseline">
                  <td align="right" valign="middle" nowrap="nowrap">Username:</td>
                  <td valign="middle"><input name="username" type="text" class="input_standard" value="" size="20" maxlength="255" /></td>
        </tr>
                <tr valign="baseline">
                  <td align="right" valign="middle" nowrap="nowrap">Passwd:</td>
                  <td valign="middle"><input name="passwd" type="password" class="input_standard" value="" size="20" /></td>
        </tr>
                <tr valign="baseline">
                  <td align="right" valign="middle" nowrap="nowrap">Enable Password:</td>
                  <td valign="middle"><input name="enable" type="password" class="input_standard" value="" size="20" /></td>
        </tr>
                <tr valign="baseline">
                  <td align="right" valign="middle" nowrap="nowrap">Router Type:</td>
                  <td valign="middle"><select name="type" class="input_standard">
                    <option value="Cisco" <?php if (!(strcmp("Cisco", ""))) {echo "SELECTED";} ?>>Cisco</option>
                  </select></td>
        </tr>
                <tr valign="baseline">
                  <td align="right" valign="middle" nowrap="nowrap">IP Next Hop:</td>
                  <td valign="middle"><input name="nexthop" type="text" class="input_standard" value="" size="32" maxlength="40" /></td>
        </tr>
                <tr valign="baseline">
                  <td align="right" valign="middle" nowrap="nowrap">Route Tags:</td>
                  <td valign="middle"><input name="tags" type="text" class="input_standard" value="" size="32" maxlength="255" /></td>
        </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Add BGP speaker" /></td>
                </tr>
      </table>
      		<input type="hidden" value="<?php echo $_GET['damit']; ?>" name="damit" />
              <input type="hidden" name="MM_insert" value="form10" />
    </form>
            <p>&nbsp;</p>
<?php
		}
		
		elseif ($_POST['action'] == "add_prefix") {
			
			if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit);  ?>
            
            DAM-it &gt;&gt; <strong><?php echo $row_getDamit['descr']; ?></strong><br />
			<br />
<form action="<?php echo $editFormAction; ?>" method="post" name="form8" id="form8" onsubmit="MM_validateForm('prefix','','R','mask','','NinRange8:32');return document.MM_returnValue">
              <table>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Prefix:</td>
                  <td><input name="prefix" type="text" class="input_standard" id="prefix" value="" size="32" maxlength="40" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Mask:</td>
                  <td>/
                    <input name="mask" type="text" class="input_standard" id="mask" value="" size="5" maxlength="3" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Description:</td>
                  <td><textarea name="descr" cols="50" rows="5" class="input_standard"></textarea></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Add prefix exception" /></td>
                </tr>
              </table>
              <input type="hidden" value="<?php echo $_GET['damit']; ?>" name="damit" />
              <input type="hidden" name="MM_insert" value="form8" />
    </form>
            <p>&nbsp;</p>
<?php 
		}
		
		elseif ($_POST['action'] == "add_as") {
			
			if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit);  ?>
			
		DAM-it &gt;&gt; <strong><?php echo $row_getDamit['descr']; ?></strong><br />
			<br />
<form action="<?php echo $editFormAction; ?>" method="post" name="form7" id="form7" onsubmit="MM_validateForm('asnumber','','RinRange0:65535');return document.MM_returnValue">
              <table>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">AS Number:</td>
                  <td><input name="asnumber" type="text" class="input_standard" id="asnumber" value="" size="10" maxlength="5" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Add Autonomous System" /></td>
                </tr>
              </table>
              <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
              <input type="hidden" name="MM_insert" value="form7" />
    </form>
            <p>&nbsp;</p>
<?php 
        }
		
		elseif ($_POST['action'] == "edit_device") {
			
			if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit); 
			
			mysql_select_db($database_damit, $damit);
			$query_getDevice = "SELECT * FROM device WHERE device.id = '".$_GET['device']."'";
			$getDevice = mysql_query($query_getDevice, $damit) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			?>
			
			DAM-it &gt;&gt; <a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>" title="Browse this DAM-it server"><?php echo $row_getDamit['descr']; ?></a> &gt;&gt; Device &gt;&gt; <strong><?php echo $row_getDevice['name']; ?></strong> <br />
			<br />
<form action="<?php echo $editFormAction; ?>" method="post" name="form6" id="form6" onsubmit="MM_validateForm('address','','R','devicename','','R','netflowname','','R','community','','R','basepps','','RinRange500:10000000','tolerance','','RinRange10:999');return document.MM_returnValue">
              <table>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Device Name:</td>
                  <td><input name="devicename" type="text" class="input_standard" id="devicename" value="<?php echo htmlentities($row_getDevice['name'], ENT_COMPAT, 'utf-8'); ?>" size="32" maxlength="255" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">NFDump Name:</td>
                  <td><input name="netflowname" type="text" class="input_standard" id="netflowname" value="<?php echo $row_getDevice['netflowname']; ?>" size="32" maxlength="255" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">IP Address:</td>
                  <td><input name="address" type="text" class="input_standard" id="address" value="<?php echo htmlentities($row_getDevice['address'], ENT_COMPAT, 'utf-8'); ?>" size="20" maxlength="15" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">SNMP Community:</td>
                  <td><input name="community" type="text" class="input_standard" id="community" value="<?php echo htmlentities($row_getDevice['community'], ENT_COMPAT, 'utf-8'); ?>" size="32" maxlength="255" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Base PPS Threshold</td>
                  <td><input name="basepps" type="text" class="input_standard" id="basepps" value="<?php echo $row_getDevice['basepps']; ?>" size="20" maxlength="11" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">PPS Tolerance:</td>
                  <td><input name="tolerance" type="text" class="input_standard" id="tolerance" value="<?php echo $row_getDevice['avgtolerance']; ?>" size="10" maxlength="3" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Update device" /></td>
                </tr>
              </table>
              <input type="hidden" name="MM_update" value="form6" />
              <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
        	  <input type="hidden" name="device" value="<?php echo $_GET['device']; ?>" />
              <input type="hidden" name="id" value="<?php echo $row_getDevice['id']; ?>" />
    </form>
            <p>&nbsp;</p>
<?php
		}
		
		elseif ($_POST['action'] == "delete_device") {
			
			if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 20 || ($pageLevel > 20 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit); 
			
			mysql_select_db($database_damit, $damit);
			$query_getDevice = "SELECT * FROM device WHERE device.id = '".$_GET['device']."'";
			$getDevice = mysql_query($query_getDevice, $damit) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			?>
			
			DAM-it &gt;&gt; <a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>" title="Browse this DAM-it server"><?php echo $row_getDamit['descr']; ?></a> &gt;&gt; Device &gt;&gt; <strong><?php echo $row_getDevice['name']; ?></strong> <br />
			<br />
			
        	<p>Are you sure you want to delete this device?
   	<ul>
                	<li>All historic PPS data will be deleted.</li>
                    <li>Any incidents for any interfaces on this device will be deleted, including related Netflow data.</li>
    </ul>
    </p>
        <form action="" method="post" target="_self">
        <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
        <input type="hidden" name="device" value="<?php echo $_GET['device']; ?>" />
        	<input type="submit" value="Confirm" name="confirm" class="input_standard" />
        </form>
        
        <?php	
		}
		
		elseif ($_POST['action'] == "add_device") {
			
			if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit); ?>
			
			DAM-it &gt;&gt; <a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>" title="Browse this DAM-it server"><?php echo $row_getDamit['descr']; ?></a><br />
			<br />
<form action="<?php echo $editFormAction; ?>" method="post" name="form5" id="form5" onsubmit="MM_validateForm('address','','R','devicename','','R','netflowname','','R','community','','R','basepps','','RinRange500:10000000','tolerance','','RinRange1:999');return document.MM_returnValue">
              <table>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Device Name:</td>
                  <td><input name="devicename" type="text" class="input_standard" id="devicename" size="32" maxlength="255" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">NFDump Name:</td>
                  <td><input name="netflowname" type="text" class="input_standard" id="netflowname" size="32" maxlength="255" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">IP Address:</td>
                  <td><input name="address" type="text" class="input_standard" id="address" value="" size="20" maxlength="15" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">SNMP Community:</td>
                  <td><input name="community" type="text" class="input_standard" id="community" value="" size="32" maxlength="255" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Base PPS Threshold</td>
                  <td><input name="basepps" type="text" class="input_standard" id="basepps" value="" size="20" maxlength="11" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">PPS Tolerance:</td>
                  <td><input name="tolerance" type="text" class="input_standard" id="tolerance" size="10" maxlength="3" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Add device" /></td>
                </tr>
              </table>
              <input type="hidden" name="MM_insert" value="form5" />
              <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
    </form>
            <p>&nbsp;</p>
<?php 
        }
		
		elseif ($_POST['action'] == "edit_system") {
			
			if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit); 
			
			mysql_select_db($database_damit, $damit);
			$query_getSystem = "SELECT * FROM `system`";
			$getSystem = mysql_query($query_getSystem, $damit) or die(mysql_error());
			$row_getSystem = mysql_fetch_assoc($getSystem);
			$totalRows_getSystem = mysql_num_rows($getSystem);
			
			?>
            
    DAM-it &gt;&gt; <a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>" title="Browse this DAM-it server"><?php echo $row_getDamit['descr']; ?></a><br />
    
    <p class="text_red"><strong>WARNING: These settings affect the ability of DAM-it to react to, and correctly identify potential threats.  Change them with caution.</strong> <br />
      *
    These settings will only take effect when the dampoller process is restarted.</p>
    
    <form action="<?php echo $editFormAction; ?>" method="post" name="form3" id="form3" onsubmit="MM_validateForm('timer','','RinRange10:300','profiling','','RinRange7:90','ppsthreshold','','RinRange100:99999','mexpiry','','RinRange1:999');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Timer:</td>
          <td><input name="timer" type="text" class="input_standard" id="timer" value="<?php echo htmlentities($row_getSystem['timer'], ENT_COMPAT, 'utf-8'); ?>" size="5" maxlength="3" /> 
          seconds*</td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Profiling Duration:</td>
          <td><input name="profiling" type="text" class="input_standard" id="profiling" value="<?php echo htmlentities($row_getSystem['profiling'], ENT_COMPAT, 'utf-8'); ?>" size="3" maxlength="2" /> 
          days*</td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Per-Flow PPS Threshold:</td>
          <td><input name="ppsthreshold" type="text" class="input_standard" id="ppsthreshold" value="<?php echo htmlentities($row_getSystem['ppsthreshold'], ENT_COMPAT, 'utf-8'); ?>" size="10" maxlength="5" /> 
          PPS</td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Mitigation Expiry:</td>
          <td><input name="mexpiry" type="text" class="input_standard" id="mexpiry" value="<?php echo htmlentities($row_getSystem['mexpiry']); ?>" size="5" maxlength="3" />
hours*</td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Sender Email Address:</td>
          <td><input name="email" type="text" class="input_standard" id="email" value="<?php echo htmlentities($row_getSystem['emailfrom'], ENT_COMPAT, 'utf-8'); ?>" size="32" maxlength="255" />
            *</td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Update system properties" /> <?php if ($_POST['MM_update'] == "form3") { ?>
          <span class="text_red">Settings updated</span>            <?php } ?></td>
        </tr>
      </table>
      <input type="hidden" name="MM_update" value="form3" />
      <input type="hidden" name="action" value="edit_system" />
      <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
      <input type="hidden" name="id" value="<?php echo $row_getSystem['id']; ?>" />
    </form>
    <br /><br />
    <form action="<?php echo $editFormAction; ?>" method="post" name="form4" id="form4">
      <input type="submit" class="input_standard" value="Restart service" onclick="if (confirm('Are you sure you want to restart the service?  Any changes made to core system settings will take effect upon restarting.') { return true; } else { return false; }" /><?php if ($row_getSystem['restart'] == 1) { ?>
      <span class="text_red">Service is restarting...</span>
      <?php } ?>
      <input type="hidden" name="restart" value="1" />
      <input type="hidden" name="MM_update" value="form4" />
      <input type="hidden" name="action" value="edit_system" />
      <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
      <input type="hidden" name="id" value="<?php echo $row_getSystem['id']; ?>" />
    </form>
    <br />
    <form method="post" name="refresh_frm" id="refresh_frm" action="<?php echo $editFormAction; ?>">
      <input type="submit" name="refresh" value="Refresh" class="input_standard" />
      <input type="hidden" name="action" value="edit_system" />
    </form>
    <p>&nbsp;</p>
<p>&nbsp;</p>
<?php	
		}
		
		elseif ($_POST['action'] == "edit_interface") {
			
			if (!(getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == ""))) { ?>
        
        <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
                
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit); 
			
			mysql_select_db($database_damit, $damit);
			$query_getDevice = "SELECT * FROM device WHERE device.id = '".$_GET['device']."'";
			$getDevice = mysql_query($query_getDevice, $damit) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_damit, $damit);
			$query_getInterface = "SELECT * FROM interfaces WHERE interfaces.device = '".$_GET['device']."' AND interfaces.ifindex = '".$_GET['interface']."'";
			$getInterface = mysql_query($query_getInterface, $damit) or die(mysql_error());
			$row_getInterface = mysql_fetch_assoc($getInterface);
			$totalRows_getInterface = mysql_num_rows($getInterface); ?>
		
        DAM-it >> <a href="?browse=damits&amp;damit=<?php echo $row_getDamit['id']; ?>" title="Browse DAM-it server"><?php echo $row_getDamit['descr']; ?></a> &gt;&gt; Device &gt;&gt; <a href="?browse=damits&amp;damit=<?php echo $row_getDamit['id']; ?>&amp;device=<?php echo $_GET['device']; ?>" title="Browse device"><?php echo $row_getDevice['name']; ?></a> &gt;&gt; Interface &gt;&gt; <strong><?php echo $row_getInterface['ifDescr']; ?></strong> <br />
        
        <br />
<form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2">
          <table>
            <tr valign="baseline">
              <td align="right" valign="middle" nowrap="nowrap"><strong>Monitor this interface?</strong></td>
              <td valign="middle"><input type="checkbox" name="monitored" value="1"  <?php if (!(strcmp(htmlentities($row_getInterface['monitored'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> /></td>
            </tr>
            <tr valign="baseline">
              <td colspan="2" nowrap="nowrap">&nbsp;</td>
            </tr>
            <tr valign="baseline">
              <td colspan="2" nowrap="nowrap"><input type="submit" class="input_standard" value="Update interface" /></td>
            </tr>
          </table>
          <input type="hidden" name="MM_update" value="form2" />
          <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
          <input type="hidden" name="device" value="<?php echo $_GET['device']; ?>" />
          <input type="hidden" name="interface" value="<?php echo $_GET['interface']; ?>" />
          <input type="hidden" name="id" value="<?php echo $row_getInterface['id']; ?>" />
    </form>
        <p>&nbsp;</p>
<?php	
		}
		elseif ($_GET['damit'] != "" && $_GET['device'] != "" && $_GET['interface'] != "") {
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit); 
			
			mysql_select_db($database_damit, $damit);
			$query_getDevice = "SELECT * FROM device WHERE device.id = '".$_GET['device']."'";
			$getDevice = mysql_query($query_getDevice, $damit) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_damit, $damit);
			$query_getInterface = "SELECT * FROM interfaces WHERE interfaces.device = '".$_GET['device']."' AND interfaces.ifindex = '".$_GET['interface']."'";
			$getInterface = mysql_query($query_getInterface, $damit) or die(mysql_error());
			$row_getInterface = mysql_fetch_assoc($getInterface);
			$totalRows_getInterface = mysql_num_rows($getInterface);
			
			$maxRows_dayAverages = 25;
			$pageNum_dayAverages = 0;
			if (isset($_GET['pageNum_dayAverages'])) {
			  $pageNum_dayAverages = $_GET['pageNum_dayAverages'];
			}
			$startRow_dayAverages = $pageNum_dayAverages * $maxRows_dayAverages;
			
			mysql_select_db($database_damit, $damit);
			$query_dayAverages = "SELECT * FROM dayaverage WHERE dayaverage.device = '".$_GET['device']."'  AND dayaverage.ifindex = '".$_GET['interface']."'";
			$query_limit_dayAverages = sprintf("%s LIMIT %d, %d", $query_dayAverages, $startRow_dayAverages, $maxRows_dayAverages);
			$dayAverages = mysql_query($query_limit_dayAverages, $damit) or die(mysql_error());
			$row_dayAverages = mysql_fetch_assoc($dayAverages);
			
			if (isset($_GET['totalRows_dayAverages'])) {
			  $totalRows_dayAverages = $_GET['totalRows_dayAverages'];
			} else {
			  $all_dayAverages = mysql_query($query_dayAverages);
			  $totalRows_dayAverages = mysql_num_rows($all_dayAverages);
			}
			$totalPages_dayAverages = ceil($totalRows_dayAverages/$maxRows_dayAverages)-1;
			
			$queryString_dayAverages = "";
			if (!empty($_SERVER['QUERY_STRING'])) {
			  $params = explode("&", $_SERVER['QUERY_STRING']);
			  $newParams = array();
			  foreach ($params as $param) {
				if (stristr($param, "pageNum_dayAverages") == false && 
					stristr($param, "totalRows_dayAverages") == false) {
				  array_push($newParams, $param);
				}
			  }
			  if (count($newParams) != 0) {
				$queryString_dayAverages = "&" . htmlentities(implode("&", $newParams));
			  }
			}
			$queryString_dayAverages = sprintf("&totalRows_dayAverages=%d%s", $totalRows_dayAverages, $queryString_dayAverages);

			?>
DAM-it >> <a href="?browse=damits&amp;damit=<?php echo $row_getDamit['id']; ?>" title="Browse DAM-it server"><?php echo $row_getDamit['descr']; ?></a> &gt;&gt; Device &gt;&gt; <a href="?browse=damits&amp;damit=<?php echo $row_getDamit['id']; ?>&amp;device=<?php echo $_GET['device']; ?>" title="Browse device"><?php echo $row_getDevice['name']; ?></a> &gt;&gt; Interface &gt;&gt; <strong><?php echo $row_getInterface['ifDescr']; ?></strong> <br />
<br />
<a href="#_averages">Hourly Averages</a>
<h3 id="_averages">Hourly Averages</h3>

<?php if ($row_getInterface['monitored'] == 1) { ?>

<form action="" method="post" name="frm_reprofile" target="_self">
        
        	<input type="hidden" name="reprofile" value="1" />
            <input type="hidden" name="device" value="<?php echo $row_getInterface['device']; ?>" />
            <input type="hidden" name="ifindex" value="<?php echo $row_getInterface['ifindex']; ?>" />
            <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
            <input type="submit" value="Re-profile interface" class="input_standard" />
        
    </form>
        <br />
        
<?php } ?>

<table border="0">
  <tr>
    <td><?php if ($pageNum_dayAverages > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_dayAverages=%d%s", $currentPage, 0, $queryString_dayAverages); ?>"><img src="images/First.gif" border="0" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_dayAverages > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_dayAverages=%d%s", $currentPage, max(0, $pageNum_dayAverages - 1), $queryString_dayAverages); ?>"><img src="images/Previous.gif" border="0" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_dayAverages < $totalPages_dayAverages) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_dayAverages=%d%s", $currentPage, min($totalPages_dayAverages, $pageNum_dayAverages + 1), $queryString_dayAverages); ?>"><img src="images/Next.gif" border="0" /></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_dayAverages < $totalPages_dayAverages) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_dayAverages=%d%s", $currentPage, $totalPages_dayAverages, $queryString_dayAverages); ?>"><img src="images/Last.gif" border="0" /></a>
        <?php } // Show if not last page ?></td>
    </tr>
</table>
</p>
<table width="50%" border="0">
              <tr>
                <td><strong>Day</strong></td>
                <td><strong>Hour</strong></td>
                <td><strong>PPS In</strong></td>
                <td><strong>PPS Out</strong></td>
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
                  <td><?php echo get_day($row_dayAverages['dayofweek']); ?></td>
                  <td><a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>&amp;device=<?php echo $_GET['device']; ?>&amp;interface=<?php echo $_GET['interface']; ?>&amp;day=<?php echo $row_dayAverages['dayofweek']; ?>&amp;hour=<?php echo $row_dayAverages['hourofday']; ?>" title="Browse PPS samples"><?php echo $row_dayAverages['hourofday']; ?></a></td>
                  <td><?php echo $row_dayAverages['ppsin']; ?></td>
                  <td><?php echo $row_dayAverages['ppsout']; ?></td>
      </tr>
<?php } while ($row_dayAverages = mysql_fetch_assoc($dayAverages)); ?>
    </table>
<?php }
		elseif ($_GET['damit'] != "" && $_GET['device'] != "") {
		
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit); 
			
			mysql_select_db($database_damit, $damit);
			$query_getDevice = "SELECT * FROM device WHERE device.id = '".$_GET['device']."'";
			$getDevice = mysql_query($query_getDevice, $damit) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			$maxRows_interfaces = 25;
			$pageNum_interfaces = 0;
			if (isset($_GET['pageNum_interfaces'])) {
			  $pageNum_interfaces = $_GET['pageNum_interfaces'];
			}
			$startRow_interfaces = $pageNum_interfaces * $maxRows_interfaces;
			
			mysql_select_db($database_damit, $damit);
			$query_interfaces = "SELECT * FROM interfaces WHERE interfaces.device = '".$_GET['device']."'";
			$query_limit_interfaces = sprintf("%s LIMIT %d, %d", $query_interfaces, $startRow_interfaces, $maxRows_interfaces);
			$interfaces = mysql_query($query_limit_interfaces, $damit) or die(mysql_error());
			$row_interfaces = mysql_fetch_assoc($interfaces);
			
			if (isset($_GET['totalRows_interfaces'])) {
			  $totalRows_interfaces = $_GET['totalRows_interfaces'];
			} else {
			  $all_interfaces = mysql_query($query_interfaces);
			  $totalRows_interfaces = mysql_num_rows($all_interfaces);
			}
			$totalPages_interfaces = ceil($totalRows_interfaces/$maxRows_interfaces)-1;
			
			$queryString_interfaces = "";
			if (!empty($_SERVER['QUERY_STRING'])) {
			  $params = explode("&", $_SERVER['QUERY_STRING']);
			  $newParams = array();
			  foreach ($params as $param) {
				if (stristr($param, "pageNum_interfaces") == false && 
					stristr($param, "totalRows_interfaces") == false) {
				  array_push($newParams, $param);
				}
			  }
			  if (count($newParams) != 0) {
				$queryString_interfaces = "&" . htmlentities(implode("&", $newParams));
			  }
			}
			$queryString_interfaces = sprintf("&totalRows_interfaces=%d%s", $totalRows_interfaces, $queryString_interfaces);

			?>
            
            DAM-it >> <a href="?browse=damits&amp;damit=<?php echo $row_getDamit['id']; ?>" title="Browse DAM-it server"><?php echo $row_getDamit['descr']; ?></a> &gt;&gt; Device &gt;&gt; <strong><?php echo $row_getDevice['name']; ?></strong>
			
            <br /><br />
            
            <a href="#_interfaces">Interfaces</a>
            
    <h3 id="_interfaces">Interfaces    </h3>
    <table border="0">
      <tr>
        <td><?php if ($pageNum_interfaces > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_interfaces=%d%s", $currentPage, 0, $queryString_interfaces); ?>"><img src="images/First.gif" border="0" /></a>
        <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_interfaces > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_interfaces=%d%s", $currentPage, max(0, $pageNum_interfaces - 1), $queryString_interfaces); ?>"><img src="images/Previous.gif" border="0" /></a>
        <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_interfaces < $totalPages_interfaces) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_interfaces=%d%s", $currentPage, min($totalPages_interfaces, $pageNum_interfaces + 1), $queryString_interfaces); ?>"><img src="images/Next.gif" border="0" /></a>
        <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_interfaces < $totalPages_interfaces) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_interfaces=%d%s", $currentPage, $totalPages_interfaces, $queryString_interfaces); ?>"><img src="images/Last.gif" border="0" /></a>
        <?php } // Show if not last page ?></td>
      </tr>
    </table>
    </p>
<table width="50%" border="0">
      <tr>
        <td><strong>Index</strong></td>
        <td><strong>Description</strong></td>
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
          <td><?php echo $row_interfaces['ifindex']; ?></td>
          <td><a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>&amp;device=<?php echo $_GET['device']; ?>&amp;interface=<?php echo $row_interfaces['ifindex']; ?>" title="Browse interface data"><?php if ($row_interfaces['monitored'] == 1) { ?><font color="red"><strong><?php echo $row_interfaces['ifDescr']; ?></strong> (Monitored)</font><?php } else { ?><?php echo $row_interfaces['ifDescr']; ?><?php } ?></a></td>
      </tr>
        <?php } while ($row_interfaces = mysql_fetch_assoc($interfaces)); ?>
    </table>
    <?php }
		elseif ($_GET['damit'] != "") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getDamit = "SELECT * FROM damit WHERE id = ".$_GET['damit']."";
			$getDamit = mysql_query($query_getDamit, $subman) or die(mysql_error());
			$row_getDamit = mysql_fetch_assoc($getDamit);
			$totalRows_getDamit = mysql_num_rows($getDamit);
	
			$hostname_damit = $row_getDamit['host'];
			$database_damit = $row_getDamit['dbname'];
			$username_damit = $row_getDamit['username'];
			$password_damit = $row_getDamit['pwd'];
			$damit = mysql_connect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
			mysql_select_db($database_damit, $damit);

			mysql_select_db($database_damit, $damit);
			$query_devices = "SELECT *, FROM_UNIXTIME(device.polldate) AS TS FROM device ORDER BY device.name";
			$devices = mysql_query($query_devices, $damit) or die(mysql_error());
			$row_devices = mysql_fetch_assoc($devices);
			$totalRows_devices = mysql_num_rows($devices);

			mysql_select_db($database_damit, $damit);
			$query_getSystem = "SELECT system.*, from_unixtime(lastrun) as lastruntime FROM `system`";
			$getSystem = mysql_query($query_getSystem, $damit) or die(mysql_error());
			$row_getSystem = mysql_fetch_assoc($getSystem);
			$totalRows_getSystem = mysql_num_rows($getSystem);

			$maxRows_log = 5;
			$pageNum_log = 0;
			if (isset($_GET['pageNum_log'])) {
			  $pageNum_log = $_GET['pageNum_log'];
			}
			$startRow_log = $pageNum_log * $maxRows_log;
			
			mysql_select_db($database_damit, $damit);
			
			if ($_SESSION['search'] != "" && $_SESSION['incidenttype'] != "" && $_SESSION['incidenttype'] != "all") {
				
				$query_log = "SELECT log.*, incident.stage, FROM_UNIXTIME(log.ts) AS TS, device.name AS devicename, device.id as deviceid FROM log LEFT JOIN device ON device.id = log.device LEFT JOIN incident ON incident.id = log.incident WHERE log.incident = '".$_SESSION['search']."' AND incident.stage = ".$_SESSION['incidenttype']." ORDER BY log.id DESC";
				
			}
			elseif ($_SESSION['search'] != "") {
				
				$query_log = "SELECT log.*, incident.stage, FROM_UNIXTIME(log.ts) AS TS, device.name AS devicename, device.id as deviceid FROM log LEFT JOIN device ON device.id = log.device LEFT JOIN incident ON incident.id = log.incident WHERE log.incident = '".$_SESSION['search']."' ORDER BY log.id DESC";
				
			}
			elseif ($_SESSION['incidenttype'] != "" && $_SESSION['incidenttype'] != "all") {
				
				$query_log = "SELECT log.*, incident.stage, FROM_UNIXTIME(log.ts) AS TS, device.name AS devicename, device.id as deviceid FROM log LEFT JOIN device ON device.id = log.device LEFT JOIN incident ON incident.id = log.incident WHERE incident.stage = ".$_SESSION['incidenttype']." ORDER BY log.id DESC";
				
			}
			else {
	
				$query_log = "SELECT log.*, incident.stage, FROM_UNIXTIME(log.ts) AS TS, device.name AS devicename, device.id as deviceid FROM log LEFT JOIN device ON device.id = log.device LEFT JOIN incident ON incident.id = log.incident ORDER BY log.id DESC";
				
			}
			$query_limit_log = sprintf("%s LIMIT %d, %d", $query_log, $startRow_log, $maxRows_log);
			$log = mysql_query($query_limit_log, $damit) or die(mysql_error());
			$row_log = mysql_fetch_assoc($log);
			
			if (isset($_GET['totalRows_log'])) {
			  $totalRows_log = $_GET['totalRows_log'];
			} else {
			  $all_log = mysql_query($query_log);
			  $totalRows_log = mysql_num_rows($all_log);
			}
			$totalPages_log = ceil($totalRows_log/$maxRows_log)-1;

			$maxRows_incidents = 5;
			$pageNum_incidents = 0;
			if (isset($_GET['pageNum_incidents'])) {
			  $pageNum_incidents = $_GET['pageNum_incidents'];
			}
			$startRow_incidents = $pageNum_incidents * $maxRows_incidents;
			
			mysql_select_db($database_damit, $damit);
			if ($_SESSION['search'] != "" && $_SESSION['incidenttype'] != "" && $_SESSION['incidenttype'] != "all") {
				
				$query_incidents = "SELECT incident.*, from_unixtime(ts) as TS, device.name as devicename, device.id as deviceid, interfaces.ifdescr FROM incident LEFT JOIN device ON device.id = incident.device LEFT JOIN interfaces ON interfaces.ifindex = incident.ifindex AND interfaces.device = device.id WHERE incident.id = '".$_SESSION['search']."' AND incident.stage = ".$_SESSION['incidenttype']." ORDER BY incident.ts DESC";
				
			}
			elseif ($_SESSION['search'] != "") {
				
				$query_incidents = "SELECT incident.*, from_unixtime(ts) as TS, device.name as devicename, device.id as deviceid, interfaces.ifdescr FROM incident LEFT JOIN device ON device.id = incident.device LEFT JOIN interfaces ON interfaces.ifindex = incident.ifindex AND interfaces.device = device.id WHERE incident.id = '".$_SESSION['search']."' ORDER BY incident.ts DESC";
				
			}
			elseif ($_SESSION['incidenttype'] != "" && $_SESSION['incidenttype'] != "all") {
				
				$query_incidents = "SELECT incident.*, from_unixtime(ts) as TS, device.name as devicename, device.id as deviceid, interfaces.ifdescr FROM incident LEFT JOIN device ON device.id = incident.device LEFT JOIN interfaces ON interfaces.ifindex = incident.ifindex AND interfaces.device = device.id WHERE incident.stage = ".$_SESSION['incidenttype']." ORDER BY incident.ts DESC";	
				
			}
			else {
			
				$query_incidents = "SELECT incident.*, from_unixtime(ts) as TS, device.name as devicename, device.id as deviceid, interfaces.ifdescr FROM incident LEFT JOIN device ON device.id = incident.device LEFT JOIN interfaces ON interfaces.ifindex = incident.ifindex AND interfaces.device = device.id ORDER BY incident.ts DESC";
				
			}
			$query_limit_incidents = sprintf("%s LIMIT %d, %d", $query_incidents, $startRow_incidents, $maxRows_incidents);
			$incidents = mysql_query($query_limit_incidents, $damit) or die(mysql_error());
			$row_incidents = mysql_fetch_assoc($incidents);
			
			if (isset($_GET['totalRows_incidents'])) {
			  $totalRows_incidents = $_GET['totalRows_incidents'];
			} else {
			  $all_incidents = mysql_query($query_incidents);
			  $totalRows_incidents = mysql_num_rows($all_incidents);
			}
			$totalPages_incidents = ceil($totalRows_incidents/$maxRows_incidents)-1;

			mysql_select_db($database_damit, $damit);
$query_asses = "SELECT * FROM asses ORDER BY asses.asnumber";
$asses = mysql_query($query_asses, $damit) or die(mysql_error());
$row_asses = mysql_fetch_assoc($asses);
$totalRows_asses = mysql_num_rows($asses);

mysql_select_db($database_damit, $damit);
$query_prefixes = "SELECT * FROM prefixexception ORDER BY prefixexception.`prefix`, prefixexception.mask";
$prefixes = mysql_query($query_prefixes, $damit) or die(mysql_error());
$row_prefixes = mysql_fetch_assoc($prefixes);
$totalRows_prefixes = mysql_num_rows($prefixes);

mysql_select_db($database_damit, $damit);
$query_alertgroups = "SELECT * FROM emailgroup ORDER BY emailgroup.name";
$alertgroups = mysql_query($query_alertgroups, $damit) or die(mysql_error());
$row_alertgroups = mysql_fetch_assoc($alertgroups);
$totalRows_alertgroups = mysql_num_rows($alertgroups);

mysql_select_db($database_damit, $damit);
$query_emails = "SELECT email.*, emailgroup.name AS groupname FROM email LEFT JOIN emailgroup ON emailgroup.id = email.emailgroup ORDER BY email.email";
$emails = mysql_query($query_emails, $damit) or die(mysql_error());
$row_emails = mysql_fetch_assoc($emails);
$totalRows_emails = mysql_num_rows($emails);

mysql_select_db($database_damit, $damit);
$query_bgp = "SELECT * FROM bgp ORDER BY bgp.address";
$bgp = mysql_query($query_bgp, $damit) or die(mysql_error());
$row_bgp = mysql_fetch_assoc($bgp);
$totalRows_bgp = mysql_num_rows($bgp);
						
						$queryString_log = "";
						if (!empty($_SERVER['QUERY_STRING'])) {
						  $params = explode("&", $_SERVER['QUERY_STRING']);
						  $newParams = array();
						  foreach ($params as $param) {
							if (stristr($param, "pageNum_log") == false && 
								stristr($param, "totalRows_log") == false) {
							  array_push($newParams, $param);
							}
						  }
						  if (count($newParams) != 0) {
							$queryString_log = "&" . htmlentities(implode("&", $newParams));
						  }
						}
						$queryString_log = sprintf("&totalRows_log=%d%s", $totalRows_log, $queryString_log);
			
			$queryString_incidents = "";
			if (!empty($_SERVER['QUERY_STRING'])) {
			  $params = explode("&", $_SERVER['QUERY_STRING']);
			  $newParams = array();
			  foreach ($params as $param) {
				if (stristr($param, "pageNum_incidents") == false && 
					stristr($param, "totalRows_incidents") == false) {
				  array_push($newParams, $param);
				}
			  }
			  if (count($newParams) != 0) {
				$queryString_incidents = "&" . htmlentities(implode("&", $newParams));
			  }
			}
			$queryString_incidents = sprintf("&totalRows_incidents=%d%s", $totalRows_incidents, $queryString_incidents);
			?>
    
	DAM-it &gt;&gt; <strong><?php echo $row_getDamit['descr']; ?></strong>
			
            <br /><br />
            
            <a href="#_properties">Properties</a><br />
            <a href="#_devices">Devices</a><br />
            <a href="#_incidents">Incidents</a><br />
            <a href="#_log">Log</a><br />
            <a href="#_asses">Local Autonomous Systems</a><br />
            <a href="#_prefixes">Exempt Prefixes</a><br />
            <a href="#_bgp">BGP Speakers</a><br />
            <a href="#_emailgroups">Alert Groups</a><br />
            <a href="#_email">Email Recipients</a><br />
            
    <h3 id="_properties">Properties</h3>
    
    <table width="100%" border="0">
      <tr>
        <td><strong>Timer</strong></td>
        <td><strong>Profiling Duration</strong></td>
        <td><strong>Per-Flow PPS Threshold</strong></td>
        <td><strong>Mitigation Expiry</strong></td>
        <td><strong>Last Run Time</strong></td>
        <td><strong>Sender Email Address</strong></td>
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
          <td><?php echo $row_getSystem['timer']; ?> seconds</td>
          <td><?php echo $row_getSystem['profiling']; ?> days</td>
          <td><?php echo $row_getSystem['ppsthreshold']; ?> PPS</td>
          <td><?php echo $row_getSystem['mexpiry']; ?> hours</td>
          <td><?php echo $row_getSystem['lastruntime']; ?></td>
        <td><?php echo $row_getSystem['emailfrom']; ?></td>
      </tr>
        <?php } while ($row_getSystem = mysql_fetch_assoc($getSystem)); ?>
    </table>
    
    <h3 id="_devices">Devices</h3>
    <?php if ($totalRows_devices > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0">
    <tr>
      <td><strong>Device Name</strong></td>
      <td><strong>IP Address</strong></td>
      <td><strong>NFDump Name</strong></td>
      <td><strong>SNMP Community</strong></td>
      <td><strong>Profiling Start Time</strong></td>
      <td><strong>PPS Tolerance</strong></td>
      <td><strong>Base PPS Margin</strong></td>
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
      <td><a href="?browse=damits&amp;damit=<?php echo $row_getDamit['id']; ?>&amp;device=<?php echo $row_devices['id']; ?>" title="Browse device"><?php echo $row_devices['name']; ?></a></td>
      <td><?php echo $row_devices['address']; ?></td>
      <td><?php echo $row_devices['netflowname']; ?></td>
      <td><?php echo $row_devices['community']; ?></td>
      <td><?php echo $row_devices['TS']; ?></td>
      <td><?php echo $row_devices['avgtolerance']; ?>%</td>
      <td><?php echo $row_devices['basepps']; ?></td>
    </tr>
    <?php } while ($row_devices = mysql_fetch_assoc($devices)); ?>
  </table>
<?php } // Show if recordset not empty 
  	else { ?>
		<p>There are no devices to display.</p>
    <?php } ?>
    
<h3 id="_incidents">Incidents</h3>

		<form action="" method="get" name="incidentSearchFrm" target="_self" id="incidentSearchFrm">
      <div class="searchBoxLeft"><img src="images/semi-left.gif" alt="" /></div>
       <input name="search" type="text" class="searchFormField" value="<?php echo $_SESSION['search']; ?>" />
       <div class="searchBoxCancel">
         <a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>&amp;submit=_cancel_search" title="Cancel search"><img src="images/cancel.gif" alt="Cancel search" border="0" /></a>
       </div>
      <div class="searchBoxRight"><img src="images/semi-right.gif" alt="" width="10" height="20" /></div>
       	 &nbsp;
       	   <input type="image" src="images/search.gif" alt="Search" name="submit" value="_search"/>&nbsp;&nbsp;&nbsp;<em>You can search by incident number only.</em>
       	 <p class="accounting_search"><strong>Incident Type</strong><br />
       	   <br />
       	   <input type="hidden" name="browse" value="<?php echo $_GET['browse']; ?>" />
       	   <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
       	   <input name="type" type="radio" onchange="MM_callJS('document.incidentSearchFrm.submit();')" value="all"   <?php if (!(strcmp($_SESSION['incidenttype'],"all"))) {echo "checked=\"checked\"";} ?><?php if ($_SESSION['incidenttype'] == "") {echo "checked=\"checked\"";} ?> />All&nbsp;
           <input   <?php if (!(strcmp($_SESSION['incidenttype'],"2"))) {echo "checked=\"checked\"";} ?> type="radio" onchange="MM_callJS('document.incidentSearchFrm.submit();')" name="type" value="2" />Netflow Data Collection&nbsp;
           <input   <?php if (!(strcmp($_SESSION['incidenttype'],"4"))) {echo "checked=\"checked\"";} ?> type="radio" onchange="MM_callJS('document.incidentSearchFrm.submit();')" name="type" value="4" />Attack Mitigation&nbsp;
           <input   <?php if (!(strcmp($_SESSION['incidenttype'],"5"))) {echo "checked=\"checked\"";} ?> type="radio" onchange="MM_callJS('document.incidentSearchFrm.submit();')" name="type" value="5" />Closed&nbsp;
           <input   <?php if (!(strcmp($_SESSION['incidenttype'],"6"))) {echo "checked=\"checked\"";} ?> type="radio" onchange="MM_callJS('document.incidentSearchFrm.submit();')" name="type" value="6" />Closed with Exceptions&nbsp;
           <input   <?php if (!(strcmp($_SESSION['incidenttype'],"7"))) {echo "checked=\"checked\"";} ?> type="radio" onchange="MM_callJS('document.incidentSearchFrm.submit();')" name="type" value="7" />Attack Closed&nbsp;
         </p>
         
</form>

    
        <?php if ($totalRows_incidents > 0) { // Show if recordset not empty ?>
        
    <p>Showing incidents <?php echo ($startRow_incidents + 1) ?> to <?php echo min($startRow_incidents + $maxRows_incidents, $totalRows_incidents) ?> of <?php echo $totalRows_incidents ?></p>
    <table border="0">
      <tr>
        <td><?php if ($pageNum_incidents > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_incidents=%d%s", $currentPage, 0, $queryString_incidents); ?>"><img src="images/First.gif" border="0" /></a>
          <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_incidents > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_incidents=%d%s", $currentPage, max(0, $pageNum_incidents - 1), $queryString_incidents); ?>"><img src="images/Previous.gif" border="0" /></a>
          <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_incidents < $totalPages_incidents) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_incidents=%d%s", $currentPage, min($totalPages_incidents, $pageNum_incidents + 1), $queryString_incidents); ?>"><img src="images/Next.gif" border="0" /></a>
          <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_incidents < $totalPages_incidents) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_incidents=%d%s", $currentPage, $totalPages_incidents, $queryString_incidents); ?>"><img src="images/Last.gif" border="0" /></a>
          <?php } // Show if not last page ?></td>
      </tr>
    </table>
    </p>

  <table width="100%" border="0">
    <tr>
      <td width="40">&nbsp;</td>
      <td><strong>Incident #</strong></td>
      <td><strong>Device</strong></td>
      <td><strong>Interface</strong></td>
      <td><strong>Timestamp</strong></td>
      <td><strong>PPS Rate</strong></td>      
      <td><strong>Stage</strong></td>
    </tr>
    <?php
  	$count = 0;
	do {
								switch($row_incidents['stage']) {
									case 1: $colour = "orange";
									break;
									case 2: $colour = "orange";
									break;
									case 3: $colour = "orange";
									break;
									case 4: $colour = "red";
									break;
									case 5: $colour = "green";
									break;
									case 6: $colour = "orange";
									break;
									case 7: $colour = "green";
									break;
									
								}
								
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td bgcolor="<?php echo $colour; ?>"><img src="images/trbox.gif" /></td>
      <td bgcolor="<?php echo $bgcolour; ?>"><a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>&amp;incident=<?php echo $row_incidents['id']; ?>" title="View this incident">#<?php echo $row_incidents['id']; ?></a></td>
      <td><a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>&amp;device=<?php echo $row_incidents['deviceid']; ?>" title="Browse device"><?php echo $row_incidents['devicename']; ?></a></td>
      <td><?php echo $row_incidents['ifdescr']; ?></td>      
      <td><?php echo $row_incidents['TS']; ?></td>
      <td><?php echo $row_incidents['pps']; ?></td>      
      <td><strong><?php echo get_stage($row_incidents['stage']); ?></strong></td>
    </tr>
    <?php } while ($row_incidents = mysql_fetch_assoc($incidents)); ?>
  </table>
<?php } // Show if recordset not empty 
  	else { ?>
    	<p>There are no incidents to display.</p>
    <?php } ?>
    
<h3 id="_log">Log</h3>
    
    <?php if ($totalRows_log > 0) { // Show if recordset not empty ?>
<p>Showing records <?php echo ($startRow_log + 1) ?> to <?php echo min($startRow_log + $maxRows_log, $totalRows_log) ?> of <?php echo $totalRows_log ?></p>
<table border="0">
        <tr>
          <td><?php if ($pageNum_log > 0) { // Show if not first page ?>
              <a href="<?php printf("%s?pageNum_log=%d%s", $currentPage, 0, $queryString_log); ?>"><img src="images/First.gif" border="0" /></a>
          <?php } // Show if not first page ?></td>
          <td><?php if ($pageNum_log > 0) { // Show if not first page ?>
              <a href="<?php printf("%s?pageNum_log=%d%s", $currentPage, max(0, $pageNum_log - 1), $queryString_log); ?>"><img src="images/Previous.gif" border="0" /></a>
          <?php } // Show if not first page ?></td>
          <td><?php if ($pageNum_log < $totalPages_log) { // Show if not last page ?>
              <a href="<?php printf("%s?pageNum_log=%d%s", $currentPage, min($totalPages_log, $pageNum_log + 1), $queryString_log); ?>"><img src="images/Next.gif" border="0" /></a>
          <?php } // Show if not last page ?></td>
          <td><?php if ($pageNum_log < $totalPages_log) { // Show if not last page ?>
              <a href="<?php printf("%s?pageNum_log=%d%s", $currentPage, $totalPages_log, $queryString_log); ?>"><img src="images/Last.gif" border="0" /></a>
          <?php } // Show if not last page ?></td>
        </tr>
    </table>
<table width="100%" border="0">
  <tr>
      <td width="40">&nbsp;</td>
      <td><strong>Device</strong></td>
      <td><strong>Timestamp</strong></td>
      <td><strong>Message</strong></td>
      <td><strong>Incident #</strong></td>
    </tr>
    <?php
  	$count = 0;
							do { 
							
								switch($row_log['msgtype']) {
									case 1: $colour = "orange";
									break;
									case 2: $colour = "red";
									break;
									case 3: $colour = "green";
									break;
								}
								
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
    	<td bgcolor="<?php echo $colour; ?>"><img src="images/trbox.gif" /></td>
        <td><a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>&amp;device=<?php echo $row_log['deviceid']; ?>" title="Browse device"><?php echo $row_log['devicename']; ?></a></td>
        <td><?php echo $row_log['TS']; ?></td>
        <td><strong><?php echo $row_log['message']; ?></strong></td>
        <td><a href="?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>&amp;incident=<?php echo $row_log['incident']; ?>" title="View this incident">#<?php echo $row_log['incident']; ?></a></td>
      </tr>
      <?php } while ($row_log = mysql_fetch_assoc($log)); ?>
</table>
<?php } // Show if recordset not empty 
  	else { ?>
    	<p>There are no log messages to display.</p>
    <?php } ?>
    
    <h3 id="_asses">Local Autonomous Systems    </h3>
    <?php if ($totalRows_asses > 0) { // Show if recordset not empty ?>
    
    <form action="" method="post" target="_self" name="frm_delete_asses" id="frm_delete_asses">
        <input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_asses; ?>">
        <input type="hidden" name="permission_action_type" value="delete_asses">
        
  <table border="0" width="50%">
    <tr>
    	<td width="40"><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_asses'), this.checked);" /></td>
      <td><strong>AS Number</strong></td>
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
    <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_asses['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_asses['id']; ?>"></td>
      <td><?php echo $row_asses['asnumber']; ?></td>
    </tr>
    <?php } while ($row_asses = mysql_fetch_assoc($asses)); ?>
  </table>
  <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
  </form>
  
  <?php } // Show if recordset not empty 
  	else { ?>
    	
    <p>There are no local Autonomous Systems to display.  Note: having no local AS numbers will open the Netflow query to all destinations.</p>
    <?php } ?>
    
    <h3 id="_prefixes">Exempt Prefixes    </h3>
    
     
        <?php if ($totalRows_prefixes > 0) { // Show if recordset not empty ?>
        
        <form action="" method="post" target="_self" name="frm_delete_prefixes" id="frm_delete_prefixes">
        <input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_prefixes; ?>">
        <input type="hidden" name="permission_action_type" value="delete_prefixes">
        
  <table width="50%" border="0">
    <tr>
      <td width="40"><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_prefixes'), this.checked);" /></td>
      <td><strong>Prefix</strong></td>
      <td><strong>Mask</strong></td>
      <td><strong>Description</strong></td>
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
      <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_prefixes['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_prefixes['id']; ?>"></td>
      <td><?php echo $row_prefixes['prefix']; ?></td>
      <td>/<?php echo $row_prefixes['mask']; ?></td>
      <td><?php echo $row_prefixes['descr']; ?></td>
    </tr>
    <?php } while ($row_prefixes = mysql_fetch_assoc($prefixes)); ?>
  </table>
  
  <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
  </form>
  <?php } // Show if recordset not empty 
  	else { ?>
   	<p>There are no exempt prefixes to display.</p>
   <?php } ?>
	
    <h3 id="_bgp">BGP Speakers</h3>
    <?php if ($totalRows_bgp > 0) { // Show if recordset not empty ?>
    
    <form action="" method="post" target="_self" name="frm_delete_bgps" id="frm_delete_bgps">
        <input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_bgp; ?>">
        <input type="hidden" name="permission_action_type" value="delete_bgps">
        
  <table width="100%" border="0">
    <tr>
    	<td width="40"><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_bgps'), this.checked);" /></td>
      <td><strong>IP Address</strong></td>
      <td><strong>Description</strong></td>
      <td><strong>Username</strong></td>
      <td><strong>Password</strong></td>
      <td><strong>Enable Password</strong></td>
      <td><strong>Router Type</strong></td>
      <td><strong>IP Next Hop</strong></td>
      <td><strong>Route Tags</strong></td>
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
    	<td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_bgp['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_bgp['id']; ?>"></td>
      <td><?php echo $row_bgp['address']; ?></td>
      <td><?php echo $row_bgp['descr']; ?></td>
      <td><?php echo $row_bgp['username']; ?></td>
      <td>*****</td>
      <td>*****</td>
      <td><?php echo $row_bgp['type']; ?></td>
      <td><?php echo $row_bgp['nexthop']; ?></td>
      <td><?php echo $row_bgp['tags']; ?></td>
    </tr>
    <?php } while ($row_bgp = mysql_fetch_assoc($bgp)); ?>
  </table>
  
  <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
  </form>
  
  <?php } // Show if recordset not empty 
  	else { ?>
    	<p>There are no BGP speakers to display.</p>
    <?php } ?>

<h3 id="_emailgroups">Alert Groups </h3>

<?php if ($totalRows_alertgroups > 0) { // Show if recordset not empty ?>
    
     <form action="" method="post" target="_self" name="frm_delete_alertgroups" id="frm_delete_alertgroups">
        <input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_alertgroups; ?>">
        <input type="hidden" name="permission_action_type" value="delete_alertgroups">
        
  <table width="50%" border="0">
    <tr>
    	<td width="40"><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_alertgroups'), this.checked);" /></td>
      <td><strong>Group Name</strong></td>
      <td><strong>Alert Level</strong></td>
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
    	<td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_alertgroups['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_alertgroups['id']; ?>"></td>
      <td><?php echo $row_alertgroups['name']; ?></td>
      <td><?php echo get_stage($row_alertgroups['alertlevel']); ?></td>
    </tr>
    <?php } while ($row_alertgroups = mysql_fetch_assoc($alertgroups)); ?>
  </table>
  
   <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
  </form>
  
  <?php } // Show if recordset not empty 
  	else { ?>
    	<p>There are no alert groups to display.</p>
  <?php } ?>

<h3 id="_email">Email Recipients    </h3>
    <?php if ($totalRows_emails > 0) { // Show if recordset not empty ?>
    
     <form action="" method="post" target="_self" name="frm_delete_emails" id="frm_delete_emails">
        <input type="hidden" name="totalRows_permissions" value="<?php echo $totalRows_emails; ?>">
        <input type="hidden" name="permission_action_type" value="delete_emails">
        
  <table width="50%" border="0">
    <tr>
    	<td width="40"><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('frm_delete_emails'), this.checked);" /></td>
      <td><strong>Email</strong></td>
      <td><strong>Alert Group</strong></td>
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
    	<td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_emails['id']; ?>" value="1" /><input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_emails['id']; ?>"></td>
      <td><?php echo $row_emails['email']; ?></td>
      <td><?php echo $row_emails['groupname']; ?></td>
    </tr>
    <?php } while ($row_emails = mysql_fetch_assoc($emails)); ?>
  </table>
  
   <input type="hidden" name="damit" value="<?php echo $_GET['damit']; ?>" />
  </form>
  
  <?php } // Show if recordset not empty 
  	else { ?>
    	<p>There are no email recipients to display.</p>
  <?php } ?>
  
<?php } ?>

<?php } ?>

  </div>

</div>
<div id="containerView_footerLeft">



</div>

  <?php if ($_GET['print'] != 1) { ?>
  
<div id="containerView_footerRight">
  
    
  	<?php if ($_GET['browse'] == "damits") { ?>
  
<form action="damitMgmtView.php?browse=damits&amp;damit=<?php echo $_GET['damit']; ?>&amp;device=<?php echo $_GET['device']; ?>&amp;interface=<?php echo $_GET['interface']; ?>" method="post" name="frm_damit_action" target="_self" id="frm_damit_action" onsubmit="if (this.action.value == 'delete_asses') { document.frm_delete_asses.submit(); return false; }; if (this.action.value == 'delete_prefixes') { document.frm_delete_prefixes.submit(); return false; }; if (this.action.value == 'delete_alertgroups') { document.frm_delete_alertgroups.submit(); return false; }; if (this.action.value == 'delete_emails') { document.frm_delete_emails.submit(); return false; }; if (this.action.value == 'delete_bgps') { document.frm_delete_bgps.submit(); return false; }">
        	<select name="action" id="action" class="input_standard">
            	<?php if ($pageLevel > 10) { ?>
                <option value="add_damit">Add a DAM-it server</option>
                <?php } ?>
            	<?php if (getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == "")) { ?>
                <?php if ($_GET['damit'] != "") { ?>
                <option value="add_device">Add a device</option>
                <option value="add_as">Add a local Autonomous System</option>
                <option value="add_prefix">Add an exempt prefix</option>
                <option value="add_bgp">Add a BGP speaker</option>
                <option value="add_alertgroup">Add an alert group</option>
              <option value="add_email">Add an email recipient</option>
          <?php if (getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 20 || ($pageLevel > 20 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == "")) { ?>
                <option value="delete_asses">Delete local Autonomous System(s)</option>
                <option value="delete_prefixes">Delete exempt prefix(es)</option>
                <option value="delete_bgps">Delete BGP speaker(s)</option>
              	<option value="delete_alertgroups">Delete alert group(s)</option>  
              <option value="delete_emails">Delete email recipient(s)</option>
              <?php } ?>
          <?php if ($_GET['device'] != "") { ?>
                <option value="edit_device">Edit device</option>
                <?php if (getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) > 10 || ($pageLevel > 10 && getDamitLevel($_GET['damit'],$_SESSION['MM_Username']) == "")) { ?>
                <option value="delete_device">Delete device</option>
                <?php } ?>
                <?php if ($_GET['interface'] != "") { ?>
                <option value="edit_interface">Edit interface</option>
                <?php } ?>
                <?php } ?>
  <option value="edit_system">Edit system properties</option>
                <?php } ?>
                <?php } ?>
            </select>
            <input type="submit" value="Go" class="input_standard" id="submit" name="submit" />
    </form>
    <?php } ?>
  </div>

<?php } ?>

</div>

</div>

<?php include('includes/ipm_footer.php'); ?>
</body>
</html>
<?php
mysql_free_result($devices);

mysql_free_result($getSystem);

mysql_free_result($log);

mysql_free_result($incidents);

mysql_free_result($asses);

mysql_free_result($prefixes);

mysql_free_result($emails);

mysql_free_result($bgp);

mysql_free_result($incidentlog);

mysql_free_result($netflowanalysis);

mysql_free_result($netflowdata);

mysql_free_result($ppssamples);

mysql_free_result($getInterface);

mysql_free_result($dayAverages);

mysql_free_result($dayaverages);

mysql_free_result($interfaces);

mysql_free_result($damits);

mysql_free_result($getDevice);
?>
