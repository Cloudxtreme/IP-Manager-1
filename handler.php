<?php require_once('Connections/subman.php'); 
require_once('includes/standard_functions.php'); ?>
<?php
session_start();

switch($_GET['action']) {
	
	case 'browseport':
		$_SESSION['_browseport_address'] = $_GET['address'];
		$_SESSION['_browseport_container'] = $_GET['container'];
		header("Location: containerView.php?browse=devices&container=".$_GET['container']);
		break;
	
	case 'browseport_cancel':
		mysql_select_db($database_subman, $subman);
		$query_address = "SELECT addresses.* FROM addresses WHERE addresses.id = ".$_SESSION['_browseport_address']."";
		$address = mysql_query($query_address, $subman) or die(mysql_error());
		$row_address = mysql_fetch_assoc($address);
		$totalRows_address = mysql_num_rows($address);
		
		$container = $_SESSION['_browseport_container'];
		
		unset($_SESSION["_browseport_address"]);
		unset($_SESSION["_browseport_container"]);
		header("Location: containerView.php?browse=networks&container=".$container."&parent=".$row_address['network']);
		break;
		
	case 'browseport_setaddress':
		if ($_GET['portid'] != "") {
			$updateSQL = "UPDATE portsports SET router = ".$_SESSION['_browseport_address']." WHERE id = ".$_GET['portid'].";";
		
			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
		}
		elseif ($_GET['subint'] != "") {
			$updateSQL = "UPDATE subint SET router = ".$_SESSION['_browseport_address']." WHERE id = ".$_GET['subint'].";";
		
			mysql_select_db($database_subman, $subman);
  			$Result1 = mysql_query($updateSQL, $subman) or die(mysql_error());
		}
		
		mysql_select_db($database_subman, $subman);
		$query_address = "SELECT addresses.* FROM addresses WHERE addresses.id = ".$_SESSION['_browseport_address']."";
		$address = mysql_query($query_address, $subman) or die(mysql_error());
		$row_address = mysql_fetch_assoc($address);
		$totalRows_address = mysql_num_rows($address);
		
		$container = $_SESSION['_browseport_container'];
		
		unset($_SESSION["_browseport_address"]);
		unset($_SESSION["_browseport_container"]);
		header("Location: containerView.php?browse=networks&container=".$container."&parent=".$row_address['network']);
		break;
	
	case 'provide_cancel':
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
		unset($_SESSION['provide_address']);
		unset($_SESSION['provide_address_node_a']);
		unset($_SESSION['provide_address_node_b']);
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
		
		header("Location: containerView.php?browse=devices&container=".$_GET['container']);
		break;
		
}

if (isset($_POST['provide']) && $_POST['provide'] == "step0") {
	
	if (!isset($_POST['wizard_summary'])) {
		$_SESSION['provide_template'] = $_POST['template'];
	}
	$_SESSION['provide_step'] = 1;
	
	header("Location: containerView.php?browse=devices&container=".$_POST['container']);
	
}

if (isset($_POST['provide']) && $_POST['provide'] == "step1") {
	
	if (!isset($_POST['wizard_summary'])) {
		$_SESSION['provide_node_a'] = $_POST['node_a'];
		$_SESSION['provide_node_b'] = $_POST['node_b'];
		$_SESSION['provide_layer'] = $_POST['layer'];
		$_SESSION['provide_logical_node_a'] = $_POST['logical_node_a'];
		$_SESSION['provide_logical_node_b'] = $_POST['logical_node_b'];	
		$_SESSION['provide_cct'] = $_POST['cct'];
		$_SESSION['provide_customer'] = $_POST['customer'];
	}
	$_SESSION['provide_step'] = 2;
	
	header("Location: containerView.php?browse=devices&container=".$_POST['container']);
}

if (isset($_POST['provide']) && $_POST['provide'] == "step2") {
	
	if (!isset($_POST['wizard_summary'])) {
		$_SESSION['provide_vpn'] = $_POST['vpn'];	
		$_SESSION['provide_vlan_node_a'] = $_POST['vlan_node_a'];
		$_SESSION['provide_vlan_node_b'] = $_POST['vlan_node_b'];
		if ($_SESSION['provide_layer'] == 3 && $_POST['vlan_node_a'] != "") {
			$_SESSION['provide_logical_node_a'] = "subint";
		}
		if ($_SESSION['provide_layer'] == 3 && (($_POST['vlan_node_b'] != "" && $_POST['vlan_node_b'] != "same") || ($_POST['vlan_node_a'] != "" && $_POST['vlan_node_b'] == "same"))) {
			$_SESSION['provide_logical_node_b'] = "subint";
		}
	}
	$_SESSION['provide_step'] = 3;
	
	header("Location: containerView.php?browse=devices&container=".$_POST['container']);
}

