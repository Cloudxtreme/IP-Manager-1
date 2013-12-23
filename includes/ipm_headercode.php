<?php

if (isset($_POST['MM_insert']) && $_POST['MM_insert'] == 'new_report') {
	
	$insertSQL = sprintf("INSERT INTO reports (username, container, title) VALUES (%s, %s, %s)",
							   GetSQLValueString($_SESSION['MM_Username'], "text"),
							   GetSQLValueString($_POST['container'], "int"),
							   GetSQLValueString($_POST['report_title'], "text"));
		
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					  
		$reportID = mysql_insert_id();
	
	$insertSQL = sprintf("REPLACE INTO reportobjects (report, reporttype, objectid) VALUES (%s, %s, %s)",
							   GetSQLValueString($reportID, "int"),
							   GetSQLValueString($_POST['report_type'], "text"),
							   GetSQLValueString($_POST['report_object_id'], "int"));
		
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					  
	if ($_POST['report_type'] == "network") {
		
		mysql_select_db($database_subman, $subman);
		$query_getNetwork = "SELECT * FROM networks WHERE networks.id = '".$_POST['report_object_id']."'";
		$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
		$row_getNetwork = mysql_fetch_assoc($getNetwork);
		$totalRows_getNetwork = mysql_num_rows($getNetwork);
		
		if ($row_getNetwork['v6mask'] == "") {
	
			$net = find_net(long2ip($row_getNetwork['network']),$row_getNetwork['mask']);
		
			mysql_select_db($database_subman, $subman);
			$query_getGroupNetworks = "SELECT * FROM networks WHERE networks.network BETWEEN ".$net['network']." AND ".$net['broadcast']." AND maskLong > ".$row_getNetwork['maskLong']." AND container = ".$_POST['container']."";
			$getGroupNetworks = mysql_query($query_getGroupNetworks, $subman) or die(mysql_error());
			$row_getGroupNetworks = mysql_fetch_assoc($getGroupNetworks);
			$totalRows_getGroupNetworks = mysql_num_rows($getGroupNetworks);
		}
		else {
			
			mysql_select_db($database_subman, $subman);
			$query_getGroupNetworks = "SELECT * FROM networks WHERE networks.network BETWEEN ".$row_getNetwork['network']." AND ".bcadd($row_getNetwork['network'],bcpow(2,(128 - $row_getNetwork['v6mask'])))." AND v6mask > '".$row_getNetwork['v6mask']."' AND container = ".$_POST['container']."";
			$getGroupNetworks = mysql_query($query_getGroupNetworks, $subman) or die(mysql_error());
			$row_getGroupNetworks = mysql_fetch_assoc($getGroupNetworks);
			$totalRows_getGroupNetworks = mysql_num_rows($getGroupNetworks);
			
		}
		
		if ($totalRows_getGroupNetworks > 0) {
			do {
				
				$insertSQL = sprintf("REPLACE INTO reportobjects (report, reporttype, objectid) VALUES (%s, %s, %s)",
								   GetSQLValueString($reportID, "int"),
								   GetSQLValueString($_POST['report_type'], "text"),
								   GetSQLValueString($row_getGroupNetworks['id'], "int"));
			
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						  
			} while ($row_getGroupNetworks = mysql_fetch_assoc($getGroupNetworks));
		}
		
	}
	
	$insertGoTo = "containerView.php?browse=reports&container=".$_POST['container']."&report=".$reportID;
  
  header(sprintf("Location: %s", $insertGoTo));
  
}

if (isset($_POST['MM_insert']) && $_POST['MM_insert'] == 'add_to_report') {
		
	$insertSQL = sprintf("REPLACE INTO reportobjects (report, reporttype, objectid) VALUES (%s, %s, %s)",
							   GetSQLValueString($_POST['report_id'], "int"),
							   GetSQLValueString($_POST['report_type'], "text"),
							   GetSQLValueString($_POST['report_object_id'], "int"));
		
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
	if ($_POST['report_type'] == "network") {
		
		mysql_select_db($database_subman, $subman);
		$query_getNetwork = "SELECT * FROM networks WHERE networks.id = '".$_POST['report_object_id']."'";
		$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
		$row_getNetwork = mysql_fetch_assoc($getNetwork);
		$totalRows_getNetwork = mysql_num_rows($getNetwork);
		
		if ($row_getNetwork['v6mask'] == "") {
	
			$net = find_net(long2ip($row_getNetwork['network']),$row_getNetwork['mask']);
		
			mysql_select_db($database_subman, $subman);
			$query_getGroupNetworks = "SELECT * FROM networks WHERE networks.network BETWEEN ".$net['network']." AND ".$net['broadcast']." AND maskLong > ".$row_getNetwork['maskLong']." AND container = ".$_POST['container']."";
			$getGroupNetworks = mysql_query($query_getGroupNetworks, $subman) or die(mysql_error());
			$row_getGroupNetworks = mysql_fetch_assoc($getGroupNetworks);
			$totalRows_getGroupNetworks = mysql_num_rows($getGroupNetworks);
		}
		else {
			
			mysql_select_db($database_subman, $subman);
			$query_getGroupNetworks = "SELECT * FROM networks WHERE networks.network BETWEEN ".$row_getNetwork['network']." AND ".bcadd($row_getNetwork['network'],bcpow(2,(128 - $row_getNetwork['v6mask'])))." AND v6mask > '".$row_getNetwork['v6mask']."' AND container = ".$_POST['container']."";
			$getGroupNetworks = mysql_query($query_getGroupNetworks, $subman) or die(mysql_error());
			$row_getGroupNetworks = mysql_fetch_assoc($getGroupNetworks);
			$totalRows_getGroupNetworks = mysql_num_rows($getGroupNetworks);
			
		}
		
		if ($totalRows_getGroupNetworks > 0) {
			do {
				
				$insertSQL = sprintf("REPLACE INTO reportobjects (report, reporttype, objectid) VALUES (%s, %s, %s)",
								   GetSQLValueString($_POST['report_id'], "int"),
								   GetSQLValueString($_POST['report_type'], "text"),
								   GetSQLValueString($row_getGroupNetworks['id'], "int"));
			
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						  
			} while ($row_getGroupNetworks = mysql_fetch_assoc($getGroupNetworks));
		}

	}
	
	if ($_POST['report_type'] == "vlanpool") {
		
		mysql_select_db($database_subman, $subman);
		$query_getVlanPool = "SELECT * FROM vlanpool WHERE vlanpool.id = '".$_POST['report_object_id']."'";
		$getVlanPool = mysql_query($query_getVlanPool, $subman) or die(mysql_error());
		$row_getVlanPool = mysql_fetch_assoc($getVlanPool);
		$totalRows_getVlanPool = mysql_num_rows($getVlanPool);
		
		$insertSQL = sprintf("REPLACE INTO reportobjects (report, reporttype, objectid) VALUES (%s, %s, %s)",
								   GetSQLValueString($_POST['report_id'], "int"),
								   GetSQLValueString($_POST['report_type'], "text"),
								   GetSQLValueString($_POST['report_object_id'], "int"));
			
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
	}
	
	$insertGoTo = "containerView.php?browse=reports&container=".$_POST['container']."&report=".$_POST['report_id'];
  
  header(sprintf("Location: %s", $insertGoTo));
  
}

if (isset($_POST['MM_update']) && $_POST['MM_update'] == 'frm_report_edit') {
		
	$updateSQL = sprintf("UPDATE reports SET title=%s, descr=%s WHERE id=%s",
							   GetSQLValueString($_POST['report_title'], "text"),
							   GetSQLValueString($_POST['report_descr'], "text"),
							   GetSQLValueString($_POST['report_id'], "int"));
		
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	
	$_SESSION['errstr'] .= "The report has been updated.";
	
	$insertGoTo = "containerView.php?browse=reports&container=".$_POST['container']."&report=".$_POST['report_id'];
  
  header(sprintf("Location: %s", $insertGoTo));
  
}

if (isset($_POST['MM_delete']) && $_POST['MM_delete'] == 'frm_delete_reportobject') {
		
	$deleteSQL = sprintf("DELETE FROM reportobjects WHERE id=%s",
							   GetSQLValueString($_POST['objectid'], "int"));
		
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	$insertGoTo = "containerView.php?browse=reports&container=".$_POST['container']."&report=".$_POST['report_id'];
  
  header(sprintf("Location: %s", $insertGoTo));
  
}

if (isset($_POST['MM_delete']) && $_POST['MM_delete'] == 'frm_delete_report') {
		
	$deleteSQL = sprintf("DELETE FROM reports WHERE id=%s",
							   GetSQLValueString($_POST['report'], "int"));
		
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
					  
	$deleteSQL = sprintf("DELETE FROM reportobjects WHERE report=%s",
							   GetSQLValueString($_POST['report'], "int"));
		
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	$insertGoTo = "containerView.php?browse=reports&container=".$_POST['container'];
  
  header(sprintf("Location: %s", $insertGoTo));
  
}

if (isset($_POST['as_action_type']) && $_POST['as_action_type'] == 'delete') {
	
	if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
                
	for ($i = 0; $i < $_POST['totalRows_asses']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM asses WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['rt_action_type']) && $_POST['rt_action_type'] == 'delete') {
	
	for ($i = 0; $i < $_POST['totalRows_vpn_rts']; $i ++) {
		if ($_POST['check_'.($i+1)] == 1) {
  			$deleteSQL = sprintf("DELETE FROM rt WHERE id=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			
			$deleteSQL = sprintf("DELETE FROM rtvpn WHERE rt=%s",
                       GetSQLValueString($_POST['id_'.($i+1)], "int"));

  			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		}
	}
	
  $deleteGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['MM_delete']) && $_POST['MM_delete'] == 'frm_delete_secondary') {
	
	mysql_select_db($database_subman, $subman);
	$query_getLink = "SELECT linknetworks.*, links.provide_node_b, links.provide_node_a, links.provide_port_node_a, links.provide_port_node_b, servicetemplate.recover_secondarynets, portsdevices.name AS devicename, portsdevices.managementip, networks.network, networks.id AS networkID, INET_NTOA(networks.network) AS _network, networks.mask, networks.v6mask, vrf.name AS vrfname, servicetemplate.id AS servicetemplateid FROM linknetworks LEFT JOIN links ON links.id = linknetworks.link LEFT JOIN servicetemplate ON servicetemplate.id = links.servicetemplate LEFT JOIN portsdevices ON portsdevices.id = servicetemplate.routesnode LEFT JOIN networks ON networks.id = linknetworks.network LEFT JOIN vrf ON vrf.id = links.provide_vrf WHERE linknetworks.id = ".$_POST['linknet']."";
	$getLink = mysql_query($query_getLink, $subman) or die(mysql_error());
	$row_getLink = mysql_fetch_assoc($getLink);
	$totalRows_getLink = mysql_num_rows($getLink);
	
	mysql_select_db($database_subman, $subman);
	$query_checkLinknetwork = "SELECT * FROM linknetworks WHERE linknetworks.network = '".$row_getLink['networkID']."'";
	$checkLinknetwork = mysql_query($query_checkLinknetwork, $subman) or die(mysql_error());
	$row_checkLinknetwork = mysql_fetch_assoc($checkLinknetwork);
	$totalRows_checkLinknetwork = mysql_num_rows($checkLinknetwork);
	
	mysql_select_db($database_subman, $subman);
	if ($row_getLink['provide_port_node_b'] != "") {
		$query_checkAddresses = "SELECT * FROM addresses WHERE addresses.network = '".$row_getLink['networkID']."' AND ((addresses.portid != '".$row_getLink['provide_port_node_a']."' AND addresses.portid != '".$row_getLink['provide_port_node_b']."')  OR addresses.portid IS NULL)";
	}
	else {
		$query_checkAddresses = "SELECT * FROM addresses WHERE addresses.network = '".$row_getLink['networkID']."' AND (addresses.portid != '".$row_getLink['provide_port_node_a']."' OR addresses.portid IS NULL)";
	}
	$checkAddresses = mysql_query($query_checkAddresses, $subman) or die(mysql_error());
	$row_checkAddresses = mysql_fetch_assoc($checkAddresses);
	$totalRows_checkAddresses = mysql_num_rows($checkAddresses);
	
	mysql_select_db($database_subman, $subman);
	$query_provide_scripts = "SELECT * FROM scripts WHERE scripts.servicetemplate = '".$row_getLink['servicetemplateid']."' AND scripts.scriptrole = 'secondarynetsrecover'";
	$provide_scripts = mysql_query($query_provide_scripts, $subman) or die(mysql_error());
	$row_provide_scripts = mysql_fetch_assoc($provide_scripts);
	$totalRows_provide_scripts = mysql_num_rows($provide_scripts);
	
	if ($row_getLink['v6mask'] == "") {
		$deletesecondary_network = $row_getLink['_network'];
		$deletesecondary_mask = $row_getLink['mask'];
	}
	else {
		$deletesecondary_network = Net_IPv6::Compress(long2ipv6($row_getLink['network']));
		$deletesecondary_mask = "/".$row_getLink['v6mask'];
	}
	$deletesecondary_devicename = $row_getLink['devicename'];
	$deletesecondary_servicetemplateid = $row_getLink['servicetemplateid'];
	
	mysql_select_db($database_subman, $subman);
	$query_secondarynets = "SELECT linknetworks.*, links.container, links.provide_node_a AS deviceid, portsdevices.devicegroup, links.provide_card_node_a AS cardid, links.provide_port_node_a AS portid FROM linknetworks LEFT JOIN links ON links.id = linknetworks.link LEFT JOIN portsdevices ON portsdevices.id = links.provide_node_a WHERE linknetworks.id = '".$_POST['linknet']."'";
	$secondarynets = mysql_query($query_secondarynets, $subman) or die(mysql_error());
	$row_secondarynets = mysql_fetch_assoc($secondarynets);
	$totalRows_secondarynets = mysql_num_rows($secondarynets);
	
	
	$deleteSQL = sprintf("DELETE FROM linknetworks WHERE id=%s",
			   GetSQLValueString($_POST['linknet'], "int"));

	mysql_select_db($database_subman, $subman);
	$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	if ($totalRows_checkLinknetwork < 2 && $totalRows_checkAddresses == 0) {
	
		$deleteSQL = sprintf("DELETE FROM networks WHERE id=%s",
			   GetSQLValueString($row_getLink['networkID'], "int"));

		mysql_select_db($database_subman, $subman);
		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	}
	
	$deleteSQL = sprintf("DELETE FROM addresses WHERE network=%s AND (portid = '".$row_getLink['provide_port_node_a']."' OR portid = '".$row_getLink['provide_port_node_b']."')",
			   GetSQLValueString($row_getLink['networkID'], "int"));

	mysql_select_db($database_subman, $subman);
	$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	$linknetid = $_POST['link'];
			
		if ($row_getLink['recover_secondarynets'] == "" && $totalRows_provide_scripts == 0) {
			
			$deleteGoTo = "containerView.php?browse=devices&container=".$row_secondarynets['container']."&group=".$row_secondarynets['devicegroup']."&device=".$row_secondarynets['deviceid']."&card=".$row_secondarynets['cardid']."&port=".$row_secondarynets['portid']."&linkview=1";
  			header(sprintf("Location: %s", $deleteGoTo));
			
		}
		else {
			
			$secondarynetsconfig = $row_getLink['recover_secondarynets'];
			$secondarynetsdelete = 1;
			
		}
		
}

if (isset($_POST['MM_delete']) && $_POST['MM_delete'] == 'frm_delete_route') {
	
	mysql_select_db($database_subman, $subman);
	$query_getLink = "SELECT linknets.*, servicetemplate.recover_routes, portsdevices.name AS devicename, portsdevices.managementip, networks.network, INET_NTOA(networks.network) AS _network, networks.mask, networks.v6mask, vrf.name AS vrfname, servicetemplate.id AS servicetemplateid FROM linknets LEFT JOIN links ON links.id = linknets.link LEFT JOIN servicetemplate ON servicetemplate.id = links.servicetemplate LEFT JOIN portsdevices ON portsdevices.id = servicetemplate.routesnode LEFT JOIN networks ON networks.id = linknets.network LEFT JOIN vrf ON vrf.id = links.provide_vrf WHERE linknets.id = ".$_POST['linknet']."";
	$getLink = mysql_query($query_getLink, $subman) or die(mysql_error());
	$row_getLink = mysql_fetch_assoc($getLink);
	$totalRows_getLink = mysql_num_rows($getLink);
	
	mysql_select_db($database_subman, $subman);
	$query_provide_scripts = "SELECT * FROM scripts WHERE scripts.servicetemplate = '".$row_getLink['servicetemplateid']."' AND scripts.scriptrole = 'routesrecover'";
	$provide_scripts = mysql_query($query_provide_scripts, $subman) or die(mysql_error());
	$row_provide_scripts = mysql_fetch_assoc($provide_scripts);
	$totalRows_provide_scripts = mysql_num_rows($provide_scripts);
	
	if ($row_getLink['v6mask'] == "") {
		$deleteroutes_network = $row_getLink['_network'];
		$deleteroutes_mask = $row_getLink['mask'];
	}
	else {
		$deleteroutes_network = Net_IPv6::Compress(long2ipv6($row_getLink['network']));
		$deleteroutes_mask = "/".$row_getLink['v6mask'];
	}
	$deleteroutes_vrf = $row_getLink['vrfname'];
	$deleteroutes_nexthop = $row_getLink['nexthop'];
	$deleteroutes_devicename = $row_getLink['devicename'];
	$deleteroutes_servicetemplateid = $row_getLink['servicetemplateid'];
	$deleteroutes_router = $row_getLink['managementip'];
	
	
	mysql_select_db($database_subman, $subman);
	$query_routes = "SELECT linknets.*, links.container, links.provide_node_a AS deviceid, portsdevices.devicegroup, links.provide_card_node_a AS cardid, links.provide_port_node_a AS portid FROM linknets LEFT JOIN links ON links.id = linknets.link LEFT JOIN portsdevices ON portsdevices.id = links.provide_node_a WHERE linknets.id = '".$_POST['linknet']."'";
	$routes = mysql_query($query_routes, $subman) or die(mysql_error());
	$row_routes = mysql_fetch_assoc($routes);
	$totalRows_routes = mysql_num_rows($routes);
		
	$deleteSQL = sprintf("DELETE FROM linknets WHERE id=%s",
			   GetSQLValueString($_POST['linknet'], "int"));

	mysql_select_db($database_subman, $subman);
	$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	$linknetid = $_POST['link'];
			
		if ($row_getLink['recover_routes'] == "" && $totalRows_provide_scripts == 0) {
			
			$deleteGoTo = "containerView.php?browse=devices&container=".$row_routes['container']."&group=".$row_routes['devicegroup']."&device=".$row_routes['deviceid']."&card=".$row_routes['cardid']."&port=".$row_routes['portid']."&linkview=1";
  			header(sprintf("Location: %s", $deleteGoTo));
			
		}
		else {
			
			$routesconfig = $row_getLink['recover_routes'];
			$routesdelete = 1;
			
		}
		
}

