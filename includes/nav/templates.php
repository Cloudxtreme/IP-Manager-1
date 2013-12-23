<?php require_once('../Connections/subman.php'); ?>
<?php include('../standard_functions.php'); ?>
<?php

if (!isset($_SESSION)) {
  session_start();
}

$link = $_GET['link'];

mysql_select_db($database_subman, $subman);
$query_links = "SELECT links.*, portsdevices.devicegroup FROM links LEFT JOIN portsdevices ON portsdevices.id = links.provide_node_a WHERE links.servicetemplate = '".$link."' AND links.container = '".$_GET['container']."' ORDER BY links.provide_cct";
$links = mysql_query($query_links, $subman) or die(mysql_error());
$row_links = mysql_fetch_assoc($links);
$totalRows_links = mysql_num_rows($links);
		
if ($totalRows_links > 0) {
    do {
       	echo "<li><a href=\"?browse=devices&amp;container=".$_GET['container']."&amp;device=".$row_links['provide_node_a']."&amp;group=".$row_links['devicegroup']."&amp;port=".$row_links['provide_port_node_a']."&amp;linkview=1\" title=\"Display this link\">".$row_links['provide_cct']."</a></li>";
    } while ($row_links = mysql_fetch_assoc($links));
}
else {
	echo "<li><a class=\"NOLINK\">No links to display</a></li>";
}
?>