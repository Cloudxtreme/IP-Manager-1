<?php require_once('../Connections/subman.php'); ?>
<?php require_once('Net/IPv4.php'); ?>
<?php require_once('Net/IPv6.php'); ?>
<?php include('../includes/standard_functions.php'); ?>
<?php

if (!isset($_SESSION)) {
  session_start();
}

if ($_GET['errstr'] != 1) {
	
	$_SESSION['errstr'] = "";
	
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

$query_report = "SELECT * FROM reports WHERE container = '".$_GET['container']."' AND username = '".$_SESSION['MM_Username']."' AND id = '".$_GET['report']."'";
$report = mysql_query($query_report, $subman) or die(mysql_error());
$row_report = mysql_fetch_assoc($report);
$totalRows_report = mysql_num_rows($report);

$query_reportobjects = "SELECT reportobjects.* FROM reportobjects LEFT JOIN networks ON networks.id = reportobjects.objectid WHERE report = '".$_GET['report']."' ORDER BY reportobjects.reporttype, networks.network, networks.mask, networks.v6mask";
$reportobjects = mysql_query($query_reportobjects, $subman) or die(mysql_error());
$row_reportobjects = mysql_fetch_assoc($reportobjects);
$totalRows_reportobjects = mysql_num_rows($reportobjects);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>IP Manager Report - <?php echo $row_report['title']; ?></title>
<link href="../css/ipm5.css" rel="stylesheet" type="text/css" />
</head>

<body style="background-color:#FFFFFF; background-image:none;">

<div class="ipm_body" style="border:none; box-shadow:none;">

<img src="../images/ipm_banner.gif" alt="IPM Logo" width="150" />

<h3><img src="../images/reports-icon.gif" alt="Reports icon" align="absmiddle" /> <?php echo $row_report['title']; ?></h3>

<?php if ($row_report['descr'] != "") { ?>
<p><em><?php echo $row_report['descr']; ?></em></p>
<?php } ?>

<table width="100%" border="0" cellspacing="0">
        
<?php do { ?>

        <?php if ($row_reportobjects['reporttype'] == "network") {
			
			mysql_select_db($database_subman, $subman);
			$query_getNetwork = "SELECT * FROM networks WHERE networks.id = '".$row_reportobjects['objectid']."' ORDER BY networks.network ASC, networks.masklong ASC, networks.v6mask ASC";
			$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
			$row_getNetwork = mysql_fetch_assoc($getNetwork);
			$totalRows_getNetwork = mysql_num_rows($getNetwork);
			
			if ($row_getNetwork['v6mask'] == "") {
	
				$net = find_net(long2ip($row_getNetwork['network']),$row_getNetwork['mask']);
			
				mysql_select_db($database_subman, $subman);
				$query_getGroupNetworks = "SELECT * FROM networks WHERE networks.network BETWEEN ".$net['network']." AND ".$net['broadcast']." AND maskLong > ".$row_getNetwork['maskLong']." AND container = ".$_GET['container']."";
				$getGroupNetworks = mysql_query($query_getGroupNetworks, $subman) or die(mysql_error());
				$row_getGroupNetworks = mysql_fetch_assoc($getGroupNetworks);
				$totalRows_getGroupNetworks = mysql_num_rows($getGroupNetworks);
			}
			else {
				
				mysql_select_db($database_subman, $subman);
				$query_getGroupNetworks = "SELECT * FROM networks WHERE networks.network BETWEEN ".$row_getNetwork['network']." AND ".bcadd($row_getNetwork['network'],bcpow(2,(128 - $row_getNetwork['v6mask'])))." AND v6mask > '".$row_getNetwork['v6mask']."' AND container = ".$_GET['container']."";
				$getGroupNetworks = mysql_query($query_getGroupNetworks, $subman) or die(mysql_error());
				$row_getGroupNetworks = mysql_fetch_assoc($getGroupNetworks);
				$totalRows_getGroupNetworks = mysql_num_rows($getGroupNetworks);
				
			}
			
			mysql_select_db($database_subman, $subman);
			$query_addresses = "SELECT addresses.*, customer.name as customername, customer.id as customerID, portsports.port, portsports.id as portID, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.id as devicegroupID FROM addresses LEFT JOIN customer ON customer.id = addresses.customer LEFT JOIN portsports ON (portsports.router = addresses.id) OR (portsports.id = addresses.portid) LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device  LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE addresses.network = '".$row_getNetwork['id']."' ORDER BY addresses.address ASC";
			$addresses = mysql_query($query_addresses, $subman) or die(mysql_error());
			$row_addresses = mysql_fetch_assoc($addresses);
			$totalRows_addresses = mysql_num_rows($addresses);
			
			if ($row_getNetwork['v6mask'] == "") {
						$net = array();
						$net = find_net(long2ip($row_getNetwork['network']),$row_getNetwork['mask']);
					}
					
					if ($row_getNetwork['mask'] == "255.255.255.255" || $row_getNetwork['v6mask'] == 128) {
						$remaining = 0;
					}
					else {
						if ($row_getNetwork['mask'] == "255.255.255.254") {
							$remaining = 2 - $totalRows_addresses;
						}
						elseif ($row_getNetwork['v6mask'] == "") {
			   			 	$remaining = ($net['broadcast'] - $net['network'] - 1) - $totalRows_addresses;
						}
						else {
							$remaining = bcpow(2,(128 - $row_getNetwork['v6mask']) - 1);
						}
					}
					
			?>
            
            <?php                        
			if ($row_getNetwork['v6mask'] == "") { ?>

                <tr><td class="report_objectheader" colspan="5"><strong><?php echo long2ip($row_getNetwork['network']); if ($row_getNetwork['mask'] == "255.255.255.255") { echo "/32"; } else { echo get_slash($row_getNetwork['mask']); } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_getNetwork['descr']; ?></strong>
                <br />
                <span class="report_subheader">
                <?php 
				if ($row_getNetwork['mask'] != "255.255.255.255" && $totalRows_getGroupNetworks == 0) {
					
					echo "<em>Network: ".$net['cidr']." (".$net['total']." addresses)";
					echo "<br>";
					echo "Broadcast: ".long2ip($net['broadcast']);
					echo "<br>";
					echo "Normal Host Range: ".long2ip($net['firstaddress'])." --> ".long2ip($net['lastaddress'])." (".$remaining." remaining)</em>"; 
					
				} ?>
                </span>
                </td></tr>
                
            <?php } else { ?>
            
            	<tr><td class="report_objectheader" colspan="5"><strong><?php echo Net_IPv6::Compress(long2ipv6($row_getNetwork['network']))."/".$row_getNetwork['v6mask']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_getNetwork['descr']; ?></strong></td></tr>
            
            <?php } ?>
            
            <?php if ($totalRows_addresses > 0) { ?>
            	
                <tr>
                	<td class="report_objectsubheader">Address</td>
                    <td class="report_objectsubheader">Description</td>
                    <td class="report_objectsubheader">Customer</td>
                    <td class="report_objectsubheader">Device Port</td>
                    <td class="report_objectsubheader">Comments</td>
                </tr>
            	
                <?php do { 
					
					mysql_select_db($database_subman, $subman);
					$query_address_ports = "SELECT portsports.*, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, cards.id as cardid,  portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.id as devicegroupID FROM portsports  LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portsports.router = '".$row_addresses['id']."' OR portsports.id = '".$row_addresses['portid']."'";
					$address_ports = mysql_query($query_address_ports, $subman) or die(mysql_error());
					$row_address_ports = mysql_fetch_assoc($address_ports);
					$totalRows_address_ports = mysql_num_rows($address_ports);
					
					mysql_select_db($database_subman, $subman);
					$query_address_subints = "SELECT subint.*, portsports.port, portsports.id as portID, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, cards.id as cardid, portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.id as devicegroupID FROM subint LEFT JOIN portsports ON portsports.id = subint.port LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE subint.router = '".$row_addresses['id']."' OR subint.id = '".$row_addresses['subintid']."'";
					$address_subints = mysql_query($query_address_subints, $subman) or die(mysql_error());
					$row_address_subints = mysql_fetch_assoc($address_subints);
					$totalRows_address_subints = mysql_num_rows($address_subints);
					
					?>
            	<tr>
                	<td class="report_objectrow"><?php if ($row_getNetwork['v6mask'] == "" ) { echo long2ip($row_addresses['address']); } else { echo Net_IPv6::compress(long2ipv6($row_addresses['address'])); } ?></td>
          			<td class="report_objectrow"><?php echo $row_addresses['descr']; ?></td>
          			<td class="report_objectrow"><?php echo $row_addresses['customername']?></td>
          			<td class="report_objectrow"><?php if ($totalRows_address_ports > 0 && $totalRows_address_subints == 0) { ?><?php echo $row_address_ports['devicename']; ?><br /><?php echo $row_address_ports['cardtypename']; ?> <?php if (!($row_address_ports['rack']) && !($row_address_ports['module']) && !($row_address_ports['slot'])) { echo "Virtual"; } else { if (isset($row_address_ports['rack'])) { echo $row_address_ports['rack']."/"; } if (isset($row_address_ports['module'])) { echo $row_address_ports['module'].'/'; } if (isset($row_address_ports['slot'])) { echo $row_address_ports['slot']; } } ?>/<?php echo $row_address_ports['port']; ?><?php } ?>
          		<?php if ($totalRows_address_subints > 0) { ?><?php echo $row_address_subints['devicename']; ?><br /><?php echo $row_address_subints['cardtypename']; ?> <?php if (!($row_address_subints['rack']) && !($row_address_subints['module']) && !($row_address_subints['slot'])) { echo "Virtual"; } else { if (isset($row_address_subints['rack'])) { echo $row_address_subints['rack']."/"; } if (isset($row_address_subints['module'])) { echo $row_address_subints['module'].'/'; } if (isset($row_address_subints['slot'])) { echo $row_address_subints['slot']; } } ?>/<?php echo $row_address_subints['port']; ?><font color="#FF0000">.<?php echo $row_address_subints['subint']; ?></font><?php } ?></td>
          			<td class="report_objectrow"><?php echo $row_address['comments']; ?></td>
        		</tr>
                <?php } while ($row_addresses = mysql_fetch_assoc($addresses)); ?>
            
            <?php } elseif ($totalRows_getGroupNetworks == 0) { ?>
            
            	<tr><td colspan="5"><em>No address information for this network.</em></td></tr>
                
            <?php } else { ?>
            	
                <tr><td colspan="5"><em>This network is subnetted.</em></td></tr>
                
            <?php } ?>
            
        <?php } ?>
			
		<?php if ($row_reportobjects['reporttype'] == "vlanpool") {
			
			mysql_select_db($database_subman, $subman);
			$query_getVlanPool = "SELECT * FROM vlanpool WHERE vlanpool.id = '".$row_reportobjects['objectid']."' ORDER BY vlanpool.name ASC";
			$getVlanPool = mysql_query($query_getVlanPool, $subman) or die(mysql_error());
			$row_getVlanPool = mysql_fetch_assoc($getVlanPool);
			$totalRows_getVlanPool = mysql_num_rows($getVlanPool);
			
			mysql_select_db($database_subman, $subman);
			$query_vlans = "SELECT vlan.*, customer.name as customername, customer.id as customerID FROM vlan LEFT JOIN customer ON customer.id = vlan.customer WHERE vlan.vlanpool = '".$row_getVlanPool['id']."' ORDER BY vlan.number ASC";
			$vlans = mysql_query($query_vlans, $subman) or die(mysql_error());
			$row_vlans = mysql_fetch_assoc($vlans);
			$totalRows_vlans = mysql_num_rows($vlans);
                
		?>
			<tr><td class="report_objectheader" colspan="5"><strong><?php echo $row_getVlanPool['name']; ?></strong></td></tr>
	
			<?php if ($totalRows_vlans > 0) { ?>
            	
                <tr>
                	<td class="report_objectsubheader">VLAN ID</td>
                    <td class="report_objectsubheader">Name</td>
                    <td class="report_objectsubheader">Customer</td>
                    <td class="report_objectsubheader" colspan="2">Member Ports</td>
                </tr>
                
                <?php do { 
                
                	mysql_select_db($database_subman, $subman);
					$query_vlan_ports = "SELECT portsports.*, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, cards.id as cardid,  portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.id as devicegroupID FROM portsports  LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portsports.vlanid = '".$row_vlans['id']."'";
					$vlan_ports = mysql_query($query_vlan_ports, $subman) or die(mysql_error());
					$row_vlan_ports = mysql_fetch_assoc($vlan_ports);
					$totalRows_vlan_ports = mysql_num_rows($vlan_ports);
					
					mysql_select_db($database_subman, $subman);
					$query_vlan_subints = "SELECT subint.*, portsports.port, portsports.id as portID, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, cards.id as cardid, portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.id as devicegroupID FROM subint LEFT JOIN portsports ON portsports.id = subint.port LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE subint.vlanid = '".$row_vlans['id']."'";
					$vlan_subints = mysql_query($query_vlan_subints, $subman) or die(mysql_error());
					$row_vlan_subints = mysql_fetch_assoc($vlan_subints);
					$totalRows_vlan_subints = mysql_num_rows($vlan_subints);
					
					?>
                
                	<tr>
                		<td class="report_objectrow"><?php echo $row_vlans['number']; ?></td>
                		<td class="report_objectrow"><?php echo $row_vlans['name']; ?></td>
                		<td class="report_objectrow"><?php echo $row_vlans['customername']; ?></td>
                		<td class="report_objectrow" colspan="2"><?php if ($totalRows_vlan_ports > 0 && $totalRows_vlan_subints == 0) { ?><?php do { ?><?php echo $row_vlan_ports['devicename']; ?><br /><?php echo $row_vlan_ports['cardtypename']; ?> <?php if (!($row_vlan_ports['rack']) && !($row_vlan_ports['module']) && !($row_vlan_ports['slot'])) { echo "Virtual"; } else { if (isset($row_vlan_ports['rack'])) { echo $row_vlan_ports['rack']."/"; } if (isset($row_vlan_ports['module'])) { echo $row_vlan_ports['module'].'/'; } if (isset($row_vlan_ports['slot'])) { echo $row_vlan_ports['slot']; } } ?>/<?php echo $row_vlan_ports['port']; ?><br /><?php } while ($row_vlan_ports = mysql_fetch_assoc($vlan_ports)); ?><?php } ?>
          		<?php if ($totalRows_vlan_subints > 0) { ?><?php do { ?><?php echo $row_vlan_subints['devicename']; ?><br /><?php echo $row_vlan_subints['cardtypename']; ?> <?php if (!($row_vlan_subints['rack']) && !($row_vlan_subints['module']) && !($row_vlan_subints['slot'])) { echo "Virtual"; } else { if (isset($row_vlan_subints['rack'])) { echo $row_vlan_subints['rack']."/"; } if (isset($row_vlan_subints['module'])) { echo $row_vlan_subints['module'].'/'; } if (isset($row_vlan_subints['slot'])) { echo $row_vlan_subints['slot']; } } ?>/<?php echo $row_vlan_subints['port']; ?><font color="#FF0000">.<?php echo $row_vlan_subints['subint']; ?></font><br /><?php } while ($row_vlan_subints = mysql_fetch_assoc($vlan_subints)); ?><?php } ?></td>
            		</tr>
            	
            	<?php } while ($row_vlans = mysql_fetch_assoc($vlans)); ?>
                
            <?php } ?>
                
		<?php } ?>
	
				<tr><td colspan="5">&nbsp;</td></tr>
				
	<?php } while ($row_reportobjects = mysql_fetch_assoc($reportobjects)); ?>

</table>

</div>

</body>
</html>