if (isset($_POST['MM_delete']) && $_POST['MM_delete'] == 'frm_delete_servicetemplate') {

	mysql_select_db($database_subman, $subman);
	$query_checkLinks = "SELECT * FROM links WHERE links.servicetemplate = '".$_POST['id']."'";
	$checkLinks = mysql_query($query_checkLinks, $subman) or die(mysql_error());
	$row_checkLinks = mysql_fetch_assoc($checkLinks);
	$totalRows_checkLinks = mysql_num_rows($checkLinks);

	if ($totalRows_checkLinks > 0) {
		
		$_SESSION['errstr'] .= "You cannot delete the service template whilst there are still active links that were deployed using the template.  Please delete the links first.";
		$errstrflag = 1;
		
	}
	else {
		
		$deleteSQL = sprintf("DELETE FROM servicetemplate WHERE id=%s",
				   GetSQLValueString($_POST['id'], "int"));

		mysql_select_db($database_subman, $subman);
		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
		$deleteSQL = sprintf("DELETE FROM arbitraryfields WHERE servicetemplate=%s",
				   GetSQLValueString($_POST['id'], "int"));

		mysql_select_db($database_subman, $subman);
		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
		$deleteSQL = sprintf("DELETE scripts,scriptvariables FROM scripts LEFT JOIN scriptvariables ON scriptvariables.script = scripts.id where scripts.servicetemplate = %s",
				   GetSQLValueString($_POST['id'], "int"));

		mysql_select_db($database_subman, $subman);
		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
		$updateSQL = sprintf("UPDATE servicetemplate SET templatelink = NULL WHERE templatelink=%s",
				   GetSQLValueString($_POST['id'], "int"));

		mysql_select_db($database_subman, $subman);
		$Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	
	}
	
  $deleteGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsetemplates=service&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['address_action_type']) && $_POST['address_action_type'] == 'delete') {
	for ($i = 0; $i < $_POST['totalRows_addresses']; $i ++) {
		
		if ($_POST['check_'.($i+1)] == 1) {
			
			mysql_select_db($database_subman, $subman);
			$query_check_address = "SELECT * FROM portsports WHERE portsports.router = '".$_POST['id_'.($i+1)]."'";
			$check_address = mysql_query($query_check_address, $subman) or die(mysql_error());
			$row_check_address = mysql_fetch_assoc($check_address);
			$totalRows_check_address = mysql_num_rows($check_address);
			
			mysql_select_db($database_subman, $subman);
			$query_check_address2 = "SELECT * FROM subint WHERE subint.router = '".$_POST['id_'.($i+1)]."'";
			$check_address2 = mysql_query($query_check_address2, $subman) or die(mysql_error());
			$row_check_address2 = mysql_fetch_assoc($check_address2);
			$totalRows_check_address2 = mysql_num_rows($check_address2);
			
			mysql_select_db($database_subman, $subman);
			$query_check_address1 = "SELECT * FROM addresses WHERE addresses.id = '".$_POST['id_'.($i+1)]."'";
			$check_address1 = mysql_query($query_check_address1, $subman) or die(mysql_error());
			$row_check_address1 = mysql_fetch_assoc($check_address1);
			$totalRows_check_address1 = mysql_num_rows($check_address1);
			
			if ($totalRows_check_address == 0 && $totalRows_check_address2 == 0 && ($row_check_address1['portid'] == "" || $row_check_address1['portid'] == 0)) {
				
				$deleteSQL = sprintf("DELETE FROM addresses WHERE id=%s",
						   GetSQLValueString($_POST['id_'.($i+1)], "int"));
	
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
				
			}
			else {
				
				$_SESSION['errstr'] = "One or more addresses could not be deleted as they are attached to a link.  Please delete the relevant link(s) first.";
				$errstrflag = 1;
				
			}
			
		}
	}
	
  $deleteGoTo = "containerView.php?errstr=".$errstrflag;
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if (isset($_SESSION['provide_step']) && $_SESSION['provide_step'] == 10) {
	
	#if ($_SESSION['provide_network'] != "" && $_SESSION['provide_existing'] == "") {
	#	mysql_select_db($database_subman, $subman);
	#	$query_checkNetwork = "SELECT * FROM networks WHERE networks.network = '".$_SESSION['provide_network']."'";
	#	$checkNetwork = mysql_query($query_checkNetwork, $subman) or die(mysql_error());
	#	$row_checkNetwork = mysql_fetch_assoc($checkNetwork);
	#	$totalRows_checkNetwork = mysql_num_rows($checkNetwork);
	#}
	if ($_SESSION['provide_timeslots_node_a'] > 0) {
		
		mysql_select_db($database_subman, $subman);
		$query_card_node_a = "SELECT * FROM cards WHERE cards.id = ".$_SESSION['provide_card_node_a']."";
		$card_node_a = mysql_query($query_card_node_a, $subman) or die(mysql_error());
		$row_card_node_a = mysql_fetch_assoc($card_node_a);
		$totalRows_card_node_a = mysql_num_rows($card_node_a);
		
		mysql_select_db($database_subman, $subman);
		$query_checktimeslots_node_a = "SELECT * FROM timeslots WHERE timeslots.card = ".$_SESSION['provide_card_node_a']." AND timeslots.port = ".$_SESSION['provide_port_node_a']."";
		$checktimeslots_node_a = mysql_query($query_checktimeslots_node_a, $subman) or die(mysql_error());
		$row_checktimeslots_node_a = mysql_fetch_assoc($checktimeslots_node_a);
		$totalRows_checktimeslots_node_a = mysql_num_rows($checktimeslots_node_a);
	
	}
	if ($_SESSION['provide_timeslots_node_b'] > 0) {
		
		mysql_select_db($database_subman, $subman);
		$query_card_node_b= "SELECT * FROM cards WHERE cards.id = ".$_SESSION['provide_card_node_b']."";
		$card_node_b = mysql_query($query_card_node_b, $subman) or die(mysql_error());
		$row_card_node_b = mysql_fetch_assoc($card_node_b);
		$totalRows_card_node_b = mysql_num_rows($card_node_b);
		
		mysql_select_db($database_subman, $subman);
		$query_checktimeslots_node_b = "SELECT * FROM timeslots WHERE timeslots.card = ".$_SESSION['provide_card_node_b']." AND timeslots.port = ".$_SESSION['provide_port_node_b']."";
		$checktimeslots_node_b = mysql_query($query_checktimeslots_node_b, $subman) or die(mysql_error());
		$row_checktimeslots_node_b = mysql_fetch_assoc($checktimeslots_node_b);
		$totalRows_checktimeslots_node_b = mysql_num_rows($checktimeslots_node_b);
	
	}
		
	mysql_select_db($database_subman, $subman);
	$query_port_node_a = "SELECT * FROM portsports WHERE portsports.card = ".$_SESSION['provide_card_node_a']." AND portsports.port = ".$_SESSION['provide_port_node_a']."";
	$port_node_a = mysql_query($query_port_node_a, $subman) or die(mysql_error());
	$row_port_node_a = mysql_fetch_assoc($port_node_a);
	$totalRows_port_node_a = mysql_num_rows($port_node_a);
	
	if ($_SESSION['provide_port_node_b'] != "") {
		
		mysql_select_db($database_subman, $subman);
		$query_port_node_b = "SELECT * FROM portsports WHERE portsports.card = ".$_SESSION['provide_card_node_b']." AND portsports.port = ".$_SESSION['provide_port_node_b']."";
		$port_node_b = mysql_query($query_port_node_b, $subman) or die(mysql_error());
		$row_port_node_b = mysql_fetch_assoc($port_node_b);
		$totalRows_port_node_b = mysql_num_rows($port_node_b);
		
	}
	
	#if ($totalRows_checkNetwork > 0) { ?>
    	
      
    
    <?php# } ?>
	<?php if (($totalRows_checktimeslots_node_a + $_SESSION['provide_timeslots_node_a']) > $row_card_node_a['timeslots']) { ?>
    	
        <p class="text_red">Error: The number of timeslots required now exceed the number available on the selected card.</p>
        
    <?php }
	elseif (($totalRows_checktimeslots_node_b + $_SESSION['provide_timeslots_node_b']) > $row_card_node_b['timeslots']) { ?>
    	
        <p class="text_red">Error: The number of timeslots required now exceed the number available on the selected card.</p>
        
    <?php }
	elseif ($totalRows_port_node_a > 0 && $_SESSION['provide_logical_node_a'] == "normal") { ?>
    	
        <p class="text_red">Error: The port is no longer available on the selected card.</p>
        
    <?php }
	elseif ($totalRows_port_node_b > 0 && $_SESSION['provide_logical_node_b'] == "normal") { ?>
    	
        <p class="text_red">Error: The port is no longer available on the selected card.</p>
        
    <?php }
	else {
		
		mysql_select_db($database_subman, $subman);
		$query_customer = "SELECT * FROM customer WHERE customer.id = ".$_SESSION['provide_customer']."";
		$customer = mysql_query($query_customer, $subman) or die(mysql_error());
		$row_customer = mysql_fetch_assoc($customer);
		$totalRows_customer = mysql_num_rows($customer);
		
		mysql_select_db($database_subman, $subman);
		$query_node_a = "SELECT * FROM portsdevices WHERE portsdevices.id = '".$_SESSION['provide_node_a']."'";
		$node_a = mysql_query($query_node_a, $subman) or die(mysql_error());
		$row_node_a = mysql_fetch_assoc($node_a);
		$totalRows_node_a = mysql_num_rows($node_a);
		
		mysql_select_db($database_subman, $subman);
		$query_node_b = "SELECT * FROM portsdevices WHERE portsdevices.id = '".$_SESSION['provide_node_b']."'";
		$node_b = mysql_query($query_node_b, $subman) or die(mysql_error());
		$row_node_b = mysql_fetch_assoc($node_b);
		$totalRows_node_b = mysql_num_rows($node_b);
		
		$vlan_node_a = $_SESSION['provide_vlan_node_a'];
		
		if ($_SESSION['provide_vlan_node_b'] == "same") {
			$vlan_node_b = $_SESSION['provide_vlan_node_a'];
		}
		else {
			$vlan_node_b = $_SESSION['provide_vlan_node_b'];
		}
		
		
		$_SESSION['provide_cct'] = str_replace("%customername%",$row_customer['name'],$_SESSION['provide_cct']);
		$_SESSION['provide_cct'] = str_replace("%customername_upper%",strtoupper($row_customer['name']),$_SESSION['provide_cct']);
		$_SESSION['provide_cct'] = str_replace("%nodea%",$row_node_a['name'],$_SESSION['provide_cct']);
		$_SESSION['provide_cct'] = str_replace("%nodeb%",$row_node_b['name'],$_SESSION['provide_cct']);
		$_SESSION['provide_cct'] = str_replace("%customername_trimmed%",str_replace(" ","",$row_customer['name']),$_SESSION['provide_cct']);
		$_SESSION['provide_cct'] = str_replace("%customername_trimmed_upper%",str_replace(" ","",strtoupper($row_customer['name'])),$_SESSION['provide_cct']);
		
						
		if ($_SESSION['provide_layer'] == 3) {
			
			if ($_SESSION['provide_existing'] != 1) {	
			
				mysql_select_db($database_subman, $subman);
				$query_netgroup = "SELECT networkGroup FROM networks WHERE networks.id = ".$_SESSION['provide_parent']."";
				$netgroup = mysql_query($query_netgroup, $subman) or die(mysql_error());
				$row_netgroup = mysql_fetch_assoc($netgroup);
				$totalRows_netgroup = mysql_num_rows($netgroup);
				
				mysql_select_db($database_subman, $subman);
				$query_getNetwork = "SELECT * FROM networks WHERE networks.id = ".$_SESSION['provide_parent']."";
				$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
				$row_getNetwork = mysql_fetch_assoc($getNetwork);
				$totalRows_getNetwork = mysql_num_rows($getNetwork);
				
				if ($row_getNetwork['v6mask'] == "") {
					
					$mask = get_dotted_mask($_SESSION['provide_netsize']);
					$descr = $row_customer['name'].": ".$_SESSION['provide_cct'];
					
					  $insertSQL = sprintf("INSERT INTO networks (network, mask, maskLong, short, descr, `user`, `date`, comments, networkGroup, parent, container) VALUES (%s, %s, INET_ATON('%s'), INET_NTOA('%s'), %s, %s, now(), %s, %s, %s, %s)",
							   GetSQLValueString($_SESSION['provide_network'], "text"),
							   GetSQLValueString($mask, "text"),
							   $mask,
							   $_SESSION['provide_network'],
							   GetSQLValueString($descr, "text"),
							   GetSQLValueString($_SESSION['MM_Username'], "text"),
							   GetSQLValueString($_POST['comments'], "text"),
							   GetSQLValueString($row_netgroup['networkGroup'], "int"),
							   GetSQLValueString($_SESSION['provide_parent'], "int"),
							   GetSQLValueString($_GET['container'], "int"));
		
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					  
					  $network = mysql_insert_id();
					  
				}
				else {
					
					$descr = $row_customer['name'].": ".$_SESSION['provide_cct'];
					
					  $insertSQL = sprintf("INSERT INTO networks (network, v6mask, short, descr, `user`, `date`, comments, networkGroup, parent, container) VALUES (%s, %s, %s, %s, %s, now(), %s, %s, %s, %s)",
							   GetSQLValueString($_SESSION['provide_network'], "text"),
							   GetSQLValueString($_SESSION['provide_netsize'], "text"),
							   GetSQLValueString(long2ipv6($_SESSION['provide_network']), "text"),
							   GetSQLValueString($descr, "text"),
							   GetSQLValueString($_SESSION['MM_Username'], "text"),
							   GetSQLValueString($_POST['comments'], "text"),
							   GetSQLValueString($row_netgroup['networkGroup'], "int"),
							   GetSQLValueString($_SESSION['provide_parent'], "int"),
							   GetSQLValueString($_GET['container'], "int"));
		
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					  
					  $network = mysql_insert_id();
					
				}
				  
				  if ($_SESSION['provide_address'] == 1) {
					  
					  if ($row_getNetwork['v6mask'] == "") {
							  
							$net = array();
							$net = find_net(long2ip($_SESSION['provide_network']),$mask);
							
							// Check that the address is not already being used on this network, if it is, print an error message
							mysql_select_db($database_subman, $subman);
							$query_checkAddr = "SELECT * FROM addresses WHERE addresses.network = ".$network." AND addresses.address = '".ip2long($_SESSION['provide_address_node_a'])."'";
							$checkAddr = mysql_query($query_checkAddr, $subman) or die(mysql_error());
							$row_checkAddr = mysql_fetch_assoc($checkAddr);
							$totalRows_checkAddr = mysql_num_rows($checkAddr);
							
							if (!(Net_IPv4::ipInNetwork($_SESSION['provide_address_node_a'], long2ip($_SESSION['provide_network']).get_slash($mask)))) { 
								$manualerrflag = 1;
							}
							
							if ($_SESSION['provide_node_b'] != "") {
								
								mysql_select_db($database_subman, $subman);
								$query_checkAddrB = "SELECT * FROM addresses WHERE addresses.network = ".$network." AND addresses.address = '".ip2long($_SESSION['provide_address_node_b'])."'";
								$checkAddrB = mysql_query($query_checkAddrB, $subman) or die(mysql_error());
								$row_checkAddrB = mysql_fetch_assoc($checkAddrB);
								$totalRows_checkAddrB = mysql_num_rows($checkAddrB);
								
								if (!(Net_IPv4::ipInNetwork($_SESSION['provide_address_node_b'], long2ip($_SESSION['provide_network']).get_slash($mask)))) { 
									$manualerrflag = 1;
								}
								
							}
							
					  }
					  else {
							
							// Check that the address is not already being used on this network, if it is, print an error message
							mysql_select_db($database_subman, $subman);
							$query_checkAddr = "SELECT * FROM addresses WHERE addresses.network = ".$network." AND addresses.address = '".ipv62long(Net_IPv6::Uncompress($_SESSION['provide_address_node_a']))."'";
							$checkAddr = mysql_query($query_checkAddr, $subman) or die(mysql_error());
							$row_checkAddr = mysql_fetch_assoc($checkAddr);
							$totalRows_checkAddr = mysql_num_rows($checkAddr);
							
							if (!(Net_IPv6::isInNetmask(Net_IPv6::Compress($_SESSION['provide_address_node_a']), Net_IPv6::Compress(long2ipv6($_SESSION['provide_network'])),$_SESSION['provide_netsize']))) { 
								$manualerrflag = 1;
							}
							
							if ($_SESSION['provide_node_b'] != "") {
								
								mysql_select_db($database_subman, $subman);
								$query_checkAddrB = "SELECT * FROM addresses WHERE addresses.network = ".$network." AND addresses.address = '".ipv62long(Net_IPv6::Uncompress($_SESSION['provide_address_node_b']))."'";
								$checkAddrB = mysql_query($query_checkAddrB, $subman) or die(mysql_error());
								$row_checkAddrB = mysql_fetch_assoc($checkAddrB);
								$totalRows_checkAddrB = mysql_num_rows($checkAddrB);
								
								if (!(Net_IPv6::isInNetmask(Net_IPv6::Compress($_SESSION['provide_address_node_b']), Net_IPv6::Compress(long2ipv6($_SESSION['provide_network'])),$_SESSION['provide_netsize']))) { 
									$manualerrflag = 1;
								}
								
							}
						  
					  }
								
				  }
				  
				  if ($row_getNetwork['v6mask'] == "") {
					  
					  if ($_SESSION['provide_address'] == 1 && $manualerrflag != 1 && $totalRows_checkAddr == 0 && ($totalRows_checkAddrB == 0 || !isset($totalRows_checkAddrB)) && ($_SESSION['provide_address_node_a'] != $_SESSION['provide_address_node_b'])) {
						  
						  $firstAddr = ip2long($_SESSION['provide_address_node_a']);
						  $secondAddr = ip2long($_SESSION['provide_address_node_b']);
					  }
					  elseif ($_SESSION['provide_netsize'] == 31) {
						  $firstAddr = $_SESSION['provide_network'];
						  $secondAddr = $_SESSION['provide_network']+1;
					  }
					  else {
						  $firstAddr = $_SESSION['provide_network']+1;
						  $secondAddr = $_SESSION['provide_network']+2;
					  }
					  
				  }
				  else {
					  
					  if ($_SESSION['provide_address'] == 1 && $manualerrflag != 1 && $totalRows_checkAddr == 0 && ($totalRows_checkAddrB == 0 || !isset($totalRows_checkAddrB)) && ($_SESSION['provide_address_node_a'] != $_SESSION['provide_address_node_b'])) {
						  
						  $firstAddr = ipv62long(Net_IPv6::Uncompress($_SESSION['provide_address_node_a']));
						  $secondAddr = ipv62long(Net_IPv6::Uncompress($_SESSION['provide_address_node_b']));
					  }
					  else {
						  $firstAddr = bcadd($_SESSION['provide_network'],1);
						  $secondAddr = bcadd($_SESSION['provide_network'],2);
					  }
					  
				  }
					  
				  
				  if ($_SESSION['provide_node_b'] != "") {
					  $descr = $row_node_a['name']." --> ".$row_node_b['name']." (".$_SESSION['provide_cct'].")";
				  }
				  else {
					  $descr = $_SESSION['provide_cct'];
				  }
				  
				  if ($row_getNetwork['v6mask'] == "") {
					  
					  $insertSQL = sprintf("INSERT INTO addresses (address, network, short, descr, customer, `user`, date) VALUES (%s, %s, INET_NTOA('%s'), %s, %s, %s, now())",
							   GetSQLValueString($firstAddr, "text"),
							   GetSQLValueString($network, "int"),
							   $firstAddr,
							   GetSQLValueString($descr, "text"),
							   GetSQLValueString($_SESSION['provide_customer'], "int"),
							   GetSQLValueString($_SESSION['MM_Username'], "text"));
					
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					  
					  $router_node_a = mysql_insert_id();
					  
					if ($_SESSION['provide_node_b'] != "") {
						
						$descr = $row_node_b['name']." --> ".$row_node_a['name']." (".$_SESSION['provide_cct'].")";
						
						$insertSQL = sprintf("INSERT INTO addresses (address, network, short, descr, customer, `user`, date) VALUES (%s, %s, INET_NTOA('%s'), %s, %s, %s, now())",
							   GetSQLValueString($secondAddr, "text"),
							   GetSQLValueString($network, "int"),
							   $secondAddr,
							   GetSQLValueString($descr, "text"),
							   GetSQLValueString($_SESSION['provide_customer'], "int"),
							   GetSQLValueString($_SESSION['MM_Username'], "text"));
					
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					  
					  $router_node_b = mysql_insert_id();
					  
					}
					
				  }
				  else {
					  
					  $insertSQL = sprintf("INSERT INTO addresses (address, network, short, descr, customer, `user`, date) VALUES (%s, %s, %s, %s, %s, %s, now())",
							   GetSQLValueString($firstAddr, "text"),
							   GetSQLValueString($network, "int"),
							   GetSQLValueString(long2ipv6($firstAddr),"text"),
							   GetSQLValueString($descr, "text"),
							   GetSQLValueString($_SESSION['provide_customer'], "int"),
							   GetSQLValueString($_SESSION['MM_Username'], "text"));
					
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					  
					  $router_node_a = mysql_insert_id();
					  
					if ($_SESSION['provide_node_b'] != "") {
						
						$descr = $row_node_b['name']." --> ".$row_node_a['name']." (".$_SESSION['provide_cct'].")";
						
						 $insertSQL = sprintf("INSERT INTO addresses (address, network, short, descr, customer, `user`, date) VALUES (%s, %s, %s, %s, %s, %s, now())",
							   GetSQLValueString($secondAddr, "text"),
							   GetSQLValueString($network, "int"),
							   GetSQLValueString(long2ipv6($secondAddr),"text"),
							   GetSQLValueString($descr, "text"),
							   GetSQLValueString($_SESSION['provide_customer'], "int"),
							   GetSQLValueString($_SESSION['MM_Username'], "text"));
					
					  mysql_select_db($database_subman, $subman);
					  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					  
					  $router_node_b = mysql_insert_id();
					  
					}
					
					  
				  }
			
			}
			
			else {
				
				mysql_select_db($database_subman, $subman);
				$query_network = "SELECT * FROM networks WHERE networks.id = ".$_SESSION['provide_parent']."";
				$network = mysql_query($query_network, $subman) or die(mysql_error());
				$row_network = mysql_fetch_assoc($network);
				$totalRows_network = mysql_num_rows($network);
				
				if ($row_network['v6mask'] == "") {
					$net = find_net(long2ip($row_network['network']),$row_network['mask']);
				}
				
				$addresses = array();
				
				if ($_SESSION['provide_node_b'] != "") {
					  $descr = $row_node_a['name']." --> ".$row_node_b['name']." (".$_SESSION['provide_cct'].")";
				  }
				  else {
					  $descr = $_SESSION['provide_cct'];
				  }
				
				if ($_SESSION['provide_address'] == 1) {
						
						if ($row_network['v6mask'] == "") {
							
							// Check that the address is not already being used on this network, if it is, print an error message
							mysql_select_db($database_subman, $subman);
							$query_checkAddr = "SELECT * FROM addresses WHERE addresses.network = ".$row_network['id']." AND addresses.address = '".ip2long($_SESSION['provide_address_node_a'])."'";
							$checkAddr = mysql_query($query_checkAddr, $subman) or die(mysql_error());
							$row_checkAddr = mysql_fetch_assoc($checkAddr);
							$totalRows_checkAddr = mysql_num_rows($checkAddr);
							
							if (!(Net_IPv4::ipInNetwork($_SESSION['provide_address_node_a'], long2ip($_SESSION['provide_network']).get_slash($row_network['mask'])))) { 
								$manualerrflag = 1;
							}
							
							if ($_SESSION['provide_node_b'] != "") {
								
								mysql_select_db($database_subman, $subman);
								$query_checkAddrB = "SELECT * FROM addresses WHERE addresses.network = ".$row_network['id']." AND addresses.address = '".ip2long($_SESSION['provide_address_node_b'])."'";
								$checkAddrB = mysql_query($query_checkAddrB, $subman) or die(mysql_error());
								$row_checkAddrB = mysql_fetch_assoc($checkAddrB);
								$totalRows_checkAddrB = mysql_num_rows($checkAddrB);
								
								if (!(Net_IPv4::ipInNetwork($_SESSION['provide_address_node_b'], long2ip($_SESSION['provide_network']).get_slash($row_network['mask'])))) { 
									$manualerrflag = 1;
								}
								
							}
							
						}
						else {
							
							// Check that the address is not already being used on this network, if it is, print an error message
							mysql_select_db($database_subman, $subman);
							$query_checkAddr = "SELECT * FROM addresses WHERE addresses.network = ".$row_network['id']." AND addresses.address = '".ipv62long(Net_IPv6::Uncompress($_SESSION['provide_address_node_a']))."'";
							$checkAddr = mysql_query($query_checkAddr, $subman) or die(mysql_error());
							$row_checkAddr = mysql_fetch_assoc($checkAddr);
							$totalRows_checkAddr = mysql_num_rows($checkAddr);
							
							if (!(Net_IPv6::isInNetmask(Net_IPv6::Compress($_SESSION['provide_address_node_a']), Net_IPv6::Compress(long2ipv6($_SESSION['provide_network'])),$_SESSION['provide_netsize']))) { 
								$manualerrflag = 1;
							}
							
							if ($_SESSION['provide_node_b'] != "") {
								
								mysql_select_db($database_subman, $subman);
								$query_checkAddrB = "SELECT * FROM addresses WHERE addresses.network = ".$row_network['id']." AND addresses.address = '".ipv62long(Net_IPv6::Uncompress($_SESSION['provide_address_node_b']))."'";
								$checkAddrB = mysql_query($query_checkAddrB, $subman) or die(mysql_error());
								$row_checkAddrB = mysql_fetch_assoc($checkAddrB);
								$totalRows_checkAddrB = mysql_num_rows($checkAddrB);
								
								if (!(Net_IPv6::isInNetmask(Net_IPv6::Compress($_SESSION['provide_address_node_b']), Net_IPv6::Compress(long2ipv6($_SESSION['provide_network'])),$_SESSION['provide_netsize']))) { 
									$manualerrflag = 1;
								}
								
							}
						  
					  }
							
				  }
				  
				  if ($row_network['v6mask'] == "") {
					  
					  if ($_SESSION['provide_address'] == 1 && $manualerrflag != 1 && $totalRows_checkAddr == 0 && ($totalRows_checkAddrB == 0 || !isset($totalRows_checkAddrB)) && ($_SESSION['provide_address_node_a'] != $_SESSION['provide_address_node_b'])) {
						  $firstAddr = ip2long($_SESSION['provide_address_node_a']);
						  $secondAddr = ip2long($_SESSION['provide_address_node_b']);
					  }
					  else {
						  
							for ($i = ($row_network['network']+1); $i < $net['broadcast']; $i++) {
								
								mysql_select_db($database_subman, $subman);
								$query_addresses = "SELECT * FROM addresses WHERE addresses.network = ".$_SESSION['provide_parent']." AND addresses.address = '".$i."'";
								$addresses = mysql_query($query_addresses, $subman) or die(mysql_error());
								$row_addresses = mysql_fetch_assoc($addresses);
								$totalRows_addresses = mysql_num_rows($addresses);
								
								if ($totalRows_addresses == 0) {
									
									if ($firstAddr == "") {
										
										$firstAddr = $i;
										$firstAddrFlag = 1;
										
									}
									if ($firstAddr != "" && $i > $firstAddr) {
										
										$secondAddr = $i;
									
									}
									if ($firstAddr != "" && $secondAddr != "") {
										
										$i = $net['broadcast'];	
										
									}
									
								}
								
							}
					  
					  }
					  
					if ($firstAddr != "") {
	
						$insertSQL = sprintf("INSERT INTO addresses (address, network, short, descr, customer, `user`, date) VALUES (%s, %s, INET_NTOA('%s'), %s, %s, %s, now())",
							   GetSQLValueString($firstAddr, "text"),
							   GetSQLValueString($row_network['id'], "int"),
							   $firstAddr,
							   GetSQLValueString($descr, "text"),
							   GetSQLValueString($_SESSION['provide_customer'], "int"),
							   GetSQLValueString($_SESSION['MM_Username'], "text"));
					
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						  
						  $router_node_a = mysql_insert_id();
					  
					}
					
					if ($_SESSION['provide_node_b'] != "") {
						
						if ($secondAddr != "") {
							
							$descr = $row_node_b['name']." --> ".$row_node_a['name']." (".$_SESSION['provide_cct'].")";
							
							$insertSQL = sprintf("INSERT INTO addresses (address, network, short, descr, customer, `user`, date) VALUES (%s, %s, INET_NTOA('%s'), %s, %s, %s, now())",
								   GetSQLValueString($secondAddr, "text"),
								   GetSQLValueString($row_network['id'], "int"),
								   $secondAddr,
								   GetSQLValueString($descr, "text"),
								   GetSQLValueString($_SESSION['provide_customer'], "int"),
								   GetSQLValueString($_SESSION['MM_Username'], "text"));
						
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						  
						  $router_node_b = mysql_insert_id();
					  
						}
						
					}
					
				  }
				  else {
					  
					if ($_SESSION['provide_address'] == 1 && $manualerrflag != 1 && $totalRows_checkAddr == 0 && ($totalRows_checkAddrB == 0 || !isset($totalRows_checkAddrB)) && ($_SESSION['provide_address_node_a'] != $_SESSION['provide_address_node_b'])) {
						  $firstAddr = ipv62long(Net_IPv6::Uncompress($_SESSION['provide_address_node_a']));
						  $secondAddr = ipv62long(Net_IPv6::Uncompress($_SESSION['provide_address_node_b']));
					  }
					  else {
						  
							for ($i = bcadd($row_network['network'],1); bccomp((bcadd($row_network['network'],bcpow(2,(128 - $row_network['v6mask'])))),$i) == 1; $i = bcadd($i,1)) {
								
								mysql_select_db($database_subman, $subman);
								$query_addresses = "SELECT * FROM addresses WHERE addresses.network = ".$_SESSION['provide_parent']." AND addresses.address = '".$i."'";
								$addresses = mysql_query($query_addresses, $subman) or die(mysql_error());
								$row_addresses = mysql_fetch_assoc($addresses);
								$totalRows_addresses = mysql_num_rows($addresses);
								
								if ($totalRows_addresses == 0) {
									
									if ($firstAddr == "") {
										
										$firstAddr = $i;
										$firstAddrFlag = 1;
										
									}
									if ($firstAddr != "" && (bccomp($i,$firstAddr) == 1)) {
										
										$secondAddr = $i;
									
									}
									if ($firstAddr != "" && $secondAddr != "") {
										
										$i = bcadd($row_network['network'],bcpow(2,(128 - $row_network['v6mask'])));	
										
									}
									
								}
								
							}
					  
					  }
					  
					if ($firstAddr != "") {
	
						$insertSQL = sprintf("INSERT INTO addresses (address, network, short, descr, customer, `user`, date) VALUES (%s, %s, %s, %s, %s, %s, now())",
							   GetSQLValueString($firstAddr, "text"),
							   GetSQLValueString($row_network['id'], "int"),
							   GetSQLValueString(long2ipv6($firstAddr), "text"),
							   GetSQLValueString($descr, "text"),
							   GetSQLValueString($_SESSION['provide_customer'], "int"),
							   GetSQLValueString($_SESSION['MM_Username'], "text"));
					
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						  
						  $router_node_a = mysql_insert_id();
					  
					}
					
					if ($_SESSION['provide_node_b'] != "") {
						
						if ($secondAddr != "") {
							
							$descr = $row_node_b['name']." --> ".$row_node_a['name']." (".$_SESSION['provide_cct'].")";
							
							$insertSQL = sprintf("INSERT INTO addresses (address, network, short, descr, customer, `user`, date) VALUES (%s, %s, %s, %s, %s, %s, now())",
							   GetSQLValueString($secondAddr, "text"),
							   GetSQLValueString($row_network['id'], "int"),
							   GetSQLValueString(long2ipv6($secondAddr), "text"),
							   GetSQLValueString($descr, "text"),
							   GetSQLValueString($_SESSION['provide_customer'], "int"),
							   GetSQLValueString($_SESSION['MM_Username'], "text"));
						
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						  
						  $router_node_b = mysql_insert_id();
					  
						}
						
					}  
					  
				  }
				
			}
			  
		}
		
		if ($_SESSION['additional_address'] == 1) {
			
			if ($_SESSION['provide_subint_node_a']) {
				$linknetworkport = $_SESSION['provide_subint_node_a'];
				
				$updateSQL = sprintf("UPDATE addresses SET subintid = %s WHERE addresses.id = %s",
							   GetSQLValueString($linknetworkport, "int"),
							   GetSQLValueString($router_node_a,"int"));
						
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
						  
			}
			else {
				$linknetworkport = $_SESSION['provide_port_node_a'];
				
				$updateSQL = sprintf("UPDATE addresses SET portid = %s WHERE addresses.id = %s",
							   GetSQLValueString($linknetworkport, "int"),
							   GetSQLValueString($router_node_a,"int"));
						
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
			}
						  
			
			if ($router_node_b) {
				
				if ($_SESSION['provide_subint_node_b']) {
					$linknetworkport = $_SESSION['provide_subint_node_b'];
					
					$updateSQL = sprintf("UPDATE addresses SET subintid = %s WHERE addresses.id = %s",
							   GetSQLValueString($linknetworkport, "int"),
							   GetSQLValueString($router_node_b,"int"));
						
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
						  
				}
				else {
					$linknetworkport = $_SESSION['provide_port_node_b'];
					
					$updateSQL = sprintf("UPDATE addresses SET portid = %s WHERE addresses.id = %s",
							   GetSQLValueString($linknetworkport, "int"),
							   GetSQLValueString($router_node_b,"int"));
						
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
				}
						  
			}
			
			if ($_SESSION['provide_existing'] == 1) {
				$network = $_SESSION['provide_parent'];
			}
		
			$insertSQL = sprintf("REPLACE INTO linknetworks (link, network) VALUES (%s, %s)",
							   GetSQLValueString($_SESSION['additional_address_link'], "int"),
							   GetSQLValueString($network, "int"));
						
						  mysql_select_db($database_subman, $subman);
						  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
			
			
		}
		else {
		
			if (preg_match('/^auto_+/',$_SESSION['provide_vlan_node_a'])) {
				
				list($crap,$vlanpool) = split('_',$_SESSION['provide_vlan_node_a']);
				
				mysql_select_db($database_subman, $subman);
				$query_vlanpool = "SELECT * FROM vlanpool WHERE vlanpool.id = '".$vlanpool."'";
				$vlanpool = mysql_query($query_vlanpool, $subman) or die(mysql_error());
				$row_vlanpool = mysql_fetch_assoc($vlanpool);
				$totalRows_vlanpool = mysql_num_rows($vlanpool);
				
				for ($i = $row_vlanpool['poolstart']; $i <= $row_vlanpool['poolend']; $i++) {
					
					mysql_select_db($database_subman, $subman);
					$query_vlan = "SELECT * FROM vlan WHERE vlan.vlanpool = '".$row_vlanpool['id']."' AND vlan.number = ".$i."";
					$vlan = mysql_query($query_vlan, $subman) or die(mysql_error());
					$row_vlan = mysql_fetch_assoc($vlan);
					$totalRows_vlan = mysql_num_rows($vlan);
					
					if ($totalRows_vlan == 0) {
						
						$insertSQL = sprintf("INSERT INTO vlan (name, vlanpool, number, customer) VALUES (%s, %s, %s, %s)",
						   GetSQLValueString($_SESSION['provide_cct'], "text"),
						   GetSQLValueString($row_vlanpool['id'], "int"),
						   GetSQLValueString($i, "int"),
						   GetSQLValueString($_SESSION['provide_customer'], "int"));
				
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$vlan_node_a = mysql_insert_id();
						
						if ($_SESSION['provide_vlan_node_b'] == "same") {
							$vlan_node_b = $vlan_node_a;
						}
						
						$i = $row_vlanpool['poolend'] + 1;
	
					}
					
				}	
					
			}
			
			if (preg_match('/^manual_+/',$_SESSION['provide_vlan_node_a'])) {
				
				list($crap,$vlanpool) = split('_',$_SESSION['provide_vlan_node_a']);
				
				mysql_select_db($database_subman, $subman);
				$query_vlan = "SELECT * FROM vlan WHERE vlan.vlanpool = '".$vlanpool."' AND vlan.number = ".$_SESSION['vlannumber_a'];
				$vlan = mysql_query($query_vlan, $subman) or die(mysql_error());
				$row_vlan = mysql_fetch_assoc($vlan);
				$totalRows_vlan = mysql_num_rows($vlan);
					
				mysql_select_db($database_subman, $subman);
				$query_vlanpool = "SELECT * FROM vlanpool WHERE vlanpool.id = '".$vlanpool."'";
				$vlanpool = mysql_query($query_vlanpool, $subman) or die(mysql_error());
				$row_vlanpool = mysql_fetch_assoc($vlanpool);
				$totalRows_vlanpool = mysql_num_rows($vlanpool);
					
				if ($totalRows_vlan > 0) {
					
					for ($i = $row_vlanpool['poolstart']; $i <= $row_vlanpool['poolend']; $i++) {
						
						mysql_select_db($database_subman, $subman);
						$query_vlan = "SELECT * FROM vlan WHERE vlan.vlanpool = '".$row_vlanpool['id']."' AND vlan.number = ".$i."";
						$vlan = mysql_query($query_vlan, $subman) or die(mysql_error());
						$row_vlan = mysql_fetch_assoc($vlan);
						$totalRows_vlan = mysql_num_rows($vlan);
						
						if ($totalRows_vlan == 0) {
							
							$insertSQL = sprintf("INSERT INTO vlan (name, vlanpool, number, customer) VALUES (%s, %s, %s, %s)",
							   GetSQLValueString($_SESSION['provide_cct'], "text"),
							   GetSQLValueString($row_vlanpool['id'], "int"),
							   GetSQLValueString($i, "int"),
							   GetSQLValueString($_SESSION['provide_customer'], "int"));
					
							mysql_select_db($database_subman, $subman);
							$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
							
							$vlan_node_a = mysql_insert_id();
							
							if ($_SESSION['provide_vlan_node_b'] == "same") {
								$vlan_node_b = $vlan_node_a;
							}
							
							$i = $row_vlanpool['poolend'] + 1;
		
						}
						
					}
					
				}
				
				else {
					
					$insertSQL = sprintf("INSERT INTO vlan (name, vlanpool, number, customer) VALUES (%s, %s, %s, %s)",
							   GetSQLValueString($_SESSION['provide_cct'], "text"),
							   GetSQLValueString($row_vlanpool['id'], "int"),
							   GetSQLValueString($_SESSION['vlannumber_a'], "int"),
							   GetSQLValueString($_SESSION['provide_customer'], "int"));
					
							mysql_select_db($database_subman, $subman);
							$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
							
							$vlan_node_a = mysql_insert_id();
							
				}
					
			}
			
			if (preg_match('/^auto_+/',$_SESSION['provide_vlan_node_b'])) {
				
				list($crap,$vlanpool) = split('_',$_SESSION['provide_vlan_node_b']);
				
				mysql_select_db($database_subman, $subman);
				$query_vlanpool = "SELECT * FROM vlanpool WHERE vlanpool.id = '".$vlanpool."'";
				$vlanpool = mysql_query($query_vlanpool, $subman) or die(mysql_error());
				$row_vlanpool = mysql_fetch_assoc($vlanpool);
				$totalRows_vlanpool = mysql_num_rows($vlanpool);
				
				for ($i = $row_vlanpool['poolstart']; $i <= $row_vlanpool['poolend']; $i++) {
					
					mysql_select_db($database_subman, $subman);
					$query_vlan = "SELECT * FROM vlan WHERE vlan.vlanpool = '".$row_vlanpool['id']."' AND vlan.number = ".$i."";
					$vlan = mysql_query($query_vlan, $subman) or die(mysql_error());
					$row_vlan = mysql_fetch_assoc($vlan);
					$totalRows_vlan = mysql_num_rows($vlan);
					
					if ($totalRows_vlan == 0) {
						
						$insertSQL = sprintf("INSERT INTO vlan (name, vlanpool, number, customer) VALUES (%s, %s, %s, %s)",
						   GetSQLValueString($_SESSION['provide_cct'], "text"),
						   GetSQLValueString($row_vlanpool['id'], "int"),
						   GetSQLValueString($i, "int"),
						   GetSQLValueString($_SESSION['provide_customer'], "int"));
				
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$vlan_node_b = mysql_insert_id();
						
						$i = $row_vlanpool['poolend'] + 1;
	
					}
					
				}	
				
			}
			
			if (preg_match('/^manual_+/',$_SESSION['provide_vlan_node_b'])) {
				
				list($crap,$vlanpool) = split('_',$_SESSION['provide_vlan_node_b']);
				
				mysql_select_db($database_subman, $subman);
				$query_vlan = "SELECT * FROM vlan WHERE vlan.vlanpool = '".$vlanpool."' AND vlan.number = ".$_SESSION['vlannumber_b'];
				$vlan = mysql_query($query_vlan, $subman) or die(mysql_error());
				$row_vlan = mysql_fetch_assoc($vlan);
				$totalRows_vlan = mysql_num_rows($vlan);
					
				mysql_select_db($database_subman, $subman);
				$query_vlanpool = "SELECT * FROM vlanpool WHERE vlanpool.id = '".$vlanpool."'";
				$vlanpool = mysql_query($query_vlanpool, $subman) or die(mysql_error());
				$row_vlanpool = mysql_fetch_assoc($vlanpool);
				$totalRows_vlanpool = mysql_num_rows($vlanpool);
					
				if ($totalRows_vlan > 0) {
					
					for ($i = $row_vlanpool['poolstart']; $i <= $row_vlanpool['poolend']; $i++) {
						
						mysql_select_db($database_subman, $subman);
						$query_vlan = "SELECT * FROM vlan WHERE vlan.vlanpool = '".$row_vlanpool['id']."' AND vlan.number = ".$i."";
						$vlan = mysql_query($query_vlan, $subman) or die(mysql_error());
						$row_vlan = mysql_fetch_assoc($vlan);
						$totalRows_vlan = mysql_num_rows($vlan);
						
						if ($totalRows_vlan == 0) {
							
							$insertSQL = sprintf("INSERT INTO vlan (name, vlanpool, number, customer) VALUES (%s, %s, %s, %s)",
							   GetSQLValueString($_SESSION['provide_cct'], "text"),
							   GetSQLValueString($row_vlanpool['id'], "int"),
							   GetSQLValueString($i, "int"),
							   GetSQLValueString($_SESSION['provide_customer'], "int"));
					
							mysql_select_db($database_subman, $subman);
							$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
							
							$vlan_node_b = mysql_insert_id();
							
							$i = $row_vlanpool['poolend'] + 1;
		
						}
						
					}
					
				}
				
				else {
					
					$insertSQL = sprintf("INSERT INTO vlan (name, vlanpool, number, customer) VALUES (%s, %s, %s, %s)",
							   GetSQLValueString($_SESSION['provide_cct'], "text"),
							   GetSQLValueString($row_vlanpool['id'], "int"),
							   GetSQLValueString($_SESSION['vlannumber_b'], "int"),
							   GetSQLValueString($_SESSION['provide_customer'], "int"));
					
							mysql_select_db($database_subman, $subman);
							$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
							
							$vlan_node_b = mysql_insert_id();
							
				}
					
			}
			
			if ($_SESSION['provide_vpn'] != "") {
				
				mysql_select_db($database_subman, $subman);
				$query_provide_vpnxconnect = "SELECT vpnxconnect.*, vpn.layer FROM vpnxconnect LEFT JOIN vpn ON vpn.id = vpnxconnect.vpn WHERE vpnxconnect.vpn = '".$_SESSION['provide_vpn']."'";
				$provide_vpnxconnect = mysql_query($query_provide_vpnxconnect, $subman) or die(mysql_error());
				$row_provide_vpnxconnect = mysql_fetch_assoc($provide_vpnxconnect);
				$totalRows_provide_vpnxconnect = mysql_num_rows($provide_vpnxconnect);
				
				if ($row_provide_vpnxconnect['layer'] == 4) {
					
					$xconnectid = $row_provide_vpnxconnect['xconnect'];
					
				}
				
				if (preg_match('/^new_multi_+/',$_SESSION['provide_vpn']) && $_SESSION['provide_layer'] == 2) {
					
					$insertSQL = sprintf("INSERT INTO vpn (name, descr, layer) VALUES (%s, %s, '4')",
								   GetSQLValueString($_SESSION['provide_cct'], "text"),
								   GetSQLValueString($_SESSION['provide_cct'], "text"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$vpnID = mysql_insert_id();
						
					$insertSQL = sprintf("INSERT INTO vpncustomer (vpn, customer) VALUES (%s, %s)",
								   GetSQLValueString($vpnID, "int"),
								   GetSQLValueString($_SESSION['provide_customer'], "int"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					
					list($crap,$crap1,$providerID) = split("_",$_SESSION['provide_vpn']);
					
					$insertSQL = sprintf("INSERT INTO providervpn (provider, vpn) VALUES (%s, %s)",
								   GetSQLValueString($providerID, "int"),
								   GetSQLValueString($vpnID, "int"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					
					$_SESSION['provide_vpn'] = $vpnID;
					
				}
				elseif (preg_match('/^new_+/',$_SESSION['provide_vpn']) && $_SESSION['provide_layer'] == 2) {
					
					$insertSQL = sprintf("INSERT INTO vpn (name, descr, layer) VALUES (%s, %s, %s)",
								   GetSQLValueString($_SESSION['provide_cct'], "text"),
								   GetSQLValueString($_SESSION['provide_cct'], "text"),
								   GetSQLValueString($_SESSION['provide_layer'], "int"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$vpnID = mysql_insert_id();
						
					$insertSQL = sprintf("INSERT INTO vpncustomer (vpn, customer) VALUES (%s, %s)",
								   GetSQLValueString($vpnID, "int"),
								   GetSQLValueString($_SESSION['provide_customer'], "int"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					
					list($crap,$providerID) = split("_",$_SESSION['provide_vpn']);
					
					$insertSQL = sprintf("INSERT INTO providervpn (provider, vpn) VALUES (%s, %s)",
								   GetSQLValueString($providerID, "int"),
								   GetSQLValueString($vpnID, "int"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					
					$_SESSION['provide_vpn'] = $vpnID;
					
				}
				
				else {
					
					mysql_select_db($database_subman, $subman);
					$query_checkCustomer = "SELECT * FROM vpncustomer WHERE vpncustomer.customer = '".$_SESSION['provide_customer']."' AND vpncustomer.vpn = '".$_SESSION['provide_vpn']."'";
					$checkCustomer = mysql_query($query_checkCustomer, $subman) or die(mysql_error());
					$row_checkCustomer = mysql_fetch_assoc($checkCustomer);
					$totalRows_checkCustomer = mysql_num_rows($checkCustomer);
					
					if ($totalRows_checkCustomer == 0) {
						
						$insertSQL = sprintf("INSERT INTO vpncustomer (vpn, customer) VALUES (%s, %s)",
								   GetSQLValueString($_SESSION['provide_vpn'], "int"),
								   GetSQLValueString($_SESSION['provide_customer'], "int"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					
					}
					
				}
				
			}
			
			if ($_SESSION['provide_xconnectpool'] != "") {
				
				mysql_select_db($database_subman, $subman);
				$query_xconnectpool = "SELECT * FROM xconnectpool WHERE xconnectpool.id = '".$_SESSION['provide_xconnectpool']."'";
				$xconnectpool = mysql_query($query_xconnectpool, $subman) or die(mysql_error());
				$row_xconnectpool = mysql_fetch_assoc($xconnectpool);
				$totalRows_xconnectpool = mysql_num_rows($xconnectpool);
				
				if ($_SESSION['xconnectid']) {
					mysql_select_db($database_subman, $subman);
					$query_xconnectids = "SELECT * FROM xconnectid WHERE xconnectid.xconnectpool = '".$_SESSION['provide_xconnectpool']."' AND xconnectid.xconnectid = '".$_SESSION['xconnectid']."'";
					$xconnectids = mysql_query($query_xconnectids, $subman) or die(mysql_error());
					$row_xconnectids = mysql_fetch_assoc($xconnectids);
					$totalRows_xconnectids = mysql_num_rows($xconnectids);
				}
				
				if ($_SESSION['xconnectid'] && $totalRows_xconnectids == 0) {
					
					$insertSQL = sprintf("INSERT INTO xconnectid (xconnectpool, xconnectid, descr) VALUES (%s, %s, %s)",
						   GetSQLValueString($row_xconnectpool['id'], "int"),
						   GetSQLValueString($_SESSION['xconnectid'], "int"),
						   GetSQLValueString($_SESSION['provide_cct'], "text"));
				
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$xconnectid = mysql_insert_id();
						
						$insertSQL = sprintf("INSERT INTO vpnxconnect (vpn, xconnect) VALUES (%s, %s)",
						   GetSQLValueString($_SESSION['provide_vpn'], "int"),
						   GetSQLValueString($xconnectid, "int"));
				
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
				}
				else {
					
				for ($i = $row_xconnectpool['xconnectstart']; $i <= $row_xconnectpool['xconnectend']; $i++) {
					
					mysql_select_db($database_subman, $subman);
					$query_xconnect = "SELECT * FROM xconnectid WHERE xconnectid.xconnectpool = '".$row_xconnectpool['id']."' AND xconnectid.xconnectid = ".$i."";
					$xconnect = mysql_query($query_xconnect, $subman) or die(mysql_error());
					$row_xconnect = mysql_fetch_assoc($xconnect);
					$totalRows_xconnect = mysql_num_rows($xconnect);
					
					if ($totalRows_xconnect == 0) {
						
						$insertSQL = sprintf("INSERT INTO xconnectid (xconnectpool, xconnectid, descr) VALUES (%s, %s, %s)",
						   GetSQLValueString($row_xconnectpool['id'], "int"),
						   GetSQLValueString($i, "int"),
						   GetSQLValueString($_SESSION['provide_cct'], "text"));
				
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$xconnectid = mysql_insert_id();
						
						$insertSQL = sprintf("INSERT INTO vpnxconnect (vpn, xconnect) VALUES (%s, %s)",
						   GetSQLValueString($_SESSION['provide_vpn'], "int"),
						   GetSQLValueString($xconnectid, "int"));
				
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$i = $row_xconnectpool['xconnectend'] + 1;
	
					}
					
				}
				
				}
				
			}
			
			if ($_SESSION['provide_layer'] == 2) {
					
				mysql_select_db($database_subman, $subman);
				$query_checkPort = "SELECT * FROM portsports WHERE portsports.card = ".$_SESSION['provide_card_node_a']." AND portsports.port = ".$_SESSION['provide_port_node_a']."";
				$checkPort = mysql_query($query_checkPort, $subman) or die(mysql_error());
				$row_checkPort = mysql_fetch_assoc($checkPort);
				$totalRows_checkPort = mysql_num_rows($checkPort);
				
				if ($_SESSION['provide_node_b'] != "") {
					$descr = $row_node_a['name']." --> ".$row_node_b['name']." (".$_SESSION['provide_cct'].")";
				}
				else {
					$descr = $_SESSION['provide_cct'];
				}
					
				if ($_SESSION['provide_vpn'] != "" && $_SESSION['provide_vlan_node_a'] != "" && $_SESSION['provide_logical_node_a'] != "subint") {
					
					if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_b'] != "subint")) {
						
						$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`, vlanid, vpn, vrfvc) VALUES (%s, %s, %s, %s, %s, %s, %s)",
								   GetSQLValueString($_SESSION['provide_card_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_port_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_customer'], "int"),
								   GetSQLValueString($descr, "text"),
								   GetSQLValueString($vlan_node_a, "int"),
								   GetSQLValueString($_SESSION['provide_vpn'], "int"),
								   GetSQLValueString($xconnectid, "int"));
					
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$portid_node_a = mysql_insert_id();
						
					}
					
				}
				elseif ($_SESSION['provide_vpn'] != "" && $_SESSION['provide_vlan_node_a'] == "" && $_SESSION['provide_logical_node_a'] != "subint") {
					
					if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_a'] != "subint")) {
					
						$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`, vpn, vrfvc) VALUES (%s, %s, %s, %s, %s, %s)",
								   GetSQLValueString($_SESSION['provide_card_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_port_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_customer'], "int"),
								   GetSQLValueString($descr, "text"),
								   GetSQLValueString($_SESSION['provide_vpn'], "int"),
								   GetSQLValueString($xconnectid, "int"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$portid_node_a = mysql_insert_id();
						
					}
					
				}
				elseif ($_SESSION['provide_vpn'] == "" && $_SESSION['provide_vlan_node_a'] != "" && $_SESSION['provide_logical_node_a'] != "subint") {
	
					if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_a'] != "subint")) {
						
						$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`, vlanid) VALUES (%s, %s, %s, %s, %s)",
								   GetSQLValueString($_SESSION['provide_card_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_port_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_customer'], "int"),
								   GetSQLValueString($descr, "text"),
								   GetSQLValueString($vlan_node_a, "int"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$portid_node_a = mysql_insert_id();
						
					}
					
				}
				else {
					
					if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_a'] != "subint")) {
						
						$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`) VALUES (%s, %s, %s, %s)",
								   GetSQLValueString($_SESSION['provide_card_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_port_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_customer'], "int"),
								   GetSQLValueString($descr, "text"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$portid_node_a = mysql_insert_id();
						
					}
					
				}
				
				if ($_SESSION['provide_node_b'] != "") {
					
					mysql_select_db($database_subman, $subman);
					$query_checkPort = "SELECT * FROM portsports WHERE portsports.card = ".$_SESSION['provide_card_node_b']." AND portsports.port = ".$_SESSION['provide_port_node_b']."";
					$checkPort = mysql_query($query_checkPort, $subman) or die(mysql_error());
					$row_checkPort = mysql_fetch_assoc($checkPort);
					$totalRows_checkPort = mysql_num_rows($checkPort);
				
					$descr = $row_node_b['name']." --> ".$row_node_a['name']." (".$_SESSION['provide_cct'].")";
						
					if ($_SESSION['provide_vpn'] != "" && $_SESSION['provide_vlan_node_b'] != "" && $_SESSION['provide_logical_node_b'] != "subint") {
						
					if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_b'] != "subint")) {
							
							$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`, vlanid, vpn, vrfvc) VALUES (%s, %s, %s, %s, %s, %s, %s)",
									   GetSQLValueString($_SESSION['provide_card_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_port_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_customer'], "int"),
									   GetSQLValueString($descr, "text"),
									   GetSQLValueString($vlan_node_b, "int"),
									   GetSQLValueString($_SESSION['provide_vpn'], "int"),
									   GetSQLValueString($xconnectid, "int"));
							
							mysql_select_db($database_subman, $subman);
							$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
							
							$portid_node_b = mysql_insert_id();
							
						}
						
					}
					elseif ($_SESSION['provide_vpn'] != "" && $_SESSION['provide_vlan_node_b'] == "" && $_SESSION['provide_logical_node_b'] != "subint") {
						
					if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_b'] != "subint")) {
							
							$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`, vpn, vrfvc) VALUES (%s, %s, %s, %s, %s, %s)",
									   GetSQLValueString($_SESSION['provide_card_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_port_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_customer'], "int"),
									   GetSQLValueString($descr, "text"),
									   GetSQLValueString($_SESSION['provide_vpn'], "int"),
									   GetSQLValueString($xconnectid, "int"));
							
							mysql_select_db($database_subman, $subman);
							$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
							
							$portid_node_b = mysql_insert_id();
							
						}
						
					}
					elseif ($_SESSION['provide_vpn'] == "" && $_SESSION['provide_vlan_node_b'] != "" && $_SESSION['provide_logical_node_b'] != "subint") {
						
					if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_b'] != "subint")) {
							
							$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`, vlanid) VALUES (%s, %s, %s, %s, %s)",
									   GetSQLValueString($_SESSION['provide_card_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_port_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_customer'], "int"),
									   GetSQLValueString($descr, "text"),
									   GetSQLValueString($vlan_node_b, "int"));
							
							mysql_select_db($database_subman, $subman);
							$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
							
							$portid_node_b = mysql_insert_id();
							
						}
						
					}
					else {
						
					if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_b'] != "subint")) {
							
							$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`) VALUES (%s, %s, %s, %s)",
									   GetSQLValueString($_SESSION['provide_card_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_port_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_customer'], "int"),
									   GetSQLValueString($descr, "text"));
							
							mysql_select_db($database_subman, $subman);
							$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
							
							$portid_node_b = mysql_insert_id();
							
						}
						
					}
						
				}
				
			} // provide_layer = 2
			
			if ($_SESSION['provide_layer'] == 3) {
				
				mysql_select_db($database_subman, $subman);
				$query_checkPort = "SELECT * FROM portsports WHERE portsports.card = ".$_SESSION['provide_card_node_a']." AND portsports.port = ".$_SESSION['provide_port_node_a']."";
				$checkPort = mysql_query($query_checkPort, $subman) or die(mysql_error());
				$row_checkPort = mysql_fetch_assoc($checkPort);
				$totalRows_checkPort = mysql_num_rows($checkPort);
					
				if ($_SESSION['provide_node_b'] != "") {
					$descr = $row_node_a['name']." --> ".$row_node_b['name']." (".$_SESSION['provide_cct'].")";
				}
				else {
					$descr = $_SESSION['provide_cct'];
				}
					
				if ($_SESSION['provide_vpn'] != "" && $_SESSION['provide_logical_node_a'] != "subint") {
					
					if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_a'] != "subint")) {
						
						$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`, router, vpn, vrfvc) VALUES (%s, %s, %s, %s, %s, %s, %s)",
								   GetSQLValueString($_SESSION['provide_card_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_port_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_customer'], "int"),
								   GetSQLValueString($descr, "text"),
								   GetSQLValueString($router_node_a, "int"),
								   GetSQLValueString($_SESSION['provide_vpn'], "int"),
								   GetSQLValueString($_SESSION['provide_vrf'], "int"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$portid_node_a = mysql_insert_id();
						
					}
					
				}
				elseif ($_SESSION['provide_logical_node_a'] == "subint") {
					
					if ($totalRows_checkPort == 0) {
						
						$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`) VALUES (%s, %s, %s, %s)",
								   GetSQLValueString($_SESSION['provide_card_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_port_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_customer'], "int"),
								   GetSQLValueString($descr, "text"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$portid_node_a = mysql_insert_id();
						
					}
					
				}
				else {
					
					if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_a'] != "subint")) {
						
						$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`, router) VALUES (%s, %s, %s, %s, %s)",
								   GetSQLValueString($_SESSION['provide_card_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_port_node_a'], "int"),
								   GetSQLValueString($_SESSION['provide_customer'], "int"),
								   GetSQLValueString($descr, "text"),
								   GetSQLValueString($router_node_a, "int"));
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$portid_node_a = mysql_insert_id();
						
					}
					
				}
				
				if ($_SESSION['provide_node_b'] != "") {
					
					mysql_select_db($database_subman, $subman);
					$query_checkPort = "SELECT * FROM portsports WHERE portsports.card = ".$_SESSION['provide_card_node_b']." AND portsports.port = ".$_SESSION['provide_port_node_b']."";
					$checkPort = mysql_query($query_checkPort, $subman) or die(mysql_error());
					$row_checkPort = mysql_fetch_assoc($checkPort);
					$totalRows_checkPort = mysql_num_rows($checkPort);
					
					$descr = $row_node_b['name']." --> ".$row_node_a['name']." (".$_SESSION['provide_cct'].")";
	
					if ($_SESSION['provide_vpn'] != "") {
						
					if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_b'] != "subint")) {
							
							if ($_SESSION['pece'] == 1) {
								
								$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`, router) VALUES (%s, %s, %s, %s, %s)",
									   GetSQLValueString($_SESSION['provide_card_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_port_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_customer'], "int"),
									   GetSQLValueString($descr, "text"),
									   GetSQLValueString($router_node_b, "int"));
									   
							}
							else {
								
								$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`, router, vpn, vrfvc) VALUES (%s, %s, %s, %s, %s, %s, %s)",
									   GetSQLValueString($_SESSION['provide_card_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_port_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_customer'], "int"),
									   GetSQLValueString($descr, "text"),
									   GetSQLValueString($router_node_b, "int"),
									   GetSQLValueString($_SESSION['provide_vpn'], "int"),
									   GetSQLValueString($_SESSION['provide_vrf'], "int"));
									   
							}
							
							mysql_select_db($database_subman, $subman);
							$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
							
							$portid_node_b = mysql_insert_id();
							
						}
						
					}
					elseif ($_SESSION['provide_logical_node_b'] == "subint") {
						
						if ($totalRows_checkPort == 0) {
							
							$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`) VALUES (%s, %s, %s, %s)",
									   GetSQLValueString($_SESSION['provide_card_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_port_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_customer'], "int"),
									   GetSQLValueString($descr, "text"));
							
							mysql_select_db($database_subman, $subman);
							$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
							
							$portid_node_b = mysql_insert_id();
							
						}
						
					}
					else {
						
						if ($totalRows_checkPort == 0 || ($totalRows_checkPort > 0 && $_SESSION['provide_logical_node_b'] != "subint")) {
							
							$insertSQL = sprintf("INSERT INTO portsports (card, port, customer, `usage`, router) VALUES (%s, %s, %s, %s, %s)",
									   GetSQLValueString($_SESSION['provide_card_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_port_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_customer'], "int"),
									   GetSQLValueString($descr, "text"),
									   GetSQLValueString($router_node_b, "int"));
							
							mysql_select_db($database_subman, $subman);
							$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
							
							$portid_node_b = mysql_insert_id();
							
						}
						
					}
					
				}
				
			} // provide_layer = 3
			
			if ($_SESSION['provide_subint_node_a'] != "") {
				
				if ($_SESSION['provide_node_b'] != "") {
					$descr = $row_node_a['name']." --> ".$row_node_b['name']." (".$_SESSION['provide_cct'].")";
				}
				else {
					$descr = $_SESSION['provide_cct'];
				}
				
				mysql_select_db($database_subman, $subman);
				$query_checkPort = "SELECT * FROM portsports WHERE portsports.card = ".$_SESSION['provide_card_node_a']." AND portsports.port = ".$_SESSION['provide_port_node_a']."";
				$checkPort = mysql_query($query_checkPort, $subman) or die(mysql_error());
				$row_checkPort = mysql_fetch_assoc($checkPort);
				$totalRows_checkPort = mysql_num_rows($checkPort);
				
				if ($portid_node_a == "") {
					$portid_node_a = $row_checkPort['id'];
				}
				
				if ($_SESSION['provide_subint_node_a'] == "0") {
						
					if ($vlan_node_a != "") {
						
						for ($i = 1; $i < 65536; $i++) {
							
							mysql_select_db($database_subman, $subman);
							$query_checkSubint = "SELECT * FROM subint WHERE subint.port = ".$portid_node_a." AND subint.subint = ".$i."";
							$checkSubint = mysql_query($query_checkSubint, $subman) or die(mysql_error());
							$row_checkSubint = mysql_fetch_assoc($checkSubint);
							$totalRows_checkSubint = mysql_num_rows($checkSubint);
							
							if ($totalRows_checkSubint == 0) {
								
								$_SESSION['provide_subint_node_a'] = $i;
								$i = 65536;
								
							}
							
						}
						
						mysql_select_db($database_subman, $subman);
						$query_checkSubintVlan = "SELECT * FROM subint WHERE subint.port = ".$portid_node_a." AND subint.subint = ".$vlan_node_a."";
						$checkSubintVlan = mysql_query($query_checkSubintVlan, $subman) or die(mysql_error());
						$row_checkSubintVlan = mysql_fetch_assoc($checkSubintVlan);
						$totalRows_checkSubintVlan = mysql_num_rows($checkSubintVlan);
						
						if ($totalRows_checkSubintVlan == 0) {
							
							mysql_select_db($database_subman, $subman);
							$query_vlan = "SELECT * FROM vlan WHERE vlan.id = '".$vlan_node_a."'";
							$vlan = mysql_query($query_vlan, $subman) or die(mysql_error());
							$row_vlan = mysql_fetch_assoc($vlan);
							$totalRows_vlan = mysql_num_rows($vlan);
		
							$_SESSION['provide_subint_node_a'] = $row_vlan['number'];
							
						}
						
					}
					
				}
				
				if ($totalRows_checkSubint == 0) {
					
					if ($_SESSION['provide_layer'] == 2) {
						$vrfvc = $xconnectid;
					}
					elseif ($_SESSION['provide_layer'] == 3) {
						$vrfvc = $_SESSION['provide_vrf'];
					}
					
					$insertSQL = sprintf("INSERT INTO subint (port, subint, customer, `usage`, router, vlanid, vpn, vrfvc) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
										   GetSQLValueString($portid_node_a, "int"),
										   GetSQLValueString($_SESSION['provide_subint_node_a'], "int"),
										   GetSQLValueString($_SESSION['provide_customer'], "int"),
										   GetSQLValueString($descr, "text"),
										   GetSQLValueString($router_node_a, "int"),
										   GetSQLValueString($vlan_node_a, "int"),
										   GetSQLValueString($_SESSION['provide_vpn'], "int"),
										   GetSQLValueString($vrfvc, "int"));
				
					mysql_select_db($database_subman, $subman);
					$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
					
					$subint_node_a = mysql_insert_id();
				}
			
			}
			
			if ($_SESSION['provide_node_b'] != "" ) {
				
				mysql_select_db($database_subman, $subman);
				$query_checkPort = "SELECT * FROM portsports WHERE portsports.card = ".$_SESSION['provide_card_node_b']." AND portsports.port = ".$_SESSION['provide_port_node_b']."";
				$checkPort = mysql_query($query_checkPort, $subman) or die(mysql_error());
				$row_checkPort = mysql_fetch_assoc($checkPort);
				$totalRows_checkPort = mysql_num_rows($checkPort);
				
				if ($portid_node_b == "") {
					$portid_node_b = $row_checkPort['id'];
				}
				
				if ($_SESSION['provide_subint_node_b'] != "") {
					
					$descr = $row_node_b['name']." --> ".$row_node_a['name']." (".$_SESSION['provide_cct'].")";
					
					if ($_SESSION['provide_subint_node_b'] == "0") {
						
						if ($_SESSION['provide_vlan_node_b'] == "same") {
							$_SESSION['provide_subint_node_b'] = $_SESSION['provide_subint_node_a'];
							$vlan_node_b = $vlan_node_a;
						}
							
						elseif ($vlan_node_b != "") {
							
							for ($i = 1; $i < 65536; $i++) {
								
								mysql_select_db($database_subman, $subman);
								$query_checkSubint = "SELECT * FROM subint WHERE subint.port = ".$portid_node_b." AND subint.subint = ".$i."";
								$checkSubint = mysql_query($query_checkSubint, $subman) or die(mysql_error());
								$row_checkSubint = mysql_fetch_assoc($checkSubint);
								$totalRows_checkSubint = mysql_num_rows($checkSubint);
								
								if ($totalRows_checkSubint == 0) {
									
									$_SESSION['provide_subint_node_b'] = $i;
									$i = 65536;
									
								}
								
							}

							mysql_select_db($database_subman, $subman);
							$query_checkSubintVlan = "SELECT * FROM subint WHERE subint.port = ".$portid_node_b." AND subint.subint = ".$vlan_node_b."";
							$checkSubintVlan = mysql_query($query_checkSubintVlan, $subman) or die(mysql_error());
							$row_checkSubintVlan = mysql_fetch_assoc($checkSubintVlan);
							$totalRows_checkSubintVlan = mysql_num_rows($checkSubintVlan);
							
							if ($totalRows_checkSubintVlan == 0) {
								
								mysql_select_db($database_subman, $subman);
								$query_vlan = "SELECT * FROM vlan WHERE vlan.id = '".$vlan_node_b."'";
								$vlan = mysql_query($query_vlan, $subman) or die(mysql_error());
								$row_vlan = mysql_fetch_assoc($vlan);
								$totalRows_vlan = mysql_num_rows($vlan);
	
								$_SESSION['provide_subint_node_b'] = $row_vlan['number'];
							
							}
							
						}
						
					}
					
					if ($totalRows_checkSubint == 0) {
						
						if ($_SESSION['provide_layer'] == 2) {
							$vrfvc = $xconnectid;
						}
						elseif ($_SESSION['provide_layer'] == 3) {
							$vrfvc = $_SESSION['provide_vrf'];
						}
						
						if ($_SESSION['pece'] == 1) {
							
							$insertSQL = sprintf("INSERT INTO subint (port, subint, customer, `usage`, router, vlanid) VALUES (%s, %s, %s, %s, %s, %s)",
											   GetSQLValueString($portid_node_b, "int"),
											   GetSQLValueString($_SESSION['provide_subint_node_b'], "int"),
											   GetSQLValueString($_SESSION['provide_customer'], "int"),
											   GetSQLValueString($descr, "text"),
											   GetSQLValueString($router_node_b, "int"),
											   GetSQLValueString($vlan_node_b, "int"));
											   
						}
						else {
						
							$insertSQL = sprintf("INSERT INTO subint (port, subint, customer, `usage`, router, vlanid, vpn, vrfvc) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
											   GetSQLValueString($portid_node_b, "int"),
											   GetSQLValueString($_SESSION['provide_subint_node_b'], "int"),
											   GetSQLValueString($_SESSION['provide_customer'], "int"),
											   GetSQLValueString($descr, "text"),
											   GetSQLValueString($router_node_b, "int"),
											   GetSQLValueString($vlan_node_b, "int"),
											   GetSQLValueString($_SESSION['provide_vpn'], "int"),
											   GetSQLValueString($vrfvc, "int"));
						
						}
						
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$subint_node_b = mysql_insert_id();
					}
				
				}	
				
			}
			
			if ($_SESSION['provide_timeslots_node_a'] > 0) {
				
				mysql_select_db($database_subman, $subman);
				$query_checkPort = "SELECT * FROM portsports WHERE portsports.card = ".$_SESSION['provide_card_node_a']." AND portsports.port = ".$_SESSION['provide_port_node_a']."";
				$checkPort = mysql_query($query_checkPort, $subman) or die(mysql_error());
				$row_checkPort = mysql_fetch_assoc($checkPort);
				$totalRows_checkPort = mysql_num_rows($checkPort);
				
				mysql_select_db($database_subman, $subman);
				$query_card = "SELECT * FROM cards WHERE cards.id = ".$_SESSION['provide_card_node_a']."";
				$card = mysql_query($query_card, $subman) or die(mysql_error());
				$row_card = mysql_fetch_assoc($card);
				$totalRows_card = mysql_num_rows($card);
				
				$count = 0;
				
				for ($i = 1; $i <= $row_card['timeslots']; $i++) {
					
					mysql_select_db($database_subman, $subman);
					$query_timeslot = "SELECT * FROM timeslots WHERE timeslots.card = ".$_SESSION['provide_card_node_a']." AND timeslots.port = ".$_SESSION['provide_port_node_a']." AND timeslots.timeslot = ".$i."";
					$timeslot = mysql_query($query_timeslot, $subman) or die(mysql_error());
					$row_timeslot = mysql_fetch_assoc($timeslot);
					$totalRows_timeslot = mysql_num_rows($timeslot);
					
					if ($totalRows_timeslot == 0) {
						
						$insertSQL = sprintf("INSERT INTO timeslots (card, port, timeslot, portid) VALUES (%s, %s, %s, %s)",
									   GetSQLValueString($_SESSION['provide_card_node_a'], "int"),
									   GetSQLValueString($_SESSION['provide_port_node_a'], "int"),
									   GetSQLValueString($i, "int"),
									   GetSQLValueString($portid_node_a, "int"));
							
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$count++;
						
						if ($count == $_SESSION['provide_timeslots_node_a']) {
							$i = $row_card['timeslots'] + 1;
						}
						
					}
					
				}
				
			}
			
			if ($_SESSION['provide_timeslots_node_b'] > 0) {
				
				mysql_select_db($database_subman, $subman);
				$query_checkPort = "SELECT * FROM portsports WHERE portsports.card = ".$_SESSION['provide_card_node_b']." AND portsports.port = ".$_SESSION['provide_port_node_b']."";
				$checkPort = mysql_query($query_checkPort, $subman) or die(mysql_error());
				$row_checkPort = mysql_fetch_assoc($checkPort);
				$totalRows_checkPort = mysql_num_rows($checkPort);
				
				mysql_select_db($database_subman, $subman);
				$query_card = "SELECT * FROM cards WHERE cards.id = ".$_SESSION['provide_card_node_b']."";
				$card = mysql_query($query_card, $subman) or die(mysql_error());
				$row_card = mysql_fetch_assoc($card);
				$totalRows_card = mysql_num_rows($card);
				
				$count = 0;
				
				for ($i = 1; $i <= $row_card['timeslots']; $i++) {
					
					mysql_select_db($database_subman, $subman);
					$query_timeslot = "SELECT * FROM timeslots WHERE timeslots.card = ".$_SESSION['provide_card_node_b']." AND timeslots.port = ".$_SESSION['provide_port_node_b']." AND timeslots.timeslot = ".$i."";
					$timeslot = mysql_query($query_timeslot, $subman) or die(mysql_error());
					$row_timeslot = mysql_fetch_assoc($timeslot);
					$totalRows_timeslot = mysql_num_rows($timeslot);
					
					if ($totalRows_timeslot == 0) {
						
						$insertSQL = sprintf("INSERT INTO timeslots (card, port, timeslot, portid) VALUES (%s, %s, %s, %s)",
									   GetSQLValueString($_SESSION['provide_card_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_port_node_b'], "int"),
									   GetSQLValueString($i, "int"),
									   GetSQLValueString($portid_node_b, "int"));
							
						mysql_select_db($database_subman, $subman);
						$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
						
						$count++;
						
						if ($count == $_SESSION['provide_timeslots_node_b']) {
							$i = $row_card['timeslots'] + 1;
						}
						
					}
					
				}
				
			}
			
			
			if ($_SESSION['provide_existing'] == 1) {
				$network = $_SESSION['provide_parent'];
			}
			
			if ($_SESSION['provide_template'] == "") {
				$_SESSION['provide_template'] = 0;
			}
			
			$insertSQL = sprintf("INSERT INTO links (provide_node_a, provide_node_b, provide_layer, provide_logical_node_a, provide_logical_node_b, provide_cct, provide_customer, provide_vpn, provide_vlan_node_a, provide_vlan_node_b, provide_xconnect, provide_vrf, provide_pece, provide_network, provide_card_node_a, provide_card_node_b, provide_subint_node_a, provide_subint_node_b, provide_port_node_a, provide_port_node_b, container, servicetemplate, linked) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
									   GetSQLValueString($_SESSION['provide_node_a'], "int"),
									   GetSQLValueString($_SESSION['provide_node_b'], "int"),
									   GetSQLValueString($_SESSION['provide_layer'], "int"),
									   GetSQLValueString($_SESSION['provide_logical_node_a'], "text"),
									   GetSQLValueString($_SESSION['provide_logical_node_b'], "text"),
									   GetSQLValueString($_SESSION['provide_cct'], "text"),
									   GetSQLValueString($_SESSION['provide_customer'], "int"),
									   GetSQLValueString($_SESSION['provide_vpn'], "int"),
									   GetSQLValueString($vlan_node_a, "int"),
									   GetSQLValueString($vlan_node_b, "int"),
									   GetSQLValueString($xconnectid, "int"),
									   GetSQLValueString($_SESSION['provide_vrf'], "int"),
									   GetSQLValueString($_SESSION['pece'], "int"),
									   GetSQLValueString($network, "int"),
									   GetSQLValueString($_SESSION['provide_card_node_a'], "int"),
									   GetSQLValueString($_SESSION['provide_card_node_b'], "int"),
									   GetSQLValueString($subint_node_a, "int"),
									   GetSQLValueString($subint_node_b, "int"),
									   GetSQLValueString($portid_node_a, "int"),
									   GetSQLValueString($portid_node_b, "int"),
									   GetSQLValueString($_GET['container'], "int"),
									   GetSQLValueString($_SESSION['provide_template'], "int"),
									   GetSQLValueString($_SESSION['linked'], "int"));
							
			mysql_select_db($database_subman, $subman);
			$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
			
			$new_link_id = mysql_insert_id();
			
			if ($_SESSION['linked']) {
				
				$updateSQL = sprintf("update links SET linkedfrom=%s WHERE id = %s",
									   GetSQLValueString($new_link_id, "int"),
									   GetSQLValueString($_SESSION['linked'], "int"));
				
				mysql_select_db($database_subman, $subman);
			$Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
				
			}
		
		}
		
	}
	
}


