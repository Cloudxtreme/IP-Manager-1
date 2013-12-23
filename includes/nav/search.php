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

		
		mysql_select_db($database_subman, $subman);
		$query_links_abcd = "SELECT links.*, portsdevices.devicegroup FROM links INNER JOIN portsdevices ON portsdevices.id = links.provide_node_a INNER JOIN customer ON customer.id = links.provide_customer WHERE (customer.name LIKE '%".$_GET['search']."%' OR links.provide_cct like '%".$_GET['search']."%') AND links.container = ".$_GET['container']." ORDER BY links.provide_cct LIMIT ".$_SESSION['searchrows']."";
		$links_abcd = mysql_query($query_links_abcd, $subman) or die(mysql_error());
		$row_links_abcd = mysql_fetch_assoc($links_abcd);
		$totalRows_links_abcd = mysql_num_rows($links_abcd); 
		
// Select all matching internal networks (subnets only)
		mysql_select_db($database_subman, $subman);
		$query_networks = "SELECT networks.* FROM networks WHERE (networks.short LIKE '%".$_GET['search']."%' OR networks.descr LIKE '%".$_GET['search']."%') AND networks.container = ".$_GET['container']." ORDER BY networks.network ASC, networks.mask ASC, networks.`variable` DESC LIMIT ".$_SESSION['searchrows']."";
		$networks = mysql_query($query_networks, $subman) or die(mysql_error());
		$row_networks = mysql_fetch_assoc($networks);
		$totalRows_networks = mysql_num_rows($networks);
		
		// Select all matching address information
		mysql_select_db($database_subman, $subman);
		$query_addresses = "SELECT addresses.id as addressid, addresses.address, addresses.network, addresses.descr, networks.v6mask, networks.networkGroup, customer.name as customerName FROM addresses INNER JOIN networks ON addresses.network = networks.id LEFT JOIN customer ON customer.id = addresses.customer WHERE (addresses.short LIKE '".$_GET['search']."' OR addresses.descr LIKE '%".$_GET['search']."%' OR customer.name LIKE '%".$_GET['search']."%' OR addresses.comments  LIKE '%".$_GET['search']."%') AND networks.container = ".$_GET['container']." ORDER BY addresses.address LIMIT ".$_SESSION['searchrows']."";
		$addresses = mysql_query($query_addresses, $subman) or die(mysql_error());
		$row_addresses = mysql_fetch_assoc($addresses);
		$totalRows_addresses = mysql_num_rows($addresses);
		
		mysql_select_db($database_subman, $subman);
		$query_customers = "SELECT customer.* FROM customer WHERE (customer.name LIKE '%".$_GET['search']."%' OR customer.account LIKE '%".$_GET['search']."%') AND customer.container = ".$_GET['container']." ORDER BY customer.name ASC, customer.account ASC LIMIT ".$_SESSION['searchrows']."";
		$customers = mysql_query($query_customers, $subman) or die(mysql_error());
		$row_customers = mysql_fetch_assoc($customers);
		$totalRows_customers = mysql_num_rows($customers);
		
		mysql_select_db($database_subman, $subman);
		$query_mpls = "select provider.*, vpn.id as vpnid, vpn.name as vpnname, vpn.layer as vpnlayer, customer.name as customername, customer.id as customerID, vrf.name as vrfname, vrf.id as vrfID, xconnectid.xconnectid as xconnectid from provider left join providervpn on providervpn.provider = provider.id left join vpn on vpn.id = providervpn.vpn left join vpncustomer on vpncustomer.vpn = vpn.id left join customer on customer.id = vpncustomer.customer left join vpnvrf on vpnvrf.vpn = vpn.id left join vrf on vrf.id = vpnvrf.vrf left join vpnxconnect on vpnxconnect.vpn = vpn.id left join xconnectid on xconnectid.id = vpnxconnect.xconnect WHERE (vpn.name like '%".$_GET['search']."%' or customer.name like '%".$_GET['search']."%' or vrf.name like '%".$_GET['search']."%' or xconnectid.xconnectid like '%".$_GET['search']."%') AND provider.container = ".$_GET['container']." group by vpn.id, vrf.id, xconnectid.id LIMIT ".$_SESSION['searchrows']."";
		$mpls = mysql_query($query_mpls, $subman) or die(mysql_error());
		$row_mpls = mysql_fetch_assoc($mpls);
		$totalRows_mpls = mysql_num_rows($mpls);
		
		// Select all matching port information
		mysql_select_db($database_subman, $subman);
		$query_ports = "SELECT portsdevices.devicegroup, cards.id as cardid, cards.device, portsports.card, cards.rack, cards.module, cards.slot, portsports.port, portsports.id as portid, portsdevices.name, devicetypes.name as devicetype, devicetypes.image, customer.name as customerName, portsports.usage, cardtypes.name as cardtype FROM
														devicetypes INNER JOIN portsdevices ON devicetypes.id = portsdevices.devicetype
														INNER JOIN cards ON cards.device = portsdevices.id
														INNER JOIN cardtypes ON cardtypes.id = cards.cardtype
														INNER JOIN portsports ON portsports.card = cards.id
														INNER JOIN customer ON customer.id = portsports.customer
														INNER JOIN portgroups ON portgroups.id = portsdevices.devicegroup
														WHERE (customer.name LIKE '%".$_GET['search']."%' OR portsports.usage LIKE '%".$_GET['search']."%' OR portsports.router LIKE '%".$_GET['search']."%') AND portgroups.container = ".$_GET['container']." ORDER BY portsports.port LIMIT ".$_SESSION['searchrows']."";
		$ports = mysql_query($query_ports, $subman) or die(mysql_error());
		$row_ports = mysql_fetch_assoc($ports);
		$totalRows_ports = mysql_num_rows($ports);
		
		$query_vlansubints = "SELECT portsdevices.devicegroup, cards.id as cardid, cards.device, portsports.card, cards.rack, cards.module, cards.slot, portsports.port, portsports.id as portid, subint.subint,  portsdevices.name, devicetypes.name as devicetype, devicetypes.image, customer.name as customerName, subint.usage, cardtypes.name as cardtype FROM
														devicetypes INNER JOIN portsdevices ON devicetypes.id = portsdevices.devicetype
														INNER JOIN cards ON cards.device = portsdevices.id
														INNER JOIN cardtypes ON cardtypes.id = cards.cardtype
														INNER JOIN portsports ON portsports.card = cards.id
														INNER JOIN subint ON subint.port = portsports.id
														INNER JOIN customer ON customer.id = subint.customer
														INNER JOIN portgroups ON portgroups.id = portsdevices.devicegroup
														WHERE (customer.name LIKE '%".$_GET['search']."%' OR subint.usage LIKE '%".$_GET['search']."%' OR subint.router LIKE '%".$_GET['search']."%') AND portgroups.container = ".$_GET['container']." ORDER BY subint.subint LIMIT ".$_SESSION['searchrows']."";
		$vlansubints = mysql_query($query_vlansubints, $subman) or die(mysql_error());
		$row_vlansubints = mysql_fetch_assoc($vlansubints);
		$totalRows_vlansubints = mysql_num_rows($vlansubints);
		
		// Select all matching vlan information
		mysql_select_db($database_subman, $subman);
		$query_vlans = "select portsdevices.devicegroup, portsdevices.id as deviceid, vlanpool.id as vlanpoolid, vlanpool.name, vlanpool.poolstart, vlanpool.poolend, vlan.id as vlanid, vlan.name as vlanname, vlan.number
							from portsdevices inner join vlanpool on portsdevices.id = vlanpool.device
							inner join vlan on vlanpool.id = vlan.vlanpool
							INNER JOIN portgroups ON portgroups.id = portsdevices.devicegroup
							INNER JOIN customer ON customer.id = vlan.customer
							where (customer.name LIKE '%".$_GET['search']."%' OR vlan.number like '%".$_GET['search']."%' or vlan.name like '%".$_GET['search']."%') AND portgroups.container = ".$_GET['container']." LIMIT ".$_SESSION['searchrows']."";
		$vlans = mysql_query($query_vlans, $subman) or die(mysql_error());
		$row_vlans = mysql_fetch_assoc($vlans);
		$totalRows_vlans = mysql_num_rows($vlans);
		
		mysql_select_db($database_subman, $subman);
		$query_asses = "SELECT asses.*, customer.name as customerName FROM asses LEFT JOIN customer ON customer.id = asses.customer WHERE (asses.number LIKE '%".$_GET['search']."%' OR asses.name LIKE '%".$_GET['search']."%' OR asses.descr LIKE '%".$_GET['search']."%' OR customer.name LIKE '%".$_GET['search']."%') AND asses.container = ".$_GET['container']." ORDER BY asses.number LIMIT ".$_SESSION['searchrows']."";
		$asses = mysql_query($query_asses, $subman) or die(mysql_error());
		$row_asses = mysql_fetch_assoc($asses);
		$totalRows_asses = mysql_num_rows($asses);
		
		mysql_select_db($database_subman, $subman);
		$query_devices = "SELECT portsdevices.*, devicetypes.image FROM portsdevices LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE (portsdevices.name LIKE '%".$_GET['search']."%' OR portsdevices.descr LIKE '%".$_GET['search']."%' OR devicetypes.name LIKE '%".$_GET['search']."%' OR portgroups.name LIKE '%".$_GET['search']."%' OR portsdevices.managementip LIKE '%".$_GET['search']."%') AND portgroups.container = ".$_GET['container']." ORDER BY portsdevices.name LIMIT ".$_SESSION['searchrows']."";
		$devices = mysql_query($query_devices, $subman) or die(mysql_error());
		$row_devices = mysql_fetch_assoc($devices);
		$totalRows_devices = mysql_num_rows($devices);

