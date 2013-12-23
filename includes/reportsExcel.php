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

require_once 'PHPExcel/Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("IP Manager 5.0")
							 ->setLastModifiedBy("IP Manager 5.0")
							 ->setTitle($row_report['title'])
							 ->setSubject("IP Manager Report")
							 ->setDescription($row_report['descr']);


$column = "A";
$row = 1;
$row6 = 1;
$rowv = 1;


$objPHPExcel->createSheet();
$objPHPExcel->createSheet();

$objPHPExcel->setActiveSheetIndex(0)
            			->setCellValue('A'.$row, 'Network')
            			->setCellValue('B'.$row, 'Description')
            			->setCellValue('C'.$row, 'Broadcast')
            			->setCellValue('D'.$row, 'Remaining (%)');

$objPHPExcel->setActiveSheetIndex(1)
            			->setCellValue('A'.$row6, 'Network')
            			->setCellValue('B'.$row6, 'Description');
            			
$objPHPExcel->setActiveSheetIndex(2)
            			->setCellValue('A'.$rowv, 'VLAN ID')
            			->setCellValue('B'.$rowv, 'Name')
            			->setCellValue('C'.$rowv, 'Customer');
            			
do { 
	
	if ($row_reportobjects['reporttype'] == "network") {
			
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
					                      
			if ($row_getNetwork['v6mask'] == "") {
                	
                	$row++;
                	
               	if ($row_getNetwork['mask'] == "255.255.255.255") { $slashmask = "/32"; } else { $slashmask = get_slash($row_getNetwork['mask']); }
                	
                	$objPHPExcel->setActiveSheetIndex(0)
            			->setCellValue('A'.$row, long2ip($row_getNetwork['network']).$slashmask)
            			->setCellValue('B'.$row, $row_getNetwork['descr']);
										
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValueExplicit('D'.$row, ((($remaining/$net['total'])*100)), PHPExcel_Cell_DataType::TYPE_NUMERIC)
						->setCellValue('C'.$row, long2ip($net['broadcast']));
						$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('[Black][>=40]##.00#;[Red][<=40]##.00#;##.00#');
						
					if ($totalRows_addresses > 0) {
					
						$row1 = 3;
						
						$ipv4sheetname = long2ip($row_getNetwork['network']).'-'.str_replace('/','',$slashmask);
						
						$myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, $ipv4sheetname);
						$objPHPExcel->addSheet($myWorkSheet);
						$objPHPExcel->getActiveSheet()->getCell('A'.$row)->getHyperlink()->setUrl("sheet://'".$ipv4sheetname."'!A1");
						$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
						
						$objPHPExcel->getSheetByName($ipv4sheetname)
							->setCellValue('A1', long2ip($row_getNetwork['network']).$slashmask)
							->setCellValue('B1', $row_getNetwork['descr']);
						
						$objPHPExcel->getSheetByName($ipv4sheetname)->getCell('A1')->getHyperlink()->setUrl("sheet://'IPv4 Networks'!A1");
						$objPHPExcel->getSheetByName($ipv4sheetname)->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
						
						$objPHPExcel->getSheetByName($ipv4sheetname)
            				->setCellValue('A'.$row1, 'Address')
            				->setCellValue('B'.$row1, 'Description')
            				->setCellValue('C'.$row1, 'Customer')
            				->setCellValue('D'.$row1, 'Device Port')
            				->setCellValue('E'.$row1, 'Comments');
            				
            			do {
            			
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
						
							$row1++;
							$deviceport = "";
							
							if ($totalRows_address_ports > 0 && $totalRows_address_subints == 0) { 
								$deviceport = $row_address_ports['devicename'].": ".$row_address_ports['cardtypename']." ";
								if ($row_address_ports['rack'] == "" && $row_address_ports['module'] == "" && $row_address_ports['slot'] == "") { $deviceport .= "Virtual/"; } elseif ($row_address_ports['rack'] != "") { $deviceport .= $row_address_ports['rack']."/"; } elseif ($row_address_ports['module'] != "") { $deviceport .= $row_address_ports['module']."/"; } elseif ($row_address_ports['slot'] != "") { $deviceport .= $row_address_ports['slot']."/"; }
								$deviceport .= $row_address_ports['port'];
							}
							if ($totalRows_address_subints > 0) { 
								$deviceport = $row_address_subints['devicename'].": ".$row_address_subints['cardtypename']." ";
								if ($row_address_subints['rack'] == "" && $row_address_subints['module'] == "" && $row_address_subints['slot'] == "") { $deviceport .= "Virtual/"; } elseif ($row_address_subints['rack'] != "") { $deviceport .= $row_address_subints['rack']."/"; } elseif ($row_address_subints['module'] != "") { $deviceport .= $row_address_subints['module']."/"; } elseif ($row_address_subints['slot'] != "") { $deviceport .= $row_address_subints['slot']."/"; }
								$deviceport .= $row_address_subints['port'].$row_address_subints['subint'];
							}
          		
							$objPHPExcel->getSheetByName($ipv4sheetname)
            					->setCellValue('A'.$row1, long2ip($row_addresses['address']))
            					->setCellValue('B'.$row1, $row_addresses['descr'])
            					->setCellValue('C'.$row1, $row_addresses['customername'])
            					->setCellValue('D'.$row1, $deviceport)
            					->setCellValue('E'.$row1, $row_addresses['comments']);
								
							
						} while ($row_addresses = mysql_fetch_assoc($addresses));
						
            			$objPHPExcel->getSheetByName($ipv4sheetname)->getColumnDimension('A')->setAutoSize(true);
            			$objPHPExcel->getSheetByName($ipv4sheetname)->getColumnDimension('B')->setAutoSize(true);
            			$objPHPExcel->getSheetByName($ipv4sheetname)->getColumnDimension('C')->setAutoSize(true);
            			$objPHPExcel->getSheetByName($ipv4sheetname)->getColumnDimension('D')->setAutoSize(true);
            			$objPHPExcel->getSheetByName($ipv4sheetname)->getColumnDimension('E')->setAutoSize(true);
            			$objPHPExcel->getSheetByName($ipv4sheetname)->getStyle('A3:E3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('DEDEDEDE');
						$objPHPExcel->getSheetByName($ipv4sheetname)->getStyle('A3:E3')->getFont()->setBold(true);
									
					}
				
				}
			
				else {
            		
            		$row6++;
            		
            		$objPHPExcel->setActiveSheetIndex(1)
            			->setCellValue('A'.$row6, Net_IPv6::Compress(long2ipv6($row_getNetwork['network']))."/".$row_getNetwork['v6mask'])
            			->setCellValue('B'.$row6, $row_getNetwork['descr']);
            			
            		if ($totalRows_addresses > 0) {
					
						$row1 = 3;
						
						$ipv6sheetname = str_replace(':','-',Net_IPv6::compress(long2ipv6($row_getNetwork['network'])).'--'.$row_getNetwork['v6mask']);
						
						$myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, $ipv6sheetname);
						$objPHPExcel->addSheet($myWorkSheet);
						$objPHPExcel->getActiveSheet()->getCell('A'.$row6)->getHyperlink()->setUrl("sheet://'".$ipv6sheetname."'!A1");
						$objPHPExcel->getActiveSheet()->getStyle('A'.$row6)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
						
						$objPHPExcel->getSheetByName($ipv6sheetname)
							->setCellValue('A1', Net_IPv6::compress(long2ipv6($row_getNetwork['network'])).$row_getNetwork['v6mask'])
							->setCellValue('B1', $row_getNetwork['descr']);
						
						$objPHPExcel->getSheetByName($ipv6sheetname)->getCell('A1')->getHyperlink()->setUrl("sheet://'IPv6 Networks'!A1");
						$objPHPExcel->getSheetByName($ipv6sheetname)->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
						
						$objPHPExcel->getSheetByName($ipv6sheetname)
            				->setCellValue('A'.$row1, 'Address')
            				->setCellValue('B'.$row1, 'Description')
            				->setCellValue('C'.$row1, 'Customer')
            				->setCellValue('D'.$row1, 'Device Port')
            				->setCellValue('E'.$row1, 'Comments');
            				
            			do {
            			
            				mysql_select_db($database_subman, $subman);
							$query_address_ports = "SELECT portsports.*, cards.module, cards.slot, cardtypes.name as cardtypename, cards.id as cardid,  portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.id as devicegroupID FROM portsports  LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portsports.router = '".$row_addresses['id']."' OR portsports.id = '".$row_addresses['portid']."'";
							$address_ports = mysql_query($query_address_ports, $subman) or die(mysql_error());
							$row_address_ports = mysql_fetch_assoc($address_ports);
							$totalRows_address_ports = mysql_num_rows($address_ports);
					
							mysql_select_db($database_subman, $subman);
							$query_address_subints = "SELECT subint.*, portsports.port, portsports.id as portID, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, cards.id as cardid, portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.id as devicegroupID FROM subint LEFT JOIN portsports ON portsports.id = subint.port LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE subint.router = '".$row_addresses['id']."' OR subint.id = '".$row_addresses['subintid']."'";
							$address_subints = mysql_query($query_address_subints, $subman) or die(mysql_error());
							$row_address_subints = mysql_fetch_assoc($address_subints);
							$totalRows_address_subints = mysql_num_rows($address_subints);
						
							$row1++;
							$deviceport = "";
														
							if ($totalRows_address_ports > 0 && $totalRows_address_subints == 0) { 
								$deviceport = $row_address_ports['devicename'].": ".$row_address_ports['cardtypename']." ";
								if ($row_address_ports['rack'] == "" && $row_address_ports['module'] == "" && $row_address_ports['slot'] == "") { $deviceport .= "Virtual/"; } elseif ($row_address_ports['rack'] != "") { $deviceport .= $row_address_ports['rack']."/"; } elseif ($row_address_ports['module'] != "") { $deviceport .= $row_address_ports['module']."/"; } elseif ($row_address_ports['slot'] != "") { $deviceport .= $row_address_ports['slot']."/"; }
								$deviceport .= $row_address_ports['port'];
							}
							if ($totalRows_address_subints > 0) { 
								$deviceport = $row_address_subints['devicename'].": ".$row_address_subints['cardtypename']." ";
								if ($row_address_subints['rack'] == "" && $row_address_subints['module'] == "" && $row_address_subints['slot'] == "") { $deviceport .= "Virtual/"; } elseif ($row_address_subints['rack'] != "") { $deviceport .= $row_address_subints['rack']."/"; } elseif ($row_address_subints['module'] != "") { $deviceport .= $row_address_subints['module']."/"; } elseif ($row_address_subints['slot'] != "") { $deviceport .= $row_address_subints['slot']."/"; }
								$deviceport .= $row_address_subints['port'].$row_address_subints['subint'];
							}
          		
							$objPHPExcel->getSheetByName($ipv6sheetname)
            					->setCellValue('A'.$row1, Net_IPv6::compress(long2ipv6($row_addresses['address'])))
            					->setCellValue('B'.$row1, $row_addresses['descr'])
            					->setCellValue('C'.$row1, $row_addresses['customername'])
            					->setCellValue('D'.$row1, $deviceport)
            					->setCellValue('E'.$row1, $row_addresses['comments']);
								
							
						} while ($row_addresses = mysql_fetch_assoc($addresses));
						
            			$objPHPExcel->getSheetByName($ipv6sheetname)->getColumnDimension('A')->setAutoSize(true);
            			$objPHPExcel->getSheetByName($ipv6sheetname)->getColumnDimension('B')->setAutoSize(true);
            			$objPHPExcel->getSheetByName($ipv6sheetname)->getColumnDimension('C')->setAutoSize(true);
            			$objPHPExcel->getSheetByName($ipv6sheetname)->getColumnDimension('D')->setAutoSize(true);
            			$objPHPExcel->getSheetByName($ipv6sheetname)->getColumnDimension('E')->setAutoSize(true);
            			$objPHPExcel->getSheetByName($ipv6sheetname)->getStyle('A3:E3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('DEDEDEDE');
						$objPHPExcel->getSheetByName($ipv6sheetname)->getStyle('A3:E3')->getFont()->setBold(true);
									
					}
            
 				}
 				
			}
	
	if ($row_reportobjects['reporttype'] == "vlanpool") {
			
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
			
            
            do {
				
				$rowv++;            
				$row2 = 3;
				
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
				
				$objPHPExcel->setActiveSheetIndex(2)
            		->setCellValue('A'.$rowv, $row_vlans['number'])
            		->setCellValue('B'.$rowv, $row_vlans['name'])
            		->setCellValue('C'.$rowv, $row_vlans['customername']);
            		
				if ($totalRows_vlan_ports > 0 || $totalRows_vlan_subints > 0) {
					
					$vlansheetname = "VLAN ID ".$row_vlans['number'];
					
					$myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, $vlansheetname);
					$objPHPExcel->addSheet($myWorkSheet);
					$objPHPExcel->getActiveSheet()->getCell('A'.$rowv)->getHyperlink()->setUrl("sheet://'".$vlansheetname."'!A1");
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rowv)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
					
					$objPHPExcel->getSheetByName($vlansheetname)
						->setCellValue('A1', "VLAN ID: ".$row_vlans['number'])
						->setCellValue('B1', $row_vlans['name']);
					
					$objPHPExcel->getSheetByName($vlansheetname)->getCell('A1')->getHyperlink()->setUrl("sheet://'VLANs'!A1");
					$objPHPExcel->getSheetByName($vlansheetname)->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
            		
            		$objPHPExcel->getSheetByName($vlansheetname)
            			->setCellValue('A'.$row2, 'Device')
            			->setCellValue('B'.$row2, 'Card Type')
            			->setCellValue('C'.$row2, 'Rack/Module/Slot/Port.Subinterface');
            				
            		if ($totalRows_vlan_ports > 0 && $totalRows_vlan_subints == 0) {

            			do {
						
							$row2++;
							            			
							if ($row_vlan_ports['slot'] == "") { $slot = "Virtual"; } else { $slot = $row_vlan_ports['slot']; }
						
								$deviceport = $row_vlan_ports['devicename'].": ".$row_vlan_ports['cardtypename']." ";
								if ($row_vlan_ports['rack'] == "" && $row_vlan_ports['module'] == "" && $row_vlan_ports['slot'] == "") { $deviceport .= "Virtual/"; } elseif ($row_vlan_ports['rack'] != "") { $deviceport .= $row_vlan_ports['rack']."/"; } elseif ($row_vlan_ports['module'] != "") { $deviceport .= $row_vlan_ports['module']."/"; } elseif ($row_vlan_ports['slot'] != "") { $deviceport .= $row_vlan_ports['slot']."/"; }
								$deviceport .= $row_vlan_ports['port'];
							
							$objPHPExcel->getSheetByName($vlansheetname)
            					->setCellValue('A'.$row2, $row_vlan_ports['devicename'])
         		   				->setCellValue('B'.$row2, $row_vlan_ports['cardtypename'])
            					->setCellValue('C'.$row2, $deviceport);
            		
            			} while ($row_vlan_ports = mysql_fetch_assoc($vlan_ports));
            			
            		}
            		
            		if ($totalRows_vlan_subints > 0) {
            		
            			do {
						
							$row2++;
							            			
							if ($row_vlan_subints['slot'] == "") { $slot = "Virtual"; } else { $slot = $row_vlan_subints['slot']; }

								$deviceport = $row_vlan_subints['devicename'].": ".$row_vlan_subints['cardtypename']." ";
								if ($row_vlan_subints['rack'] == "" && $row_vlan_subints['module'] == "" && $row_vlan_subints['slot'] == "") { $deviceport .= "Virtual/"; } elseif ($row_vlan_subints['rack'] != "") { $deviceport .= $row_vlan_subints['rack']."/"; } elseif ($row_vlan_subints['module'] != "") { $deviceport .= $row_vlan_subints['module']."/"; } elseif ($row_vlan_subints['slot'] != "") { $deviceport .= $row_vlan_subints['slot']."/"; }
								$deviceport .= $row_vlan_subints['port'].$row_vlan_subints['subint'];
							
							$objPHPExcel->getSheetByName($vlansheetname)
            					->setCellValue('A'.$row2, $row_vlan_subints['devicename'])
         		   				->setCellValue('B'.$row2, $row_vlan_subints['cardtypename'])
            					->setCellValue('C'.$row2, $deviceport);
            		
            			} while ($row_vlan_subints = mysql_fetch_assoc($vlan_subints));
            		
            		}
            				
				}
			
				$objPHPExcel->getSheetByName($vlansheetname)->getColumnDimension('A')->setAutoSize(true);
            	$objPHPExcel->getSheetByName($vlansheetname)->getColumnDimension('B')->setAutoSize(true);
            	$objPHPExcel->getSheetByName($vlansheetname)->getColumnDimension('C')->setAutoSize(true);
            	$objPHPExcel->getSheetByName($vlansheetname)->getStyle('A3:C3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('DEDEDEDE');
				$objPHPExcel->getSheetByName($vlansheetname)->getStyle('A3:C3')->getFont()->setBold(true);
						
			} while ($row_vlans = mysql_fetch_assoc($vlans));
									
	}
        
} while ($row_reportobjects = mysql_fetch_assoc($reportobjects));
	
$objPHPExcel->getSheet(0)->setTitle('IPv4 Networks');
$objPHPExcel->getSheet(1)->setTitle('IPv6 Networks');
$objPHPExcel->getSheet(2)->setTitle('VLANs');
$objPHPExcel->getSheet(0)->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getSheet(0)->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getSheet(0)->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getSheet(0)->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getSheet(0)->getStyle('A1:D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('DEDEDEDE');
$objPHPExcel->getSheet(0)->getStyle('A1:D1')->getFont()->setBold(true);
$objPHPExcel->getSheet(1)->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getSheet(1)->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getSheet(1)->getStyle('A1:B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('DEDEDEDE');
$objPHPExcel->getSheet(1)->getStyle('A1:B1')->getFont()->setBold(true);
$objPHPExcel->getSheet(2)->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getSheet(2)->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getSheet(2)->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getSheet(2)->getStyle('A1:C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('DEDEDEDE');
$objPHPExcel->getSheet(2)->getStyle('A1:C1')->getFont()->setBold(true);

$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="IP_Manager_Report.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');