if (isset($_POST['change_customer_link']) && $_POST['change_customer_link'] != "") {
	
	mysql_select_db($database_subman, $subman);
	$query_link = "SELECT links.* FROM links WHERE links.id = ".$_POST['change_customer_link']."";
	$link = mysql_query($query_link, $subman) or die(mysql_error());
	$row_link = mysql_fetch_assoc($link);
	$totalRows_link = mysql_num_rows($link);
	
	mysql_select_db($database_subman, $subman);
	$query_linknetworks = "SELECT linknetworks.* FROM linknetworks WHERE linknetworks.link = ".$_POST['change_customer_link']."";
	$linknetworks = mysql_query($query_linknetworks, $subman) or die(mysql_error());
	$row_linknetworks = mysql_fetch_assoc($linknetworks);
	$totalRows_linknetworks = mysql_num_rows($linknetworks);

	mysql_select_db($database_subman, $subman);
	$query_getCustomer = "SELECT customer.* FROM customer WHERE customer.id = '".$_POST['customer']."'";
	$getCustomer = mysql_query($query_getCustomer, $subman) or die(mysql_error());
	$row_getCustomer = mysql_fetch_assoc($getCustomer);
	$totalRows_getCustomer = mysql_num_rows($getCustomer);
	
	$updateSQL = sprintf("UPDATE links SET provide_customer=%s WHERE id=%s",
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($_POST['change_customer_link'], "int"));
	

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  $change_customer = $row_getCustomer['name'].": ".$row_link['provide_cct'];
  
  $updateSQL = sprintf("UPDATE networks SET descr=%s WHERE id=%s",
                       GetSQLValueString($change_customer, "text"),
                       GetSQLValueString($row_link['provide_network'], "int"));
	

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  $updateSQL = sprintf("UPDATE addresses SET customer=%s WHERE network=%s AND customer=%s",
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($row_link['provide_network'], "int"),
					   GetSQLValueString($_POST['old_customer_id'], "int"));
	

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  if ($totalRows_linknetworks > 0) {
  
  	do { 
  	
  		$updateSQL = sprintf("UPDATE networks SET descr=%s WHERE id=%s",
                       GetSQLValueString($change_customer, "text"),
                       GetSQLValueString($row_linknetworks['network'], "int"));
	

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  		$updateSQL = sprintf("UPDATE addresses SET customer=%s WHERE network=%s AND customer=%s",
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($row_linknetworks['network'], "int"),
					   GetSQLValueString($_POST['old_customer_id'], "int"));
	

  		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  	} while ($row_linknetworks = mysql_fetch_assoc($linknetworks));
  	
  }
  
  if ($row_link['provide_vlan_node_a'] != "") {
	  
	mysql_select_db($database_subman, $subman);
	$query_checkLinks = "SELECT links.* FROM links WHERE links.provide_vlan_node_a = '".$row_link['provide_vlan_node_a']."' AND links.provide_customer = '".$_POST['customer']."'";
	$checkLinks = mysql_query($query_checkLinks, $subman) or die(mysql_error());
	$row_checkLinks = mysql_fetch_assoc($checkLinks);
	$totalRows_checkLinks = mysql_num_rows($checkLinks);
	
	if ($totalRows_checkLinks == 1) {
		
		$updateSQL = sprintf("UPDATE vlan SET customer=%s WHERE id=%s",
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($row_link['provide_vlan_node_a'], "int"));
	

		  mysql_select_db($database_subman, $subman);
		  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
		
	}
	
  }
  
  if ($row_link['provide_vlan_node_b'] != "") {
	  
	mysql_select_db($database_subman, $subman);
	$query_checkLinks = "SELECT links.* FROM links WHERE links.provide_vlan_node_b = '".$row_link['provide_vlan_node_b']."' AND links.provide_customer = '".$_POST['customer']."'";
	$checkLinks = mysql_query($query_checkLinks, $subman) or die(mysql_error());
	$row_checkLinks = mysql_fetch_assoc($checkLinks);
	$totalRows_checkLinks = mysql_num_rows($checkLinks);
	
	if ($totalRows_checkLinks == 1) {
		
		$updateSQL = sprintf("UPDATE vlan SET customer=%s WHERE id=%s",
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($row_link['provide_vlan_node_b'], "int"));
	

		  mysql_select_db($database_subman, $subman);
		  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
		
	}
	
  }
  
  if ($row_link['provide_vpn'] != "") {
	  
	mysql_select_db($database_subman, $subman);
	$query_checkLinks = "SELECT links.* FROM links WHERE links.provide_vpn = '".$row_link['provide_vpn']."' AND links.provide_customer = '".$_POST['old_customer_id']."'";
	$checkLinks = mysql_query($query_checkLinks, $subman) or die(mysql_error());
	$row_checkLinks = mysql_fetch_assoc($checkLinks);
	$totalRows_checkLinks = mysql_num_rows($checkLinks);
	
	if ($totalRows_checkLinks == 0) {
		
		$deleteSQL = sprintf("DELETE FROM vpncustomer WHERE customer=%s AND vpn=%s",
                       GetSQLValueString($_POST['old_customer_id'], "int"),
                       GetSQLValueString($row_link['provide_vpn'], "int"));
	

  		mysql_select_db($database_subman, $subman);
		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}
	
	mysql_select_db($database_subman, $subman);
	$query_checkVPNCustomers = "SELECT * FROM vpncustomer WHERE vpncustomer.vpn = '".$row_link['provide_vpn']."' AND vpncustomer.customer = '".$_POST['customer']."'";
	$checkVPNCustomers = mysql_query($query_checkVPNCustomers, $subman) or die(mysql_error());
	$row_checkVPNCustomers = mysql_fetch_assoc($checkVPNCustomers);
	$totalRows_checkVPNCustomers = mysql_num_rows($checkVPNCustomers);
	
	if ($totalRows_checkVPNCustomers == 0) {
		
		$insertSQL = sprintf("INSERT INTO vpncustomer (vpn, customer) VALUES (%s, %s)",
						   GetSQLValueString($row_link['provide_vpn'], "int"),
						   GetSQLValueString($_POST['customer'], "int"));
		
	
		mysql_select_db($database_subman, $subman);
		$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());	

	}
	
  }
  
  $updateSQL = sprintf("UPDATE portsports SET customer=%s WHERE id=%s",
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($row_link['provide_port_node_a'], "int"));
	

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  if ($row_link['provide_port_node_b'] != "") {
	  
	  $updateSQL = sprintf("UPDATE portsports SET customer=%s WHERE id=%s",
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($row_link['provide_port_node_b'], "int"));
		
	
	  mysql_select_db($database_subman, $subman);
	  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  }
  
  if ($row_link['provide_subint_node_a'] != "") {
  
	  $updateSQL = sprintf("UPDATE subint SET customer=%s WHERE id=%s",
						   GetSQLValueString($_POST['customer'], "int"),
						   GetSQLValueString($row_link['provide_subint_node_a'], "int"));
		
	
	  mysql_select_db($database_subman, $subman);
	  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	  
  }
  
  if ($row_link['provide_subint_node_b'] != "") {
	  
	  $updateSQL = sprintf("UPDATE subint SET customer=%s WHERE id=%s",
						   GetSQLValueString($_POST['customer'], "int"),
						   GetSQLValueString($row_link['provide_subint_node_b'], "int"));
		
	
	  mysql_select_db($database_subman, $subman);
	  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	  
  }
  
  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
	
}