$totalrecords = $totalRows_links_abcd + $totalRows_networks + $totalRows_addresses + $totalRows_mpls + $totalRows_ports + $totalRows_vlansubints + $totalRows_vlans + $totalRows_asses;
?>

<div align="center"><a href="#" onclick="searchQry(document.getElementById('search').value,<?php if ($_SESSION['searchrows'] > 5) { echo $_SESSION['searchrows']-5; } ?>); return false;"><img src="images/scroll_top.gif" width="20px" alt="Less results" align="absmiddle" border="0" title="Less results"></a> showing <?php echo $totalrecords; ?> results <a href="#" onclick="searchQry(document.getElementById('search').value,<?php if ($_SESSION['searchrows'] < 100) { echo $_SESSION['searchrows']+5; } ?>); return false;"><img src="images/scroll_bottom.gif" width="20px" alt="More results" align="absmiddle" border="0" title="More results"></a> </div>

<?php
if ($totalRows_links_abcd > 0) { // Show if recordset not empty ?>

	<p class="searchHeader"><img src="images/link.gif" alt="Links" height="20" align="absmiddle"> Links</p>
	
              <?php
				do { 
	        		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"?browse=devices&amp;container=".$_GET['container']."&amp;device=".$row_links_abcd['provide_node_a']."&amp;group=".$row_links_abcd['devicegroup']."&amp;port=".$row_links_abcd['provide_port_node_a']."&amp;linkview=1\" title=\"Display this link\"><strong>".$row_links_abcd['provide_cct']."</strong></a><br>";
	        	?>
              <?php } while ($row_links_abcd = mysql_fetch_assoc($links_abcd)); ?>
<?php } // Show if recordset not empty 