if (isset($_POST['provide']) && $_POST['provide'] == "step3") {
	
	if (!isset($_POST['wizard_summary'])) {
	
		if (isset($_POST['additional_address']) && $_POST['additional_address'] == 1) {
		
			$_SESSION['additional_address'] = 1;
			$_SESSION['additional_address_link'] = $_POST['additional_address_link'];
			$_SESSION['provide_node_a'] = $_POST['node_a'];
			$_SESSION['provide_node_b'] = $_POST['node_b'];
			$_SESSION['provide_layer'] = $_POST['layer'];
			$_SESSION['provide_logical_node_a'] = $_POST['logical_node_a'];
			$_SESSION['provide_logical_node_b'] = $_POST['logical_node_b'];	
			$_SESSION['provide_cct'] = $_POST['cct'];
			$_SESSION['provide_customer'] = $_POST['customer'];
			$_SESSION['provide_vpn'] = $_POST['vpn'];	
			$_SESSION['provide_vlan_node_a'] = $_POST['vlan_node_a'];
			$_SESSION['provide_vlan_node_b'] = $_POST['vlan_node_b'];
			$_SESSION['provide_xconnectpool'] = $_POST['xconnectpool'];
			$_SESSION['manual_xconnect'] = $_POST['manual_xconnect'];
			$_SESSION['provide_vrf'] = $_POST['vrf'];
			$_SESSION['provide_card_node_a'] = $_POST['card_node_a'];
			$_SESSION['provide_card_node_b'] = $_POST['card_node_b'];
			$_SESSION['provide_timeslots_node_a'] = $_POST['timeslots_node_a'];
			$_SESSION['provide_timeslots_node_b'] = $_POST['timeslots_node_b'];
			$_SESSION['provide_subint_node_a'] = $_POST['subint_node_a'];
			$_SESSION['provide_subint_node_b'] = $_POST['subint_node_b'];
			$_SESSION['provide_port_node_a'] = $_POST['port_node_a'];
			$_SESSION['provide_port_node_b'] = $_POST['port_node_b'];
			$_SESSION['provide_template'] = $_POST['template'];
		
		}
	
		else {
			
			$_SESSION['provide_xconnectpool'] = $_POST['xconnectpool'];
			$_SESSION['manual_xconnect'] = $_POST['manual_xconnect'];
			$_SESSION['provide_vrf'] = $_POST['vrf'];
			$_SESSION['vlannumber_a'] = $_POST['vlannumber_a'];
			$_SESSION['vlannumber_b'] = $_POST['vlannumber_b'];
			$_SESSION['pece'] = $_POST['pece'];

		}
		
		
	}
		
	$_SESSION['provide_step'] = 4;
		
	header("Location: containerView.php?browse=devices&container=".$_POST['container']);
}

if (isset($_POST['provide']) && $_POST['provide'] == "step4") {
	
	if (!isset($_POST['wizard_summary'])) {
		$_SESSION['provide_netgroup'] = $_POST['netgroup'];
		$_SESSION['xconnectid'] = $_POST['xconnectid'];
		#$_SESSION['provide_netsize'] = $_POST['netsize'];
	}
	else {
		unset($_SESSION['provide_existing']);
	}
	$_SESSION['provide_step'] = 5;
	
	header("Location: containerView.php?browse=devices&container=".$_POST['container']);
}

if (isset($_POST['provide']) && $_POST['provide'] == "step5") {
	
	if (!isset($_POST['wizard_summary'])) {
	
	$_SESSION['provide_parent'] = $_POST['parent'];
	
	if ($_POST['existing'] == 1) {
		
		mysql_select_db($database_subman, $subman);
		$query_checkNetwork = "SELECT * FROM networks WHERE networks.parent = ".$_SESSION['provide_parent']."";
		$checkNetwork = mysql_query($query_checkNetwork, $subman) or die(mysql_error());
		$row_checkNetwork = mysql_fetch_assoc($checkNetwork);
		$totalRows_checkNetwork = mysql_num_rows($checkNetwork);
		
		mysql_select_db($database_subman, $subman);
		$query_getNetwork = "SELECT * FROM networks WHERE networks.id = ".$_SESSION['provide_parent']."";
		$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
		$row_getNetwork = mysql_fetch_assoc($getNetwork);
		$totalRows_getNetwork = mysql_num_rows($getNetwork);
		
		mysql_select_db($database_subman, $subman);
		$query_checkAddresses = "SELECT * FROM addresses WHERE addresses.network = ".$row_getNetwork['id']."";
		$checkAddresses = mysql_query($query_checkAddresses, $subman) or die(mysql_error());
		$row_checkAddresses = mysql_fetch_assoc($checkAddresses);
		$totalRows_checkAddresses = mysql_num_rows($checkAddresses);
		
		if ($row_getNetwork['v6mask'] == "") {
			$net = find_net(long2ip($row_getNetwork['network']),$row_getNetwork['mask']);
			$addressesavailable = $net['total']-$totalRows_checkAddresses;
		}
		else {
			$addressesavailable = bcsub(bcsub(bcadd($row_subnets['network'],bcpow(2,(128 - $row_subnets['v6mask']))),$row_getNetwork['network']),$totalRows_checkAddresses);
		}
		
		#function get_slash ($mask) {
	
			#$bits=strpos(decbin(ip2long($mask)),"0"); 
			
			#return "/".$bits;
		
		#}

		if ($totalRows_checkNetwork == 0) {
			if (($_SESSION['provide_node_b'] == "" && $addressesavailable > 0) || ($_SESSION['provide_node_b'] != "" && $addressesavailable > 1)) {
				
				$_SESSION['provide_existing'] = $_POST['existing'];
				$_SESSION['provide_network'] = $row_getNetwork['network'];
				if ($row_getNetwork['v6mask'] == "") {
					$_SESSION['provide_netsize'] = str_replace("/","",get_slash($row_getNetwork['mask']));
				}
				else {
					$_SESSION['provide_netsize'] = str_replace("/","",$row_getNetwork['v6mask']);
				}
				#$_SESSION['provide_step'] = 7;
				
			}
			
		}
		#else {
			$_SESSION['provide_step'] = 6;
		#}
		
	}
	else {
		$_SESSION['provide_step'] = 6;
		$_SESSION['provide_netsize'] = $_POST['netsize'];
	}
	
	}
	else {
		$_SESSION['provide_step'] = 6;
	}
	
	header("Location: containerView.php?browse=devices&container=".$_POST['container']);
}