if (isset($_POST['change_cct_link']) && $_POST['change_cct_link'] != "") {
	
	mysql_select_db($database_subman, $subman);
	$query_link = "SELECT links.*, customer.name as customername FROM links LEFT JOIN customer ON customer.id = links.provide_customer WHERE links.id = ".$_POST['change_cct_link']."";
	$link = mysql_query($query_link, $subman) or die(mysql_error());
	$row_link = mysql_fetch_assoc($link);
	$totalRows_link = mysql_num_rows($link);
	
	mysql_select_db($database_subman, $subman);
	$query_checkLinks = "SELECT * FROM links WHERE links.provide_network = '".$row_link['provide_network']."'";
	$checkLinks = mysql_query($query_checkLinks, $subman) or die(mysql_error());
	$row_checkLinks = mysql_fetch_assoc($checkLinks);
	$totalRows_checkLinks = mysql_num_rows($checkLinks);
	
	$updateSQL = sprintf("UPDATE links SET provide_cct=%s WHERE id=%s",
                       GetSQLValueString($_POST['change_cct'], "text"),
                       GetSQLValueString($_POST['change_cct_link'], "int"));
	

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  $change_cct_ref = $row_link['customername'].": ".$_POST['change_cct'];
  
  if ($totalRows_checkLinks == 1) {
  
  $updateSQL = sprintf("UPDATE networks SET descr=%s WHERE id=%s",
                       GetSQLValueString($change_cct_ref, "text"),
                       GetSQLValueString($row_link['provide_network'], "int"));
	

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  }
  
  if ($row_link['provide_vpn'] != "" && $row_link['provide_layer'] == 2) {
	  
	  $updateSQL = sprintf("UPDATE vpn SET name=%s WHERE id=%s",
						   GetSQLValueString($_POST['change_cct'], "text"),
						   GetSQLValueString($row_link['provide_vpn'], "int"));
		
	
	  mysql_select_db($database_subman, $subman);
	  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  }
  
  if ($row_link['provide_xconnect'] != "") {
	  
	  $updateSQL = sprintf("UPDATE xconnectid SET descr=%s WHERE id=%s",
						   GetSQLValueString($_POST['change_cct'], "text"),
						   GetSQLValueString($row_link['provide_xconnect'], "int"));
		
	
	  mysql_select_db($database_subman, $subman);
	  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  }
  
	mysql_select_db($database_subman, $subman);
	$query_port_node_a = "SELECT portsports.*, addresses.descr as addressdescr, addresses.id as addressid FROM portsports LEFT JOIN addresses ON addresses.id = portsports.router WHERE portsports.id = '".$row_link['provide_port_node_a']."'";
	$port_node_a = mysql_query($query_port_node_a, $subman) or die(mysql_error());
	$row_port_node_a = mysql_fetch_assoc($port_node_a);
	$totalRows_port_node_a = mysql_num_rows($port_node_a);
	
	mysql_select_db($database_subman, $subman);
	$query_port_node_b = "SELECT portsports.*, addresses.descr as addressdescr, addresses.id as addressid FROM portsports LEFT JOIN addresses ON addresses.id = portsports.router WHERE portsports.id = '".$row_link['provide_port_node_b']."'";
	$port_node_b = mysql_query($query_port_node_b, $subman) or die(mysql_error());
	$row_port_node_b = mysql_fetch_assoc($port_node_b);
	$totalRows_port_node_b = mysql_num_rows($port_node_b);
	
	mysql_select_db($database_subman, $subman);
	$query_subint_node_a = "SELECT subint.*, addresses.descr as addressdescr, addresses.id as addressid FROM subint LEFT JOIN addresses ON addresses.id = subint.router WHERE subint.id = '".$row_link['provide_subint_node_a']."'";
	$subint_node_a = mysql_query($query_subint_node_a, $subman) or die(mysql_error());
	$row_subint_node_a = mysql_fetch_assoc($subint_node_a);
	$totalRows_subint_node_a = mysql_num_rows($subint_node_a);
	
	mysql_select_db($database_subman, $subman);
	$query_subint_node_b = "SELECT subint.*, addresses.descr as addressdescr, addresses.id as addressid FROM subint LEFT JOIN addresses ON addresses.id = subint.router WHERE subint.id = '".$row_link['provide_subint_node_b']."'";
	$subint_node_b = mysql_query($query_subint_node_b, $subman) or die(mysql_error());
	$row_subint_node_b = mysql_fetch_assoc($subint_node_b);
	$totalRows_subint_node_b = mysql_num_rows($subint_node_b);
	
	
	mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  $change_cct_addr_ref = str_replace($_POST['old_cct'],$_POST['change_cct'],$row_port_node_a['addressdescr']);
  
  $updateSQL = sprintf("UPDATE addresses SET descr=%s WHERE id=%s",
                       GetSQLValueString($change_cct_addr_ref, "text"),
                       GetSQLValueString($row_port_node_a['addressid'], "int"));
	

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  $change_cct_port_ref = str_replace($_POST['old_cct'],$_POST['change_cct'],$row_port_node_a['usage']);
  
  $updateSQL = sprintf("UPDATE portsports SET `usage`=%s WHERE id=%s",
                       GetSQLValueString($change_cct_port_ref, "text"),
                       GetSQLValueString($row_port_node_a['id'], "int"));
	

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  if ($row_link['provide_port_node_b'] != "") {
	  
	  $change_cct_addr_ref = str_replace($_POST['old_cct'],$_POST['change_cct'],$row_port_node_b['addressdescr']);
	  
	  $updateSQL = sprintf("UPDATE addresses SET descr=%s WHERE id=%s",
						   GetSQLValueString($change_cct_addr_ref, "text"),
						   GetSQLValueString($row_port_node_b['addressid'], "int"));
		
	
	  mysql_select_db($database_subman, $subman);
	  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	  
	  $change_cct_port_ref = str_replace($_POST['old_cct'],$_POST['change_cct'],$row_port_node_b['usage']);
  
	  $updateSQL = sprintf("UPDATE portsports SET `usage`=%s WHERE id=%s",
						   GetSQLValueString($change_cct_port_ref, "text"),
						   GetSQLValueString($row_port_node_b['id'], "int"));
		
	
	  mysql_select_db($database_subman, $subman);
	  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
  
  }
  
  if ($row_link['provide_subint_node_a'] != "") {
	  
	  $change_cct_addr_ref = str_replace($_POST['old_cct'],$_POST['change_cct'],$row_subint_node_a['addressdescr']);
	  
	  $updateSQL = sprintf("UPDATE addresses SET descr=%s WHERE id=%s",
						   GetSQLValueString($change_cct_addr_ref, "text"),
						   GetSQLValueString($row_subint_node_a['addressid'], "int"));
		
	
	  mysql_select_db($database_subman, $subman);
	  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	  
	  $change_cct_port_ref = str_replace($_POST['old_cct'],$_POST['change_cct'],$row_subint_node_a['usage']);
  
	  $updateSQL = sprintf("UPDATE subint SET `usage`=%s WHERE id=%s",
						   GetSQLValueString($change_cct_port_ref, "text"),
						   GetSQLValueString($row_subint_node_a['id'], "int"));
		
	
	  mysql_select_db($database_subman, $subman);
	  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	  
  }
  
  if ($row_link['provide_subint_node_b'] != "") {
	  
	  $change_cct_addr_ref = str_replace($_POST['old_cct'],$_POST['change_cct'],$row_subint_node_b['addressdescr']);
	  
	  $updateSQL = sprintf("UPDATE addresses SET descr=%s WHERE id=%s",
						   GetSQLValueString($change_cct_addr_ref, "text"),
						   GetSQLValueString($row_subint_node_b['addressid'], "int"));
		
	
	  mysql_select_db($database_subman, $subman);
	  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	  
	  $change_cct_port_ref = str_replace($_POST['old_cct'],$_POST['change_cct'],$row_subint_node_b['usage']);
  
	  $updateSQL = sprintf("UPDATE subint SET `usage`=%s WHERE id=%s",
						   GetSQLValueString($change_cct_port_ref, "text"),
						   GetSQLValueString($row_subint_node_b['id'], "int"));
		
	
	  mysql_select_db($database_subman, $subman);
	  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	  
  }
  
  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
	
}