if ($totalRows_networks > 0 || $totalRows_addresses > 0) { ?>

		<br /><p class="searchHeader"><img src="images/network_icon.gif" alt="Networks and Addresses" height="20" align="absmiddle"> Networks and Addresses</p>

<?php
if ($totalRows_networks > 0) { // Show if recordset not empty ?>

              <?php
				do { 
					
					if (getNetworkLevel($row_networks['id'], $_SESSION['MM_Username']) > 0 || (getNetGroupLevel($row_networks['networkGroup'],$_SESSION['MM_Username']) > 0 && getNetworkLevel($row_networks['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getNetGroupLevel($row_networks['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_networks['id'],$_SESSION['MM_Username']) == "")) {
				?>
        	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=&amp;parent=<?php echo $row_networks['id']; ?>" title="Browse network"><?php if ($row_networks['v6mask'] == "") { ?><strong><?php echo long2ip($row_networks['network']); ?><?php if ($row_networks['mask'] == "255.255.255.255") { echo "/32"; } else { echo get_slash($row_networks['mask']); } ?></strong><?php } else { ?><strong><?php echo Net_IPv6::compress(long2ipv6($row_networks['network'])); ?><?php echo "/".$row_networks['v6mask']; ?></strong><?php } ?></a> <?php echo $row_networks['descr']; ?><br>
              <?php } ?>
              <?php } while ($row_networks = mysql_fetch_assoc($networks)); ?>
<?php } // Show if recordset not empty 

if ($totalRows_addresses > 0) { // Show if recordset not empty ?>
			
              <?php
				do { 
					
					if (getNetworkLevel($row_addresses['network'], $_SESSION['MM_Username']) > 0 || (getNetGroupLevel($row_addresses['networkGroup'],$_SESSION['MM_Username']) > 0 && getNetworkLevel($row_addresses['network'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getNetGroupLevel($row_addresses['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_addresses['network'],$_SESSION['MM_Username']) == "")) {
						
				?>
        	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=&amp;parent=<?php echo $row_addresses['network']; ?>#<?php echo $row_addresses['addressid']; ?>" title="Browse network"><?php if ($row_addresses['v6mask'] == "") { ?><strong><?php echo long2ip($row_addresses['address']); ?></strong><?php } else { ?><strong><?php echo Net_IPv6::compress(long2ipv6($row_addresses['address'])); ?><?php echo "/".$row_addresses['v6mask']; } ?></strong></a> <?php echo $row_addresses['descr']; ?><br>
                
              <?php } ?>
              <?php } while ($row_addresses = mysql_fetch_assoc($addresses)); ?>
			<?php } 
}

if ($totalRows_customers > 0) { // Show if recordset not empty ?>
			
			<br /><p class="searchHeader"><img src="images/customers_icon.gif" alt="Customers" height="20" align="absmiddle"> Customers</p>
			
              <?php
				do { 
					
				?>
        	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?browse=customers&amp;container=<?php echo $_GET['container']; ?>&amp;customer=<?php echo $row_customers['id']; ?>" title="View customer records"><strong><?php echo $row_customers['name']; ?></strong></a> <?php echo $row_customers['account']; ?><br>

              <?php } while ($row_customers = mysql_fetch_assoc($customers));
              
}

if ($totalRows_devices > 0) { // Show if recordset not empty ?>			

	<br /><p class="searchHeader"><img src="images/devices_icon.gif" alt="Devices" height="20" align="absmiddle"> Devices</p>
	
              <?php
				do { 
					
					if (getDeviceLevel($row_devices['id'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($row_devices['devicegroup'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($row_devices['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($row_devices['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_devices['id'],$_SESSION['MM_Username']) == "")) {
						
					?>
        		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/<?php echo $row_devices['image']; ?>" alt="<?php echo $row_ports['devicetype']; ?>" width="20" align="absmiddle" border="0"> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;device=<?php echo $row_devices['id']; ?>&amp;group=<?php echo $row_devices['devicegroup']; ?>" title="Browse Device"><strong><?php echo $row_devices['name']; ?></strong></a><br />
                <?php } ?>
                <?php } while ($row_devices = mysql_fetch_assoc($devices)); ?>
            </table>
		<?php } 
		
if ($totalRows_ports > 0 || $totalRows_vlansubints > 0) { // Show if recordset not empty ?>

	<br /><p class="searchHeader"><img src="images/devices_icon.gif" alt="Devices and Ports" height="20" align="absmiddle"> Device Port Usage</p>
	
              <?php 
			  	if ($totalRows_ports > 0) {
					
					do { 
						
						if (getDeviceLevel($row_ports['device'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($row_ports['devicegroup'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($row_ports['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($row_ports['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_ports['device'],$_SESSION['MM_Username']) == "")) {
						
						?>
        		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/<?php echo $row_ports['image']; ?>" alt="<?php echo $row_ports['devicetype']; ?>" width="20" align="absmiddle" border="0"> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;device=<?php echo $row_ports['device']; ?>&amp;group=<?php echo $row_ports['devicegroup']; ?>&amp;card=<?php echo $row_ports['cardid']; ?>" title="Browse card"><strong><?php echo $row_ports['name']; ?> : <?php echo $row_ports['cardtype']; ?> <?php if (!($row_ports['rack']) && !($row_ports['module']) && !($row_ports['slot'])) { echo "Virtual"; } else { if (isset($row_ports['rack'])) { echo $row_ports['rack']."/"; } if (isset($row_ports['module'])) { echo $row_ports['module'].'/'; } if (isset($row_ports['slot'])) { echo $row_ports['slot']; } } ?>/<?php echo $row_ports['port']; ?></strong></a> <?php echo $row_ports['customerName']; ?> - <?php echo $row_ports['usage'];?><br />
              <?php } ?>
              <?php  } while ($row_ports = mysql_fetch_assoc($ports)); 
			  	} ?>
			  <?php 
			  	if ($totalRows_vlansubints > 0) {
					do { 
						
						if (getDeviceLevel($row_vlansubints['device'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($row_vlansubints['devicegroup'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($row_vlansubints['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($row_vlansubints['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_vlansubints['device'],$_SESSION['MM_Username']) == "")) {
							
						?>
        		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/<?php echo $row_vlansubints['image']; ?>" alt="<?php echo $row_vlansubints['devicetype']; ?>" width="20" align="absmiddle" border="0"> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;device=<?php echo $row_vlansubints['device']; ?>&amp;group=<?php echo $row_vlansubints['devicegroup']; ?>&amp;card=<?php echo $row_vlansubints['cardid']; ?>&amp;port=<?php echo $row_vlansubints['portid']; ?>" title="Browse card"><strong><?php echo $row_vlansubints['name']; ?> : <?php echo $row_vlansubints['cardtype']; ?> <?php if (!($row_vlansubints['rack']) && !($row_vlansubints['module']) && !($row_vlansubints['slot'])) { echo "Virtual"; } else { if (isset($row_vlansubints['rack'])) { echo $row_vlansubints['rack']."/"; } if (isset($row_vlansubints['module'])) { echo $row_vlansubints['module'].'/'; } if (isset($row_vlansubints['slot'])) { echo $row_vlansubints['slot']; } } ?>/<?php echo $row_vlansubints['port']; ?>.<font color="#FF0000"><?php echo $row_vlansubints['subint']; ?></font></strong></a> <?php echo $row_vlansubints['customerName']; ?> - <?php echo $row_vlansubints['usage'];?><br />
              <?php } ?>
              <?php  } while ($row_vlansubints = mysql_fetch_assoc($vlansubints)); ?>
			  <?php	} ?>
             <?php }

if ($totalRows_vlans > 0) { // Show if recordset not empty ?>			

	<br /><p class="searchHeader"><img src="images/devices_icon.gif" alt="VLANs" height="20" align="absmiddle"> VLANs</p>
	
              <?php
				do { 
					
					if (getDeviceLevel($row_vlans['deviceid'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($row_vlans['devicegroup'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($row_vlans['deviceid'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($row_vlans['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_vlans['deviceid'],$_SESSION['MM_Username']) == "")) {
						
					?>
        		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;device=<?php echo $row_vlans['deviceid']; ?>&amp;group=<?php echo $row_vlans['devicegroup']; ?>&amp;vlanpool=<?php echo $row_vlans['vlanpoolid']; ?>&amp;vlan=<?php echo $row_vlans['vlanid']; ?>" title="Browse VLAN"><strong>VLAN [<?php echo $row_vlans['number']; ?>]</strong></a> <?php echo $row_vlans['vlanname']; ?> <?php echo $row_vlans['name']; ?> (Range: <?php echo $row_vlans['poolstart']; ?> to <?php echo $row_vlans['poolend']; ?>)<br />
                <?php } ?>
                <?php } while ($row_vlans = mysql_fetch_assoc($vlans)); ?>
            </table>
		<?php } 

if ($totalRows_mpls > 0) { // Show if recordset not empty ?>			

	<br /><p class="searchHeader"><img src="images/vpn_icon.gif" alt="VPNs" height="20" align="absmiddle"> VPNs</p>
	
              <?php 
				do { 
					
					if (getVpnLevel($row_mpls['vpnid'], $_SESSION['MM_Username']) > 0 || (getProviderLevel($row_mpls['id'],$_SESSION['MM_Username']) > 0 && getVpnLevel($row_mpls['vpnid'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getProviderLevel($row_mpls['id'],$_SESSION['MM_Username']) == "" && getVpnLevel($row_mpls['vpnid'],$_SESSION['MM_Username']) == "")) {
					
					?>
        		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $row_mpls['id']; ?>&amp;vpn=<?php echo $row_mpls['vpnid']; ?>" title="Browse VPN"><strong><?php echo $row_mpls['vpnname']; ?></strong></a> <?php echo getVPNLayer($row_mpls['vpnlayer']); ?> <?php echo $row_mpls['customername']; ?> <?php echo $row_mpls['vrfname']; ?> <?php echo $row_mpls['xconnectid']; ?><br />
                <?php } ?>
                <?php } while ($row_mpls = mysql_fetch_assoc($mpls)); ?>

		<?php }       

if ($totalRows_asses > 0) { ?>
        <br /><p class="searchHeader"><img src="images/bgp_icon.gif" alt="BGP Autonomous Systems" height="20" align="absmiddle"> BGP Autonomous Systems</p>
        
        <?php
							do { 
								?>
    			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?browse=bgp&amp;container=<?php echo $_GET['container']; ?>" title="Browse Autonomous Systems"><strong>AS [<?php echo $row_asses['number']; ?>]</strong></a> <?php echo $row_asses['customerName']; ?> <?php echo $row_asses['name']; ?><br />
            <?php } while ($row_asses = mysql_fetch_assoc($asses)); ?>
        <?php }
        
}

?>