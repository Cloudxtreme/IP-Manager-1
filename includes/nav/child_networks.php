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

mysql_select_db($database_subman, $subman);
$query_network = "SELECT * FROM networks WHERE networks.parent = ".$_GET['network']." ORDER BY networks.network ASC";
$network = mysql_query($query_network, $subman) or die(mysql_error());
$row_network = mysql_fetch_assoc($network);
$totalRows_network = mysql_num_rows($network);
			
	  	$count = 0;
	  	do {
			if ($row_network['v6mask'] == "") { 
				$net = find_net(long2ip($row_network['network']),$row_network['mask']);
			}
			
			mysql_select_db($database_subman, $subman);
			$query_check_network_subnets = "SELECT * FROM networks WHERE networks.parent = '".$row_network['id']."'";
			$check_network_subnets = mysql_query($query_check_network_subnets, $subman) or die(mysql_error());
			$row_check_network_subnets = mysql_fetch_assoc($check_network_subnets);
			$totalRows_check_network_subnets = mysql_num_rows($check_network_subnets);

	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			}
			?>
      <?php if ($_GET['group'] != "" && ($_GET['parent'] == "" || $_GET['parent'] == 0) && (in_array($row_network['parent'],$arr_base_parent))) { 
			}
			else {
				$count++;
		?>
		<?php 
		if (!isset($_GET['count'])) {
			$_GET['count'] = 1;
		}
		echo "|";
		for ($i = 0; $i < ($_GET['count']); $i++) {
			echo "---";
		} ?>
			
      <?php if ($totalRows_check_network_subnets > 0) { ?><strong><?php } ?>
        <?php if ($totalRows_check_network_subnets > 0) { ?><a href="" onClick="ShowChildren(<?php echo $row_network['id']; ?>, <?php echo ($_GET['count']+1); ?>); document.getElementById('expand<?php echo $row_network['id']; ?>').style.display = 'none'; return false;"><img src="images/expand1.gif" alt="Expand" align="absmiddle" title="Show subnetworks" id="expand<?php echo $row_network['id']; ?>"></a><?php } ?>&nbsp;<a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;parent=<?php echo $row_network['id']; ?>" title="<?php echo $row_network['comments']; ?>"><?php if ($row_network['v6mask'] == "") { echo long2ip($net['network']); } else { echo Net_IPv6::compress(long2ipv6($row_network['network'])); } ?><?php if ($row_network['v6mask'] == "") { if ($row_network['mask'] == "255.255.255.255") { echo "/32"; } else { echo get_slash($row_network['mask']); } } else { echo "/".$row_network['v6mask']; } ?></a>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_network['descr']; ?>
        <?php if ($totalRows_check_network_subnets > 0) { ?></strong><?php } ?><br />
      
      <?php if ($totalRows_check_network_subnets > 0) { ?>
      	<span id="child_network<?php echo $row_network['id']; ?>"></span>
      <?php } ?>
      
      <?php } ?>
      <?php } while ($row_network = mysql_fetch_assoc($network)); ?>