if (isset($_POST['provide']) && $_POST['provide'] == "step6") {
	
	if (!isset($_POST['wizard_summary'])) {
	
	if ($_SESSION['provide_existing'] != 1) { 
		$_SESSION['provide_network'] = $_POST['network'];
	}
	$_SESSION['provide_address'] = $_POST['manualaddress'];
	
	}
	$_SESSION['provide_step'] = 7;
	
	header("Location: containerView.php?browse=devices&container=".$_POST['container']);
}

if (isset($_POST['provide']) && $_POST['provide'] == "step7") {
	
	if ($_SESSION['additional_address'] == 1) {
		
		if (!isset($_POST['wizard_summary'])) {
			$_SESSION['provide_address_node_a'] = $_POST['address_node_a'];
			$_SESSION['provide_address_node_b'] = $_POST['address_node_b'];
		}
		$_SESSION['provide_step'] = 9;
	}
	else {
		
		if (!isset($_POST['wizard_summary'])) {
			$_SESSION['provide_address_node_a'] = $_POST['address_node_a'];
			$_SESSION['provide_address_node_b'] = $_POST['address_node_b'];
			$_SESSION['provide_card_node_a'] = $_POST['card_node_a'];
			$_SESSION['provide_card_node_b'] = $_POST['card_node_b'];
			$_SESSION['provide_timeslots_node_a'] = $_POST['timeslots_node_a'];
			$_SESSION['provide_timeslots_node_b'] = $_POST['timeslots_node_b'];
			$_SESSION['provide_subint_node_a'] = $_POST['subint_node_a'];
			$_SESSION['provide_subint_node_b'] = $_POST['subint_node_b'];
		}
		$_SESSION['provide_step'] = 8;
		
	}
	
	header("Location: containerView.php?browse=devices&container=".$_POST['container']);
}

if (isset($_POST['provide']) && $_POST['provide'] == "step8") {
	
	if (!isset($_POST['wizard_summary'])) {
	
		$_SESSION['provide_port_node_a'] = $_POST['port_node_a'];
		$_SESSION['provide_port_node_b'] = $_POST['port_node_b'];
	
		mysql_select_db($database_subman, $subman);
		$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
		$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
		$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
		$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
	
		do {
			$_SESSION['arbitrary'.$row_arbitraryfields['id']] = $_POST['arbitrary'.$row_arbitraryfields['id']];
		} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
	
	}
	$_SESSION['provide_step'] = 9;
	
	header("Location: containerView.php?browse=devices&container=".$_POST['container']);
	
}
if (isset($_POST['provide']) && $_POST['provide'] == "step9") {
	
	$_SESSION['provide_step'] = 10;
	
	header("Location: containerView.php?browse=devices&container=".$_POST['container']);
}
if (isset($_POST['provide']) && $_POST['provide'] == "step10") {
	
	mysql_select_db($database_subman, $subman);
	$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
	$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
	$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
	$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
	
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
	
	if ($_POST['templatelink']) {
		$_SESSION['provide_template'] = $_POST['templatelink'];
		$_SESSION['provide_step'] = 1;
		$_SESSION['templatelink'] = $_POST['templatelink'];
		$_SESSION['linked'] = $_POST['linked'];
		header("Location: containerView.php?&browse=devices&container=".$_POST['container']);
	}
	else {
	
		do {
			unset($_SESSION['arbitrary'.$row_arbitraryfields['id']]);
		} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
	
		header("Location: containerView.php?&browse=devices&container=".$_POST['container']."&device=".$_POST['device']."&port=".$_POST['port']."&linkview=1");
	}
	
}
?>