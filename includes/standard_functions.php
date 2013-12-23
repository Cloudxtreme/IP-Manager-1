<?php

##### GLOBAL VARIABLES #######
$scriptpath = '/Users/gareth/Sites/subman_v2/scripts/';

##############################


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

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  session_destroy();
  #session_unregister($_SESSION['MM_Username']);
  #session_unregister($_SESSION['MM_UserGroup']);
  #session_unregister($_SESSION['PrevUrl']);
	
  $logoutGoTo = "login.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}




function getContainerLevel ($container = "",$user) {

	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupcontainerpermissions.level FROM usrgroupcontainerpermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupcontainerpermissions.usrgroup LEFT JOIN container ON container.id = usrgroupcontainerpermissions.container LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND container.id = '".$container."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
	
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getNetGroupLevel ($netgroup = "",$user) {
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupnetgrouppermissions.level FROM usrgroupnetgrouppermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupnetgrouppermissions.usrgroup LEFT JOIN networkgroup ON networkgroup.id = usrgroupnetgrouppermissions.networkgroup LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND networkgroup.id = '".$netgroup."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
	
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getNetworkLevel ($network = "",$user) {
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupnetworkpermissions.level FROM usrgroupnetworkpermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupnetworkpermissions.usrgroup LEFT JOIN networks ON networks.id = usrgroupnetworkpermissions.network LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND networks.id = '".$network."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
		
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getDeviceGroupLevel ($group = "",$user) {
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupdevicegrouppermissions.level FROM usrgroupdevicegrouppermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupdevicegrouppermissions.usrgroup LEFT JOIN portgroups ON portgroups.id = usrgroupdevicegrouppermissions.devicegroup LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND portgroups.id = '".$group."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
		
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getDeviceLevel ($device = "",$user) {
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupdevicepermissions.level FROM usrgroupdevicepermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupdevicepermissions.usrgroup LEFT JOIN portsdevices ON portsdevices.id = usrgroupdevicepermissions.device LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND portsdevices.id = '".$device."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
		
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getProviderLevel ($provider = "",$user) {
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupproviderpermissions.level FROM usrgroupproviderpermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupproviderpermissions.usrgroup LEFT JOIN provider ON provider.id = usrgroupproviderpermissions.provider LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND provider.id = '".$provider."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
		
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getVpnLevel ($vpn = "",$user) {
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupvpnpermissions.level FROM usrgroupvpnpermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupvpnpermissions.usrgroup LEFT JOIN vpn ON vpn.id = usrgroupvpnpermissions.vpn LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND vpn.id = '".$vpn."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
		
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getRadiatorLevel ($radiator = "",$user) {
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupradiatorpermissions.level FROM usrgroupradiatorpermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupradiatorpermissions.usrgroup LEFT JOIN radiator ON radiator.id = usrgroupradiatorpermissions.radiator LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND radiator.id = '".$radiator."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
		
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getRadiatorGroupLevel ($radiator = "", $group, $user) {
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupradgrouppermissions.level FROM usrgroupradgrouppermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupradgrouppermissions.usrgroup LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND usrgroupradgrouppermissions.radiator = '".$radiator."' AND usrgroupradgrouppermissions.subscribergrp = '".$group."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
		
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getRadiatorFeatureLevel ($radiator = "", $feature, $user) {
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupradfeaturepermissions.level FROM usrgroupradfeaturepermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupradfeaturepermissions.usrgroup LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND usrgroupradfeaturepermissions.radiator = '".$radiator."' AND usrgroupradfeaturepermissions.feature = '".$feature."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
		
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getDamitLevel ($damit = "",$user) {
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupdamitpermissions.level FROM usrgroupdamitpermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupdamitpermissions.usrgroup LEFT JOIN damit ON damit.id = usrgroupdamitpermissions.damit LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND damit.id = '".$damit."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
		
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getObserviumLevel ($observium = "",$user) {
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getLevel_level = "SELECT usrgroupobserviumpermissions.level FROM usrgroupobserviumpermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupobserviumpermissions.usrgroup LEFT JOIN observium ON observium.id = usrgroupobserviumpermissions.observium LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND observium.id = '".$observium."';";
	$getLevel_level = mysql_query($query_getLevel_level, $subman) or die(mysql_error());
	$row_getLevel_level = mysql_fetch_assoc($getLevel_level);
	$totalRows_getLevel_level = mysql_num_rows($getLevel_level);
		
	return $row_getLevel_level['level'];
	
	mysql_free_result($getLevel_level);
}

function getPermission ($level) {

	switch($level) {
		case 0: $permission = "None";
		break;
		case ($level <= 10): $permission = "Read Only";
		break;
		case ($level <= 20): $permission = "Read, Modify";
		break;
		case ($level <= 30): $permission = "Read, Modify, Delete";
		break;
		case 127: $permission = "Superuser";
	}
	
	return $permission;
	
}

function getVPNLayer ($layer) {

	switch($layer) {
		case 2: $vpnlayer = "(EoMPLS)";
		break;
		case 3: $vpnlayer = "(IP VPN)";
		break;
		case 4: $vpnlayer = "(VPLS)";
		break;
	}
	
	return $vpnlayer;
	
}

function total_addresses($mask) {
	
	$bits=strpos(decbin(ip2long($mask)),"0");
   
    if ($bits == "") { $bits = 32; }
	
	return pow(2,(32-$bits));
	
}

function find_net($host,$mask) { 

   $bits=strpos(decbin(ip2long($mask)),"0");
   
   if ($bits == "") { $bits = 32; }
   
   $net["cidr"]=gethostbyname($host)."/".$bits; 

   $net["network"]=bindec(decbin(ip2long(gethostbyname($host))) & decbin(ip2long($mask))); 

   $binhost=str_pad(decbin(ip2long(gethostbyname($host))),32,"0",STR_PAD_LEFT); 
   $binmask=str_pad(decbin(ip2long($mask)),32,"0",STR_PAD_LEFT); 
   for ($i=0; $i<32; $i++) { 
      if (substr($binhost,$i,1)=="1" || substr($binmask,$i,1)=="0") { 
         $broadcast.="1"; 
      }  else { 
         $broadcast.="0"; 
      } 
   } 
   $net["broadcast"]=bindec($broadcast);
   $net["firstaddress"] = $net["network"]+1;
   $net["lastaddress"] = $net["broadcast"]-1;
   $net["total"] = ($net["broadcast"] - $net["network"]) -1;

   return $net; 
}

function get_slash ($mask,$slashflag = 1) {
	
	$bits=strpos(decbin(ip2long($mask)),"0"); 
	
	if ($slashflag == 0) {
		return $bits;
	}
	else {
		return "/".$bits;
	}

}

function get_dotted_mask ($bits) {
	
	$slash = array(
		32 => "255.255.255.255",
		31 => "255.255.255.254",
		30 => "255.255.255.252",
		29 => "255.255.255.248",
		28 => "255.255.255.240",
		27 => "255.255.255.224",
		26 => "255.255.255.192",
		25 => "255.255.255.128",
		24 => "255.255.255.0",
		23 => "255.255.254.0",
		22 => "255.255.252.0",
		21 => "255.255.248.0",
		20 => "255.255.240.0",
		19 => "255.255.224.0",
		18 => "255.255.192.0",
		17 => "255.255.128.0",
		16 => "255.255.0.0",
		15 => "255.254.0.0",
		14 => "255.252.0.0",
		13 => "255.248.0.0",
		12 => "255.240.0.0",
		11 => "255.224.0.0",
		10 => "255.192.0.0",
		9 => "255.128.0.0",
		8 => "255.0.0.0");	
	
	return ($slash[$bits]);
	
}

function get_day ($day) {
	
	$slash = array(
		1 => "Sunday",
		2 => "Monday",
		3 => "Tuesday",
		4 => "Wednesday",
		5 => "Thursday",
		6 => "Friday",
		7 => "Saturday");	
	
	return ($slash[$day]);
	
}

function get_stage ($stage) {
	
	$slash = array(
		1 => "[1] PPS Anomaly",
		2 => "[2] Netflow Data Collection",
		3 => "[3] Netflow Analysis",
		4 => "[4] Attack Mitigation",
		5 => "[5] Closed",
		6 => "[6] Closed with Exceptions",
		7 => "[7] Attack Closed");	
	
	return ($slash[$stage]);
	
}

function ipv62long($hex) {
	
	list($_7,$_6,$_5,$_4,$_3,$_2,$_1,$_0) = split(":",$hex);
	
	$dec7 = bcmul(hexdec($_7), bcpow(65536,7));
	$dec6 = bcmul(hexdec($_6), bcpow(65536,6));
	$dec5 = bcmul(hexdec($_5), bcpow(65536,5));
	$dec4 = bcmul(hexdec($_4), bcpow(65536,4));
	$dec3 = bcmul(hexdec($_3), bcpow(65536,3));
	$dec2 = bcmul(hexdec($_2), bcpow(65536,2));
	$dec1 = bcmul(hexdec($_1), bcpow(65536,1));
	$dec0 = bcmul(hexdec($_0), bcpow(65536,0));
	
	$dec = bcadd($dec7,$dec6);
	$dec = bcadd($dec,$dec5);
	$dec = bcadd($dec,$dec4);
	$dec = bcadd($dec,$dec3);
	$dec = bcadd($dec,$dec2);
	$dec = bcadd($dec,$dec1);
	$dec = bcadd($dec,$dec0);
	
	require('Connections/subman.php');
	
	mysql_select_db($database_subman, $subman);
	$query_getDec = "SELECT (CAST(CONV('".$_7."',16,10) AS DECIMAL(39,0)) * CAST(POW(65536,7) AS DECIMAL(39,0))) +
	(CAST(CONV('".$_6."',16,10) AS DECIMAL(39,0)) * CAST(POW(65536,6) AS DECIMAL(39,0))) +
	(CAST(CONV('".$_5."',16,10) AS DECIMAL(39,0)) * CAST(POW(65536,5) AS DECIMAL(39,0))) +
	(CAST(CONV('".$_4."',16,10) AS DECIMAL(39,0)) * CAST(POW(65536,4) AS DECIMAL(39,0))) +
	(CAST(CONV('".$_3."',16,10) AS DECIMAL(39,0)) * CAST(POW(65536,3) AS DECIMAL(39,0))) +
	(CAST(CONV('".$_2."',16,10) AS DECIMAL(39,0)) * CAST(POW(65536,2) AS DECIMAL(39,0))) +
	(CAST(CONV('".$_1."',16,10) AS DECIMAL(39,0)) * CAST(POW(65536,1) AS DECIMAL(39,0))) +
	(CAST(CONV('".$_0."',16,10) AS DECIMAL(39,0)) * CAST(POW(65536,0) AS DECIMAL(39,0))) as getdec;";
	$getDec = mysql_query($query_getDec, $subman) or die(mysql_error());
	$row_getDec = mysql_fetch_assoc($getDec);
	$totalRows_getDec = mysql_num_rows($getDec);
	
	//$dec = 	(hexdec($_7) * pow(65536,7)) + (hexdec($_6) * pow(65536,6)) + (hexdec($_5) * pow(65536,5)) + (hexdec($_4) * pow(65536,4)) + (hexdec($_3) * pow(65536,3)) + (hexdec($_2) * pow(65536,2)) + (hexdec($_1) * pow(65536,1)) + (hexdec($_0) * 65536);
	
	//return($row_getDec['getdec']);
	return($dec);
}
	
function long2ipv6($dec) {
	
	$bits7 = bcdiv($dec, bcpow(65536,7));

	$bits6_long = bcsub($dec, bcmul($bits7, bcpow(65536,7)));
	
	$bits6 = bcdiv($bits6_long, bcpow(65536,6));
	
	$bits5_long = bcsub($bits6_long, bcmul($bits6, bcpow(65536,6)));
	
	$bits5 = bcdiv($bits5_long, bcpow(65536,5));
	
	$bits4_long = bcsub($bits5_long, bcmul($bits5, bcpow(65536,5)));
	
	$bits4 = bcdiv($bits4_long, bcpow(65536,4));
	
	$bits3_long = bcsub($bits4_long, bcmul($bits4, bcpow(65536,4)));
	
	$bits3 = bcdiv($bits3_long, bcpow(65536,3));

	$bits2_long = bcsub($bits3_long, bcmul($bits3, bcpow(65536,3)));
	
	$bits2 = bcdiv($bits2_long, bcpow(65536,2));
	
	$bits1_long = bcsub($bits2_long, bcmul($bits2, bcpow(65536,2)));
	
	$bits1 = bcdiv($bits1_long, 65536);
	
	$bits0_long = bcsub($bits1_long, bcmul($bits1, 65536));
	
	$bits0 = $bits0_long;
	
	return(dechex($bits7).":".dechex($bits6).":".dechex($bits5).":".dechex($bits4).":".dechex($bits3).":".dechex($bits2).":".dechex($bits1).":".dechex($bits0));
	
}

function generatePassword ($length = 8)
{

  // start with a blank password
  $password = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
    
  // set up a counter
  $i = 0; 
    
  // add random characters to $password until $length is reached
  while ($i < $length) { 

    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        
    // we don't want this character if it's already in the password
    if (!strstr($password, $char)) { 
      $password .= $char;
      $i++;
    }

  }

  // done!
  return $password;

}

function getpageicon ($_page) {

	switch($_page) {
		case "networks" : $icon = "network_icon.gif";
		break;
		case "customers" : $icon = "customers_icon.gif";
		break;
		case "devices" : $icon = "devices_icon.gif";
		break;
		case "vpns" : $icon = "vpn_icon.gif";
		break;
		case "bgp" : $icon = "bgp_icon.gif";
		break;
		case "ripe" : $icon = "ripe_icon.gif";
		break;
		case "useradmin" : $icon = "logout_icon.gif";
		break;
		case "search" : $icon = "search.gif";
		break;
		
	}
	
	return($icon);

}

function wizardsummary ($step) {

require('Connections/subman.php');

	mysql_select_db($database_subman, $subman);
	$query_template = "SELECT servicetemplate.* FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
	$template = mysql_query($query_template, $subman) or die(mysql_error());
	$row_template = mysql_fetch_assoc($template);
	$totalRows_template = mysql_num_rows($template);
					
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
			
	mysql_select_db($database_subman, $subman);
	$query_vlan_node_a = "SELECT * FROM vlan WHERE vlan.id = '".$_SESSION['provide_vlan_node_a']."'";
	$vlan_node_a = mysql_query($query_vlan_node_a, $subman) or die(mysql_error());
	$row_vlan_node_a = mysql_fetch_assoc($vlan_node_a);
	$totalRows_vlan_node_a = mysql_num_rows($vlan_node_a);
			
	mysql_select_db($database_subman, $subman);
	$query_vlan_node_b = "SELECT * FROM vlan WHERE vlan.id = '".$_SESSION['provide_vlan_node_b']."'";
	$vlan_node_b = mysql_query($query_vlan_node_b, $subman) or die(mysql_error());
	$row_vlan_node_b = mysql_fetch_assoc($vlan_node_b);
	$totalRows_vlan_node_b = mysql_num_rows($vlan_node_b);
			
	mysql_select_db($database_subman, $subman);
	$query_customer = "SELECT * FROM customer WHERE customer.id = '".$_SESSION['provide_customer']."'";
	$customer = mysql_query($query_customer, $subman) or die(mysql_error());
	$row_customer = mysql_fetch_assoc($customer);
	$totalRows_customer = mysql_num_rows($customer);
			
	mysql_select_db($database_subman, $subman);
	$query_vpn = "SELECT vpn.*, provider.name as providername, provider.asnumber FROM vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE vpn.id = '".$_SESSION['provide_vpn']."'";
	$vpn = mysql_query($query_vpn, $subman) or die(mysql_error());
	$row_vpn = mysql_fetch_assoc($vpn);
	$totalRows_vpn = mysql_num_rows($vpn);
			
	mysql_select_db($database_subman, $subman);
	$query_xconnectpool = "SELECT * FROM xconnectpool WHERE xconnectpool.id = '".$_SESSION['provide_xconnectpool']."'";
	$xconnectpool = mysql_query($query_xconnectpool, $subman) or die(mysql_error());
	$row_xconnectpool = mysql_fetch_assoc($xconnectpool);
	$totalRows_xconnectpool = mysql_num_rows($xconnectpool);
			
	mysql_select_db($database_subman, $subman);
	$query_vrf = "SELECT * FROM vrf WHERE vrf.id = '".$_SESSION['provide_vrf']."'";
	$vrf = mysql_query($query_vrf, $subman) or die(mysql_error());
	$row_vrf = mysql_fetch_assoc($vrf);
	$totalRows_vrf = mysql_num_rows($vrf);
			
	mysql_select_db($database_subman, $subman);
	$query_card_node_a = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = '".$_SESSION['provide_card_node_a']."'";
	$card_node_a = mysql_query($query_card_node_a, $subman) or die(mysql_error());
	$row_card_node_a = mysql_fetch_assoc($card_node_a);
	$totalRows_card_node_a = mysql_num_rows($card_node_a);
			
	mysql_select_db($database_subman, $subman);
	$query_card_node_b = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = '".$_SESSION['provide_card_node_b']."'";
	$card_node_b = mysql_query($query_card_node_b, $subman) or die(mysql_error());
	$row_card_node_b = mysql_fetch_assoc($card_node_b);
	$totalRows_card_node_b = mysql_num_rows($card_node_b);
			
	mysql_select_db($database_subman, $subman);
	$query_getNetwork = "SELECT * FROM networks WHERE networks.id = '".$_SESSION['provide_parent']."'";
	$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
	$row_getNetwork = mysql_fetch_assoc($getNetwork);
	$totalRows_getNetwork = mysql_num_rows($getNetwork);
			
			
    if ($totalRows_template == 0) {
    	$row_template['name'] = 'No service template';
    }  
    
    echo '<div id="wizard_summary" class="wizard_summary">
    	
       	<h2>Provide Link: Step '.$step.' of 9</h2>
       	<strong>'.substr_replace($row_template['name'],'...',50).'</strong>';
       	
            	
    if ($step > 1 && !isset($_SESSION['additional_address'])) {
    
    	echo '<form id="frm_wizard_summary1" name="frm_wizard_summary1" action="handler.php" target="_self" method="POST">
    		<input type="hidden" name="wizard_summary" value="1">
    		<input type="hidden" name="provide" value="step0">
    		<input type="hidden" name="container" value="'.$_GET['container'].'">
    	</form>
    	
    	<br /><img src="images/nav/right_arrow.gif" alt="Arrow" align="absmiddle"> <a href="#" onClick="document.getElementById(\'frm_wizard_summary1\').submit();">Devices, port type, customer and circuit reference</a>'; 
    
    }
    if ($step > 2 && !isset($_SESSION['additional_address'])) {
    
    echo '<form id="frm_wizard_summary2" name="frm_wizard_summary2" action="handler.php" target="_self" method="POST">
    		<input type="hidden" name="wizard_summary" value="1">
    		<input type="hidden" name="provide" value="step1">
    		<input type="hidden" name="container" value="'.$_GET['container'].'">
    	</form>
    	
    	<br /><img src="images/nav/right_arrow.gif" alt="Arrow" align="absmiddle"> <a href="#" onClick="document.getElementById(\'frm_wizard_summary2\').submit();">VPN and VLAN information</a>'; 
    
    }
    if ($step > 3 && !isset($_SESSION['additional_address'])) {
    
    echo '<form id="frm_wizard_summary3" name="frm_wizard_summary3" action="handler.php" target="_self" method="POST">
    		<input type="hidden" name="wizard_summary" value="1">
    		<input type="hidden" name="provide" value="step2">
    		<input type="hidden" name="container" value="'.$_GET['container'].'">
    	</form>
    	
    	<br /><img src="images/nav/right_arrow.gif" alt="Arrow" align="absmiddle"> <a href="#" onClick="document.getElementById(\'frm_wizard_summary3\').submit();">Manual VLAN IDs, VRF/Pseudowire Pool information</a>'; 
    
    }
    if ($step > 4) {
    
    echo '<form id="frm_wizard_summary4" name="frm_wizard_summary4" action="handler.php" target="_self" method="POST">
    		<input type="hidden" name="wizard_summary" value="1">
    		<input type="hidden" name="provide" value="step3">
    		<input type="hidden" name="container" value="'.$_GET['container'].'">
    	</form>
    	
    	<br /><img src="images/nav/right_arrow.gif" alt="Arrow" align="absmiddle"> <a href="#" onClick="document.getElementById(\'frm_wizard_summary4\').submit();">Network group</a>'; 
    
    }
    if ($step > 5) {
    
    echo '<form id="frm_wizard_summary5" name="frm_wizard_summary5" action="handler.php" target="_self" method="POST">
    		<input type="hidden" name="wizard_summary" value="1">
    		<input type="hidden" name="provide" value="step4">
    		<input type="hidden" name="container" value="'.$_GET['container'].'">
    	</form>
    	
    	<br /><img src="images/nav/right_arrow.gif" alt="Arrow" align="absmiddle"> <a href="#" onClick="document.getElementById(\'frm_wizard_summary5\').submit();">Parent network</a>'; 
    
    }
    if ($step > 6) {
    
    echo '<form id="frm_wizard_summary6" name="frm_wizard_summary6" action="handler.php" target="_self" method="POST">
    		<input type="hidden" name="wizard_summary" value="1">
    		<input type="hidden" name="provide" value="step5">
    		<input type="hidden" name="container" value="'.$_GET['container'].'">
    	</form>
    	
    	<br /><img src="images/nav/right_arrow.gif" alt="Arrow" align="absmiddle"> <a href="#" onClick="document.getElementById(\'frm_wizard_summary6\').submit();">Link Network</a>'; 
    
    }
    if ($step > 7) {
    
    echo '<form id="frm_wizard_summary7" name="frm_wizard_summary7" action="handler.php" target="_self" method="POST">
    		<input type="hidden" name="wizard_summary" value="1">
    		<input type="hidden" name="provide" value="step6">
    		<input type="hidden" name="container" value="'.$_GET['container'].'">
    	</form>
    	
    	<br /><img src="images/nav/right_arrow.gif" alt="Arrow" align="absmiddle"> <a href="#" onClick="document.getElementById(\'frm_wizard_summary7\').submit();">Manual address assignment and line cards</a>'; 
    
    }
    if ($step > 8) {
    
    echo '<form id="frm_wizard_summary8" name="frm_wizard_summary8" action="handler.php" target="_self" method="POST">
    		<input type="hidden" name="wizard_summary" value="1">
    		<input type="hidden" name="provide" value="step7">
    		<input type="hidden" name="container" value="'.$_GET['container'].'">
    	</form>
    	
    	<br /><img src="images/nav/right_arrow.gif" alt="Arrow" align="absmiddle"> <a href="#" onClick="document.getElementById(\'frm_wizard_summary8\').submit();">Port selection and arbitrary fields</a>'; 
    
    }
    
	echo '</div>';
            
            
			
}

?>