if (isset($_POST['add_route']) && $_POST['add_route'] != "") {
	
	
	mysql_select_db($database_subman, $subman);
	$query_checkLinknets = "SELECT * FROM linknets WHERE linknets.network = ".$_POST['network']." AND linknets.link = ".$_POST['add_route']."";
	$checkLinknets = mysql_query($query_checkLinknets, $subman) or die(mysql_error());
	$row_checkLinknets = mysql_fetch_assoc($checkLinknets);
	$totalRows_checkLinknets = mysql_num_rows($checkLinknets);
	
	if ($totalRows_checkLinknets > 0) {
		
		$insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&device=".$_POST['device']."&group=".$_POST['devicegroup']."&port=".$_POST['port']."&linkview=1&errstr=".$errstrflag;
		
			header(sprintf("Location: %s", $insertGoTo));
			
	}
	else {
	
	mysql_select_db($database_subman, $subman);
	$query_network = "SELECT * FROM networks WHERE networks.id = ".$_POST['network']."";
	$network = mysql_query($query_network, $subman) or die(mysql_error());
	$row_network = mysql_fetch_assoc($network);
	$totalRows_network = mysql_num_rows($network);
	
	if ($row_network['v6mask'] == "" && !(Net_IPv4::validateIP($_POST['nexthop']))) {
		
		$_SESSION['errstr'] .= "The next hop you entered is invalid.  Please try again.";
		$errstrflag = 1;
			
	}
	elseif ($row_network['v6mask'] != "" && !(Net_IPv6::checkIPv6($_POST['nexthop']))) {
			
			$_SESSION['errstr'] .= "The next hop you entered is invalid.  Please try again.";
			$errstrflag = 1;
			
			$_POST['nexthop'] = Net_IPv6::Compress($_POST['nexthop']);
			
	}
	
	else {
		
		mysql_select_db($database_subman, $subman);
		$query_link = "SELECT links.*, servicetemplate.routes, servicetemplate.id AS servicetemplateid FROM links LEFT JOIN servicetemplate ON servicetemplate.id = links.servicetemplate WHERE links.id = ".$_POST['add_route']."";
		$link = mysql_query($query_link, $subman) or die(mysql_error());
		$row_link = mysql_fetch_assoc($link);
		$totalRows_link = mysql_num_rows($link);
		
		mysql_select_db($database_subman, $subman);
		$query_provide_scripts = "SELECT * FROM scripts WHERE scripts.servicetemplate = '".$row_link['servicetemplateid']."' AND scripts.scriptrole = 'routesdeploy'";
		$provide_scripts = mysql_query($query_provide_scripts, $subman) or die(mysql_error());
		$row_provide_scripts = mysql_fetch_assoc($provide_scripts);
		$totalRows_provide_scripts = mysql_num_rows($provide_scripts);
		
		$insertSQL = sprintf("INSERT INTO linknets (link, network, nexthop) VALUES (%s, %s, %s)",
					   GetSQLValueString($_POST['add_route'], "int"),
                       GetSQLValueString($_POST['network'], "int"),
					   GetSQLValueString($_POST['nexthop'], "text"));

		  mysql_select_db($database_subman, $subman);
		  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
			
			$linknetid = mysql_insert_id();
			
		if ($row_link['routes'] == "" && $totalRows_provide_scripts == 0) {
			
		  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&device=".$_POST['device']."&group=".$_POST['devicegroup']."&port=".$_POST['port']."&linkview=1&errstr=".$errstrflag;
		
			header(sprintf("Location: %s", $insertGoTo));
			
		}
		else {
			
			$routesconfig = $row_link['routes'];
			
		}
		
	}
	
	}
	
}

