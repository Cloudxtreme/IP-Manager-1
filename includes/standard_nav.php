<?php
mysql_select_db($database_subman, $subman);
$query_containers = "SELECT * FROM `container` ORDER BY `container`.name";
$containers = mysql_query($query_containers, $subman) or die(mysql_error());
$row_containers = mysql_fetch_assoc($containers);
$totalRows_containers = mysql_num_rows($containers);
?>

<li><a class="NOLINK"><img src="images/container_icon.gif" alt="Container" width="20" height="20" align="absmiddle" border="0" /> Container</a>
        <?php if ($totalRows_containers > 0) { // Show if recordset not empty ?>
        <ul>
<?php do { 
    $containerLevel = getContainerLevel($row_containers['id'], $_SESSION['MM_Username']);
    
if ($containerLevel > 0) { 
?>
        <li><a href="containerView.php?browse=networks&amp;container=<?php echo $row_containers['id']; ?>" title="<?php echo $row_containers['descr']; ?>">
<?php if (strlen($row_containers['name']) < 25) { echo $row_containers['name']; } else { echo substr_replace($row_containers['name'],'...',25); } ?>
</a></li>
<?php }
	else { ?>
    	<li><a class="NOLINK"><?php if (strlen($row_containers['name']) < 25) { echo $row_containers['name']; } else { echo substr_replace($row_containers['name'],'...',25); } ?></a></li>
  <?php } 
} while ($row_containers = mysql_fetch_assoc($containers)); ?>
	</ul>
<?php } ?>
</li>