<?php
mysql_select_db($database_subman, $subman);
$query_getUser = "SELECT * FROM `user` WHERE `user`.username = '".$_SESSION['MM_Username']."'";
$getUser = mysql_query($query_getUser, $subman) or die(mysql_error());
$row_getUser = mysql_fetch_assoc($getUser);
$totalRows_getUser = mysql_num_rows($getUser);
?>

<li><a class="NOLINK"><img src="images/admin_icon.gif" alt="Admin" width="20" height="20" align="absmiddle" border="0" /> Admin</a>
	<?php if ($ipm->getPageLevel(3,$_SESSION['MM_Username']) > 0 || getPageLevel(4,$_SESSION['MM_Username']) > 0 || getPageLevel(5,$_SESSION['MM_Username']) > 0) { ?>
    <ul>
        <?php if ($ipm->getPageLevel(3,$_SESSION['MM_Username']) > 0) { ?>
        <li><a href="userGroupView.php" title="Manage user and group access.">User Groups</a></li>
        <?php } ?>
        <?php if ($ipm->getPageLevel(4,$_SESSION['MM_Username']) > 0) { ?>
        <li><a href="containerMgmtView.php" title="Manage containers.">Container Management</a></li>
        <?php } ?>
        <?php if ($ipm->getPageLevel(5,$_SESSION['MM_Username']) > 0) { ?>
        <li><a href="radiatorMgmtView.php?browse=radiators" title="Manage Radiator servers.">Radiator Plugin</a></li>
        <?php } ?>
        <?php if ($ipm->getPageLevel(6,$_SESSION['MM_Username']) > 0) { ?>
        <li><a href="damitMgmtView.php?browse=damits" title="Manage DAM-it servers.">DAM-it Plugin</a></li>
        <?php } ?>
        <?php if ($ipm->getPageLevel(7,$_SESSION['MM_Username']) > 0) { ?>
        <li><a href="observiumMgmtView.php?browse=observiums" title="Manage Observium servers.">Observium Plugin</a></li>
        <?php } ?>
    </ul>
    <?php } ?>
</li>
<li><a href="index.php?doLogout=true" title="Logout"><img src="images/logout_icon.gif" alt="Logout" width="21" height="20" align="absmiddle" border="0" /> <?php echo $row_getUser['firstname']; ?> <?php echo $row_getUser['lastname']; ?></a>
	<?php if ($_GET['container']) { ?>
	<ul>
		<li><a href="index.php?doLogout=true" title="Logout"><img src="images/logout_icon.gif" alt="Logout" width="21" height="20" align="absmiddle" border="0" /> Logout</a></li>
    	<li><a href="containerView.php?browse=useradmin&amp;container=<?php echo $_GET['container']; ?>" title="Edit your profile">Edit profile</a></li>
    </ul>
    <?php } ?>
</li>
<?php
mysql_free_result($getUser);
?>
