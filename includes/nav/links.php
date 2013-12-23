<?php require_once('../Connections/subman.php'); ?>
<?php include('../standard_functions.php'); ?>
<?php

if (!isset($_SESSION)) {
  session_start();
}

$start = $_GET['start'];
$end = $_GET['end'];

switch($start.$end) {

	case "09": 
		mysql_select_db($database_subman, $subman);
		$query_links_1234 = "SELECT links.*, portsdevices.devicegroup FROM links INNER JOIN portsdevices ON portsdevices.id = links.provide_node_a WHERE links.provide_cct NOT REGEXP '^[A-Z]' AND links.container = ".$_GET['container']." ORDER BY links.provide_cct";
		$links_1234 = mysql_query($query_links_1234, $subman) or die(mysql_error());
		$row_links_1234 = mysql_fetch_assoc($links_1234);
		$totalRows_links_1234 = mysql_num_rows($links_1234); 
		
        if ($totalRows_links_1234 > 0) {
            do {
            	echo "<li><a href=\"?browse=devices&amp;container=".$_GET['container']."&amp;device=".$row_links_1234['provide_node_a']."&amp;group=".$row_links_1234['devicegroup']."&amp;port=".$row_links_1234['provide_port_node_a']."&amp;linkview=1\" title=\"Display this link\">".$row_links_1234['provide_cct']."</a></li>";
            } while ($row_customers_1234 = mysql_fetch_assoc($customers_1234));
        }
        else {
			echo "<li><a class=\"NOLINK\">No links to display</a></li>";
		}
    	break;
    
    default:
    	mysql_select_db($database_subman, $subman);
		$query_links_abcd = "SELECT links.*, portsdevices.devicegroup FROM links INNER JOIN portsdevices ON portsdevices.id = links.provide_node_a WHERE links.provide_cct REGEXP '^[".$start."-".$end."]' AND links.container = ".$_GET['container']." ORDER BY links.provide_cct";
		$links_abcd = mysql_query($query_links_abcd, $subman) or die(mysql_error());
		$row_links_abcd = mysql_fetch_assoc($links_abcd);
		$totalRows_links_abcd = mysql_num_rows($links_abcd); 
		
        if ($totalRows_links_abcd > 0) {
            do {
            	echo "<li><a href=\"?browse=devices&amp;container=".$_GET['container']."&amp;device=".$row_links_abcd['provide_node_a']."&amp;group=".$row_links_abcd['devicegroup']."&amp;port=".$row_links_abcd['provide_port_node_a']."&amp;linkview=1\" title=\"Display this link\">".$row_links_abcd['provide_cct']."</a></li>";
            } while ($row_links_abcd = mysql_fetch_assoc($links_abcd));
        }
        else {
			echo "<li><a class=\"NOLINK\">No links to display</a></li>";
		}
    	break;
    
}
?>