<?php require_once('../Connections/subman.php'); ?>
<?php require_once('Net/IPv4.php'); ?>
<?php require_once('Net/IPv6.php'); ?>
<?php include('../standard_functions.php'); ?>
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

$containerLevel = getContainerLevel($_GET['container'],$_SESSION['MM_Username']);

if ($containerLevel < 10) { ?>
<p class="text_red">Error: You are not authorised to view the selected content.</p>
<?php 
	exit();
}

if ($_GET['search'] != "") {

if ($_SESSION['searchrows'] == "" && ($_GET['limit'] == "undefined" || $_GET['limit'] == "")) {
	$_SESSION['searchrows'] = 5;
}
elseif ($_GET['limit'] == "undefined" || $_GET['limit'] == "") {

}
else {
	$_SESSION['searchrows'] = $_GET['limit'];
}

$_GET['search'] = addslashes($_GET['search']);

	
// Select all matching internal networks (subnets only)
		mysql_select_db($database_subman, $subman);
		$query_networks = "SELECT networks.* FROM networks WHERE (networks.short LIKE '%".$_GET['search']."%' OR networks.descr LIKE '%".$_GET['search']."%') AND networks.container = ".$_GET['container']." ORDER BY networks.network ASC, networks.mask ASC, networks.`variable` DESC LIMIT 50";
		$networks = mysql_query($query_networks, $subman) or die(mysql_error());
		$row_networks = mysql_fetch_assoc($networks);
		$totalRows_networks = mysql_num_rows($networks);
		
?>

<p><strong>Click the network to select it.</strong></p>
<?php
if ($totalRows_networks > 0) { // Show if recordset not empty ?>

              <?php
				do { 
					
					mysql_select_db($database_subman, $subman);
					$query_checkNetwork = "SELECT * FROM linknets WHERE linknets.network = '".$row_networks['id']."'";
					$checkNetwork = mysql_query($query_checkNetwork, $subman) or die(mysql_error());
					$row_checkNetwork = mysql_fetch_assoc($checkNetwork);
					$totalRows_checkNetwork = mysql_num_rows($checkNetwork);
					
					if ($totalRows_checkNetwork == 0) {
						if (getNetworkLevel($row_networks['id'], $_SESSION['MM_Username']) > 10 || (getNetGroupLevel($row_networks['networkGroup'],$_SESSION['MM_Username']) > 10 && getNetworkLevel($row_networks['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getNetGroupLevel($row_networks['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_networks['id'],$_SESSION['MM_Username']) == "")) {
				?>
        	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="document.getElementById('network').value = '<?php echo $row_networks['id']; ?>'; document.getElementById('searchQ_1').style.display = 'none'; document.getElementById('selected_network').style.display = 'block'; document.getElementById('selected_network').value = '<?php if ($row_networks['v6mask'] == "") { ?><?php echo long2ip($row_networks['network']); ?><?php if ($row_networks['mask'] == "255.255.255.255") { echo "/32"; } else { echo get_slash($row_networks['mask']); } ?><?php } else { ?><?php echo Net_IPv6::compress(long2ipv6($row_networks['network'])); ?><?php echo "/".$row_networks['v6mask']; ?><?php } ?>'; document.getElementById('network_search').value = '<?php echo $row_networks['descr']; ?>'; document.getElementById('nexthop').focus();"><?php if ($row_networks['v6mask'] == "") { ?><strong><?php echo long2ip($row_networks['network']); ?><?php if ($row_networks['mask'] == "255.255.255.255") { echo "/32"; } else { echo get_slash($row_networks['mask']); } ?></strong><?php } else { ?><strong><?php echo Net_IPv6::compress(long2ipv6($row_networks['network'])); ?><?php echo "/".$row_networks['v6mask']; ?></strong><?php } ?></a> <?php echo $row_networks['descr']; ?><br>
              <?php 	}
              		} ?>
              <?php } while ($row_networks = mysql_fetch_assoc($networks)); ?>
<?php } // Show if recordset not empty 

}
?>