if (isset($_POST['recover_link']) && $_POST['recover_link'] != "") {
	
	mysql_select_db($database_subman, $subman);
	$query_link = "SELECT links.*, networks.mask, networks.v6mask, customer.name AS customername, vpn.name AS vpnname, vrf.name AS vrfname, xconnectid.xconnectid FROM links LEFT JOIN networks ON networks.id = links.provide_network LEFT JOIN customer ON customer.id = links.provide_customer LEFT JOIN vpn ON vpn.id = links.provide_vpn LEFT JOIN vrf ON vrf.id = links.provide_vrf LEFT JOIN xconnectid ON xconnectid.id = links.provide_xconnect WHERE links.id = ".$_POST['recover_link']."";
	$link = mysql_query($query_link, $subman) or die(mysql_error());
	$row_link = mysql_fetch_assoc($link);
	$totalRows_link = mysql_num_rows($link);
	
	mysql_select_db($database_subman, $subman);
	$query_routes = "SELECT linknets.* FROM linknets WHERE linknets.link = '".$row_link['id']."'";
	$routes = mysql_query($query_routes, $subman) or die(mysql_error());
	$row_routes = mysql_fetch_assoc($routes);
	$totalRows_routes = mysql_num_rows($routes);
	
	mysql_select_db($database_subman, $subman);
	$query_secondary = "SELECT linknetworks.* FROM linknetworks WHERE linknetworks.link = '".$row_link['id']."'";
	$secondary = mysql_query($query_secondary, $subman) or die(mysql_error());
	$row_secondary = mysql_fetch_assoc($secondary);
	$totalRows_secondary = mysql_num_rows($secondary);
	
	mysql_select_db($database_subman, $subman);
	$query_linked = "SELECT links.* FROM links WHERE links.id = '".$row_link['id']."'";
	$linked = mysql_query($query_linked, $subman) or die(mysql_error());
	$row_linked = mysql_fetch_assoc($linked);
	$totalRows_linked = mysql_num_rows($linked);
	
	if ($totalRows_routes > 0) {
		
		if ($totalRows_linked  > 0) {
			
			$updateSQL = sprintf("UPDATE links SET linkedfrom = NULL WHERE links.id=%s",
					   GetSQLValueString($row_link['id'], "int"));

		  mysql_select_db($database_subman, $subman);
		  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
		  
		}
		
        $_SESSION['errstr'] .= "The link cannot be deleted as there are networks/routes attached.  Please delete the routes first.";
		$errstrflag = 1;
            
 		$insertGoTo = "?".$_SERVER['QUERY_STRING']."&errstr=".$errstrflag;
		
		header(sprintf("Location: %s", $insertGoTo));

	}
	elseif ($totalRows_secondary > 0) {
		
		if ($totalRows_linked  > 0) {
			
			$updateSQL = sprintf("UPDATE links SET linkedfrom = NULL WHERE links.id=%s",
					   GetSQLValueString($row_link['id'], "int"));

		  mysql_select_db($database_subman, $subman);
		  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
		  
		}
		
        $_SESSION['errstr'] .= "The link cannot be deleted as there are secondary addresses/networks attached.  Please remove these networks first.";
		$errstrflag = 1;
            
 		$insertGoTo = "?".$_SERVER['QUERY_STRING']."&errstr=".$errstrflag;
		
		header(sprintf("Location: %s", $insertGoTo));

	}
	else {
		
		mysql_select_db($database_subman, $subman);
		$query_port_node_a = "SELECT portsports.*, portsdevices.managementip AS loopback FROM portsports LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN portsdevices ON portsdevices.id = cards.device WHERE portsports.id = '".$row_link['provide_port_node_a']."'";
		$port_node_a = mysql_query($query_port_node_a, $subman) or die(mysql_error());
		$row_port_node_a = mysql_fetch_assoc($port_node_a);
		$totalRows_port_node_a = mysql_num_rows($port_node_a);
		
		mysql_select_db($database_subman, $subman);
		$query_port_node_b = "SELECT portsports.*, portsdevices.managementip AS loopback FROM portsports LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN portsdevices ON portsdevices.id = cards.device WHERE portsports.id = '".$row_link['provide_port_node_b']."'";
		$port_node_b = mysql_query($query_port_node_b, $subman) or die(mysql_error());
		$row_port_node_b = mysql_fetch_assoc($port_node_b);
		$totalRows_port_node_b = mysql_num_rows($port_node_b);
		
		mysql_select_db($database_subman, $subman);
		$query_subint_node_a = "SELECT subint.* FROM subint WHERE subint.port = '".$row_link['provide_port_node_a']."' AND subint.id = '".$row_link['provide_subint_node_a']."'";
		$subint_node_a = mysql_query($query_subint_node_a, $subman) or die(mysql_error());
		$row_subint_node_a = mysql_fetch_assoc($subint_node_a);
		$totalRows_subint_node_a = mysql_num_rows($subint_node_a);
		
		mysql_select_db($database_subman, $subman);
		$query_subint_node_b = "SELECT subint.* FROM subint WHERE subint.port = '".$row_link['provide_port_node_b']."' AND subint.id = '".$row_link['provide_subint_node_b']."'";
		$subint_node_b = mysql_query($query_subint_node_b, $subman) or die(mysql_error());
		$row_subint_node_b = mysql_fetch_assoc($subint_node_b);
		$totalRows_subint_node_b = mysql_num_rows($subint_node_b);
		
		$recovery_linked = $row_link['linked'];
		$recovery_linkedfrom = $row_link['linkedfrom'];
		$recovery_node_a = $row_link['provide_node_a'];
		$recovery_node_b = $row_link['provide_node_b'];
		$recovery_layer = $row_link['provide_layer'];
		$recovery_logical_node_a = $row_link['provide_logical_node_a'];
		$recovery_logical_node_b = $row_link['provide_logical_node_b'];
		$recovery_cct = $row_link['provide_cct'];
		$recovery_customer = $row_link['customername'];
		$recovery_vpn = $row_link['vpnname'];
		$recovery_loopback_node_a = $row_port_node_a['loopback'];
		$recovery_loopback_node_b = $row_port_node_b['loopback'];
		
		mysql_select_db($database_subman, $subman);
		$query_vlan_node_a = "SELECT * FROM vlan WHERE vlan.id = '".$row_link['provide_vlan_node_a']."'";
		$vlan_node_a = mysql_query($query_vlan_node_a, $subman) or die(mysql_error());
		$row_vlan_node_a = mysql_fetch_assoc($vlan_node_a);
		$totalRows_vlan_node_a = mysql_num_rows($vlan_node_a);
		
		mysql_select_db($database_subman, $subman);
		$query_vlan_node_b = "SELECT * FROM vlan WHERE vlan.id = '".$row_link['provide_vlan_node_b']."'";
		$vlan_node_b = mysql_query($query_vlan_node_b, $subman) or die(mysql_error());
		$row_vlan_node_b = mysql_fetch_assoc($vlan_node_b);
		$totalRows_vlan_node_b = mysql_num_rows($vlan_node_b);
		
		$recovery_vlan_node_a = $row_vlan_node_a['number'];
		$recovery_vlan_node_b = $row_vlan_node_b['number'];
		$recovery_xconnect = $row_link['xconnectid'];
		$recovery_vrf = $row_link['vrfname'];
		$recovery_network = $row_link['provide_network'];
		$recovery_mask = $row_link['mask'];
		$recovery_v6_mask = $row_link['v6mask'];
		$recovery_card_node_a = $row_link['provide_card_node_a'];
		$recovery_card_node_b = $row_link['provide_card_node_b'];
		$recovery_timeslots_node_a = $row_link['provide_timeslots_node_a'];
		$recovery_timeslots_node_b = $row_link['provide_timeslots_node_b'];
		$recovery_subint_node_a = $row_subint_node_a['subint'];
		$recovery_subint_node_b = $row_subint_node_b['subint'];
		$recovery_port_node_a = $row_port_node_a['port'];
		$recovery_port_node_b = $row_port_node_b['port'];
		$recovery_template = $row_link['servicetemplate'];

		mysql_select_db($database_subman, $subman);
		$query_timeslotRange_node_a = "SELECT min(timeslot) as firstslot, max(timeslot) as lastslot FROM timeslots WHERE timeslots.portid = '".$row_port_node_a['id']."'";
		$timeslotRange_node_a = mysql_query($query_timeslotRange_node_a, $subman) or die(mysql_error());
		$row_timeslotRange_node_a = mysql_fetch_assoc($timeslotRange_node_a);
		$totalRows_timeslotRange_node_a = mysql_num_rows($timeslotRange_node_a);
	
		mysql_select_db($database_subman, $subman);
		$query_timeslotRange_node_b = "SELECT min(timeslot) as firstslot, max(timeslot) as lastslot FROM timeslots WHERE timeslots.portid = '".$row_port_node_b['id']."'";
		$timeslotRange_node_b = mysql_query($query_timeslotRange_node_b, $subman) or die(mysql_error());
		$row_timeslotRange_node_b = mysql_fetch_assoc($timeslotRange_node_b);
		$totalRows_timeslotRange_node_b = mysql_num_rows($timeslotRange_node_b);
		
		$recovery_first_timeslot_node_a = $row_timeslotRange_node_a['firstslot'];
		$recovery_last_timeslot_node_a = $row_timeslotRange_node_a['lastslot'];
		
		$recovery_first_timeslot_node_b = $row_timeslotRange_node_b['firstslot'];
		$recovery_last_timeslot_node_b = $row_timeslotRange_node_b['lastslot'];
		
		if ($recovery_subint_node_a != "") {
			
			mysql_select_db($database_subman, $subman);
			$query_router_node_a = "SELECT subint.router, addresses.address, addresses.id AS addressID FROM subint LEFT JOIN addresses ON addresses.id = subint.router WHERE subint.id = '".$row_subint_node_a['id']."'";
			$router_node_a = mysql_query($query_router_node_a, $subman) or die(mysql_error());
			$row_router_node_a = mysql_fetch_assoc($router_node_a);
			$totalRows_router_node_a = mysql_num_rows($router_node_a);
			$recovery_address_node_a = $row_router_node_a['address'];
			$recovery_address_node_a_id = $row_router_node_a['router'];
			
		}
		else {
			
			mysql_select_db($database_subman, $subman);
			$query_router_node_a = "SELECT portsports.router, addresses.address, addresses.id AS addressID FROM portsports LEFT JOIN addresses ON addresses.id = portsports.router WHERE portsports.id = '".$row_link['provide_port_node_a']."'";
			$router_node_a = mysql_query($query_router_node_a, $subman) or die(mysql_error());
			$row_router_node_a = mysql_fetch_assoc($router_node_a);
			$totalRows_router_node_a = mysql_num_rows($router_node_a);
			$recovery_address_node_a = $row_router_node_a['address'];
			$recovery_address_node_a_id = $row_router_node_a['router'];
			
		}
		
		if ($recovery_subint_node_b != "") {
			
			mysql_select_db($database_subman, $subman);
			$query_router_node_b = "SELECT subint.router, addresses.address, addresses.id AS addressID FROM subint LEFT JOIN addresses ON addresses.id = subint.router WHERE subint.id = '".$row_subint_node_b['id']."'";
			$router_node_b = mysql_query($query_router_node_b, $subman) or die(mysql_error());
			$row_router_node_b = mysql_fetch_assoc($router_node_b);
			$totalRows_router_node_b = mysql_num_rows($router_node_b);
			$recovery_address_node_b = $row_router_node_b['address'];
			$recovery_address_node_b_id = $row_router_node_b['router'];

		}
		else {
			
			mysql_select_db($database_subman, $subman);
			$query_router_node_b = "SELECT portsports.router, addresses.address, addresses.id AS addressID FROM portsports LEFT JOIN addresses ON addresses.id = portsports.router WHERE portsports.id = '".$row_link['provide_port_node_b']."'";
			$router_node_b = mysql_query($query_router_node_b, $subman) or die(mysql_error());
			$row_router_node_b = mysql_fetch_assoc($router_node_b);
			$totalRows_router_node_b = mysql_num_rows($router_node_b);
			$recovery_address_node_b = $row_router_node_b['address'];
			$recovery_address_node_b_id = $row_router_node_b['router'];
		}
		
		
		mysql_select_db($database_subman, $subman);
		$query_subints_node_a = "SELECT * FROM subint WHERE subint.port = ".$row_link['provide_port_node_a']." AND subint.id != '".$row_link['provide_subint_node_a']."'";
		$subints_node_a = mysql_query($query_subints_node_a, $subman) or die(mysql_error());
		$row_subints_node_a = mysql_fetch_assoc($subints_node_a);
		$totalRows_subints_node_a = mysql_num_rows($subints_node_a);
		
		if ($totalRows_subints_node_a == 0) {
			$deleteSQL = sprintf("DELETE FROM portsports WHERE portsports.id=%s",
						   GetSQLValueString($row_link['provide_port_node_a'], "int"));
	
			mysql_select_db($database_subman, $subman);
			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
		}
		
		if ($row_link['provide_subint_node_a'] != "") {
			
			$deleteSQL = sprintf("DELETE FROM subint WHERE subint.id=%s",
							   GetSQLValueString($row_link['provide_subint_node_a'], "int"));
		
			mysql_select_db($database_subman, $subman);
			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
		}
		
		$deleteSQL = sprintf("DELETE FROM timeslots WHERE timeslots.portid=%s",
						   GetSQLValueString($row_link['provide_port_node_a'], "int"));
		
		mysql_select_db($database_subman, $subman);
		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
		if ($row_link['provide_port_node_b'] != "") {
			
			mysql_select_db($database_subman, $subman);
			$query_subints_node_b = "SELECT * FROM subint WHERE subint.port = ".$row_link['provide_port_node_b']." AND subint.id != '".$row_link['provide_subint_node_b']."'";
			$subints_node_b = mysql_query($query_subints_node_b, $subman) or die(mysql_error());
			$row_subints_node_b = mysql_fetch_assoc($subints_node_b);
			$totalRows_subints_node_b = mysql_num_rows($subints_node_b);
			
			if ($totalRows_subints_node_b == 0) {
				$deleteSQL = sprintf("DELETE FROM portsports WHERE portsports.id=%s",
							   GetSQLValueString($row_link['provide_port_node_b'], "int"));
		
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
			}
			
			if ($row_link['provide_subint_node_b'] != "") {
				
				$deleteSQL = sprintf("DELETE FROM subint WHERE subint.id=%s",
								   GetSQLValueString($row_link['provide_subint_node_b'], "int"));
			
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
			}
			
			$deleteSQL = sprintf("DELETE FROM timeslots WHERE timeslots.portid=%s",
							   GetSQLValueString($row_link['provide_port_node_b'], "int"));
		
			mysql_select_db($database_subman, $subman);
			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
		}
		
		if ($row_link['provide_network'] != "" && $_POST['retain_network'] != "1") {
			
			mysql_select_db($database_subman, $subman);
			$query_checkNetwork = "SELECT * FROM links WHERE links.provide_network = ".$row_link['provide_network']." AND links.id != ".$row_link['id']."";
			$checkNetwork = mysql_query($query_checkNetwork, $subman) or die(mysql_error());
			$row_checkNetwork = mysql_fetch_assoc($checkNetwork);
			$totalRows_checkNetwork = mysql_num_rows($checkNetwork);
			
			mysql_select_db($database_subman, $subman);
			$query_checkNetwork1 = "SELECT * FROM linknetworks WHERE linknetworks.link = ".$row_link['id']."";
			$checkNetwork1 = mysql_query($query_checkNetwork1, $subman) or die(mysql_error());
			$row_checkNetwork1 = mysql_fetch_assoc($checkNetwork1);
			$totalRows_checkNetwork1 = mysql_num_rows($checkNetwork1);
			
			mysql_select_db($database_subman, $subman);
			$query_checkAddresses = "SELECT * FROM addresses WHERE addresses.network = ".$row_link['provide_network']."";
			$checkAddresses = mysql_query($query_checkAddresses, $subman) or die(mysql_error());
			$row_checkAddresses = mysql_fetch_assoc($checkAddresses);
			$totalRows_checkAddresses = mysql_num_rows($checkAddresses);
			
			if ($totalRows_checkNetwork == 0 && (($totalRows_checkAddresses == 1 && $row_link['provide_node_b'] == "") || ($totalRows_checkAddresses == 2 && $row_link['provide_node_b'] != ""))) {
				
				$deleteSQL = sprintf("DELETE FROM networks WHERE networks.id=%s",
							   GetSQLValueString($row_link['provide_network'], "int"));
		
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
				
				$deleteSQL = sprintf("DELETE FROM addresses  WHERE addresses.network=%s",
							   GetSQLValueString($row_link['provide_network'], "int"));
		
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
				
			}
			
			if ($recovery_address_node_a_id != "") {
			
				$deleteSQL = sprintf("DELETE FROM addresses WHERE addresses.id=%s",
							   GetSQLValueString($recovery_address_node_a_id, "int"));
		
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			
			}
			if ($recovery_address_node_b_id != "") {
			
				$deleteSQL = sprintf("DELETE FROM addresses WHERE addresses.id=%s",
							   GetSQLValueString($recovery_address_node_b_id, "int"));
		
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			
			}		
			
			do {
					
				mysql_select_db($database_subman, $subman);
				$query_checkNetwork2 = "SELECT * FROM linknetworks WHERE linknetworks.link != ".$row_link['id']." AND linknetworks.network = '".$row_checkNetwork1['network']."'";
				$checkNetwork2 = mysql_query($query_checkNetwork2, $subman) or die(mysql_error());
				$row_checkNetwork2 = mysql_fetch_assoc($checkNetwork2);
				$totalRows_checkNetwork2 = mysql_num_rows($checkNetwork2);
				
				mysql_select_db($database_subman, $subman);
				$query_checkAddresses = "SELECT * FROM addresses WHERE addresses.network = '".$row_checkNetwork1['network']."'";
				$checkAddresses = mysql_query($query_checkAddresses, $subman) or die(mysql_error());
				$row_checkAddresses = mysql_fetch_assoc($checkAddresses);
				$totalRows_checkAddresses = mysql_num_rows($checkAddresses);
			
				if ($totalRows_checkNetwork2 == 0 && (($totalRows_checkAddresses == 1 && $row_link['provide_node_b'] == "") || ($totalRows_checkAddresses == 2 && $row_link['provide_node_b'] != ""))) {
					
					$deleteSQL = sprintf("DELETE linknetworks, networks, addresses FROM linknetworks left join networks on networks.id = linknetworks.network left join addresses on addresses.network = networks.id WHERE linknetworks.link=%s and linknetworks.network=%s",
							   GetSQLValueString($row_link['id'], "int"),
							   GetSQLValueString($row_checkNetwork1['network'], "int"));
		
					mysql_select_db($database_subman, $subman);
					$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
					
				}
		
			} while ($row_checkNetwork1 = mysql_fetch_assoc($checkNetwork1));
			
		}
		else {
			
			$deleteSQL = sprintf("DELETE FROM linknetworks WHERE linknetworks.link=%s",
						   GetSQLValueString($row_link['id'], "int"));
	
			mysql_select_db($database_subman, $subman);
			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			
			#ß$deleteSQL = sprintf("DELETE linknetworks, addresses FROM linknetworks left join networks on networks.id = linknetworks.network left join addresses on addresses.network = networks.id WHERE linknetworks.link=%s AND addresses.id = %s",
			#			   GetSQLValueString($row_link['id'], "int"),
			#			   GetSQLValueString($row_router_node_a['router'],"int"));
	
			#mysql_select_db($database_subman, $subman);
			#$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			
			#$deleteSQL = sprintf("DELETE FROM addresses WHERE addresses.id=%s",
			#			   GetSQLValueString($row_router_node_a['router'], "int"));
	
			#mysql_select_db($database_subman, $subman);
			#$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			
			#if ($totalRows_router_node_b > 0) {
				
			#	$deleteSQL = sprintf("DELETE linknetworks, addresses FROM linknetworks left join networks on networks.id = linknetworks.network left join addresses on addresses.network = networks.id WHERE linknetworks.link=%s AND addresses.id = %s",
			#			   GetSQLValueString($row_link['id'], "int"),
			#			   GetSQLValueString($row_router_node_b['router'],"int"));
	
			#mysql_select_db($database_subman, $subman);
			#$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			
			#	$deleteSQL = sprintf("DELETE FROM addresses WHERE addresses.id=%s",
			#			   GetSQLValueString($row_router_node_b['router'], "int"));
	
			#	mysql_select_db($database_subman, $subman);
			#	$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
				
			#}
			
		}
		
		if ($row_link['provide_vlan_node_a'] != "") {
			
			mysql_select_db($database_subman, $subman);
			$query_checkVlan = "SELECT * FROM portsports WHERE portsports.vlanid = ".$row_link['provide_vlan_node_a']."";
			$checkVlan = mysql_query($query_checkVlan, $subman) or die(mysql_error());
			$row_checkVlan = mysql_fetch_assoc($checkVlan);
			$totalRows_checkVlan = mysql_num_rows($checkVlan);
			
			mysql_select_db($database_subman, $subman);
			$query_checkVlan1 = "SELECT * FROM subint WHERE subint.vlanid = ".$row_link['provide_vlan_node_a']."";
			$checkVlan1 = mysql_query($query_checkVlan1, $subman) or die(mysql_error());
			$row_checkVlan1 = mysql_fetch_assoc($checkVlan1);
			$totalRows_checkVlan1 = mysql_num_rows($checkVlan1);
			
			if ($totalRows_checkVlan == 0 && $totalRows_checkVlan1 == 0) {
				
				$deleteSQL = sprintf("DELETE FROM vlan WHERE vlan.id=%s",
							   GetSQLValueString($row_link['provide_vlan_node_a'], "int"));
		
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
				
			}
			
		}
		
		if ($row_link['provide_vlan_node_b'] != "") {
			
			mysql_select_db($database_subman, $subman);
			$query_checkVlan = "SELECT * FROM portsports WHERE portsports.vlanid = ".$row_link['provide_vlan_node_b']."";
			$checkVlan = mysql_query($query_checkVlan, $subman) or die(mysql_error());
			$row_checkVlan = mysql_fetch_assoc($checkVlan);
			$totalRows_checkVlan = mysql_num_rows($checkVlan);
			
			mysql_select_db($database_subman, $subman);
			$query_checkVlan1 = "SELECT * FROM subint WHERE subint.vlanid = ".$row_link['provide_vlan_node_b']."";
			$checkVlan1 = mysql_query($query_checkVlan1, $subman) or die(mysql_error());
			$row_checkVlan1 = mysql_fetch_assoc($checkVlan1);
			$totalRows_checkVlan1 = mysql_num_rows($checkVlan1);
			
			if ($totalRows_checkVlan == 0 && $totalRows_checkVlan1 == 0) {
				
				$deleteSQL = sprintf("DELETE FROM vlan WHERE vlan.id=%s",
							   GetSQLValueString($row_link['provide_vlan_node_b'], "int"));
		
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
				
			}
			
		}
		
		if ($row_link['provide_vpn'] != "") { 
			
			mysql_select_db($database_subman, $subman);
			$query_checkVpnCustomer = "SELECT * FROM links WHERE links.provide_vpn = '".$row_link['provide_vpn']."' AND links.provide_customer = '".$row_link['provide_customer']."' AND links.id != '".$row_link['id']."'";
			$checkVpnCustomer = mysql_query($query_checkVpnCustomer, $subman) or die(mysql_error());
			$row_checkVpnCustomer = mysql_fetch_assoc($checkVpnCustomer);
			$totalRows_checkVpnCustomer = mysql_num_rows($checkVpnCustomer);
			
			if ($totalRows_checkVpnCustomer == 0) {
				
				$deleteSQL = sprintf("DELETE FROM vpncustomer WHERE vpncustomer.vpn=%s AND vpncustomer.customer=%s",
							   GetSQLValueString($row_link['provide_vpn'], "int"),
							   GetSQLValueString($row_link['provide_customer'], "int"));
		
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
			}
			
			if ($row_link['provide_xconnect'] != "") {
				
				$deleteSQL = sprintf("DELETE FROM vpnxconnect  WHERE vpnxconnect.vpn=%s AND vpnxconnect.xconnect=%s",
						   GetSQLValueString($row_link['provide_vpn'], "int"),
						   GetSQLValueString($row_link['provide_xconnect'], "int"));
	
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
				
				$deleteSQL = sprintf("DELETE FROM xconnectid  WHERE xconnectid.id=%s",
						   GetSQLValueString($row_link['provide_xconnect'], "int"));
	
				mysql_select_db($database_subman, $subman);
				$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
				
				mysql_select_db($database_subman, $subman);
				$query_checkVpn = "SELECT * FROM vpnxconnect WHERE vpnxconnect.vpn = ".$row_link['provide_vpn']."";
				$checkVpn = mysql_query($query_checkVpn, $subman) or die(mysql_error());
				$row_checkVpn = mysql_fetch_assoc($checkVpn);
				$totalRows_checkVpn = mysql_num_rows($checkVpn);
				
				if ($totalRows_checkVpn == 0) {
					
					$deleteSQL = sprintf("DELETE FROM vpn WHERE vpn.id=%s",
						   GetSQLValueString($row_link['provide_vpn'], "int"));
	
					mysql_select_db($database_subman, $subman);
					$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
					
					$deleteSQL = sprintf("DELETE FROM providervpn WHERE providervpn.vpn=%s",
						   GetSQLValueString($row_link['provide_vpn'], "int"));
	
					mysql_select_db($database_subman, $subman);
					$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
				}
				
			}
			
		}
		
		$deleteSQL = sprintf("DELETE FROM links  WHERE links.id=%s",
						   GetSQLValueString($_POST['recover_link'], "int"));
	
		mysql_select_db($database_subman, $subman);
		$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
	}
	
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_card")) {
	
	mysql_select_db($database_subman, $subman);
	$query_checkCard = "SELECT * FROM cards WHERE cards.device = '".$_POST['device']."' AND cards.rack ='".$_POST['rack']."' AND cards.module = '".$_POST['module']."' AND cards.slot = '".$_POST['slot']."' AND cards.cardtype = '".$_POST['cardtype']."'";
	$checkCard = mysql_query($query_checkCard, $subman) or die(mysql_error());
	$row_checkCard = mysql_fetch_assoc($checkCard);
	$totalRows_checkCard = mysql_num_rows($checkCard);
	
	if ($totalRows_checkCard > 0) {
    
    	$_SESSION['errstr'] .= "A card already exists with the selected rack/module/slot combination.  Please try again.";
		$errstrflag = 1;
    
	}
	else {
	
	if ($_POST['rack'] != "" && ($_POST['module'] == "" || $_POST['slot'] == "")) {
		if ($_POST['module'] == "") {
			$_POST['module'] = 0;
		}
		if ($_POST['slot'] == "") {
			$_POST['slot'] = 0;
		}
		$_SESSION['errstr'] .= "An invalid rack/module/slot combination was chosem, zero values have been used instead of blanks.";
		$errstrflag = 1;
	}
	elseif ($_POST['rack'] == "" && $_POST['module'] != "" && $_POST['slot'] == "") {
		$_POST['slot'] = 0;
		$_SESSION['errstr'] .= "An invalid rack/module/slot combination was chosem, zero values have been used instead of blanks.";
		$errstrflag = 1;
	}
	
		
  $insertSQL = sprintf("INSERT INTO cards (device, cardtype, timeslots, slotbandwidth, rack, module, slot, startport, endport) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
					   GetSQLValueString($_POST['device'], "int"),
                       GetSQLValueString($_POST['cardtype'], "int"),
                       GetSQLValueString($_POST['timeslots'], "int"),
					   GetSQLValueString($_POST['bandwidth'], "int"),
					   GetSQLValueString($_POST['rack'], "int"),
					   GetSQLValueString($_POST['module'], "int"),
					   GetSQLValueString($_POST['slot'], "int"),
					   GetSQLValueString($_POST['startport'], "int"),
					   GetSQLValueString($_POST['endport'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php?errstr=".$errstrflag;
  if (isset($_SERVER['QUERY_STRING'])) {
	$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
	$insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
	}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_cardtype")) {
		
  $insertSQL = sprintf("INSERT INTO cardtypes (name, config) VALUES (%s, %s)",
					   GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['config'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsecardtypes=1";

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_devicetype")) {
		
  $insertSQL = sprintf("INSERT INTO devicetypes (name, image) VALUES (%s, %s)",
					   GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['image'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsedevicetypes=1";

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_vlan")) {
	
	mysql_select_db($database_subman, $subman);
	$query_checkVlan = "SELECT * FROM vlan WHERE vlan.number = '".$_POST['number']."' AND vlan.vlanpool = ".$_POST['vlanpool']."";
	$checkVlan = mysql_query($query_checkVlan, $subman) or die(mysql_error());
	$row_checkVlan = mysql_fetch_assoc($checkVlan);
	$totalRows_checkVlan = mysql_num_rows($checkVlan);
	
	if ($totalRows_checkVlan > 0) { 
    
    	$_SESSION['errstr'] .= "The VLAN you have entered already exists in this container/VLAN pool.  Please try again.";
		$errstrflag = 1;
    
	}
	else {
		
  $insertSQL = sprintf("INSERT INTO vlan (name, vlanpool, number, customer) VALUES (%s, %s, %s, %s)",
					   GetSQLValueString($_POST['vlanname'], "text"),
                       GetSQLValueString($_POST['vlanpool'], "int"),
                       GetSQLValueString($_POST['number'], "text"),
                       GetSQLValueString($_POST['customer'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php?errstr=".$errstrflag;
  if (isset($_SERVER['QUERY_STRING'])) {
	$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
	$insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
	}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_as")) {
	
	mysql_select_db($database_subman, $subman);
	$query_checkAS = "SELECT * FROM asses WHERE asses.number = '".$_POST['as']."' AND asses.container = '".$_POST['container']."'";
	$checkAS = mysql_query($query_checkAS, $subman) or die(mysql_error());
	$row_checkAS = mysql_fetch_assoc($checkAS);
	$totalRows_checkAS = mysql_num_rows($checkAS);
	
	if ($totalRows_checkAS > 0) {
    
    	$_SESSION['errstr'] .= "The AS you have entered already exists in this container.  Please try again.";
		$errstrflag = 1;
    
	}
	else {
		
  $insertSQL = sprintf("INSERT INTO asses (number, name, descr, passwd, customer, container) VALUES (%s, %s, %s, %s, %s, %s)",
					   GetSQLValueString($_POST['as'], "int"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['customer'], "int"),
					   GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php?errstr".$errstrflag;
  if (isset($_SERVER['QUERY_STRING'])) {
	$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
	$insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
	}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_variable")) {
			
  $insertSQL = sprintf("INSERT INTO scriptvariables (variablename, value, script) VALUES (%s, %s, %s)",
					   GetSQLValueString($_POST['variablename'], "text"),
					   GetSQLValueString($_POST['value'], "text"),
					   GetSQLValueString($_POST['script'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
	$scriptid = mysql_insert_id();
	
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsetemplates=service&template=".$_POST['template']."&script=".$_POST['script'];

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_script")) {
			
  $insertSQL = sprintf("INSERT INTO scripts (description, location, scripttype, scriptrole, servicetemplate) VALUES (%s, %s, %s, %s, %s)",
					   GetSQLValueString($_POST['description'], "text"),
					   GetSQLValueString($_POST['location'], "text"),
					   GetSQLValueString($_POST['scripttype'], "text"),
					   GetSQLValueString($_POST['scriptrole'], "text"),
					   GetSQLValueString($_POST['template'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
	$scriptid = mysql_insert_id();
	
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsetemplates=service&template=".$_POST['template']."&script=".$scriptid;

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_field")) {
			
  $insertSQL = sprintf("INSERT INTO arbitraryfields (title, value, fieldtype, hint, servicetemplate) VALUES (%s, %s, %s, %s, %s)",
					   GetSQLValueString($_POST['title'], "text"),
					   GetSQLValueString($_POST['value'], "text"),
					   GetSQLValueString($_POST['fieldtype'], "text"),
					   GetSQLValueString($_POST['hint'], "text"),
					   GetSQLValueString($_POST['template'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
	$fieldid = mysql_insert_id();
	
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsetemplates=service&template=".$_POST['template'];

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "frm_delete_arbitrary")) {

  $deleteSQL = sprintf("DELETE FROM arbitraryfields WHERE id=%s",
					   GetSQLValueString($_POST['field'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsetemplates=service&template=".$_POST['template']."#_fields";

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "frm_delete_variable")) {
			
  $deleteSQL = sprintf("DELETE FROM scriptvariables WHERE id=%s",
					   GetSQLValueString($_POST['variable'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsetemplates=service&template=".$_POST['template']."&script=".$_POST['script'];

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "frm_delete_script")) {

  $deleteSQL = sprintf("DELETE FROM scripts WHERE id=%s",
					   GetSQLValueString($_POST['script'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	
	 $deleteSQL = sprintf("DELETE FROM scriptvariables WHERE script=%s",
					   GetSQLValueString($_POST['script'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
  
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsetemplates=service&template=".$_POST['template'];

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_update_script")) {
			
  $updateSQL = sprintf("UPDATE scripts SET description=%s, location=%s, scripttype=%s, scriptrole=%s, servicetemplate=%s, autorun=%s WHERE id=%s",
					   GetSQLValueString($_POST['description'], "text"),
					   GetSQLValueString($_POST['location'], "text"),
					   GetSQLValueString($_POST['scripttype'], "text"),
					   GetSQLValueString($_POST['scriptrole'], "text"),
					   GetSQLValueString($_POST['template'], "int"),
					   GetSQLValueString($_POST['autorun'], "int"),
					   GetSQLValueString($_POST['script'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	
	
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsetemplates=service&template=".$_POST['template']."&script=".$_POST['script'];

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_update_field")) {
			
  $updateSQL = sprintf("UPDATE arbitraryfields SET title=%s, value=%s, fieldtype=%s, hint=%s, servicetemplate=%s WHERE id=%s",
					   GetSQLValueString($_POST['title'], "text"),
					   GetSQLValueString($_POST['value'], "text"),
					   GetSQLValueString($_POST['fieldtype'], "text"),
					   GetSQLValueString($_POST['hint'], "text"),
					   GetSQLValueString($_POST['template'], "int"),
					   GetSQLValueString($_POST['field'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	
	
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsetemplates=service&template=".$_POST['template'];

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_adddevicegroup")) {
			
  $insertSQL = sprintf("INSERT INTO portgroups (name, container) VALUES (%s, %s)",
					   GetSQLValueString($_POST['groupname'], "text"),
					   GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&devicegroups=1";

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_addnetgroup")) {
			
  $insertSQL = sprintf("INSERT INTO networkgroup (name, descr, `user`, `date`, container) VALUES (%s, %s, %s, now(), %s)",
					   GetSQLValueString($_POST['groupname'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_SESSION['MM_Username'], "text"),
					   GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php?browse=networks&container=".$_POST['container']."&networkgroups=1";

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_ripe")) {
			
  $insertSQL = sprintf("INSERT INTO ripe (mask, inetname, approved, approvalDate, `user`, `date`, ncc, comments, container) VALUES (%s, %s, %s, %s, %s, now(), %s, %s, %s)",
					   GetSQLValueString($_POST['mask'], "text"),
                       GetSQLValueString($_POST['netname'], "text"),
                       GetSQLValueString($_POST['approved'], "text"),
                       GetSQLValueString($_POST['approvaldate'], "text"),
                       GetSQLValueString($_SESSION['MM_Username'], "text"),
					   GetSQLValueString($_POST['ncc'], "text"),
					   GetSQLValueString($_POST['comments'], "text"),
					   GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php?browse=ripe&container=".$_POST['container'];

	header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_edit_vlan")) {
  $updateSQL = sprintf("UPDATE vlan SET name=%s, customer=%s WHERE id=%s",
                       GetSQLValueString($_POST['vlanname'], "text"),
                       GetSQLValueString($_POST['customer'], "int"),					   
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_edit_vlanpool")) {
  $updateSQL = sprintf("UPDATE vlanpool SET name=%s, comments=%s WHERE id=%s",
                       GetSQLValueString($_POST['poolname'], "text"),
					   GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_vlanpool")) {
		
  $insertSQL = sprintf("INSERT INTO vlanpool (name, device, poolstart, poolend, comments, devicegroup) VALUES (%s, %s, %s, %s, %s, %s)",
					   GetSQLValueString($_POST['poolname'], "text"),
                       GetSQLValueString($_POST['device'], "int"),
                       GetSQLValueString($_POST['poolstart'], "int"),
                       GetSQLValueString($_POST['poolend'], "int"),
                       GetSQLValueString($_POST['comments'], "text"),
					   GetSQLValueString($_POST['shared'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
	$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
	$insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_editdevicegroup")) {
  $updateSQL = sprintf("UPDATE portgroups SET name=%s WHERE id=%s",
                       GetSQLValueString($_POST['groupname'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_editnetgroup")) {
  $updateSQL = sprintf("UPDATE networkgroup SET name=%s, descr=%s WHERE id=%s",
                       GetSQLValueString($_POST['groupname'], "text"),
					   GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
  $updateSQL = sprintf("UPDATE customer SET name=%s, account=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['account'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_edit_rtpool")) {
  $updateSQL = sprintf("UPDATE rtpool SET descr=%s WHERE id=%s",
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_edit_rdpool")) {
  $updateSQL = sprintf("UPDATE rdpool SET descr=%s WHERE id=%s",
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_edit_pseudowirepool")) {
  $updateSQL = sprintf("UPDATE xconnectpool SET descr=%s WHERE id=%s",
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_rtpool")) {
  $insertSQL = sprintf("INSERT INTO rtpool (provider, rtstart, rtend, descr) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['provider'], "int"),
                       GetSQLValueString($_POST['start'], "int"),
                       GetSQLValueString($_POST['end'], "int"),
                       GetSQLValueString($_POST['descr'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_rdpool")) {
  $insertSQL = sprintf("INSERT INTO rdpool (provider, rdstart, rdend, descr) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['provider'], "int"),
                       GetSQLValueString($_POST['start'], "int"),
                       GetSQLValueString($_POST['end'], "int"),
                       GetSQLValueString($_POST['descr'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_pseudowirepool")) {
  $insertSQL = sprintf("INSERT INTO xconnectpool (xconnectstart, xconnectend, descr, container) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['start'], "int"),
                       GetSQLValueString($_POST['end'], "int"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_provider")) {
  $insertSQL = sprintf("INSERT INTO provider (`name`, asnumber, descr, container) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['asnumber'], "int"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_edit_vrf")) {
  $updateSQL = sprintf("UPDATE vrf SET name=%s, descr=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_edit_vpn")) {
  $updateSQL = sprintf("UPDATE vpn SET name=%s, descr=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form4")) {
  $updateSQL = sprintf("UPDATE provider SET name=%s, asnumber=%s, descr=%s, `container`=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['asnumber'], "int"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['container'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form7")) {

if (Net_IPv4::validateIP($_POST['managementip'])) {
	$ip = $_POST['managementip'];
}
else {
	$ip = '';
}

  $updateSQL = sprintf("UPDATE portsdevices SET name=%s, descr=%s, managementip=%s, snmpcommunity=%s WHERE id=%s",
                       GetSQLValueString($_POST['devname'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
					   GetSQLValueString($ip, "text"),
					   GetSQLValueString($_POST['snmpcommunity'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form8")) {
	
	if (Net_IPv6::checkIPv6($_POST['network'])) {
				
				if ($_POST['parent'] == "" || $_POST['parent'] == 0) {
					$_POST['parent'] = '0';
					
					$netGroup = $_POST['networkGroup'];
				}
				else {
		
					mysql_select_db($database_subman, $subman);
					$query_getNetwork = "SELECT * FROM networks WHERE networks.id = ".$_POST['parent']."";
					$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
					$row_getNetwork = mysql_fetch_assoc($getNetwork);
					$totalRows_getNetwork = mysql_num_rows($getNetwork);
					
					$netGroup = $row_getNetwork['networkGroup'];
					
				}
				
			$_POST['network'] = Net_IPv6::uncompress($_POST['network']);
			$_POST['network'] = Net_IPv6::getNetmask($_POST['network'], $_POST['mask']);
			
			$maskLong = bcadd(ipv62long($_POST['network']),bcpow(2,(128 - $_POST['mask'])));
			
	mysql_select_db($database_subman, $subman);
	$query_check_networks = "SELECT * FROM networks WHERE (networks.network > ".(ipv62long($_POST['network'])-1)." AND networks.network < ".($maskLong+1).") AND networks.v6mask >= ".$_POST['mask']." AND networks.container = ".$_POST['container'].";";
	$check_networks = mysql_query($query_check_networks, $subman) or die(mysql_error());
	$row_check_networks = mysql_fetch_assoc($check_networks);
	$totalRows_check_networks = mysql_num_rows($check_networks);
	
	if ($totalRows_check_networks > 0) { 
    
    	$_SESSION['errstr'] .= "The network you entered overlaps with other networks in this container.  Please try again.";
		$errstrflag = 1;
    
	}
	elseif ($_POST['parent'] != 0 && (!(Net_IPv6::isInNetmask($_POST['network'], long2ipv6($row_getNetwork['network']), $row_getNetwork['v6mask'])))) { 
			
		$_SESSION['errstr'] .= "You have chosen a network that is outside of the current parent network.  Please try again.";
		$errstrflag = 1;
	
		}
	else {
		
  $insertSQL = sprintf("INSERT INTO networks (network, short, v6mask, descr, `user`, `date`, comments, networkGroup, parent, `container`) VALUES (%s, %s, %s, %s, %s, now(), %s, %s, %s, %s)",
                       GetSQLValueString(ipv62long($_POST['network']), "text"),
					   GetSQLValueString($_POST['network'], "text"),
                       GetSQLValueString($_POST['mask'], "int"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['user'], "text"),-
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($netGroup, "int"),
                       GetSQLValueString($_POST['parent'], "int"),
                       GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  }
	}
	else { 
		
		$_SESSION['errstr'] .= "The network entered is not a valid IPv6 network.  Please try again.";
		$errstrflag = 1;
		
	}
	
	$insertGoTo = "containerView.php?errstr=".$errstrflag;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
  
}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form6")) {

if (Net_IPv4::validateIP($_POST['managementip'])) {
	$ip = $_POST['managementip'];
}
else {
	$ip = '';
}

  $insertSQL = sprintf("INSERT INTO portsdevices (name, descr, devicetype, devicegroup, managementip, snmpcommunity) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['devname'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['devicetype'], "int"),
                       GetSQLValueString($_POST['devicegroup'], "int"),
					   GetSQLValueString($ip, "text"),
					   GetSQLValueString($_POST['snmpcommunity'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
	
  $insertGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
	$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
	$insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form12")) {
  $updateSQL = sprintf("UPDATE `user` SET firstname=%s, lastname=%s, password=%s, email=%s WHERE id=%s",
                       GetSQLValueString($_POST['firstname'], "text"),
                       GetSQLValueString($_POST['lastname'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  $updateGoTo = "containerView.php?container=".$_POST['container'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form11")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getRTPool = "SELECT * FROM rtpool WHERE rtpool.id = ".$_POST['rt']."";
	$getRTPool = mysql_query($query_getRTPool, $subman) or die(mysql_error());
	$row_getRTPool = mysql_fetch_assoc($getRTPool);
	$totalRows_getRTPool = mysql_num_rows($getRTPool);
	
	for ($i = $row_getRTPool['rtstart']; $i <= $row_getRTPool['rtend']; $i++) {
		
		mysql_select_db($database_subman, $subman);
		$query_getRT = "SELECT * FROM rt WHERE rt.rtpool = ".$_POST['rt']." AND rt.rt = ".$i."";
		$getRT = mysql_query($query_getRT, $subman) or die(mysql_error());
		$row_getRT = mysql_fetch_assoc($getRT);
		$totalRows_getRT = mysql_num_rows($getRT);
		
		if ($totalRows_getRT == 0) {
			 $insertSQL = sprintf("INSERT INTO rt (rtpool, rt, descr) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['rt'], "int"),
                       GetSQLValueString($i, "int"),
                       GetSQLValueString($_POST['descr'], "text"));

			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
			
			$lastID = mysql_insert_id();
			$i = $row_getRTPool['rtend'] + 1;
		}
		
	}
	
	$insertSQL = sprintf("INSERT INTO rtvpn (vpn, rt) VALUES (%s, %s)",
                       GetSQLValueString($_POST['vpn'], "int"),
                       GetSQLValueString($lastID, "int"));

  	mysql_select_db($database_subman, $subman);
  	$Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  if ($_SESSION['provide_step'] == 3) {
	  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container'];
  }
  else {
  $insertGoTo = "containerView.php";
	  if (isset($_SERVER['QUERY_STRING'])) {
		$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
		$insertGoTo .= $_SERVER['QUERY_STRING'];
	  }
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form10")) {
	
	mysql_select_db($database_subman, $subman);
	$query_getRDPool = "SELECT * FROM rdpool WHERE rdpool.id = ".$_POST['rd']."";
	$getRDPool = mysql_query($query_getRDPool, $subman) or die(mysql_error());
	$row_getRDPool = mysql_fetch_assoc($getRDPool);
	$totalRows_getRDPool = mysql_num_rows($getRDPool);
	
	for ($i = $row_getRDPool['rdstart']; $i <= $row_getRDPool['rdend']; $i++) {
		
		mysql_select_db($database_subman, $subman);
		$query_getRD = "SELECT * FROM rd WHERE rd.rdpool = ".$_POST['rd']." AND rd.rd = ".$i."";
		$getRD = mysql_query($query_getRD, $subman) or die(mysql_error());
		$row_getRD = mysql_fetch_assoc($getRD);
		$totalRows_getRD = mysql_num_rows($getRD);
		
		if ($totalRows_getRD == 0) {
			 $insertSQL = sprintf("INSERT INTO rd (rdpool, rd, descr) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['rd'], "int"),
                       GetSQLValueString($i, "int"),
                       GetSQLValueString($_POST['descr'], "text"));

			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
			
			$rd = mysql_insert_id();
			$i = $row_getRDPool['rdend'] + 1;
		}
		
	}
	
  $insertSQL = sprintf("INSERT INTO vrf (rd, name, descr) VALUES (%s, %s, %s)",
                       GetSQLValueString($rd, "int"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['descr'], "text"));


  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
  
  $lastID = mysql_insert_id();
  
  $insertSQL = sprintf("INSERT INTO vpnvrf (vpn, vrf) VALUES (%s, %s)",
                       GetSQLValueString($_POST['vpn'], "int"),
                       GetSQLValueString($lastID, "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  if ($_SESSION['provide_step'] == 3) {
	  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container'];
  }
  else {
  $insertGoTo = "containerView.php";
	  if (isset($_SERVER['QUERY_STRING'])) {
		$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
		$insertGoTo .= $_SERVER['QUERY_STRING'];
	  }
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form9")) {
  $insertSQL = sprintf("INSERT INTO vpn (name, descr, layer) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['layer'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
  
  $lastID = mysql_insert_id();

  
  $insertSQL = sprintf("INSERT INTO providervpn (provider, vpn) VALUES (%s, %s)",
                       GetSQLValueString($_POST['provider'], "int"),
                       GetSQLValueString($lastID, "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
  
  if ($_SESSION['provide_step'] == 2) {
	  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container'];

  }
  else {
  $insertGoTo = "containerView.php";
	  if (isset($_SERVER['QUERY_STRING'])) {
		$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
		$insertGoTo .= $_SERVER['QUERY_STRING'];
	  }
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form5")) {
  $insertSQL = sprintf("INSERT INTO customer (name, account, `container`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['account'], "int"),
                       GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

	$lastID = mysql_insert_id();
	
  $insertGoTo = "containerView.php?browse=customers&container=".$_GET['container']."&customer=".$lastID;
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_network")) {
  $insertSQL = sprintf("INSERT INTO networks (network, short, mask, descr, maskLong, `user`, `date`, comments, networkGroup, parent, `container`) VALUES (%s, INET_NTOA('%s'), %s, %s, INET_ATON('%s'), %s, now(), %s, %s, %s, %s)",
                       GetSQLValueString($_POST['network'], "text"),
                       $_POST['network'],					   
                       GetSQLValueString($_POST['mask'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       $_POST['mask'],					   
                       GetSQLValueString($_POST['user'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['networkGroup'], "int"),
                       GetSQLValueString($_POST['parent'], "int"),
                       GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
  
    $insertGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_v6_network")) {
  $insertSQL = sprintf("INSERT INTO networks (network, short, v6mask, descr, `user`, `date`, comments, networkGroup, parent, `container`) VALUES (%s, %s, %s, %s, %s, now(), %s, %s, %s, %s)",
                       GetSQLValueString($_POST['network'], "text"),
                       GetSQLValueString(long2ipv6($_POST['network']),"text"),					   
                       GetSQLValueString($_POST['mask'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['user'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['networkGroup'], "int"),
                       GetSQLValueString($_POST['parent'], "int"),
                       GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
  
    $insertGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
}

if (isset($_POST['saveservicetemplate']) && $_POST['saveservicetemplate'] == 1) {
	
	  $insertSQL = sprintf("INSERT INTO servicetemplate (name, provide_node_a, provide_node_b, provide_layer, provide_logical_node_a, provide_logical_node_b, provide_cct, provide_customer, provide_vpn, provide_vlan_node_a, provide_vlan_node_b, provide_xconnectpool, provide_vrf, provide_pece, provide_netgroup, provide_netsize, provide_parent, provide_card_node_a, provide_card_node_b, provide_timeslots_node_a, provide_timeslots_node_b, do_not_subnet, manual_addressing, manual_xconnect, container) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
																																																																																																 					   GetSQLValueString($_POST['servicename'], "text"),
                       GetSQLValueString($_SESSION['provide_node_a'], "int"),
                       GetSQLValueString($_SESSION['provide_node_b'], "int"),					   
                       GetSQLValueString($_SESSION['provide_layer'], "int"),
                       GetSQLValueString($_SESSION['provide_logical_node_a'], "text"),
                       GetSQLValueString($_SESSION['provide_logical_node_b'], "text"),					   
                       GetSQLValueString($_SESSION['provide_cct'], "text"),					   
                       GetSQLValueString($_SESSION['provide_customer'], "int"),
                       GetSQLValueString($_SESSION['provide_vpn'], "text"),
                       GetSQLValueString($_SESSION['provide_vlan_node_a'], "text"),
                       GetSQLValueString($_SESSION['provide_vlan_node_b'], "text"),
					   GetSQLValueString($_SESSION['provide_xconnectpool'], "int"),
					   GetSQLValueString($_SESSION['provide_vrf'], "int"),
					   GetSQLValueString($_SESSION['pece'], "int"),
					   GetSQLValueString($_SESSION['provide_netgroup'], "int"),
					   GetSQLValueString($_SESSION['provide_netsize'], "int"),
					   GetSQLValueString($_SESSION['provide_parent'], "int"),
					   GetSQLValueString($_SESSION['provide_card_node_a'], "int"),
					   GetSQLValueString($_SESSION['provide_card_node_b'], "int"),
					   GetSQLValueString($_SESSION['provide_timeslots_node_a'], "int"),
					   GetSQLValueString($_SESSION['provide_timeslots_node_b'], "int"),
					   GetSQLValueString($_SESSION['provide_existing'], "int"),
					   GetSQLValueString($_SESSION['provide_address'], "int"),
					   GetSQLValueString($_SESSION['manual_xconnect'], "int"),
					   GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());
  
  	unset($_SESSION['provide_template']);
	unset($_SESSION['provide_node_a']);
	unset($_SESSION['provide_node_b']);
	unset($_SESSION['provide_layer']);
	unset($_SESSION['provide_logical_node_a']);
	unset($_SESSION['provide_logical_node_b']);
	unset($_SESSION['provide_cct']);
	unset($_SESSION['provide_customer']);
	unset($_SESSION['provide_vpn']);
	unset($_SESSION['provide_vlan_node_a']);
	unset($_SESSION['provide_vlan_node_b']);
	unset($_SESSION['provide_xconnectpool']);
	unset($_SESSION['provide_vrf']);
	unset($_SESSION['pece']);
	unset($_SESSION['provide_netgroup']);
	unset($_SESSION['provide_netsize']);
	unset($_SESSION['provide_parent']);
	unset($_SESSION['provide_existing']);
	unset($_SESSION['provide_network']);
	unset($_SESSION['provide_card_node_a']);
	unset($_SESSION['provide_card_node_b']);
	unset($_SESSION['provide_timeslots_node_a']);
	unset($_SESSION['provide_timeslots_node_b']);
	unset($_SESSION['provide_subint_node_a']);
	unset($_SESSION['provide_subint_node_b']);
	unset($_SESSION['provide_port_node_a']);
	unset($_SESSION['provide_port_node_b']);
	unset($_SESSION['provide_step']);
	unset($_SESSION['templatelink']);
	unset($_SESSION['linked']);
	unset($_SESSION['vlannumber_a']);
	unset($_SESSION['vlannumber_b']);
	unset($_SESSION['additional_address']);
	unset($_SESSION['manual_xconnect']);
	unset($_SESSION['xconnectid']);
  
    $insertGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
	
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_update_servicetemplate")) {

	if ($_POST['node_a'] == "") {
		$_POST['node_a'] = 0;
	}
	if ($_POST['node_b'] == "") {
		$_POST['node_b'] = 0;
	}
	if ($_POST['layer'] == "") {
		$_POST['layer'] = 0;
	}
	if ($_POST['logical_node_a'] == "") {
		$_POST['logical_node_a'] = 0;
	}
	if ($_POST['logical_node_b'] == "") {
		$_POST['logical_node_b'] = 0;
	}
	if ($_POST['circuit'] == "") {
		$_POST['circuit'] = 0;
	}
	if ($_POST['customer'] == "") {
		$_POST['customer'] = 0;
	}
	if ($_POST['vpn'] == "") {
		$_POST['vpn'] = 0;
	}
	if ($_POST['vlan_node_a'] == "") {
		$_POST['vlan_node_a'] = 0;
	}
	if ($_POST['vlan_node_b'] == "") {
		$_POST['vlan_node_b'] = 0;
	}
	if ($_POST['xconnectpool'] == "") {
		$_POST['xconnectpool'] = 0;
	}
	if ($_POST['manual_xconnect'] == "") {
		$_POST['manual_xconnect'] = 0;
	}
	if ($_POST['vrf'] == "") {
		$_POST['vrf'] = 0;
	}
	if ($_POST['pece'] == "") {
		$_POST['pece'] = 0;
	}
	if ($_POST['netgroup'] == "") {
		$_POST['netgroup'] = 0;
	}
	if ($_POST['netsize'] == "") {
		$_POST['netsize'] = 0;
	}
	if ($_POST['parent'] == "") {
		$_POST['parent'] = 0;
	}
	if ($_POST['card_node_a'] == "") {
		$_POST['card_node_a'] = 0;
	}
	if ($_POST['card_node_b'] == "") {
		$_POST['card_node_b'] = 0;
	}
	if ($_POST['timeslots_node_a'] == "") {
		$_POST['timeslots_node_a'] = 0;
	}
	if ($_POST['timeslots_node_b'] == "") {
		$_POST['timeslots_node_b'] = 0;
	}
	if ($_POST['subint_auto_a'] == "") {
		$_POST['subint_auto_a'] = 0;
	}
	if ($_POST['subint_auto_b'] == "") {
		$_POST['subint_auto_b'] = 0;
	}
	if ($_POST['do_not_subnet'] == "") {
		$_POST['do_not_subnet'] = 0;
	}
	if ($_POST['manual_addressing'] == "") {
		$_POST['manual_addressing'] = 0;
	}
	
	if ($_POST['nodea_default_radio'] == 'device') {
		$nodea_default_id = $_POST['nodea_default_device'];
	}
	elseif ($_POST['nodea_default_radio'] == 'devicetype') {
		$nodea_default_id = $_POST['nodea_default_devicetype'];
	}
	elseif ($_POST['nodea_default_radio'] == 'devicegroup') {
		$nodea_default_id = $_POST['nodea_default_devicegroup'];
	}
	
	if ($_POST['nodeb_default_radio'] == 'device') {
		$nodeb_default_id = $_POST['nodeb_default_device'];
	}
	elseif ($_POST['nodeb_default_radio'] == 'devicetype') {
		$nodeb_default_id = $_POST['nodeb_default_devicetype'];
	}
	elseif ($_POST['nodeb_default_radio'] == 'devicegroup') {
		$nodeb_default_id = $_POST['nodeb_default_devicegroup'];
	}
	
	if ($_POST['nodea_card_default_radio'] == 'card') {
		$nodea_card_default_id = $_POST['nodea_card_default_card'];
	}
	elseif ($_POST['nodea_card_default_radio'] == 'cardtype') {
		$nodea_card_default_id = $_POST['nodea_card_default_cardtype'];
	}
	
	if ($_POST['nodeb_card_default_radio'] == 'card') {
		$nodeb_card_default_id = $_POST['nodeb_card_default_card'];
	}
	elseif ($_POST['nodeb_card_default_radio'] == 'cardtype') {
		$nodeb_card_default_id = $_POST['nodeb_card_default_cardtype'];
	}
	
  $updateSQL = sprintf("UPDATE servicetemplate SET name=%s, provide_node_a_editable=%s, provide_node_a_default=%s, provide_node_a_default_id=%s, provide_node_b_editable=%s, provide_node_b_default=%s, provide_node_b_default_id=%s, provide_layer_editable=%s, provide_logical_node_a_editable=%s, provide_logical_node_b_editable=%s, provide_cct_editable=%s, provide_cct=%s, provide_customer_editable=%s, provide_vpn_editable=%s, provide_vlan_node_a_editable=%s, provide_vlan_node_b_editable=%s, provide_xconnectpool_editable=%s, manual_xconnect_editable=%s, provide_vrf_editable=%s, provide_pece_editable=%s, provide_netgroup_editable=%s, provide_netsize_editable=%s, provide_netsize_default=%s, provide_netsize_default_min_ipv4=%s, provide_netsize_default_max_ipv4=%s, provide_netsize_default_min_ipv6=%s, provide_netsize_default_max_ipv6=%s, provide_parent_editable=%s, provide_card_node_a_editable=%s, provide_card_node_a_default=%s, provide_card_node_a_default_id=%s, provide_card_node_b_editable=%s, provide_card_node_b_default=%s, provide_card_node_b_default_id=%s, provide_subint_node_a_auto=%s, provide_subint_node_b_auto=%s, provide_timeslots_node_a_editable=%s, provide_timeslots_node_b_editable=%s, config_a=%s, config_b=%s, recover_a=%s, recover_b=%s, `disabled`=%s, templatelink=%s, routes=%s, routesnode=%s, recover_routes=%s, secondarynets=%s, secondarynets_b=%s, recover_secondarynets=%s, recover_secondarynets_b=%s, step1prompt=%s, step2prompt=%s, step3prompt=%s, step4prompt=%s, step5prompt=%s, step6prompt=%s, step7prompt=%s, step8prompt=%s, do_not_subnet_editable=%s, manual_addressing_editable=%s WHERE id=%s",
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['node_a'], "text"),
                       GetSQLValueString($_POST['nodea_default_radio'], "text"),
                       GetSQLValueString($nodea_default_id, "int"),
                       GetSQLValueString($_POST['node_b'], "text"),
                       GetSQLValueString($_POST['nodeb_default_radio'], "text"),
                       GetSQLValueString($nodeb_default_id, "int"),
                       GetSQLValueString($_POST['layer'], "text"),
                       GetSQLValueString($_POST['logical_node_a'], "int"),
					   GetSQLValueString($_POST['logical_node_b'], "int"),
					   GetSQLValueString($_POST['circuit'], "int"),
					   GetSQLValueString($_POST['value_circuit'], "text"),
					   GetSQLValueString($_POST['customer'], "int"),
					   GetSQLValueString($_POST['vpn'], "int"),
					   GetSQLValueString($_POST['vlan_node_a'], "int"),
					   GetSQLValueString($_POST['vlan_node_b'], "int"),
					   GetSQLValueString($_POST['xconnectpool'], "int"),
					   GetSQLValueString($_POST['manual_xconnect'], "int"),
					   GetSQLValueString($_POST['vrf'], "int"),
					   GetSQLValueString($_POST['pece'], "int"),
					   GetSQLValueString($_POST['netgroup'], "int"),
					   GetSQLValueString($_POST['netsize'], "int"),
					   GetSQLValueString($_POST['netsize_default_radio'], "text"),
					   GetSQLValueString($_POST['netsize_default_maskrange_min_ipv4'], "int"),
					   GetSQLValueString($_POST['netsize_default_maskrange_max_ipv4'], "int"),
					   GetSQLValueString($_POST['netsize_default_maskrange_min_ipv6'], "int"),
					   GetSQLValueString($_POST['netsize_default_maskrange_max_ipv6'], "int"),
					   GetSQLValueString($_POST['parent'], "int"),
					   GetSQLValueString($_POST['card_node_a'], "int"),
					   GetSQLValueString($_POST['nodea_card_default_radio'], "text"),
                       GetSQLValueString($nodea_card_default_id, "int"),
					   GetSQLValueString($_POST['card_node_b'], "int"),
					   GetSQLValueString($_POST['nodeb_card_default_radio'], "text"),
                       GetSQLValueString($nodeb_card_default_id, "int"),
					   GetSQLValueString($_POST['subint_auto_a'], "int"),
					   GetSQLValueString($_POST['subint_auto_b'], "int"),
					   GetSQLValueString($_POST['timeslots_node_a'], "int"),
					   GetSQLValueString($_POST['timeslots_node_b'], "int"),
					   GetSQLValueString($_POST['config_a'], "text"),
					   GetSQLValueString($_POST['config_b'], "text"),
					   GetSQLValueString($_POST['recover_a'], "text"),
					   GetSQLValueString($_POST['recover_b'], "text"),
					   GetSQLValueString($_POST['disabled'], "int"),
					   GetSQLValueString($_POST['templatelink'], "int"),
					   GetSQLValueString($_POST['routes'], "text"),
					   GetSQLValueString($_POST['routesnode'], "int"),
					   GetSQLValueString($_POST['recover_routes'], "text"),
					   GetSQLValueString($_POST['secondarynets'], "text"),
					   GetSQLValueString($_POST['secondarynets_b'], "text"),
					   GetSQLValueString($_POST['recover_secondarynets'], "text"),
					   GetSQLValueString($_POST['recover_secondarynets_b'], "text"),
					   GetSQLValueString($_POST['step1prompt'], "text"),
					   GetSQLValueString($_POST['step2prompt'], "text"),
					   GetSQLValueString($_POST['step3prompt'], "text"),
					   GetSQLValueString($_POST['step4prompt'], "text"),
					   GetSQLValueString($_POST['step5prompt'], "text"),
					   GetSQLValueString($_POST['step6prompt'], "text"),
					   GetSQLValueString($_POST['step7prompt'], "text"),
					   GetSQLValueString($_POST['step8prompt'], "text"),
					   GetSQLValueString($_POST['do_not_subnet'], "int"),
					   GetSQLValueString($_POST['manual_addressing'], "int"),
					   GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	
  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

mysql_select_db($database_subman, $subman);
$query_getNetwork = "SELECT * FROM networks WHERE networks.id = ".$_POST['id']."";
$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
$row_getNetwork = mysql_fetch_assoc($getNetwork);
$totalRows_getNetwork = mysql_num_rows($getNetwork);

if ($row_getNetwork['v6mask'] == "") {
	
	$net = find_net(long2ip($row_getNetwork['network']),$row_getNetwork['mask']);

	mysql_select_db($database_subman, $subman);
	$query_getGroupNetworks = "SELECT * FROM networks WHERE networks.network BETWEEN ".$net['network']." AND ".$net['broadcast']." AND maskLong > ".$row_getNetwork['maskLong']." AND container = ".$_POST['container']."";
	$getGroupNetworks = mysql_query($query_getGroupNetworks, $subman) or die(mysql_error());
	$row_getGroupNetworks = mysql_fetch_assoc($getGroupNetworks);
	$totalRows_getGroupNetworks = mysql_num_rows($getGroupNetworks);
}
else {
	
	mysql_select_db($database_subman, $subman);
	$query_getGroupNetworks = "SELECT * FROM networks WHERE networks.network BETWEEN ".$row_getNetwork['network']." AND ".bcadd($row_getNetwork['network'],bcpow(2,(128 - $row_getNetwork['v6mask'])))." AND v6mask > '".$row_getNetwork['v6mask']."' AND container = ".$_POST['container']."";
	$getGroupNetworks = mysql_query($query_getGroupNetworks, $subman) or die(mysql_error());
	$row_getGroupNetworks = mysql_fetch_assoc($getGroupNetworks);
	$totalRows_getGroupNetworks = mysql_num_rows($getGroupNetworks);
	
}

  $updateSQL = sprintf("UPDATE networks SET descr=%s, updateUser=%s, updateDate=now(), comments=%s, networkGroup=%s, ripeBlock=%s WHERE id=%s",
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['updateUser'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['networkGroup'], "int"),
					   GetSQLValueString($_POST['ripe'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
	
  if ($totalRows_getGroupNetworks > 0) { 
	do {
	
		$updateSQL = "UPDATE networks SET networkGroup = '".$_POST['networkGroup']."' WHERE id = ".$row_getGroupNetworks['id'].";";
		
		mysql_select_db($database_subman, $subman);
  		$Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());

  	} while ($row_getGroupNetworks = mysql_fetch_assoc($getGroupNetworks));
  }
	
  $updateGoTo = "containerView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {

	$ip_calc = new Net_IPv4();
	$ip_calc->ip = $_POST['network'];
	$ip_calc->netmask = $_POST['mask'];
	$ip_calc->calculate();
	
	$_POST['network'] = $ip_calc->network;
	
	if ($_POST['parent'] == "" || $_POST['parent'] == 0) {
		$_POST['parent'] = '0';
		
		$netGroup = $_POST['networkGroup'];
	}
	else {
		
		mysql_select_db($database_subman, $subman);
		$query_getNetwork = "SELECT * FROM networks WHERE networks.id = ".$_POST['parent']."";
		$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
		$row_getNetwork = mysql_fetch_assoc($getNetwork);
		$totalRows_getNetwork = mysql_num_rows($getNetwork);
		
		$netGroup = $row_getNetwork['networkGroup'];
	}
	
	$net = find_net($_POST['network'],$_POST['mask']);
		
	mysql_select_db($database_subman, $subman);
	$query_check_networks = "SELECT * FROM networks WHERE ((networks.network > ".($net['network']-1)." AND networks.network < ".($net['broadcast']+1).") AND networks.maskLong > INET_ATON('".$_POST['mask']."') AND networks.container = ".$_POST['container'].") OR networks.network = ".$net['network']." AND networks.mask = '".$_POST['mask']."' AND networks.container = ".$_POST['container'].";";
	$check_networks = mysql_query($query_check_networks, $subman) or die(mysql_error());
	$row_check_networks = mysql_fetch_assoc($check_networks);
	$totalRows_check_networks = mysql_num_rows($check_networks);
	
	if (!(Net_IPv4::validateIP($_POST['network']))) {
		
        $_SESSION['errstr'] .= "The network you entered is invalid.  Please try again.";
		$errstrflag = 1;
            
	}
	elseif ($totalRows_check_networks > 0) {
		
		$_SESSION['errstr'] .= "The base network you are trying to add overlaps with other networks in this container.  Please try again.";
		$errstrflag = 1;

		}
	elseif ($_POST['parent'] != 0 && (!(Net_IPv4::ipInNetwork($_POST['network'], long2ip($row_getNetwork['network']).get_slash($row_getNetwork['mask']))))) {
		
        $_SESSION['errstr'] .= "You have chosen a network that is outside of the current parent network.  Please try again.";
		$errstrflag = 1;
            
		}
	else {

  $insertSQL = sprintf("INSERT INTO networks (network, mask, maskLong, short, descr, `user`, `date`, comments, networkGroup, parent, container) VALUES (%s, %s, INET_ATON('%s'), %s, %s, %s, now(), %s, %s, %s, %s)",
                       GetSQLValueString($net['network'], "text"),
                       GetSQLValueString($_POST['mask'], "text"),
                       $_POST['mask'],
                       GetSQLValueString($_POST['network'], "text"),					   
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['user'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($netGroup, "int"),
                       GetSQLValueString($_POST['parent'], "int"),
					   GetSQLValueString($_POST['container'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "containerView.php?errstr=".$errstrflag;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
	}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_add_address")) {

	mysql_select_db($database_subman, $subman);
	$query_parent = "SELECT * FROM networks WHERE networks.id = '".$_POST['network']."'";
	$parent = mysql_query($query_parent, $subman) or die(mysql_error());
	$row_parent = mysql_fetch_assoc($parent);
	$totalRows_parent = mysql_num_rows($parent);
	
	if ($row_parent['v6mask'] == "") {
		
		$net = array();
		$net = find_net(long2ip($row_parent['network']),$row_parent['mask']);
		
		// Check that the address is not already being used on this network, if it is, print an error message
		mysql_select_db($database_subman, $subman);
		$query_checkAddr = "SELECT * FROM addresses WHERE addresses.network = ".$_POST['network']." AND addresses.address = ".ip2long($_POST['address'])."";
		$checkAddr = mysql_query($query_checkAddr, $subman) or die(mysql_error());
		$row_checkAddr = mysql_fetch_assoc($checkAddr);
		$totalRows_checkAddr = mysql_num_rows($checkAddr);
		
	}
	else {
		
		mysql_select_db($database_subman, $subman);
		$query_checkAddr = "SELECT * FROM addresses WHERE addresses.network = ".$_POST['network']." AND addresses.address = ".ipv62long(Net_IPv6::Uncompress($_POST['address']))."";
		$checkAddr = mysql_query($query_checkAddr, $subman) or die(mysql_error());
		$row_checkAddr = mysql_fetch_assoc($checkAddr);
		$totalRows_checkAddr = mysql_num_rows($checkAddr);
		
	}
// Check if address is inside the network, if not, print an error message
	if (($row_parent['v6mask'] == "") && (!(Net_IPv4::ipInNetwork($_POST['address'], long2ip($row_parent['network']).get_slash($row_parent['mask']))))) { 
			
			$_SESSION['errstr'] .= "You have chosen an address that is outside of the current network.  Please try again.";
		   	$errstrflag = 1;
		}
		elseif (!(Net_IPv6::isInNetmask($_POST['address'], long2ipv6($row_parent['network']), $row_parent['v6mask']))) {
			
			$_SESSION['errstr'] .= "You have chosen an address that is outside of the current network.  Please try again.";
		   	$errstrflag = 1;
			
		}
	elseif ($totalRows_checkAddr > 0) {
		
			$_SESSION['errstr'] .= "The IP address is already in use on this network.  Please try again.";
		   	$errstrflag = 1;

	}
	else {
		
		if ($row_parent['v6mask'] == "") {

			$insertSQL = sprintf("INSERT INTO addresses (address, network, short, descr, customer, comments, `user`, date) VALUES (INET_ATON('%s'), %s, %s, %s, %s, %s, %s, now())",
                       $_POST['address'],
                       GetSQLValueString($_POST['network'], "int"),
					   GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['user'], "text"));
			
		}
		else {
			
			$_POST['address'] = Net_IPv6::uncompress($_POST['address']);
			$address = ipv62long($_POST['address']);
			
			$insertSQL = sprintf("INSERT INTO addresses (address, network, short, descr, customer, comments, `user`, date) VALUES (%s, %s, %s, %s, %s, %s, %s, now())",
                       GetSQLValueString($address, "text"),
                       GetSQLValueString($_POST['network'], "int"),
					   GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['user'], "text"));
		}
		
  

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "containerView.php?browse=networks&container=".$_GET['container']."&group=".$_GET['group']."&parent=".$_GET['parent']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $insertGoTo));
  }
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_edit_address")) {
		
  $insertSQL = sprintf("UPDATE addresses SET address=%s, network=%s, descr=%s, customer=%s, comments=%s, updateUser=%s, updateDate=now() WHERE id=%s",
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['network'], "int"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['user'], "text"),
					   GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "containerView.php?browse=networks&container=".$_POST['container']."&group=".$_POST['group']."&parent=".$_POST['parent'];
    if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_update_cardtype")) {
		
  $insertSQL = sprintf("UPDATE cardtypes SET name=%s, config=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['config'], "text"),
					   GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsecardtypes=1";

  header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_update_devicetype")) {
		
  $insertSQL = sprintf("UPDATE devicetypes SET name=%s, image=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['image'], "text"),
					   GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsedevicetypes=1";

  header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "frm_delete_device")) {

	mysql_select_db($database_subman, $subman);
	$query_check_cards = "SELECT * FROM cards WHERE cards.device = '".$_POST['id']."'";
	$check_cards = mysql_query($query_check_cards, $subman) or die(mysql_error());
	$row_check_cards = mysql_fetch_assoc($check_cards);
	$totalRows_check_cards = mysql_num_rows($check_cards);
	
	mysql_select_db($database_subman, $subman);
	$query_check_vlanpools = "SELECT * FROM vlanpool WHERE vlanpool.device = '".$_POST['id']."'";
	$check_vlanpools = mysql_query($query_check_vlanpools, $subman) or die(mysql_error());
	$row_check_vlanpools = mysql_fetch_assoc($check_vlanpools);
	$totalRows_check_vlanpools = mysql_num_rows($check_vlanpools);
	
	if ($totalRows_check_cards == 0 && $totalRows_check_vlanpools == 0) {
		
	  $deleteSQL = sprintf("DELETE FROM portsdevices WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	}
	else {
        	
           $_SESSION['errstr'] .= "The device was not deleted as it contains VLAN pool(s) and/or line card(s).  Please remove these items first.";
		   $errstrflag = 1;
	}
  
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&group=".$_POST['group']."&errstr=".$errstrflag;

  header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "frm_delete_devicegroup")) {

	mysql_select_db($database_subman, $subman);
	$query_check_devices = "SELECT * FROM portsdevices WHERE portsdevices.devicegroup = '".$_POST['id']."'";
	$check_devices = mysql_query($query_check_devices, $subman) or die(mysql_error());
	$row_check_devices = mysql_fetch_assoc($check_devices);
	$totalRows_check_devices = mysql_num_rows($check_devices);
	
	if ($totalRows_check_devices == 0) {
		
	  $deleteSQL = sprintf("DELETE FROM portgroups WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	}
	else {
        	
           $_SESSION['errstr'] .= "The device group was not deleted as it contains devices.  Please update the device(s) first.";
		   $errstrflag = 1;
	}
  
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&devicegroups=1&errstr=".$errstrflag;

  header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "frm_delete_netgroup")) {

	mysql_select_db($database_subman, $subman);
	$query_check_networks = "SELECT * FROM networks WHERE networks.networkGroup = '".$_POST['id']."'";
	$check_networks = mysql_query($query_check_networks, $subman) or die(mysql_error());
	$row_check_networks = mysql_fetch_assoc($check_networks);
	$totalRows_check_networks = mysql_num_rows($check_networks);
	
	if ($totalRows_check_networks == 0) {
		
	  $deleteSQL = sprintf("DELETE FROM networkgroup WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	}
	else {
        	
           $_SESSION['errstr'] .= "The network group was not deleted as it contains networks.  Please update the network(s) first.";
		   $errstrflag = 1;
	}
  
  $insertGoTo = "containerView.php?browse=networks&container=".$_POST['container']."&networkgroups=1&errstr=".$errstrflag;;

  header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "frm_delete_cardtype")) {

	mysql_select_db($database_subman, $subman);
	$query_check_cards = "SELECT * FROM cards WHERE cards.cardtype = ".$_POST['id']."";
	$check_cards = mysql_query($query_check_cards, $subman) or die(mysql_error());
	$row_check_cards = mysql_fetch_assoc($check_cards);
	$totalRows_check_cards = mysql_num_rows($check_cards);
	
	if ($totalRows_check_cards == 0) {
		
	  $deleteSQL = sprintf("DELETE FROM cardtypes WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	}
	else {
        	
           $_SESSION['errstr'] .= "The card type was not deleted as it is currently in use.  Please delete the card(s) first.";
		   $errstrflag = 1;
	}
  
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsecardtypes=1&errstr=".$errstrflag;;

  header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "frm_delete_devicetype")) {

	mysql_select_db($database_subman, $subman);
	$query_check_devices = "SELECT * FROM portsdevices WHERE portsdevices.devicetype = ".$_POST['id']."";
	$check_devices = mysql_query($query_check_devices, $subman) or die(mysql_error());
	$row_check_devices = mysql_fetch_assoc($check_devices);
	$totalRows_check_devices = mysql_num_rows($check_devices);
	
	if ($totalRows_check_devices == 0) {
		
	  $deleteSQL = sprintf("DELETE FROM devicetypes WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
	
	}
	else {
        	
           $_SESSION['errstr'] .= "The device type was not deleted as it is currently in use.  Please delete the device(s) first.";
		   $errstrflag = 1;
	}
  
  $insertGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&browsedevicetypes=1&errstr=".$errstrflag;;

  header(sprintf("Location: %s", $insertGoTo));

}

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

if ((isset($_POST['id'])) && ($_POST['id'] != "") && (isset($_POST['frm_delete_vlanpool']))) {
		
		mysql_select_db($database_subman, $subman);
		$query_check_vlans = "SELECT * FROM vlan WHERE vlan.vlanpool = ".$_POST['id']."";
		$check_vlans = mysql_query($query_check_vlans, $subman) or die(mysql_error());
		$row_check_vlans = mysql_fetch_assoc($check_vlans);
		$totalRows_check_vlans = mysql_num_rows($check_vlans);
		
		if ($totalRows_check_vlans == 0) { 
			  
			  $deleteSQL = sprintf("DELETE FROM vlanpool WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  		
		}
		else {
        	
           $_SESSION['errstr'] .= "The VLAN pool was not deleted as it contains VLANs.  Please delete the VLAN(s) first.";
		   $errstrflag = 1;
			}
		

  $deleteGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&group=".$_POST['group']."&device=".$_POST['device']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_POST['id'])) && ($_POST['id'] != "") && (isset($_POST['frm_delete_vlan']))) {
		
		mysql_select_db($database_subman, $subman);
		$query_check_ports = "SELECT * FROM portsports WHERE portsports.vlanid = ".$_POST['id']."";
		$check_ports = mysql_query($query_check_ports, $subman) or die(mysql_error());
		$row_check_ports = mysql_fetch_assoc($check_ports);
		$totalRows_check_ports = mysql_num_rows($check_ports);
		
		mysql_select_db($database_subman, $subman);
		$query_check_links = "SELECT * FROM links WHERE links.provide_vlan_node_a = ".$_POST['id']." OR links.provide_vlan_node_b = ".$_POST['id'].";";
		$check_links = mysql_query($query_check_links, $subman) or die(mysql_error());
		$row_check_links = mysql_fetch_assoc($check_links);
		$totalRows_check_links = mysql_num_rows($check_links);
		
		if ($totalRows_check_links == 0 && $totalRows_check_ports == 0) { 
			  
			  $deleteSQL = sprintf("DELETE FROM vlan WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  		
		}
		else {
        	
           $_SESSION['errstr'] .= "The VLAN was not deleted as it contains link information/ports.  Please delete the link(s) first.";
		   $errstrflag = 1;
			}
		

  $deleteGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&group=".$_POST['group']."&device=".$_POST['device']."&vlanpool=".$_POST['vlanpool']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_POST['id'])) && ($_POST['id'] != "") && (isset($_POST['frm_delete_card']))) {
		
		mysql_select_db($database_subman, $subman);
		$query_check_ports = "SELECT * FROM portsports WHERE portsports.card = ".$_POST['id']."";
		$check_ports = mysql_query($query_check_ports, $subman) or die(mysql_error());
		$row_check_ports = mysql_fetch_assoc($check_ports);
		$totalRows_check_ports = mysql_num_rows($check_ports);
		
		mysql_select_db($database_subman, $subman);
		$query_check_links = "SELECT * FROM links WHERE links.provide_card_node_a = ".$_POST['id']." OR links.provide_card_node_b = ".$_POST['id'].";";
		$check_links = mysql_query($query_check_links, $subman) or die(mysql_error());
		$row_check_links = mysql_fetch_assoc($check_links);
		$totalRows_check_links = mysql_num_rows($check_links);
		
		if ($totalRows_check_links == 0 && $totalRows_check_ports == 0) { 
			  
			  $deleteSQL = sprintf("DELETE FROM cards WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  		
		}
		else {
        	
           $_SESSION['errstr'] .= "The line card was not deleted as it contains link information/ports.  Please delete the link(s) first.";
		   $errstrflag = 1;
			}
		

  $deleteGoTo = "containerView.php?browse=devices&container=".$_POST['container']."&group=".$_POST['group']."&device=".$_POST['device']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_POST['id'])) && ($_POST['id'] != "") && (isset($_POST['frm_delete_ripe']))) {
		
		mysql_select_db($database_subman, $subman);
		$query_check_ripe = "SELECT * FROM networks WHERE networks.ripeBlock = ".$_POST['id']."";
		$check_ripe = mysql_query($query_check_ripe, $subman) or die(mysql_error());
		$row_check_ripe = mysql_fetch_assoc($check_ripe);
		$totalRows_check_ripe = mysql_num_rows($check_ripe);
		
		if ($totalRows_check_ripe == 0) { 
			  
			  $deleteSQL = sprintf("DELETE FROM ripe WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  
		}
		else {
        	
           $_SESSION['errstr'] .= "The allocation was not deleted as it contains networks.  Please remove the networks from the allocation first.";
		   $errstrflag = 1;
			}
		

  $deleteGoTo = "containerView.php?browse=ripe&container=".$_POST['container']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['MM_delete']) && $_POST['MM_delete'] == "frm_delete_rtpool") {
		
		mysql_select_db($database_subman, $subman);
		$query_check_rts = "SELECT * FROM rt WHERE rt.rtpool = ".$_POST['id']."";
		$check_rts = mysql_query($query_check_rts, $subman) or die(mysql_error());
		$row_check_rts = mysql_fetch_assoc($check_rts);
		$totalRows_check_rts = mysql_num_rows($check_rts);
		
		if ($totalRows_check_rts == 0) { 
			  
			  $deleteSQL = sprintf("DELETE FROM rtpool WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  			  		
		}
		else {
        	
           $_SESSION['errstr'] .= "The RT pool was not deleted as it contains RT(s).  Please remove the RT(s) first.";
		   $errstrflag = 1;
			}
		

  $deleteGoTo = "containerView.php?browse=vpns&container=".$_POST['container']."&provider=".$_POST['provider']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['MM_delete']) && $_POST['MM_delete'] == "frm_delete_rdpool") {
		
		mysql_select_db($database_subman, $subman);
		$query_check_rds = "SELECT * FROM rd WHERE rd.rdpool = ".$_POST['id']."";
		$check_rds = mysql_query($query_check_rds, $subman) or die(mysql_error());
		$row_check_rds = mysql_fetch_assoc($check_rds);
		$totalRows_check_rds = mysql_num_rows($check_rds);
		
		if ($totalRows_check_rds == 0) { 
			  
			  $deleteSQL = sprintf("DELETE FROM rdpool WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  			  		
		}
		else {
        	
           $_SESSION['errstr'] .= "The RD pool was not deleted as it contains RD(s).  Please remove the RD(s) first.";
		   $errstrflag = 1;
			}
		

  $deleteGoTo = "containerView.php?browse=vpns&container=".$_POST['container']."&provider=".$_POST['provider']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['MM_delete']) && $_POST['MM_delete'] == "frm_delete_pseudowirepool") {
		
		mysql_select_db($database_subman, $subman);
		$query_check_xconnects = "SELECT * FROM xconnectid WHERE xconnectid.xconnectpool = ".$_POST['id']."";
		$check_xconnects = mysql_query($query_check_xconnects, $subman) or die(mysql_error());
		$row_check_xconnects = mysql_fetch_assoc($check_xconnects);
		$totalRows_check_xconnects = mysql_num_rows($check_xconnects);
		
		if ($totalRows_check_xconnects == 0) { 
			  
			  $deleteSQL = sprintf("DELETE FROM xconnectpool WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  			  		
		}
		else {
        	
           $_SESSION['errstr'] .= "The pseudowire pool was not deleted as it contains pseudowire(s).  Please remove the pseudowire(s) first.";
		   $errstrflag = 1;
			}
		

  $deleteGoTo = "containerView.php?browse=vpns&container=".$_POST['container']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

if (isset($_POST['MM_delete']) && $_POST['MM_delete'] == "frm_delete_provider") {
		
		mysql_select_db($database_subman, $subman);
		$query_check_providervpns = "SELECT * FROM providervpn WHERE providervpn.vpn = ".$_POST['id']."";
		$check_providervpns = mysql_query($query_check_providervpns, $subman) or die(mysql_error());
		$row_check_providervpns = mysql_fetch_assoc($check_providervpns);
		$totalRows_check_providervpns = mysql_num_rows($check_providervpns);
		
		mysql_select_db($database_subman, $subman);
		$query_check_rdpools = "SELECT * FROM rdpool WHERE rdpool.provider = ".$_POST['id']."";
		$check_rdpools = mysql_query($query_check_rdpools, $subman) or die(mysql_error());
		$row_check_rdpools = mysql_fetch_assoc($check_rdpools);
		$totalRows_check_rdpools = mysql_num_rows($check_rdpools);
		
		mysql_select_db($database_subman, $subman);
		$query_check_rtpools = "SELECT * FROM rtpool WHERE rtpool.provider = ".$_POST['id']."";
		$check_rtpools = mysql_query($query_check_rtpools, $subman) or die(mysql_error());
		$row_check_rtpools = mysql_fetch_assoc($check_rtpools);
		$totalRows_check_rtpools = mysql_num_rows($check_rtpools);
		
		if ($totalRows_check_providervpns == 0 && $totalRows_check_rdpools == 0 && $totalRows_check_rtpools == 0) { 
			  
			  $deleteSQL = sprintf("DELETE FROM provider WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  			  		
		}
		else {
        	
           $_SESSION['errstr'] .= "The provider was not deleted as it contains link VPN(s), RD pool(s) or RT pool(s).  Please remove these items first.";
		   $errstrflag = 1;
			}
		

  $deleteGoTo = "containerView.php?browse=vpns&container=".$_POST['container']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_POST['id'])) && ($_POST['id'] != "") && (isset($_POST['frm_delete_vpn']))) {
		
		mysql_select_db($database_subman, $subman);
		$query_check_vrfs = "SELECT * FROM vpnvrf WHERE vpnvrf.vpn = ".$_POST['id']."";
		$check_vrfs = mysql_query($query_check_vrfs, $subman) or die(mysql_error());
		$row_check_vrfs = mysql_fetch_assoc($check_vrfs);
		$totalRows_check_vrfs = mysql_num_rows($check_vrfs);
		
		mysql_select_db($database_subman, $subman);
		$query_check_rts = "SELECT * FROM rtvpn WHERE rtvpn.vpn = ".$_POST['id']."";
		$check_rts = mysql_query($query_check_rts, $subman) or die(mysql_error());
		$row_check_rts = mysql_fetch_assoc($check_rts);
		$totalRows_check_rts = mysql_num_rows($check_rts);
		
		mysql_select_db($database_subman, $subman);
		$query_check_links = "SELECT * FROM links WHERE links.provide_vpn = ".$_POST['id'].";";
		$check_links = mysql_query($query_check_links, $subman) or die(mysql_error());
		$row_check_links = mysql_fetch_assoc($check_links);
		$totalRows_check_links = mysql_num_rows($check_links);
		
		if ($totalRows_check_links == 0 && $totalRows_check_rts == 0 && $totalRows_check_vrfs == 0) { 
			  
			  $deleteSQL = sprintf("DELETE FROM vpn WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  
			  $deleteSQL = sprintf("DELETE FROM providervpn WHERE vpn=%s",
								   GetSQLValueString($_POST['rd'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  
			  $deleteSQL = sprintf("DELETE FROM vpncustomer WHERE vpn=%s",
								   GetSQLValueString($_POST['rd'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  		
		}
		else {
        	
           $_SESSION['errstr'] .= "The VPN was not deleted as it contains link information/VRFs, or route-targets.  Please delete the link(s)/RTs first.";
		   $errstrflag = 1;
			}
		

  $deleteGoTo = "containerView.php?browse=vpns&container=".$_POST['container']."&provider=".$_POST['provider']."&vpn=".$_POST['vpn']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_POST['id'])) && ($_POST['id'] != "") && (isset($_POST['frm_delete_vrf']))) {
		
		mysql_select_db($database_subman, $subman);
		$query_getVrf = "SELECT * FROM vrf WHERE vrf.id = ".$_POST['id']."";
		$getVrf = mysql_query($query_getVrf, $subman) or die(mysql_error());
		$row_getVrf = mysql_fetch_assoc($getVrf);
		$totalRows_getVrf = mysql_num_rows($getVrf);
			
		mysql_select_db($database_subman, $subman);
		$query_check_links = "SELECT * FROM links WHERE links.provide_vrf = ".$_POST['id'].";";
		$check_links = mysql_query($query_check_links, $subman) or die(mysql_error());
		$row_check_links = mysql_fetch_assoc($check_links);
		$totalRows_check_links = mysql_num_rows($check_links);
		
		if ($totalRows_check_links == 0) { 
			  
			  $deleteSQL = sprintf("DELETE FROM vrf WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  
			  $deleteSQL = sprintf("DELETE FROM rd WHERE id=%s",
								   GetSQLValueString($row_getVrf['rd'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  
			  $deleteSQL = sprintf("DELETE FROM vpnvrf WHERE vrf=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			mysql_select_db($database_subman, $subman);
			$Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
		}
		else {
        	
           $_SESSION['errstr'] .= "The VRF was not deleted as it contains link information.  Please delete the link(s) first.";
		   $errstrflag = 1;
			}
		

  $deleteGoTo = "containerView.php?browse=vpns&container=".$_POST['container']."&provider=".$_POST['provider']."&vpn=".$_POST['vpn']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_POST['id'])) && ($_POST['id'] != "") && (isset($_POST['frm_delete_network']))) {
		
		mysql_select_db($database_subman, $subman);
		$query_check_routes = "SELECT * FROM routes WHERE routes.network = ".$_POST['id'].";";
		$check_routes = mysql_query($query_check_routes, $subman) or die(mysql_error());
		$row_check_routes = mysql_fetch_assoc($check_routes);
		$totalRows_check_routes = mysql_num_rows($check_routes);
		
		mysql_select_db($database_subman, $subman);
		$query_check_routes1 = "SELECT * FROM linknets WHERE linknets.network = ".$_POST['id'].";";
		$check_routes1 = mysql_query($query_check_routes1, $subman) or die(mysql_error());
		$row_check_routes1 = mysql_fetch_assoc($check_routes1);
		$totalRows_check_routes1 = mysql_num_rows($check_routes1);
		
		mysql_select_db($database_subman, $subman);
		$query_check_links = "SELECT * FROM links WHERE links.provide_network = ".$_POST['id'].";";
		$check_links = mysql_query($query_check_links, $subman) or die(mysql_error());
		$row_check_links = mysql_fetch_assoc($check_links);
		$totalRows_check_links = mysql_num_rows($check_links);
		
		mysql_select_db($database_subman, $subman);
		$query_check_linknets = "SELECT * FROM linknetworks WHERE linknetworks.network = ".$_POST['id'].";";
		$check_linknets = mysql_query($query_check_linknets, $subman) or die(mysql_error());
		$row_check_linknets = mysql_fetch_assoc($check_linknets);
		$totalRows_check_linknets = mysql_num_rows($check_linknets);
		
		if ($totalRows_check_routes == 0 && $totalRows_check_routes1 == 0 && $totalRows_check_links == 0 && $totalRows_check_linknets == 0) { 
		
			mysql_select_db($database_subman, $subman);
			$query_getNetwork = "SELECT * FROM networks WHERE networks.id = ".$_POST['id'].";";
			$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
			$row_getNetwork = mysql_fetch_assoc($getNetwork);
			$totalRows_getNetwork = mysql_num_rows($getNetwork);
			
	  
			  $updateSQL = sprintf("UPDATE networks SET parent = ".$row_getNetwork['parent']." WHERE parent=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
			  
			  $deleteSQL = sprintf("DELETE FROM networks WHERE id=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  
			   $deleteSQL = sprintf("DELETE FROM linknetworks WHERE network=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			  
			  $deleteSQL = sprintf("DELETE FROM addresses WHERE network=%s",
								   GetSQLValueString($_POST['id'], "int"));
			
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
			
			  $deleteSQL = sprintf("DELETE FROM reportobjects WHERE reporttype = 'network' AND objectid=%s",
							   GetSQLValueString($_POST['id'], "int"));
		
			  mysql_select_db($database_subman, $subman);
			  $Result1 = mysql_query($deleteSQL, $subman) or die(mysql_error());
		
		}
		else {
        	if ($totalRows_check_routes > 0) {
				
			   $_SESSION['errstr'] .= "The network was not deleted as there is a subscriber route for the user ".$row_check_routes['subscriber'].".  Please delete the route first.";
			   $errstrflag = 1;
			   
			}
			if ($totalRows_check_routes1 > 0) {
				
				$_SESSION['errstr'] .= "The network was not deleted as it is attached to a link as a route. Please delete the route first (click the link icon next to the network description when browsing the network to view the link).";
			   $errstrflag = 1;
			   
			}
			if ($totalRows_check_links > 0) {
				
				$_SESSION['errstr'] .= "The network was not deleted as it is being used by a link. Please delete the link first (click the link icon next to the network description when browsing the network to view the link).";
			   $errstrflag = 1;
			   
			}
			if ($totalRows_check_linknets > 0) {
				
				$_SESSION['errstr'] .= "The network was not deleted as it is being used by a link. Please delete the link first (click the link icon next to the network description when browsing the network to view the link).";
			   $errstrflag = 1;
			   
			}
			
			}
		

  $deleteGoTo = "containerView.php?browse=networks&container=".$_POST['container']."&group=".$_POST['group']."&errstr=".$errstrflag;
  header(sprintf("Location: %s", $deleteGoTo));
}

?>