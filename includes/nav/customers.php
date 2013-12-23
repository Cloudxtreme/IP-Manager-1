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
		$query_customers_1234 = "SELECT * FROM customer WHERE container = '".$_GET['container']."' AND customer.name NOT REGEXP '^[A-Z]' ORDER BY customer.name";
		$customers_1234 = mysql_query($query_customers_1234, $subman) or die(mysql_error());
		$row_customers_1234 = mysql_fetch_assoc($customers_1234);
		$totalRows_customers_1234 = mysql_num_rows($customers_1234);
		
        if ($totalRows_customers_1234 > 0) {
            do {
            	echo "<li><a href=\"?browse=customers&amp;container=".$_GET['container']."&amp;customer=".$row_customers_1234['id']."\" title=\"".$row_customers_1234['name']."\">";
                if (strlen($row_customers_1234['name']) < 25) { echo $row_customers_1234['name']; } else { echo substr_replace($row_customers_1234['name'],'...',25); };
                echo "</a></li>";
            } while ($row_customers_1234 = mysql_fetch_assoc($customers_1234));
        }
        else {
			echo "<li><a class=\"NOLINK\">No customers to display</a></li>";
		}
    	break;
    
    default:
    	mysql_select_db($database_subman, $subman);
		$query_customers_abcd = "SELECT * FROM customer WHERE container = '".$_GET['container']."' AND customer.name REGEXP '^[".$start."-".$end."]' ORDER BY customer.name";
		$customers_abcd = mysql_query($query_customers_abcd, $subman) or die(mysql_error());
		$row_customers_abcd = mysql_fetch_assoc($customers_abcd);
		$totalRows_customers_abcd = mysql_num_rows($customers_abcd);
		
		if ($totalRows_customers_abcd > 0) {
            do {
            	echo "<li><a href=\"?browse=customers&amp;container=".$_GET['container']."&amp;customer=".$row_customers_abcd['id']."\" title=\"".$row_customers_abcd['name']."\">";
                if (strlen($row_customers_abcd['name']) < 25) { echo $row_customers_abcd['name']; } else { echo substr_replace($row_customers_abcd['name'],'...',25); };
                echo "</a></li>";
            } while ($row_customers_abcd = mysql_fetch_assoc($customers_abcd));
        }
        else {
			echo "<li><a class=\"NOLINK\">No customers to display</a></li>";
		}
        break;
    
}
?>