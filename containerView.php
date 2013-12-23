<?php require_once('Connections/subman.php'); ?>
<?php require_once('Net/IPv4.php'); ?>
<?php require_once('Net/IPv6.php'); ?>
<?php include('includes/standard_functions.php'); ?>
<?php include('includes/ipm_class.php'); ?>
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

require_once('includes/ipm_headercode.php');

mysql_select_db($database_subman, $subman);
$query_container = "SELECT * FROM `container` WHERE `container`.id = '".$_GET['container']."'";
$container = mysql_query($query_container, $subman) or die(mysql_error());
$row_container = mysql_fetch_assoc($container);
$totalRows_container = mysql_num_rows($container);

mysql_select_db($database_subman, $subman);
$query_network_groups = "SELECT * FROM networkgroup WHERE networkgroup.`container` = '".$_GET['container']."' ORDER BY networkgroup.name";
$network_groups = mysql_query($query_network_groups, $subman) or die(mysql_error());
$row_network_groups = mysql_fetch_assoc($network_groups);
$totalRows_network_groups = mysql_num_rows($network_groups);

mysql_select_db($database_subman, $subman);
$query_device_groups = "SELECT * FROM portgroups WHERE container = '".$_GET['container']."' ORDER BY portgroups.name";
$device_groups = mysql_query($query_device_groups, $subman) or die(mysql_error());
$row_device_groups = mysql_fetch_assoc($device_groups);
$totalRows_device_groups = mysql_num_rows($device_groups);

mysql_select_db($database_subman, $subman);
$query_device_types = "SELECT * FROM devicetypes ORDER BY devicetypes.name";
$device_types = mysql_query($query_device_types, $subman) or die(mysql_error());
$row_device_types = mysql_fetch_assoc($device_types);
$totalRows_device_types = mysql_num_rows($device_types);

mysql_select_db($database_subman, $subman);
$query_vpn_providers = "SELECT * FROM provider WHERE container = '".$_GET['container']."' ORDER BY provider.name";
$vpn_providers = mysql_query($query_vpn_providers, $subman) or die(mysql_error());
$row_vpn_providers = mysql_fetch_assoc($vpn_providers);
$totalRows_vpn_providers = mysql_num_rows($vpn_providers);

mysql_select_db($database_subman, $subman);
$query_pseudowire_pools = "SELECT * FROM xconnectpool WHERE container = '".$_GET['container']."' ORDER BY xconnectpool.xconnectstart";
$pseudowire_pools = mysql_query($query_pseudowire_pools, $subman) or die(mysql_error());
$row_pseudowire_pools = mysql_fetch_assoc($pseudowire_pools);
$totalRows_pseudowire_pools = mysql_num_rows($pseudowire_pools);

mysql_select_db($database_subman, $subman);
$query_serviceTemplates = "SELECT * FROM servicetemplate WHERE container = '".$_GET['container']."' ORDER BY servicetemplate.name";
$serviceTemplates = mysql_query($query_serviceTemplates, $subman) or die(mysql_error());
$row_serviceTemplates = mysql_fetch_assoc($serviceTemplates);
$totalRows_serviceTemplates = mysql_num_rows($serviceTemplates);

mysql_select_db($database_subman, $subman);
$query_reports = "SELECT * FROM reports WHERE container = '".$_GET['container']."' AND username = '".$_SESSION['MM_Username']."' ORDER BY reports.title";
$reports = mysql_query($query_reports, $subman) or die(mysql_error());
$row_reports = mysql_fetch_assoc($reports);
$totalRows_reports = mysql_num_rows($reports);

$ipm = new ipm;

$pageLevel = $ipm->getPageLevel(2,$_SESSION['MM_Username']);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>IP Manager <?php $ipm->getVer(); ?></title>
<link href="css/ipm5.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/jqueryslidemenu.css" />
<!--[if lte IE 7]>
<style type="text/css">
html .jqueryslidemenu{height: 1%;} /*Holly Hack for IE7 and below*/
</style>
<![endif]-->

<link rel="stylesheet" type="text/css" href="css/jqcontextmenu.css" />

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>

<script type="text/javascript" src="jqcontextmenu.js">

/***********************************************
* jQuery Context Menu- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for this script and 100s more
***********************************************/

</script>

<script type="text/javascript">

//Usage: $(elementselector).addcontextmenu('id_of_context_menu_on_page')
//To apply context menu to entire document, use: $(document).addcontextmenu('id_of_context_menu_on_page')

jQuery(document).ready(function($){
	$('div.rightclick').addcontextmenu('contextmenu1') //apply context menu to links with class="mylinks"
})



</script>

<script type="text/javascript" src="jqueryslidemenu.js"></script>


<script type="text/javascript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}

function checkAll(theForm, status) {
for (i=0,n=theForm.elements.length;i<n;i++)
theForm.elements[i].checked = status;
}
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>


<script type="text/javascript">
function showCustomers(str, end)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("customers" + str + end).innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/customers.php?container=<?php echo $_GET['container']; ?>&start="+str+"&end="+end,true);
xmlhttp.send();
}

function showLinks(str, end)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("links" + str + end).innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/links.php?container=<?php echo $_GET['container']; ?>&start="+str+"&end="+end,true);
xmlhttp.send();
}

function showTemplates(str)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("template" + str).innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/templates.php?container=<?php echo $_GET['container']; ?>&link="+str,true);
xmlhttp.send();
}

function searchQry(str,limit)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("searchQ").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/search.php?container=<?php echo $_GET['container']; ?>&search="+str+"&limit="+limit,true);
xmlhttp.send();
}

function searchQry_networks(str)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("searchQ_1").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/search_networks.php?container=<?php echo $_GET['container']; ?>&search="+str,true);
xmlhttp.send();
}

function searchQry_parents(str)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("searchQ_1").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/search_parents.php?container=<?php echo $_GET['container']; ?>&search="+str,true);
xmlhttp.send();
}

function ShowChildren(network, count)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("child_network" + network).innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/child_networks.php?container=<?php echo $_GET['container']; ?>&network="+network+"&count="+count,true);
xmlhttp.send();
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
</script>

</head>
<body onLoad="<?php if ($_GET['action'] == 'add_address') { ?>document.frm_add_address.descr.focus();<?php }?> <?php if ($_GET['print'] == 1) { ?>window.print(); window.close();<?php } ?><?php if ($_SESSION['errstr'] != "") { echo "alert('".$_SESSION['errstr']."');"; } ?>">
<div class="rightclick">
<div class="banner">
  <img src="images/ipm_banner.gif" alt="IPM Home" name="ipm_banner" width="296" height="59" id="ipm_banner" />
  <div id="ipm_search">
  <input name="search" type="text" id="search" maxlength="255" autocomplete="off" onkeyup="if (this.value == '') { document.getElementById('searchQ').style.display='none'; } else { document.getElementById('searchQ').style.display='block'; searchQry(this.value); }">
  <div id="ipm_search_cancel">
    <a href="#" title="Cancel search" onclick="document.getElementById('search').value = ''; document.getElementById('searchQ').style.display = 'none';"><img src="images/cancel.gif" border="0" alt="Cancel search"></a>
    </div>    
    </div>
  <div id="ipm_search_button"><img src="images/ipm_search_icon.gif" border="0" alt="Search" id="ipm_search_icon" /></div>
  <div id="searchQ" class="searchQ	"><img src="images/spinningwheel.gif" alt="Please wait..." width="30" height="30" align="absmiddle"> searching...</div>
    <input type="hidden" value="search" name="browse" />
    <input type="hidden" value="<?php echo $_GET['container']; ?>" name="container" />
    </div>
</div>

<div class="ipm_body">








<div class="ipm_nav">
<!-- Sample menu definition -->
<div id="myslidemenu" class="jqueryslidemenu" style="z-index:10000">

<ul>
	<?php include('includes/standard_nav.php'); ?>
	<li><a href="containerView.php?browse=networks&amp;container=<?php echo $_GET['container']; ?>"><img src="images/network_icon.gif" width="20" height="20" alt="Networks" border="0" align="absmiddle" /> Networks</a>
       	<ul>
           	<li><a href="containerView.php?browse=networks&amp;container=<?php echo $_GET['container']; ?>">All Networks</a></li>
            <li><a class="NOLINK">Network Groups</a>
            	
                	<?php if ($totalRows_network_groups > 0) { // Show if recordset not empty ?>
                    <ul>
        <?php do { 
  			if (getNetGroupLevel($row_network_groups['id'],$_SESSION['MM_Username']) > 0 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getNetGroupLevel($row_network_groups['id'],$_SESSION['MM_Username']) == "")) { 
	?>
        <li><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_network_groups['id']; ?>" title="<?php echo $row_network_groups['descr']; ?>">
          <?php if (strlen($row_network_groups['name']) < 25) { echo $row_network_groups['name']; } else { echo substr_replace($row_network_groups['name'],'...',25); } ?>
      </a></li>
        <?php } else { ?>
        	<li><a class="NOLINK"><?php if (strlen($row_network_groups['name']) < 25) { echo $row_network_groups['name']; } else { echo substr_replace($row_network_groups['name'],'...',25); } ?></a></li>
        <?php }
	} while ($row_network_groups = mysql_fetch_assoc($network_groups)); ?>
    				</ul>
<?php } ?>
            </li>
            <li><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;networkgroups=1" title="Manage Network Groups">Manage Network Groups</a></li>
        </ul>
    </li>
	<li><a href="?browse=customers&amp;container=<?php echo $_GET['container']; ?>"><img src="images/customers_icon.gif" alt="Customers" width="23" height="20" align="absmiddle" border="0" /> Customers</a>
    	<ul>
        	<li><a class="NOLINK" onmouseover="if (document.getElementById('customers09').getElementsByTagName('li').length < 2) { showCustomers('0','9'); }">0 - 9...</a>
        		<ul id="customers09">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
        	<li><a class="NOLINK" onmouseover="if (document.getElementById('customersAD').getElementsByTagName('li').length < 2) { showCustomers('A','D'); }">A - D</a>
        		<ul id="customersAD">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
            <li><a class="NOLINK" onmouseover="if (document.getElementById('customersEH').getElementsByTagName('li').length < 2) { showCustomers('E','H'); }">E - H</a>
        		<ul id="customersEH">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
            <li><a class="NOLINK" onmouseover="if (document.getElementById('customersIL').getElementsByTagName('li').length < 2) { showCustomers('I','L'); }">I - L</a>
        		<ul id="customersIL">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
            <li><a class="NOLINK" onmouseover="if (document.getElementById('customersMP').getElementsByTagName('li').length < 2) { showCustomers('M','P'); }">M - P</a>
        		<ul id="customersMP">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
            <li><a class="NOLINK" onmouseover="if (document.getElementById('customersQT').getElementsByTagName('li').length < 2) { showCustomers('Q','T'); }">Q - T</a>
        		<ul id="customersQT">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
            <li><a class="NOLINK" onmouseover="if (document.getElementById('customersUZ').getElementsByTagName('li').length < 2) { showCustomers('U','Z'); }">U - Z</a>
        		<ul id="customersUZ">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
        </ul>
    </li>
	<li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>"><img src="images/devices_icon.gif" alt="Devices" width="20" height="20" align="absmiddle" border="0" /> Devices</a>
    	<ul>
       		<li><a class="NOLINK">By Device Group</a>
            
        	<?php if ($totalRows_device_groups > 0) { // Show if recordset not empty ?>
            	<ul>
        			<?php do { 
						if (getDeviceGroupLevel($row_device_groups['id'],$_SESSION['MM_Username']) > 0 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($row_device_groups['id'],$_SESSION['MM_Username']) == "")) { ?>
					<li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_device_groups['id']; ?>" title="<?php echo $row_device_groups['name']; ?>">
					  <?php if (strlen($row_device_groups['name']) < 25) { echo $row_device_groups['name']; } else { echo substr_replace($row_device_groups['name'],'...',25); } ?>
				  </a></li>
				<?php 
                    }
                    else { ?>
                    <li><a class="NOLINK"><?php if (strlen($row_device_groups['name']) < 25) { echo $row_device_groups['name']; } else { echo substr_replace($row_device_groups['name'],'...',25); } ?></a></li>
                    <?php }
                    } while ($row_device_groups = mysql_fetch_assoc($device_groups)); ?>
            	</ul>
<?php } // Show if recordset not empty ?>
    		</li>
    		
    		<li><a class="NOLINK">By Device Type</a>
            
        	<?php if ($totalRows_device_types > 0) { // Show if recordset not empty ?>
            	<ul>
        			<?php do { 
        				
        				mysql_select_db($database_subman, $subman);
						$query_devices = "SELECT portsdevices.* FROM portsdevices left join portgroups on portgroups.id = portsdevices.devicegroup WHERE portgroups.container = '".$_GET['container']."' AND devicetype = '".$row_device_types['id']."' ORDER BY portsdevices.name";
						$devices = mysql_query($query_devices, $subman) or die(mysql_error());
						$row_devices = mysql_fetch_assoc($devices);
						$totalRows_devices = mysql_num_rows($devices);

						if (getDeviceLevel($row_devices['id'],$_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($row_device_groups['id'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($row_devices['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($row_device_groups['id'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_devices['id'],$_SESSION['MM_Username']) == "")) { ?>
					<li>
						<a class="NOLINK"><?php if (strlen($row_device_types['name']) < 25) { echo $row_device_types['name']; } else { echo substr_replace($row_device_types['name'],'...',25); } ?></a>
						<ul>
							<?php if ($totalRows_devices == 0) { ?>
							<li><a class="NOLINK">No devices to display</a></li>
							<?php } else { ?>
							<?php do { ?>
							<li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_devices['devicegroup']; ?>&amp;device=<?php echo $row_devices['id']; ?>" title="<?php echo $row_devices['descr']; ?>"><?php echo $row_devices['name']; ?></a></li>
				  			<?php } while ($row_devices = mysql_fetch_assoc($devices)); ?>
				  			<?php } ?>
						</ul>
						</li>
				<?php 
                    }
                    else { ?>
                    <li><a class="NOLINK"><?php if (strlen($row_device_groups['name']) < 25) { echo $row_device_groups['name']; } else { echo substr_replace($row_device_groups['name'],'...',25); } ?></a></li>
                    <?php }
                    } while ($row_device_types = mysql_fetch_assoc($device_types)); ?>
            	</ul>
<?php } // Show if recordset not empty ?>
    		</li>
        
        <li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&browsecardtypes=1" title="Manage Line Card Types">Manage Line Card Types</a></li>
        <li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&browsedevicetypes=1" title="Manage Device Types">Manage Device Types</a></li>
        <li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&devicegroups=1" title="Manage Device Groups">Manage Device Groups</a></li>
        
    	<li><a class="NOLINK">Templates</a>
            <ul>
                <li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&browsetemplates=service" title="Manage service templates">Service Templates</a></li>
            </ul>
        </li>
    </ul>
    <li><a class="NOLINK"><img src="images/link.gif" alt="Links" border="0" width="25" height="20" align="absmiddle" /> Links</a>
    	<ul>
        	<?php if ($containerLevel > 10) { ?><li><a href="#" onClick="document.getElementById('frm_providelink').submit()"><img src="images/plus_icon.gif" alt="Provide Link" border="0" align="absmiddle" /> <strong>Provide Link</strong></a></li><?php } ?>
        	<li><a class="NOLINK">By Service Template</a>
            	<?php if ($totalRows_serviceTemplates > 0) { ?>
            	<ul>
                	<?php 
						
						do { ?>
                        
                    	<li><a class="NOLINK" onmouseover="if (document.getElementById('template<?php echo $row_serviceTemplates['id']; ?>').getElementsByTagName('li').length < 2) { showTemplates('<?php echo $row_serviceTemplates['id']; ?>'); }"><?php echo $row_serviceTemplates['name']; ?></a>
        					<ul id="template<?php echo $row_serviceTemplates['id']; ?>">
        						<li><a class="NOLINK">loading...</a></li>
        					</ul>
                        </li>
                    <?php } while ($row_serviceTemplates = mysql_fetch_assoc($serviceTemplates)); ?>
                    
                </ul>
				<?php } ?>
            </li>
            <li>
            	<a class="NOLINK">By Circuit Reference</a>
                <ul>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('links09').getElementsByTagName('li').length < 2) { showLinks('0','9'); }">0 - 9...</a>
        				<ul id="links09">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksAD').getElementsByTagName('li').length < 2) { showLinks('A','D'); }">A - D</a>
        				<ul id="linksAD">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksEH').getElementsByTagName('li').length < 2) { showLinks('E','H'); }">E - H</a>
        				<ul id="linksEH">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksIL').getElementsByTagName('li').length < 2) { showLinks('I','L'); }">I - L</a>
        				<ul id="linksIL">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksMP').getElementsByTagName('li').length < 2) { showLinks('M','P'); }">M - P</a>
        				<ul id="linksMP">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksQT').getElementsByTagName('li').length < 2) { showLinks('Q','T'); }">Q - T</a>
        				<ul id="linksQT">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksUZ').getElementsByTagName('li').length < 2) { showLinks('U','Z'); }">U - Z</a>
        				<ul id="linksUZ">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                </ul>
            </li>
        </ul>   
    </li>
	<li><a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>"><img src="images/vpn_icon.gif" alt="VPNs" width="28" height="20" align="absmiddle" border="0" />VPNs</a>
    	<ul>
        	<li><a class="NOLINK">MPLS Providers</a>
                	<?php if ($totalRows_vpn_providers > 0) { // Show if recordset not empty ?>
                    <ul>
        <?php do { 
	  		
			$provider = $row_vpn_providers['asnumber']." ".$row_vpn_providers['name'];
			
	  		if (getProviderLevel($row_vpn_providers['id'],$_SESSION['MM_Username']) > 0 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getProviderLevel($row_vpn_providers['id'],$_SESSION['MM_Username']) == "")) { ?>
        <li><a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $row_vpn_providers['id']; ?>" title="<?php echo $row_vpn_providers['descr']; ?>">
          <?php if (strlen($provider) < 25) { echo $provider; } else { echo substr_replace($provider,'...',25); } ?>
      </a></li>
        <?php 
			}
			else { ?>
            	<li><a class="NOLINK"><?php if (strlen($provider) < 25) { echo $provider; } else { echo substr_replace($provider,'...',25); } ?></a></li>
            <?php }				
			} while ($row_vpn_providers = mysql_fetch_assoc($vpn_providers)); ?>
            </ul>
<?php } // Show if recordset not empty ?>
            </li>
            <li><a class="NOLINK">Pseudowire Pools</a>
                	<?php if ($totalRows_pseudowire_pools > 0) { // Show if recordset not empty ?>
                    <ul>
        <?php do { 
			
	  		if (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0) { ?>
        <li><a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;xconnectpool=<?php echo $row_pseudowire_pools['id']; ?>" title="<?php echo $row_pseudowire_pools['descr']; ?>">
          <?php if (strlen($row_pseudowire_pools['descr']) < 19) { echo $row_pseudowire_pools['descr']; } else { echo "Pool: ".substr_replace($row_pseudowire_pools['descr'],'...',19); } ?>
      </a></li>
        <?php 
			}
			else { ?>
            	<li><a class="NOLINK"><?php if (strlen($row_pseudowire_pools['descr']) < 19) { echo $row_pseudowire_pools['descr']; } else { echo "Pool: ".substr_replace($row_pseudowire_pools['descr'],'...',19); } ?></a></li>
            <?php }				

			} while ($row_pseudowire_pools = mysql_fetch_assoc($pseudowire_pools)); ?>
            </ul>
        <?php } // Show if recordset not empty ?>
            </li>
        </ul>
    </li>
    <li><a class="NOLINK"><img src="images/bgp_icon.gif" alt="BGP" width="18" height="20" align="absmiddle" border="0" /> BGP</a>
    	<ul>
        	<li><a href="?browse=bgp&amp;container=<?php echo $_GET['container']; ?>">Autonomous Systems</a></li>
        </ul>
    </li>
    <li><a href="?browse=reports&amp;container=<?php echo $_GET['container']; ?>"><img src="images/reports-icon.gif" alt="Reports" align="absmiddle" width="20" height="20" border="0" /> Reports</a>
    	<?php if ($totalRows_reports > 0) { ?>
        	<ul>
            <?php do { ?>
            <li><a href="?browse=reports&amp;container=<?php echo $_GET['container']; ?>&amp;report=<?php echo $row_reports['id']; ?>" title="<?php echo $row_reports['descr']; ?>"><img src="images/reports-icon.gif" alt="Reports" align="absmiddle" width="20" height="20" border="0" /> <?php echo $row_reports['title']; ?></a></li>
            <?php } while ($row_reports = mysql_fetch_assoc($reports)); ?>
            </ul>
        <?php } ?>
    </li>
    
    <?php include('includes/standard_nav_footer.php'); ?>
<!-- Please leave at least one new line or white space symbol after the closing -->

</ul>
</div>
</div>








<div class="rightclick">
  
  <?php if ($_GET['print'] != 1 or $_GET['print'] == 1) { ?>
<?php if ($containerLevel > 10) { ?>
<form action="?browse=devices&amp;container=<?php echo $_GET['container']; ?>" method="post" target="_self" name="frm_providelink" id="frm_providelink">
	<input type="hidden" name="action" value="provide_link" />
</form>
<?php }







if ($_GET['browse'] == "bgp") {
		
		$maxRows_asses = 25;
$pageNum_asses = 0;
if (isset($_GET['pageNum_asses'])) {
  $pageNum_asses = $_GET['pageNum_asses'];
}
$startRow_asses = $pageNum_asses * $maxRows_asses;

mysql_select_db($database_subman, $subman);
$query_asses = "SELECT asses.* FROM asses WHERE asses.container = ".$_GET['container']." ORDER BY asses.number";
$query_limit_asses = sprintf("%s LIMIT %d, %d", $query_asses, $startRow_asses, $maxRows_asses);
$asses = mysql_query($query_limit_asses, $subman) or die(mysql_error());
$row_asses = mysql_fetch_assoc($asses);

if (isset($_GET['totalRows_asses'])) {
  $totalRows_asses = $_GET['totalRows_asses'];
} else {
  $all_asses = mysql_query($query_asses);
  $totalRows_asses = mysql_num_rows($all_asses);
}
$totalPages_asses = ceil($totalRows_asses/$maxRows_asses)-1;

$queryString_asses = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_asses") == false && 
        stristr($param, "totalRows_asses") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_asses = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_asses = sprintf("&totalRows_asses=%d%s", $totalRows_asses, $queryString_asses);
		
		if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
	?>
    BGP <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>Autonomous Systems</strong>
    <p>&nbsp;</p>
    
    <?php if ($totalRows_asses > 0) { ?>
    <table border="0">
      <tr>
        <td><?php if ($pageNum_asses > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_asses=%d%s", $currentPage, 0, $queryString_asses); ?>"><img src="images/First.gif" border="0" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_asses > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_asses=%d%s", $currentPage, max(0, $pageNum_asses - 1), $queryString_asses); ?>"><img src="images/Previous.gif" border="0" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_asses < $totalPages_asses) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_asses=%d%s", $currentPage, min($totalPages_asses, $pageNum_asses + 1), $queryString_asses); ?>"><img src="images/Next.gif" border="0" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_asses < $totalPages_asses) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_asses=%d%s", $currentPage, $totalPages_asses, $queryString_asses); ?>"><img src="images/Last.gif" border="0" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table>
<table border="0" width="100%">
      <tr>
        <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('as_action'), this.checked);" /></td>
        <td><strong>AS Number</strong></td>
        <td><strong>Name</strong></td>
        <td><strong>Description</strong></td>
        <td><strong>Password</strong></td>
        <td><strong>Customer</strong></td>
      </tr>
      
      <?php } ?>
      
      <?php
	  
	  if ($_POST['action'] == "add_as") {
		  
		  if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
                
	  for ($i = 65001; $i < 65535; $i ++) {
						
			mysql_select_db($database_subman, $subman);
			$query_nextAS = "SELECT * FROM asses WHERE asses.number = ".$i." AND container = ".$_GET['container']."";
			$nextAS = mysql_query($query_nextAS, $subman) or die(mysql_error());
			$row_nextAS = mysql_fetch_assoc($nextAS);
			$totalRows_nextAS = mysql_num_rows($nextAS);

				if ( $totalRows_nextAS == 0 ) {
							$nextAS = $i;
							$i = 65535;
							}
				}
			
			mysql_select_db($database_subman, $subman);
			$query_customers = "SELECT * FROM customer WHERE container = '".$_GET['container']."' ORDER BY customer.name";
			$customers = mysql_query($query_customers, $subman) or die(mysql_error());
			$row_customers = mysql_fetch_assoc($customers);
			$totalRows_customers = mysql_num_rows($customers);
			
		
	?>
    <table border="0" width="100%">
      <tr>
        <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('as_action'), this.checked);" /></td>
        <td><strong>AS Number</strong></td>
        <td><strong>Name</strong></td>
        <td><strong>Description</strong></td>
        <td><strong>Password</strong></td>
        <td><strong>Customer</strong></td>
      </tr>
    	<form action="<?php echo $editFormAction; ?>" method="post" target="_self" id="frm_add_as" onSubmit="MM_validateForm('as','','RinRange1:4294967296','name','','R','descr','','R');return document.MM_returnValue" >
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top"><input name="as" id="as" type="text" class="input_standard" value="<?php echo $nextAS; ?>" size="15" maxlength="10" />
            <br />
            <input type="submit" class="input_standard" value="Add Autonomous System" />
            <a href="?browse=bgp&amp;container=<?php echo $_GET['container']; ?>" title="Cancel">Cancel</a>
            <input name="user" type="hidden" id="user" value="<?php echo $_SESSION['MM_Username']; ?>" /></td>
          <td valign="top"><input name="name" type="text" class="input_standard" id="name" size="32" maxlength="255" /></td>
          <td valign="top"><textarea name="descr" cols="32" rows="2" class="input_standard" id="descr" /></textarea></td>
          <td valign="top"><input type="text" name="password" id="password" class="input_standard" size="20" maxlength="255" /></td>          
          <td valign="top"><select name="customer" class="input_standard" id="customer">
            <?php
do {  
?>
            <option value="<?php echo $row_customers['id']?>"><?php echo $row_customers['name']?></option>
            <?php
} while ($row_customers = mysql_fetch_assoc($customers));
  $rows = mysql_num_rows($customers);
  if($rows > 0) {
      mysql_data_seek($customers, 0);
	  $row_customers = mysql_fetch_assoc($customers);
  }
?>
          </select></td>
        </tr>
        <input type="hidden" name="MM_insert" value="frm_add_as" />
        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      </form>
      </table>
      
      
<?php 
	}
	?>
    
    <?php if ($totalRows_asses > 0) { ?>
    
    <form action="" method="post" target="_self" id="as_action" name="as_action">
        <input type="hidden" name="totalRows_asses" value="<?php echo $totalRows_asses; ?>" />
        <input type="hidden" name="as_action_type" value="delete" />
        
        <?php 
		
	  	$count = 0;
			
		do { 
	  		
			mysql_select_db($database_subman, $subman);
			$query_customer = "SELECT * FROM customer WHERE customer.id = '".$row_asses['customer']."'";
			$customer = mysql_query($query_customer, $subman) or die(mysql_error());
			$row_customer = mysql_fetch_assoc($customer);
			$totalRows_customer = mysql_num_rows($customer);
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			}
			?>
        
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_asses['id']; ?>" value="1" />
            <input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_asses['id']; ?>" /></td>
          <td><?php echo $row_asses['number']; ?></td>
          <td><?php echo $row_asses['name']; ?></td>
          <td><?php echo $row_asses['descr']; ?></td>
          <td><?php echo $row_asses['passwd']; ?></td>
          <td><a href="?browse=customers&amp;customer=<?php echo $row_customer['id']; ?>&amp;container=<?php echo $_GET['container']; ?>" title="Browse this customer"><?php echo $row_customer['name']; ?></a></td>
        </tr>
        <?php } while ($row_asses = mysql_fetch_assoc($asses)); ?>
    </table>
    </form>
    
    <?php } else { ?>
      
      	<p>There are no BGP Autonomous Systems to display.</p>
        
      <?php } ?>










      
<?php

	}	
	
	
	elseif ($_GET['networkgroups'] == 1) {
		
		if ($_POST['action'] == "delete_network_group") {
			
            mysql_select_db($database_subman, $subman);
			$query_networkGroup = "SELECT * FROM networkgroup WHERE networkgroup.id = ".$_GET['group']."";
			$networkGroup = mysql_query($query_networkGroup, $subman) or die(mysql_error());
			$row_networkGroup = mysql_fetch_assoc($networkGroup);
			$totalRows_networkGroup = mysql_num_rows($networkGroup);
			?>
            
             Networks <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;networkgroups=1" title="Browse network groups">Network Groups</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_networkGroup['name']; ?></strong>
             
             <p>&nbsp;</p>
             
             <?php
			
				if (!(getNetGroupLevel($_GET['group'], $_SESSION['MM_Username']) > 20 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getNetGroupLevel($_GET['group'],$_SESSION['MM_Username']) == ""))) { ?>
		<p class="text_red">Error: You are not authorised to view the selected content.</p>
		<?php 
						exit();
				} ?>
                
			<form action="" method="post" target="_self" name="frm_delete_netgroup" id="frm_delete_netgroup">
              <p>Are you sure you want to delete this network group?</p>
              <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
              <input name="id" type="hidden" id="id" value="<?php echo $_GET['group']; ?>" />
              <input name="MM_delete" type="hidden" id="MM_delete" value="frm_delete_netgroup" />
              <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
            </form>
		
        <?php	
		}
		
		elseif ($_GET['group'] != "") {
			
			mysql_select_db($database_subman, $subman);
			$query_networkGroup = "SELECT * FROM networkgroup WHERE networkgroup.id = ".$_GET['group']."";
			$networkGroup = mysql_query($query_networkGroup, $subman) or die(mysql_error());
			$row_networkGroup = mysql_fetch_assoc($networkGroup);
			$totalRows_networkGroup = mysql_num_rows($networkGroup);
			?>
            
             Networks <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;networkgroups=1" title="Browse network groups">Network Groups</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_networkGroup['name']; ?></strong>
             
             <p>&nbsp;</p>
             
             <?php
			
				if (!(getNetGroupLevel($_GET['group'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getNetGroupLevel($_GET['group'],$_SESSION['MM_Username']) == ""))) { ?>
		<p class="text_red">Error: You are not authorised to view the selected content.</p>
		<?php 
						exit();
				} ?>
				
			<form action="<?php echo $editFormAction; ?>" method="post" id="frm_editnetgroup" onSubmit="MM_validateForm('groupname','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td align="right" valign="top">Name:</td>
          <td><input name="groupname" type="text" class="input_standard" id="groupname" value="<?php echo htmlentities($row_networkGroup['name'], ENT_COMPAT, 'utf-8'); ?>" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="top">Description:</td>
          <td><textarea cols="20" rows="5" class="input_standard" name="descr"><?php echo htmlentities($row_networkGroup['descr'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Update network group" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="MM_update" value="frm_editnetgroup" />
      <input type="hidden" name="id" value="<?php echo $row_networkGroup['id']; ?>" />
    </form>	
		
        <?php	
		}
		
		elseif ($_POST['action'] == "add_network_group") {
		
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				} ?>
                
            Networks <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>Network Groups</strong>
            
            <p>&nbsp;</p>
            
            <form name="frm_addnetgroup" id="frm_addnetgroup" method="post" target="_self" action=""  onSubmit="MM_validateForm('groupname','','R');return document.MM_returnValue">
      			<table>
        <tr valign="baseline">
          <td align="right" valign="top">Name:</td>
          <td><input name="groupname" type="text" class="input_standard" id="groupname" value="" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="top">Description:</td>
          <td><textarea cols="20" rows="5" class="input_standard" name="descr"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add network group" /></td>
        </tr>
      </table>
      			<input type="hidden" name="MM_insert" value="frm_addnetgroup" />
                <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
    		</form>
    
    <?php }
		
		else {
                
			mysql_select_db($database_subman, $subman);
			$query_networkGroups = "SELECT * FROM networkgroup WHERE networkgroup.container = ".$_GET['container']." ORDER BY networkgroup.name";
			$networkGroups = mysql_query($query_networkGroups, $subman) or die(mysql_error());
			$row_networkGroups = mysql_fetch_assoc($networkGroups);
			$totalRows_networkGroups = mysql_num_rows($networkGroups);
			?>
            
             Networks <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>Network Groups</strong>
             
             <p>&nbsp;</p>
             
             <?php if ($totalRows_networkGroups > 0) { ?>
             
            <table border="0" width="50%">
              <tr>
                <td><strong>Network Groups</strong></td>
              </tr>
              <?php $count = 0;
                do {
                    
                    $count++;
                    if ($count % 2) {
                        $bgcolour = "#EAEAEA";
                    }
                    else {
                        $bgcolour = "#F5F5F5";
                    } ?>
                <tr bgcolor="<?php echo $bgcolour; ?>">
                  <td><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;networkgroups=1&amp;group=<?php echo $row_networkGroups['id']; ?>" title"Edit this network group"><?php echo $row_networkGroups['name']; ?></a></td>
                </tr>
                <?php } while ($row_networkGroups = mysql_fetch_assoc($networkGroups)); ?>
            </table>
            
            <?php } else { ?>
            
            	<p>There are no network groups to display.</p>
                
            <?php } ?>
			
    <?php 
		}
		
	}
		
	elseif ($_GET['browse'] == "networks") { 
	
	if ($_GET['group'] == "" && $_GET['parent'] == "") {
		mysql_select_db($database_subman, $subman);
		$query_base_parent = "SELECT MIN(networks.parent) FROM networks WHERE container = ".$_GET['container'];
		$base_parent = mysql_query($query_base_parent, $subman) or die(mysql_error());
		$row_base_parent = mysql_fetch_assoc($base_parent);
		$totalRows_base_parent = mysql_num_rows($base_parent);
		
		$query_networks = "SELECT * FROM networks WHERE networks.parent = '".$row_base_parent['MIN(networks.parent)']."' AND historic != 1 AND container = ".$_GET['container']." ORDER BY networks.network";
	}
	elseif ($_GET['group'] != "" && ($_GET['parent'] == "" || $_GET['parent'] == 0)) {			
		mysql_select_db($database_subman, $subman);
		$query_base_parent = "SELECT id FROM networks WHERE networks.networkGroup = '".$_GET['group']."' AND container = ".$_GET['container']."";
		$base_parent = mysql_query($query_base_parent, $subman) or die(mysql_error());
		$row_base_parent = mysql_fetch_assoc($base_parent);
		$totalRows_base_parent = mysql_num_rows($base_parent);
		
		$arr_base_parent = array();
		
		do {
			array_push($arr_base_parent,$row_base_parent['id']);
		} while ($row_base_parent = mysql_fetch_assoc($base_parent));
			
		
		$query_networks = "SELECT * FROM networks WHERE networkGroup = '".$_GET['group']."' AND historic != 1 AND container = ".$_GET['container']." ORDER BY networks.network";
	}
	elseif ($_GET['parent'] != "") {			
		$query_networks = "SELECT * FROM networks WHERE networks.parent = '".$_GET['parent']."' AND historic != 1 AND container = ".$_GET['container']." ORDER BY networks.network";

		mysql_select_db($database_subman, $subman);
		$query_parent = "SELECT * FROM networks WHERE networks.id = '".$_GET['parent']."' AND container = ".$_GET['container']."";
		$parent = mysql_query($query_parent, $subman) or die(mysql_error());
		$row_parent = mysql_fetch_assoc($parent);
		$totalRows_parent = mysql_num_rows($parent);
	}
	else {
		$query_networks = "SELECT * FROM networks WHERE networks.parent = '".$_GET['parent']."' AND networkGroup = '".$_GET['group']."' AND historic != 1 AND container = ".$_GET['container']." ORDER BY networks.network";
		
		mysql_select_db($database_subman, $subman);
		$query_parent = "SELECT * FROM networks WHERE networks.id = '".$_GET['parent']."' AND container = ".$_GET['container']."";
		$parent = mysql_query($query_parent, $subman) or die(mysql_error());
		$row_parent = mysql_fetch_assoc($parent);
		$totalRows_parent = mysql_num_rows($parent);
	}
	$networks = mysql_query($query_networks, $subman) or die(mysql_error());
	$row_networks = mysql_fetch_assoc($networks);
	$totalRows_networks = mysql_num_rows($networks);
	
	if ($row_parent['v6mask'] == "") {
		$net = find_net(long2ip($row_parent['network']), $row_parent['mask']);
	}
	?>
    <?php
		if ($_GET['parent'] != "" && $_GET['parent'] != 0) { 
			
			mysql_select_db($database_subman, $subman);
			$query_routes = "SELECT linknets.*, links.container, links.provide_node_a AS deviceid, portsdevices.devicegroup, links.provide_card_node_a AS cardid, links.provide_port_node_a AS portid FROM linknets LEFT JOIN links ON links.id = linknets.link LEFT JOIN portsdevices ON portsdevices.id = links.provide_node_a WHERE linknets.network = '".$_GET['parent']."'";
			$routes = mysql_query($query_routes, $subman) or die(mysql_error());
			$row_routes = mysql_fetch_assoc($routes);
			$totalRows_routes = mysql_num_rows($routes);
			?>
    Networks <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;parent=<?php echo $row_parent['parent']; ?>" title="<?php echo $row_parent['comments']; ?>"><?php if ($row_parent['v6mask'] == "") { echo $net['cidr']; } else { echo Net_IPv6::compress(long2ipv6($row_parent['network']))."/".$row_parent['v6mask']; } ?></a> &nbsp;<?php if ($row_parent['v6mask'] == "") { echo $row_parent['mask']; } ?> &nbsp;<?php echo $row_parent['descr']; ?> <?php if ($totalRows_routes > 0) { ?><a href="?browse=devices&amp;container=<?php echo $row_routes['container']; ?>&amp;group=<?php echo $row_routes['devicegroup']; ?>&amp;device=<?php echo $row_routes['deviceid']; ?>&amp;card=<?php echo $row_routes['cardid']; ?>&amp;port=<?php echo $row_routes['portid']; ?>&amp;linkview=1" title="This network is attached to a link as a route, click to view the link"><img src="images/link.gif" alt="Link" align="absmiddle" /></a><?php } ?><br />
    <br />
    <?php 
		} elseif ($_GET['group'] != "") {
		
			mysql_select_db($database_subman, $subman);
			$query_networkGroup = "SELECT * FROM networkgroup WHERE networkgroup.id = ".$_GET['group']."";
			$networkGroup = mysql_query($query_networkGroup, $subman) or die(mysql_error());
			$row_networkGroup = mysql_fetch_assoc($networkGroup);
			$totalRows_networkGroup = mysql_num_rows($networkGroup);
		
		?>
		
		Networks <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_networkGroup['name']; ?></strong><br />
    	<br />
    	
    	<?php
    	}
		else { 
		?>
	Networks <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>All Networks</strong><br />
    <br />
    
    <?php } ?>
    <?php
				if ($_POST['action'] == "delete_network") {
        
	        if (!(getNetworkLevel($row_parent['id'], $_SESSION['MM_Username']) > 10 || (getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 10 && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				} ?>
    <form action="" method="post" target="_self" name="frm_delete_network" id="frm_delete_network">
      <p>Are you sure you want to delete this network?  
   	  <li>Any addressing information will be deleted.</li>
        <li>If this network is subnetted, the subnets will be re-assigned to the parent network.</li>
        <li>If there are any subscriber routes associated with this network, the network WILL NOT BE DELETED.</li>
      </p>
      <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
      <input name="id" type="hidden" id="id" value="<?php echo $_GET['parent']; ?>" />
      <input name="frm_delete_network" type="hidden" id="frm_delete_network" value="1" />
      <input name="group" type="hidden" id="group" value="<?php echo $_GET['group']; ?>" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
    </form>
    <?php
				}
				elseif ($_POST['action'] == "edit_network") {
        
	        if (!(getNetworkLevel($row_parent['id'], $_SESSION['MM_Username']) > 10 || (getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 10 && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				} ?>
    <form action="<?php echo $editFormAction; ?>" method="post" id="form1" onSubmit="MM_validateForm('descr','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td align="right" valign="top">Network Description:</td>
          <td><input name="descr" type="text" class="input_standard" id="descr" value="<?php echo htmlentities($row_parent['descr'], ENT_COMPAT, 'utf-8'); ?>" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="top">Comments:</td>
          <td><textarea name="comments" cols="32" rows="5" class="input_standard"><?php echo htmlentities($row_parent['comments'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right">Network Group:</td>
          <td><select name="networkGroup" class="input_standard">
            <option value="">None</option>
            <?php 
		mysql_select_db($database_subman, $subman);
		$query_network_groups = "SELECT * FROM networkgroup WHERE networkgroup.`container` = '".$_GET['container']."' ORDER BY networkgroup.name";
		$network_groups = mysql_query($query_network_groups, $subman) or die(mysql_error());
		$row_network_groups = mysql_fetch_assoc($network_groups);
		$totalRows_network_groups = mysql_num_rows($network_groups);
do {  
?>
            <option value="<?php echo $row_network_groups['id']?>" <?php if (!(strcmp($row_network_groups['id'], htmlentities($row_parent['networkGroup'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_network_groups['name']?></option>
            <?php
} while ($row_network_groups = mysql_fetch_assoc($network_groups));
?>
          </select></td>
        </tr>
        <tr> </tr>
        <tr valign="baseline">
          <td align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Update network" /></td>
        </tr>
      </table>
      <input type="hidden" name="updateUser" value="<?php echo $_SESSION['MM_Username']; ?>" />

      <input type="hidden" name="updateDate" value="<?php echo htmlentities($row_parent['updateDate'], ENT_COMPAT, 'utf-8'); ?>" />
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="MM_update" value="form1" />
      <input type="hidden" name="id" value="<?php echo $row_parent['id']; ?>" />
    </form>
    <?php }
	elseif ($_POST['action'] == "add_network") { 

		$network_mask = get_dotted_mask($_POST['add_network_mask']); ?>
    <form name="frm_add_network_change_mask" id="frm_add_network_change_mask" method="post" target="_self" action="">
      <input type="hidden" name="action" value="add_network" />
      <select name="add_network_mask" class="input_standard" onChange="document.frm_add_network_change_mask.submit();">
      
      <optgroup label="Common prefix lengths">
        <?php if (get_slash($row_parent['mask'],0) < 16) { ?>
      	 <option value="16" <?php if (!(strcmp(32, $_POST['add_network_mask']))) {echo "SELECTED";} ?>>255.255.0.0 : /16</option>
      	<?php } if (get_slash($row_parent['mask'],0) < 24) { ?>
      	 <option value="24" <?php if (!(strcmp(48, $_POST['add_network_mask']))) {echo "SELECTED";} ?>>255.255.255.0 : /24</option>
      	<?php } if (get_slash($row_parent['mask'],0) < 28) { ?>
      	 <option value="28" <?php if (!(strcmp(48, $_POST['add_network_mask']))) {echo "SELECTED";} ?>>255.255.255.240 : /28</option>
      	<?php } if (get_slash($row_parent['mask'],0) < 29) { ?>
      	 <option value="29" <?php if (!(strcmp(48, $_POST['add_network_mask']))) {echo "SELECTED";} ?>>255.255.255.248 : /29</option>
      	<?php } if (get_slash($row_parent['mask'],0) < 30) { ?>
      	 <option value="30" <?php if (!(strcmp(48, $_POST['add_network_mask']))) {echo "SELECTED";} ?>>255.255.255.252 : /30</option>
      	<?php } ?>
      </optgroup>
      <optgroup label="All prefix lengths">
        <?php $sizeOfNet = strpos(decbin(ip2long($row_parent['mask'])),"0");
			$i = 32;
			while ($i > $sizeOfNet) { ?>
        <option value="<?php echo $i; ?>" <?php if ($network_mask == get_dotted_mask($i)) { echo "selected=\"selected\""; } ?>><?php echo get_dotted_mask($i); ?> : /<?php echo $i; ?></option>
        <?php
			$i--;
			} ?>
		</optgroup>
      </select>
      <input name="go" type="submit" class="input_standard" id="go" value="Go" />
    </form>
    <?php
	
		if ($network_mask == "") { ?>
    <p>Please select a mask from the drop-down list.</p>
    <?php }
		else {
		
		$net = find_net(long2ip($row_parent['network']),$row_parent['mask']);
		
		$networks = array();
		
		$nextNet = $net['network'];

		do {
			
			$count++;
			
			$net1 = find_net(long2ip($nextNet),$network_mask);
			
		mysql_select_db($database_subman, $subman);
		$query_subnets = "SELECT * FROM networks WHERE (((networks.network > ".($nextNet-1)." AND networks.network < ".($net1['broadcast']+1).")) AND networks.historic != 1 AND networks.maskLong > INET_ATON('".$row_parent['mask']."') AND networks.container = ".$_GET['container'].") ORDER BY networks.maskLong ASC LIMIT 1";
		$subnets = mysql_query($query_subnets, $subman) or die(mysql_error());
		$row_subnets = mysql_fetch_assoc($subnets);
		$totalRows_subnets = mysql_num_rows($subnets);
		
			if ($totalRows_subnets > 0) {
				
				$net2 = find_net(long2ip($row_subnets['network']),$row_subnets['mask']);
				
				if ($net2['broadcast'] < $net1['broadcast']) {
					$nextNet = $net1['broadcast']+1;
				}
				else {
					$nextNet = $net2['broadcast']+1;
				}
			}
			else {
				array_push($networks,$net1['network']);
				$nextNet = $net1['broadcast']+1;
			}

		} while (($nextNet < $net['broadcast']) && ($count < 256));
		
		if (count($networks) == 0) {
	?>
    <p>There are no
      <?php if ($network_mask = "255.255.255.255") { } else { echo get_slash($network_mask); } ?>
      slots available within this network.</p>
    <?php } else { ?>
    <p>Please select from one of the first 255 available networks below.  To add a network manually, please select the appropriate menu option.</p>
    <form action="<?php echo $editFormAction; ?>" method="POST" name="frm_add_network" target="_self" id="frm_add_network" onSubmit="MM_validateForm('descr','','R','network','','R');return document.MM_returnValue">
      <p>
        <select name="network" size="10" class="input_standard" id="network">
          <?php for($i=0;$i<count($networks);$i++) { ?>
          <option value="<?php echo $networks[$i]; ?>"><?php echo long2ip($networks[$i]); ?></option>
          <?php } ?>
        </select>
        <input name="mask" type="hidden" id="mask" value="<?php echo $network_mask; ?>" />
        <input name="parent" type="hidden" id="parent" value="<?php echo $_GET['parent']; ?>" />
        <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
        <input name="networkGroup" type="hidden" id="networkGroup" value="<?php echo $row_parent['networkGroup']; ?>" />
        <input name="user" type="hidden" id="user" value="<?php echo $_SESSION['MM_Username']; ?>" />
      </p>
      <p>Please enter a description for the network:<br />
        <input name="descr" type="text" class="input_standard" id="descr" value="" size="32" maxlength="255" />
      </p>
      <p>Comments:<br />
        <textarea name="comments" cols="32" rows="5" class="input_standard" id="comments"></textarea>
      </p>
      <p>
        <input name="submit" type="submit" class="input_standard" id="submit" value="Add network" />
      </p>
      <input type="hidden" name="MM_insert" value="frm_add_network" />
    </form>
    <?php }
		} ?>
    <?php }
	
	elseif ($_POST['action'] == "add_v6_network") { 

		$network_mask = $_POST['add_network_mask']; ?>
    <form name="frm_add_network_change_mask" id="frm_add_network_change_mask" method="post" target="_self" action="">
      <input type="hidden" name="action" value="add_v6_network" />
      <select name="add_network_mask" class="input_standard" onChange="document.frm_add_network_change_mask.submit();">
      
      <optgroup label="Common prefix lengths">
        <?php if ($row_parent['v6mask'] < 32) { ?>
      	 <option value="32" <?php if (!(strcmp(32, $_POST['add_network_mask']))) {echo "SELECTED";} ?>><strong>/32</strong></option>
      	<?php } if ($row_parent['v6mask'] < 48) { ?>
      	 <option value="48" <?php if (!(strcmp(48, $_POST['add_network_mask']))) {echo "SELECTED";} ?>><strong>/48</strong></option>
      	<?php } if ($row_parent['v6mask'] < 56) { ?>
      	 <option value="56" <?php if (!(strcmp(56, $_POST['add_network_mask']))) {echo "SELECTED";} ?>><strong>/56</strong></option>
      	<?php } if ($row_parent['v6mask'] < 64) { ?>
      	 <option value="64" <?php if (!(strcmp(64, $_POST['add_network_mask']))) {echo "SELECTED";} ?>><strong>/64</strong></option>
      	<?php } ?>
      </optgroup>
      <optgroup label="All prefix lengths">
      	<?php $i = 128;
			while ($i > $row_parent['v6mask']) { ?>
        <option value="<?php echo $i; ?>" <?php if (!(strcmp($i, $_POST['add_network_mask']))) {echo "SELECTED";} ?>>/<?php echo $i; ?></option>
        <?php $i--;
        	} ?>
      </optgroup>
      </select>
      <input name="go" type="submit" class="input_standard" id="go" value="Go" />
    </form>
    <?php
	
		if ($network_mask == "") { ?>
    <p>Please select a mask from the drop-down list.</p>
    <?php }
		else {
		
		$nextNet = $row_parent['network'];
		$nextNetMask = $row_parent['v6mask'];
		
		$networks = array();
		
			if ($nextNetMask < $network_mask) {
				
				do {
					
					$count++;
					
				mysql_select_db($database_subman, $subman);
				$query_subnets = "SELECT * FROM networks WHERE (((networks.network > ".bcsub($nextNet,1)." AND networks.network < ".bcadd($nextNet,bcpow(2,(128 - $network_mask))).")) AND networks.historic != 1 AND networks.v6mask > '".$row_parent['v6mask']."' AND networks.container = ".$_GET['container'].") ORDER BY networks.v6mask ASC LIMIT 1";
				$subnets = mysql_query($query_subnets, $subman) or die(mysql_error());
				$row_subnets = mysql_fetch_assoc($subnets);
				$totalRows_subnets = mysql_num_rows($subnets);
				
					if ($totalRows_subnets > 0) {

						if (bccomp(bcadd($nextNet,bcpow(2,(128 - $network_mask))), bcadd($row_subnets['network'],bcpow(2,(128 - $row_subnets['v6mask'])))) == 1) {
							$nextNet = bcadd($nextNet,bcpow(2,(128 - $network_mask)));
						}
						else {
							$nextNet = bcadd($row_subnets['network'],bcpow(2,(128 - $row_subnets['v6mask'])));
						}
					}
					else {
						array_push($networks,$nextNet);
						$nextNet = bcadd($nextNet,bcpow(2,(128 - $network_mask)));
					}
				
				
				} while (( bccomp((bcadd($row_parent['network'],bcpow(2,(128 - $row_parent['v6mask'])))),$nextNet) == 1 ) && ($count < 256)); 
				
			}
		
		if (count($networks) == 0) {
	?>
    <p>There are no
      /<?php echo $network_mask; ?>
      slots available within this network.</p>
    <?php } else { ?>
    <p>Please select from one of the first 255 available networks below.  To add a network manually, please select the appropriate menu option.</p>
    <form action="<?php echo $editFormAction; ?>" method="POST" name="frm_add_v6_network" target="_self" id="frm_add_v6_network" onSubmit="MM_validateForm('descr','','R','network','','R');return document.MM_returnValue">
      <p>
        <select name="network" size="10" class="input_standard" id="network">
          <?php for($i=0;$i<count($networks);$i++) { ?>
          <option value="<?php echo $networks[$i]; ?>"><?php echo long2ipv6(Net_IPv6::Uncompress($networks[$i])); ?></option>
          <?php } ?>
        </select>
        <input name="mask" type="hidden" id="mask" value="<?php echo $network_mask; ?>" />
        <input name="parent" type="hidden" id="parent" value="<?php echo $_GET['parent']; ?>" />
        <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
        <input name="networkGroup" type="hidden" id="networkGroup" value="<?php echo $row_parent['networkGroup']; ?>" />
        <input name="user" type="hidden" id="user" value="<?php echo $_SESSION['MM_Username']; ?>" />
      </p>
      <p>Please enter a description for the network:<br />
        <input name="descr" type="text" class="input_standard" id="descr" value="" size="32" maxlength="255" />
      </p>
      <p>Comments:<br />
        <textarea name="comments" cols="32" rows="5" class="input_standard" id="comments"></textarea>
      </p>
      <p>
        <input name="submit" type="submit" class="input_standard" id="submit" value="Add network" />
      </p>
      <input type="hidden" name="MM_insert" value="frm_add_v6_network" />
    </form>
    <?php }
		} ?>
    <?php }
	
	elseif ($_POST['action'] == 'add_base_network') { ?>
    <form action="<?php echo $editFormAction; ?>" method="post" id="form2" onSubmit="MM_validateForm('descr','','R','network','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td align="right">IPv4 Network:</td>
          <td><input name="network" type="text" class="input_standard" id="network" value="" size="20" maxlength="15" />
            <input name="parent" type="hidden" id="parent" value="<?php echo $_GET['parent']; ?>" /></td>
        </tr>
        <tr valign="baseline">
          <td align="right">Mask:</td>
          <td><select name="mask" class="input_standard">
            <optgroup label="Common prefix lengths">
      		 <option value="255.255.0.0" <?php if (!(strcmp(32, $_POST['add_network_mask']))) {echo "SELECTED";} ?>>255.255.0.0 : /16</option>
      		 <option value="255.255.255.0" <?php if (!(strcmp(48, $_POST['add_network_mask']))) {echo "SELECTED";} ?>>255.255.255.0 : /24</option>
      		 <option value="255.255.255.240" <?php if (!(strcmp(48, $_POST['add_network_mask']))) {echo "SELECTED";} ?>>255.255.255.240: /28</option>
      		 <option value="255.255.255.248" <?php if (!(strcmp(48, $_POST['add_network_mask']))) {echo "SELECTED";} ?>>255.255.255.248 : /29</option>
      		 <option value="255.255.255.252" <?php if (!(strcmp(48, $_POST['add_network_mask']))) {echo "SELECTED";} ?>>255.255.255.252 : /30</option>
      		</optgroup>
      		<optgroup label="All prefix lengths">
        	 <?php for ($i = 8; $i < 33; $i++) { ?>
        		<option value="<?php echo get_dotted_mask($i); ?>"><?php echo get_dotted_mask($i); ?> : /<?php echo $i; ?></option>
        	 <?php } ?>
      		</optgroup>
          </select></td>
        </tr>
        <tr valign="baseline">
          <td align="right">Description:</td>
          <td><input name="descr" type="text" class="input_standard" id="descr" value="" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="top">Comments:</td>
          <td><textarea name="comments" cols="32" rows="5" class="input_standard"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add base network" /></td>
        </tr>
      </table>
      <input type="hidden" name="user" value="<?php echo $_SESSION['MM_Username']; ?>" />
      <input type="hidden" name="date" value="" />
      <input type="hidden" name="networkGroup" value="<?php echo $_GET['group']; ?>" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="MM_insert" value="form2" />
    </form>
    <p>&nbsp;</p>
    <?php
	}
	
	elseif ($_POST['action'] == 'add_base_network_v6') { ?>

<form action="<?php echo $editFormAction; ?>" method="post" name="form8" id="form8" onSubmit="MM_validateForm('network','','R','descr','','R');return document.MM_returnValue">
  <table>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">IPv6 Network:</td>
      <td><input name="network" type="text" class="input_standard" value="" size="32" maxlength="39" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Mask:</td>
      <td><select name="mask" class="input_standard">
      <optgroup label="Common prefix lengths">
      	<option value="32" <?php if (!(strcmp(32, $_POST['add_network_mask']))) {echo "SELECTED";} ?>><strong>/32</strong></option>
      	<option value="48" <?php if (!(strcmp(48, $_POST['add_network_mask']))) {echo "SELECTED";} ?>><strong>/48</strong></option>
      	<option value="56" <?php if (!(strcmp(56, $_POST['add_network_mask']))) {echo "SELECTED";} ?>><strong>/56</strong></option>
      	<option value="64" <?php if (!(strcmp(64, $_POST['add_network_mask']))) {echo "SELECTED";} ?>><strong>/64</strong></option>
      </optgroup>
      <optgroup label="All prefix lengths">
        <?php for ($i = 8; $i < 129; $i++) { ?>
        <option value="<?php echo $i; ?>">/<?php echo $i; ?></option>
        <?php } ?>
      </optgroup>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Description:</td>
      <td><input name="descr" type="text" class="input_standard" value="" size="32" maxlength="255" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right" valign="top">Comments:</td>
      <td><textarea name="comments" cols="32" rows="5" class="input_standard"></textarea></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" class="input_standard" value="Add base network" /></td>
    </tr>
  </table>
  <input type="hidden" name="user" value="<?php echo $_SESSION['MM_Username']; ?>" />
  <input type="hidden" name="networkGroup" value="<?php echo $_GET['group']; ?>" />
  <input type="hidden" name="parent" value="<?php echo $_GET['parent']; ?>" />
  <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
  <input type="hidden" name="MM_insert" value="form8" />
</form>
<p>&nbsp;</p>
<?php
	}
	elseif ($totalRows_networks == 0 && $_GET['parent'] != "") {
			
				if (!(getNetworkLevel($row_parent['id'], $_SESSION['MM_Username']) > 0 || (getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 0 && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			if ($row_parent['mask'] == "255.255.255.255" || $row_parent['v6mask'] == 128) { ?>
    <p>This is a host (/32) network.  There is no further address information to display.</p>
    <?php }
			else {
					
					if (isset($_GET['sort'])) {
						$sort = $_GET['sort'];
					}
					else {
						$sort = "addresses.address";
					}
					if (isset($_GET['sortdir'])) {
						$sortdir = $_GET['sortdir'];
					}
					else {
						$sortdir = "ASC";
					}
			
					mysql_select_db($database_subman, $subman);
					$query_addresses = "SELECT addresses.*, customer.name as customername, customer.id as customerID, portsports.port, portsports.id as portID, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.id as devicegroupID FROM addresses LEFT JOIN customer ON customer.id = addresses.customer LEFT JOIN portsports ON (portsports.router = addresses.id) OR (portsports.id = addresses.portid) LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device  LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE addresses.network = '".$_GET['parent']."' ORDER BY ".$sort." ".$sortdir."";
					$addresses = mysql_query($query_addresses, $subman) or die(mysql_error());
					$row_addresses = mysql_fetch_assoc($addresses);
					$totalRows_addresses = mysql_num_rows($addresses);
					
					if ($row_parent['v6mask'] == "") {
						$net = array();
						$net = find_net(long2ip($row_parent['network']),$row_parent['mask']);
					}
					
					if ($row_parent['mask'] == "255.255.255.255" || $row_parent['v6mask'] == 128) {
						$remaining = 0;
					}
					else {
						if ($row_parent['mask'] == "255.255.255.254") {
							$remaining = 2 - $totalRows_addresses;
						}
						elseif ($row_parent['v6mask'] == "") {
			   			 	$remaining = ($net['broadcast'] - $net['network'] - 1) - $totalRows_addresses;
						}
						else {
							$remaining = bcpow(2,(128 - $row_parent['v6mask']) - 1);
						}
					}

					
		?>
    <table border="0" width="100%">
      <tr>
        <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('address_action'), this.checked);" /></td>
        <td><strong><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;parent=<?php echo $_GET['parent']; ?>&amp;sort=addresses.address&amp;sortdir=<?php if ($sortdir == "ASC") { echo "DESC"; } else { echo "ASC"; } ?>" title="Sort by this column">Address</a></strong><?php if ($sort == "addresses.address") { ?><?php if ($sortdir == "ASC") { ?><img src="h_arrow_over.gif" alt="Sorted by this column (ascending)" /><?php } else { ?><img src="h_arrow_over_up.gif" alt="Sorted by this column (descending)" /><?php } ?><?php } ?></td>
        <td><strong><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;parent=<?php echo $_GET['parent']; ?>&amp;sort=addresses.descr&amp;sortdir=<?php if ($sortdir == "ASC") { echo "DESC"; } else { echo "ASC"; } ?>" title="Sort by this column">Description</a></strong><?php if ($sort == "addresses.descr") { ?><?php if ($sortdir == "ASC") { ?><img src="h_arrow_over.gif" alt="Sorted by this column (ascending)" /><?php } else { ?><img src="h_arrow_over_up.gif" alt="Sorted by this column (descending)" /><?php } ?><?php } ?></td>
        <td><strong><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;parent=<?php echo $_GET['parent']; ?>&amp;sort=customer.name&amp;sortdir=<?php if ($sortdir == "ASC") { echo "DESC"; } else { echo "ASC"; } ?>" title="Sort by this column">Customer</a></strong><?php if ($sort == "customer.name") { ?><?php if ($sortdir == "ASC") { ?><img src="h_arrow_over.gif" alt="Sorted by this column (ascending)" /><?php } else { ?><img src="h_arrow_over_up.gif" alt="Sorted by this column (descending)" /><?php } ?><?php } ?></td>
        <td><strong><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;parent=<?php echo $_GET['parent']; ?>&amp;sort=portsdevices.name,cards.rack,cards.module,cards.slot,portsports.port&amp;sortdir=<?php if ($sortdir == "ASC") { echo "DESC"; } else { echo "ASC"; } ?>" title="Sort by this column">Device Port</a></strong><?php if ($sort == "portsdevices.name,cards.rack,cards.module,cards.slot,portsports.port") { ?><?php if ($sortdir == "ASC") { ?><img src="h_arrow_over.gif" alt="Sorted by this column (ascending)" /><?php } else { ?><img src="h_arrow_over_up.gif" alt="Sorted by this column (descending)" /><?php } ?><?php } ?></td>
        <td><strong><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;parent=<?php echo $_GET['parent']; ?>&amp;sort=addresses.comments&amp;sortdir=<?php if ($sortdir == "ASC") { echo "DESC"; } else { echo "ASC"; } ?>" title="Sort by this column">Comments</a></strong><?php if ($sort == "addresses.comments") { ?><?php if ($sortdir == "ASC") { ?><img src="h_arrow_over.gif" alt="Sorted by this column (ascending)" /><?php } else { ?><img src="h_arrow_over_up.gif" alt="Sorted by this column (descending)" /><?php } ?><?php } ?></td>
      </tr>
      <?php if ($_GET['action'] == "edit_address") { 
      
			if (!(getNetworkLevel($row_parent['id'], $_SESSION['MM_Username']) > 10 || (getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 10 && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == ""))) { ?>
      <p class="text_red">Error: You are not authorised to view the selected content.</p>
      <?php 
					exit();
				}
				
	  		mysql_select_db($database_subman, $subman);
			$query_customers = "SELECT * FROM customer WHERE container = '".$_GET['container']."' ORDER BY customer.name";
			$customers = mysql_query($query_customers, $subman) or die(mysql_error());
			$row_customers = mysql_fetch_assoc($customers);
			$totalRows_customers = mysql_num_rows($customers);
			
			mysql_select_db($database_subman, $subman);
			$query_address = "SELECT addresses.*, customer.name as customername, customer.id as customerID FROM addresses LEFT JOIN customer ON customer.id = addresses.customer WHERE addresses.id = ".$_GET['address']."";
			$address = mysql_query($query_address, $subman) or die(mysql_error());
			$row_address = mysql_fetch_assoc($address);
			$totalRows_address = mysql_num_rows($address);
			
	?>
      <form action="containerView.php" method="post" target="_self" id="frm_edit_address" onSubmit="MM_validateForm('address','','R','descr','','R');return document.MM_returnValue" >
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top"><input name="address" type="hidden" class="input_standard" value="<?php echo $row_address['address']; ?>" />
            <strong><?php if ($row_parent['v6mask'] == "" ) { echo long2ip($row_address['address']); } else { echo Net_IPv6::compress(long2ipv6($row_address['address'])); } ?></strong> <br />
            <input type="submit" class="input_standard" value="Update address" />
            <input name="network" type="hidden" id="network" value="<?php echo $_GET['parent']; ?>" />
            <input name="user" type="hidden" id="user" value="<?php echo $_SESSION['MM_Username']; ?>" />
            <input type="hidden" value="<?php echo $_GET['address']; ?>" name="id" /></td>
          <td valign="top"><input name="descr" type="text" class="input_standard" id="descr" value="<?php echo $row_address['descr']; ?>" size="32" maxlength="255" /></td>
          <td valign="top"><select name="customer" class="input_standard" id="customer">
            <?php
do {  
?>
            <option value="<?php echo $row_customers['id']?>"<?php if (!(strcmp($row_customers['id'], $row_address['customer']))) {echo "selected=\"selected\"";} ?>><?php echo $row_customers['name']?></option>
            <?php
} while ($row_customers = mysql_fetch_assoc($customers));
  $rows = mysql_num_rows($customers);
  if($rows > 0) {
      mysql_data_seek($customers, 0);
	  $row_customers = mysql_fetch_assoc($customers);
  }
?>
          </select></td>
          <td><?php if ($row_address['portID'] != "") { ?><strong><?php echo $row_address['devicename']; ?><br /><?php echo $row_address['cardtypename']; ?> <?php if (!(isset($row_address['rack'])) && !(isset($row_address['module'])) && !(isset($row_address['slot']))) { echo "Virtual"; } else { if (isset($row_address['rack'])) { echo $row_address['rack']."/"; } if (isset($row_address['module'])) { echo $row_address['module'].'/'; } if (isset($row_address['slot'])) { echo $row_address['slot']; } } ?>/<?php echo $row_address['port']; ?><?php if ($row_address['subint'] != "") { ?><font color="#FF0000">.<?php echo $row_address['subint']; ?></font></strong><?php } } ?></td>
          <td valign="top"><textarea name="comments" cols="20" rows="2" class="input_standard" id="comments"><?php echo $row_address['comments']; ?></textarea></td>
        </tr>
        <input type="hidden" name="group" value="<?php echo $_GET['group']; ?>" />
        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
        <input type="hidden" name="parent" value="<?php echo $_GET['parent']; ?>" />
        <input type="hidden" name="MM_update" value="frm_edit_address" />
      </form>
      <?php } ?>
      <?php if ($_POST['action'] == "add_address") { 
	  		
			if (!(getNetworkLevel($row_parent['id'], $_SESSION['MM_Username']) > 10 || (getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 10 && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == ""))) { ?>
      <p class="text_red">Error: You are not authorised to view the selected content.</p>
      <?php 
					exit();
				}
				
	  		mysql_select_db($database_subman, $subman);
			$query_customers = "SELECT * FROM customer WHERE container = '".$_GET['container']."' ORDER BY customer.name";
			$customers = mysql_query($query_customers, $subman) or die(mysql_error());
			$row_customers = mysql_fetch_assoc($customers);
			$totalRows_customers = mysql_num_rows($customers);
			
			if ($row_parent['mask'] == "255.255.255.254") {
				
				mysql_select_db($database_subman, $subman);
				$query_nextAddr = "SELECT * FROM addresses WHERE addresses.network = ".$_GET['parent']." AND addresses.address = ".$row_parent['network']."";
				$nextAddr = mysql_query($query_nextAddr, $subman) or die(mysql_error());
				$row_nextAddr = mysql_fetch_assoc($nextAddr);
				$totalRows_nextAddr = mysql_num_rows($nextAddr);

				if ( $totalRows_nextAddr == 0 ) {
					$nextAddr = $row_parent['network'];
				}
				else {
					$nextAddr = $row_parent['network']+1;
				}
				
			}		
			else {
				
				if ($row_parent['v6mask'] == "") {
					
					for ($i = ($net['network'] + 1); $i < $net['broadcast']; $i ++) {
						mysql_select_db($database_subman, $subman);
						$query_nextAddr = "SELECT * FROM addresses WHERE addresses.network = ".$_GET['parent']." AND addresses.address = ".$i."";
						$nextAddr = mysql_query($query_nextAddr, $subman) or die(mysql_error());
						$row_nextAddr = mysql_fetch_assoc($nextAddr);
						$totalRows_nextAddr = mysql_num_rows($nextAddr);
		
							if ( $totalRows_nextAddr == 0 ) {
										$nextAddr = $i;
										$i = ($net['broadcast'] - 1);
										}
							}
				}
				else {
					
					for ($i = (bcadd($row_parent['network'], 1)); bccomp((bcadd($row_parent['network'],bcpow(2,(128 - $row_parent['v6mask'])))),$i) > 0; $i = (bcadd($i,1))) {
	
						mysql_select_db($database_subman, $subman);
						$query_nextAddr = "SELECT * FROM addresses WHERE addresses.network = ".$_GET['parent']." AND addresses.address = ".$i."";
						$nextAddr = mysql_query($query_nextAddr, $subman) or die(mysql_error());
						$row_nextAddr = mysql_fetch_assoc($nextAddr);
						$totalRows_nextAddr = mysql_num_rows($nextAddr);
		
							if ( $totalRows_nextAddr == 0 ) {
										$nextAddr = $i;
										$i = bcadd($row_parent['network'],bcpow(2,(128 - $row_parent['v6mask'])));
										}
							}
				}
				
			}
	?>
      <form action="<?php echo $editFormAction; ?>" method="post" target="_self" id="frm_add_address" onSubmit="MM_validateForm('address','','R','descr','','R');return document.MM_returnValue" >
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top"><input name="address" type="text" class="input_standard" value="<?php if ($row_parent['v6mask'] == "") { if (long2ip($nextAddr) == "0.0.0.0") { echo (long2ip($nw + 1)); } else { echo long2ip($nextAddr); } } else { if (long2ipv6($nextAddr) == "0:0:0:0:0:0:0:0") { echo Net_IPv6::compress(long2ipv6(bcadd($row_parent['network'],1))); } else { echo Net_IPv6::compress(long2ipv6($nextAddr)); } } ?>" size="16" maxlength="38" />
            <br />
            <input type="submit" class="input_standard" value="Add address" />
            <a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;parent=<?php echo $_GET['parent']; ?>" title="Cancel">Cancel</a>
            <input name="network" type="hidden" id="network" value="<?php echo $_GET['parent']; ?>" />
            <input name="user" type="hidden" id="user" value="<?php echo $_SESSION['MM_Username']; ?>" /></td>
          <td valign="top"><input name="descr" type="text" class="input_standard" id="descr" size="32" maxlength="255" /></td>
          <td valign="top"><select name="customer" class="input_standard" id="customer">
            <?php
do {  
?>
            <option value="<?php echo $row_customers['id']?>"><?php echo $row_customers['name']?></option>
            <?php
} while ($row_customers = mysql_fetch_assoc($customers));
  $rows = mysql_num_rows($customers);
  if($rows > 0) {
      mysql_data_seek($customers, 0);
	  $row_customers = mysql_fetch_assoc($customers);
  }
?>
          </select></td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td valign="top"><textarea name="comments" cols="20" rows="2" class="input_standard" id="comments"></textarea></td>
        </tr>
        <input type="hidden" name="MM_insert" value="frm_add_address" />
      </form>
      <?php } ?>
      <?php if ($totalRows_addresses > 0) { // Show if recordset not empty ?>
      <form action="" method="post" target="_self" id="address_action" name="address_action">
        <input type="hidden" name="totalRows_addresses" value="<?php echo $totalRows_addresses; ?>" />
        <input type="hidden" name="address_action_type" value="delete" />
        <?php 
  		$count = 0;
		do { 
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			}
			
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
			
			?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_addresses['id']; ?>" value="1" />
            <input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_addresses['id']; ?>" /></td>
          <td><a href="?browse=<?php echo $_GET['browse']; ?>&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;parent=<?php echo $_GET['parent']; ?>&amp;action=edit_address&amp;address=<?php echo $row_addresses['id']; ?>" title="Edit this address" name="<?php echo $row_addresses['id']; ?>" id="<?php echo $row_addresses['id']; ?>"><?php if ($row_parent['v6mask'] == "") { echo long2ip($row_addresses['address']); } else { echo Net_IPv6::compress(long2ipv6($row_addresses['address'])); } ?></a></td>
          <td><?php echo $row_addresses['descr']; ?></td>
          <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_addresses['customer']; ?>" title="Browse this customer"><?php echo $row_addresses['customername']; ?></a></td>
          <td><?php if ($totalRows_address_ports > 0 && $totalRows_address_subints == 0) { ?><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_address_ports['devicegroupID']; ?>&device=<?php echo $row_address_ports['deviceID']; ?>&amp;card=<?php echo $row_address_ports['cardid']; ?>" title="Browse this device line card"><?php echo $row_address_ports['devicename']; ?><br /><?php echo $row_address_ports['cardtypename']; ?> <?php if (!(isset($row_address_ports['rack'])) && !(isset($row_address_ports['module'])) && !(isset($row_address_ports['slot']))) { echo "Virtual"; } else { if (isset($row_address_ports['rack'])) { echo $row_address_ports['rack']."/"; } if (isset($row_address_ports['module'])) { echo $row_address_ports['module'].'/'; } if (isset($row_address_ports['slot'])) { echo $row_address_ports['slot']; } } ?>/<?php echo $row_address_ports['port']; ?></a> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_address_ports['devicegroupID']; ?>&device=<?php echo $row_address_ports['deviceID']; ?>&amp;card=<?php echo $row_address_ports['cardid']; ?>&amp;port=<?php echo $row_address_ports['id']; ?>&amp;linkview=1" title="Browse link"><img src="images/link.gif" alt="Browse link" border="0" align="absmiddle" /></a><?php } ?>
          		<?php if ($totalRows_address_subints > 0) { ?><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_address_subints['devicegroupID']; ?>&device=<?php echo $row_address_subints['deviceID']; ?>&amp;card=<?php echo $row_address_subints['cardid']; ?>&port=<?php echo $row_address_subints['portID']; ?>" title="Browse this port"><?php echo $row_address_subints['devicename']; ?><br /><?php echo $row_address_subints['cardtypename']; ?> <?php if (!(isset($row_address_subints['rack'])) && !(isset($row_address_subints['module'])) && !(isset($row_address_subints['slot']))) { echo "Virtual"; } else { if (isset($row_address_subints['rack'])) { echo $row_address_subints['rack']."/"; } if (isset($row_address_subints['module'])) { echo $row_address_subints['module'].'/'; } if (isset($row_address_subints['slot'])) { echo $row_address_subints['slot']; } } ?>/<?php echo $row_address_subints['port']; ?><font color="#FF0000">.<?php echo $row_address_subints['subint']; ?></font></a>  <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_address_subints['devicegroupID']; ?>&device=<?php echo $row_address_subints['deviceID']; ?>&amp;card=<?php echo $row_address_subints['cardid']; ?>&amp;port=<?php echo $row_address_subints['portID']; ?>&amp;linkview=1" title="Browse link"><img src="images/link.gif" alt="Browse link" border="0" align="absmiddle" /></a><?php } ?>
          </td>
          <td><?php echo $row_addresses['comments']; ?></td>
        </tr>
        <?php } while ($row_addresses = mysql_fetch_assoc($addresses)); ?>
      </form>
    </table>
    <?php } // Show if recordset not empty 
			else { ?>
    </table>
    <p>There is no address information to display.</p>
    <?php } ?>
    <?php
			}
		}
		else {
			
			if ($totalRows_networks > 0) { 
			
			if ($_GET['parent'] != "" && $_GET['parent'] != "0" && !(getNetworkLevel($row_parent['id'], $_SESSION['MM_Username']) > 0 || (getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 0 && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				if ($_GET['group'] != "" && $_GET['group'] != "0" && !(getNetworkLevel($row_parent['id'], $_SESSION['MM_Username']) > 0 || (getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 0 && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_parent['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
		?>
    <table border="0" width="100%">
      <?php 
	  	$count = 0;
	  	do {
			
			if ($row_networks['v6mask'] == "") { 
				$net = find_net(long2ip($row_networks['network']),$row_networks['mask']);
			}
			
			mysql_select_db($database_subman, $subman);
			$query_check_network_subnets = "SELECT * FROM networks WHERE networks.parent = '".$row_networks['id']."'";
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
      <?php if ($_GET['group'] != "" && ($_GET['parent'] == "" || $_GET['parent'] == 0) && (in_array($row_networks['parent'],$arr_base_parent))) { 
			}
			else {
				$count++;
		?>
      <tr bgcolor="<?php echo $bgcolour; ?>">
        <td><?php if ($totalRows_check_network_subnets > 0) { ?><a href="" onClick="ShowChildren(<?php echo $row_networks['id']; ?>, 1); document.getElementById('expand<?php echo $row_networks['id']; ?>').style.display = 'none'; return false;"><img src="images/expand1.gif" alt="Expand" align="absmiddle" title="Show subnetworks" id="expand<?php echo $row_networks['id']; ?>"></a><?php } ?>&nbsp;<?php if ($totalRows_check_network_subnets > 0) { ?><strong><?php } ?><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;parent=<?php echo $row_networks['id']; ?>" title="<?php echo $row_networks['comments']; ?>"><?php if ($row_networks['v6mask'] == "") { echo long2ip($net['network']); } else { echo Net_IPv6::compress(long2ipv6($row_networks['network'])); } ?><?php if ($row_networks['v6mask'] == "") { if ($row_networks['mask'] == "255.255.255.255") { echo "/32"; } else { echo get_slash($row_networks['mask']); } } else { echo "/".$row_networks['v6mask']; } ?></a><?php if ($totalRows_check_network_subnets > 0) { ?></strong><?php } ?>
        	<?php if ($totalRows_check_network_subnets > 0) { ?><strong><?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_networks['descr']; ?><?php if ($totalRows_check_network_subnets > 0) { ?></strong><?php } ?>
        	<?php if ($totalRows_check_network_subnets > 0) { ?>
      		<br /><span id="child_network<?php echo $row_networks['id']; ?>"></span>
      		<?php } ?>
      	</td>
      </tr>
      
      <?php } ?>
      <?php } while ($row_networks = mysql_fetch_assoc($networks)); ?>
    </table>
    <?php
		}
		else { ?>
        
        	<?php if (($totalRows_networks > 0 || ($totalRows_addresses == 0 && $totalRows_networks == 0)) && ((getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == 127) || ((getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == 127) && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == "") || ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127) && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == ""))) { ?>
        <?php if ($_GET['parent'] != "" && $_GET['parent'] != 0 && $row_parent['v6mask'] == "") { ?>
        <p><a href="#" onClick="document.getElementById('action').value = 'add_base_network'; document.getElementById('frm_network_action').submit(); return false;">Add a base IPv4 network</a></p>
        <?php } elseif ($_GET['parent'] != "" && $_GET['parent'] != 0) { ?>
        <p><a href="#" onClick="document.getElementById('action').value = 'add_base_network_v6'; document.getElementById('frm_network_action').submit(); return false;">Add a a base IPv6 network</a></p>
        <?php } else { ?>
        <p><a href="#" onClick="document.getElementById('action').value = 'add_base_network'; document.getElementById('frm_network_action').submit(); return false;">Add a base IPv4 network</a></p>
        <p><a href="#" onClick="document.getElementById('action').value = 'add_base_network_v6'; document.getElementById('frm_network_action').submit(); return false;">Add a base IPv6 network</a></p>
        <?php } ?>
        <?php } ?>
        
        <?php }
		}
        
	}
        
	
	if ($_GET['browse'] == "customers") {
		
		if ($_POST['action'] == "edit_customer") { 
			
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_customer = "SELECT * FROM customer WHERE customer.id = ".$_GET['customer']."";
			$customer = mysql_query($query_customer, $subman) or die(mysql_error());
			$row_customer = mysql_fetch_assoc($customer);
			$totalRows_customer = mysql_num_rows($customer);
			?>
     
          Customer <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_customer['name']; ?></strong><br /><br />
          
    <form action="<?php echo $editFormAction; ?>" method="post" name="form3" id="form3" onSubmit="MM_validateForm('name','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Customer Name:</td>
          <td><input name="name" type="text" class="input_standard" id="name" value="<?php echo htmlentities($row_customer['name'], ENT_COMPAT, 'utf-8'); ?>" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Account #:</td>
          <td><input name="account" type="text" class="input_standard" value="<?php echo htmlentities($row_customer['account'], ENT_COMPAT, 'utf-8'); ?>" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Update customer" /></td>
        </tr>
      </table>
      <input type="hidden" name="MM_update" value="form3" />
      <input type="hidden" name="id" value="<?php echo $row_customer['id']; ?>" />
    </form>
    <?php }
	
		elseif ($_POST['action'] == "add_customer") { 
			
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				?>
    <form action="<?php echo $editFormAction; ?>" method="post" name="form5" id="form5" onSubmit="MM_validateForm('name','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Customer Name:</td>
          <td><input name="name" type="text" class="input_standard" id="name" value="" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Account #:</td>
          <td><input name="account" type="text" class="input_standard" value="" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add customer" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="MM_insert" value="form5" />
    </form>
    <p>&nbsp;</p>
    
    <?php } ?>
<?php
		
		if ($_POST['action'] == "" && $_GET['customer'] != "") {
			
			mysql_select_db($database_subman, $subman);
			$query_customer = "SELECT * FROM customer WHERE customer.id = ".$_GET['customer']."";
			$customer = mysql_query($query_customer, $subman) or die(mysql_error());
			$row_customer = mysql_fetch_assoc($customer);
			$totalRows_customer = mysql_num_rows($customer);

			mysql_select_db($database_subman, $subman);
			$query_customer_addresses = "SELECT addresses.id as addressID, addresses.address, addresses.descr as addressDesr, networks.id as networkID, networks.network, networks.maskLong, networks.parent, networks.container, networks.v6mask FROM addresses INNER JOIN networks ON networks.id = addresses.network LEFT JOIN customer ON customer.id = addresses.customer where customer.id = ".$_GET['customer']." AND networks.historic != 1 AND networks.container = ".$_GET['container']." ORDER BY networks.network, addresses.address";
			$customer_addresses = mysql_query($query_customer_addresses, $subman) or die(mysql_error());
			$row_customer_addresses = mysql_fetch_assoc($customer_addresses);
			$totalRows_customer_addresses = mysql_num_rows($customer_addresses);
			
			mysql_select_db($database_subman, $subman);
			$query_customer_vlans = "SELECT vlan.*, vlanpool.name as vlanpoolname, vlanpool.id as vlanpoolID, vlanpool.device as deviceID, portsdevices.devicegroup, portsdevices.name as devicename FROM vlan LEFT JOIN vlanpool ON vlanpool.id = vlan.vlanpool LEFT JOIN portsdevices ON portsdevices.id = vlanpool.device WHERE vlan.customer = ".$_GET['customer']." ORDER BY vlan.number";
			$customer_vlans = mysql_query($query_customer_vlans, $subman) or die(mysql_error());
			$row_customer_vlans = mysql_fetch_assoc($customer_vlans);
			$totalRows_customer_vlans = mysql_num_rows($customer_vlans);
			
			mysql_select_db($database_subman, $subman);
			$query_customer_ports = "SELECT portsports.*, cards.rack, cards.module, cards.slot, devicetypes.name as devicetypename, portsdevices.name as devicename, portsdevices.id as deviceID, portsdevices.devicegroup, addresses.address, addresses.id as addressID, addresses.network as addressnetwork, networks.v6mask, customer.name as customername, customer.id as customerID, vlan.name as vlanname, vpn.name as vpnname, vpn.id as vpnID, provider.id as providerID FROM portsports LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype LEFT JOIN addresses ON addresses.id = portsports.router LEFT JOIN networks ON networks.id = addresses.network LEFT JOIN customer ON customer.id = portsports.customer LEFT JOIN vlan ON vlan.id = portsports.vlanid LEFT JOIN vpn ON vpn.id = portsports.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE portsports.customer = ".$_GET['customer']." ORDER BY portsdevices.name, cards.rack, cards.module, cards.slot, portsports.port";
			$customer_ports = mysql_query($query_customer_ports, $subman) or die(mysql_error());
			$row_customer_ports = mysql_fetch_assoc($customer_ports);
			$totalRows_customer_ports = mysql_num_rows($customer_ports);
			
			mysql_select_db($database_subman, $subman);
			$query_customer_port_subints = "SELECT subint.*, portsports.port, portsports.id as portid, cards.rack, cards.module, cards.slot, devicetypes.name as devicetypename, portsdevices.name as devicename, portsdevices.id as deviceID, portsdevices.devicegroup, addresses.address, addresses.id as addressID, addresses.network as addressnetwork, networks.v6mask, customer.name as customername, customer.id as customerID, vlan.name as vlanname, vpn.name as vpnname, vpn.id as vpnID, provider.id as providerID FROM subint LEFT JOIN portsports ON portsports.id = subint.port LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype LEFT JOIN addresses ON addresses.id = subint.router LEFT JOIN networks ON networks.id = addresses.network LEFT JOIN customer ON customer.id = subint.customer LEFT JOIN vlan ON vlan.id = subint.vlanid LEFT JOIN vpn ON vpn.id = subint.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE subint.customer = ".$_GET['customer']." ORDER BY portsdevices.name, cards.rack, cards.module, cards.slot, portsports.port, subint.subint";
			$customer_port_subints = mysql_query($query_customer_port_subints, $subman) or die(mysql_error());
			$row_customer_port_subints = mysql_fetch_assoc($customer_port_subints);
			$totalRows_customer_port_subints = mysql_num_rows($customer_port_subints);	
			
			mysql_select_db($database_subman, $subman);
			$query_customer_vpns = "SELECT vpn.*, provider.name as providername, provider.asnumber, provider.id as providerid FROM vpn LEFT JOIN vpncustomer ON vpncustomer.vpn = vpn.id LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE vpncustomer.customer = ".$_GET['customer']." ORDER BY vpn.name";
			$customer_vpns = mysql_query($query_customer_vpns, $subman) or die(mysql_error());
			$row_customer_vpns = mysql_fetch_assoc($customer_vpns);
			$totalRows_customer_vpns = mysql_num_rows($customer_vpns);
			
			mysql_select_db($database_subman, $subman);
			$query_customer_asses = "SELECT asses.* FROM asses WHERE asses.customer = ".$_GET['customer']." ORDER BY asses.number";
			$customer_asses = mysql_query($query_customer_asses, $subman) or die(mysql_error());
			$row_customer_asses = mysql_fetch_assoc($customer_asses);
			$totalRows_customer_asses = mysql_num_rows($customer_asses);
			
			 ?>
     Customer <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_customer['name']; ?></strong><br /><br />
    
    <p><a href="#_customeraddressing">Customer Addressing</a><br />
      <a href="#_customervlans">Customer VLANs</a><br>
      <a href="#_customerports">Customer Port Usage</a><br />
      <a href="#_customervpns">Customer VPN Membership</a><br />
      <a href="#_customerasses">Customer Autonomous Systems</a>
      
    <h3 id="_customeraddressing">Customer Addressing</h3>
    <?php if ($totalRows_customer_addresses > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><strong>Network</strong></td>
          <td><strong>Address</strong></td>
          <td><strong>Description</strong></td>
        </tr>
        <?php $count = 0;
		do { 
			
			if ($row_customer_addresses['v6mask'] == "") {
				$net = find_net(long2ip($row_customer_addresses['network']),long2ip($row_customer_addresses['maskLong']));
			}
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=networks&amp;container=<?php echo $row_customer_addresses['container']; ?>&amp;parent=<?php echo $row_customer_addresses['networkID']; ?>" title="View this network"><?php if ($row_customer_addresses['v6mask'] == "" ) { echo long2ip($net['network']); } else { echo Net_IPv6::compress(long2ipv6($row_customer_addresses['network'])); } ?><?php if ($row_customer_addresses['v6mask'] == "") { echo get_slash(long2ip($row_customer_addresses['maskLong'])); } else { echo "/".$row_customer_addresses['v6mask']; } ?></a></td>
          <td><a href="?browse=networks&amp;container=<?php echo $row_customer_addresses['container']; ?>&amp;parent=<?php echo $row_customer_addresses['networkID']; ?>#<?php echo $row_customer_addresses['addressID']; ?>" title="View this address"><?php if ($row_customer_addresses['v6mask'] == "") { echo long2ip($row_customer_addresses['address']); } else { echo Net_IPv6::compress(long2ipv6($row_customer_addresses['address'])); } ?></a></td>
          <td><?php echo $row_customer_addresses['addressDesr']; ?></td>
        </tr>
        <?php } while ($row_customer_addresses = mysql_fetch_assoc($customer_addresses)); ?>
      </table>
      <?php } // Show if recordset not empty 
  	else { ?>
    <p>There is now addressing information to display for this customer.</p>
    <?php        
	}?>
    <h3 id="_customervlans">Customer VLANs</h3>
    <?php if ($totalRows_customer_vlans > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><strong>VLAN ID</strong></td>
          <td><strong>VLAN Name</strong></td>
          <td><strong>Root Device (Pool Owner)</strong></td>
          <td><strong>VLAN Pool</strong></td>
        </tr>
        <?php $count = 0;
		do { 
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><strong><?php echo $row_customer_vlans['number']; ?></strong></td>
          <td><?php echo $row_customer_vlans['name']; ?></td>
          <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_customer_vlans['devicegroup']; ?>&device=<?php echo $row_customer_vlans['deviceID']; ?>" title="Browse this device"><?php echo $row_customer_vlans['devicename']; ?></a></td>
          <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_customer_vlans['devicegroup']; ?>&device=<?php echo $row_customer_vlans['deviceID']; ?>&vlanpool=<?php echo $row_customer_vlans['vlanpoolID']; ?>" title="View VLAN pool"><?php echo $row_customer_vlans['vlanpoolname']; ?></a></td>
        </tr>
        <?php } while ($row_customer_vlans = mysql_fetch_assoc($customer_vlans)); ?>
      </table>
      <?php } // Show if recordset not empty 
  	else { ?>
    <p>There are no VLANs for this customer.</p>
    <?php 
	} ?>
    <h3 id="_customerports">Customer Port Usage</h3>
    <?php if ($totalRows_customer_ports > 0 || $totalRows_customer_port_subints > 0) { ?>
    <table width="100%" border="0">
      <tr>
        <td><strong>Rack/Module/Slot/Port<br />
          .Subinterface </strong></td>
        <td><strong>Device</strong></td>
        <td><strong>Port Type</strong></td>
        <td><strong>Usage</strong></td>
        <td><strong>Primary IP Address</strong></td>
        <td><strong>Comments</strong></td>
        <td><strong>VPN</strong></td>
      </tr>
    <?php if ($totalRows_customer_ports > 0) { // Show if recordset not empty ?>
        <?php $count = 0;
		do { 
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><strong><?php if (!(isset($row_customer_ports['rack'])) && !(isset($row_customer_ports['module'])) && !(isset($row_customer_ports['slot']))) { echo "Virtual"; } else { if (isset($row_customer_ports['rack'])) { echo $row_customer_ports['rack']."/"; } if (isset($row_customer_ports['module'])) { echo $row_customer_ports['module'].'/'; } if (isset($row_customer_ports['slot'])) { echo $row_customer_ports['slot']; } } ?>/<?php echo $row_customer_ports['port']; ?></strong> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_customer_ports['devicegroup']; ?>&device=<?php echo $row_customer_ports['deviceID']; ?>&port=<?php echo $row_customer_ports['id']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a></td>
          <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_customer_ports['devicegroup']; ?>&device=<?php echo $row_customer_ports['deviceID']; ?>" title="Browse this device"><?php echo $row_customer_ports['devicename']; ?></a></td>
          <td><?php echo $row_customer_ports['cardtypename']; ?></td>
          <td><?php echo $row_customer_ports['usage']; ?></td>
          <td><?php if ($row_customer_ports['router'] != "") { ?>
            <a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;parent=<?php echo $row_customer_ports['addressnetwork']; ?>#<?php echo $row_customer_ports['addressID']; ?>" title="View addressing"><?php if ($row_customer_ports['v6mask'] == "") { echo long2ip($row_customer_ports['address']); } else { echo Net_IPv6::compress(long2ipv6($row_customer_ports['address'])); } ?></a>
            <?php } ?></td>
          <td><?php echo $row_customer_ports['comments']; ?></td>
          <td><?php if ($row_customer_ports['vpnID'] != "") { ?><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_customer_ports['providerID']; ?>&vpn=<?php echo $row_customer_ports['vpnID']; ?>" title="Browse this VPN"><?php echo $row_customer_ports['vpnname']; ?></a><?php } ?></td>
        </tr>
        <?php } while ($row_customer_ports = mysql_fetch_assoc($customer_ports)); ?>
      <?php } // If recordset is not empty ?>
      <?php if ($totalRows_customer_port_subints > 0) { // Show if recordset not empty ?>
        <?php $count = 0;
		do { 
					
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><strong><?php if (!(isset($row_customer_port_subints['rack'])) && !(isset($row_customer_port_subints['module'])) && !(isset($row_customer_port_subints['slot']))) { echo "Virtual"; } else { if (isset($row_customer_port_subints['rack'])) { echo $row_customer_port_subints['rack']."/"; } if (isset($row_customer_port_subints['module'])) { echo $row_customer_port_subints['module'].'/'; } if (isset($row_customer_port_subints['slot'])) { echo $row_customer_port_subints['slot']; } } ?>/<?php echo $row_customer_port_subints['port']; ?>.<font color="#FF0000"><?php echo $row_customer_port_subints['subint']; ?></font></strong> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_customer_port_subints['devicegroup']; ?>&device=<?php echo $row_customer_port_subints['deviceID']; ?>&port=<?php echo $row_customer_port_subints['portid']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a></td>
          <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_customer_port_subints['devicegroupID']; ?>&device=<?php echo $row_customer_port_subints['deviceID']; ?>" title="Browse this device"><?php echo $row_customer_port_subints['devicename']; ?></a></td>
          <td><?php echo $row_customer_port_subints['cardtypename']; ?></td>
          <td><?php echo $row_customer_port_subints['usage']; ?></td>
          <td><?php if ($row_customer_port_subints['router'] != "") { ?>
            <a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;parent=<?php echo $row_customer_port_subints['addressnetwork']; ?>#<?php echo $row_customer_port_subints['addressID']; ?>" title="View addressing"><?php if ($row_customer_port_subints['v6mask'] == "") { echo long2ip($row_customer_port_subints['address']); } else { echo Net_IPv6::compress(long2ipv6($row_customer_port_subints['address'])); } ?></a>
            <?php } ?></td>
          <td><?php echo $row_customer_port_subints['comments']; ?></td>
          <td><?php if ($row_customer_port_subints['vpnID'] != "") { ?><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_customer_port_subints['providerID']; ?>&vpn=<?php echo $row_customer_port_subints['vpnID']; ?>" title="Browse this VPN"><?php echo $row_customer_port_subints['vpnname']; ?></a><?php } ?></td>
        </tr>
        <?php } while ($row_customer_port_subints = mysql_fetch_assoc($customer_port_subints)); ?>
        <?php } // Show if recordset not empty ?>
    </table>
    <?php }
		else { ?>
    <p>There are no ports allocated to this customer.</p>
    <?php } ?>
        
    <h3 id="_customervpns">Customer VPN Membership</h3>
    
    <?php if ($totalRows_customer_vpns > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0">
    <tr>
      <td><strong>VPN Provider</strong></td>
      <td><strong>VPN Type</strong></td>
      <td><strong>VPN Name</strong></td>
      <td><strong>Description</strong></td>
    </tr>
    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><?php echo $row_customer_vpns['asnumber']; ?> <?php echo $row_customer_vpns['providername']; ?></td>
      <td><?php echo getVPNLayer($row_customer_vpns['layer']); ?></td>
      <td><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_customer_vpns['providerid']; ?>&vpn=<?php echo $row_customer_vpns['id']; ?>" title="Browse this VPN"><?php echo $row_customer_vpns['name']; ?></a></td>
      <td><?php echo $row_customer_vpns['descr']; ?></td>
    </tr>
    <?php } while ($row_customer_vpns = mysql_fetch_assoc($customer_vpns)); ?>
  </table>
  <?php } // Show if recordset not empty 
  		else { ?>
        
        	<p>This customer is not a member of any VPN.</p>
        
  <?php } ?>    
        
        <h3 id="_customerasses">Customer Autonomous Systems</h3>
        
        <?php if ($totalRows_customer_asses > 0) { ?>
        
        	<table width="100%" border="0">
                <tr>
                  <td><strong>AS Number</strong></td>
                  <td><strong>Name</strong></td>
                  <td><strong>Description</strong></td>
                  <td><strong>Password</strong></td>
                </tr>
        <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    			<tr bgcolor="<?php echo $bgcolour; ?>">
                	<td><?php echo $row_customer_asses['number']; ?></td>
                    <td><?php echo $row_customer_asses['name']; ?></td>
                    <td><?php echo $row_customer_asses['descr']; ?></td>
                    <td><?php echo $row_customer_asses['passwd']; ?></td>
                </tr>
            <?php } while ($row_customer_asses = mysql_fetch_assoc($customer_asses)); ?>
            
            </table>
        
        <?php }
		else { ?>
        
        	<p>There are no Autonomous Systems to display for this customer.</p>
        
        <?php } ?>
            <?php
		}
		else { ?>
	
		<?php if (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) { ?>
        <p><a href="#" onClick="document.getElementById('action').value = 'add_customer'; document.getElementById('frm_customer_action').submit(); return false;">Add a customer</a></p>
        <?php } ?>
	
	<?php }
   
	}
	
	if ($_POST['recover_link'] != "") { ?>
		
		<h4>Link Recovered</h4>
        
        <p>The link was recovered from the database.</p>
        
        
        <?php
				mysql_select_db($database_subman, $subman);
				$query_node_a = "SELECT * FROM portsdevices WHERE portsdevices.id = '".$recovery_node_a."'";
				$node_a = mysql_query($query_node_a, $subman) or die(mysql_error());
				$row_node_a = mysql_fetch_assoc($node_a);
				$totalRows_node_a = mysql_num_rows($node_a);
				
				mysql_select_db($database_subman, $subman);
				$query_node_b = "SELECT * FROM portsdevices WHERE portsdevices.id = '".$recovery_node_b."'";
				$node_b = mysql_query($query_node_b, $subman) or die(mysql_error());
				$row_node_b = mysql_fetch_assoc($node_b);
				$totalRows_node_b = mysql_num_rows($node_b);
				
				mysql_select_db($database_subman, $subman);
				$query_card_node_a = "SELECT cards.*, cardtypes.name as cardtypename, cardtypes.config FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = '".$recovery_card_node_a."'";
				$card_node_a = mysql_query($query_card_node_a, $subman) or die(mysql_error());
				$row_card_node_a = mysql_fetch_assoc($card_node_a);
				$totalRows_card_node_a = mysql_num_rows($card_node_a);
				
				mysql_select_db($database_subman, $subman);
				$query_card_node_b = "SELECT cards.*, cardtypes.name as cardtypename, cardtypes.config FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = '".$recovery_card_node_b."'";
				$card_node_b = mysql_query($query_card_node_b, $subman) or die(mysql_error());
				$row_card_node_b = mysql_fetch_assoc($card_node_b);
				$totalRows_card_node_b = mysql_num_rows($card_node_b);
				
            	?>
                
                                <?php
					mysql_select_db($database_subman, $subman);
					$query_recovery_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$recovery_template."'";
					$recovery_template = mysql_query($query_recovery_template, $subman) or die(mysql_error());
					$row_recovery_template = mysql_fetch_assoc($recovery_template);
					$totalRows_recovery_template = mysql_num_rows($recovery_template);
					
					mysql_select_db($database_subman, $subman);
					$query_provide_scripts = "SELECT * FROM scripts WHERE scripts.servicetemplate = '".$row_recovery_template['id']."' AND scripts.scriptrole = 'servicerecover'";
					$provide_scripts = mysql_query($query_provide_scripts, $subman) or die(mysql_error());
					$row_provide_scripts = mysql_fetch_assoc($provide_scripts);
					$totalRows_provide_scripts = mysql_num_rows($provide_scripts);
						
					if ($row_recovery_template['recover_a'] != "") {
						
						if ($row_card_node_a['slot'] == "" && $row_card_node_a['rack'] == "" && $row_card_node_a['module'] == "") { 
						 	$row_card_node_a['slot'] =  "Virtual";
						}
						if ($row_card_node_a['rack'] != "" && $row_card_node_a['module'] != "") {
							$ifnumber = $row_card_node_a['rack']."/".$row_card_node_a['module']."/".$row_card_node_a['slot']."/".$row_port_node_a['port'];
						}
						elseif ($row_card_node_a['module'] != "") {
							$ifnumber = $row_card_node_a['module']."/".$row_card_node_a['slot']."/".$row_port_node_a['port'];
						}
						elseif ($row_card_node_a['slot'] != "Virtual") {
							$ifnumber = $row_card_node_a['slot']."/".$row_port_node_a['port'];
						}
						else {
							$ifnumber = $row_port_node_a['port'];
						}
						
						$parentifnumber = $ifnumber;
						
						if ($recovery_subint_node_a != "") {
							$ifnumber .= ".".$recovery_subint_node_a;
						}
						
						if ($row_card_node_b['slot'] == "" && $row_card_node_b['rack'] == "" && $row_card_node_b['module'] == "") { 
						 	$row_card_node_b['slot'] =  "Virtual";
						}
						if ($row_card_node_b['rack'] != "" && $row_card_node_b['module'] != "") {
							$ifnumberb = $row_card_node_b['rack']."/".$row_card_node_b['module']."/".$row_card_node_b['slot']."/".$row_port_node_b['port'];
						}
						elseif ($row_card_node_b['module'] != "") {
							$ifnumberb = $row_card_node_b['module']."/".$row_card_node_b['slot']."/".$row_port_node_b['port'];
						}
						elseif ($row_card_node_b['slot'] != "Virtual") {
							$ifnumberb = $row_card_node_b['slot']."/".$row_port_node_b['port'];
						}
						else {
							$ifnumberb = $row_port_node_b['port'];
						}
						
						$parentifnumberb = $ifnumberb;
						
						if ($recovery_subint_node_b != "") {
							$ifnumberb .= ".".$recovery_subint_node_b;
						}
						
						if ($recovery_v6_mask != "") {
							$ipversioncisco = 'ipv6';
							$ipversion = 'IPv6';
						}
						else {
							$ipversioncisco = 'ip';
							$ipversion = 'IPv4';
						}
						
						$config = str_replace("%customername%",$recovery_customer,$row_recovery_template['recover_a']);
						$config = str_replace("%nodea%",$row_node_a['name'],$config);
						$config = str_replace("%nodeb%",$row_node_b['name'],$config);
						$config = str_replace("%customername_trimmed%",str_replace(" ","",$recovery_customer),$config);
						$config = str_replace("%iftype%",$row_card_node_a['config'],$config);
						$config = str_replace("%iftype_a%",$row_card_node_a['config'],$config);
						$config = str_replace("%iftype_b%",$row_card_node_b['config'],$config);
						$config = str_replace("%ifnumber%",$ifnumber,$config);
						$config = str_replace("%ifnumber_a%",$ifnumber,$config);
						$config = str_replace("%ifnumber_b%",$ifnumberb,$config);
						$config = str_replace("%ifnumber_nosubint%",$parentifnumber,$config);
						$config = str_replace("%ifnumber_a_nosubint%",$parentifnumber,$config);
						$config = str_replace("%ifnumber_b_nosubint%",$parentifnumberb,$config);
						$config = str_replace("%circuitnumber%",$recovery_cct,$config);
						$config = str_replace("%circuitnumber_trimmed%",str_replace(" ","",$recovery_cct),$config);
						$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
						$config = str_replace("%ipversion%",$ipversion,$config);
						if ($ipversion == "IPv6") {
							$config = str_replace("%ipaddress%",Net_IPv6::compress(long2ipv6($recovery_address_node_a)),$config);
							$config = str_replace("%ipaddress_a%",Net_IPv6::compress(long2ipv6($recovery_address_node_a)),$config);
							$config = str_replace("%ipaddress_b%",Net_IPv6::compress(long2ipv6($recovery_address_node_b)),$config);
							$config = str_replace("%ipaddress_opposite%",Net_IPv6::compress(long2ipv6($recovery_address_node_b)),$config);
							$config = str_replace("%addressmask_cisco%",Net_IPv6::compress(long2ipv6($recovery_address_node_a))."/".$recovery_v6_mask,$config);
							$config = str_replace("%secondary_cisco%","",$config);
							$config = str_replace("%netmask%","/".$recovery_v6_mask,$config);
							$config = str_replace("%netmask_slash%","/".$recovery_v6_mask,$config);
						}
						else {
							$config = str_replace("%ipaddress%",long2ip($recovery_address_node_a),$config);
							$config = str_replace("%ipaddress_a%",long2ip($recovery_address_node_a),$config);
							$config = str_replace("%ipaddress_b%",long2ip($recovery_address_node_b),$config);
							$config = str_replace("%ipaddress_opposite%",long2ip($recovery_address_node_b),$config);
							$config = str_replace("%addressmask_cisco%",long2ip($recovery_address_node_a)." ".$recovery_mask,$config);
							$config = str_replace("%secondary_cisco%","secondary",$config);
							$config = str_replace("%netmask%",$recovery_mask,$config);
							$config = str_replace("%netmask_slash%",get_slash($recovery_mask),$config);
						}
						$config = str_replace("%vlan%",$recovery_vlan_node_a,$config);
						$config = str_replace("%vpn%",$recovery_vpn,$config);
						$config = str_replace("%vrf%",$recovery_vrf,$config);
						$config = str_replace("%xconnect%",$recovery_xconnect,$config);
						$config = str_replace("%loopback%",$recovery_loopback_node_a,$config);
						$config = str_replace("%loopback_a%",$recovery_loopback_node_a,$config);
						$config = str_replace("%loopback_b%",$recovery_loopback_node_b,$config);
						$config = str_replace("%loopback_opposite%",$recovery_loopback_node_b,$config);
						$config = str_replace("%first_timeslot%",$recovery_first_timeslot_node_a,$config);
						$config = str_replace("%last_timeslot%",$recovery_last_timeslot_node_a,$config);
						
						mysql_select_db($database_subman, $subman);
						$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
						$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
						$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
						$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
				
						do {
						
							$config = str_replace("%".$row_arbitraryfields['id']."%",$_SESSION['arbitrary'.$row_arbitraryfields['id']],$config);
							
						} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
						
						echo "<h5>Device Recovery Configuration Template - ".$row_node_a['name']."</h5>";
						echo "<p><textarea cols=\"100\" rows=\"10\" class=\"input_standard\">".$config."</textarea></p>";
						
						if ($totalRows_node_b > 0) {
							
							if ($row_recovery_template['recover_b'] != "") {
								
								$config = str_replace("%customername%",$recovery_customer,$row_recovery_template['recover_b']);
								$config = str_replace("%nodea%",$row_node_a['name'],$config);
								$config = str_replace("%nodeb%",$row_node_b['name'],$config);
								$config = str_replace("%customername_trimmed%",str_replace(" ","",$recovery_customer),$config);
								$config = str_replace("%iftype%",$row_card_node_b['config'],$config);
								$config = str_replace("%iftype_a%",$row_card_node_a['config'],$config);
								$config = str_replace("%iftype_b%",$row_card_node_b['config'],$config);
								$config = str_replace("%ifnumber%",$ifnumberb,$config);
								$config = str_replace("%ifnumber_a%",$ifnumber,$config);
								$config = str_replace("%ifnumber_b%",$ifnumberb,$config);
								$config = str_replace("%ifnumber_nosubint%",$parentifnumberb,$config);
								$config = str_replace("%ifnumber_a_nosubint%",$parentifnumber,$config);
								$config = str_replace("%ifnumber_b_nosubint%",$parentifnumberb,$config);
								$config = str_replace("%circuitnumber%",$recovery_cct,$config);
								$config = str_replace("%circuitnumber_trimmed%",str_replace(" ","",$recovery_cct),$config);
								$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
								$config = str_replace("%ipversion%",$ipversion,$config);
								if ($ipversion == "IPv6") {
									$config = str_replace("%ipaddress%",Net_IPv6::compress(long2ipv6($recovery_address_node_b)),$config);
									$config = str_replace("%ipaddress_a%",Net_IPv6::compress(long2ipv6($recovery_address_node_a)),$config);
									$config = str_replace("%ipaddress_b%",Net_IPv6::compress(long2ipv6($recovery_address_node_b)),$config);
									$config = str_replace("%ipaddress_opposite%",Net_IPv6::compress(long2ipv6($recovery_address_node_a)),$config);
									$config = str_replace("%addressmask_cisco%",Net_IPv6::compress(long2ipv6($recovery_address_node_b))."/".$recovery_v6_mask,$config);
									$config = str_replace("%secondary_cisco%","",$config);
									$config = str_replace("%netmask%","/".$recovery_v6_mask,$config);
									$config = str_replace("%netmask_slash%","/".$recovery_v6_mask,$config);
								}
								else {
									$config = str_replace("%ipaddress%",long2ip($recovery_address_node_b),$config);
									$config = str_replace("%ipaddress_a%",long2ip($recovery_address_node_a),$config);
									$config = str_replace("%ipaddress_b%",long2ip($recovery_address_node_b),$config);
									$config = str_replace("%ipaddress_opposite%",long2ip($recovery_address_node_a),$config);
									$config = str_replace("%addressmask_cisco%",long2ip($recovery_address_node_b)." ".$recovery_mask,$config);
									$config = str_replace("%secondary_cisco%","secondary",$config);
									$config = str_replace("%netmask%",$recovery_mask,$config);
									$config = str_replace("%netmask_slash%",get_slash($recovery_mask),$config);
								}
								$config = str_replace("%vlan%",$recovery_vlan_node_a,$config);
								$config = str_replace("%vpn%",$recovery_vpn,$config);
								$config = str_replace("%vrf%",$recovery_vrf,$config);
								$config = str_replace("%xconnect%",$recovery_xconnect,$config);
								$config = str_replace("%loopback%",$recovery_loopback_node_b,$config);
								$config = str_replace("%loopback_a%",$recovery_loopback_node_a,$config);
								$config = str_replace("%loopback_b%",$recovery_loopback_node_b,$config);
								$config = str_replace("%loopback_opposite%",$recovery_loopback_node_a,$config);
								$config = str_replace("%first_timeslot%",$recovery_first_timeslot_node_b,$config);
								$config = str_replace("%last_timeslot%",$recovery_last_timeslot_node_b,$config);
								
								mysql_select_db($database_subman, $subman);
								$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
								$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
								$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
								$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
				
								do {
						
									$config = str_replace("%".$row_arbitraryfields['id']."%",$_SESSION['arbitrary'.$row_arbitraryfields['id']],$config);
							
								} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
						
								echo "<h5>Device Recovery Configuration Template - ".$row_node_b['name']."</h5>";
								echo "<p><textarea cols=\"100\" rows=\"10\" class=\"input_standard\">".$config."</textarea></p>";
							}
							
						}
						
					}
					
					if ($totalRows_provide_scripts > 0) {
							
						?>
                        <?php do {
							
							mysql_select_db($database_subman, $subman);
							$query_variables = "SELECT * FROM scriptvariables WHERE scriptvariables.script = '".$row_provide_scripts['id']."' ORDER BY id";
							$variables = mysql_query($query_variables, $subman) or die(mysql_error());
							$row_variables = mysql_fetch_assoc($variables);
							$totalRows_variables = mysql_num_rows($variables);
							
							if ($recovery_v6_mask != "") {
								$ipversioncisco = 'ipv6';
								$ipversion = 'IPv6';
							}
							else {
								$ipversioncisco = 'ip';
								$ipversion = 'IPv4';
							}
							?>
                        <?php if ($row_provide_scripts['scripttype'] == 'HTTP') { ?>
                        <?php $script = $row_provide_scripts['location']."?"; ?>
						<?php do { 
								$config = str_replace("%customername%",$recovery_customer,$row_variables['value']);
						$config = str_replace("%nodea%",$row_node_a['name'],$config);
						$config = str_replace("%nodeb%",$row_node_b['name'],$config);
						$config = str_replace("%customername_trimmed%",str_replace(" ","",$recovery_customer),$config);
						$config = str_replace("%iftype%",$row_card_node_a['config'],$config);
						$config = str_replace("%iftype_a%",$row_card_node_a['config'],$config);
						$config = str_replace("%iftype_b%",$row_card_node_b['config'],$config);
						$config = str_replace("%ifnumber%",$ifnumber,$config);
						$config = str_replace("%ifnumber_a%",$ifnumber,$config);
						$config = str_replace("%ifnumber_b%",$ifnumberb,$config);
						$config = str_replace("%ifnumber_nosubint%",$parentifnumber,$config);
						$config = str_replace("%ifnumber_a_nosubint%",$parentifnumber,$config);
						$config = str_replace("%ifnumber_b_nosubint%",$parentifnumberb,$config);
						$config = str_replace("%circuitnumber%",$recovery_cct,$config);
						$config = str_replace("%circuitnumber_trimmed%",str_replace(" ","",$recovery_cct),$config);
						$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
						$config = str_replace("%ipversion%",$ipversion,$config);
						if ($ipversion == "IPv6") {
							$config = str_replace("%ipaddress%",Net_IPv6::compress(long2ipv6($recovery_address_node_a)),$config);
							$config = str_replace("%ipaddress_a%",Net_IPv6::compress(long2ipv6($recovery_address_node_a)),$config);
							$config = str_replace("%ipaddress_b%",Net_IPv6::compress(long2ipv6($recovery_address_node_b)),$config);
							$config = str_replace("%ipaddress_opposite%",Net_IPv6::compress(long2ipv6($recovery_address_node_b)),$config);
							$config = str_replace("%addressmask_cisco%",Net_IPv6::compress(long2ipv6($recovery_address_node_a))."/".$recovery_v6_mask,$config);
							$config = str_replace("%secondary_cisco%","",$config);
							$config = str_replace("%netmask%","/".$recovery_v6_mask,$config);
							$config = str_replace("%netmask_slash%","/".$recovery_v6_mask,$config);
						}
						else {
							$config = str_replace("%ipaddress%",long2ip($recovery_address_node_a),$config);
							$config = str_replace("%ipaddress_a%",long2ip($recovery_address_node_a),$config);
							$config = str_replace("%ipaddress_b%",long2ip($recovery_address_node_b),$config);
							$config = str_replace("%ipaddress_opposite%",long2ip($recovery_address_node_b),$config);
							$config = str_replace("%addressmask_cisco%",long2ip($recovery_address_node_a)." ".$recovery_mask,$config);
							$config = str_replace("%secondary_cisco%","secondary",$config);
							$config = str_replace("%netmask%",$recovery_mask,$config);
							$config = str_replace("%netmask_slash%",get_slash($recovery_mask),$config);
						}
						$config = str_replace("%vlan%",$recovery_vlan_node_a,$config);
						$config = str_replace("%vpn%",$recovery_vpn,$config);
						$config = str_replace("%vrf%",$recovery_vrf,$config);
						$config = str_replace("%xconnect%",$recovery_xconnect,$config);
						$config = str_replace("%loopback%",$recovery_loopback_node_a,$config);
						$config = str_replace("%loopback_a%",$recovery_loopback_node_a,$config);
						$config = str_replace("%loopback_b%",$recovery_loopback_node_b,$config);
						$config = str_replace("%loopback_opposite%",$recovery_loopback_node_b,$config);
						$config = str_replace("%first_timeslot%",$recovery_first_timeslot_node_a,$config);
						$config = str_replace("%last_timeslot%",$recovery_last_timeslot_node_a,$config);
						
						mysql_select_db($database_subman, $subman);
						$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
						$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
						$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
						$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
				
						do {
						
							$config = str_replace("%".$row_arbitraryfields['id']."%",$_SESSION['arbitrary'.$row_arbitraryfields['id']],$config);
							
						} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
						
						?>
                            <?php } while ($row_variables = mysql_fetch_assoc($variables)); ?>
                            
                            <br />
                            <?php if ($row_provide_scripts['autorun'] != 1) { ?>
                            <input type="button" name="launch_script" id="launch_script" class="input_red" title="Execute Script <?php echo $script; ?>" value="<?php echo $row_provide_scripts['description']; ?>" onClick="document.getElementById('script_terminal').src = '<?php echo $script; ?>'; document.getElementById('launch_script').disabled = 'disabled'; document.getElementById('script_prompt').style.display = 'block';" /><span id="script_prompt" style="display:none">Executing script, please wait for output below...</span><br />
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto"></iframe><br />
                            <?php } else { ?>
                            Executing script, please wait for output below...<br />
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto" src="<?php echo $script; ?>"></iframe><br />
                            <?php } ?>
                            
                        <?php } elseif ($row_provide_scripts['scripttype'] == 'CLI') { ?>
                        
                        <?php $script = $row_provide_scripts['location']." "; ?>
						<?php do { 
								$config = str_replace("%customername%",$recovery_customer,$row_variables['value']);
								$config = str_replace("%nodea%",$row_node_a['name'],$config);
								$config = str_replace("%nodeb%",$row_node_b['name'],$config);
								$config = str_replace("%customername_trimmed%",str_replace(" ","",$recovery_customer),$config);
								$config = str_replace("%iftype%",$row_card_node_a['config'],$config);
								$config = str_replace("%iftype_a%",$row_card_node_a['config'],$config);
								$config = str_replace("%iftype_b%",$row_card_node_b['config'],$config);
								$config = str_replace("%ifnumber%",$ifnumber,$config);
								$config = str_replace("%ifnumber_a%",$ifnumber,$config);
								$config = str_replace("%ifnumber_b%",$ifnumberb,$config);
								$config = str_replace("%ifnumber_nosubint%",$parentifnumber,$config);
								$config = str_replace("%ifnumber_a_nosubint%",$parentifnumber,$config);
								$config = str_replace("%ifnumber_b_nosubint%",$parentifnumberb,$config);
								$config = str_replace("%circuitnumber%",$recovery_cct,$config);
								$config = str_replace("%circuitnumber_trimmed%",str_replace(" ","",$recovery_cct),$config);
								$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
								$config = str_replace("%ipversion%",$ipversion,$config);
								if ($ipversion == "IPv6") {
									$config = str_replace("%ipaddress%",Net_IPv6::compress(long2ipv6($recovery_address_node_a)),$config);
									$config = str_replace("%ipaddress_a%",Net_IPv6::compress(long2ipv6($recovery_address_node_a)),$config);
									$config = str_replace("%ipaddress_b%",Net_IPv6::compress(long2ipv6($recovery_address_node_b)),$config);
									$config = str_replace("%ipaddress_opposite%",Net_IPv6::compress(long2ipv6($recovery_address_node_b)),$config);
									$config = str_replace("%addressmask_cisco%",Net_IPv6::compress(long2ipv6($recovery_address_node_a))."/".$recovery_v6_mask,$config);
									$config = str_replace("%secondary_cisco%","",$config);
									$config = str_replace("%netmask%","/".$recovery_v6_mask,$config);
									$config = str_replace("%netmask_slash%","/".$recovery_v6_mask,$config);
								}
								else {
									$config = str_replace("%ipaddress%",long2ip($recovery_address_node_a),$config);
									$config = str_replace("%ipaddress_a%",long2ip($recovery_address_node_a),$config);
									$config = str_replace("%ipaddress_b%",long2ip($recovery_address_node_b),$config);
									$config = str_replace("%ipaddress_opposite%",long2ip($recovery_address_node_b),$config);
									$config = str_replace("%addressmask_cisco%",long2ip($recovery_address_node_a)." ".$recovery_mask,$config);
									$config = str_replace("%secondary_cisco%","secondary",$config);
									$config = str_replace("%netmask%",$recovery_mask,$config);
									$config = str_replace("%netmask_slash%",get_slash($recovery_mask),$config);
								}
								$config = str_replace("%vlan%",$recovery_vlan_node_a,$config);
								$config = str_replace("%vpn%",$recovery_vpn,$config);
								$config = str_replace("%vrf%",$recovery_vrf,$config);
								$config = str_replace("%xconnect%",$recovery_xconnect,$config);
								$config = str_replace("%loopback%",$recovery_loopback_node_b,$config);
								$config = str_replace("%loopback_a%",$recovery_loopback_node_a,$config);
								$config = str_replace("%loopback_b%",$recovery_loopback_node_b,$config);
								$config = str_replace("%loopback_opposite%",$recovery_loopback_node_a,$config);
								$config = str_replace("%first_timeslot%",$recovery_first_timeslot_node_b,$config);
								$config = str_replace("%last_timeslot%",$recovery_last_timeslot_node_b,$config);
								
								mysql_select_db($database_subman, $subman);
								$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
								$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
								$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
								$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
				
								do {
						
									$config = str_replace("%".$row_arbitraryfields['id']."%",$_SESSION['arbitrary'.$row_arbitraryfields['id']],$config);
							
								} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
						
								?><?php $script .= $config." ";?>
                            <?php } while ($row_variables = mysql_fetch_assoc($variables)); ?>
                            <?php 
								$_SESSION['script'] = $script;
								?>
                            <br />
                            <?php if ($row_provide_scripts['autorun'] != 1) { ?>
                            <input type="button" name="launch_script" id="launch_script" class="input_red" title="Execute Script <?php echo $script; ?>" value="<?php echo $row_provide_scripts['description']; ?>" onClick="document.getElementById('script_terminal').src = 'includes/launch_cli.php'; document.getElementById('launch_script').disabled = 'disabled'; document.getElementById('script_prompt').style.display = 'block';" /><span id="script_prompt" style="display:none">Executing script, please wait for output below...</span><br />
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto"></iframe><br />
                            <?php } else { ?>
                            Executing script, please wait for output below...<br />
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto" src="includes/launch_cli.php"></iframe><br />
                            <?php } ?>
                        <?php
						
						} ?>
                        
                        <?php
						
							} while ($row_provide_scripts = mysql_fetch_assoc($provide_scripts));
						
						}
					?>
                    
                    <?php if ($recovery_linked) { 
						mysql_select_db($database_subman, $subman);
						$query_checklink = "SELECT * FROM links WHERE links.id = ".$recovery_linked."";
						$checklink = mysql_query($query_checklink, $subman) or die(mysql_error());
						$row_checklink = mysql_fetch_assoc($checklink);
						$totalRows_checklink = mysql_num_rows($checklink);
						
							if ($totalRows_checklink > 0) {
												
										?>
								<p class="text_red"><strong>This template requires that another link be recovered.  Please click the button below to recover the associated.</strong></p>
								<form action="" method="post" target="_self" id="frm_recover_link" name="frm_recover_link">
											<input type="hidden" name="action" value="recover_link" />
											<input type="hidden" value="<?php echo $row_checklink['id']; ?>" name="link" />
											<input type="submit" value="Recover Link" class="input_standard" />
	</form>
                <?php } 
					} elseif ($recovery_linkedfrom) { 
						mysql_select_db($database_subman, $subman);
						$query_checklink = "SELECT * FROM links WHERE links.id = ".$recovery_linkedfrom."";
						$checklink = mysql_query($query_checklink, $subman) or die(mysql_error());
						$row_checklink = mysql_fetch_assoc($checklink);
						$totalRows_checklink = mysql_num_rows($checklink);
						
							if ($totalRows_checklink > 0) {
												
										?>
								<p class="text_red"><strong>This template requires that another link be recovered.  Please click the button below to recover the associated.</strong></p>
								<form action="" method="post" target="_self" id="frm_recover_link" name="frm_recover_link">
											<input type="hidden" name="action" value="recover_link" />
											<input type="hidden" value="<?php echo $row_checklink['id']; ?>" name="link" />
											<input type="submit" value="Recover Link" class="input_standard" />
	</form>
                <?php } 
					} else { ?>
                	<p><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_node_a['devicegroup']; ?>" title="Browse device">Click here</a> to go to the devices page.</p>
                <?php } ?>
                    
	<?php	
	}
	
	elseif ($_GET['browse'] == "devices") { 
		
		if ($_POST['action'] == "add_variable") {
			
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_template = "SELECT * FROM servicetemplate WHERE container = ".$_GET['container']." AND id = ".$_GET['template']." ORDER BY servicetemplate.name";
			$template = mysql_query($query_template, $subman) or die(mysql_error());
			$row_template = mysql_fetch_assoc($template);
			$totalRows_template = mysql_num_rows($template);
			
			mysql_select_db($database_subman, $subman);
			$query_script = "SELECT * FROM scripts WHERE scripts.id = ".$_GET['script']."";
			$script = mysql_query($query_script, $subman) or die(mysql_error());
			$row_script = mysql_fetch_assoc($script);
			$totalRows_script = mysql_num_rows($script);
			
		?>
        
        Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service" title="Browse service templates">Service Templates</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service&amp;template=<?php echo $_GET['template']; ?>" title="View service template"><?php echo $row_template['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service&amp;template=<?php echo $_GET['template']; ?>&amp;script=<?php echo $row_script['id']; ?>" title="View script"><?php echo $row_script['description']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>Add a variable</strong><br /><br />
        
        <form action="" method="post" target="_self" name="frm_add_variable" onSubmit="MM_validateForm('variablename','','R','value','','R');return document.MM_returnValue">
            <table>
			<tr valign="baseline">
			  <td align="right" valign="top">Variable Name/Switch:</td>
			  <td><input name="variablename" type="text" class="input_standard" id="variablename" value="" size="32" maxlength="255" /></td>
			</tr>
            <tr valign="baseline">
			  <td align="right" valign="top">Value:</td>
			  <td><input name="value" type="text" class="input_standard" id="value" value="" size="32" maxlength="255" />
		      <br /><span class="text_red">For CLI scripts, variables will be passed in the format '&lt;script name&gt; &lt;value&gt;...'.  For HTTP, variables and values are passed in the query string.</span></td>
			</tr>
			<tr valign="baseline">
			  <td align="right">&nbsp;</td>
			  <td><input type="submit" class="input_standard" value="Add variable" /></td>
			</tr>
		  </table>
					<input type="hidden" name="MM_insert" value="frm_add_variable" />
                    <input type="hidden" name="template" value="<?php echo $_GET['template']; ?>" />
                    <input type="hidden" name="script" value="<?php echo $_GET['script']; ?>" />
					<input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
    </form>
            
            <h5>Value Keywords</h5>
                <table width="50%" border="0">
                	<tr>
                    	<td>%nodea%</td>
                        <td>Node A</td>
                    </tr>
                    <tr>
                    	<td>%nodeb%</td>
                        <td>Node B</td>
                    </tr>
                    <tr>
                    	<td>%iftype%</td>
                        <td>Interface type (e.g. GigabitEthernet)</td>
                    </tr>
                    <tr>
                    	<td>%iftype_a%</td>
                        <td>Node A's interface type (e.g. GigabitEthernet)</td>
                    </tr>
                    <tr>
                    	<td>%iftype_b%</td>
                        <td>Node B's interface type (e.g. GigabitEthernet)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber%</td>
                        <td>Interface number (e.g. 1/1.1)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber_nosubint%</td>
                        <td>Interface number without sub-interface number or channel group(e.g. 1/1)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber_a%</td>
                        <td>Node A's interface number (e.g. 1/1.1)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber_a_nosubint%</td>
                        <td>Interface number without sub-interface number or channel group(e.g. 1/1)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber_b%</td>
                        <td>Node B's interface number (e.g. 1/1.1)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber_b_nosubint%</td>
                        <td>Interface number without sub-interface number or channel group(e.g. 1/1)</td>
                    </tr>
                    <tr>
                    	<td>%customername%</td>
                        <td>Customer name</td>
                    </tr>
                    <tr>
                    	<td>%customername_trimmed%</td>
                        <td>Customer name with whitespace removed</td>
                    </tr>
                    <tr>
                    	<td>%circuitnumber%</td>
                        <td>Circuit number</td>
                    </tr>
                    <tr>
                    	<td>%circuitnumber_trimmed%</td>
                        <td>Circuit number with whitespace removed</td>
                    </tr>
                    <tr>
                    	<td>%ipversion_cisco%</td>
                        <td>Outputs 'ip' for IPv4 and 'ipv6' for IPv6</td>
                    </tr>
                    <tr>
                    	<td>%ipversion%</td>
                        <td>Outputs 'IPv4' for IPv4 and 'IPv6' for IPv6</td>
                    </tr>
                    <tr>
                    	<td>%ipaddress%</td>
                        <td>The node's interface IP address</td>
                    </tr>
                    <tr>
                    	<td>%ipaddress_a%</td>
                        <td>Node A's interface IP address</td>
                    </tr>
                    <tr>
                    	<td>%ipaddress_b%</td>
                        <td>Node B's interface IP address</td>
                    </tr>
                    <tr>
                    	<td>%ipaddress_opposite%</td>
                        <td>The other node's interface IP address</td>
                    </tr>
                    <tr>
                    	<td>%addressmask_cisco%</td>
                        <td>Outputs [address]/[mask] for IPv6 and [address] [dotted mask] for IPv4 (i.e. Cisco's interface addressing scheme)</td>
                    </tr>
                    <tr>
                    	<td>%secondary_cisco%</td>
                        <td>Outputs the keyword 'secondary' for v4 addresses and nothing for v6 addresses (used at the end of the IP address configuration statement on Cisco routers)</td>
                    </tr>
                    <tr>
                    	<td>%netmask%</td>
                        <td>The node's interface subnet mask in dotted decimal format</td>
                    </tr>
                    <tr>
                    	<td>%netmask_slash%</td>
                        <td>The node's interface subnet mask in slash format</td>
                    </tr>
                    <tr>
                    	<td>%vlan%</td>
                        <td>Port VLAN/dot1q tag</td>
                    </tr>
                    <tr>
                    	<td>%vpn%</td>
                        <td>VPN name</td>
                    </tr>
                    <tr>
                    	<td>%vrf%</td>
                        <td>VRF name</td>
                    </tr>
                    <tr>
                    	<td>%xconnect%</td>
                        <td>Pseudowire ID</td>
                    </tr>
                    <tr>
                    	<td>%loopback%</td>
                        <td>The node's router loopback</td>
                    </tr>
                    <tr>
                    	<td>%loopback_a%</td>
                        <td>Node A's router loopback</td>
                    </tr>
                    <tr>
                    	<td>%loopback_b%</td>
                        <td>Node B's router loopback</td>
                    </tr>
                    <tr>
                    	<td>%loopback_opposite%</td>
                        <td>The other node's router loopback</td>
                    </tr>
                    <tr>
                    	<td>%first_timeslot%</td>
                        <td>The first timeslot used (TDM only)</td>
                    </tr>
                    <tr>
                    	<td>%last_timeslot%</td>
                        <td>The last timeslot used (TDM only)</td>
                    </tr>
                    <tr>
                    	<td>%network%</td>
                        <td>Route prefix (routes configuration only)</td>
                    </tr>
                    <tr>
                    	<td>%nexthop%</td>
                        <td>Route prefix next hop (routes configuration only)</td>
                    </tr>
                    <tr>
                    	<td>%route_cisco%</td>
                        <td>Outputs [network]/[mask] for IPv6 prefixes and [network] [dotted mask] for IPv4 prefixes - i.e. Cisco's route prefix format (routes configuration only)</td>
                    </tr>
                    <tr>
                    	<td>%router%</td>
                        <td>The loopback address of the device that routes should be deployed on (you can set this in the template) (routes configuration only)</td>
                    </tr>
                </table>
        <?php
		}
		
		elseif ($_POST['action'] == "add_script") {
			
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_template = "SELECT * FROM servicetemplate WHERE container = ".$_GET['container']." AND id = ".$_GET['template']." ORDER BY servicetemplate.name";
			$template = mysql_query($query_template, $subman) or die(mysql_error());
			$row_template = mysql_fetch_assoc($template);
			$totalRows_template = mysql_num_rows($template);
			
			?>
            Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service" title="Browse service templates">Service Templates</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service&amp;template=<?php echo $_GET['template']; ?>" title="View service template"><?php echo $row_template['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>Add a script</strong><br /><br />
            
            <form action="" method="post" target="_self" name="frm_add_script" onSubmit="MM_validateForm('description','','R','location','','R');return document.MM_returnValue">
            <table>
			<tr valign="baseline">
			  <td align="right" valign="top">Description:</td>
			  <td><input name="description" type="text" class="input_standard" id="description" value="" size="32" maxlength="255" /></td>
			</tr>
            <tr valign="baseline">
			  <td align="right" valign="top">Path/Location:</td>
			  <td><input name="location" type="text" class="input_standard" id="location" value="" size="32" maxlength="255" />
		      <br /><span class="text_red">For CLI scripts, enter the full system path (including the executable e.g. 'perl &lt;path to script&gt;').  For HTTP, enter the URL.<br />Ensure that the web server has permission to execute the script.</span></td>
			</tr>
            <tr valign="baseline">
			  <td align="right" valign="top">Execution:</td>
			  <td><select name="scripttype" id="scripttype" class="input_standard">
              	<option value="CLI">CLI</option>
                <option value="HTTP">HTTP</option>
              </select></td>
			</tr>
            <tr valign="baseline">
			  <td align="right" valign="top">Type:</td>
			  <td><select name="scriptrole" id="scriptrole" class="input_standard">
              	<option value="servicedeploy">Service Deployment</option>
                <option value="servicerecover">Service Recovery</option>
                <option value="routesdeploy">Routes Deployment</option>
                <option value="routesrecover">Routes Recovery</option>
                <option value="secondarynetsdeploy">Secondary Networks Deployment</option>
                <option value="secondarynetsrecover">Secondary Networks Recovery</option>
              </select></td>
			</tr>
			<tr valign="baseline">
				<td align="right" valign="top">Autorun:</td>
				<td><input type="checkbox" name="autorun" value="1"></td>
			</tr>
			<tr valign="baseline">
			  <td align="right">&nbsp;</td>
			  <td><input type="submit" class="input_standard" value="Add script" /></td>
			</tr>
		  </table>
					<input type="hidden" name="MM_insert" value="frm_add_script" />
                    <input type="hidden" name="template" value="<?php echo $_GET['template']; ?>" />
					<input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
            </form>
		<?php	
		}
		
		elseif ($_POST['action'] == "add_field") {
			
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_template = "SELECT * FROM servicetemplate WHERE container = ".$_GET['container']." AND id = ".$_GET['template']." ORDER BY servicetemplate.name";
			$template = mysql_query($query_template, $subman) or die(mysql_error());
			$row_template = mysql_fetch_assoc($template);
			$totalRows_template = mysql_num_rows($template);
			
			?>
            Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service" title="Browse service templates">Service Templates</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service&amp;template=<?php echo $_GET['template']; ?>" title="View service template"><?php echo $row_template['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>Add an arbitrary field</strong><br /><br />
            
            <form action="" method="post" target="_self" name="frm_add_script" onSubmit="MM_validateForm('title','','R','value','','R');return document.MM_returnValue">
            <table>
			<tr valign="baseline">
			  <td align="right" valign="top">Title:</td>
			  <td><input name="title" type="text" class="input_standard" id="title" value="" size="32" maxlength="255" /></td>
			</tr>
            <tr valign="baseline">
			  <td align="right" valign="top">Default Value:</td>
			  <td><textarea name="value" cols="50" rows="10" class="input_standard" id="value"></textarea>
		    </tr>
            <tr valign="baseline">
			  <td align="right" valign="top">User Hint:</td>
			  <td><textarea cols="50" rows="10" id="hint" name="hint" class="input_standard"></textarea></td>
			</tr>
            <tr valign="baseline">
			  <td align="right" valign="top">Type:</td>
			  <td><select name="fieldtype" id="fieldtype" class="input_standard">
              	<option value="text">Text</option>
              	<option value="textarea">Text Area</option>
                <option value="checkbox">Checkbox (default value is ignored)</option>
              </select></td>
			</tr>
			<tr valign="baseline">
			  <td align="right">&nbsp;</td>
			  <td><input type="submit" class="input_standard" value="Add field" /></td>
			</tr>
		  </table>
					<input type="hidden" name="MM_insert" value="frm_add_field" />
                    <input type="hidden" name="template" value="<?php echo $_GET['template']; ?>" />
					<input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
            </form>
		<?php	
		}
		
		elseif ($_POST['action'] == "delete_servicetemplate") {
			
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_template = "SELECT * FROM servicetemplate WHERE container = ".$_GET['container']." AND id = ".$_GET['template']." ORDER BY servicetemplate.name";
			$template = mysql_query($query_template, $subman) or die(mysql_error());
			$row_template = mysql_fetch_assoc($template);
			$totalRows_template = mysql_num_rows($template);
			?>
			
			Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service" title="Browse service templates">Service Templates</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_template['name']; ?></strong>
			
            <form action="" method="post" target="_self" name="frm_delete_servicetemplate">
			<p>Are you sure you want to delete this service template?</p>
            <input type="hidden" name="id" value="<?php echo $_GET['template']; ?>" />
            <input type="hidden" name="MM_delete" value="frm_delete_servicetemplate" />
            <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
            <input type="submit" value="Confirm" class="input_standard" />
            
			
		<?php
        }
		elseif ($_POST['action'] == "recover_link") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getLink = "SELECT * FROM links WHERE links.id = ".$_POST['link']."";
			$getLink = mysql_query($query_getLink, $subman) or die(mysql_error());
			$row_getLink = mysql_fetch_assoc($getLink);
			$totalRows_getLink = mysql_num_rows($getLink);
			
			mysql_select_db($database_subman, $subman);
			$query_getNodeA = "SELECT * FROM portsdevices WHERE portsdevices.id = '".$row_getLink['provide_node_a']."'";
			$getNodeA = mysql_query($query_getNodeA, $subman) or die(mysql_error());
			$row_getNodeA = mysql_fetch_assoc($getNodeA);
			$totalRows_getNodeA = mysql_num_rows($getNodeA);
			
			mysql_select_db($database_subman, $subman);
			$query_getNodeB = "SELECT * FROM portsdevices WHERE portsdevices.id = '".$row_getLink['provide_node_b']."'";
			$getNodeB = mysql_query($query_getNodeB, $subman) or die(mysql_error());
			$row_getNodeB = mysql_fetch_assoc($getNodeB);
			$totalRows_getNodeB = mysql_num_rows($getNodeB);
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT vpn.*, provider.id as providerid FROM vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE vpn.id = '".$row_getLink['provide_vpn']."'";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			
			mysql_select_db($database_subman, $subman);
			$query_network = "SELECT * FROM networks WHERE networks.id = '".$row_getLink['provide_network']."'";
			$network = mysql_query($query_network, $subman) or die(mysql_error());
			$row_network = mysql_fetch_assoc($network);
			$totalRows_network = mysql_num_rows($network);
			
			if ($row_getNodeA['devicegroup'] != "" && $row_getNodeA['devicegroup'] != "0" && !(getDeviceLevel($row_getNodeA['id'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($row_getNodeA['devicegroup'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($row_getNodeA['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($row_getNodeA['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_getNodeA['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			if ($row_getNodeB['devicegroup'] != "" && $row_getNodeB['devicegroup'] != "0" && !(getDeviceLevel($row_getNodeB['id'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($row_getNodeB['devicegroup'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($row_getNodeB['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($row_getNodeB['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_getNodeB['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			if (!(getVpnLevel($row_getVpn['id'], $_SESSION['MM_Username']) > 20 || (getProviderLevel($row_getVpn['providerid'],$_SESSION['MM_Username']) > 20 && getVpnLevel($row_getVpn['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($row_getVpn['providerid'],$_SESSION['MM_Username']) == "" && getVpnLevel($row_getVpn['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			if (!(getNetworkLevel($row_network['id'], $_SESSION['MM_Username']) > 20 || (getNetGroupLevel($row_network['networkGroup'],$_SESSION['MM_Username']) > 0 && getNetworkLevel($row_network['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getNetGroupLevel($row_network['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_network['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				?>
			
			<h4>Recover link...</h4>
            
            <p>Are you sure you want to recover this link?</p>
            
            <ul>
            	<li>All port information will be removed.</li>
                <li>Any pseudowire numbers will be released.</li>
                <li>If this link is the only link in a layer 2 VPN, the VPN will be removed.</li>
                <li>If the ports are associated with a VLAN, and there are no other ports in the VLAN, the VLAN will be removed.</li>
                <li>Any sub-interfaces or timeslots associated with the link will be removed.</li>
                <li>Any network addressing will be removed (unless the checkbox below is ticked).</li>
            </ul>
            
            <form action="" method="post" target="_self">
            
            	<p><input type="checkbox" value="1" class="input_standard" name="retain_network" /> <em>Retain network addressing </em></p>
                <input type="hidden" name="recover_link" value="<?php echo $_POST['link']; ?>" />
                <input type="submit" value="Recover link" class="input_standard" />
            
            </form>
		
        <?php	
		}
		elseif ($routesconfig || ($_POST['MM_delete'] == 'frm_delete_route' && $totalRows_provide_scripts > 0)) { 
			
			mysql_select_db($database_subman, $subman);
			$query_getLink = "SELECT linknets.*, servicetemplate.routes, portsdevices.name AS devicename, portsdevices.managementip, INET_NTOA(networks.network) AS _network, networks.network, networks.mask, networks.v6mask, vrf.name AS vrfname, servicetemplate.id AS servicetemplateid FROM linknets LEFT JOIN links ON links.id = linknets.link LEFT JOIN servicetemplate ON servicetemplate.id = links.servicetemplate LEFT JOIN portsdevices ON portsdevices.id = servicetemplate.routesnode LEFT JOIN networks ON networks.id = linknets.network LEFT JOIN vrf ON vrf.id = links.provide_vrf WHERE linknets.id = '".$linknetid."'";
			$getLink = mysql_query($query_getLink, $subman) or die(mysql_error());
			$row_getLink = mysql_fetch_assoc($getLink);
			$totalRows_getLink = mysql_num_rows($getLink);
			
			#if ($totalRows_getLink == 0) {
				
				#mysql_select_db($database_subman, $subman);
				#$query_provide_scripts = "SELECT * FROM scripts WHERE scripts.servicetemplate = '".$deleteroutes_servicetemplateid."' AND scripts.scriptrole = 'routesrecover'";
				#$provide_scripts = mysql_query($query_provide_scripts, $subman) or die(mysql_error());
				#$row_provide_scripts = mysql_fetch_assoc($provide_scripts);
				#$totalRows_provide_scripts = mysql_num_rows($provide_scripts);
			
			#	if ($row_getLink['v6mask'] == "") {
			#		$deleteroutes_network = $row_getLink['_network'];
			#		$deleteroutes_mask = $row_getLink['mask'];
			#	}
			#	else {
			#		$deleteroutes_network = Net_IPv6::Compress(long2ipv6($row_getLink['network']));
			#		$deleteroutes_mask = "/".$row_getLink['v6mask'];
			#	}
			#	$deleteroutes_vrf = $row_getLink['vrfname'];
			#	$deleteroutes_nexthop = $row_getLink['nexthop'];
			#	$deleteroutes_devicename = $row_getLink['devicename'];
			#	$deleteroutes_servicetemplateid = $row_getLink['servicetemplateid'];
				
			#}
			#else {
				
				#mysql_select_db($database_subman, $subman);
				#$query_provide_scripts = "SELECT * FROM scripts WHERE scripts.servicetemplate = '".$row_getLink['servicetemplateid']."' AND scripts.scriptrole = 'routesdeploy'";
				#$provide_scripts = mysql_query($query_provide_scripts, $subman) or die(mysql_error());
				#$row_provide_scripts = mysql_fetch_assoc($provide_scripts);
				#$totalRows_provide_scripts = mysql_num_rows($provide_scripts);
			
			#}
			?>
        	
            <?php if ($routesconfig) { ?>
            
            <p><strong>Routes Configuration Template</strong></p>
            
            <?php
            
            if (Net_IPv6::checkIPv6($deleteroutes_network)) {
				$ipversioncisco = 'ipv6';
				$ipversion = 'IPv6';
			}
			else {
				$ipversioncisco = 'ip';
				$ipversion = 'IPv4';
			}
			
            if ($routesdelete == 1) {
			
			if ($ipversion == "IPv6") {
				$config = str_replace("%network%",$deleteroutes_network,$routesconfig);
				$config = str_replace("%netmask%",$deleteroutes_mask,$config);
				$config = str_replace("%netmask_slash%",$deleteroutes_mask,$config);
				$config = str_replace("%route_cisco%",$deleteroutes_network.$deleteroutes_mask,$config);
			}
			else {
				$config = str_replace("%network%",$deleteroutes_network,$routesconfig);
				$config = str_replace("%netmask%",$deleteroutes_mask,$config);
				$config = str_replace("%netmask_slash%",get_slash($deleteroutes_mask),$config);
				$config = str_replace("%route_cisco%",$deleteroutes_network." ".$deleteroutes_mask,$config);
			}
			$config = str_replace("%vrf%",$deleteroutes_vrf,$config);
			$config = str_replace("%nexthop%",$deleteroutes_nexthop,$config);
			$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
			$config = str_replace("%ipversion%",$ipversion,$config);
			$config = str_replace("%router%",$deleteroutes_router,$config);
			
			}
			else {
			
			if ($row_getLink['v6mask'] != "") {
				$ipversioncisco = 'ipv6';
				$ipversion = 'IPv6';
			}
			else {
				$ipversioncisco = 'ip';
				$ipversion = 'IPv4';
			}
			
			if ($ipversion == "IPv6") {
				$config = str_replace("%network%",Net_IPv6::compress(long2ipv6($row_getLink['network'])),$routesconfig);
				$config = str_replace("%netmask%","/".$row_getLink['v6mask'],$config);
				$config = str_replace("%netmask_slash%","/".$row_getLink['v6mask'],$config);
				$config = str_replace("%route_cisco%",Net_IPv6::compress(long2ipv6($row_getLink['network']))."/".$row_getLink['v6mask'],$config);
			}
			else {
				$config = str_replace("%network%",long2ip($row_getLink['network']),$routesconfig);
				$config = str_replace("%netmask%",$row_getLink['mask'],$config);
				$config = str_replace("%netmask_slash%",get_slash($row_getLink['mask']),$config);
				$config = str_replace("%route_cisco%",long2ip($row_getLink['network'])." ".$row_getLink['mask'],$routesconfig);
			}
			$config = str_replace("%vrf%",$row_getLink['vrfname'],$config);
			$config = str_replace("%nexthop%",$row_getLink['nexthop'],$config);
			$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
			$config = str_replace("%ipversion%",$ipversion,$config);
			$config = str_replace("%router%",$row_getLink['managementip'],$config);
			
			}
			?>
						
            <textarea name="routes" class="input_standard" cols="50" rows="5"><?php echo $config; ?></textarea>
            
            <?php } ?>
            
            <?php if ($totalRows_provide_scripts > 0) {
							
				
						?>
                        
                        <?php do {
							
							mysql_select_db($database_subman, $subman);
							$query_variables = "SELECT * FROM scriptvariables WHERE scriptvariables.script = '".$row_provide_scripts['id']."' ORDER BY id";
							$variables = mysql_query($query_variables, $subman) or die(mysql_error());
							$row_variables = mysql_fetch_assoc($variables);
							$totalRows_variables = mysql_num_rows($variables);
							
							if (Net_IPv6::checkIPv6($deleteroutes_network)) {
								$ipversioncisco = 'ipv6';
								$ipversion = 'IPv6';
							}
							else {
								$ipversioncisco = 'ip';
								$ipversion = 'IPv4';
							}
							?>
                        <?php if ($row_provide_scripts['scripttype'] == 'HTTP') { ?>
                        <?php $script = $row_provide_scripts['location']."?"; ?>
						<?php do { 
								if ($routesdelete == 1) {
			
			if ($ipversion == "IPv6") {
				$config = str_replace("%network%",$deleteroutes_network,$row_variables['value']);
				$config = str_replace("%netmask%",$deleteroutes_mask,$config);
				$config = str_replace("%netmask_slash%",$deleteroutes_mask,$config);
				$config = str_replace("%route_cisco%",$deleteroutes_network.$deleteroutes_mask,$config);
			}
			else {
				$config = str_replace("%network%",$deleteroutes_network,$row_variables['value']);
				$config = str_replace("%netmask%",$deleteroutes_mask,$config);
				$config = str_replace("%netmask_slash%",get_slash($deleteroutes_mask),$config);
				$config = str_replace("%route_cisco%",$deleteroutes_network." ".$deleteroutes_mask,$config);
			}
			$config = str_replace("%vrf%",$deleteroutes_vrf,$config);
			$config = str_replace("%nexthop%",$deleteroutes_nexthop,$config);
			$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
			$config = str_replace("%ipversion%",$ipversion,$config);
			$config = str_replace("%router%",$deleteroutes_router,$config);
			
			}
			else {
			
			if ($row_getLink['v6mask'] != "") {
				$ipversioncisco = 'ipv6';
				$ipversion = 'IPv6';
			}
			else {
				$ipversioncisco = 'ip';
				$ipversion = 'IPv4';
			}
			
			if ($ipversion == "IPv6") {
				$config = str_replace("%network%",Net_IPv6::compress(long2ipv6($row_getLink['network'])),$row_variables['value']);
				$config = str_replace("%netmask%","/".$row_getLink['v6mask'],$config);
				$config = str_replace("%netmask_slash%","/".$row_getLink['v6mask'],$config);
				$config = str_replace("%route_cisco%",Net_IPv6::compress(long2ipv6($row_getLink['network']))."/".$row_getLink['v6mask'],$config);
			}
			else {
				$config = str_replace("%network%",long2ip($row_getLink['network']),$row_variables['value']);
				$config = str_replace("%netmask%",$row_getLink['mask'],$config);
				$config = str_replace("%netmask_slash%",get_slash($row_getLink['mask']),$config);
				$config = str_replace("%route_cisco%",long2ip($row_getLink['network'])." ".$row_getLink['mask'],$routesconfig);
			}
			$config = str_replace("%vrf%",$row_getLink['vrfname'],$config);
			$config = str_replace("%nexthop%",$row_getLink['nexthop'],$config);
			$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
			$config = str_replace("%ipversion%",$ipversion,$config);
			$config = str_replace("%router%",$row_getLink['managementip'],$config);
			
			}
								?><?php $script .= $row_variables['variablename']."=".urlencode($config)."&";?>
                            <?php } while ($row_variables = mysql_fetch_assoc($variables)); ?>
                            
                            <br />
                            <?php if ($row_provide_scripts['autorun'] != 1) { ?>
                            <input type="button" name="launch_script" id="launch_script" class="input_red" title="Execute Script <?php echo $script; ?>" value="<?php echo $row_provide_scripts['description']; ?>" onClick="document.getElementById('script_terminal').src = '<?php echo $script; ?>'; document.getElementById('launch_script').disabled = 'disabled'; document.getElementById('script_prompt').style.display = 'block';" /><span id="script_prompt" style="display:none">Executing script, please wait for output below...</span><br />
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto"></iframe><br />
                            <?php } else { ?>
                            Executing script, please wait for output below...<br />
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto" src="<?php echo $script; ?>"></iframe><br />
                            <?php } ?>
                            
                        <?php } elseif ($row_provide_scripts['scripttype'] == 'CLI') { ?>
                        
                        <?php $script = $row_provide_scripts['location']." "; ?>
						<?php do { 
								if ($routesdelete == 1) {
			
			if ($ipversion == "IPv6") {
				$config = str_replace("%network%",$deleteroutes_network,$row_variables['value']);
				$config = str_replace("%netmask%",$deleteroutes_mask,$config);
				$config = str_replace("%netmask_slash%",$deleteroutes_mask,$config);
				$config = str_replace("%route_cisco%",$deleteroutes_network.$deleteroutes_mask,$config);
			}
			else {
				$config = str_replace("%network%",$deleteroutes_network,$row_variables['value']);
				$config = str_replace("%netmask%",$deleteroutes_mask,$config);
				$config = str_replace("%netmask_slash%",get_slash($deleteroutes_mask),$config);
				$config = str_replace("%route_cisco%",$deleteroutes_network.get_slash($deleteroutes_mask),$config);
			}
			$config = str_replace("%vrf%",$deleteroutes_vrf,$config);
			$config = str_replace("%nexthop%",$deleteroutes_nexthop,$config);
			$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
			$config = str_replace("%ipversion%",$ipversion,$config);
			$config = str_replace("%router%",$deleteroutes_router,$config);
			
			}
			else {
			
			if ($row_getLink['v6mask'] != "") {
				$ipversioncisco = 'ipv6';
				$ipversion = 'IPv6';
			}
			else {
				$ipversioncisco = 'ip';
				$ipversion = 'IPv4';
			}
			
			if ($ipversion == "IPv6") {
				$config = str_replace("%network%",Net_IPv6::compress(long2ipv6($row_getLink['network'])),$row_variables['value']);
				$config = str_replace("%netmask%","/".$row_getLink['v6mask'],$config);
				$config = str_replace("%netmask_slash%","/".$row_getLink['v6mask'],$config);
				$config = str_replace("%route_cisco%",Net_IPv6::compress(long2ipv6($row_getLink['network']))."/".$row_getLink['v6mask'],$config);
			}
			else {
				$config = str_replace("%network%",long2ip($row_getLink['network']),$row_variables['value']);
				$config = str_replace("%netmask%",$row_getLink['mask'],$config);
				$config = str_replace("%netmask_slash%",get_slash($row_getLink['mask']),$config);
				$config = str_replace("%route_cisco%",long2ip($row_getLink['network'])." ".$row_getLink['mask'],$routesconfig);
			}
			$config = str_replace("%vrf%",$row_getLink['vrfname'],$config);
			$config = str_replace("%nexthop%",$row_getLink['nexthop'],$config);
			$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
			$config = str_replace("%ipversion%",$ipversion,$config);
			$config = str_replace("%router%",$row_getLink['managementip'],$config);
			
			}
								?><?php $script .= $config." ";?>
                            <?php } while ($row_variables = mysql_fetch_assoc($variables)); ?>
                            <?php
								$_SESSION['script'] = $script;
								?>
                            <br />
                            <?php if ($row_provide_scripts['autorun'] != 1) { ?>
                            <input type="button" name="launch_script" id="launch_script" class="input_red" title="Execute Script <?php echo $script; ?>" value="<?php echo $row_provide_scripts['description']; ?>" onClick="document.getElementById('script_terminal').src = 'includes/launch_cli.php'; document.getElementById('launch_script').disabled = 'disabled'; document.getElementById('script_prompt').style.display = 'block';" /><span id="script_prompt" style="display:none">Executing script, please wait for output below...</span><br />
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto"></iframe><br />
                            <?php } else { ?>
                            Executing script, please wait for output below...<br />
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto" src="includes/launch_cli.php"></iframe><br />
                            <?php } ?>
                        <?php
						
						} ?>
                        
                        <?php
						
							} while ($row_provide_scripts = mysql_fetch_assoc($provide_scripts));
						
						}
						?>
            <br />
            <a href="?browse=devices&container=<?php echo $_POST['container']; ?>&device=<?php echo $_POST['device']; ?>&group=<?php echo $_POST['devicegroup']; ?>&port=<?php echo $_POST['port']; ?>&linkview=1" title="View link">Click here</a> to view the link.
        <?php } 
		
		elseif ($_POST['action'] == "add_route") {
			
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_getLink = "SELECT * FROM links WHERE links.id = ".$_POST['link']."";
			$getLink = mysql_query($query_getLink, $subman) or die(mysql_error());
			$row_getLink = mysql_fetch_assoc($getLink);
			$totalRows_getLink = mysql_num_rows($getLink);
			
			mysql_select_db($database_subman, $subman);
			$query_getNetwork = "SELECT * FROM networks WHERE networks.id = '".$row_getLink['provide_network']."'";
			$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
			$row_getNetwork = mysql_fetch_assoc($getNetwork);
			$totalRows_getNetwork = mysql_num_rows($getNetwork);
			
			mysql_select_db($database_subman, $subman);
			$query_network = "SELECT networks.*, container.name as containername FROM networks LEFT JOIN container ON container.id = networks.container WHERE networks.container = ".$_GET['container']." ORDER BY networks.network, networks.mask, networks.v6mask";
			$network = mysql_query($query_network, $subman) or die(mysql_error());
			$row_network = mysql_fetch_assoc($network);
			$totalRows_network = mysql_num_rows($network);
			
				?>
            
    <p>Search for the network to add as a route.</p>
            
    <form action="" method="post" target="_self" onSubmit="MM_validateForm('nexthop','','R','network','','RIsNum');return document.MM_returnValue">
   	  
   	  Network: <input type="text" name="network_search" id="network_search" class="input_standard" size="32" maxlength="512" autocomplete="off" onKeyUp="if (this.value == '') { document.getElementById('searchQ_1').style.display='none'; } else { document.getElementById('searchQ_1').style.display='block'; searchQry_networks(this.value); }">
   	  <div id="searchQ_1" class="searchQ_1"></div>
   	  	<input type="hidden" name="network" id="network" value="">
        Next hop: <input name="nexthop" type="text" class="input_standard" id="nexthop" size="32" maxlength="50" /><br />
        <input type="hidden" name="add_route" value="<?php echo $_POST['link']; ?>" />
        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
        <input type="hidden" name="device" value="<?php echo $_GET['device']; ?>" />
        <input type="hidden" name="devicegroup" value="<?php echo $_GET['group']; ?>" />
        <input type="hidden" name="port" value="<?php echo $_GET['port']; ?>" />
        <input type="text" class="input_selected" disabled="disabled" id="selected_network" value="" style="display:none" size="50"><br />
        <input type="submit" value="Add route" class="input_standard" />
    </form>
    
            <?php
			
		}
		
		elseif ($_POST['action'] == "change_cct") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getLink = "SELECT * FROM links WHERE links.id = ".$_POST['link']."";
			$getLink = mysql_query($query_getLink, $subman) or die(mysql_error());
			$row_getLink = mysql_fetch_assoc($getLink);
			$totalRows_getLink = mysql_num_rows($getLink);
			
			mysql_select_db($database_subman, $subman);
			$query_getNodeA = "SELECT * FROM portsdevices WHERE portsdevices.id = '".$row_getLink['provide_node_a']."'";
			$getNodeA = mysql_query($query_getNodeA, $subman) or die(mysql_error());
			$row_getNodeA = mysql_fetch_assoc($getNodeA);
			$totalRows_getNodeA = mysql_num_rows($getNodeA);
			
			mysql_select_db($database_subman, $subman);
			$query_getNodeB = "SELECT * FROM portsdevices WHERE portsdevices.id = '".$row_getLink['provide_node_b']."'";
			$getNodeB = mysql_query($query_getNodeB, $subman) or die(mysql_error());
			$row_getNodeB = mysql_fetch_assoc($getNodeB);
			$totalRows_getNodeB = mysql_num_rows($getNodeB);
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT vpn.*, provider.id as providerid FROM vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE vpn.id = '".$row_getLink['provide_vpn']."'";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			
			mysql_select_db($database_subman, $subman);
			$query_network = "SELECT * FROM networks WHERE networks.id = '".$row_getLink['provide_network']."'";
			$network = mysql_query($query_network, $subman) or die(mysql_error());
			$row_network = mysql_fetch_assoc($network);
			$totalRows_network = mysql_num_rows($network);
			
			if ($row_getNodeA['devicegroup'] != "" && $row_getNodeA['devicegroup'] != "0" && !(getDeviceLevel($row_getNodeA['id'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($row_getNodeA['devicegroup'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($row_getNodeA['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($row_getNodeA['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_getNodeA['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			if ($row_getNodeB['devicegroup'] != "" && $row_getNodeB['devicegroup'] != "0" && !(getDeviceLevel($row_getNodeB['id'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($row_getNodeB['devicegroup'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($row_getNodeB['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($row_getNodeB['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_getNodeB['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			if (!(getVpnLevel($row_getVpn['id'], $_SESSION['MM_Username']) > 20 || (getProviderLevel($row_getVpn['providerid'],$_SESSION['MM_Username']) > 20 && getVpnLevel($row_getVpn['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($row_getVpn['providerid'],$_SESSION['MM_Username']) == "" && getVpnLevel($row_getVpn['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			if (!(getNetworkLevel($row_network['id'], $_SESSION['MM_Username']) > 20 || (getNetGroupLevel($row_network['networkGroup'],$_SESSION['MM_Username']) > 0 && getNetworkLevel($row_network['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getNetGroupLevel($row_network['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_network['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				?>
			
			Link <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getLink['provide_cct']; ?></strong>
            
            <form action="" method="post" target="_self" onSubmit="MM_validateForm('change_cct','','R');return document.MM_returnValue">
            	
                <input type="text" name="change_cct" id="change_cct" value="<?php echo $row_getLink['provide_cct']; ?>" width="32" maxlength="255" class="input_standard" />
                <input type="hidden" name="change_cct_link" value="<?php echo $_POST['link']; ?>" />
                <input type="hidden" name="old_cct" value="<?php echo $row_getLink['provide_cct']; ?>" />
                <input type="submit" value="Change Circuit Reference" class="input_standard" />
            
            </form>
		
        <?php	
		}
		
		elseif ($_POST['action'] == "change_customer") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getLink = "SELECT links.*, customer.name as customername FROM links LEFT JOIN customer ON customer.id = links.provide_customer WHERE links.id = ".$_POST['link']."";
			$getLink = mysql_query($query_getLink, $subman) or die(mysql_error());
			$row_getLink = mysql_fetch_assoc($getLink);
			$totalRows_getLink = mysql_num_rows($getLink);
			
			mysql_select_db($database_subman, $subman);
			$query_customers = "SELECT * FROM customer WHERE container = '".$_GET['container']."' ORDER BY customer.name";
			$customers = mysql_query($query_customers, $subman) or die(mysql_error());
			$row_customers = mysql_fetch_assoc($customers);
			$totalRows_customers = mysql_num_rows($customers);
			
			mysql_select_db($database_subman, $subman);
			$query_getNodeA = "SELECT * FROM portsdevices WHERE portsdevices.id = '".$row_getLink['provide_node_a']."'";
			$getNodeA = mysql_query($query_getNodeA, $subman) or die(mysql_error());
			$row_getNodeA = mysql_fetch_assoc($getNodeA);
			$totalRows_getNodeA = mysql_num_rows($getNodeA);
			
			mysql_select_db($database_subman, $subman);
			$query_getNodeB = "SELECT * FROM portsdevices WHERE portsdevices.id = '".$row_getLink['provide_node_b']."'";
			$getNodeB = mysql_query($query_getNodeB, $subman) or die(mysql_error());
			$row_getNodeB = mysql_fetch_assoc($getNodeB);
			$totalRows_getNodeB = mysql_num_rows($getNodeB);
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT vpn.*, provider.id as providerid FROM vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE vpn.id = '".$row_getLink['provide_vpn']."'";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			
			mysql_select_db($database_subman, $subman);
			$query_network = "SELECT * FROM networks WHERE networks.id = '".$row_getLink['provide_network']."'";
			$network = mysql_query($query_network, $subman) or die(mysql_error());
			$row_network = mysql_fetch_assoc($network);
			$totalRows_network = mysql_num_rows($network);
			
			if ($row_getNodeA['devicegroup'] != "" && $row_getNodeA['devicegroup'] != "0" && !(getDeviceLevel($row_getNodeA['id'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($row_getNodeA['devicegroup'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($row_getNodeA['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($row_getNodeA['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_getNodeA['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			if ($row_getNodeB['devicegroup'] != "" && $row_getNodeB['devicegroup'] != "0" && !(getDeviceLevel($row_getNodeB['id'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($row_getNodeB['devicegroup'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($row_getNodeB['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($row_getNodeB['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_getNodeB['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			if (!(getVpnLevel($row_getVpn['id'], $_SESSION['MM_Username']) > 20 || (getProviderLevel($row_getVpn['providerid'],$_SESSION['MM_Username']) > 20 && getVpnLevel($row_getVpn['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($row_getVpn['providerid'],$_SESSION['MM_Username']) == "" && getVpnLevel($row_getVpn['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
			
			if (!(getNetworkLevel($row_network['id'], $_SESSION['MM_Username']) > 20 || (getNetGroupLevel($row_network['networkGroup'],$_SESSION['MM_Username']) > 0 && getNetworkLevel($row_network['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getNetGroupLevel($row_network['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_network['id'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				?>
			
			Link <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getLink['provide_cct']; ?></strong>
            
            <ul>
            
            	<li>If this link is part of a VPN, the customer will be added to the VPN.  The original customer will only be removed from the VPN if there are no additional links for that customer in the VPN.</li>
                <li>If there is a network assigned for this customer, the description will be changed.  Any addresses within the network that are assigned to the previous customer will be re-allocated to the new customer.</li>
                <li>Any ports allocated will be re-assigned to the new customer</li>
                <li>If VLANs are in use, the ownership of the VLAN will only be changed if this link is the only link with ports in that VLAN (VLAN ownership can be changed manually at any time by selecting 'Edit VLAN' in the menu).</li>
            
            </ul>
            <form action="" method="post" target="_self">
            	
                <select name="customer" class="input_standard" id="customer">
            <?php
do {  
?>
            <option value="<?php echo $row_customers['id']?>" <?php if ($row_customers['id'] == $row_getLink['provide_customer']) echo "selected=\"selected\""; ?>><?php echo $row_customers['name']?></option>
            <?php
} while ($row_customers = mysql_fetch_assoc($customers));
  $rows = mysql_num_rows($customers);
  if($rows > 0) {
      mysql_data_seek($customers, 0);
	  $row_customers = mysql_fetch_assoc($customers);
  }
?>
          </select>
                <input type="hidden" name="change_customer_link" value="<?php echo $_POST['link']; ?>" />
                <input type="hidden" name="old_customer" value="<?php echo $row_getLink['customername']; ?>" />
                <input type="hidden" name="old_customer_id" value="<?php echo $row_getLink['provide_customer']; ?>" />
                <input type="submit" value="Change Customer" class="input_standard" />
            
            </form>
		
        <?php	
		}
		
		elseif ($_POST['action'] == "add_devicegroup") {
			
				if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
		<p class="text_red">Error: You are not authorised to view the selected content.</p>
	
		<?php 
						exit();
					} ?>
					
				Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>Device Groups</strong>
				
				<p>&nbsp;</p>
				
				<form name="frm_adddevicegroup" id="frm_adddevicegroup" method="post" target="_self" action=""  onSubmit="MM_validateForm('groupname','','R');return document.MM_returnValue">
					<table>
			<tr valign="baseline">
			  <td align="right" valign="top">Name:</td>
			  <td><input name="groupname" type="text" class="input_standard" id="groupname" value="" size="32" maxlength="255" /></td>
			</tr>
			<tr valign="baseline">
			  <td align="right">&nbsp;</td>
			  <td><input type="submit" class="input_standard" value="Add device group" /></td>
			</tr>
		  </table>
					<input type="hidden" name="MM_insert" value="frm_adddevicegroup" />
					<input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
				</form>
		
		<?php 
		
		}
			
		elseif ($_GET['devicegroups'] == 1) {
		
			if ($_POST['action'] == "delete_device_group") {
				
				mysql_select_db($database_subman, $subman);
				$query_deviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
				$deviceGroup = mysql_query($query_deviceGroup, $subman) or die(mysql_error());
				$row_deviceGroup = mysql_fetch_assoc($deviceGroup);
				$totalRows_deviceGroup = mysql_num_rows($deviceGroup);
				?>
				
				 Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;devicegroups=1" title="Browse device groups">Device Groups</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_deviceGroup['name']; ?></strong>
				 
				 <p>&nbsp;</p>
				 
				 <?php
				
					if (!(getDeviceGroupLevel($_GET['group'], $_SESSION['MM_Username']) > 20 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == ""))) { ?>
			<p class="text_red">Error: You are not authorised to view the selected content.</p>
			<?php 
							exit();
					} ?>
					
				<form action="" method="post" target="_self" name="frm_delete_devicegroup" id="frm_delete_devicegroup">
				  <p>Are you sure you want to delete this device group?</p>
				  <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
				  <input name="id" type="hidden" id="id" value="<?php echo $_GET['group']; ?>" />
				  <input name="MM_delete" type="hidden" id="MM_delete" value="frm_delete_devicegroup" />
				  <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
				</form>
			
			<?php	
			}
		
			elseif ($_GET['group'] != "") {
				
				mysql_select_db($database_subman, $subman);
				$query_deviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
				$deviceGroup = mysql_query($query_deviceGroup, $subman) or die(mysql_error());
				$row_deviceGroup = mysql_fetch_assoc($deviceGroup);
				$totalRows_deviceGroup = mysql_num_rows($deviceGroup);
				?>
				
				 Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;devicegroups=1" title="Browse device groups">Device Groups</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_deviceGroup['name']; ?></strong>
				 
				 <p>&nbsp;</p>
				 
				 <?php
				
					if (!(getDeviceGroupLevel($_GET['group'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == ""))) { ?>
			<p class="text_red">Error: You are not authorised to view the selected content.</p>
			<?php 
							exit();
					} ?>
					
				<form action="<?php echo $editFormAction; ?>" method="post" id="frm_editdevicegroup" onSubmit="MM_validateForm('groupname','','R');return document.MM_returnValue">
		  <table>
			<tr valign="baseline">
			  <td align="right" valign="top">Name:</td>
			  <td><input name="groupname" type="text" class="input_standard" id="groupname" value="<?php echo htmlentities($row_deviceGroup['name'], ENT_COMPAT, 'utf-8'); ?>" size="32" maxlength="255" /></td>
			</tr>
			<tr valign="baseline">
			  <td align="right">&nbsp;</td>
			  <td><input type="submit" class="input_standard" value="Update device group" /></td>
			</tr>
		  </table>
		  <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
		  <input type="hidden" name="MM_update" value="frm_editdevicegroup" />
		  <input type="hidden" name="id" value="<?php echo $row_deviceGroup['id']; ?>" />
		</form>	
			
			<?php	
			}
			
			else {
					
				mysql_select_db($database_subman, $subman);
				$query_deviceGroups = "SELECT * FROM portgroups WHERE portgroups.container = ".$_GET['container']." ORDER BY portgroups.name";
				$deviceGroups = mysql_query($query_deviceGroups, $subman) or die(mysql_error());
				$row_deviceGroups = mysql_fetch_assoc($deviceGroups);
				$totalRows_deviceGroups = mysql_num_rows($deviceGroups);
				?>
				
				 Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>Device Groups</strong>
				 
				 <p>&nbsp;</p>
				 
                 <?php if ($totalRows_deviceGroups > 0) { ?>
                 
				<table border="0" width="50%">
				  <tr>
					<td><strong>Device Groups</strong></td>
				  </tr>
				  <?php $count = 0;
					do {
						
						$count++;
						if ($count % 2) {
							$bgcolour = "#EAEAEA";
						}
						else {
							$bgcolour = "#F5F5F5";
						} ?>
					<tr bgcolor="<?php echo $bgcolour; ?>">
					  <td><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;devicegroups=1&amp;group=<?php echo $row_deviceGroups['id']; ?>" title"Edit this device group"><?php echo $row_deviceGroups['name']; ?></a></td>
					</tr>
					<?php } while ($row_deviceGroups = mysql_fetch_assoc($deviceGroups)); ?>
				</table>
                
                <?php } else { ?>
                
                	<p>There are no device groups to display.</p>
                    
                <?php } ?>
				
		<?php 
			}
			
		}
		
		elseif ($_POST['action'] == "add_cardtype") { 
			
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				?>
			
			Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsecardtypes=1" title="Browse line card types">Line Card Types</a><br /><br />
			
            <form action="" method="post" name="frm_add_cardtype" target="_self" onSubmit="MM_validateForm('typename','','R','config','','R');return document.MM_returnValue">
                	
                 <table border="0" width="50%">
                  <tr>
                    <td><strong>Type</strong></td>
                    <td><input name="name" type="text" class="input_standard" id="typename" value="" size="32" maxlength="255" /></td>
                  </tr>
                    <tr>
                        <td><strong>Config Name</strong></td>
                        <td><input name="config" type="text" class="input_standard" id="config" value="" size="32" maxlength="255" /></td>
                    </tr>
                    
              </table>
                	
                    <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                    <input type="hidden" name="MM_insert" value="frm_add_cardtype" />
                    <input type="submit" value="Add" class="input_standard" />
                    
    </form>
                
		<?php }
		
		elseif ($_POST['action'] == "add_devicetype") { 
			
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				?>
			
			Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsedevicetypes=1" title="Browse device types">Device Types</a><br /><br />
			
            <form action="" method="post" name="frm_add_devicetype" target="_self" onSubmit="MM_validateForm('typename','','R','image','','R');return document.MM_returnValue">
                	
                 <table border="0" width="50%">
                  <tr>
                    <td><strong>Type</strong></td>
                    <td><input name="name" type="text" class="input_standard" id="typename" value="" size="32" maxlength="255" /></td>
                  </tr>
                    <tr>
                        <td><strong>Image Filename</strong></td>
                        <td><input name="image" type="text" class="input_standard" id="image" value="" size="32" maxlength="255" /></td>
                    </tr>
                    
              </table>
                	
                    <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                    <input type="hidden" name="MM_insert" value="frm_add_devicetype" />
                    <input type="submit" value="Add" class="input_standard" />
                    
    </form>
                
		<?php }
		
		elseif ($_GET['browsedevicetypes'] == 1) {
			
			if ($_GET['devicetype'] != "") { 
				
				if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
				mysql_select_db($database_subman, $subman);
				$query_getDeviceType = "SELECT * FROM devicetypes WHERE devicetypes.id = ".$_GET['devicetype'];
				$getDeviceType = mysql_query($query_getDeviceType, $subman) or die(mysql_error());
				$row_getDeviceType = mysql_fetch_assoc($getDeviceType);
				$totalRows_getDeviceType = mysql_num_rows($getDeviceType);
				?>
				
                 Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsedevicetypes=1" title="Browse device types">Device Types</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getDeviceType['name']; ?></strong><br /><br />
                 
                 <form action="" method="post" name="frm_update_devicetype" target="_self" onSubmit="MM_validateForm('typename','','R','config','','R');return document.MM_returnValue">
                	
                 <table border="0" width="50%">
                  <tr>
                    <td><strong>Type</strong></td>
                    <td><input name="name" type="text" class="input_standard" id="typename" value="<?php echo $row_getDeviceType['name']; ?>" size="32" maxlength="255" /></td>
                  </tr>
                    <tr>
                        <td><strong>Image Filename</strong></td>
                        <td><input name="image" type="text" class="input_standard" id="image" value="<?php echo $row_getDeviceType['image']; ?>" size="32" maxlength="255" /></td>
                    </tr>
                    
                  </table>
                	
                    <input type="hidden" name="MM_update" value="frm_update_devicetype" />
                    <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                    <input type="hidden" name="id" value="<?php echo $row_getDeviceType['id']; ?>" />
                    <input type="submit" value="Save" class="input_standard" />
                    
                </form>
                <br />
                
                <?php
				if (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20) { 
				?>
                <form action="" method="post" name="frm_delete_devicetype" target="_self">
				<input type="hidden" name="MM_delete" value="frm_delete_devicetype" />
                    <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                    <input type="hidden" name="id" value="<?php echo $row_getDeviceType['id']; ?>" />
                    <input type="submit" value="Delete" class="input_standard" />
                </form>
                
                <?php } ?>
       	       
        <?php
			}
			
			else {
			
				mysql_select_db($database_subman, $subman);
				$query_deviceTypes = "SELECT * FROM devicetypes ORDER BY devicetypes.name";
				$deviceTypes = mysql_query($query_deviceTypes, $subman) or die(mysql_error());
				$row_deviceTypes = mysql_fetch_assoc($deviceTypes);
				$totalRows_deviceTypes = mysql_num_rows($deviceTypes);
				?>
             Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>Device Types</strong>
             
    <p>&nbsp;</p>
             
            <table border="0" width="50%">
              <tr>
                <td><strong>Type</strong></td>
                <td><strong>Image</strong></td>
              </tr>
              <?php $count = 0;
                do {
                    
                    $count++;
                    if ($count % 2) {
                        $bgcolour = "#EAEAEA";
                    }
                    else {
                        $bgcolour = "#F5F5F5";
                    } ?>
                <tr bgcolor="<?php echo $bgcolour; ?>">
                  <td><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsedevicetypes=1&amp;devicetype=<?php echo $row_deviceTypes['id']; ?>" title"Edit this device type"><?php echo $row_deviceTypes['name']; ?></a></td>
                  <td><?php echo $row_deviceTypes['image']; ?></td>
                </tr>
                <?php } while ($row_deviceTypes = mysql_fetch_assoc($deviceTypes)); ?>
            </table>
			
		<?php 
			}
			
        } 
		
		elseif ($_GET['browsecardtypes'] == 1) {
			
			if ($_GET['cardtype'] != "") { 
				
				if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
				mysql_select_db($database_subman, $subman);
				$query_getCardType = "SELECT * FROM cardtypes WHERE cardtypes.id = ".$_GET['cardtype'];
				$getCardType = mysql_query($query_getCardType, $subman) or die(mysql_error());
				$row_getCardType = mysql_fetch_assoc($getCardType);
				$totalRows_getCardType = mysql_num_rows($getCardType);
				?>
				
                 Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsecardtypes=1" title="Browse line card types">Line Card Types</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getCardType['name']; ?></strong><br /><br />
                 
				<form action="" method="post" name="frm_update_cardtype" target="_self" onSubmit="MM_validateForm('typename','','R','config','','R');return document.MM_returnValue">
                	
                 <table border="0" width="50%">
                  <tr>
                    <td><strong>Type</strong></td>
                    <td><input name="name" type="text" class="input_standard" id="typename" value="<?php echo $row_getCardType['name']; ?>" size="32" maxlength="255" /></td>
                  </tr>
                    <tr>
                        <td><strong>Config Name</strong></td>
                        <td><input name="config" type="text" class="input_standard" id="config" value="<?php echo $row_getCardType['config']; ?>" size="32" maxlength="255" /></td>
                    </tr>
                    
                  </table>
                	
                    <input type="hidden" name="MM_update" value="frm_update_cardtype" />
                    <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                    <input type="hidden" name="id" value="<?php echo $row_getCardType['id']; ?>" />
                    <input type="submit" value="Save" class="input_standard" />
                    
                </form>
                <br />
                
                <?php
				if (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20) { 
				?>
                <form action="" method="post" name="frm_delete_cardtype" target="_self">
				<input type="hidden" name="MM_delete" value="frm_delete_cardtype" />
                    <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                    <input type="hidden" name="id" value="<?php echo $row_getCardType['id']; ?>" />
                    <input type="submit" value="Delete" class="input_standard" />
                </form>
                <?php } ?>
                
			<?php }
			
			else {
			
				mysql_select_db($database_subman, $subman);
				$query_cardTypes = "SELECT * FROM cardtypes ORDER BY cardtypes.name";
				$cardTypes = mysql_query($query_cardTypes, $subman) or die(mysql_error());
				$row_cardTypes = mysql_fetch_assoc($cardTypes);
				$totalRows_cardTypes = mysql_num_rows($cardTypes);
				?>
             Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>Line Card Types</strong>
             
    <p>&nbsp;</p>
             
            <table border="0" width="50%">
              <tr>
                <td><strong>Type</strong></td>
                <td><strong>Config Name</strong></td>
              </tr>
              <?php $count = 0;
                do {
                    
                    $count++;
                    if ($count % 2) {
                        $bgcolour = "#EAEAEA";
                    }
                    else {
                        $bgcolour = "#F5F5F5";
                    } ?>
                <tr bgcolor="<?php echo $bgcolour; ?>">
                  <td><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsecardtypes=1&amp;cardtype=<?php echo $row_cardTypes['id']; ?>" title"Edit this card type"><?php echo $row_cardTypes['name']; ?></a></td>
                  <td><?php echo $row_cardTypes['config']; ?></td>
                </tr>
                <?php } while ($row_cardTypes = mysql_fetch_assoc($cardTypes)); ?>
            </table>
			
		<?php 
			}
			
		}
            
		elseif ($_GET['browsetemplates'] == "service") { 
			
			if ($_GET['script'] != "") {
				
				if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
				mysql_select_db($database_subman, $subman);
				$query_template = "SELECT * FROM servicetemplate WHERE container = ".$_GET['container']." AND id = ".$_GET['template']." ORDER BY servicetemplate.name";
				$template = mysql_query($query_template, $subman) or die(mysql_error());
				$row_template = mysql_fetch_assoc($template);
				$totalRows_template = mysql_num_rows($template);
				
				mysql_select_db($database_subman, $subman);
				$query_script = "SELECT * FROM scripts WHERE scripts.id = ".$_GET['script']."";
				$script = mysql_query($query_script, $subman) or die(mysql_error());
				$row_script = mysql_fetch_assoc($script);
				$totalRows_script = mysql_num_rows($script);
				?>
				
				Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service" title="Browse service templates">Service Templates</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service&amp;template=<?php echo $row_template['id']; ?>" title="View service template"><?php echo $row_template['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_script['description']; ?></strong><br /><br />
            
            <form action="" method="post" name="frm_update_script" target="_self" onSubmit="MM_validateForm('description','','R','location','','R');return document.MM_returnValue">
            <table>
			<tr valign="baseline">
			  <td align="right" valign="top">Description:</td>
			  <td><input name="description" type="text" class="input_standard" id="description" value="<?php echo $row_script['description']; ?>" size="32" maxlength="255" /></td>
			</tr>
            <tr valign="baseline">
			  <td align="right" valign="top">Path/Location:</td>
			  <td><input name="location" type="text" class="input_standard" id="location" value="<?php echo $row_script['location']; ?>" size="32" maxlength="255" />
		      <br /><span class="text_red">For CLI scripts, enter the full system path (including the executable e.g. 'perl &lt;path to script&gt;').  For HTTP, enter the URL.<br />Ensure that the web server has permission to execute the script.</span></td>
			</tr>
            <tr valign="baseline">
			  <td align="right" valign="top">Execution:</td>
			  <td><select name="scripttype" id="scripttype" class="input_standard">
              	<option value="CLI" <?php if (!(strcmp($row_script['scripttype'],'CLI'))) {echo "selected=\"selected\"";} ?>>CLI</option>
                <option value="HTTP" <?php if (!(strcmp($row_script['scripttype'],'HTTP'))) {echo "selected=\"selected\"";} ?>>HTTP</option>
              </select></td>
			</tr>
            <tr valign="baseline">
			  <td align="right" valign="top">Type:</td>
			  <td><select name="scriptrole" id="scriptrole" class="input_standard">
              	<option value="servicedeploy" <?php if (!(strcmp($row_script['scriptrole'],'servicedeploy'))) {echo "selected=\"selected\"";} ?>>Service Deployment</option>
                <option value="servicerecover" <?php if (!(strcmp($row_script['scriptrole'],'servicerecover'))) {echo "selected=\"selected\"";} ?>>Service Recovery</option>
                <option value="routesdeploy" <?php if (!(strcmp($row_script['scriptrole'],'routesdeploy'))) {echo "selected=\"selected\"";} ?>>Routes Deployment</option>
                <option value="routesrecover" <?php if (!(strcmp($row_script['scriptrole'],'routesrecover'))) {echo "selected=\"selected\"";} ?>>Routes Recovery</option>
                <option value="secondarynetsdeploy" <?php if (!(strcmp($row_script['scriptrole'],'secondarynetsdeploy'))) {echo "selected=\"selected\"";} ?>>Secondary Networks Deployment</option>
                <option value="secondarynetsrecover" <?php if (!(strcmp($row_script['scriptrole'],'secondarynetsrecover'))) {echo "selected=\"selected\"";} ?>>Secondary Networks Recovery</option>
              </select></td>
			</tr>
			<tr valign="baseline">
				<td align="right" valign="top">Autorun:</td>
				<td><input type="checkbox" name="autorun" id="autorun" value="1" <?php if ($row_script['autorun'] == 1) { echo "checked=\"checked\""; } ?>></td>
			</tr>
			<tr valign="baseline">
			  <td align="right">&nbsp;</td>
			  <td><input type="submit" class="input_standard" value="Update script" /> <input type="button" name="delete" class="input_standard" value="Delete script" onClick="if (confirm('Are you sure you want to remove the script (the script itself will not be deleted, just the reference to it)?')) { document.getElementById('frm_delete_script<?php echo $_GET['script']; ?>').submit(); } else { return false; }"></td>
			</tr>
		  </table>
					<input type="hidden" name="MM_update" value="frm_update_script" />
                    <input type="hidden" name="template" value="<?php echo $_GET['template']; ?>" />
                    <input type="hidden" name="script" value="<?php echo $_GET['script']; ?>" />
					<input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
            </form>
            
            <form action="" method="post" target="_self" name="frm_delete_script<?php echo $_GET['script']; ?>" id="frm_delete_script<?php echo $_GET['script']; ?>">
                            <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                            <input type="hidden" name="script" value="<?php echo $_GET['script']; ?>" />
                            <input type="hidden" name="template" value="<?php echo $_GET['template']; ?>" />
                            <input type="hidden" name="MM_delete" value="frm_delete_script" />
                      </form>
                      
            <h5>Script Variables</h5>
            
            <?php
				mysql_select_db($database_subman, $subman);
				$query_scriptvariables = "SELECT * FROM scriptvariables WHERE scriptvariables.script = ".$_GET['script']." ORDER BY scriptvariables.id";
				$scriptvariables = mysql_query($query_scriptvariables, $subman) or die(mysql_error());
				$row_scriptvariables = mysql_fetch_assoc($scriptvariables);
				$totalRows_scriptvariables = mysql_num_rows($scriptvariables);
				?>
                
                <?php if ($totalRows_scriptvariables > 0) { ?>
                
                	<table width="50%" border="0">
                    <tr>
                    	<td><strong>Variable Name</strong></td>
                        <td><strong>Value</strong></td>
                    </tr>
                    <?php 
					$count = 0;
					
					do {
						
						$count++;
						
						if ($count % 2) {
							$bgcolour = "#EAEAEA";
						}
						else {
							$bgcolour = "#F5F5F5";
						}
					?>
                    
                    <tr bgcolor="<?php echo $bgcolour; ?>">
                    
                    	<td><a href="#" title="Delete variable" onClick="if (confirm('Are you sure you want to remove the variable <?php echo $row_scriptvariables['variablename']; ?>?')) { document.frm_delete_variable<?php echo $row_scriptvariables['id']; ?>.submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete variable" border="0" align="absmiddle" /></a> <?php echo $row_scriptvariables['variablename']; ?></td>
                        <td><?php echo $row_scriptvariables['value']; ?></td>
                        <form action="" method="post" target="_self" name="frm_delete_variable<?php echo $row_scriptvariables['id']; ?>" id="frm_delete_variable<?php echo $row_scriptvariables['id']; ?>">
                        	<input type="hidden" name="variable" value="<?php echo $row_scriptvariables['id']; ?>" />
                            <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                            <input type="hidden" name="script" value="<?php echo $_GET['script']; ?>" />
                            <input type="hidden" name="template" value="<?php echo $_GET['template']; ?>" />
                            <input type="hidden" name="MM_delete" value="frm_delete_variable" />
                        </form>
                    
                    </tr>
                    
                    <?php } while ($row_scriptvariables = mysql_fetch_assoc($scriptvariables)); ?>
                    
                    </table>
                
                <?php } else { ?>
                	
                    <p>There are no variables for this script.  Select the 'Add a variable' option from the drop-down menu above to add a variable.</p>

				<?php } ?>
                
            <?php
			}
			
			elseif ($_GET['field'] != "") {
				
				if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
				mysql_select_db($database_subman, $subman);
				$query_template = "SELECT * FROM servicetemplate WHERE container = ".$_GET['container']." AND id = ".$_GET['template']." ORDER BY servicetemplate.name";
				$template = mysql_query($query_template, $subman) or die(mysql_error());
				$row_template = mysql_fetch_assoc($template);
				$totalRows_template = mysql_num_rows($template);
				
				mysql_select_db($database_subman, $subman);
				$query_arbitraryfield = "SELECT * FROM arbitraryfields WHERE arbitraryfields.id = ".$_GET['field']."";
				$arbitraryfield = mysql_query($query_arbitraryfield, $subman) or die(mysql_error());
				$row_arbitraryfield = mysql_fetch_assoc($arbitraryfield);
				$totalRows_arbitraryfield = mysql_num_rows($arbitraryfield);
				?>
				
				Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service" title="Browse service templates">Service Templates</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service&amp;template=<?php echo $row_template['id']; ?>" title="View service template"><?php echo $row_template['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_arbitraryfield['title']; ?></strong><br /><br />
            
            <form action="" method="post" name="frm_update_script" target="_self" onSubmit="MM_validateForm('title','','R','value','','R');return document.MM_returnValue">
            <table>
			<tr valign="baseline">
			  <td align="right" valign="top">Title:</td>
			  <td><input name="title" type="text" class="input_standard" id="title" value="<?php echo $row_arbitraryfield['title']; ?>" size="32" maxlength="255" /></td>
			</tr>
            <tr valign="baseline">
			  <td align="right" valign="top">Default Value:</td>
			  <td><textarea name="value" cols="50" rows="10" class="input_standard" id="value"><?php echo $row_arbitraryfield['value']; ?></textarea>
		    </tr>
            <tr valign="baseline">
			  <td align="right" valign="top">User Hint:</td>
			  <td><textarea cols="50" rows="10" id="hint" name="hint" class="input_standard"><?php echo $row_arbitraryfield['hint']; ?></textarea></td>
			</tr>
            <tr valign="baseline">
			  <td align="right" valign="top">Type:</td>
			  <td><select name="fieldtype" id="fieldtype" class="input_standard">
              	<option value="text" <?php if ($row_arbitraryfield['fieldtype'] == "text") { echo "selected=\"selected\""; } ?>>Text</option>
              	<option value="textarea" <?php if ($row_arbitraryfield['fieldtype'] == "textarea") { echo "selected=\"selected\""; } ?>>Text Area</option>
                <option value="checkbox"<?php if ($row_arbitraryfield['fieldtype'] == "checkbox") { echo "selected=\"selected\""; } ?>>Checkbox (default value is ignored)</option>
              </select></td>
			</tr>
			<tr valign="baseline">
			  <td align="right">&nbsp;</td>
			  <td><input type="submit" class="input_standard" value="Update field" /> <input type="button" name="delete" class="input_standard" value="Delete field" onClick="if (confirm('Are you sure you want to remove the arbitrary field?')) { document.getElementById('frm_delete_field<?php echo $_GET['field']; ?>').submit(); } else { return false; }"></td>
			</tr>
		  </table>
					<input type="hidden" name="MM_update" value="frm_update_field" />
                    <input type="hidden" name="template" value="<?php echo $_GET['template']; ?>" />
                    <input type="hidden" name="field" value="<?php echo $_GET['field']; ?>" />
					<input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
            </form>
            
            <form action="" method="post" target="_self" name="frm_delete_field<?php echo $_GET['field']; ?>" id="frm_delete_field<?php echo $_GET['field']; ?>">
                            <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                            <input type="hidden" name="field" value="<?php echo $_GET['field']; ?>" />
                            <input type="hidden" name="template" value="<?php echo $_GET['template']; ?>" />
                            <input type="hidden" name="MM_delete" value="frm_delete_arbitrary" />
                      </form>
            
            <?php 
            }
            
			elseif ($_GET['template'] != "") { 

				if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
				mysql_select_db($database_subman, $subman);
				$query_template = "SELECT * FROM servicetemplate WHERE container = ".$_GET['container']." AND id = ".$_GET['template']." ORDER BY servicetemplate.name";
				$template = mysql_query($query_template, $subman) or die(mysql_error());
				$row_template = mysql_fetch_assoc($template);
				$totalRows_template = mysql_num_rows($template);
				
				mysql_select_db($database_subman, $subman);
				$query_devicetypes = "SELECT * FROM devicetypes ORDER BY devicetypes.name";
				$devicetypes = mysql_query($query_devicetypes, $subman) or die(mysql_error());
				$row_devicetypes = mysql_fetch_assoc($devicetypes);
				$totalRows_devicetypes = mysql_num_rows($devicetypes);
				
				mysql_select_db($database_subman, $subman);
				$query_cardtypes = "SELECT * FROM cardtypes ORDER BY cardtypes.name";
				$cardtypes = mysql_query($query_cardtypes, $subman) or die(mysql_error());
				$row_cardtypes = mysql_fetch_assoc($cardtypes);
				$totalRows_cardtypes = mysql_num_rows($cardtypes);
				
				mysql_select_db($database_subman, $subman);
				$query_devicegroups = "SELECT * FROM portgroups WHERE container = '".$_GET['container']."' ORDER BY portgroups.name";
				$devicegroups = mysql_query($query_devicegroups, $subman) or die(mysql_error());
				$row_devicegroups = mysql_fetch_assoc($devicegroups);
				$totalRows_devicegroups = mysql_num_rows($devicegroups);
				
				mysql_select_db($database_subman, $subman);
				$query_provide_devices = "SELECT portsdevices.*, portgroups.name AS devicegroupname FROM portsdevices LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portgroups.container = ".$_GET['container']." ORDER BY portgroups.name, portsdevices.name";
				$provide_devices = mysql_query($query_provide_devices, $subman) or die(mysql_error());
				$row_provide_devices = mysql_fetch_assoc($provide_devices);
				$totalRows_provide_devices = mysql_num_rows($provide_devices);
				?>
				
				Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service" title="Browse service templates">Service Templates</a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_template['name']; ?></strong>
				
				<p>
				<a href="#_template">Template Parameters</a><br />
				<a href="#_fields">Arbitrary Fields</a><br />
				<a href="#_service">Service Deployment and Recovery</a><br />
				<a href="#_routes">Routes Deployment and Recovery</a><br />
				<a href="#_secondarynets">Secondary Networks Deployment and Recovery</a><br />
				<a href="#_scripts">Scripts</a><br />
				<a href="#_linkedtemplate">Linked Template</a><br />
				<a href="#_keywords">Template Keywords</a><br />
				</p>
				
                <h2 id="_template">Template Parameters</h2>
                
                <form action="" method="post" name="frm_update_servicetemplate" target="_self" onSubmit="if (document.frm_update_servicetemplate.netsize_default_radio[1].checked) { if ((document.getElementById('netsize_default_maskrange_min_ipv4').value < 8) || (document.getElementById('netsize_default_maskrange_min_ipv4').value > 32) || (document.getElementById('netsize_default_maskrange_max_ipv4').value < 8) || (document.getElementById('netsize_default_maskrange_max_ipv4').value > 32) || (document.getElementById('netsize_default_maskrange_min_ipv6').value < 8) || (document.getElementById('netsize_default_maskrange_min_ipv6').value > 128) || (document.getElementById('netsize_default_maskrange_max_ipv6').value < 8) || (document.getElementById('netsize_default_maskrange_max_ipv6').value > 128)) { alert('There was an error with the selected mask range.  Masks cannot be out of range for the relevant protocol, and the minimum mask cannot be greater than the maximum.'); return false; } }; MM_validateForm('descr','','R','value_circuit','','R');return document.MM_returnValue">
                
                <h5>Name</h5>
                <input type="text" name="descr" size="50" maxlength="255" class="input_standard" value="<?php echo $row_template['name']; ?>">
                <h5>Active?</h5>
                
                Yes: <input type="radio" value="0" <?php if (!(strcmp($row_template['disabled'],0))) {echo "checked=\"checked\"";} ?> name="disabled" /> No: <input type="radio" value="1" <?php if (!(strcmp($row_template['disabled'],1))) {echo "checked=\"checked\"";} ?> name="disabled" />
                <br />&nbsp;
                <table border="0" width="100%">
                  <tr>
                    <td><strong>Field</strong></td>
                    <td><strong>Template Default</strong></td>
                    <td><strong>Editable</strong></td>
                    <td><strong>Prompt/Provisioning Notes</strong></td>
                  </tr>
                  
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Node A</strong></td>
                        <?php 
						mysql_select_db($database_subman, $subman);
						if ($row_template['provide_node_a_default'] == 'device' && $row_template['provide_node_a_default_id']) {
								$query_templateitem = "SELECT * FROM portsdevices WHERE id = ".$row_template['provide_node_a_default_id']."";
							}
							elseif ($row_template['provide_node_a_default'] == 'device') {
								$query_templateitem = "SELECT * FROM portsdevices WHERE id = ".$row_template['provide_node_a']."";
							}
							elseif ($row_template['provide_node_a_default'] == 'devicetype') {
								$query_templateitem = "SELECT * FROM portsdevices WHERE devicetype = ".$row_template['provide_node_a_default_id']."";
							}
							elseif ($row_template['provide_node_a_default'] == 'devicegroup') {
								$query_templateitem = "SELECT * FROM portsdevices WHERE devicegroup = ".$row_template['provide_node_a_default_id']."";
							}
						$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
						$row_templateitem = mysql_fetch_assoc($templateitem);
						$totalRows_templateitem = mysql_num_rows($templateitem);
						?>
                        <td><input type="radio" name="nodea_default_radio" id="nodea_default_radio_device" value="device" <?php if ($row_template['provide_node_a_default'] == 'device') { echo "checked=\"checked\""; } ?>>
                        <select name="nodea_default_device" class="input_standard">
                  <?php
                  	$dgroup = '';
do {  
	if ($dgroup != '' && $dgroup != $row_provide_devices['devicegroupname']) { ?>
		</optgroup>
		<optgroup label="<?php echo $row_provide_devices['devicegroupname']; ?>">
	<?php }
	elseif ($dgroup != $row_provide_devices['devicegroupname']) { ?>
		<optgroup label="<?php echo $row_provide_devices['devicegroupname']; ?>">
	<?php }
	
?>
                  <option value="<?php echo $row_provide_devices['id']?>" <?php if ($row_provide_devices['id'] == $row_templateitem['id']) { echo "selected='selected'"; } ?>><?php echo $row_provide_devices['name']?></option>
                  <?php
	$dgroup = $row_provide_devices['devicegroupname'];
	
} while ($row_provide_devices = mysql_fetch_assoc($provide_devices));
  $rows = mysql_num_rows($provide_devices);
  if($rows > 0) {
      mysql_data_seek($provide_devices, 0);
	  $row_provide_devices = mysql_fetch_assoc($provide_devices);
  }
?>
					</optgroup>
                </select><br />
                        	<strong>OR</strong> Device Type: <br /><input type="radio" name="nodea_default_radio" id="nodea_default_radio_devicetype" value="devicetype" <?php if ($row_template['provide_node_a_default'] == 'devicetype') { echo "checked=\"checked\""; } ?>>
                        	<select name="nodea_default_devicetype" class="input_standard">
                        		<?php do { ?>
                        			<option value="<?php echo $row_devicetypes['id']; ?>" <?php if ($row_devicetypes['id'] == $row_templateitem['devicetype']) { echo "selected=\"selected\""; } ?>><?php echo $row_devicetypes['name']; ?></option>
                        		<?php } while ($row_devicetypes = mysql_fetch_assoc($devicetypes)); ?>
                        	</select>
                        	
                        	<br />
                        	<strong>OR</strong> Device Group: <br /><input type="radio" name="nodea_default_radio" id="nodea_default_radio_devicegroup" value="devicegroup" <?php if ($row_template['provide_node_a_default'] == 'devicegroup') { echo "checked=\"checked\""; } ?>>
                        	<select name="nodea_default_devicegroup" class="input_standard">
                        		<?php do { ?>
                        			<option value="<?php echo $row_devicegroups['id']; ?>" <?php if ($row_devicegroups['id'] == $row_templateitem['devicegroup']) { echo "selected=\"selected\""; } ?>><?php echo $row_devicegroups['name']; ?></option>
                        		<?php } while ($row_devicegroups = mysql_fetch_assoc($devicegroups)); ?>
                        	</select>
                        </td>
                        <td><input <?php if (!(strcmp($row_template['provide_node_a_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="node_a" class="input_standard" value="1" /></td>
                        <td rowspan="7" align="center"><textarea name="step1prompt" id="step1prompt" cols="50" rows="5" class="input_standard"><?php echo $row_template['step1prompt']; ?></textarea></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>Node B</strong></td>
                        <?php
							
							mysql_select_db($database_subman, $subman);
							if ($row_template['provide_node_b_default'] == 'device' && $row_template['provide_node_b_default_id']) {
								$query_templateitem = "SELECT * FROM portsdevices WHERE id = '".$row_template['provide_node_b_default_id']."'";
							}
							elseif ($row_template['provide_node_b_default'] == 'device') {
								$query_templateitem = "SELECT * FROM portsdevices WHERE id = '".$row_template['provide_node_b']."'";
							}
							elseif ($row_template['provide_node_b_default'] == 'devicetype') {
								$query_templateitem = "SELECT * FROM portsdevices WHERE devicetype = ".$row_template['provide_node_b_default_id']."";
							}
							elseif ($row_template['provide_node_b_default'] == 'devicegroup') {
								$query_templateitem = "SELECT * FROM portsdevices WHERE devicegroup = ".$row_template['provide_node_b_default_id']."";
							}
							$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
							$row_templateitem = mysql_fetch_assoc($templateitem);
							$totalRows_templateitem = mysql_num_rows($templateitem);
						
							mysql_select_db($database_subman, $subman);
							$query_devicetypes = "SELECT * FROM devicetypes ORDER BY devicetypes.name";
							$devicetypes = mysql_query($query_devicetypes, $subman) or die(mysql_error());
							$row_devicetypes = mysql_fetch_assoc($devicetypes);
							$totalRows_devicetypes = mysql_num_rows($devicetypes);
				
							mysql_select_db($database_subman, $subman);
							$query_devicegroups = "SELECT * FROM portgroups WHERE container = '".$_GET['container']."' ORDER BY portgroups.name";
							$devicegroups = mysql_query($query_devicegroups, $subman) or die(mysql_error());
							$row_devicegroups = mysql_fetch_assoc($devicegroups);
							$totalRows_devicegroups = mysql_num_rows($devicegroups);
				
							mysql_select_db($database_subman, $subman);
							$query_provide_devices = "SELECT portsdevices.*, portgroups.name AS devicegroupname FROM portsdevices LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portgroups.container = ".$_GET['container']." ORDER BY portgroups.name, portsdevices.name";
							$provide_devices = mysql_query($query_provide_devices, $subman) or die(mysql_error());
							$row_provide_devices = mysql_fetch_assoc($provide_devices);
							$totalRows_provide_devices = mysql_num_rows($provide_devices);
				
						?>
                        <td><input type="radio" name="nodeb_default_radio" id="nodeb_default_radio_device" value="device" <?php if ($row_template['provide_node_b_default'] == 'device') { echo "checked=\"checked\""; } ?>>
                        <select name="nodeb_default_device" class="input_standard">
                        	<option value="">None</option>
                  <?php
                  	$dgroup = '';
do {  
	if ($dgroup != '' && $dgroup != $row_provide_devices['devicegroupname']) { ?>
		</optgroup>
		<optgroup label="<?php echo $row_provide_devices['devicegroupname']; ?>">
	<?php }
	elseif ($dgroup != $row_provide_devices['devicegroupname']) { ?>
		<optgroup label="<?php echo $row_provide_devices['devicegroupname']; ?>">
	<?php }
	
?>
                  <option value="<?php echo $row_provide_devices['id']?>" <?php if ($row_provide_devices['id'] == $row_templateitem['id']) { echo "selected='selected'"; } ?>><?php echo $row_provide_devices['name']?></option>
                  <?php
	$dgroup = $row_provide_devices['devicegroupname'];
	
} while ($row_provide_devices = mysql_fetch_assoc($provide_devices));
  $rows = mysql_num_rows($provide_devices);
  if($rows > 0) {
      mysql_data_seek($provide_devices, 0);
	  $row_provide_devices = mysql_fetch_assoc($provide_devices);
  }
?>
					</optgroup>
                </select><br />
                        	<strong>OR</strong> Device Type: <br /><input type="radio" name="nodeb_default_radio" id="nodeb_default_radio_devicetype" value="devicetype" <?php if ($row_template['provide_node_b_default'] == 'devicetype') { echo "checked=\"checked\""; } ?>>
                        	<select name="nodeb_default_devicetype" class="input_standard">
                        		<?php do { ?>
                        			<option value="<?php echo $row_devicetypes['id']; ?>" <?php if ($row_devicetypes['id'] == $row_templateitem['devicetype']) { echo "selected=\"selected\""; } ?>><?php echo $row_devicetypes['name']; ?></option>
                        		<?php } while ($row_devicetypes = mysql_fetch_assoc($devicetypes)); ?>
                        	</select>
                        	
                        	<br />
                        	<strong>OR</strong> Device Group: <br /><input type="radio" name="nodeb_default_radio" id="nodeb_default_radio_devicegroup" value="devicegroup" <?php if ($row_template['provide_node_b_default'] == 'devicegroup') { echo "checked=\"checked\""; } ?>>
                        	<select name="nodeb_default_devicegroup" class="input_standard">
                        		<?php do { ?>
                        			<option value="<?php echo $row_devicegroups['id']; ?>" <?php if ($row_devicegroups['id'] == $row_templateitem['devicegroup']) { echo "selected=\"selected\""; } ?>><?php echo $row_devicegroups['name']; ?></option>
                        		<?php } while ($row_devicegroups = mysql_fetch_assoc($devicegroups)); ?>
                        	</select>
                        </td>
                        <td><input <?php if (!(strcmp($row_template['provide_node_b_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="node_b" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Port Layer</strong></td>
                        <td>Layer <?php echo $row_template['provide_layer']; ?></td>
                        <td><input <?php if (!(strcmp($row_template['provide_layer_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="layer" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>Node A Logical Interface Type</strong></td>
                        <td><?php echo $row_template['provide_logical_node_a']; ?></td>
                        <td><input <?php if (!(strcmp($row_template['provide_logical_node_a_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="logical_node_a" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Node B Logical Interface Type</strong></td>
                        <td><?php echo $row_template['provide_logical_node_b']; ?></td>
                        <td><input <?php if (!(strcmp($row_template['provide_logical_node_b_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="logical_node_b" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>Circuit Number</strong></td>
                        <td><input type="text" size="32" maxlength="255" name="value_circuit" class="input_standard" value="<?php echo $row_template['provide_cct']; ?>"></td>
                        <td><input <?php if (!(strcmp($row_template['provide_cct_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="circuit" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Customer</strong></td>
                        <?php 
						mysql_select_db($database_subman, $subman);
						$query_templateitem = "SELECT name FROM customer WHERE id = ".$row_template['provide_customer']."";
						$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
						$row_templateitem = mysql_fetch_assoc($templateitem);
						$totalRows_templateitem = mysql_num_rows($templateitem);
						?>
                        <td><?php echo $row_templateitem['name']; ?></td>
                        <td><input <?php if (!(strcmp($row_template['provide_customer_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="customer" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>VPN</strong></td>
                         <?php
						if ($row_template['provide_vpn']) {
							
							if (preg_match('/^auto_+/',$row_template['provide_vpn']) || preg_match('/^new_+/',$row_template['provide_vpn'])) {
								
								list($junk,$provider) = split('_',$row_template['provide_vpn']);
								
								mysql_select_db($database_subman, $subman);
								$query_templateitem = "SELECT provider.name AS provider FROM provider WHERE provider.id = ".$provider."";
								$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
								$row_templateitem = mysql_fetch_assoc($templateitem);
								$totalRows_templateitem = mysql_num_rows($templateitem);
								
								$row_templateitem['name'] = strtoupper($junk).' ['.$row_templateitem['provider'].']';
								
							} else {
								
								mysql_select_db($database_subman, $subman);
								$query_templateitem = "SELECT name FROM vpn WHERE id = ".$row_template['provide_vpn']."";
								$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
								$row_templateitem = mysql_fetch_assoc($templateitem);
								$totalRows_templateitem = mysql_num_rows($templateitem);
								
							}
						
						?>
                        <td><?php echo $row_templateitem['name']; ?></td>
                        
                        <?php } else { ?>
                        <td>---</td>
                        <?php } ?>
                        <td><input <?php if (!(strcmp($row_template['provide_vpn_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="vpn" class="input_standard" value="1"  /></td>
                        <td rowspan="3" align="center"><textarea name="step2prompt" id="step2prompt" cols="50" rows="5" class="input_standard"><?php echo $row_template['step2prompt']; ?></textarea></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Node A VLAN</strong></td>
                        <?php if ($row_template['provide_vlan_node_a']) {
						
							if (preg_match('/^auto_+/',$row_template['provide_vlan_node_a']) || preg_match('/^new_+/',$row_template['provide_vlan_node_a']) || preg_match('/^manual_+/',$row_template['provide_vlan_node_a'])) {
								
								list($junk,$pool) = split('_',$row_template['provide_vlan_node_a']);
								
								mysql_select_db($database_subman, $subman);
								$query_templateitem = "SELECT vlanpool.name FROM vlanpool WHERE vlanpool.id = ".$pool."";
								$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
								$row_templateitem = mysql_fetch_assoc($templateitem);
								$totalRows_templateitem = mysql_num_rows($templateitem);
								
								$row_templateitem['name'] = strtoupper($junk).' ['.$row_templateitem['name'].']';
								
							} else {
								
								mysql_select_db($database_subman, $subman);
								$query_templateitem = "SELECT name, number FROM vlan WHERE id = ".$row_template['provide_vlan_node_a']."";
								$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
								$row_templateitem = mysql_fetch_assoc($templateitem);
								$totalRows_templateitem = mysql_num_rows($templateitem);
								
							} ?>
                        <td><?php echo $row_templateitem['name']; ?> [<?php echo $row_templateitem['number']; ?>]</td>
                        <?php } else { ?>
                        <td>---</td>
                        <?php } ?>
                        <td><input <?php if (!(strcmp($row_template['provide_vlan_node_a_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="vlan_node_a" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>Node B VLAN</strong></td>
                        <?php if ($row_template['provide_vlan_node_b']) {
						
							if (preg_match('/^auto_+/',$row_template['provide_vlan_node_b']) || preg_match('/^new_+/',$row_template['provide_vlan_node_b']) || preg_match('/^manual_+/',$row_template['provide_vlan_node_b']) || preg_match('/same/',$row_template['provide_vlan_node_b'])) {
								
								if (preg_match('/same/',$row_template['provide_vlan_node_b'])) {
									$row_templateitem['name'] = "Same as Node A";
								}
								else {
									
									list($junk,$pool) = split('_',$row_template['provide_vlan_node_b']);
									
									mysql_select_db($database_subman, $subman);
									$query_templateitem = "SELECT vlanpool.name FROM vlanpool WHERE vlanpool.id = ".$pool."";
									$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
									$row_templateitem = mysql_fetch_assoc($templateitem);
									$totalRows_templateitem = mysql_num_rows($templateitem);
									
									$row_templateitem['name'] = strtoupper($junk).' ['.$row_templateitem['name'].']';
									
								}
								
							} else {
								
								mysql_select_db($database_subman, $subman);
								$query_templateitem = "SELECT name, number FROM vlan WHERE id = ".$row_template['provide_vlan_node_b']."";
								$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
								$row_templateitem = mysql_fetch_assoc($templateitem);
								$totalRows_templateitem = mysql_num_rows($templateitem);
								
							} ?>
                        <td><?php echo $row_templateitem['name']; ?> [<?php echo $row_templateitem['number']; ?>]</td>
                        <?php } else { ?>
                        <td>---</td>
                        <?php } ?>
                        <td><input <?php if (!(strcmp($row_template['provide_vlan_node_b_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="vlan_node_b" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Pseudowire Pool</strong></td>
                        <?php
						if ($row_template['provide_xconnectpool']) {
							
							mysql_select_db($database_subman, $subman);
							$query_templateitem = "SELECT descr FROM xconnectpool WHERE id = ".$row_template['provide_xconnectpool']."";
							$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
							$row_templateitem = mysql_fetch_assoc($templateitem);
							$totalRows_templateitem = mysql_num_rows($templateitem);
						
						?>
                        <td><?php echo $row_templateitem['descr']; ?></td>
                        <?php } else { ?>
                        <td>---</td>
                        <?php } ?>
                        <td><input <?php if (!(strcmp($row_template['provide_xconnectpool_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="xconnectpool" class="input_standard" value="1"  /></td>
                        <td rowspan="4" align="center" bgcolor="#EAEAEA"><textarea name="step3prompt" id="step3prompt" cols="50" rows="5" class="input_standard"><?php echo $row_template['step3prompt']; ?></textarea></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>Allow Manual Pseudowire Assignment</strong></td>
                        <td><?php if ($row_template['manual_xconnect'] == 1) { echo "YES"; } else { echo "---"; } ?></td>
                        <td><input <?php if (!(strcmp($row_template['manual_xconnect_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="manual_xconnect" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>VRF</strong></td>
                        <?php
						if ($row_template['provide_vrf']) {
							
							mysql_select_db($database_subman, $subman);
							$query_templateitem = "SELECT name FROM vrf WHERE id = ".$row_template['provide_vrf']."";
							$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
							$row_templateitem = mysql_fetch_assoc($templateitem);
							$totalRows_templateitem = mysql_num_rows($templateitem);
						
						?>
                        <td><?php echo $row_templateitem['name']; ?></td>
                        <?php } else { ?>
                        <td>---</td>
                        <?php } ?>
                        <td>---<input type="hidden" name="vrf" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>PE-CE Option</strong></td>
                        <td><?php if ($row_template['pece'] == 1) { echo "YES"; } else { echo "---"; } ?></td>
                        <td><input <?php if (!(strcmp($row_template['provide_pece_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="pece" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Network Group</strong></td>
                        <?php
						if ($row_template['provide_netgroup']) {
							
							mysql_select_db($database_subman, $subman);
							$query_templateitem = "SELECT name FROM networkgroup WHERE id = ".$row_template['provide_netgroup']."";
							$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
							$row_templateitem = mysql_fetch_assoc($templateitem);
							$totalRows_templateitem = mysql_num_rows($templateitem);
						
						?>
                        <td><?php echo $row_templateitem['name']; ?></td>
                        <?php } else { ?>
                        <td>---</td>
                        <?php } ?>
                        <td><input <?php if (!(strcmp($row_template['provide_netgroup_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="netgroup" class="input_standard" value="1"  /></td>
                        <td rowspan="2" align="center" bgcolor="#F5F5F5"><textarea name="step4prompt" id="step4prompt" cols="50" rows="5" class="input_standard"><?php echo $row_template['step4prompt']; ?></textarea></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>Network Size</strong></td>
                        <td>
                        <input type="radio" name="netsize_default_radio" id="netsize_default_radio_mask" value="mask" <?php if ($row_template['provide_netsize_default'] == 'mask') { echo "checked=\"checked\""; } ?>>
                        <input type="hidden" name="netsize_default_mask" value="<?php echo $row_template['provide_netsize']; ?>">
                        <?php
						if ($row_template['provide_netsize']) {	?>
                        /<?php echo $row_template['provide_netsize']; ?>
                        <?php } else { ?>
                        ---<?php } ?>
                         <br />
                        	<strong>OR</strong> Mask Range: <br />
                        <input type="radio" name="netsize_default_radio" id="netsize_default_radio_maskrange" value="maskrange" <?php if ($row_template['provide_netsize_default'] == 'maskrange') { echo "checked=\"checked\""; } ?>>
                        <br/>IPv4: /<input type="text" size="10" maxlength="2" name="netsize_default_maskrange_min_ipv4" id="netsize_default_maskrange_min_ipv4" class="input_standard" value="<?php echo $row_template['provide_netsize_default_min_ipv4']; ?>"> - /<input type="text" size="10" maxlength="2" name="netsize_default_maskrange_max_ipv4" id="netsize_default_maskrange_max_ipv4" class="input_standard" value="<?php echo $row_template['provide_netsize_default_max_ipv4']; ?>">
                        <br/>IPv6: /<input type="text" size="10" maxlength="3" name="netsize_default_maskrange_min_ipv6" id="netsize_default_maskrange_min_ipv6" class="input_standard" value="<?php echo $row_template['provide_netsize_default_min_ipv6']; ?>"> - /<input type="text" size="10" maxlength="3" name="netsize_default_maskrange_max_ipv6" id="netsize_default_maskrange_max_ipv6" class="input_standard" value="<?php echo $row_template['provide_netsize_default_max_ipv6']; ?>">
                        </td>
                        <td><input <?php if (!(strcmp($row_template['provide_netsize_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="netsize" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Parent Network</strong></td>
                        <?php
						if ($row_template['provide_parent']) {
							
							mysql_select_db($database_subman, $subman);
							$query_templateitem = "SELECT network AS _network, mask, v6mask FROM networks WHERE id = ".$row_template['provide_parent']."";
							$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
							$row_templateitem = mysql_fetch_assoc($templateitem);
							$totalRows_templateitem = mysql_num_rows($templateitem);
							
							if ($row_templateitem['v6mask'] == "") {
								$parentnet = long2ip($row_templateitem['_network']).get_slash($row_templateitem['mask']);
							}
							else {
								$parentnet = Net_IPv6::Compress(long2ipv6($row_templateitem['_network']))."/".$row_templateitem['v6mask'];
							}
							
						
						?>
                        <td><?php echo $parentnet; ?></td>
                        <?php } else { ?>
                        <td>---</td>
                        <?php } ?>
                        <td><input <?php if (!(strcmp($row_template['provide_parent_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="parent" class="input_standard" value="1"  /></td>
                        <td align="center" rowspan="2" bgcolor="#EAEAEA"><textarea name="step5prompt" id="step5prompt" cols="50" rows="5" class="input_standard"><?php echo $row_template['step5prompt']; ?></textarea></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>Add to Existing Network (Do Not Subnet)</strong></td>
                        <td><?php if ($row_template['do_not_subnet'] == 1) { echo "Yes"; } else { echo "No"; } ?></td>
                        <td><input <?php if (!(strcmp($row_template['do_not_subnet_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="do_not_subnet" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Network Selection</strong></td>
                        <td>---</td>
                        <td>---</td>
                        <td align="center" rowspan="2" bgcolor="#F5F5F5"><textarea name="step6prompt" id="step6prompt" cols="50" rows="5" class="input_standard"><?php echo $row_template['step6prompt']; ?></textarea></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>Allow Manual Addressing</strong></td>
                        <td><?php if ($row_template['manual_addressing'] == 1) { echo "Yes"; } else { echo "No"; } ?></td>
                        <td><input <?php if (!(strcmp($row_template['manual_addressing_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="manual_addressing" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Node A Line Card</strong></td>
                        <?php
						if ($row_template['provide_card_node_a']) {
							
							mysql_select_db($database_subman, $subman);
							if ($row_template['provide_card_node_a_default'] == 'card' && $row_template['provide_card_node_a_default_id']) {
								$query_templateitem = "SELECT cards.rack, cards.module, cards.slot, cardtypes.name, cards.cardtype FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = ".$row_template['provide_card_node_a_default_id']."";
							}
							elseif ($row_template['provide_card_node_a_default'] == 'card') {
								$query_templateitem = "SELECT cards.rack, cards.module, cards.slot, cardtypes.name, cards.cardtype FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = ".$row_template['provide_card_node_a']."";
							}
							elseif ($row_template['provide_card_node_a_default'] == 'cardtype') {
								$query_templateitem = "SELECT cards.rack, cards.module, cards.slot, cardtypes.name, cards.cardtype FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.cardtype = ".$row_template['provide_card_node_a_default_id']."";
							}
							$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
							$row_templateitem = mysql_fetch_assoc($templateitem);
							$totalRows_templateitem = mysql_num_rows($templateitem);
						
							mysql_select_db($database_subman, $subman);
							$query_cardtypes = "SELECT * FROM cardtypes ORDER BY cardtypes.name";
							$cardtypes = mysql_query($query_cardtypes, $subman) or die(mysql_error());
							$row_cardtypes = mysql_fetch_assoc($cardtypes);
							$totalRows_cardtypes = mysql_num_rows($cardtypes);
				
						?>
                        <td><input type="radio" name="nodea_card_default_radio" id="nodea_card_default_radio_card" value="card" <?php if ($row_template['provide_card_node_a_default'] == 'card') { echo "checked=\"checked\""; } ?>>
                        <input type="hidden" name="nodea_card_default_card" value="<?php echo $row_template['provide_card_node_a']; ?>">
                        <?php echo $row_templateitem['name']; ?> <?php if (!(isset($row_templateitem['rack'])) && !(isset($row_templateitem['module'])) && !(isset($row_templateitem['slot']))) { echo "Virtual"; } else { if (isset($row_templateitem['rack'])) { echo $row_templateitem['rack']."/"; } if (isset($row_templateitem['module'])) { echo $row_templateitem['module'].'/'; } if (isset($row_templateitem['slot'])) { echo $row_templateitem['slot']; } } ?>
                        <br />
                        	<strong>OR</strong> Card Type: <br />
                        <input type="radio" name="nodea_card_default_radio" id="nodea_card_default_radio_cardtype" value="cardtype" <?php if ($row_template['provide_card_node_a_default'] == 'cardtype') { echo "checked=\"checked\""; } ?>>
                        <select name="nodea_card_default_cardtype" class="input_standard">
                        	<?php do { ?>
                        		<option value="<?php echo $row_cardtypes['id']; ?>" <?php if ($row_cardtypes['id'] == $row_templateitem['cardtype']) { echo "selected=\"selected\""; } ?>><?php echo $row_cardtypes['name']; ?></option>
                        	<?php } while ($row_cardtypes = mysql_fetch_assoc($cardtypes)); ?>
                        </select>
                        </td>
                        <?php } else { ?>
                        <td>---</td>
                        <?php } ?>
                        <td><input <?php if (!(strcmp($row_template['provide_card_node_a_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="card_node_a" class="input_standard" value="1"  /></td>
                        <td align="center" rowspan="6" bgcolor="#EAEAEA"><textarea name="step7prompt" id="step7prompt" cols="50" rows="5" class="input_standard"><?php echo $row_template['step7prompt']; ?></textarea></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>Node B Line Card</strong></td>
                    	<td>
                    	<input type="radio" name="nodeb_card_default_radio" id="nodeb_card_default_radio_card" value="card" <?php if ($row_template['provide_card_node_b_default'] == 'card') { echo "checked=\"checked\""; } ?>>
                        <input type="hidden" name="nodeb_card_default_card" value="<?php echo $row_template['provide_card_node_b']; ?>">
                        <?php
                        
                        mysql_select_db($database_subman, $subman);
						$query_cardtypes = "SELECT * FROM cardtypes ORDER BY cardtypes.name";
						$cardtypes = mysql_query($query_cardtypes, $subman) or die(mysql_error());
						$row_cardtypes = mysql_fetch_assoc($cardtypes);
						$totalRows_cardtypes = mysql_num_rows($cardtypes);
							
						if ($row_template['provide_card_node_b']) {
							
							mysql_select_db($database_subman, $subman);
							if ($row_template['provide_card_node_b_default'] == 'card' && $row_template['provide_card_node_b_default_id']) {
								$query_templateitem = "SELECT cards.rack, cards.module, cards.slot, cardtypes.name, cards.cardtype FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = ".$row_template['provide_card_node_b_default_id']."";
							}
							elseif ($row_template['provide_card_node_a_default'] == 'card') {
								$query_templateitem = "SELECT cards.rack, cards.module, cards.slot, cardtypes.name, cards.cardtype FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = ".$row_template['provide_card_node_b']."";
							}
							elseif ($row_template['provide_card_node_a_default'] == 'cardtype') {
								$query_templateitem = "SELECT cards.rack, cards.module, cards.slot, cardtypes.name, cards.cardtype FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.cardtype = ".$row_template['provide_card_node_b_default_id']."";
							}
							$templateitem = mysql_query($query_templateitem, $subman) or die(mysql_error());
							$row_templateitem = mysql_fetch_assoc($templateitem);
							$totalRows_templateitem = mysql_num_rows($templateitem);
						
						?>
                        
                        <?php echo $row_templateitem['name']; ?> <?php if (!(isset($row_templateitem['rack'])) && !(isset($row_templateitem['module'])) && !(isset($row_templateitem['slot']))) { echo "Virtual"; } else { if (isset($row_templateitem['rack'])) { echo $row_templateitem['rack']."/"; } if (isset($row_templateitem['module'])) { echo $row_templateitem['module'].'/'; } if (isset($row_templateitem['slot'])) { echo $row_templateitem['slot']; } } ?>
                        <?php } else { ?>
                        ---
                        <?php } ?>
                        <br />
                        	<strong>OR</strong> Card Type: <br />
                        <input type="radio" name="nodeb_card_default_radio" id="nodeb_card_default_radio_cardtype" value="cardtype" <?php if ($row_template['provide_card_node_b_default'] == 'cardtype') { echo "checked=\"checked\""; } ?>>
                        <select name="nodeb_card_default_cardtype" class="input_standard">
                        	<?php do { ?>
                        		<option value="<?php echo $row_cardtypes['id']; ?>" <?php if ($row_cardtypes['id'] == $row_templateitem['cardtype']) { echo "selected=\"selected\""; } ?>><?php echo $row_cardtypes['name']; ?></option>
                        	<?php } while ($row_cardtypes = mysql_fetch_assoc($cardtypes)); ?>
                        </select>
                        </td>
                        <td><input <?php if (!(strcmp($row_template['provide_card_node_b_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="card_node_b" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Node A Sub-Interface Auto</strong></td>
                        <td>N/A</td>
                        <td><input <?php if (!(strcmp($row_template['provide_subint_node_a_auto'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="subint_auto_a" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>Node B Sub-Interface Auto</strong></td>
                        <td>N/A</td>
                        <td><input <?php if (!(strcmp($row_template['provide_subint_node_b_auto'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="subint_auto_b" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Node A Timeslots</strong></td>
                        <?php
						if ($row_template['provide_timeslots_node_a']) {	?>
                        <td><?php echo $row_template['provide_timeslots_node_a']; ?></td>
                        <?php } else { ?>
                        <td>---</td>
                        <?php } ?>
                        <td><input <?php if (!(strcmp($row_template['provide_timeslots_node_a_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="timeslots_node_a" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#F5F5F5">
                    	<td><strong>Node B Timeslots</strong></td>
                        <?php
						if ($row_template['provide_timeslots_node_b']) {	?>
                        <td><?php echo $row_template['provide_timeslots_node_b']; ?></td>
                        <?php } else { ?>
                        <td>---</td>
                        <?php } ?>
                        <td><input <?php if (!(strcmp($row_template['provide_timeslots_node_b_editable'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="timeslots_node_b" class="input_standard" value="1"  /></td>
                    </tr>
                    <tr bgcolor="#EAEAEA">
                    	<td><strong>Port Selection</strong></td>
                        <td>---</td>
                        <td>---</td>
                        <td align="center" bgcolor="#F5F5F5"><textarea name="step8prompt" id="step8prompt" cols="50" rows="5" class="input_standard"><?php echo $row_template['step8prompt']; ?></textarea></td>
                    </tr>
                    
                </table>
                
                <h2 id="_fields">Arbitrary Fields</h2>
                
                <?php
                mysql_select_db($database_subman, $subman);
				$query_linkedtemplate = "SELECT id FROM servicetemplate WHERE templatelink = '".$_GET['template']."'";
				$linkedtemplate = mysql_query($query_linkedtemplate, $subman) or die(mysql_error());
				$row_linkedtemplate = mysql_fetch_assoc($linkedtemplate);
				$totalRows_linkedtemplate = mysql_num_rows($linkedtemplate);
				
				mysql_select_db($database_subman, $subman);
				$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_GET['template']."' OR servicetemplate = '".$row_linkedtemplate['id']."' ORDER BY arbitraryfields.title";
				$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
				$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
				$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
				?>
                
                <?php if ($totalRows_arbitraryfields > 0) { ?>
                
                	<table width="100%" border="0">
                    <tr>
                    <td><strong>Reference</strong></td>
                    <td><strong>Title</strong></td>
                    <td><strong>Field Type</strong></td>
                    <td><strong>Default Value</strong></td>
                    <td><strong>Field Prompt</strong></td>
                    </tr>
                    <?php 
					$count = 0;
					
					do {
						
						$count++;
						
						if ($count % 2) {
							$bgcolour = "#EAEAEA";
						}
						else {
							$bgcolour = "#F5F5F5";
						}
					?>
                      
                    <tr bgcolor="<?php echo $bgcolour; ?>">
                    
                    	<td><strong>%<?php echo $row_arbitraryfields['id']; ?>%</strong></td>
                    	<td><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service&amp;template=<?php echo $_GET['template']; ?>&amp;field=<?php echo $row_arbitraryfields['id']; ?>" title="Edit field"><?php echo $row_arbitraryfields['title']; ?></a></td>
                        <td><?php echo $row_arbitraryfields['fieldtype']; ?></td>
                        <td><?php echo $row_arbitraryfields['value']; ?></td>
                        <td><?php echo $row_arbitraryfields['hint']; ?></td>
                    </tr>
                    
                    
                    <?php } while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields)); ?>
                    
                    </table>
                
                <?php } else { ?>
                	
                    <p>There are no arbitrary fields for this service template.  Select the 'Add a field' option from the context menu to add a field.</p>

				<?php } ?>
                
                <br />
                <hr />
                <p><em>See the list of keyword variables at the bottom of the page to output dynamically assigned variables in text format or to pass to a script (add scripts using the context menu option).</em></p>
                
                <h2 id="_service">Service Deployment and Recovery</h2>
                <table width="100%" border="0">
                <tr><td><h5>Service Deployment Configuration Template - Node A</h5></td><td><h5>Service Recovery Configuration Template - Node A</h5></td></tr>
                <tr><td><textarea name="config_a" class="input_standard" cols="75" rows="10"><?php echo $row_template['config_a']; ?></textarea></td><td><textarea name="recover_a" class="input_standard" cols="75" rows="10"><?php echo $row_template['recover_a']; ?></textarea></td></tr>
                </table>                
                
                <table width="100%" border="0">
                <tr><td><h5>Service Deployment Configuration Template - Node B</h5></td><td><h5>Service Recovery Configuration Template - Node B</h5></td></tr>
                <tr><td><textarea name="config_b" class="input_standard" cols="75" rows="10"><?php echo $row_template['config_b']; ?></textarea></td><td><textarea name="recover_b" class="input_standard" cols="75" rows="10"><?php echo $row_template['recover_b']; ?></textarea></td></tr>
                </table>
                <br />
                <hr />
                <h2 id="_routes">Routes Deployment and Recovery</h2>
                <table width="100%" border="0">
                <tr><td colspan="2">
                <?php 
						mysql_select_db($database_subman, $subman);
						$query_node_a = "SELECT id,name FROM portsdevices WHERE id = '".$row_template['provide_node_a']."'";
						$node_a = mysql_query($query_node_a, $subman) or die(mysql_error());
						$row_node_a = mysql_fetch_assoc($node_a);
						$totalRows_node_a = mysql_num_rows($node_a);
						
						mysql_select_db($database_subman, $subman);
						$query_node_b = "SELECT id,name FROM portsdevices WHERE id = '".$row_template['provide_node_b']."'";
						$node_b = mysql_query($query_node_b, $subman) or die(mysql_error());
						$row_node_b = mysql_fetch_assoc($node_b);
						$totalRows_node_b = mysql_num_rows($node_b);
						?>
                </td>
                </tr>
                <tr><td>
                <h5>Routes Deployment Configuration Template</h5>
               
                <textarea name="routes" class="input_standard" cols="75" rows="10"><?php echo $row_template['routes']; ?></textarea>
                </td>
                <td>
                <h5>Routes Recovery Configuration Template</h5>
                <textarea name="recover_routes" class="input_standard" cols="75" rows="10"><?php echo $row_template['recover_routes']; ?></textarea>
                </td>
                </tr>
                </table>
                <br />
                <hr />
                <h2 id="_secondarynets">Secondary Networks Deployment and Recovery</h2>
                <table width="100%" border="0">
                <tr><td>
                <h5>Secondary Networks Deployment Configuration Template - Node A</h5>
               
                <textarea name="secondarynets" class="input_standard" cols="75" rows="10"><?php echo $row_template['secondarynets']; ?></textarea>
                </td>
                <td>
                <h5>Secondary Networks Recovery Configuration Template - Node A</h5>
                <textarea name="recover_secondarynets" class="input_standard" cols="75" rows="10"><?php echo $row_template['recover_secondarynets']; ?></textarea>
                </td>
                </tr>
                
                <tr><td>
                <h5>Secondary Networks Deployment Configuration Template - Node B</h5>
               
                <textarea name="secondarynets_b" class="input_standard" cols="75" rows="10"><?php echo $row_template['secondarynets_b']; ?></textarea>
                </td>
                <td>
                <h5>Secondary Networks Recovery Configuration Template - Node B</h5>
                <textarea name="recover_secondarynets_b" class="input_standard" cols="75" rows="10"><?php echo $row_template['recover_secondarynets_b']; ?></textarea>
                </td>
                </tr>
                </table>
				
				<br />
				<hr />
				
                <h2 id="_linkedtemplate">Linked Template</h2>
                
                <p><strong><em>Note: Linked templates cause the user to be notified at the end of this template that the linked template is a requirement.  When a link that uses this template is recovered, the user will also be prompted to recover any config created by the linked template.</em></strong></p>
                
                <?php
				mysql_select_db($database_subman, $subman);
				$query_serviceTemplates = "SELECT * FROM servicetemplate WHERE container = ".$_GET['container']." AND id !='".$row_template['id']."' ORDER BY servicetemplate.name";
				$serviceTemplates = mysql_query($query_serviceTemplates, $subman) or die(mysql_error());
				$row_serviceTemplates = mysql_fetch_assoc($serviceTemplates);
				$totalRows_serviceTemplates = mysql_num_rows($serviceTemplates);
				?>
                
                <select name="templatelink" class="input_standard">
                	<option value="">None</option>
                    <?php do { ?>
                    <option value="<?php echo $row_serviceTemplates['id']; ?>" <?php if (!(strcmp($row_template['templatelink'],$row_serviceTemplates['id']))) {echo "selected=\"selected\"";} ?>><?php echo $row_serviceTemplates['name']; ?></option>
                    <?php } while ($row_serviceTemplates = mysql_fetch_assoc($serviceTemplates)); ?>
                </select>
                
                <br /><br />
                <hr />
                
                <h2 id="_scripts">Scripts</h2>
                
                <?php
				mysql_select_db($database_subman, $subman);
				$query_scripts = "SELECT * FROM scripts WHERE servicetemplate = ".$_GET['template']." ORDER BY scripts.description";
				$scripts = mysql_query($query_scripts, $subman) or die(mysql_error());
				$row_scripts = mysql_fetch_assoc($scripts);
				$totalRows_scripts = mysql_num_rows($scripts);
				?>
                
                <?php if ($totalRows_scripts > 0) { ?>
                
                	<table width="100%" border="0">
                    <tr>
                    <td><strong>Description</strong></td>
                    <td><strong>Path</strong></td>
                    <td><strong>Execution</strong></td>
                    <td><strong>Type</strong></td>
                    <td><strong>Autorun</strong></td>
                    </tr>
                    <?php 
					$count = 0;
					
					do {
						
						$count++;
						
						if ($count % 2) {
							$bgcolour = "#EAEAEA";
						}
						else {
							$bgcolour = "#F5F5F5";
						}
					?>
                    
                    <tr bgcolor="<?php echo $bgcolour; ?>">
                    
                    	<td><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service&amp;template=<?php echo $_GET['template']; ?>&amp;script=<?php echo $row_scripts['id']; ?>" title="Edit script"><?php echo $row_scripts['description']; ?></a></td>
                        <td><?php echo $row_scripts['location']; ?></td>
                        <td><?php echo $row_scripts['scripttype']; ?></td>
                        <td><?php echo $row_scripts['scriptrole']; ?></td>
                        <td><?php if ($row_scripts['autorun'] == 1) { echo "Yes"; } else { echo "No"; } ?></td>
                    </tr>
                    <form action="" method="post" target="_self" name="frm_delete_script<?php echo $row_scripts['id']; ?>" id="frm_delete_script<?php echo $row_scripts['id']; ?>">
                            <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                            <input type="hidden" name="script" value="<?php echo $row_scripts['id']; ?>" />
                            <input type="hidden" name="template" value="<?php echo $_GET['template']; ?>" />
                            <input type="hidden" name="MM_delete" value="frm_delete_script" />
                      </form>
                    
                    <?php } while ($row_scripts = mysql_fetch_assoc($scripts)); ?>
                    
                    </table>
                
                <?php } else { ?>
                	
                    <p>There are no scripts for this service template.  Select the 'Add a script' option from the popup menu to add a script.</p>

				<?php } ?>
				
				<br /><br />                    
                <input type="hidden" name="id" value="<?php echo $_GET['template']; ?>" />
                <input type="hidden" name="MM_update" value="frm_update_servicetemplate" />
                <input name="submit" type="submit" value="Update Template" class="input_standard" onClick="if (confirm('WARNING: If you have changed recovery configuration templates or scripts, the new configuration/script will be used when an existing service/route/network using this template is undeployed.  Are you sure you want to update this template?')) { } else { return false; }" />
                
                </form>
                
                <br />
                <br />
                <hr />
                
                <h2 id="_keywords">Template Keywords</h2>
                
                <p><strong><em>Note: Please see Arbitrary Fields section above for details of any user-defined variables</em></strong></p>
                
                <strong>
                <table width="50%" border="0">
                	<tr>
                    	<td>%nodea%</td>
                        <td>Node A</td>
                    </tr>
                    <tr>
                    	<td>%nodeb%</td>
                        <td>Node B</td>
                    </tr>
                    <tr>
                    	<td>%iftype%</td>
                        <td>Interface type (e.g. GigabitEthernet)</td>
                    </tr>
                    <tr>
                    	<td>%iftype_a%</td>
                        <td>Node A's interface type (e.g. GigabitEthernet)</td>
                    </tr>
                    <tr>
                    	<td>%iftype_b%</td>
                        <td>Node B's interface type (e.g. GigabitEthernet)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber%</td>
                        <td>Interface number (e.g. 1/1.1)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber_nosubint%</td>
                        <td>Interface number without sub-interface number or channel group(e.g. 1/1)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber_a%</td>
                        <td>Node A's interface number (e.g. 1/1.1)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber_a_nosubint%</td>
                        <td>Interface number without sub-interface number or channel group(e.g. 1/1)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber_b%</td>
                        <td>Node B's interface number (e.g. 1/1.1)</td>
                    </tr>
                    <tr>
                    	<td>%ifnumber_b_nosubint%</td>
                        <td>Interface number without sub-interface number or channel group(e.g. 1/1)</td>
                    </tr>
                    <tr>
                    	<td>%customername%</td>
                        <td>Customer name</td>
                    </tr>
                    <tr>
                    	<td>%customername_trimmed%</td>
                        <td>Customer name with whitespace removed</td>
                    </tr>
                    <tr>
                    	<td>%circuitnumber%</td>
                        <td>Circuit number</td>
                    </tr>
                    <tr>
                    	<td>%circuitnumber_trimmed%</td>
                        <td>Circuit number with whitespace removed</td>
                    </tr>
                    <tr>
                    	<td>%ipversion_cisco%</td>
                        <td>Outputs 'ip' for IPv4 and 'ipv6' for IPv6</td>
                    </tr>
                    <tr>
                    	<td>%ipversion%</td>
                        <td>Outputs 'IPv4' for IPv4 and 'IPv6' for IPv6</td>
                    </tr>
                    <tr>
                    	<td>%ipaddress%</td>
                        <td>The node's interface IP address</td>
                    </tr>
                    <tr>
                    	<td>%ipaddress_a%</td>
                        <td>Node A's interface IP address</td>
                    </tr>
                    <tr>
                    	<td>%ipaddress_b%</td>
                        <td>Node B's interface IP address</td>
                    </tr>
                    <tr>
                    	<td>%ipaddress_opposite%</td>
                        <td>The other node's interface IP address</td>
                    </tr>
                    <tr>
                    	<td>%addressmask_cisco%</td>
                        <td>Outputs [address]/[mask] for IPv6 and [address] [dotted mask] for IPv4 (i.e. Cisco's interface addressing scheme)</td>
                    </tr>
                    <tr>
                    	<td>%secondary_cisco%</td>
                        <td>Outputs the keyword 'secondary' for v4 addresses and nothing for v6 addresses (used at the end of the IP address configuration statement on Cisco routers)</td>
                    </tr>
                    <tr>
                    	<td>%netmask%</td>
                        <td>The node's interface subnet mask in dotted decimal format</td>
                    </tr>
                    <tr>
                    	<td>%netmask_slash%</td>
                        <td>The node's interface subnet mask in slash format</td>
                    </tr>
                    <tr>
                    	<td>%vlan%</td>
                        <td>Port VLAN/dot1q tag</td>
                    </tr>
                    <tr>
                    	<td>%vpn%</td>
                        <td>VPN name</td>
                    </tr>
                    <tr>
                    	<td>%vrf%</td>
                        <td>VRF name</td>
                    </tr>
                    <tr>
                    	<td>%xconnect%</td>
                        <td>Pseudowire ID</td>
                    </tr>
                    <tr>
                    	<td>%loopback%</td>
                        <td>The node's router loopback</td>
                    </tr>
                    <tr>
                    	<td>%loopback_a%</td>
                        <td>Node A's router loopback</td>
                    </tr>
                    <tr>
                    	<td>%loopback_b%</td>
                        <td>Node B's router loopback</td>
                    </tr>
                    <tr>
                    	<td>%loopback_opposite%</td>
                        <td>The other node's router loopback</td>
                    </tr>
                    <tr>
                    	<td>%first_timeslot%</td>
                        <td>The first timeslot used (TDM only)</td>
                    </tr>
                    <tr>
                    	<td>%last_timeslot%</td>
                        <td>The last timeslot used (TDM only)</td>
                    </tr>
                    <tr>
                    	<td>%network%</td>
                        <td>Route prefix (routes configuration only)</td>
                    </tr>
                    <tr>
                    	<td>%nexthop%</td>
                        <td>Route prefix next hop (routes configuration only)</td>
                    </tr>
                    <tr>
                    	<td>%route_cisco%</td>
                        <td>Outputs [network]/[mask] for IPv6 prefixes and [network] [dotted mask] for IPv4 prefixes - i.e. Cisco's route prefix format (routes configuration only)</td>
                    </tr>
                    <tr>
                    	<td>%router%</td>
                        <td>The loopback address of the device that routes should be deployed on (you can set this in the template) (routes configuration only)</td>
                    </tr>
                </table>
                
                </strong>
                    
			<?php
            } 
			
			else {
				
			mysql_select_db($database_subman, $subman);
			$query_serviceTemplates = "SELECT * FROM servicetemplate WHERE container = ".$_GET['container']." ORDER BY servicetemplate.name";
			$serviceTemplates = mysql_query($query_serviceTemplates, $subman) or die(mysql_error());
			$row_serviceTemplates = mysql_fetch_assoc($serviceTemplates);
			$totalRows_serviceTemplates = mysql_num_rows($serviceTemplates);
			
			?>
     Devices <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>Service Templates</strong>
     
     <p>&nbsp;</p>
     
     <?php if ($totalRows_serviceTemplates > 0) { ?>
     
    <table border="0" width="50%">
      <tr>
        <td><strong>Template Name</strong></td>
      </tr>
      <?php $count = 0;
		do {
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;browsetemplates=service&amp;template=<?php echo $row_serviceTemplates['id']; ?>" title"View this service template"><?php echo $row_serviceTemplates['name']; ?></a> <?php if ($row_serviceTemplates['disabled'] == 1) { ?><font color="#FF0000">[inactive]</font><?php } ?></td>
        </tr>
        <?php } while ($row_serviceTemplates = mysql_fetch_assoc($serviceTemplates)); ?>
    </table>
        
        <?php } else { ?>
        	
            <p>There are no service templates to display.</p>
        
        <?php } ?>
        
    <?php
			}
			
		}
		elseif ($_SESSION['provide_step'] == 10) { 
		
			
			
			?>
        
        	
            
            <h5>Results</h5>
            
            <?php if ($_SESSION['provide_vpn'] != "") { 
				
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
				$query_xconnect = "SELECT * FROM xconnectid WHERE xconnectid.id = '".$xconnectid."'";
				$xconnect = mysql_query($query_xconnect, $subman) or die(mysql_error());
				$row_xconnect = mysql_fetch_assoc($xconnect);
				$totalRows_xconnect = mysql_num_rows($xconnect);
				
				mysql_select_db($database_subman, $subman);
				$query_vrf = "SELECT * FROM vrf WHERE vrf.id = '".$_SESSION['provide_vrf']."'";
				$vrf = mysql_query($query_vrf, $subman) or die(mysql_error());
				$row_vrf = mysql_fetch_assoc($vrf);
				$totalRows_vrf = mysql_num_rows($vrf);
				?>
                
                <p>The following customers were added to the VPN '<?php echo $row_vpn['asnumber']; ?> <?php echo $row_vpn['providername']; ?> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <?php echo $row_vpn['name']; ?>' <?php if ($_SESSION['provide_layer'] == 3) { ?>(<?php echo $row_vrf['name']; ?>)<?php } ?>:
                <br />
   	<ul>
                    	<li><strong><?php echo $row_customer['name']; ?></strong></li>
    </ul>
                </p>
                
                <?php if ($_SESSION['provide_layer'] == 2) { ?>
                	
                    <p>The following pseudowire numbers were allocated, and attached to the selected ports:
                    <br />
   	<ul>
                        	<li><strong><?php echo $row_xconnect['xconnectid']; ?></strong></li>
    </ul>
                    </p>
                    
                <?php } ?>
                
            <?php } ?>
                
                <?php if ($_SESSION['provide_layer'] == 3) { 

					
					
					mysql_select_db($database_subman, $subman);
					$query_network = "SELECT * FROM networks WHERE networks.id = '".$network."'";
					$network = mysql_query($query_network, $subman) or die(mysql_error());
					$row_network = mysql_fetch_assoc($network);
					$totalRows_network = mysql_num_rows($network);
					
					mysql_select_db($database_subman, $subman);
					$query_address1 = "SELECT * FROM addresses WHERE addresses.id = '".$router_node_a."'";
					$address1 = mysql_query($query_address1, $subman) or die(mysql_error());
					$row_address1 = mysql_fetch_assoc($address1);
					$totalRows_address1 = mysql_num_rows($address1);
					
					mysql_select_db($database_subman, $subman);
					$query_address2 = "SELECT * FROM addresses WHERE addresses.id = '".$router_node_b."'";
					$address2 = mysql_query($query_address2, $subman) or die(mysql_error());
					$row_address2 = mysql_fetch_assoc($address2);
					$totalRows_address2 = mysql_num_rows($address2);
					
					?>
                	
                	<p>The following network and addressing was created and attached to the relevant port(s):
                    <br />
   	<ul>
                        	<li><strong><?php if ($row_network['v6mask'] == "") { echo long2ip($row_network['network']); ?><?php echo get_slash($row_network['mask']); ?><?php } else { echo Net_IPv6::Compress(long2ipv6($row_network['network'])); ?>/<?php echo $row_network['v6mask']; } ?></strong>
                            	<ul>
                                	<?php if ($_SESSION['provide_existing'] == 1) { ?>
                                    	<li>You chose an existing network, the following addresses were added to the network:</li>
                                    <?php }?>
	                                	<li><?php if ($row_network['v6mask'] == "") { echo long2ip($row_address1['address']); } else { echo Net_IPv6::Compress(long2ipv6($row_address1['address'])); } ?> - <?php echo $row_address1['descr']; ?></li>
                                    <?php if ($totalRows_address2 > 0) { ?>
                                    	<li><?php if ($row_network['v6mask'] == "") { echo long2ip($row_address2['address']); } else { echo Net_IPv6::Compress(long2ipv6($row_address2['address'])); } ?> - <?php echo $row_address2['descr']; ?></li>
                                    <?php } ?>
                                </ul>
                            </li>
    </ul>
                    </p>
                
                <?php } ?>
                
                <?php
                
                if ($_SESSION['additional_address'] == 1) {
					$linkid = $_SESSION['additional_address_link'];
				}
				else {
					$linkid = $new_link_id;
				}
					
                mysql_select_db($database_subman, $subman);
				$query_linkA = "select links.*, customer.name as customername, portsdevices.`name` as devicename, portsdevices.managementip, vpn.`name` as vpnname, vlan.number as vlan, xconnectid.`xconnectid`, vrf.`name` as vrfname, cardtypes.`name` as cardtypename, cardtypes.config, cards.slot, cards.module, cards.rack, subint.subint as subint, portsports.`port` as port FROM links LEFT JOIN customer ON customer.id = links.provide_customer LEFT JOIN portsdevices ON portsdevices.id = links.`provide_node_a` LEFT JOIN vpn ON vpn.id = links.`provide_vpn` LEFT JOIN vlan ON vlan.id = links.`provide_vlan_node_a` LEFT JOIN xconnectid on xconnectid.id = links.`provide_xconnect` LEFT JOIN vrf ON vrf.id = links.`provide_vrf` LEFT JOIN cards ON cards.id = links.`provide_card_node_a` LEFT JOIN cardtypes ON cardtypes.id = cards.`cardtype` LEFT JOIN subint ON subint.id = links.`provide_subint_node_a` LEFT JOIN portsports ON portsports.id = links.`provide_port_node_a` WHERE links.id = '".$linkid."'";
				$linkA = mysql_query($query_linkA, $subman) or die(mysql_error());
				$row_linkA = mysql_fetch_assoc($linkA);
				$totalRows_linkA = mysql_num_rows($linkA);

				mysql_select_db($database_subman, $subman);
				$query_linkB = "select links.*, customer.name as customername, portsdevices.`name` as devicename, portsdevices.managementip, vpn.`name` as vpnname, vlan.number as vlan, xconnectid.`xconnectid`, vrf.`name` as vrfname, cardtypes.`name` as cardtypename, cardtypes.config, cards.slot, cards.module, cards.rack, subint.subint as subint, portsports.`port` as port FROM links LEFT JOIN customer ON customer.id = links.provide_customer LEFT JOIN portsdevices ON portsdevices.id = links.`provide_node_b` LEFT JOIN vpn ON vpn.id = links.`provide_vpn` LEFT JOIN vlan ON vlan.id = links.`provide_vlan_node_b` LEFT JOIN xconnectid on xconnectid.id = links.`provide_xconnect` LEFT JOIN vrf ON vrf.id = links.`provide_vrf` LEFT JOIN cards ON cards.id = links.`provide_card_node_b` LEFT JOIN cardtypes ON cardtypes.id = cards.`cardtype` LEFT JOIN subint ON subint.id = links.`provide_subint_node_b` LEFT JOIN portsports ON portsports.id = links.`provide_port_node_b` WHERE links.id = '".$linkid."'";
				$linkB = mysql_query($query_linkB, $subman) or die(mysql_error());
				$row_linkB = mysql_fetch_assoc($linkB);
				$totalRows_linkB = mysql_num_rows($linkB);
					
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
				
				if (!(preg_match('/^auto_+/',$_SESSION['provide_vlan_node_a'])) && !(preg_match('/^manual_+/',$_SESSION['provide_vlan_node_a']))) {
					$vlan_node_a = $_SESSION['provide_vlan_node_a'];
				}
				if (!(preg_match('/^auto_+/',$_SESSION['provide_vlan_node_b'])) && !(preg_match('/^manual_+/',$_SESSION['provide_vlan_node_b']))) {
					$vlan_node_b = $_SESSION['provide_vlan_node_b'];
				}
				
				mysql_select_db($database_subman, $subman);
				$query_vlan_node_a = "SELECT * FROM vlan WHERE vlan.id = '".$vlan_node_a."'";
				$vlan_node_a = mysql_query($query_vlan_node_a, $subman) or die(mysql_error());
				$row_vlan_node_a = mysql_fetch_assoc($vlan_node_a);
				$totalRows_vlan_node_a = mysql_num_rows($vlan_node_a);
				
				mysql_select_db($database_subman, $subman);
				$query_vlan_node_b = "SELECT * FROM vlan WHERE vlan.id = '".$vlan_node_b."'";
				$vlan_node_b = mysql_query($query_vlan_node_b, $subman) or die(mysql_error());
				$row_vlan_node_b = mysql_fetch_assoc($vlan_node_b);
				$totalRows_vlan_node_b = mysql_num_rows($vlan_node_b);
				
				mysql_select_db($database_subman, $subman);
				$query_card_node_a = "SELECT cards.*, cardtypes.name as cardtypename, cardtypes.config FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = '".$_SESSION['provide_card_node_a']."'";
				$card_node_a = mysql_query($query_card_node_a, $subman) or die(mysql_error());
				$row_card_node_a = mysql_fetch_assoc($card_node_a);
				$totalRows_card_node_a = mysql_num_rows($card_node_a);
				
				mysql_select_db($database_subman, $subman);
				$query_card_node_b = "SELECT cards.*, cardtypes.name as cardtypename, cardtypes.config FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = '".$_SESSION['provide_card_node_b']."'";
				$card_node_b = mysql_query($query_card_node_b, $subman) or die(mysql_error());
				$row_card_node_b = mysql_fetch_assoc($card_node_b);
				$totalRows_card_node_b = mysql_num_rows($card_node_b);

				
				mysql_select_db($database_subman, $subman);
				$query_port_node_a = "SELECT portsports.* FROM portsports WHERE portsports.id = '".$portid_node_a."'";
				$port_node_a = mysql_query($query_port_node_a, $subman) or die(mysql_error());
				$row_port_node_a = mysql_fetch_assoc($port_node_a);
				$totalRows_port_node_a = mysql_num_rows($port_node_a);
				
				mysql_select_db($database_subman, $subman);
				$query_port_node_b = "SELECT portsports.* FROM portsports WHERE portsports.id = '".$portid_node_b."'";
				$port_node_b = mysql_query($query_port_node_b, $subman) or die(mysql_error());
				$row_port_node_b = mysql_fetch_assoc($port_node_b);
				$totalRows_port_node_b = mysql_num_rows($port_node_b);
				
				if ($_SESSION['provide_subint_node_a'] != "0") {
					$subint_node_a = $_SESSION['provide_subint_node_a'];
				}
				if ($_SESSION['provide_subint_node_b'] != "0") {
					$subint_node_b = $_SESSION['provide_subint_node_b'];
				}
				
				mysql_select_db($database_subman, $subman);
				$query_subint_node_a = "SELECT subint.* FROM subint WHERE subint.port = '".$portid_node_a."' AND subint.subint = '".$subint_node_a."'";
				$subint_node_a = mysql_query($query_subint_node_a, $subman) or die(mysql_error());
				$row_subint_node_a = mysql_fetch_assoc($subint_node_a);
				$totalRows_subint_node_a = mysql_num_rows($subint_node_a);
				
				mysql_select_db($database_subman, $subman);
				$query_subint_node_b = "SELECT subint.* FROM subint WHERE subint.port = '".$portid_node_b."' AND subint.subint = '".$subint_node_b."'";
				$subint_node_b = mysql_query($query_subint_node_b, $subman) or die(mysql_error());
				$row_subint_node_b = mysql_fetch_assoc($subint_node_b);
				$totalRows_subint_node_b = mysql_num_rows($subint_node_b);
				
				mysql_select_db($database_subman, $subman);
				$query_timeslots_node_a = "SELECT timeslots.* FROM timeslots WHERE timeslots.portid = '".$portid_node_a."'";
				$timeslots_node_a = mysql_query($query_timeslots_node_a, $subman) or die(mysql_error());
				$row_timeslots_node_a = mysql_fetch_assoc($timeslots_node_a);
				$totalRows_timeslots_node_a = mysql_num_rows($timeslots_node_a);
				
				mysql_select_db($database_subman, $subman);
				$query_timeslots_node_b = "SELECT timeslots.* FROM timeslots WHERE timeslots.portid = '".$portid_node_b."'";
				$timeslots_node_b = mysql_query($query_timeslots_node_b, $subman) or die(mysql_error());
				$row_timeslots_node_b = mysql_fetch_assoc($timeslots_node_b);
				$totalRows_timeslots_node_b = mysql_num_rows($timeslots_node_b);
				
            	?>
                
                <?php if ($_SESSION['additional_address'] != 1) { ?>
                
                <p>The following device ports were allocated:
                <br />
   	<ul>
                    	<li><strong><?php echo $row_node_a['name']; ?>: <?php echo $row_card_node_a['cartypename']; ?> <?php if (!(isset($row_card_node_a['rack'])) && !(isset($row_card_node_a['module'])) && !(isset($row_card_node_a['slot']))) { echo "Virtual"; } else { if (isset($row_card_node_a['rack'])) { echo $row_card_node_a['rack']."/"; } if (isset($row_card_node_a['module'])) { echo $row_card_node_a['module'].'/'; } if (isset($row_card_node_a['slot'])) { echo $row_card_node_a['slot']; } } ?>/<?php echo $row_port_node_a['port']; ?><?php if ($totalRows_subint_node_a > 0) { ?><font color="#FF0000"><?php echo ".".$row_subint_node_a['subint']; ?></font><?php } ?></strong>
                        <?php if ($totalRows_timeslots_node_a > 0) { ?>
                        	<ul>
                           	<?php do { ?>
                                	<li>Timeslot <?php echo $row_timeslots_node_a['timeslot']; ?></li>
                                <?php } while ($row_timeslots_node_a = mysql_fetch_assoc($timeslots_node_a)); ?>
                            </ul>
                        <?php } ?>
                        </li>
                        <?php if ($totalRows_node_b > 0) { ?>
                            <li><strong><?php echo $row_node_b['name']; ?>: <?php echo $row_card_node_b['cartypename']; ?> <?php if (!(isset($row_card_node_b['rack'])) && !(isset($row_card_node_b['module'])) && !(isset($row_card_node_b['slot']))) { echo "Virtual"; } else { if (isset($row_card_node_b['rack'])) { echo $row_card_node_b['rack']."/"; } if (isset($row_card_node_b['module'])) { echo $row_card_node_b['module'].'/'; } if (isset($row_card_node_b['slot'])) { echo $row_card_node_b['slot']; } } ?>/<?php echo $row_port_node_b['port']; ?><?php if ($totalRows_subint_node_b > 0) { ?><font color="#FF0000"><?php echo ".".$row_subint_node_b['subint']; ?></font><?php } ?></strong>
                            <?php if ($totalRows_timeslots_node_b > 0) { ?>
                                <ul>
                                <?php do { ?>
                                        <li>Timeslot <?php echo $row_timeslots_node_b['timeslot']; ?></li>
                                    <?php } while ($row_timeslots_node_b = mysql_fetch_assoc($timeslots_node_b)); ?>
                                </ul>
                            <?php } ?>
                            </li>
                        <?php } ?>
    </ul>
                </p>
                <?php if ($totalRows_vlan_node_a > 0 || $totalRows_vlan_node_b > 0) { ?>
                <p>The following VLANs were provided (or used) and attached to the relevant port(s):
                
   	<ul>
                    
                	<?php if ($totalRows_vlan_node_a > 0) { ?>
                		<li><strong>[<?php echo $row_vlan_node_a['number']; ?>] <?php echo $row_vlan_node_a['name']; ?></strong></li>
                    <?php } ?>
                    <?php if ($totalRows_vlan_node_b > 0) { ?>
                		<li><strong>[<?php echo $row_vlan_node_b['number']; ?>] <?php echo $row_vlan_node_b['name']; ?></strong></li>
                    <?php } ?>
                    
    </ul>
                    
                </p>
                <?php } ?>
                
                <?php } # If not additional addresses ?>
                
                                <?php
					mysql_select_db($database_subman, $subman);
					$query_provide_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
					$provide_template = mysql_query($query_provide_template, $subman) or die(mysql_error());
					$row_provide_template = mysql_fetch_assoc($provide_template);
					$totalRows_provide_template = mysql_num_rows($provide_template);
					
					if ($_SESSION['additional_address'] == 1) { 
					
						mysql_select_db($database_subman, $subman);
						$query_provide_scripts = "SELECT * FROM scripts WHERE scripts.servicetemplate = '".$_SESSION['provide_template']."' AND scripts.scriptrole = 'secondarynetsdeploy'";
						$provide_scripts = mysql_query($query_provide_scripts, $subman) or die(mysql_error());
						$row_provide_scripts = mysql_fetch_assoc($provide_scripts);
						$totalRows_provide_scripts = mysql_num_rows($provide_scripts);
					
					}
					else {
					
						mysql_select_db($database_subman, $subman);
						$query_provide_scripts = "SELECT * FROM scripts WHERE scripts.servicetemplate = '".$_SESSION['provide_template']."' AND scripts.scriptrole = 'servicedeploy'";
						$provide_scripts = mysql_query($query_provide_scripts, $subman) or die(mysql_error());
						$row_provide_scripts = mysql_fetch_assoc($provide_scripts);
						$totalRows_provide_scripts = mysql_num_rows($provide_scripts);
					}
						
					if ($row_provide_template['config_a'] != "" || ($_SESSION['additional_address'] == 1 && $row_provide_template['secondarynets'] != "")) {
						
						if ($row_linkA['slot'] == "" && $row_linkA['rack'] == "" && $row_linkA['module'] == "") { 
						 	$row_linkA['slot'] =  "Virtual";
						}
						if ($row_linkA['rack'] != "" && $row_linkA['module'] != "") {
							$ifnumber = $row_linkA['rack']."/".$row_linkA['module']."/".$row_linkA['slot']."/".$row_linkA['port'];
						}
						elseif ($row_linkA['module'] != "") {
							$ifnumber = $row_linkA['module']."/".$row_linkA['slot']."/".$row_linkA['port'];
						}
						elseif ($row_linkA['slot'] != "Virtual") {
							$ifnumber = $row_linkA['slot']."/".$row_linkA['port'];
						}
						else {
							$ifnumber = $row_linkA['port'];
						}
						
						$parentifnumber = $ifnumber;
					
						if ($row_linkA['subint'] != "") {
							$ifnumber .= ".".$row_linkA['subint'];
						}
						
						if ($row_linkB['slot'] == "" && $row_linkB['rack'] == "" && $row_linkB['module'] == "") { 
						 	$row_linkB['slot'] =  "Virtual";
						}
						if ($row_linkB['rack'] != "" && $row_linkB['module'] != "") {
							$ifnumberb = $row_linkB['rack']."/".$row_linkB['module']."/".$row_linkB['slot']."/".$row_linkB['port'];
						}
						elseif ($row_linkB['module'] != "") {
							$ifnumberb = $row_linkB['module']."/".$row_linkB['slot']."/".$row_linkB['port'];
						}
						elseif ($row_linkB['slot'] != "Virtual") {
							$ifnumberb = $row_linkB['slot']."/".$row_linkB['port'];
						}
						else {
							$ifnumberb = $row_linkB['port'];
						}
						
						$parentifnumberb = $ifnumberb;
						
						if ($row_linkB['subint'] != "") {
							$ifnumberb .= ".".$row_linkB['subint'];
						}
						
						
						mysql_select_db($database_subman, $subman);
						$query_timeslotRange_node_a = "SELECT min(timeslot) as firstslot, max(timeslot) as lastslot FROM timeslots WHERE timeslots.portid = '".$portid_node_a."'";
						$timeslotRange_node_a = mysql_query($query_timeslotRange_node_a, $subman) or die(mysql_error());
						$row_timeslotRange_node_a = mysql_fetch_assoc($timeslotRange_node_a);
						$totalRows_timeslotRange_node_a = mysql_num_rows($timeslotRange_node_a);

						mysql_select_db($database_subman, $subman);
						$query_timeslotRange_node_b = "SELECT min(timeslot) as firstslot, max(timeslot) as lastslot FROM timeslots WHERE timeslots.portid = '".$portid_node_b."'";
						$timeslotRange_node_b = mysql_query($query_timeslotRange_node_b, $subman) or die(mysql_error());
						$row_timeslotRange_node_b = mysql_fetch_assoc($timeslotRange_node_b);
						$totalRows_timeslotRange_node_b = mysql_num_rows($timeslotRange_node_b);
						
						if ($row_network['v6mask'] != "") {
							$ipversioncisco = 'ipv6';
							$ipversion = 'IPv6';
						}
						else {
							$ipversioncisco = 'ip';
							$ipversion = 'IPv4';
						}
						
						if ($_SESSION['additional_address'] == 1) { 
							$config = str_replace("%customername%",$row_linkA['customername'],$row_provide_template['secondarynets']);
						}
						else {
							$config = str_replace("%customername%",$row_linkA['customername'],$row_provide_template['config_a']);
						}
						$config = str_replace("%nodea%",$row_linkA['devicename'],$config);
						$config = str_replace("%nodeb%",$row_linkB['devicename'],$config);
						$config = str_replace("%customername_trimmed%",str_replace(" ","",$row_linkA['customername']),$config);
						$config = str_replace("%iftype%",$row_linkA['config'],$config);
						$config = str_replace("%iftype_a%",$row_linkA['config'],$config);
						$config = str_replace("%iftype_b%",$row_linkB['config'],$config);
						$config = str_replace("%ifnumber%",$ifnumber,$config);
						$config = str_replace("%ifnumber_a%",$ifnumber,$config);
						$config = str_replace("%ifnumber_b%",$ifnumberb,$config);
						$config = str_replace("%ifnumber_nosubint%",$parentifnumber,$config);
						$config = str_replace("%ifnumber_a_nosubint%",$parentifnumber,$config);
						$config = str_replace("%ifnumber_b_nosubint%",$parentifnumberb,$config);
						$config = str_replace("%circuitnumber%",$_SESSION['provide_cct'],$config);
						$config = str_replace("%circuitnumber_trimmed%",str_replace(" ","",$_SESSION['provide_cct']),$config);
						$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
						$config = str_replace("%ipversion%",$ipversion,$config);
						if ($ipversion == "IPv6") {
							$config = str_replace("%ipaddress%",Net_IPv6::compress(long2ipv6($row_address1['address'])),$config);
							$config = str_replace("%ipaddress_a%",Net_IPv6::compress(long2ipv6($row_address1['address'])),$config);
							$config = str_replace("%ipaddress_b%",Net_IPv6::compress(long2ipv6($row_address2['address'])),$config);
							$config = str_replace("%ipaddress_opposite%",Net_IPv6::compress(long2ipv6($row_address2['address'])),$config);
							$config = str_replace("%addressmask_cisco%",Net_IPv6::compress(long2ipv6($row_address1['address']))."/".$row_network['v6mask'],$config);
							$config = str_replace("%secondary_cisco%","",$config);
							$config = str_replace("%netmask%","/".$row_network['v6mask'],$config);
							$config = str_replace("%netmask_slash%","/".$row_network['v6mask'],$config);
						}
						else {
							$config = str_replace("%ipaddress%",long2ip($row_address1['address']),$config);
							$config = str_replace("%ipaddress_a%",long2ip($row_address1['address']),$config);
							$config = str_replace("%ipaddress_b%",long2ip($row_address2['address']),$config);
							$config = str_replace("%ipaddress_opposite%",long2ip($row_address2['address']),$config);
							$config = str_replace("%addressmask_cisco%",long2ip($row_address1['address'])." ".$row_network['mask'],$config);
							$config = str_replace("%secondary_cisco%","secondary",$config);
							$config = str_replace("%netmask%",$row_network['mask'],$config);
							$config = str_replace("%netmask_slash%",get_slash($row_network['mask']),$config);
						}
						$config = str_replace("%vlan%",$row_linkA['vlan'],$config);
						$config = str_replace("%vpn%",$row_linkA['vpnname'],$config);
						$config = str_replace("%vrf%",$row_linkA['vrfname'],$config);
						$config = str_replace("%xconnect%",$row_linkA['xconnectid'],$config);
						$config = str_replace("%loopback%",$row_linkA['managementip'],$config);
						$config = str_replace("%loopback_a%",$row_linkA['managementip'],$config);
						$config = str_replace("%loopback_b%",$row_linkB['managementip'],$config);
						$config = str_replace("%loopback_opposite%",$row_linkB['managementip'],$config);
						$config = str_replace("%first_timeslot%",$row_timeslotRange_node_a['firstslot'],$config);
						$config = str_replace("%last_timeslot%",$row_timeslotRange_node_a['lastslot'],$config);
						
						mysql_select_db($database_subman, $subman);
						$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
						$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
						$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
						$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
				
						do {
						
							$config = str_replace("%".$row_arbitraryfields['id']."%",$_SESSION['arbitrary'.$row_arbitraryfields['id']],$config);
							
						} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
						
						echo "<h5>Device Configuration Template - ".$row_linkA['devicename']."</h5>";
						echo "<p><textarea cols=\"100\" rows=\"10\" class=\"input_standard\">".$config."</textarea></p>";
						
						if ($totalRows_node_b > 0) {
							
							if ($row_provide_template['config_b'] != "" || ($_SESSION['additional_address'] == 1 && $row_provide_template['secondarynets'] != "")) {
								
								if ($_SESSION['additional_address'] == 1) { 
									$config = str_replace("%customername%",$row_linkB['customername'],$row_provide_template['secondarynets_b']);
								}
								else {
									$config = str_replace("%customername%",$row_linkB['customername'],$row_provide_template['config_b']);
								}
								$config = str_replace("%nodea%",$row_linkA['devicename'],$config);
								$config = str_replace("%nodeb%",$row_linkB['devicename'],$config);
								$config = str_replace("%customername_trimmed%",str_replace(" ","",$row_linkB['customername']),$config);
								$config = str_replace("%iftype%",$row_linkB['config'],$config);
								$config = str_replace("%iftype_a%",$row_linkA['config'],$config);
								$config = str_replace("%iftype_b%",$row_linkB['config'],$config);
								$config = str_replace("%ifnumber%",$ifnumberb,$config);
								$config = str_replace("%ifnumber_a%",$ifnumber,$config);
								$config = str_replace("%ifnumber_b%",$ifnumberb,$config);
								$config = str_replace("%ifnumber_nosubint%",$parentifnumberb,$config);
								$config = str_replace("%ifnumber_a_nosubint%",$parentifnumber,$config);
								$config = str_replace("%ifnumber_b_nosubint%",$parentifnumberb,$config);
								$config = str_replace("%circuitnumber%",$_SESSION['provide_cct'],$config);
								$config = str_replace("%circuitnumber_trimmed%",str_replace(" ","",$_SESSION['provide_cct']),$config);
								$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
								$config = str_replace("%ipversion%",$ipversion,$config);
								if ($ipversion == "IPv6") {
									$config = str_replace("%ipaddress%",Net_IPv6::compress(long2ipv6($row_address2['address'])),$config);
									$config = str_replace("%ipaddress_a%",Net_IPv6::compress(long2ipv6($row_address1['address'])),$config);
									$config = str_replace("%ipaddress_b%",Net_IPv6::compress(long2ipv6($row_address2['address'])),$config);
									$config = str_replace("%ipaddress_opposite%",Net_IPv6::compress(long2ipv6($row_address1['address'])),$config);
									$config = str_replace("%addressmask_cisco%",Net_IPv6::compress(long2ipv6($row_address2['address']))."/".$row_network['v6mask'],$config);
									$config = str_replace("%secondary_cisco%","",$config);
									$config = str_replace("%netmask%","/".$row_network['v6mask'],$config);
									$config = str_replace("%netmask_slash%","/".$row_network['v6mask'],$config);
								}
								else {
									$config = str_replace("%ipaddress%",long2ip($row_address2['address']),$config);
									$config = str_replace("%ipaddress_a%",long2ip($row_address1['address']),$config);
									$config = str_replace("%ipaddress_b%",long2ip($row_address2['address']),$config);
									$config = str_replace("%ipaddress_opposite%",long2ip($row_address1['address']),$config);
									$config = str_replace("%addressmask_cisco%",long2ip($row_address2['address'])." ".$row_network['mask'],$config);
									$config = str_replace("%secondary_cisco%","secondary",$config);
									$config = str_replace("%netmask%",$row_network['mask'],$config);
									$config = str_replace("%netmask_slash%",get_slash($row_network['mask']),$config);
								}
								if ($_SESSION['provide_vlan_node_b'] == "same") {
									$row_linkB['vlan'] = $row_linkA['vlan'];
								}
								$config = str_replace("%vlan%",$row_linkB['vlan'],$config);
								$config = str_replace("%vpn%",$row_linkB['vpnname'],$config);
								$config = str_replace("%vrf%",$row_linkB['vrfname'],$config);
								$config = str_replace("%xconnect%",$row_linkB['xconnectid'],$config);
								$config = str_replace("%loopback%",$row_linkB['managementip'],$config);
								$config = str_replace("%loopback_a%",$row_linkA['managementip'],$config);
								$config = str_replace("%loopback_b%",$row_linkB['managementip'],$config);
								$config = str_replace("%loopback_opposite%",$row_linkA['managementip'],$config);
								$config = str_replace("%first_timeslot%",$row_timeslotRange_node_b['firstslot'],$config);
								$config = str_replace("%last_timeslot%",$row_timeslotRange_node_b['lastslot'],$config);
								
								mysql_select_db($database_subman, $subman);
								$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
								$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
								$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
								$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
				
								do {
						
									$config = str_replace("%".$row_arbitraryfields['id']."%",$_SESSION['arbitrary'.$row_arbitraryfields['id']],$config);
							
								} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
								
								echo "<h5>Device Configuration Template - ".$row_linkB['devicename']."</h5>";
								echo "<p><textarea cols=\"100\" rows=\"10\" class=\"input_standard\">".$config."</textarea></p>";
							}
							
						}
						
					}
					
					if ($totalRows_provide_scripts > 0) {
							
						?>
                        
                        <?php do {
							
							mysql_select_db($database_subman, $subman);
							$query_variables = "SELECT * FROM scriptvariables WHERE scriptvariables.script = '".$row_provide_scripts['id']."' ORDER BY id";
							$variables = mysql_query($query_variables, $subman) or die(mysql_error());
							$row_variables = mysql_fetch_assoc($variables);
							$totalRows_variables = mysql_num_rows($variables);
							
							if ($row_network['v6mask'] != "") {
								$ipversioncisco = 'ipv6';
								$ipversion = 'IPv6';
							}
							else {
								$ipversioncisco = 'ip';
								$ipversion = 'IPv4';
							}
							?>
                        <?php if ($row_provide_scripts['scripttype'] == 'HTTP') { ?>
                        <?php $script = $row_provide_scripts['location']."?"; ?>
						<?php do { 
								$config = str_replace("%customername%",$row_linkA['customername'],$row_variables['value']);
						$config = str_replace("%nodea%",$row_linkA['devicename'],$config);
						$config = str_replace("%nodeb%",$row_linkB['devicename'],$config);
						$config = str_replace("%customername_trimmed%",str_replace(" ","",$row_linkA['customername']),$config);
						$config = str_replace("%iftype%",$row_linkA['config'],$config);
						$config = str_replace("%iftype_a%",$row_linkA['config'],$config);
						$config = str_replace("%iftype_b%",$row_linkB['config'],$config);
						$config = str_replace("%ifnumber%",$ifnumber,$config);
						$config = str_replace("%ifnumber_a%",$ifnumber,$config);
						$config = str_replace("%ifnumber_b%",$ifnumberb,$config);
						$config = str_replace("%ifnumber_nosubint%",$parentifnumber,$config);
						$config = str_replace("%ifnumber_a_nosubint%",$parentifnumber,$config);
						$config = str_replace("%ifnumber_b_nosubint%",$parentifnumberb,$config);
						$config = str_replace("%circuitnumber%",$_SESSION['provide_cct'],$config);
						$config = str_replace("%circuitnumber_trimmed%",str_replace(" ","",$_SESSION['provide_cct']),$config);
						$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
						$config = str_replace("%ipversion%",$ipversion,$config);
						if ($ipversion == "IPv6") {
							$config = str_replace("%ipaddress%",Net_IPv6::compress(long2ipv6($row_address1['address'])),$config);
							$config = str_replace("%ipaddress_a%",Net_IPv6::compress(long2ipv6($row_address1['address'])),$config);
							$config = str_replace("%ipaddress_b%",Net_IPv6::compress(long2ipv6($row_address2['address'])),$config);
							$config = str_replace("%ipaddress_opposite%",Net_IPv6::compress(long2ipv6($row_address2['address'])),$config);
							$config = str_replace("%addressmask_cisco%",Net_IPv6::compress(long2ipv6($row_address1['address']))."/".$row_network['v6mask'],$config);
							$config = str_replace("%secondary_cisco%","",$config);
							$config = str_replace("%netmask%","/".$row_network['v6mask'],$config);
							$config = str_replace("%netmask_slash%","/".$row_network['v6mask'],$config);
						}
						else {
							$config = str_replace("%ipaddress%",long2ip($row_address1['address']),$config);
							$config = str_replace("%ipaddress_a%",long2ip($row_address1['address']),$config);
							$config = str_replace("%ipaddress_b%",long2ip($row_address2['address']),$config);
							$config = str_replace("%ipaddress_opposite%",long2ip($row_address2['address']),$config);
							$config = str_replace("%addressmask_cisco%",long2ip($row_address1['address'])." ".$row_network['mask'],$config);
							$config = str_replace("%secondary_cisco%","secondary",$config);
							$config = str_replace("%netmask%",$row_network['mask'],$config);
							$config = str_replace("%netmask_slash%",get_slash($row_network['mask']),$config);
						}
						$config = str_replace("%vlan%",$row_linkA['vlan'],$config);
						$config = str_replace("%vpn%",$row_linkA['vpnname'],$config);
						$config = str_replace("%vrf%",$row_linkA['vrfname'],$config);
						$config = str_replace("%xconnect%",$row_linkA['xconnectid'],$config);
						$config = str_replace("%loopback%",$row_linkA['managementip'],$config);
						$config = str_replace("%loopback_a%",$row_linkA['managementip'],$config);
						$config = str_replace("%loopback_b%",$row_linkB['managementip'],$config);
						$config = str_replace("%loopback_opposite%",$row_linkB['managementip'],$config);
						$config = str_replace("%first_timeslot%",$row_timeslotRange_node_a['firstslot'],$config);
						$config = str_replace("%last_timeslot%",$row_timeslotRange_node_a['lastslot'],$config);
						
						mysql_select_db($database_subman, $subman);
						$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
						$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
						$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
						$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
				
						do {
						
							$config = str_replace("%".$row_arbitraryfields['id']."%",$_SESSION['arbitrary'.$row_arbitraryfields['id']],$config);
							
						} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
						
								?><?php $script .= $row_variables['variablename']."=".urlencode($config)."&";?>
                            <?php } while ($row_variables = mysql_fetch_assoc($variables)); ?>
                            
                            <br />
                            <?php if ($row_provide_scripts['autorun'] != 1) { ?>
                            <input type="button" name="launch_script" id="launch_script" class="input_red" title="Execute Script <?php echo $script; ?>" value="<?php echo $row_provide_scripts['description']; ?>" onClick="document.getElementById('script_terminal').src = '<?php echo $script; ?>'; document.getElementById('launch_script').disabled = 'disabled'; document.getElementById('script_prompt').style.display = 'block';" /><span id="script_prompt" style="display:none">Executing script, please wait for output below...</span><br />
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto"></iframe><br />
                            <?php } else { ?>
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto" src="<?php echo $script; ?>"></iframe><br />
                            <?php } ?>
                            
                        <?php } elseif ($row_provide_scripts['scripttype'] == 'CLI') { ?>
                        
                        <?php $script = $row_provide_scripts['location']." "; ?>
						<?php do { 
								$config = str_replace("%customername%",$row_linkA['customername'],$row_variables['value']);
								$config = str_replace("%nodea%",$row_linkA['devicename'],$config);
								$config = str_replace("%nodeb%",$row_linkB['devicename'],$config);
								$config = str_replace("%customername_trimmed%",str_replace(" ","",$row_linkA['customername']),$config);
								$config = str_replace("%iftype%",$row_linkA['config'],$config);
								$config = str_replace("%iftype_a%",$row_linkA['config'],$config);
								$config = str_replace("%iftype_b%",$row_linkB['config'],$config);
								$config = str_replace("%ifnumber%",$ifnumber,$config);
								$config = str_replace("%ifnumber_a%",$ifnumber,$config);
								$config = str_replace("%ifnumber_b%",$ifnumberb,$config);
								$config = str_replace("%ifnumber_nosubint%",$parentifnumber,$config);
								$config = str_replace("%ifnumber_a_nosubint%",$parentifnumber,$config);
								$config = str_replace("%ifnumber_b_nosubint%",$parentifnumberb,$config);
								$config = str_replace("%circuitnumber%",$_SESSION['provide_cct'],$config);
								$config = str_replace("%circuitnumber_trimmed%",str_replace(" ","",$_SESSION['provide_cct']),$config);
								$config = str_replace("%ipversion_cisco%",$ipversioncisco,$config);
								$config = str_replace("%ipversion%",$ipversion,$config);
								if ($ipversion == "IPv6") {
									$config = str_replace("%ipaddress%",Net_IPv6::compress(long2ipv6($row_address1['address'])),$config);
									$config = str_replace("%ipaddress_a%",Net_IPv6::compress(long2ipv6($row_address1['address'])),$config);
									$config = str_replace("%ipaddress_b%",Net_IPv6::compress(long2ipv6($row_address2['address'])),$config);
									$config = str_replace("%ipaddress_opposite%",Net_IPv6::compress(long2ipv6($row_address2['address'])),$config);
									$config = str_replace("%addressmask_cisco%",Net_IPv6::compress(long2ipv6($row_address1['address']))."/".$row_network['v6mask'],$config);
									$config = str_replace("%secondary_cisco%","",$config);
									$config = str_replace("%netmask%","/".$row_network['v6mask'],$config);
									$config = str_replace("%netmask_slash%","/".$row_network['v6mask'],$config);
								}
								else {
									$config = str_replace("%ipaddress%",long2ip($row_address1['address']),$config);
									$config = str_replace("%ipaddress_a%",long2ip($row_address1['address']),$config);
									$config = str_replace("%ipaddress_b%",long2ip($row_address2['address']),$config);
									$config = str_replace("%ipaddress_opposite%",long2ip($row_address2['address']),$config);
									$config = str_replace("%addressmask_cisco%",long2ip($row_address1['address'])." ".$row_network['mask'],$config);
									$config = str_replace("%secondary_cisco%","secondary",$config);
									$config = str_replace("%netmask%",$row_network['mask'],$config);
									$config = str_replace("%netmask_slash%",get_slash($row_network['mask']),$config);
								}
								if ($_SESSION['provide_vlan_node_b'] == "same") {
									$row_linkB['vlan'] = $row_linkA['vlan'];
								}
								$config = str_replace("%vlan%",$row_linkA['vlan'],$config);
								$config = str_replace("%vpn%",$row_linkA['vpnname'],$config);
								$config = str_replace("%vrf%",$row_linkA['vrfname'],$config);
								$config = str_replace("%xconnect%",$row_linkA['xconnectid'],$config);
								$config = str_replace("%loopback%",$row_linkA['managementip'],$config);
								$config = str_replace("%loopback_a%",$row_linkA['managementip'],$config);
								$config = str_replace("%loopback_b%",$row_linkB['managementip'],$config);
								$config = str_replace("%loopback_opposite%",$row_linkB['managementip'],$config);
								$config = str_replace("%first_timeslot%",$row_timeslotRange_node_a['firstslot'],$config);
								$config = str_replace("%last_timeslot%",$row_timeslotRange_node_a['lastslot'],$config);
								
								mysql_select_db($database_subman, $subman);
								$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
								$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
								$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
								$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
				
								do {
						
									$config = str_replace("%".$row_arbitraryfields['id']."%",$_SESSION['arbitrary'.$row_arbitraryfields['id']],$config);
							
								} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
						
								?><?php $script .= $config." ";?>
                            <?php } while ($row_variables = mysql_fetch_assoc($variables)); ?>
                            <?php 
								$_SESSION['script'] = $script;
								?>
                            <br />
                            <?php if ($row_provide_scripts['autorun'] != 1) { ?>
                            <input type="button" name="launch_script" id="launch_script" class="input_red" title="Execute Script <?php echo $script; ?>" value="<?php echo $row_provide_scripts['description']; ?>" onClick="document.getElementById('script_terminal').src = 'includes/launch_cli.php'; document.getElementById('launch_script').disabled = 'disabled'; document.getElementById('script_prompt').style.display = 'block';" /><span id="script_prompt" style="display:none">Executing script, please wait for output below...</span><br />
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto"></iframe><br />
                            <?php } else { ?>
                            Executing script, please wait for output below...<br />
                            <iframe allowtransparency="true" name="script_terminal" id="script_terminal" class="input_standard" height="200" width="600" scrolling="auto" src="includes/launch_cli.php"></iframe><br />
                            <?php } ?>
                        <?php
						
						} ?>
                        
                        <?php
						
							} while ($row_provide_scripts = mysql_fetch_assoc($provide_scripts));
						
					}
					?>
                	
                    <form action="handler.php" method="post" target="_self">
            
            	<input type="hidden" name="provide" value="step10" />
                <input type="hidden" name="device" value="<?php echo $_SESSION['provide_node_a']; ?>" />
                <?php if ($_SESSION['additional_address'] == 1) { ?>
                	<input type="hidden" name="port" value="<?php echo $_SESSION['provide_port_node_a']; ?>" />
                <?php } else { ?>
                	<input type="hidden" name="port" value="<?php echo $portid_node_a; ?>" />
                <?php } ?>
                <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" /><br /><br />
                <?php if ($row_provide_template['templatelink'] != "" && $_SESSION['additional_address'] != 1) { ?>
                <p class="text_red"><strong>This template requires that another link be provided.  Please click the button below to continue to the next template.</strong></p>
                <input type="hidden" name="templatelink" value="<?php echo $row_provide_template['templatelink']; ?>" />
                <input type="hidden" name="linked" value="<?php echo $new_link_id; ?>" />
            	<input type="submit" name="submit" value="Continue" class="input_standard" />
                <?php } else { ?>
                <input type="submit" name="submit" value="Finish" class="input_standard" />
                <?php } ?>
            
            </form>
            
            <?php
					
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
						
					
					if ($row_provide_template['templatelink'] != "") {
						
						$_SESSION['provide_template'] = $row_provide_template['templatelink'];
						$_SESSION['provide_step'] = 1;
						$_SESSION['templatelink'] = $row_provide_template['templatelink'];
						$_SESSION['linked'] = $new_link_id;
						
					}
					else {
					
						#do {
						
							#session_unregister('arbitrary'.$row_arbitraryfields['id']);
							
						#} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
					
					}
					?>
        	
        <?php
		}
		elseif ($_SESSION['provide_step'] == 9) { 
		
			wizardsummary(9);
			
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
			
			
			?>
			
			
            
            <h5>Summary</h5>
            
            <p><em>Here is a summary of the link that will be created if you choose to continue.  If you are an administrator, you can choose to save this data as a template rather than provide the link.  To edit the template, go to Devices &gt;&gt; Templates &gt;&gt; Service Templates after the template has been saved.</em></p>
            
            <table width="50%" border="0">
            
            <?php if ($_SESSION['additional_address'] != 1) { ?>
            	<tr bgcolor="#EAEAEA">
                
                	<td><strong>Node A</strong></td>
                    <td><?php echo $row_node_a['name']; ?></td>
                
                </tr>
                <tr bgcolor="#F5F5F5">
                
                	<td><strong>Node A Port</strong></td>
                    <td><?php if ($totalRows_card_node_a == 0) { echo "---"; } else { echo $row_card_node_a['cardtypename']; ?> <?php if (!(isset($row_card_node_a['rack'])) && !(isset($row_card_node_a['module'])) && !(isset($row_card_node_a['slot']))) { echo "Virtual"; } else { if (isset($row_card_node_a['rack'])) { echo $row_card_node_a['rack']."/"; } if (isset($row_card_node_a['module'])) { echo $row_card_node_a['module'].'/'; } if (isset($row_card_node_a['slot'])) { echo $row_card_node_a['slot']; } } ?>/<?php echo $_SESSION['provide_port_node_a']; ?><?php if ($_SESSION['provide_logical_node_a'] == "subint") { ?>.<?php if ($_SESSION['provide_subint_node_a'] == 0) { echo "AUTO"; } else { echo $_SESSION['provide_subint_node_a']; } ?><?php } ?><?php if ($_SESSION['provide_logical_node_a'] == "tdm") { ?> (<?php echo $_SESSION['provide_timeslots_node_a']; ?> timeslots <?php echo $row_card_node_a['slotbandwidth'] * $_SESSION['provide_timeslots_node_a']; ?>Kbps)<?php } } ?></td>
                
                </tr>
                <tr bgcolor="#EAEAEA">
                
                	<td><strong>Node A VLAN</strong></td>
                    <td><?php if ($_SESSION['provide_vlan_node_a'] == "") { echo "---"; } else { if (preg_match('/^auto_+/',$_SESSION['provide_vlan_node_a'])) { echo "AUTO"; } elseif (preg_match('/^manual_+/',$_SESSION['provide_vlan_node_a'])) { echo "MANUAL CREATION [".$_SESSION['vlannumber_a'].']'; } else { echo "[".$row_vlan_node_a['number']."] ".$row_vlan_node_a['name']; } }?></td>
                
                </tr>
                <tr bgcolor="#F5F5F5">
                
                	<td><strong>Node A Logical Interface Type</strong></td>
                    <td><?php if ($_SESSION['provide_logical_node_a'] == "normal") { echo "Normal"; } elseif ($_SESSION['provide_logical_node_a'] == "subint") { echo "Sub-Interface"; } elseif ($_SESSION['provide_logical_node_a'] == "tdm") { echo "TDM"; } ?></td>
                
                </tr>
                <tr bgcolor="EAEAEA">
                
                	<td><strong>Node B</strong></td>
                    <td><?php if ($totalRows_node_b == 0) { echo "---"; } else { echo $row_node_b['name']; } ?></td>
                
                </tr>
                <tr bgcolor="#F5F5F5">
                
                	<td><strong>Node B Port</strong></td>
                    <td><?php if ($totalRows_card_node_b == 0) { echo "---"; } else { echo $row_card_node_b['cardtypename']; ?> <?php if (!(isset($row_card_node_b['rack'])) && !(isset($row_card_node_b['module'])) && !(isset($row_card_node_b['slot']))) { echo "Virtual"; } else { if (isset($row_card_node_b['rack'])) { echo $row_card_node_b['rack']."/"; } if (isset($row_card_node_b['module'])) { echo $row_card_node_b['module'].'/'; } if (isset($row_card_node_b['slot'])) { echo $row_card_node_b['slot']; } } ?>/<?php echo $_SESSION['provide_port_node_b']; ?><?php if ($_SESSION['provide_logical_node_b'] == "subint") { ?>.<?php if ($_SESSION['provide_subint_node_a'] == 0) { echo "AUTO"; } else { echo $_SESSION['provide_subint_node_b']; } ?><?php } ?><?php if ($_SESSION['provide_logical_node_b'] == "tdm") { ?> (<?php echo $_SESSION['provide_timeslots_node_b']; ?> timeslots <?php echo $row_card_node_b['slotbandwidth'] * $_SESSION['provide_timeslots_node_b']; ?>Kbps)<?php } } ?></td>
                
                </tr>
                <tr bgcolor="#EAEAEA">
                
                	<td><strong>Node B VLAN</strong></td>
                    <td><?php if ($_SESSION['provide_vlan_node_b'] == "") { echo "---"; } else { if (preg_match('/^auto_+/',$_SESSION['provide_vlan_node_b'])) { echo "AUTO"; } elseif (preg_match('/^manual_+/',$_SESSION['provide_vlan_node_b'])) { echo "MANUAL CREATION [".$_SESSION['vlannumber_b'].']'; } else { if ($_SESSION['provide_vlan_node_b'] == "same") { echo "Same as node A"; } else { echo "[".$row_vlan_node_b['number']."] ".$row_vlan_node_b['name']; } } } ?></td>
                
                </tr>
                <tr bgcolor="#F5F5F5">
                
                	<td><strong>Node B Logical Interface Type</strong></td>
                    <td><?php if ($totalRows_node_b == 0) { echo "---"; } else { ?><?php if ($_SESSION['provide_logical_node_b'] == "normal") { echo "Normal"; } elseif ($_SESSION['provide_logical_node_b'] == "subint") { echo "Sub-Interface"; } elseif ($_SESSION['provide_logical_node_b'] == "tdm") { echo "TDM"; } ?><?php } ?></td>
                
                </tr>
                <tr bgcolor="#EAEAEA">
                
                	<td><strong>Port Layer</strong></td>
                    <td><?php if ($_SESSION['provide_layer'] == 2) { echo "Layer 2 Data Link"; } elseif ($_SESSION['provide_layer'] == 3) { echo "Layer 3 Network"; } ?></td>
                
                </tr>
                <tr bgcolor="#F5F5F5">
                
                	<td><strong>Circuit Reference</strong></td>
                    <td><?php echo $_SESSION['provide_cct']; ?></td>
                
                </tr>
            	<tr bgcolor="#EAEAEA">
                
                	<td><strong>Customer</strong></td>
                    <td><?php echo $row_customer['name']; ?></td>
                
                </tr>
                <tr bgcolor="#F5F5F5">
                
                	<td><strong>VPN</strong></td>
                    <td><?php if (preg_match('/^new_+/',$_SESSION['provide_vpn'])) { echo "AUTO CREATE"; } elseif ($totalRows_vpn == 0) { echo "---"; } else { echo "[".$row_vpn['asnumber']."] ".$row_vpn['providername']." &gt;&gt; ".$row_vpn['name']; }?></td>
                
                </tr>
                <tr bgcolor="#EAEAEA">
                
                	<td><strong>Pseudowire Pool</strong></td>
                    <td><?php if ($totalRows_xconnectpool == 0) { echo "---"; } else { echo "[".$row_xconnectpool['xconnectstart']." - ".$row_xconnectpool['xconnectend']."] ".$row_xconnectpool['descr']; }?>
                    <?php if ($_SESSION['xconnectid']) { ?><br />Pseudowire ID: <?php echo $_SESSION['xconnectid']; ?><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>Subject to validation</em><?php } ?>
                    </td>
                
                </tr>
                <tr bgcolor="#F5F5F5">
                
                	<td><strong>VRF</strong></td>
                    <td><?php if ($totalRows_vrf == 0) { echo "---"; } else { echo $row_vrf['name']; }?></td>
                
                </tr>
                
                <tr bgcolor="#F5F5F5">
                
                	<td><strong>PE-CE Option</strong></td>
                    <td><?php if ($_SESSION['pece'] == 0) { echo "---"; } else { echo "YES"; }?></td>
                
                </tr>
                
				<?php } # If not additional addresses ?>
                
                <tr bgcolor="#EAEAEA">
                
                	<td><strong>Selected Network</strong></td>
                    <td><?php if ($_SESSION['provide_network'] == "") { echo "---"; } else {  if ($row_getNetwork['v6mask'] == "") { echo long2ip($_SESSION['provide_network'])."/".$_SESSION['provide_netsize']; } else { echo Net_IPv6::Compress(long2ipv6($_SESSION['provide_network']))."/".$_SESSION['provide_netsize'];} }?><?php if ($_SESSION['provide_existing'] == 1) { ?> (existing)<?php } ?>
                    	<?php if ($_SESSION['provide_address'] == 1) { ?><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Node A: <?php echo $_SESSION['provide_address_node_a']; ?><?php if ($_SESSION['provide_node_b'] != "") { ?><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Node B: <?php echo $_SESSION['provide_address_node_b']; ?><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>Manual addressing is subject to validation</em><?php } ?>
						<?php } ?></td>
                
                </tr>
            </table>
			
			<?php mysql_select_db($database_subman, $subman);
			$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
			$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
			$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
			$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
	
			if ($totalRows_arbitraryfields > 0) { ?>
			
				<h5>Arbitrary Fields</h5>
				
                
				<?php do {
			
					echo "<strong>".$row_arbitraryfields['title'].":</strong> ".$_SESSION['arbitrary'.$row_arbitraryfields['id']]."<br />";
					
				} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields)); ?>
				
				<p>&nbsp;</p>
			
			<?php } ?>
	
            <form action="handler.php" method="post" target="_self" id="providelink" name="providelink8">
            
            	<input type="hidden" name="provide" value="step9" />
                <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                <input name="print" type="button" class="input_standard" id="print" onClick="MM_callJS('window.print();')" value="Print" />
            	<br /><br />
                <a href="#" onClick="document.getElementById('providelink').submit();" class="input_providewizard">
								Continue</a>
						
							<a href="handler.php?action=provide_cancel&amp;container=<?php echo $_GET['container']; ?>" <?php if ($_SESSION['linked'] != "") { ?>onclick="if (window.confirm('This link is required by the template.  If you cancel now, only a superuser can provide the rest of this link.  Alternatively, you can cancel, and delete any other links associated with this template, and then re-provide all links.')) { } else { return false; }" <?php } ?>  class="input_providewizard">Cancel</a>
						
            </form>
            
            <?php if ($ipm->getPageLevel(2,$_SESSION['MM_Username']) == 127 && !isset($_SESSION['additional_address'])) { ?>
            <br /><br /><form action="" method="post" target="_self" name="savetemplate">
            
            	<h5>Save as service template...</h5>
            	<p>Template Name: <input type="text" name="servicename" class="input_standard" size="32" maxlength="255" /></p>
                <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                <input type="submit" value="Save" class="input_standard" />
                <input type="hidden" name="saveservicetemplate" value="1" />
            
            </form><br />
            
            <?php } ?>
        <?php	
		}
		elseif ($_SESSION['provide_step'] == 8) { 
			
			wizardsummary(8);
			
			mysql_select_db($database_subman, $subman);
			$query_provide_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
			$provide_template = mysql_query($query_provide_template, $subman) or die(mysql_error());
			$row_provide_template = mysql_fetch_assoc($provide_template);
			$totalRows_provide_template = mysql_num_rows($provide_template);
			
			mysql_select_db($database_subman, $subman);
			$query_provide_card_node_a = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = '".$_SESSION['provide_card_node_a']."'";
			$provide_card_node_a = mysql_query($query_provide_card_node_a, $subman) or die(mysql_error());
			$row_provide_card_node_a = mysql_fetch_assoc($provide_card_node_a);
			$totalRows_provide_card_node_a = mysql_num_rows($provide_card_node_a);
			?>
					   
			
            
            <?php if ($row_provide_template['step8prompt']) { ?>
    
    	<div id="provide_prompt"><?php echo $row_provide_template['step8prompt']; ?></div>
    
    <?php } 
    	
    		mysql_select_db($database_subman, $subman);
			$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
			$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
			$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
			$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
				?>
    
            <form action="handler.php" method="post" target="_self" name="providelink7" id="providelink" onSubmit="MM_validateForm('port_node_a','','R'<?php if ($_SESSION['provide_node_b'] != "") { ?>,'port_node_b','','R'<?php } ?><?php if ($totalRows_arbitraryfields > 0) { do { ?>,'arbitrary<?php echo $row_arbitraryfields['id']; ?>','','R'<?php } while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields)); } ?>);return document.MM_returnValue">
            
            	<h5>Port Allocation - Node A Port</h5>
            	
                <select name="port_node_a" class="input_standard" size="8">
                <?php for ($i = $row_provide_card_node_a['startport']; $i <= $row_provide_card_node_a['endport']; $i ++) { 
                	mysql_select_db($database_subman, $subman);
					$query_provide_ports_node_a = "SELECT * FROM portsports WHERE portsports.card = '".$_SESSION['provide_card_node_a']."' AND portsports.port = ".$i."";
					$provide_ports_node_a = mysql_query($query_provide_ports_node_a, $subman) or die(mysql_error());
					$row_provide_ports_node_a = mysql_fetch_assoc($provide_ports_node_a);
					$totalRows_provide_ports_node_a = mysql_num_rows($provide_ports_node_a);
					
					if ($totalRows_provide_ports_node_a == 0) { ?>
						<option value="<?php echo $i; ?>"><?php echo $row_provide_card_node_a['cardtypename']; ?> <?php if (!(isset($row_provide_card_node_a['rack'])) && !(isset($row_provide_card_node_a['module'])) && !(isset($row_provide_card_node_a['slot']))) { echo "Virtual"; } else { if (isset($row_provide_card_node_a['rack'])) { echo $row_provide_card_node_a['rack']."/"; } if (isset($row_provide_card_node_a['module'])) { echo $row_provide_card_node_a['module'].'/'; } if (isset($row_provide_card_node_a['slot'])) { echo $row_provide_card_node_a['slot']; } } ?>/<?php echo $i; ?></option>
					<?php }
					elseif ($_SESSION['provide_logical_node_a'] == "tdm") {
						mysql_select_db($database_subman, $subman);
						$query_provide_timeslots_node_a = "SELECT * FROM timeslots WHERE timeslots.port = ".$row_provide_ports_node_a['port']." AND timeslots.card = ".$_SESSION['provide_card_node_a']."";
						$provide_timeslots_node_a = mysql_query($query_provide_timeslots_node_a, $subman) or die(mysql_error());
						$row_provide_timeslots_node_a = mysql_fetch_assoc($provide_timeslots_node_a);
						$totalRows_provide_timeslots_node_a = mysql_num_rows($provide_timeslots_node_a);
						
						if ($totalRows_provide_ports_node_a > 0 && (($totalRows_provide_timeslots_node_a + $_SESSION['provide_timeslots_node_a']) <= $row_provide_card_node_a['timeslots'])) { ?>
							<option value="<?php echo $i; ?>"><?php echo $row_provide_card_node_a['cardtypename']; ?> <?php if (!(isset($row_provide_card_node_a['rack'])) && !(isset($row_provide_card_node_a['module'])) && !(isset($row_provide_card_node_a['slot']))) { echo "Virtual"; } else { if (isset($row_provide_card_node_a['rack'])) { echo $row_provide_card_node_a['rack']."/"; } if (isset($row_provide_card_node_a['module'])) { echo $row_provide_card_node_a['module'].'/'; } if (isset($row_provide_card_node_a['slot'])) { echo $row_provide_card_node_a['slot']; } } ?>/<?php echo $i; ?> (<?php echo $totalRows_provide_timeslots_node_a; ?> timeslots used)</option>
                        <?php }
					}
					elseif ($_SESSION['provide_logical_node_a'] == "subint") {
						mysql_select_db($database_subman, $subman);
						$query_provide_subints_node_a = "SELECT * FROM subint WHERE subint.port = ".$row_provide_ports_node_a['id']."";
						$provide_subints_node_a = mysql_query($query_provide_subints_node_a, $subman) or die(mysql_error());
						$row_provide_subints_node_a = mysql_fetch_assoc($provide_subints_node_a);
						$totalRows_provide_subints_node_a = mysql_num_rows($provide_subints_node_a);
						
						mysql_select_db($database_subman, $subman);
						$query_provide_subint_node_a = "SELECT * FROM subint WHERE subint.port = ".$row_provide_ports_node_a['id']." AND subint.subint = ".$_SESSION['provide_subint_node_a']."";
						$provide_subint_node_a = mysql_query($query_provide_subint_node_a, $subman) or die(mysql_error());
						$row_provide_subint_node_a = mysql_fetch_assoc($provide_subint_node_a);
						$totalRows_provide_subint_node_a = mysql_num_rows($provide_subint_node_a);
						
						if ($totalRows_provide_ports_node_a > 0 && $totalRows_provide_subint_node_a == 0 && $totalRows_provide_subints_node_a > 0) { ?>
							<option value="<?php echo $i; ?>"><?php echo $row_provide_card_node_a['cardtypename']; ?> <?php if (!(isset($row_provide_card_node_a['rack'])) && !(isset($row_provide_card_node_a['module'])) && !(isset($row_provide_card_node_a['slot']))) { echo "Virtual"; } else { if (isset($row_provide_card_node_a['rack'])) { echo $row_provide_card_node_a['rack']."/"; } if (isset($row_provide_card_node_a['module'])) { echo $row_provide_card_node_a['module'].'/'; } if (isset($row_provide_card_node_a['slot'])) { echo $row_provide_card_node_a['slot']; } } ?>/<?php echo $i; ?> (<?php echo $totalRows_provide_subints_node_a; ?> sub-interface(s) already exist)</option>
                        <?php }
					}
				} ?>
                </select>
                
                <?php if ($_SESSION['provide_node_b'] != "") { 
					
					mysql_select_db($database_subman, $subman);
					$query_provide_card_node_b = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = '".$_SESSION['provide_card_node_b']."'";
					$provide_card_node_b = mysql_query($query_provide_card_node_b, $subman) or die(mysql_error());
					$row_provide_card_node_b = mysql_fetch_assoc($provide_card_node_b);
					$totalRows_provide_card_node_b = mysql_num_rows($provide_card_node_b);
					?>
                
                <h5>Port Allocation - Node B Port</h5>
            	
                <select name="port_node_b" class="input_standard" size="8">
                <?php for ($i = $row_provide_card_node_b['startport']; $i <= $row_provide_card_node_b['endport']; $i ++) { 
                	 
					mysql_select_db($database_subman, $subman);
					$query_provide_ports_node_b = "SELECT * FROM portsports WHERE portsports.card = '".$_SESSION['provide_card_node_b']."' AND portsports.port = ".$i."";
					echo $query_provide_ports_node_b;
					$provide_ports_node_b = mysql_query($query_provide_ports_node_b, $subman) or die(mysql_error());
					$row_provide_ports_node_b = mysql_fetch_assoc($provide_ports_node_b);
					$totalRows_provide_ports_node_b = mysql_num_rows($provide_ports_node_b);
					
					?>
                    	<?php if ($totalRows_provide_ports_node_b == 0) { ?>
                        	<option value="<?php echo $i; ?>"><?php echo $row_provide_card_node_b['cardtypename']; ?> <?php if (!(isset($row_provide_card_node_b['rack'])) && !(isset($row_provide_card_node_b['module'])) && !(isset($row_provide_card_node_b['slot']))) { echo "Virtual"; } else { if (isset($row_provide_card_node_b['rack'])) { echo $row_provide_card_node_b['rack']."/"; } if (isset($row_provide_card_node_b['module'])) { echo $row_provide_card_node_b['module'].'/'; } if (isset($row_provide_card_node_b['slot'])) { echo $row_provide_card_node_b['slot']; } } ?>/<?php echo $i; ?></option>
                        <?php }
                        elseif ($_SESSION['provide_logical_node_b'] == "tdm") {
						mysql_select_db($database_subman, $subman);
						$query_provide_timeslots_node_b = "SELECT * FROM timeslots WHERE timeslots.port = ".$row_provide_ports_node_b['port']." AND timeslots.card = ".$_SESSION['provide_card_node_b']."";
						$provide_timeslots_node_b = mysql_query($query_provide_timeslots_node_b, $subman) or die(mysql_error());
						$row_provide_timeslots_node_b = mysql_fetch_assoc($provide_timeslots_node_b);
						$totalRows_provide_timeslots_node_b = mysql_num_rows($provide_timeslots_node_b);
						
						if ($totalRows_provide_ports_node_b > 0 && (($totalRows_provide_timeslots_node_b + $_SESSION['provide_timeslots_node_b']) <= $row_provide_card_node_b['timeslots'])) { ?>
							<option value="<?php echo $i; ?>"><?php echo $row_provide_card_node_b['cardtypename']; ?> <?php if (!(isset($row_provide_card_node_b['rack'])) && !(isset($row_provide_card_node_b['module'])) && !(isset($row_provide_card_node_b['slot']))) { echo "Virtual"; } else { if (isset($row_provide_card_node_b['rack'])) { echo $row_provide_card_node_b['rack']."/"; } if (isset($row_provide_card_node_b['module'])) { echo $row_provide_card_node_b['module'].'/'; } if (isset($row_provide_card_node_b['slot'])) { echo $row_provide_card_node_b['slot']; } } ?>/<?php echo $i; ?> (<?php echo $totalRows_provide_timeslots_node_b; ?> timeslots used)</option>
                        <?php }
					}
					elseif ($_SESSION['provide_logical_node_b'] == "subint") {
						mysql_select_db($database_subman, $subman);
						$query_provide_subints_node_b = "SELECT * FROM subint WHERE subint.port = ".$row_provide_ports_node_b['id']."";
						$provide_subints_node_b = mysql_query($query_provide_subints_node_b, $subman) or die(mysql_error());
						$row_provide_subints_node_b = mysql_fetch_assoc($provide_subints_node_b);
						$totalRows_provide_subints_node_b = mysql_num_rows($provide_subints_node_b);
						
						mysql_select_db($database_subman, $subman);
						$query_provide_subint_node_b = "SELECT * FROM subint WHERE subint.port = ".$row_provide_ports_node_b['id']." AND subint.subint = ".$_SESSION['provide_subint_node_b']."";
						$provide_subint_node_b = mysql_query($query_provide_subint_node_b, $subman) or die(mysql_error());
						$row_provide_subint_node_b = mysql_fetch_assoc($provide_subint_node_b);
						$totalRows_provide_subint_node_b = mysql_num_rows($provide_subint_node_b);
						
						if ($totalRows_provide_ports_node_b > 0 && $totalRows_provide_subint_node_b == 0 && $totalRows_provide_subints_node_b > 0) { ?>
							<option value="<?php echo $i; ?>"><?php echo $row_provide_card_node_b['cardtypename']; ?> <?php if (!(isset($row_provide_card_node_b['rack'])) && !(isset($row_provide_card_node_b['module'])) && !(isset($row_provide_card_node_b['slot']))) { echo "Virtual"; } else { if (isset($row_provide_card_node_b['rack'])) { echo $row_provide_card_node_b['rack']."/"; } if (isset($row_provide_card_node_b['module'])) { echo $row_provide_card_node_b['module'].'/'; } if (isset($row_provide_card_node_b['slot'])) { echo $row_provide_card_node_b['slot']; } } ?>/<?php echo $i; ?> (<?php echo $totalRows_provide_subints_node_b; ?> sub-interface(s) already exist)</option>
                        <?php }
					}
                 } ?>
                </select>
            	
            	<?php } ?>
                
                <?php 	
                
                mysql_select_db($database_subman, $subman);
				$query_arbitraryfields = "SELECT * FROM arbitraryfields WHERE servicetemplate = '".$_SESSION['provide_template']."' ORDER BY arbitraryfields.title";
				$arbitraryfields = mysql_query($query_arbitraryfields, $subman) or die(mysql_error());
				$row_arbitraryfields = mysql_fetch_assoc($arbitraryfields);
				$totalRows_arbitraryfields = mysql_num_rows($arbitraryfields);
			
				if ($totalRows_arbitraryfields > 0) { ?>
				
				<h5>Arbitrary Fields</h5>
				 
				<table width="100%">
					<tr>
						<td><strong>Field Title</strong></td>
						<td><strong>Value</strong></td>
					</tr>
				
			<?php 
				do { ?>
				
					<tr>
						<td width="20%"><strong><?php echo $row_arbitraryfields['title']; ?></strong><br /><em><?php echo $row_arbitraryfields['hint']; ?></em></td>
						<td><?php if ($row_arbitraryfields['fieldtype'] == "text") { ?>
							<input type="text" maxlength="255" size="20" class="input_standard" name="arbitrary<?php echo $row_arbitraryfields['id']; ?>" id="arbitrary<?php echo $row_arbitraryfields['id']; ?>" value="<?php if ($_SESSION['arbitrary'.$row_arbitraryfields['id']] != "") { echo $_SESSION['arbitrary'.$row_arbitraryfields['id']]; } else { echo $row_arbitraryfields['value']; } ?>">
							<?php } ?>
							<?php if ($row_arbitraryfields['fieldtype'] == "textarea") { ?>
							<textarea cols="50" rows="10" class="input_standard" name="arbitrary<?php echo $row_arbitraryfields['id']; ?>" id="arbitrary<?php echo $row_arbitraryfields['id']; ?>"><?php if ($_SESSION['arbitrary'.$row_arbitraryfields['id']] != "") { echo $_SESSION['arbitrary'.$row_arbitraryfields['id']]; } else { echo $row_arbitraryfields['value']; } ?></textarea>
							<?php } ?>
						</td>
					</tr>
					
						<?php	
						} while ($row_arbitraryfields = mysql_fetch_assoc($arbitraryfields));
						?>
					
				</table>
				
				<?php } ?>
				
	            <input type="hidden" name="provide" value="step8" />
                <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
            	<br /><br />
							<a href="#" onClick="if (document.getElementById('providelink').onsubmit()) { document.getElementById('providelink').submit(); } " class="input_providewizard">
								Next</a>
						
							<a href="handler.php?action=provide_cancel&amp;container=<?php echo $_GET['container']; ?>" <?php if ($_SESSION['linked'] != "") { ?>onclick="if (window.confirm('This link is required by the template.  If you cancel now, only a superuser can provide the rest of this link.  Alternatively, you can cancel, and delete any other links associated with this template, and then re-provide all links.')) { } else { return false; }" <?php } ?> class="input_providewizard">Cancel</a>
						
            </form><br />		   

		<?php		
		}
		elseif ($_SESSION['provide_step'] == 7) {
		
			wizardsummary(7);
			
			mysql_select_db($database_subman, $subman);
			$query_provide_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
			$provide_template = mysql_query($query_provide_template, $subman) or die(mysql_error());
			$row_provide_template = mysql_fetch_assoc($provide_template);
			$totalRows_provide_template = mysql_num_rows($provide_template);
			
			mysql_select_db($database_subman, $subman);
			if ($_SESSION['provide_logical_node_a'] == "tdm") {
				
				if ($row_provide_template['provide_card_node_a_default'] == 'card' || $totalRows_provide_template == 0) {
					$query_provide_cards_node_a = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.device = ".$_SESSION['provide_node_a']." AND cards.timeslots > 0 ORDER BY cards.rack, cards.module, cards.slot";
				}
				elseif ($row_provide_template['provide_card_node_a_default'] == 'cardtype') {
					$query_provide_cards_node_a = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.device = ".$_SESSION['provide_node_a']." AND cards.timeslots > 0 AND cards.cardtype = '".$row_provide_template['provide_card_node_a_default_id']."' ORDER BY cards.rack, cards.module, cards.slot";
				}
				
			}
			else {
			
				if ($row_provide_template['provide_card_node_a_default'] == 'card' || $totalRows_provide_template == 0) {
					$query_provide_cards_node_a = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.device = ".$_SESSION['provide_node_a']." AND (cards.timeslots <= 0 || cards.timeslots IS NULL) ORDER BY cards.rack, cards.module, cards.slot";
				}
				elseif ($row_provide_template['provide_card_node_a_default'] == 'cardtype') {
					$query_provide_cards_node_a = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.device = ".$_SESSION['provide_node_a']." AND (cards.timeslots <= 0 || cards.timeslots IS NULL) AND cards.cardtype = '".$row_provide_template['provide_card_node_a_default_id']."' ORDER BY cards.rack, cards.module, cards.slot";
				}
			
			}
			$provide_cards_node_a = mysql_query($query_provide_cards_node_a, $subman) or die(mysql_error());
			$row_provide_cards_node_a = mysql_fetch_assoc($provide_cards_node_a);
			$totalRows_provide_cards_node_a = mysql_num_rows($provide_cards_node_a);

			?>
        
        	
         	
            <?php if ($row_provide_template['step7prompt']) { ?>
    
    	<div id="provide_prompt"><?php echo $row_provide_template['step7prompt']; ?></div>
    
    <?php } ?>
    
            <form action="handler.php" method="post" target="_self" name="providelink6" id="providelink" onSubmit="MM_validateForm(<?php if ($_SESSION['provide_logical_node_a'] == "subint") { ?>'subint_node_a','','RIsNum'<?php } ?><?php if ($_SESSION['provide_logical_node_a'] == "subint" && $_SESSION['provide_logical_node_b'] == "subint" && $_SESSION['provide_node_b'] != "") { echo ","; } ?><?php if ($_SESSION['provide_node_b'] != "" && $_SESSION['provide_logical_node_b'] == "subint") { ?>'subint_node_b','','RIsNum'<?php } if ($_SESSION['provide_address'] == 1 && ($_SESSION['provide_logical_node_a'] == "subint" || $_SESSION['provide_logical_node_b'] == "subint")) { echo ","; } if ($_SESSION['provide_address'] == 1) { ?>'address_node_a','','R'<?php if ($_SESSION['provide_node_b'] != "") { ?>,'address_node_b','','R'<?php } } ?>);return document.MM_returnValue">
            
            <?php if ($_SESSION['provide_address'] == 1) { ?>
                
            	<h5>Node A Address:</h5>
                <input type="text" name="address_node_a" class="input_standard" size="32" maxlength="35" id="address_node_a" />
                
                <?php if ($_SESSION['provide_node_b'] != "") { ?>
                
                    <h5>Node B Address:</h5>
                    <input type="text" name="address_node_b" class="input_standard" size="32" maxlength="35" id="address_node_b" />
            	
                <?php } ?>
                
                <br /><em>If you enter an invalid address, or the address is not available on the subnet, IP Manager will revert to automatically allocating the addresses.</em>
				
			<?php } ?>
                
                <?php if ($_SESSION['additional_address'] != 1) {
					
					?>
                    
            	<h5>Port Allocation - Node A Line Card</h5>

                  <?php
			
if ($totalRows_provide_cards_node_a > 0) { ?>                
<select name="card_node_a" class="input_standard" <?php if ($row_provide_template['provide_card_node_a_editable'] == "0") { echo "disabled"; } ?>>
                  <?php
do {
	mysql_select_db($database_subman, $subman);
	$query_portCount = "SELECT * FROM portsports WHERE portsports.card = '".$row_provide_cards_node_a['id']."'";
	$portCount = mysql_query($query_portCount, $subman) or die(mysql_error());
	$row_portCount = mysql_fetch_assoc($portCount);
	$totalRows_portCount = mysql_num_rows($portCount);
	
	if ($_SESSION['provide_logical_node_a'] == "subint") {
		mysql_select_db($database_subman, $subman);
		$query_portCountSubint = "select * from portsports inner join subint on subint.`port` = portsports.`id` where portsports.`card` = '".$row_provide_cards_node_a['id']."'";
		$portCountSubint = mysql_query($query_portCountSubint, $subman) or die(mysql_error());
		$row_portCountSubint = mysql_fetch_assoc($portCountSubint);
		$totalRows_portCountSubint = mysql_num_rows($portCountSubint);
	}
	
	if ($_SESSION['provide_logical_node_a'] == "tdm" || ((($row_provide_cards_node_a['endport'] - $row_provide_cards_node_a['startport']) + 1) - $totalRows_portCount) > 0 || ($_SESSION['provide_logical_node_a'] == "subint" && $totalRows_portCountSubint > 0)) {
?>
                  <option value="<?php echo $row_provide_cards_node_a['id']?>" <?php if ($row_provide_cards_node_a['id'] == $row_provide_template['provide_card_node_a']) { echo "selected='selected'"; } ?>><?php echo $row_provide_cards_node_a['cardtypename']?> <?php if (!(isset($row_provide_cards_node_a['rack'])) && !(isset($row_provide_cards_node_a['module'])) && !(isset($row_provide_cards_node_a['slot']))) { echo "Virtual"; } else { if (isset($row_provide_cards_node_a['rack'])) { echo $row_provide_cards_node_a['rack']."/"; } if (isset($row_provide_cards_node_a['module'])) { echo $row_provide_cards_node_a['module'].'/'; } if (isset($row_provide_cards_node_a['slot'])) { echo $row_provide_cards_node_a['slot']; } } ?> [<?php echo $row_provide_cards_node_a['startport']; ?> - <?php echo $row_provide_cards_node_a['endport']; ?>]</option>
                  <?php
	}
	
} while ($row_provide_cards_node_a = mysql_fetch_assoc($provide_cards_node_a));
  $rows = mysql_num_rows($provide_cards_node_a);
  if($rows > 0) {
      mysql_data_seek($provide_cards_node_a, 0);
	  $row_provide_cards_node_a = mysql_fetch_assoc($provide_cards_node_a);
  }
?>                
                
              </select>
              
              <?php if ($row_provide_template['provide_card_node_a_editable'] == "0") { ?>
                	<input type="hidden" name="card_node_a" value="<?php echo $row_provide_template['provide_card_node_a']; ?>" />                
                <?php } ?>
                
                
                <?php if ($_SESSION['provide_logical_node_a'] == "tdm") { ?>
                Timeslots: 
                <select name="timeslots_node_a" class="input_standard" <?php if ($row_provide_template['provide_timeslots_node_a_editable'] == "0") { echo "disabled"; } ?>>
                
                	<?php for ($i = 1; $i <= $row_provide_cards_node_a['timeslots']; $i ++) { ?>
                    
               	  <option value="<?php echo $i; ?>" <?php if ($i == $row_provide_template['provide_timeslots_node_a']) { echo "selected='selected'"; } ?>><?php echo $i; ?> (<strong><?php echo $row_provide_cards_node_a['slotbandwidth'] * $i; ?></strong>Kbps)</option>
                    
                    <?php } ?>
                
                </select>
                
                <?php if ($row_provide_template['provide_timeslots_node_a_editable'] == "0") { ?>
                	<input type="hidden" name="timeslots_node_a" value="<?php echo $row_provide_template['provide_timeslots_node_a']; ?>" />                
                <?php } ?>
                
                <?php } ?>
                <?php if ($_SESSION['provide_logical_node_a'] == "subint") { ?>
                Sub-Interface Number: 
                <?php if ($row_provide_template['provide_subint_node_a_auto'] == "0") { ?>
                <input type="hidden" name="subint_node_a" value="0" /><strong>***FORCED AUTO***</strong>
                <?php } else { ?>
                <input type="text" size="10" maxlength="5" name="subint_node_a" class="input_standard" value="0">
                <p><em>Leave the sub-interface number as '0' if you want IP Manager to decide which sub-interface to use.  If a VLAN has been selected, IP Manager will try and match the sub-interface number with the VLAN.</em></p>
                <?php } ?>
                <?php } ?>
<?php
}
else { ?>
	<p>There are no valid line cards available on this device.</p>
<?php } ?>

                <?php if ($_SESSION['provide_node_b'] != "") { 
				
					mysql_select_db($database_subman, $subman);
					if ($_SESSION['provide_logical_node_b'] == "tdm") {
				
						if ($row_provide_template['provide_card_node_b_default'] == 'card' || $totalRows_provide_template == 0) {
							$query_provide_cards_node_b = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.device = ".$_SESSION['provide_node_b']." AND cards.timeslots > 0 ORDER BY cards.rack, cards.module, cards.slot";
						}
						elseif ($row_provide_template['provide_card_node_b_default'] == 'cardtype') {
							$query_provide_cards_node_b = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.device = ".$_SESSION['provide_node_b']." AND cards.timeslots > 0 AND cards.cardtype = '".$row_provide_template['provide_card_node_b_default_id']."' ORDER BY cards.rack, cards.module, cards.slot";
						}
				
					}
					else {
			
						if ($row_provide_template['provide_card_node_b_default'] == 'card' || $totalRows_provide_template == 0) {
							$query_provide_cards_node_b = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.device = ".$_SESSION['provide_node_b']." AND (cards.timeslots <= 0 || cards.timeslots IS NULL) ORDER BY cards.rack, cards.module, cards.slot";
						}
						elseif ($row_provide_template['provide_card_node_b_default'] == 'cardtype') {
							$query_provide_cards_node_b = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.device = ".$_SESSION['provide_node_b']." AND (cards.timeslots <= 0 || cards.timeslots IS NULL) AND cards.cardtype = '".$row_provide_template['provide_card_node_b_default_id']."' ORDER BY cards.rack, cards.module, cards.slot";
						}
			
					}
					$provide_cards_node_b = mysql_query($query_provide_cards_node_b, $subman) or die(mysql_error());
					$row_provide_cards_node_b = mysql_fetch_assoc($provide_cards_node_b);
					$totalRows_provide_cards_node_b = mysql_num_rows($provide_cards_node_b);
					?>
                
                <h5>Port Allocation - Node B Line Card</h5>

<?php if ($totalRows_provide_cards_node_b > 0) { ?>                
                <select name="card_node_b" class="input_standard" <?php if ($row_provide_template['provide_card_node_b_editable'] == "0") { echo "disabled"; } ?>>
                  <?php
do {  
	
	mysql_select_db($database_subman, $subman);
	$query_portCount = "SELECT * FROM portsports WHERE portsports.card = '".$row_provide_cards_node_b['id']."'";
	$portCount = mysql_query($query_portCount, $subman) or die(mysql_error());
	$row_portCount = mysql_fetch_assoc($portCount);
	$totalRows_portCount = mysql_num_rows($portCount);
	
	if ($_SESSION['provide_logical_node_b'] == "subint") {
		mysql_select_db($database_subman, $subman);
		$query_portCountSubint = "select * from portsports inner join subint on subint.`port` = portsports.`id` where portsports.`card` = '".$row_provide_cards_node_b['id']."'";
		$portCountSubint = mysql_query($query_portCountSubint, $subman) or die(mysql_error());
		$row_portCountSubint = mysql_fetch_assoc($portCountSubint);
		$totalRows_portCountSubint = mysql_num_rows($portCountSubint);
	}
	
	if ($_SESSION['provide_logical_node_b'] == "tdm" || ((($row_provide_cards_node_b['endport'] - $row_provide_cards_node_b['startport']) + 1) - $totalRows_portCount) > 0 || ($_SESSION['provide_logical_node_a'] == "subint" && $totalRows_portCountSubint > 0)) {
?>
                  <option value="<?php echo $row_provide_cards_node_b['id']?>" <?php if ($row_provide_cards_node_b['id'] == $row_provide_template['provide_card_node_b']) { echo "selected='selected'"; } ?>><?php echo $row_provide_cards_node_b['cardtypename']?> <?php if (!(isset($row_provide_cards_node_b['rack'])) && !(isset($row_provide_cards_node_b['module'])) && !(isset($row_provide_cards_node_b['slot']))) { echo "Virtual"; } else { if (isset($row_provide_cards_node_b['rack'])) { echo $row_provide_cards_node_b['rack']."/"; } if (isset($row_provide_cards_node_b['module'])) { echo $row_provide_cards_node_b['module'].'/'; } if (isset($row_provide_cards_node_b['slot'])) { echo $row_provide_cards_node_b['slot']; } } ?> [<?php echo $row_provide_cards_node_b['startport']; ?> - <?php echo $row_provide_cards_node_b['endport']; ?>]</option>
                  <?php
	}
	
} while ($row_provide_cards_node_b = mysql_fetch_assoc($provide_cards_node_b));
  $rows = mysql_num_rows($provide_cards_node_b);
  if($rows > 0) {
      mysql_data_seek($provide_cards_node_b, 0);
	  $row_provide_cards_node_b = mysql_fetch_assoc($provide_cards_node_b);
?>                
                
                </select>
                
                <?php if ($row_provide_template['provide_card_node_b_editable'] == "0") { ?>
                	<input type="hidden" name="card_node_b" value="<?php echo $row_provide_template['provide_card_node_b']; ?>" />                
                <?php } ?>
                
                <?php if ($_SESSION['provide_logical_node_b'] == "tdm") { ?>
                Timeslots:
                <select name="timeslots_node_b" class="input_standard" <?php if ($row_provide_template['provide_timeslots_node_b_editable'] == "0") { echo "disabled"; } ?>>
                
                	<?php for ($i = 1; $i <= $row_provide_cards_node_b['timeslots']; $i ++) { ?>
                    
               	  <option value="<?php echo $i; ?>" <?php if ($i == $row_provide_template['provide_timeslots_node_b']) { echo "selected='selected'"; } ?>><?php echo $i; ?> (<strong><?php echo $row_provide_cards_node_b['slotbandwidth'] * $i; ?></strong>Kbps)</option>
                    
                    <?php } ?>
                
                </select>
                
                <?php if ($row_provide_template['provide_timeslots_node_b_editable'] == "0") { ?>
                	<input type="hidden" name="timeslots_node_b" value="<?php echo $row_provide_template['provide_timeslots_node_b']; ?>" />                
                <?php } ?>
                
                <?php } ?>
                <?php if ($_SESSION['provide_logical_node_b'] == "subint") { ?>
                Sub-Interface Number: 
                <?php if ($row_provide_template['provide_subint_node_b_auto'] == "0") { ?>
                <input type="hidden" name="subint_node_b" value="0" /><strong>***FORCED AUTO***</strong>
                <?php } else { ?>
                <input type="text" size="10" maxlength="5" name="subint_node_b" class="input_standard" value="0">
                <p><em>Leave the sub-interface number as '0' if you want IP Manager to decide which sub-interface to use.  If a VLAN has been selected, IP Manager will try and match the sub-interface number with the VLAN.</em></p>
                <?php } ?>
                <?php } ?>
<?php
  }
else { ?>
	<p>There are no valid line cards available on this device.</p>
<?php } ?>
                
                <?php } ?>
                <?php } ?>
                
                <?php } #If additional address = 0 ?>
                
            	<input type="hidden" name="provide" value="step7" />
                <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                <?php if ($_SESSION['additional_address'] != 1 || (($totalRows_provide_cards_node_a > 0 && $_SESSION['provide_node_b'] == "") || ($totalRows_provide_cards_node_a > 0 && $totalRows_provide_cards_node_b > 0 && $_SESSION['provide_node_b'] != ""))) { ?>
            	<br /><br />
							<a href="#" onClick="if (document.getElementById('providelink').onsubmit()) { document.getElementById('providelink').submit(); } " class="input_providewizard">
								Next</a>
						
                <?php } elseif ($_SESSION['additional_address'] == 1) { ?>
                <br /><br />
							<a href="#" onClick="if (document.getElementById('providelink').onsubmit()) { document.getElementById('providelink').submit(); } " class="input_providewizard">
								Next</a>
						
				<?php } ?>
				
							<a href="handler.php?action=provide_cancel&amp;container=<?php echo $_GET['container']; ?>" <?php if ($_SESSION['linked'] != "") { ?>onclick="if (window.confirm('This link is required by the template.  If you cancel now, only a superuser can provide the rest of this link.  Alternatively, you can cancel, and delete any other links associated with this template, and then re-provide all links.')) { } else { return false; }" <?php } ?> class="input_providewizard">Cancel</a>
						
            </form><br />
            
        
        <?php }
		
		elseif ($_SESSION['provide_step'] == 6) { 
			
			wizardsummary(6);
			
			mysql_select_db($database_subman, $subman);
			$query_provide_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
			$provide_template = mysql_query($query_provide_template, $subman) or die(mysql_error());
			$row_provide_template = mysql_fetch_assoc($provide_template);
			$totalRows_provide_template = mysql_num_rows($provide_template);
			
			mysql_select_db($database_subman, $subman);
			$query_parent = "SELECT * FROM networks WHERE networks.id = '".$_SESSION['provide_parent']."' AND container = ".$_GET['container']."";
			$parent = mysql_query($query_parent, $subman) or die(mysql_error());
			$row_parent = mysql_fetch_assoc($parent);
			$totalRows_parent = mysql_num_rows($parent);
			
			mysql_select_db($database_subman, $subman);
			$query_parent_addresses = "SELECT * FROM addresses WHERE addresses.network = '".$_SESSION['provide_parent']."'";
			$parent_addresses = mysql_query($query_parent_addresses, $subman) or die(mysql_error());
			$row_parent_addresses = mysql_fetch_assoc($parent_addresses);
			$totalRows_parent_addresses = mysql_num_rows($parent_addresses);
			
			?>
        
        	
            
             <?php if ($row_provide_template['step6prompt']) { ?>
    
    	<div id="provide_prompt"><?php echo $row_provide_template['step6prompt']; ?></div>
    
    <?php } ?>
             
             <?php if ($_SESSION['provide_layer'] == 3 && $_SESSION['provide_existing'] != 1) { ?>
             
             <form action="handler.php" name="providelink4" id="providelink4" method="post" target="_self">
            	
                <h5>Network Mask</h5>
                
                
                
                <select name="netsize" class="input_standard" <?php if ($row_provide_template['provide_netgroup_editable'] == "0" && $row_provide_template['provide_netgroup'] != "" && $row_provide_template['provide_parent_editable'] == "0" && $row_provide_template['provide_netsize_editable'] == "0") { echo "disabled"; } ?> onChange="document.getElementById('providelink4').submit()">
                
                <?php if ($row_parent['v6mask'] == "") { 
				
					if ($_SESSION['provide_netsize'] == "") {
						$_SESSION['provide_netsize'] = 30;
					}
					?>
                	
                	<?php for ($i=8; $i<32; $i++) { 
                			if (($row_provide_template['provide_netsize_default'] == 'maskrange' && ($i >= $row_provide_template['provide_netsize_default_min_ipv4'] && $i <= $row_provide_template['provide_netsize_default_max_ipv4'])) || $row_provide_template['provide_netsize_default'] == 'mask' || $totalRows_provide_template == 0) { ?>
               	  		<option value="<?php echo $i; ?>" <?php if ($_SESSION['provide_netsize'] == "" && $row_provide_template['provide_netsize'] == $i || $_SESSION['provide_netsize'] == $i) { echo "selected='selected'"; } ?>>/<?php echo $i ?></option>
               	  			<?php } ?>
               	  	<?php } ?>
                  
                <?php
					
				} else { 
					
					if ($_SESSION['provide_netsize'] == "") {
						$_SESSION['provide_netsize'] = 64;
					}
					?>
                	
                	<?php for ($i=8; $i<128; $i++) { 
                			if (($row_provide_template['provide_netsize_default'] == 'maskrange' && ($i >= $row_provide_template['provide_netsize_default_min_ipv6'] && $i <= $row_provide_template['provide_netsize_default_max_ipv6'])) || $row_provide_template['provide_netsize_default'] == 'mask' || $totalRows_provide_template == 0) { ?>
               	  		<option value="<?php echo $i; ?>" <?php if ($_SESSION['provide_netsize'] == "" && $row_provide_template['provide_netsize'] == $i || $_SESSION['provide_netsize'] == $i) { echo "selected='selected'"; } ?>>/<?php echo $i ?></option>
               	  			<?php } ?>
               	  	<?php } ?>
               	  	
                <?php 
					
				} ?>
                </select>
                
                <?php if ($row_provide_template['provide_netgroup_editable'] == "0" && $row_provide_template['provide_netgroup'] != "" && $row_provide_template['provide_parent_editable'] == "0" && $row_provide_template['provide_netsize_editable'] == "0") { ?>
                	<input type="hidden" name="netsize" value="<?php echo $row_provide_template['provide_netsize']; ?>" />                
                <?php } ?>
          		<input type="submit" value="Go" class="input_standard">
               	<input type="hidden" name="parent" value="<?php echo $_SESSION['provide_parent']; ?>" />
                <input type="hidden" name="provide" value="step5" />
                <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                
            </form>
               
             <?php } ?>
             
            <form action="handler.php" name="providelink5" id="providelink" method="post" target="_self" onSubmit="MM_validateForm('network','','R');return document.MM_returnValue">
            
            	<?php if ($_SESSION['provide_layer'] == 3) { 
				
				if ($_SESSION['provide_existing'] != 1) { 
				
					if ($row_parent['v6mask'] == "") { 
					
						$network_mask = get_dotted_mask($_SESSION['provide_netsize']);
								
					$net = find_net(long2ip($row_parent['network']),$row_parent['mask']);
					
					$networks = array();
					
					$nextNet = $net['network'];
	
					if ($totalRows_parent_addresses == 0 || ($totalRows_parent_addresses > 0 && $_SESSION['provide_existing'] == 1)) {
						
					do {
						
						$count++;
						
						$net1 = find_net(long2ip($nextNet),$network_mask);
						
					mysql_select_db($database_subman, $subman);
					$query_subnets = "SELECT * FROM networks WHERE (((networks.network > ".($nextNet-1)." AND networks.network < ".($net1['broadcast']+1).")) AND networks.historic != 1 AND networks.maskLong > INET_ATON('".$row_parent['mask']."') AND networks.container = ".$_GET['container'].") ORDER BY networks.maskLong ASC LIMIT 1";
					$subnets = mysql_query($query_subnets, $subman) or die(mysql_error());
					$row_subnets = mysql_fetch_assoc($subnets);
					$totalRows_subnets = mysql_num_rows($subnets);
					
						if ($totalRows_subnets > 0) {
							
							$net2 = find_net(long2ip($row_subnets['network']),$row_subnets['mask']);
							
							if ($net2['broadcast'] < $net1['broadcast']) {
								$nextNet = $net1['broadcast']+1;
							}
							else {
								$nextNet = $net2['broadcast']+1;
							}
						}
						else {
							array_push($networks,$net1['network']);
							$nextNet = $net1['broadcast']+1;
						}
			
					} while (($nextNet < $net['broadcast']) && ($count < 256)); 
					
					}
					?>
					
					
								<h5>Network</h5>
								
					<?php
					
					if (count($networks) == 0) {
				?>
				
				<p>There are no
				  <?php if ($network_mask = "255.255.255.255") { } else { echo get_slash($network_mask); } ?>
				  slots available within this network.</p>
				<?php } else { ?>
				<p>Please select from one of the first 255 available networks below.
                <br /><span class="text_red">Note: This network will be the primary network for the link.  You can add additional networks/addresses in the Link View screen after the link has been provided.</span></p>
				  <p>
					<select name="network" size="10" class="input_standard" id="network">
					  <?php for($i=0;$i<count($networks);$i++) { ?>
					  <option value="<?php echo $networks[$i]; ?>"><?php echo long2ip($networks[$i]); ?></option>
					  <?php } ?>
					</select>
							   
				<?php } ?>
				
				<?php } else { 
					
					
					$nextNet = $row_parent['network'];
					$nextNetMask = $row_parent['v6mask'];
					
					$networks = array();
	
					if ($totalRows_parent_addresses == 0 || ($totalRows_parent_addresses > 0 && $_SESSION['provide_existing'] == 1)) {
					
						if ($nextNetMask < $_SESSION['provide_netsize']) {
							
							do {
								
								$count++;
								
							mysql_select_db($database_subman, $subman);
							$query_subnets = "SELECT * FROM networks WHERE (((networks.network > ".bcsub($nextNet,1)." AND networks.network < ".bcadd($nextNet,bcpow(2,(128 - $_SESSION['provide_netsize']))).")) AND networks.historic != 1 AND networks.v6mask > '".$row_parent['v6mask']."' AND networks.container = ".$_GET['container'].") ORDER BY networks.v6mask ASC LIMIT 1";
							$subnets = mysql_query($query_subnets, $subman) or die(mysql_error());
							$row_subnets = mysql_fetch_assoc($subnets);
							$totalRows_subnets = mysql_num_rows($subnets);
							
								if ($totalRows_subnets > 0) {
									
									if (bccomp(bcadd($nextNet,bcpow(2,(128 - $_SESSION['provide_netsize']))), bcadd($row_subnets['network'],bcpow(2,(128 - $row_subnets['v6mask'])))) == 1) {
										$nextNet = bcadd($nextNet,bcpow(2,(128 - $_SESSION['provide_netsize'])));
									}
									else {
										$nextNet = bcadd($row_subnets['network'],bcpow(2,(128 - $row_subnets['v6mask'])));
									}
								}
								else {
									array_push($networks,$nextNet);
									$nextNet = bcadd($nextNet,bcpow(2,(128 - $_SESSION['provide_netsize'])));
								}
							
							
							} while (( bccomp((bcadd($row_parent['network'],bcpow(2,(128 - $row_parent['v6mask'])))),$nextNet) == 1 ) && ($count < 256)); 
							
						}
					
					
					}
					?>
					
					
			  <h5>Network</h5>
								
					<?php
					
					if (count($networks) == 0) {
				?>
				
				<p>There are no
				  /<?php echo $_SESSION['provide_netsize']; ?>
				  slots available within this network.</p>
				<?php } else { ?>
				<p>Please select from one of the first 255 available networks below.</p>
				  <p>
					<select name="network" size="10" class="input_standard" id="network">
					  <?php for($i=0;$i<count($networks);$i++) { ?>
					  <option value="<?php echo $networks[$i]; ?>"><?php echo long2ipv6(Net_IPv6::Uncompress($networks[$i])); ?></option>
					  <?php } ?>
					</select>
							   
				<?php } ?>
				
				<?php } ?>
                
                <?php } ?>
                
                 <br /><br /><input type="checkbox" name="manualaddress" class="input_standard" value="1" <?php if ($row_provide_template['manual_addressing'] == 1) { echo "checked=\"checked\""; } ?> <?php if ($row_provide_template['manual_addressing_editable'] == "0") { echo "disabled=\"disabled\""; } ?> />Allow me to manually select the addresses for each node out of this subnet <br />
                <em>If unchecked, IP Manager will allocate the addresses from the first available ones in ascending order.</em>
                <?php if ($row_provide_template['manual_addressing'] == 1 && $row_provide_template['manual_addressing_editable'] == "0") { ?><input type="hidden" name="manualaddress" value="1" /><?php } ?>
              </p>
              
             <?php } else { ?>
                	
                    <h5>Network</h5>
                	<p>Click 'Next' to continue.</p>
                
                <?php } ?> 
              
                <input type="hidden" name="provide" value="step6" />
                <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                <?php if ($_SESSION['provide_layer'] == 2 || ($_SESSION['provide_layer'] == 3 && count($networks) > 0) || ($_SESSION['provide_layer'] == 3 && $_SESSION['provide_existing'] == 1)) { ?>
                <br /><br />
							<a href="#" onClick="if (document.getElementById('providelink').onsubmit()) { document.getElementById('providelink').submit(); } " class="input_providewizard">
								Next</a>
						
				<?php } ?>
				
							<a href="handler.php?action=provide_cancel&amp;container=<?php echo $_GET['container']; ?>" <?php if ($_SESSION['linked'] != "") { ?>onclick="if (window.confirm('This link is required by the template.  If you cancel now, only a superuser can provide the rest of this link.  Alternatively, you can cancel, and delete any other links associated with this template, and then re-provide all links.')) { } else { return false; }" <?php } ?> class="input_providewizard">Cancel</a>
						
            </form><br />
        
        <?php }
		
		elseif ($_SESSION['provide_step'] == 5) { 
			
			wizardsummary(5);
			
			mysql_select_db($database_subman, $subman);
			if ($_SESSION['provide_netgroup'] == "") {
				$query_provide_networks = "SELECT * FROM networks WHERE networks.container = ".$_GET['container']." ORDER BY networks.network, networks.mask, networks.v6mask";
			}
			else {
				$query_provide_networks = "SELECT * FROM networks WHERE networks.container = ".$_GET['container']." AND networks.networkGroup = ".$_SESSION['provide_netgroup']." ORDER BY networks.network, networks.mask, networks.v6mask";
			}
			
			$provide_networks = mysql_query($query_provide_networks, $subman) or die(mysql_error());
			$row_provide_networks = mysql_fetch_assoc($provide_networks);
			$totalRows_provide_networks = mysql_num_rows($provide_networks);
			
			mysql_select_db($database_subman, $subman);
			$query_provide_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
			$provide_template = mysql_query($query_provide_template, $subman) or die(mysql_error());
			$row_provide_template = mysql_fetch_assoc($provide_template);
			$totalRows_provide_template = mysql_num_rows($provide_template);
			?>
        
        	
            
            <?php if ($row_provide_template['step5prompt']) { ?>
    
    	<div id="provide_prompt"><?php echo $row_provide_template['step5prompt']; ?></div>
    
    <?php } ?>
    
            <form action="handler.php" name="providelink4" id="providelink" method="post" target="_self" onSubmit="<?php if ($_SESSION['provide_netgroup'] == "") { ?>MM_validateForm('parent','','RIsNum');return document.MM_returnValue<?php } ?>">
            
            	<?php if ($_SESSION['provide_layer'] == 3) { ?>
                
                <h5>Parent Network</h5>
                
                <?php if ($_SESSION['provide_netgroup'] == "") { ?>
                
                	<p>Search for a network below.</p>
                	
                	<input type="text" name="network_search" id="network_search" class="input_standard" size="32" maxlength="512" autocomplete="off" onKeyUp="if (this.value == '') { document.getElementById('searchQ_1').style.display='none'; } else { document.getElementById('searchQ_1').style.display='block'; searchQry_parents(this.value); }">
   	  <div id="searchQ_1" class="searchQ_1"></div>
   	  	<input type="hidden" name="parent" id="parent" value="">
   	  	<input type="text" name="selected_network" id="selected_network" class="input_selected" value="" disabled="disabled" style="display:none" size="50">
                
                <?php } else { ?>
                
            	<select name="parent" id="parent" class="input_standard" <?php if ($row_provide_template['provide_parent_editable'] == "0" && $row_provide_template['provide_netgroup_editable'] == "0" && $row_provide_template['provide_netgroup'] != "") { echo "disabled"; } ?>>
            	  <?php
do {
	if ($row_provide_networks['v6mask'] == "") {
		$net = find_net(long2ip($row_provide_networks['network']),$row_provide_networks['mask']);
	}
?>
<?php if ($totalRows_provide_networks > 0) { ?>
<?php if ((getNetworkLevel($row_provide_networks['id'], $_SESSION['MM_Username']) > 10 || (getNetGroupLevel($row_provide_networks['networkGroup'],$_SESSION['MM_Username']) > 10 && getNetworkLevel($row_provide_networks['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getNetGroupLevel($row_provide_networks['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($row_provide_networks['id'],$_SESSION['MM_Username']) == ""))) { 
		$netcount ++; ?>
            	  <option value="<?php echo $row_provide_networks['id']?>" <?php if ($row_provide_template['provide_parent'] == $row_provide_networks['id']) { echo "selected='selected'"; } ?>><?php if ($row_provide_networks['v6mask'] == "" ) { ?><?php echo $net['cidr']?> &gt;&gt; <?php echo $row_provide_networks['descr']; ?><?php } else { echo Net_IPv6::compress(long2ipv6($row_provide_networks['network']))."/".$row_provide_networks['v6mask']; ?> &gt;&gt; <?php echo $row_provide_networks['descr']; } ?></option>
            	  <?php
}
}
} while ($row_provide_networks = mysql_fetch_assoc($provide_networks));
  $rows = mysql_num_rows($provide_networks);
  if($rows > 0) {
      mysql_data_seek($provide_networks, 0);
	  $row_provide_networks = mysql_fetch_assoc($provide_networks);
  }
  
?>
                </select>
                
                <?php } ?>
                
                <?php if ($row_provide_template['provide_parent_editable'] == "0" && $row_provide_template['provide_netgroup_editable'] == "0" && $_SESSION['provide_netgroup'] != "") { ?>
                	<input type="hidden" name="parent" id="parent" value="<?php echo $row_provide_template['provide_parent']; ?>" />                
                <?php } ?>
                
                <?php if ($row_provide_template['provide_netsize'] != "") { ?>
                	<input type="hidden" name="netsize" value="<?php echo $row_provide_template['provide_netsize']; ?>" />                
                <?php } ?>
                
                <?php } else { ?>
                	
                    <h5>Parent Network</h5>
                    <p>Click 'Next' to continue.</p>
                    
                <?php } ?>
            	
                <p><input type="checkbox" value="1" name="existing" class="input_standard" <?php if ($row_provide_template['do_not_subnet'] == 1) { echo "checked=\"checked\""; } ?> <?php if ($row_provide_template['do_not_subnet_editable'] == "0") { echo "disabled=\"disabled\""; } ?> /> <em>Do not create a new network, but add the addressing to the selected network (if the network is already subnetted, or there are too few addresss available on the network, you will be presented with a list of new networks on the next screen).</em></p>
                <?php if ($row_provide_template['do_not_subnet'] == 1 && $row_provide_template['do_not_subnet_editable'] == "0") { ?><input type="hidden" name="existing" value="1" /><?php } ?>
                <input type="hidden" name="provide" value="step5" />
                <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                <?php if (($_SESSION['provide_layer'] == 3 && $netcount > 0) || $_SESSION['provide_layer'] != 3 || $_SESSION['provide_netgroup'] == "") { ?>
                <br /><br />
							<a href="#" onClick="<?php if ($_SESSION['provide_netgroup'] == "") { ?>if (document.getElementById('providelink').onsubmit()) { document.getElementById('providelink').submit(); } <?php } else { ?>document.getElementById('providelink').submit();<?php } ?>" class="input_providewizard">
								Next</a>
						
				<?php } ?>
				
							<a href="handler.php?action=provide_cancel&amp;container=<?php echo $_GET['container']; ?>" <?php if ($_SESSION['linked'] != "") { ?>onclick="if (window.confirm('This link is required by the template.  If you cancel now, only a superuser can provide the rest of this link.  Alternatively, you can cancel, and delete any other links associated with this template, and then re-provide all links.')) { } else { return false; }" <?php } ?> class="input_providewizard">Cancel</a>
						
            </form><br />
        	
        
        <?php } 
		
		elseif ($_SESSION['provide_step'] == 4) { 
			
			wizardsummary(4);
			
			mysql_select_db($database_subman, $subman);
			$query_provide_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
			$provide_template = mysql_query($query_provide_template, $subman) or die(mysql_error());
			$row_provide_template = mysql_fetch_assoc($provide_template);
			$totalRows_provide_template = mysql_num_rows($provide_template);
			?>
        
   	
            
    <?php if ($row_provide_template['step4prompt']) { ?>
    
    	<div id="provide_prompt"><?php echo $row_provide_template['step4prompt']; ?></div>
    
    <?php } ?>
    
    <?php if ($_SESSION['manual_xconnect']) {
    	
    	mysql_select_db($database_subman, $subman);
		$query_xconnectpool = "SELECT * FROM xconnectpool WHERE xconnectpool.id = '".$_SESSION['provide_xconnectpool']."'";
		$xconnectpool = mysql_query($query_xconnectpool, $subman) or die(mysql_error());
		$row_xconnectpool = mysql_fetch_assoc($xconnectpool);
		$totalRows_xconnectpool = mysql_num_rows($xconnectpool);
	
	} ?>
    
            <form action="handler.php" method="post" name="providelink3" id="providelink" target="_self" onSubmit="MM_validateForm(<?php if ($_SESSION['manual_xconnect']) { ?>'xconnectid','','RinRange<?php echo $row_xconnectpool['xconnectstart']; ?>:<?php echo $row_xconnectpool['xconnectend']; ?>'<?php } ?>);return document.MM_returnValue">
            
            	<?php if ($_SESSION['provide_layer'] == 3) { 
					
					mysql_select_db($database_subman, $subman);
					$query_provide_netgroups = "SELECT * FROM networkgroup WHERE networkgroup.`container` = ".$_GET['container']." ORDER BY networkgroup.name";
					$provide_netgroups = mysql_query($query_provide_netgroups, $subman) or die(mysql_error());
					$row_provide_netgroups = mysql_fetch_assoc($provide_netgroups);
					$totalRows_provide_netgroups = mysql_num_rows($provide_netgroups);
					
					
					?>
                
                <h5>Network Group</h5>
                
                <select name="netgroup" class="input_standard" <?php if ($row_provide_template['provide_netgroup_editable'] == "0") { echo "disabled"; } ?>>
                
                	<option value="">All networks</option>
                  <?php
do {  
?>
<?php if ((getNetGroupLevel($row_provide_netgroups['id'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getNetGroupLevel($row_provide_netgroups['id'],$_SESSION['MM_Username']) == ""))) { ?>

                  <option value="<?php echo $row_provide_netgroups['id']?>" <?php if ($row_provide_netgroups['id'] == $row_provide_template['provide_netgroup']) { echo "selected='selected'"; } ?>><?php echo $row_provide_netgroups['name']?></option>
                  <?php
}
} while ($row_provide_netgroups = mysql_fetch_assoc($provide_netgroups));
  $rows = mysql_num_rows($provide_netgroups);
  if($rows > 0) {
      mysql_data_seek($provide_netgroups, 0);
	  $row_provide_netgroups = mysql_fetch_assoc($provide_netgroups);
  }
?>
                
                	
                
                </select>
                <?php if ($row_provide_template['provide_netgroup_editable'] == "0") { ?>
                	<input type="hidden" name="netgroup" value="<?php echo $row_provide_template['provide_netgroup']; ?>" />                
                <?php } ?>
                
                
                <?php } else { ?>
                	
                	<?php if ($_SESSION['manual_xconnect']) { ?>
                	<h5>Pseudowire ID (Pool: <?php echo $row_xconnectpool['descr']; ?>)</h5>
                	<input type="text" size="20" maxlength="255" name="xconnectid" id="xconnectid" class="input_standard">
                	<br />
                	<?php } ?>
                	
                    <h5>Network Group</h5>
                    <p>Click 'Next' to continue.</p>
                
                <?php } ?>
                
                <input type="hidden" name="provide" value="step4" />
                <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                <br /><br />
							<a href="#" onClick="if (document.getElementById('providelink').onsubmit()) { document.getElementById('providelink').submit(); } " class="input_providewizard">
								Next</a>
						
							<a href="handler.php?action=provide_cancel&amp;container=<?php echo $_GET['container']; ?>" <?php if ($_SESSION['linked'] != "") { ?>onclick="if (window.confirm('This link is required by the template.  If you cancel now, only a superuser can provide the rest of this link.  Alternatively, you can cancel, and delete any other links associated with this template, and then re-provide all links.')) { } else { return false; }" <?php } ?> class="input_providewizard">Cancel</a>
						
            </form><br>
        
        <?php } 
		
		elseif ($_SESSION['provide_step'] == 3) { 
			
			wizardsummary(3);
			
			mysql_select_db($database_subman, $subman);
			$query_provide_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
			$provide_template = mysql_query($query_provide_template, $subman) or die(mysql_error());
			$row_provide_template = mysql_fetch_assoc($provide_template);
			$totalRows_provide_template = mysql_num_rows($provide_template);
			?>
        
   	
    
    <?php if ($row_provide_template['step3prompt']) { ?>
    
    	<div id="provide_prompt"><?php echo $row_provide_template['step3prompt']; ?></div>
    
    <?php } ?>
    
            <?php
			
					if (preg_match('/^manual_+/',$_SESSION['provide_vlan_node_a'])) {
						
						list($crap, $pool_a) = split('_',$_SESSION['provide_vlan_node_a']);
						
						mysql_select_db($database_subman, $subman);
						$query_vlanpool_a = "SELECT * FROM vlanpool WHERE vlanpool.id = '".$pool_a."'";
						$vlanpool_a = mysql_query($query_vlanpool_a, $subman) or die(mysql_error());
						$row_vlanpool_a = mysql_fetch_assoc($vlanpool_a);
						$totalRows_vlanpool_a = mysql_num_rows($vlanpool_a);
					
					}
					if (preg_match('/^manual_+/',$_SESSION['provide_vlan_node_b'])) {
						
						list($crap, $pool_b) = split('_',$_SESSION['provide_vlan_node_b']);
						
						mysql_select_db($database_subman, $subman);
						$query_vlanpool_b = "SELECT * FROM vlanpool WHERE vlanpool.id = '".$pool_b."'";
						$vlanpool_b = mysql_query($query_vlanpool_b, $subman) or die(mysql_error());
						$row_vlanpool_b = mysql_fetch_assoc($vlanpool_b);
						$totalRows_vlanpool_b = mysql_num_rows($vlanpool_b);
					
					}
					
					?>
                    
   	<form action="handler.php" method="post" name="providelink2" id="providelink" target="_self" onSubmit="MM_validateForm(<?php if (preg_match('/^manual_+/',$_SESSION['provide_vlan_node_a'])) { ?>'vlannumber_a','','RinRange<?php echo $row_vlanpool_a['poolstart']; ?>:<?php echo $row_vlanpool_a['poolend']; ?>'<?php } if (preg_match('/^manual_+/',$_SESSION['provide_vlan_node_a']) && preg_match('/^manual_+/',$_SESSION['provide_vlan_node_b'])) { echo ","; } if (preg_match('/^manual_+/',$_SESSION['provide_vlan_node_b'])) { ?>'vlannumber_b','','RinRange<?php echo $row_vlanpool_b['poolstart']; ?>:<?php echo $row_vlanpool_b['poolend']; ?>'<?php } ?>);return document.MM_returnValue">
            
            
            <?php if (preg_match('/^manual_+/',$_SESSION['provide_vlan_node_a'])) { ?>
            	
              <h5>Node A VLAN ID (Pool: <?php echo $row_vlanpool_a['name']; ?>)</h5>
                
                <input name="vlannumber_a" type="text" class="input_standard" id="vlannumber_a" size="10" maxlength="4" />
                
                Note: If the VLAN number is already in use, IP Manager will attempt to assign one automatically from the pool.
                
            <?php } ?>
            
            <?php if (preg_match('/^manual_+/',$_SESSION['provide_vlan_node_b'])) { ?>
            	
              <h5>Node B VLAN ID (Pool: <?php echo $row_vlanpool_b['name']; ?>)</h5>
                
                <input type="text" name="vlannumber_b" class="input_standard" size="10" maxlength="4" />
                
                Note: If the VLAN number is already in use, IP Manager will attempt to assign one automatically from the pool.
<?php } ?>
            
            <?php if ($_SESSION['provide_vlan_node_a'] != "" && $_SESSION['provide_vlan_node_b'] == "same") {
				
				if (preg_match('/^auto_+/',$_SESSION['provide_vlan_node_a'])) {
			
					list($crap,$vlanpool) = split('_',$_SESSION['provide_vlan_node_a']);
					
					mysql_select_db($database_subman, $subman);
					$query_checkVlanPool = "SELECT vlanpool.devicegroup FROM vlanpool WHERE vlanpool.id = '".$vlanpool."'";
					$checkVlanPool = mysql_query($query_checkVlanPool, $subman) or die(mysql_error());
					$row_checkVlanPool = mysql_fetch_assoc($checkVlanPool);
					$totalRows_checkVlanPool = mysql_num_rows($checkVlanPool);
				
				}
				elseif (!(preg_match('/^manual_+/',$_SESSION['provide_vlan_node_a']))) {
					
					mysql_select_db($database_subman, $subman);
					$query_checkVlanPool = "SELECT vlan.*, vlanpool.devicegroup FROM vlan LEFT JOIN vlanpool ON vlanpool.id = vlan.vlanpool WHERE vlan.id = ".$_SESSION['provide_vlan_node_a']."";
					$checkVlanPool = mysql_query($query_checkVlanPool, $subman) or die(mysql_error());
					$row_checkVlanPool = mysql_fetch_assoc($checkVlanPool);
					$totalRows_checkVlanPool = mysql_num_rows($checkVlanPool);

				}
				
				if ($row_checkVlanPool['devicegroup'] == "" && !(preg_match('/^manual_+/',$_SESSION['provide_vlan_node_a']))) { ?>
					
					<p>You cannot select 'Same as node A' for Node B's VLAN when you have selected a VLAN in a local pool for Node A.</p>
                    <a href="handler.php?action=provide_cancel&amp;container=<?php echo $_GET['container']; ?>" <?php if ($_SESSION['linked'] != "") { ?>onclick="if (window.confirm('This link is required by the template.  If you cancel now, only a superuser can provide the rest of this link.  Alternatively, you can cancel, and delete any other links associated with this template, and then re-provide all links.')) { } else { return false; }" <?php } ?>>Cancel</a>
					
				<?php 
					exit();
				}
				
			}
				
				if ($_SESSION['provide_vpn'] != "") { 
				
					mysql_select_db($database_subman, $subman);
					$query_provide_vpn = "SELECT * FROM vpn WHERE vpn.id = '".$_SESSION['provide_vpn']."'";
					$provide_vpn = mysql_query($query_provide_vpn, $subman) or die(mysql_error());
					$row_provide_vpn = mysql_fetch_assoc($provide_vpn);
					$totalRows_provide_vpn = mysql_num_rows($provide_vpn);

				}
				
				if ($_SESSION['provide_layer'] == 2 && $_SESSION['provide_vpn'] != "" && ($row_provide_vpn['layer'] == 2 || preg_match('/auto_+/',$_SESSION['provide_vpn']) || preg_match('/new_+/',$_SESSION['provide_vpn']))) { 
					
					mysql_select_db($database_subman, $subman);
					$query_provide_xconnectpools = "SELECT * FROM xconnectpool WHERE xconnectpool.`container` = ".$_GET['container']." ORDER BY xconnectpool.xconnectstart";
					$provide_xconnectpools = mysql_query($query_provide_xconnectpools, $subman) or die(mysql_error());
					$row_provide_xconnectpools = mysql_fetch_assoc($provide_xconnectpools);
					$totalRows_provide_xconnectpools = mysql_num_rows($provide_xconnectpools);
					
					mysql_select_db($database_subman, $subman);
					$query_provide_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
					$provide_template = mysql_query($query_provide_template, $subman) or die(mysql_error());
					$row_provide_template = mysql_fetch_assoc($provide_template);
					$totalRows_provide_template = mysql_num_rows($provide_template);
					?>
				
			  <h5>Pseudowire Pool</h5>
					
					<select name="xconnectpool" class="input_standard" <?php if ($row_provide_template['provide_xconnectpool_editable'] == "0") { echo "disabled"; } ?>>
					  <?php
do {  
?>
					  <option value="<?php echo $row_provide_xconnectpools['id']?>" <?php if ($row_provide_xconnectpools['id'] == $row_provide_template['provide_xconnectpool']) { echo "selected='selected'"; } ?>><?php echo $row_provide_xconnectpools['xconnectstart']?> - <?php echo $row_provide_xconnectpools['xconnectend']?> <?php echo $row_provide_xconnectpools['descr']?></option>
					  <?php
} while ($row_provide_xconnectpools = mysql_fetch_assoc($provide_xconnectpools));
  $rows = mysql_num_rows($provide_xconnectpools);
  if($rows > 0) {
	  mysql_data_seek($provide_xconnectpools, 0);
	  $row_provide_xconnectpools = mysql_fetch_assoc($provide_xconnectpools);
  }
?>
					
					
					
					</select>
					
					<?php if ($row_provide_template['provide_xconnectpool_editable'] == "0") { ?>
					<input type="hidden" name="xconnectpool" value="<?php echo $row_provide_template['provide_xconnectpool']; ?>" />                
				<?php } ?>
				
				<br /><br /><input type="checkbox" value="1"  name="manual_xconnect" <?php if ($row_provide_template['manual_xconnect_editable'] == "0") { echo "disabled"; } ?>  <?php if ($row_provide_template['manual_xconnect'] == "1") { echo "checked=\"checked\""; } ?>> Allow me to manually select a pseudowire ID from this pool <br /><em>(if you select an ID that is already in use, IP Manager will revert to auto selecting an ID)</em>
				
				<?php if ($row_provide_template['manual_xconnect_editable'] == "0") { ?>
					<input type="hidden" name="manual_xconnect" value="<?php echo $row_provide_template['manual_xconnect']; ?>" />                
				<?php } ?>
			  <?php } 
					
					elseif ($_SESSION['provide_layer'] == 3 && $_SESSION['provide_vpn'] != "") { 
						
						mysql_select_db($database_subman, $subman);
						$query_provide_vrfs = "SELECT vrf.*, provider.asnumber, rd.rd as rdnumber FROM vrf LEFT JOIN vpnvrf ON vpnvrf.vrf = vrf.id LEFT JOIN providervpn ON providervpn.vpn = vpnvrf.vpn LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN rd ON rd.id = vrf.rd WHERE vpnvrf.vpn = ".$_SESSION['provide_vpn']."";
						$provide_vrfs = mysql_query($query_provide_vrfs, $subman) or die(mysql_error());
						$row_provide_vrfs = mysql_fetch_assoc($provide_vrfs);
						$totalRows_provide_vrfs = mysql_num_rows($provide_vrfs);

						?>
						
						<h5>VRF</h5>
						<p><em>To add a new VRF, browse MPLS providers on the 'VPNs' menu above, select the VPN, then select 'Add a VRF' from the drop-down menu.  Your progress on this page will be saved.</em></p>
					
						<select name="vrf" class="input_standard" <?php if ($row_provide_template['provide_vrf_editable'] == "0") { echo "disabled"; } ?>>
						  <?php
do {  
?>
						  <option value="<?php echo $row_provide_vrfs['id']?>" <?php if ($row_provide_vrfs['id'] == $row_provide_template['provide_vrf']) { echo "selected='selected'"; } ?>><?php echo $row_provide_vrfs['asnumber']?>:<?php echo $row_provide_vrfs['rdnumber']?> <?php echo $row_provide_vrfs['name']?></option>
						  <?php
} while ($row_provide_vrfs = mysql_fetch_assoc($provide_vrfs));
  $rows = mysql_num_rows($provide_vrfs);
  if($rows > 0) {
	  mysql_data_seek($provide_vrfs, 0);
	  $row_provide_vrfs = mysql_fetch_assoc($provide_vrfs);
  }
?>
						
						
						
						</select>
						
						<?php if ($row_provide_template['provide_vrf_editable'] == "0") { ?>
					<input type="hidden" name="vrf" value="<?php echo $row_provide_template['provide_vrf']; ?>" />                
				<?php } ?>
					
					<?php if ($_SESSION['provide_node_b'] != "") { ?>
					<br /><br /><input type="checkbox" value="1"  name="pece" <?php if ($row_provide_template['provide_pece_editable'] == "0") { echo "disabled"; } ?>  <?php if ($row_provide_template['provide_pece'] == "1") { echo "checked=\"checked\""; } ?>> PE-CE option (i.e. No VRF for node B)
				
					<?php if ($row_provide_template['provide_pece_editable'] == "0") { ?>
					<input type="hidden" name="pece" value="<?php echo $row_provide_template['provide_pece']; ?>" />                
				<?php } ?>
					<?php } ?>
					
					<?php }
					
					else { ?>
						
						<h5>VRF/Pseudowire Pool</h5>
						<p>Click 'Next' to continue.</p>
					
					<?php } ?>
					
					<input type="hidden" name="provide" value="step3" />
					<input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
					<?php if ($_SESSION['provide_vpn'] == "" || ($_SESSION['provide_layer'] == 2 && $totalRows_provide_xconnectpools > 0) || ($_SESSION['provide_layer'] == 2 && $row_provide_vpn['layer'] == 4) || ($_SESSION['provide_layer'] == 3 && $totalRows_provide_vrfs > 0)) { ?>
					<br /><br />
							<a href="#" onClick="if (document.getElementById('providelink').onsubmit()) { document.getElementById('providelink').submit(); } " class="input_providewizard">
								Next</a>
						
				<?php } ?>
			
							<a href="handler.php?action=provide_cancel&amp;container=<?php echo $_GET['container']; ?>" <?php if ($_SESSION['linked'] != "") { ?>onclick="if (window.confirm('This link is required by the template.  If you cancel now, only a superuser can provide the rest of this link.  Alternatively, you can cancel, and delete any other links associated with this template, and then re-provide all links.')) { } else { return false; }" <?php } ?> class="input_providewizard">Cancel</a>
						
    </form><br />
        
		<?php	
		}
		
		elseif ($_SESSION['provide_step'] == 2) {
			
			wizardsummary(2);
			
			mysql_select_db($database_subman, $subman);
			$query_provide_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
			$provide_template = mysql_query($query_provide_template, $subman) or die(mysql_error());
			$row_provide_template = mysql_fetch_assoc($provide_template);
			$totalRows_provide_template = mysql_num_rows($provide_template);
			?>
		
        
        
        <?php if ($row_provide_template['step2prompt']) { ?>
    
    	<div id="provide_prompt"><?php echo $row_provide_template['step2prompt']; ?></div>
    
    <?php } ?>
    
   	<form action="handler.php" method="post" name="providelink1" id="providelink" target="_self">
            
            	<?php 
					
					mysql_select_db($database_subman, $subman);
					$query_providers = "SELECT provider.* FROM provider WHERE provider.container = '".$_GET['container']."' ORDER BY provider.name";
					$providers = mysql_query($query_providers, $subman) or die(mysql_error());
					$row_providers = mysql_fetch_assoc($providers);
					$totalRows_providers = mysql_num_rows($providers);
					
					if ($_SESSION['provide_node_b'] != "") {
						
						mysql_select_db($database_subman, $subman);
						$query_layer2_vpns = "SELECT vpn.*, provider.name as providername, provider.asnumber, provider.id as providerID FROM vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE provider.container = ".$_GET['container']." AND vpn.layer = 2 ORDER BY provider.name, vpn.name";
						$layer2_vpns = mysql_query($query_layer2_vpns, $subman) or die(mysql_error());
						$row_layer2_vpns = mysql_fetch_assoc($layer2_vpns);
						$totalRows_layer2_vpns = mysql_num_rows($layer2_vpns);
						
					}
					else {
						
						mysql_select_db($database_subman, $subman);
						$query_layer2_vpns = "SELECT vpn.*, provider.name as providername, provider.asnumber, provider.id as providerID FROM vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE provider.container = ".$_GET['container']." AND vpn.layer = 4 ORDER BY provider.name, vpn.name";
						$layer2_vpns = mysql_query($query_layer2_vpns, $subman) or die(mysql_error());
						$row_layer2_vpns = mysql_fetch_assoc($layer2_vpns);
						$totalRows_layer2_vpns = mysql_num_rows($layer2_vpns);
						
					}
					
					mysql_select_db($database_subman, $subman);
					$query_provide_vlans = "SELECT vlan.*, vlanpool.name as vlanpoolname FROM vlan LEFT JOIN vlanpool ON vlanpool.id = vlan.vlanpool LEFT JOIN portgroups ON portgroups.id = vlanpool.devicegroup LEFT JOIN portsdevices ON portsdevices.devicegroup = portgroups.id WHERE portsdevices.id = ".$_SESSION['provide_node_a']." ORDER BY vlanpool.name, vlan.number";
					$provide_vlans = mysql_query($query_provide_vlans, $subman) or die(mysql_error());
					$row_provide_vlans = mysql_fetch_assoc($provide_vlans);
					$totalRows_provide_vlans = mysql_num_rows($provide_vlans);
					
					mysql_select_db($database_subman, $subman);
					$query_provide_vlans1 = "SELECT vlan.*, vlanpool.name as vlanpoolname FROM vlan LEFT JOIN vlanpool ON vlanpool.id = vlan.vlanpool WHERE vlanpool.device = ".$_SESSION['provide_node_a']." AND (vlanpool.devicegroup IS NULL OR vlanpool.devicegroup = '') ORDER BY vlanpool.name, vlan.number";
					$provide_vlans1 = mysql_query($query_provide_vlans1, $subman) or die(mysql_error());
					$row_provide_vlans1 = mysql_fetch_assoc($provide_vlans1);
					$totalRows_provide_vlans1 = mysql_num_rows($provide_vlans1);
					
					mysql_select_db($database_subman, $subman);
					$query_getDevice = "SELECT portsdevices.*, devicetypes.name as devicetype, devicetypes.image, devicetypes.vlans FROM portsdevices LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE portsdevices.id = ".$_SESSION['provide_node_a']."";
					$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
					$row_getDevice = mysql_fetch_assoc($getDevice);
					$totalRows_getDevice = mysql_num_rows($getDevice);
			
					mysql_select_db($database_subman, $subman);
					$query_device_vlan_pools = "SELECT vlanpool.*, portsdevices.name as devicename FROM vlanpool LEFT JOIN portsdevices ON portsdevices.id = vlanpool.device WHERE vlanpool.device = ".$row_getDevice['id']." OR vlanpool.devicegroup = ".$row_getDevice['devicegroup']." ORDER BY vlanpool.devicegroup, vlanpool.name";
					$device_vlan_pools = mysql_query($query_device_vlan_pools, $subman) or die(mysql_error());
					$row_device_vlan_pools = mysql_fetch_assoc($device_vlan_pools);
					$totalRows_device_vlan_pools = mysql_num_rows($device_vlan_pools);
					?>

<?php if ($_SESSION['provide_logical_node_a'] != "tdm" || $_SESSION['provide_logical_node_b'] != "tdm") { ?>
<?php if ($_SESSION['provide_logical_node_a'] != "tdm" && $_SESSION['provide_logical_node_b'] != "tdm") { 
 if ($_SESSION['provide_layer'] == 2) {
 	
 	
 	?>
<h5>Layer 2 VPN</h5>
             
      <select name="vpn" class="input_standard" <?php if ($row_provide_template['provide_vpn_editable'] == "0") { echo "disabled"; } ?>>
                      
                      <option value="">None</option>
                      
                      <?php
                  		$pname = '';
						do {  
							if ($pname != '' && $pname != $row_providers['name']) { ?>
								</optgroup>
								<optgroup label="<?php echo $row_providers['asnumber']; ?> <?php echo $row_providers['name']; ?>">
							<?php }
							elseif ($pname != $row_providers['name']) { ?>
								<optgroup label="<?php echo $row_providers['asnumber']; ?> <?php echo $row_providers['name']; ?>">
							<?php }
	
						?>
                      <?php if ($_SESSION['provide_node_b'] != "") { ?>
                      <option value="new_<?php echo $row_providers['id']; ?>" <?php if ($row_provide_template['provide_vpn'] == 'new_'.$row_providers['id']) { echo "selected='selected'"; } ?>>AUTO CREATE (Pseudowire)</option>
                      <?php } else { ?>
                      <option value="new_multi_<?php echo $row_providers['id']; ?>" <?php if ($row_provide_template['provide_vpn'] == 'new_multi_'.$row_providers['id']) { echo "selected='selected'"; } ?>>AUTO CREATE (Multipoint)</option>
                      <?php } 
                      		$pname = $row_providers['name']; ?>
                      <?php } while ($row_providers = mysql_fetch_assoc($providers)); ?>
                      
                      
                      <?php
					  if ($totalRows_layer2_vpns > 0) { 
						$pname = '';
						do {  
							if ($pname != '' && $pname != $row_layer2_vpns['providername']) { ?>
								</optgroup>
								<optgroup label="<?php echo $row_layer2_vpns['providername']; ?> - Existing VPNs">
							<?php }
							elseif ($pname != $row_layer2_vpns['providername']) { ?>
								<optgroup label="<?php echo $row_layer2_vpns['providername']; ?> - Existing VPNs">
							<?php }
	
						?>
<?php if ((getVpnLevel($row_layer2_vpns['id'], $_SESSION['MM_Username']) > 10 || (getProviderLevel($row_layer2_vpns['providerID'],$_SESSION['MM_Username']) > 10 && getVpnLevel($row_layer2_vpns['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($row_layer2_vpns['providerID'],$_SESSION['MM_Username']) == "" && getVpnLevel($row_layer2_vpns['id'],$_SESSION['MM_Username']) == ""))) { ?>
        <option value="<?php echo $row_layer2_vpns['id']?>" <?php if ($row_layer2_vpns['id'] == $row_provide_template['provide_vpn']) { echo "selected='selected'"; } ?>><?php echo $row_layer2_vpns['name']?></option>
                      <?php
}
					$pname = $row_layer2_vpns['providername'];
				
				} while ($row_layer2_vpns = mysql_fetch_assoc($layer2_vpns));
				  $rows = mysql_num_rows($layer2_vpns);
				  if($rows > 0) {
					  mysql_data_seek($layer2_vpns, 0);
					  $row_layer2_vpns = mysql_fetch_assoc($layer2_vpns);
				  }
					  }
				?>
			</optgroup>
	  </select>
      			
				<?php if ($row_provide_template['provide_vpn_editable'] == "0") { ?>
                	<input type="hidden" name="vpn" value="<?php echo $row_provide_template['provide_vpn']; ?>" />                
                <?php } ?>
                
<?php } ?>
<?php } ?>
<?php if ($_SESSION['provide_logical_node_a'] != "tdm") { ?>

      				<h5>Node A VLAN</h5>
                    
      				<select name="vlan_node_a" class="input_standard" <?php if ($row_provide_template['provide_vlan_node_a_editable'] == "0") { echo "disabled"; } ?>>
                    
                    	<option value="">None</option>
                        
                        <?php if ($totalRows_device_vlan_pools > 0) { ?>
                        <?php
                  		$pname = '';
						do {  
							if ($pname != '' && $pname != $row_device_vlan_pools['name']) { ?>
								</optgroup>
								<optgroup label="<?php echo $row_device_vlan_pools['name']; ?>">
							<?php }
							elseif ($pname != $row_device_vlan_pools['name']) { ?>
								<optgroup label="<?php echo $row_device_vlan_pools['name']; ?>">
							<?php }
	
						?>
                        <option value="auto_<?php echo $row_device_vlan_pools['id']; ?>" <?php if ($row_provide_template['provide_vlan_node_a'] == "auto_".$row_device_vlan_pools['id']) { echo "selected='selected'"; } ?>>[AUTO CREATE] <?php echo $row_device_vlan_pools['name']; ?> <?php echo $row_device_vlan_pools['poolstart']; ?> - <?php echo $row_device_vlan_pools['poolend']; ?></option>
                        <option value="manual_<?php echo $row_device_vlan_pools['id']; ?>" <?php if ($row_provide_template['provide_vlan_node_a'] == "manual_".$row_device_vlan_pools['id']) { echo "selected='selected'"; } ?>>[MANUALLY CREATE] <?php echo $row_device_vlan_pools['name']; ?> <?php echo $row_device_vlan_pools['poolstart']; ?> - <?php echo $row_device_vlan_pools['poolend']; ?></option>
                        <?php 
                        	$pname = $row_device_vlan_pools['name'];
                        	
                        } while ($row_device_vlan_pools = mysql_fetch_assoc($device_vlan_pools)); ?>
                        <?php } ?>
                        
                        <option value="" disabled="disabled">--------------------</option>
                        
                    <?php
if ($totalRows_provide_vlans1 > 0) {
	$pname = '';
						do {  
							if ($pname != '' && $pname != $row_provide_vlans1['vlanpoolname']) { ?>
								</optgroup>
								<optgroup label="<?php echo $row_provide_vlans1['vlanpoolname']; ?>">
							<?php }
							elseif ($pname != $row_provide_vlans1['vlanpoolname']) { ?>
								<optgroup label="<?php echo $row_provide_vlans1['vlanpoolname']; ?>">
							<?php }
	
						?>
      				  <option value="<?php echo $row_provide_vlans1['id']?>" <?php if ($row_provide_vlans1['id'] == $row_provide_template['provide_vlan_node_a']) { echo "selected='selected'"; } ?>>[<?php echo $row_provide_vlans1['number']?>] <?php echo $row_provide_vlans1['name']?></option>
      				  <?php
      				  $pname = $row_provide_vlans1['vlanpoolname'];
      				  
} while ($row_provide_vlans1 = mysql_fetch_assoc($provide_vlans1));
  $rows = mysql_num_rows($provide_vlans1);
  if($rows > 0) {
      mysql_data_seek($provide_vlans1, 0);
	  $row_provide_vlans1 = mysql_fetch_assoc($provide_vlans1);
  }
}
?>
      				  <?php
if ($totalRows_provide_vlans > 0) {
					$pname = '';
						do {  
							if ($pname != '' && $pname != $row_provide_vlans['vlanpoolname']) { ?>
								</optgroup>
								<optgroup label="<?php echo $row_provide_vlans['vlanpoolname']; ?>">
							<?php }
							elseif ($pname != $row_provide_vlans['vlanpoolname']) { ?>
								<optgroup label="<?php echo $row_provide_vlans['vlanpoolname']; ?>">
							<?php }
	
						?>
      				  <option value="<?php echo $row_provide_vlans['id']?>" <?php if ($row_provide_vlans['id'] == $row_provide_template['provide_vlan_node_a']) { echo "selected='selected'"; } ?>>[<?php echo $row_provide_vlans['number']?>] <?php echo $row_provide_vlans['name']?></option>
      				  <?php
      				  $pname = $row_provide_vlans['vlanpoolname'];
      				  
} while ($row_provide_vlans = mysql_fetch_assoc($provide_vlans));
  $rows = mysql_num_rows($provide_vlans);
  if($rows > 0) {
      mysql_data_seek($provide_vlans, 0);
	  $row_provide_vlans = mysql_fetch_assoc($provide_vlans);
  }
}
?>					
                    	</optgroup>
                    </select>
                    
                    <?php if ($row_provide_template['provide_vlan_node_a_editable'] == "0") { ?>
                	<input type="hidden" name="vlan_node_a" value="<?php echo $row_provide_template['provide_vlan_node_a']; ?>" />                
                <?php } ?>
                
                    <?php if ($_SESSION['provide_layer'] == 3) { ?>
                    <p><em>You have selected a layer 3 service.  If you select a VLAN, your interface type will be overwritten to 'sub-interface' - this is the only interface type allowed when you select a VLAN on a layer 3 service.</em></p>
                    <?php } ?>
<?php } ?>                
<?php if ($_SESSION['provide_node_b'] != "") { 

	mysql_select_db($database_subman, $subman);
	$query_getDeviceB = "SELECT portsdevices.*, devicetypes.name as devicetype, devicetypes.image, devicetypes.vlans FROM portsdevices LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE portsdevices.id = ".$_SESSION['provide_node_b']."";
	$getDeviceB = mysql_query($query_getDeviceB, $subman) or die(mysql_error());
	$row_getDeviceB = mysql_fetch_assoc($getDeviceB);
	$totalRows_getDeviceB = mysql_num_rows($getDeviceB);

	mysql_select_db($database_subman, $subman);
	$query_device_vlan_pools = "SELECT vlanpool.*, portsdevices.name as devicename FROM vlanpool LEFT JOIN portsdevices ON portsdevices.id = vlanpool.device WHERE vlanpool.device = ".$row_getDeviceB['id']." OR vlanpool.devicegroup = ".$row_getDeviceB['devicegroup']." ORDER BY vlanpool.devicegroup, vlanpool.name";
	$device_vlan_pools = mysql_query($query_device_vlan_pools, $subman) or die(mysql_error());
	$row_device_vlan_pools = mysql_fetch_assoc($device_vlan_pools);
	$totalRows_device_vlan_pools = mysql_num_rows($device_vlan_pools);
					
if ($_SESSION['provide_logical_node_b'] != "tdm") { 

	mysql_select_db($database_subman, $subman);
	$query_provide_vlans = "SELECT vlan.*, vlanpool.name as vlanpoolname FROM vlan LEFT JOIN vlanpool ON vlanpool.id = vlan.vlanpool LEFT JOIN portgroups ON portgroups.id = vlanpool.devicegroup LEFT JOIN portsdevices ON portsdevices.devicegroup = portgroups.id WHERE portsdevices.id = ".$_SESSION['provide_node_b']." ORDER BY vlanpool.name, vlan.number";
	$provide_vlans = mysql_query($query_provide_vlans, $subman) or die(mysql_error());
	$row_provide_vlans = mysql_fetch_assoc($provide_vlans);
	$totalRows_provide_vlans = mysql_num_rows($provide_vlans);
	
	mysql_select_db($database_subman, $subman);
	$query_provide_vlans1 = "SELECT vlan.*, vlanpool.name as vlanpoolname FROM vlan LEFT JOIN vlanpool ON vlanpool.id = vlan.vlanpool WHERE vlanpool.device = ".$_SESSION['provide_node_b']." AND (vlanpool.devicegroup IS NULL OR vlanpool.devicegroup = '') ORDER BY vlanpool.name, vlan.number";
	$provide_vlans1 = mysql_query($query_provide_vlans1, $subman) or die(mysql_error());
	$row_provide_vlans1 = mysql_fetch_assoc($provide_vlans1);
	$totalRows_provide_vlans1 = mysql_num_rows($provide_vlans1);
	?>

      				<h5>Node B VLAN</h5>
                    
      				<select name="vlan_node_b" class="input_standard" <?php if ($row_provide_template['provide_vlan_node_b_editable'] == "0") { echo "disabled"; } ?>>
                    	
                        <?php if ($row_getDevice['devicegroup'] == $row_getDeviceB['devicegroup']) { ?>
                        <option value="same" <?php if ($row_provide_template['provide_vlan_node_b'] == "same") { echo "selected='selected'"; } ?>>Same as node A</option>
                        <?php } ?>
                    	<option value="">None</option>
                        
                        <?php if ($totalRows_device_vlan_pools > 0) { ?>
                        <?php
                  		$pname = '';
						do {  
							if ($pname != '' && $pname != $row_device_vlan_pools['name']) { ?>
								</optgroup>
								<optgroup label="<?php echo $row_device_vlan_pools['name']; ?>">
							<?php }
							elseif ($pname != $row_device_vlan_pools['name']) { ?>
								<optgroup label="<?php echo $row_device_vlan_pools['name']; ?>">
							<?php }
	
						?>
                        <option value="auto_<?php echo $row_device_vlan_pools['id']; ?>" <?php if ($row_provide_template['provide_vlan_node_b'] == "auto_".$row_device_vlan_pools['id']) { echo "selected='selected'"; } ?>>[AUTO CREATE] <?php echo $row_device_vlan_pools['name']; ?> <?php echo $row_device_vlan_pools['poolstart']; ?> - <?php echo $row_device_vlan_pools['poolend']; ?></option>
                        <option value="manual_<?php echo $row_device_vlan_pools['id']; ?>" <?php if ($row_provide_template['provide_vlan_node_b'] == "manual_".$row_device_vlan_pools['id']) { echo "selected='selected'"; } ?>>[MANUALLY CREATE] <?php echo $row_device_vlan_pools['name']; ?> <?php echo $row_device_vlan_pools['poolstart']; ?> - <?php echo $row_device_vlan_pools['poolend']; ?></option>
                        <?php 
                        	$pname = $row_device_vlan_pools['name'];
                        	
                        	} while ($row_device_vlan_pools = mysql_fetch_assoc($device_vlan_pools)); ?>
                        <?php } ?>
                        
                        <option value="" disabled="disabled">--------------------</option>
                        
                        <?php
if ($totalRows_provide_vlans1 > 0) {
$pname = '';
						do {  
							if ($pname != '' && $pname != $row_provide_vlans1['vlanpoolname']) { ?>
								</optgroup>
								<optgroup label="<?php echo $row_provide_vlans1['vlanpoolname']; ?>">
							<?php }
							elseif ($pname != $row_provide_vlans1['vlanpoolname']) { ?>
								<optgroup label="<?php echo $row_provide_vlans1['vlanpoolname']; ?>">
							<?php }
	
						?>
      				  <option value="<?php echo $row_provide_vlans1['id']?>" <?php if ($row_provide_vlans1['id'] == $row_provide_template['provide_vlan_node_b']) { echo "selected='selected'"; } ?>>[<?php echo $row_provide_vlans1['number']?>] <?php echo $row_provide_vlans1['name']?></option>
      				  <?php
      				  $pname = $row_provide_vlans1['vlanpoolname'];
      				  
} while ($row_provide_vlans1 = mysql_fetch_assoc($provide_vlans1));
  $rows = mysql_num_rows($provide_vlans1);
  if($rows > 0) {
      mysql_data_seek($provide_vlans1, 0);
	  $row_provide_vlans1 = mysql_fetch_assoc($provide_vlans1);
  }
}
?>

      				  <?php
if ($totalRows_provide_vlans > 0) {
$pname = '';
						do {  
							if ($pname != '' && $pname != $row_provide_vlans['vlanpoolname']) { ?>
								</optgroup>
								<optgroup label="<?php echo $row_provide_vlans['vlanpoolname']; ?>">
							<?php }
							elseif ($pname != $row_provide_vlans['vlanpoolname']) { ?>
								<optgroup label="<?php echo $row_provide_vlans['vlanpoolname']; ?>">
							<?php }
	
						?>
      				  <option value="<?php echo $row_provide_vlans['id']?>" <?php if ($row_provide_vlans['id'] == $row_provide_template['provide_vlan_node_b']) { echo "selected='selected'"; } ?>>[<?php echo $row_provide_vlans['number']?>] <?php echo $row_provide_vlans['name']?></option>
      				  <?php
      				  	$pname = $row_provide_vlans['vlanpoolname'];
      				  	
} while ($row_provide_vlans = mysql_fetch_assoc($provide_vlans));
  $rows = mysql_num_rows($provide_vlans);
  if($rows > 0) {
      mysql_data_seek($provide_vlans, 0);
	  $row_provide_vlans = mysql_fetch_assoc($provide_vlans);
  }
}
?>
					
                    	</optgroup>
                    </select>
                    
                    <?php if ($row_provide_template['provide_vlan_node_b_editable'] == "0") { ?>
                	<input type="hidden" name="vlan_node_b" value="<?php echo $row_provide_template['provide_vlan_node_b']; ?>" />                
                <?php } ?>
                
                    <?php if ($_SESSION['provide_layer'] == 3) { ?>
                    <p><em>You have selected a layer 3 service.  If you select a VLAN, your interface type will be overwritten to 'sub-interface' - this is the only interface type allowed when you select a VLAN on a layer 3 service.</em></p>
                    <?php } ?>

<?php } ?> 
<?php } ?>
<?php }
else { ?>
	<h5>Layer 2 VPN/VLAN</h5>
	<p>Click 'Next' to continue.</p>
<?php } ?>
                
                <?php if ($_SESSION['provide_layer'] == 3) { 
				
					mysql_select_db($database_subman, $subman);
					$query_layer3_vpns = "SELECT vpn.*, provider.name as providername, provider.asnumber, provider.id as providerID FROM vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE provider.container = ".$_GET['container']." AND vpn.layer = 3 ORDER BY provider.name, vpn.name";
					$layer3_vpns = mysql_query($query_layer3_vpns, $subman) or die(mysql_error());
					$row_layer3_vpns = mysql_fetch_assoc($layer3_vpns);
					$totalRows_layer3_vpns = mysql_num_rows($layer3_vpns);
					
					mysql_select_db($database_subman, $subman);
					$query_provide_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
					$provide_template = mysql_query($query_provide_template, $subman) or die(mysql_error());
					$row_provide_template = mysql_fetch_assoc($provide_template);
					$totalRows_provide_template = mysql_num_rows($provide_template);
					?>

<?php if ($_SESSION['provide_logical_node_a'] != "tdm" && $_SESSION['provide_logical_node_b'] != "tdm") { ?>                                	
      <h5>Layer 3 VPN</h5>
      <p><em>To add a new VPN, browse MPLS providers on the 'VPNs' menu above, then select 'Add a VPN' from the drop-down menu.  Your progress on this page will be saved.</em></p>
                    
                	<select name="vpn" class="input_standard" <?php if ($row_provide_template['provide_vpn_editable'] == "0") { echo "disabled"; } ?>>
                      <option value="">None</option>
                      <?php
						$pname = '';
						do {  
							if ($pname != '' && $pname != $row_layer3_vpns['providername']) { ?>
								</optgroup>
								<optgroup label="<?php echo $row_layer3_vpns['asnumber']; ?> <?php echo $row_layer3_vpns['providername']; ?>">
							<?php }
							elseif ($pname != $row_layer3_vpns['providername']) { ?>
								<optgroup label="<?php echo $row_layer3_vpns['asnumber']; ?> <?php echo $row_layer3_vpns['providername']; ?>">
							<?php }
	
						?>
?>
<?php if ((getVpnLevel($row_layer3_vpns['id'], $_SESSION['MM_Username']) > 10 || (getProviderLevel($row_layer3_vpns['providerID'],$_SESSION['MM_Username']) > 10 && getVpnLevel($row_layer3_vpns['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($row_layer3_vpns['providerID'],$_SESSION['MM_Username']) == "" && getVpnLevel($row_layer3_vpns['id'],$_SESSION['MM_Username']) == ""))) { ?>
                      <option value="<?php echo $row_layer3_vpns['id']?>" <?php if ($row_layer3_vpns['id'] == $row_provide_template['provide_vpn']) { echo "selected='selected'"; } ?>><?php echo $row_layer3_vpns['name']?></option>
                      <?php
}
					$pname = $row_layer3_vpns['providername'];
					
				} while ($row_layer3_vpns = mysql_fetch_assoc($layer3_vpns));
				  $rows = mysql_num_rows($layer3_vpns);
				  if($rows > 0) {
					  mysql_data_seek($layer3_vpns, 0);
					  $row_layer3_vpns = mysql_fetch_assoc($layer3_vpns);
				  }
				?>
			
			</optgroup>
	  </select>
      
      			<?php if ($row_provide_template['provide_vpn_editable'] == "0") { ?>
                	<input type="hidden" name="vpn" value="<?php echo $row_provide_template['provide_vpn']; ?>" />                
                <?php } ?>
                
                <?php }
				else { ?>
                	<h5>Layer 3 VPN</h5>
					<p>Click 'Next to continue.</p>
				<?php } ?>
                <?php } ?>
            	
                <input type="hidden" name="provide" value="step2" />
                <input type="hidden" value="<?php echo $_GET['container']; ?>" name="container" />
                <br /><br />
							<a href="#" onClick="document.getElementById('providelink').submit(); " class="input_providewizard">
								Next</a>
						
							<a href="handler.php?action=provide_cancel&amp;container=<?php echo $_GET['container']; ?>" <?php if ($_SESSION['linked'] != "") { ?>onclick="if (window.confirm('This link is required by the template.  If you cancel now, only a superuser can provide the rest of this link.  Alternatively, you can cancel, and delete any other links associated with this template, and then re-provide all links.')) { } else { return false; }" <?php } ?> class="input_providewizard">Cancel</a>
						
    </form><br />

        <?php
		}
		elseif ($_SESSION['provide_step'] == 1) { 
			
			mysql_select_db($database_subman, $subman);
			$query_provide_customers = "SELECT * FROM customer WHERE customer.`container` = ".$_GET['container']." ORDER BY customer.name";
			$provide_customers = mysql_query($query_provide_customers, $subman) or die(mysql_error());
			$row_provide_customers = mysql_fetch_assoc($provide_customers);
			$totalRows_provide_customers = mysql_num_rows($provide_customers);
			
			mysql_select_db($database_subman, $subman);
			$query_provide_template = "SELECT * FROM servicetemplate WHERE servicetemplate.id = '".$_SESSION['provide_template']."'";
			$provide_template = mysql_query($query_provide_template, $subman) or die(mysql_error());
			$row_provide_template = mysql_fetch_assoc($provide_template);
			$totalRows_provide_template = mysql_num_rows($provide_template);
			
			mysql_select_db($database_subman, $subman);
			if ($row_provide_template['provide_node_a_default'] == 'device' || $totalRows_provide_template == 0) {
				$query_provide_devices = "SELECT portsdevices.*, portgroups.name AS devicegroupname FROM portsdevices LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portgroups.container = ".$_GET['container']." ORDER BY portgroups.name, portsdevices.name";
			}
			elseif ($row_provide_template['provide_node_a_default'] == 'devicetype') {
				$query_provide_devices = "SELECT portsdevices.*, portgroups.name AS devicegroupname FROM portsdevices LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portgroups.container = ".$_GET['container']." AND portsdevices.devicetype = '".$row_provide_template['provide_node_a_default_id']."' ORDER BY portgroups.name, portsdevices.name";
			}
			elseif ($row_provide_template['provide_node_a_default'] == 'devicegroup') {
				$query_provide_devices = "SELECT portsdevices.*, portgroups.name AS devicegroupname FROM portsdevices LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portgroups.container = ".$_GET['container']." AND portsdevices.devicegroup = '".$row_provide_template['provide_node_a_default_id']."' ORDER BY portgroups.name, portsdevices.name";
			}
			$provide_devices = mysql_query($query_provide_devices, $subman) or die(mysql_error());
			$row_provide_devices = mysql_fetch_assoc($provide_devices);
			$totalRows_provide_devices = mysql_num_rows($provide_devices);
			
			wizardsummary(1);
			?>
        	
    
    
    <?php if ($row_provide_template['step1prompt']) { ?>
    
    	<div id="provide_prompt"><?php echo $row_provide_template['step1prompt']; ?></div>
    
    <?php } ?>
            
<form action="handler.php" method="post" name="providelink" id="providelink" target="_self" onSubmit="MM_validateForm('cct','','R');return document.MM_returnValue">
           	  <h5>Node A</h5>
                
                <select name="node_a" class="input_standard" <?php if ($row_provide_template['provide_node_a_editable'] == "0") { echo "disabled"; } ?>>
                  <?php
                  	$dgroup = '';
do {  
	if ($dgroup != '' && $dgroup != $row_provide_devices['devicegroupname']) { ?>
		</optgroup>
		<optgroup label="<?php echo $row_provide_devices['devicegroupname']; ?>">
	<?php }
	elseif ($dgroup != $row_provide_devices['devicegroupname']) { ?>
		<optgroup label="<?php echo $row_provide_devices['devicegroupname']; ?>">
	<?php }
	
?>
<?php if ((getDeviceLevel($row_provide_devices['id'], $_SESSION['MM_Username']) > 10 || (getDeviceGroupLevel($row_provide_devices['devicegroup'],$_SESSION['MM_Username']) > 10 && getDeviceLevel($row_provide_devices['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($row_provide_devices['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_provide_devices['id'],$_SESSION['MM_Username']) == ""))) { ?>
                  <option value="<?php echo $row_provide_devices['id']?>" <?php if ($row_provide_devices['id'] == $row_provide_template['provide_node_a'] || $row_provide_devices['id'] == $row_provide_template['provide_node_a_default_id']) { echo "selected='selected'"; } ?><?php if (!(strcmp($row_provide_devices['id'], $_GET['device'])) && $row_provide_template['provide_node_a'] == "") {echo "selected=\"selected\"";}?>><?php echo $row_provide_devices['name']?></option>
                  <?php
}
	$dgroup = $row_provide_devices['devicegroupname'];
	
} while ($row_provide_devices = mysql_fetch_assoc($provide_devices));
  $rows = mysql_num_rows($provide_devices);
  if($rows > 0) {
      mysql_data_seek($provide_devices, 0);
	  $row_provide_devices = mysql_fetch_assoc($provide_devices);
  }
?>
				  </optgroup>
                </select>
                <?php if ($row_provide_template['provide_node_a_editable'] == "0") { ?>
                	<?php if ((getDeviceLevel($row_provide_devices['id'], $_SESSION['MM_Username']) > 10 || (getDeviceGroupLevel($row_provide_devices['devicegroup'],$_SESSION['MM_Username']) > 10 && getDeviceLevel($row_provide_devices['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($row_provide_devices['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_provide_devices['id'],$_SESSION['MM_Username']) == ""))) { ?>
                	<input type="hidden" name="node_a" value="<?php echo $row_provide_template['provide_node_a']; ?>" />
                    <?php } else { 
						$node_permission_error = true ?>
                    <?php } ?>               
                <?php } ?>
                
                
                <?php
				mysql_select_db($database_subman, $subman);
			if ($row_provide_template['provide_node_b_default'] == 'device' || $totalRows_provide_template == 0) {
				$query_provide_devices = "SELECT portsdevices.*, portgroups.name AS devicegroupname FROM portsdevices LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portgroups.container = ".$_GET['container']." ORDER BY portgroups.name, portsdevices.name";
			}
			elseif ($row_provide_template['provide_node_b_default'] == 'devicetype') {
				$query_provide_devices = "SELECT portsdevices.*, portgroups.name AS devicegroupname FROM portsdevices LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portgroups.container = ".$_GET['container']." AND portsdevices.devicetype = '".$row_provide_template['provide_node_b_default_id']."' ORDER BY portgroups.name, portsdevices.name";
			}
			elseif ($row_provide_template['provide_node_b_default'] == 'devicegroup') {
				$query_provide_devices = "SELECT portsdevices.*, portgroups.name AS devicegroupname FROM portsdevices LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portgroups.container = ".$_GET['container']." AND portsdevices.devicegroup = '".$row_provide_template['provide_node_b_default_id']."' ORDER BY portgroups.name, portsdevices.name";
			}
			$provide_devices = mysql_query($query_provide_devices, $subman) or die(mysql_error());
			$row_provide_devices = mysql_fetch_assoc($provide_devices);
			$totalRows_provide_devices = mysql_num_rows($provide_devices);
				?>
                
                <h5>Node B</h5>
                <select name="node_b" class="input_standard" <?php if ($row_provide_template['provide_node_b_editable'] == "0") { echo "disabled"; } ?>>
                
                  <option value="">None</option>
                  
                  <?php
                  	$dgroup = '';
do {  
	if ($dgroup != '' && $dgroup != $row_provide_devices['devicegroupname']) { ?>
		</optgroup>
		<optgroup label="<?php echo $row_provide_devices['devicegroupname']; ?>">
	<?php }
	elseif ($dgroup != $row_provide_devices['devicegroupname']) { ?>
		<optgroup label="<?php echo $row_provide_devices['devicegroupname']; ?>">
	<?php }
	
?>
<?php if ((getDeviceLevel($row_provide_devices['id'], $_SESSION['MM_Username']) > 10 || (getDeviceGroupLevel($row_provide_devices['devicegroup'],$_SESSION['MM_Username']) > 10 && getDeviceLevel($row_provide_devices['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($row_provide_devices['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_provide_devices['id'],$_SESSION['MM_Username']) == ""))) { ?>
                  <option value="<?php echo $row_provide_devices['id']?>" <?php if ($row_provide_devices['id'] == $row_provide_template['provide_node_b'] || $row_provide_devices['id'] == $row_provide_template['provide_node_b_default_id']) { echo "selected='selected'"; } ?>><?php echo $row_provide_devices['name']?></option>
                  <?php
}
	$dgroup = $row_provide_devices['devicegroupname'];
	
} while ($row_provide_devices = mysql_fetch_assoc($provide_devices));
  $rows = mysql_num_rows($provide_devices);
  if($rows > 0) {
      mysql_data_seek($provide_devices, 0);
	  $row_provide_devices = mysql_fetch_assoc($provide_devices);
  }
?>
                </select>
                
                <?php if ($row_provide_template['provide_node_b_editable'] == "0") { ?>
                	<?php if ((getDeviceLevel($row_provide_devices['id'], $_SESSION['MM_Username']) > 10 || (getDeviceGroupLevel($row_provide_devices['devicegroup'],$_SESSION['MM_Username']) > 10 && getDeviceLevel($row_provide_devices['id'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($row_provide_devices['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($row_provide_devices['id'],$_SESSION['MM_Username']) == ""))) { ?>
                	<input type="hidden" name="node_b" value="<?php echo $row_provide_template['provide_node_b']; ?>" />
                    <?php } else { 
						$node_permission_error = true ?>
                    <?php } ?>
                    	         
                <?php } ?>
                
                <h5>Port Layer</h5>
                
<select name="layer" class="input_standard" <?php if ($row_provide_template['provide_layer_editable'] == "0") { echo "disabled"; } ?>>
                	
                    <option value="2" <?php if ($row_provide_template['provide_layer'] == 2) { echo "selected='selected'"; } ?>>Layer 2 Data-Link</option>
                    <option value="3" <?php if ($row_provide_template['provide_layer'] == 3) { echo "selected='selected'"; } ?>>Layer 3 Network</option>
                    
      </select>
      
      			<?php if ($row_provide_template['provide_layer_editable'] == "0") { ?>
                	<input type="hidden" name="layer" value="<?php echo $row_provide_template['provide_layer']; ?>" />                
                <?php } ?>
                
                <h5>Node A Logical Interface Type</h5>
                
<select name="logical_node_a" class="input_standard" <?php if ($row_provide_template['provide_logical_node_a_editable'] == "0") { echo "disabled"; } ?>>
                	
                    <option value="normal" <?php if ($row_provide_template['provide_logical_node_a'] == "normal") { echo "selected='selected'"; } ?>>Normal</option>
                    <option value="subint" <?php if ($row_provide_template['provide_logical_node_a'] == "subint") { echo "selected='selected'"; } ?>>Sub-Interface</option>
                    <option value="tdm" <?php if ($row_provide_template['provide_logical_node_a'] == "tdm") { echo "selected='selected'"; } ?>>TDM</option>
                    
      </select>
      			
				<?php if ($row_provide_template['provide_logical_node_a_editable'] == "0") { ?>
                	<input type="hidden" name="logical_node_a" value="<?php echo $row_provide_template['provide_logical_node_a']; ?>" />                
                <?php } ?>
                
                
                <h5>Node B Logical Interface Type</h5>
                
<select name="logical_node_b" class="input_standard" <?php if ($row_provide_template['provide_logical_node_b_editable'] == "0") { echo "disabled"; } ?>>
                	
                    <option value="normal" <?php if ($row_provide_template['provide_logical_node_b'] == "normal") { echo "selected='selected'"; } ?>>Normal</option>
                    <option value="subint" <?php if ($row_provide_template['provide_logical_node_b'] == "subint") { echo "selected='selected'"; } ?>>Sub-Interface</option>
                    <option value="tdm" <?php if ($row_provide_template['provide_logical_node_b'] == "tdm") { echo "selected='selected'"; } ?>>TDM</option>
                    
      </select>
                
                <?php if ($row_provide_template['provide_logical_node_b_editable'] == "0") { ?>
                	<input type="hidden" name="logical_node_b" value="<?php echo $row_provide_template['provide_logical_node_b']; ?>" />                
                <?php } ?>
                
                <h5>Customer</h5>
                
                <select name="customer" class="input_standard" <?php if ($row_provide_template['provide_customer_editable'] == "0") { echo "disabled"; } ?>>
                  <?php
do {  
?>
                  <option value="<?php echo $row_provide_customers['id']?>" <?php if ($row_provide_customers['id'] == $row_provide_template['provide_customer']) { echo "selected='selected'"; } ?>><?php echo $row_provide_customers['name']?></option>
                  <?php
} while ($row_provide_customers = mysql_fetch_assoc($provide_customers));
  $rows = mysql_num_rows($provide_customers);
  if($rows > 0) {
      mysql_data_seek($provide_customers, 0);
	  $row_provide_customers = mysql_fetch_assoc($provide_customers);
  }
?>
                
                
                
                </select>
                
                <?php if ($row_provide_template['provide_customer_editable'] == "0") { ?>
                	<input type="hidden" name="customer" value="<?php echo $row_provide_template['provide_customer']; ?>" />                
                <?php } ?>
                
                
      <h5>Circuit Reference</h5>
                
      <input name="cct" type="text" class="input_standard" id="cct" size="70" maxlength="255" value="<?php echo $row_provide_template['provide_cct']; ?>" <?php if ($row_provide_template['provide_cct_editable'] == "0") { echo "disabled"; } ?> />
      
      <?php if ($row_provide_template['provide_cct_editable'] == "0") { ?>
                	<input type="hidden" name="cct" value="<?php echo $row_provide_template['provide_cct']; ?>" />                
                <?php } ?>
                
        <input type="hidden" value="step1" name="provide" />
                <input type="hidden" value="<?php echo $_GET['container']; ?>" name="container" />
                
                <?php if ($totalRows_provide_devices > 0 && !($node_permission_error)) { ?>
          <br /><br />
							<a href="#" onClick="if (document.getElementById('providelink').onsubmit()) { document.getElementById('providelink').submit(); } " class="input_providewizard">
								Next</a>
						
				<?php } ?>
				
							<a href="handler.php?action=provide_cancel&amp;container=<?php echo $_GET['container']; ?>" <?php if ($_SESSION['linked'] != "") { ?>onclick="if (window.confirm('This link is required by the template.  If you cancel now, only a superuser can provide the rest of this link.  Alternatively, you can cancel, and delete any other links associated with this template, and then re-provide all links.')) { } else { return false; }" <?php } ?> class="input_providewizard">Cancel</a>
						
          
    </form>
    <br /><br />
    <h5>Circuit Reference Keywords</h5>
                <table width="50%" border="0">
                	<tr>
                    	<td>%nodea%</td>
                        <td>Node A</td>
                    </tr>
                    <tr>
                    	<td>%nodeb%</td>
                        <td>Node B</td>
                    </tr>
                    <tr>
                    	<td>%customername%</td>
                        <td>Customer name</td>
                    </tr>
                    <tr>
                    	<td>%customername_upper%</td>
                        <td>Customer name (upper case)</td>
                    </tr>
                    <tr>
                    	<td>%customername_trimmed_upper%</td>
                        <td>Customer name with whitespace removed (upper case)</td>
                    </tr>
                </table>
    
			
		<?php
        }
		
		elseif ($_GET['linkview'] == 1) { 
			
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.name as devicetype, devicetypes.image, devicetypes.vlans FROM portsdevices LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE portsdevices.id = '".$_GET['device']."'";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			if ($row_getDevice['devicegroup'] != "" && $row_getDevice['devicegroup'] != "0" && !(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($row_getDevice['devicegroup'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($row_getDevice['devicegroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_links = "SELECT * FROM links WHERE (links.provide_node_a = '".$_GET['device']."' OR links.provide_node_b = '".$_GET['device']."') AND (links.provide_port_node_a = '".$_GET['port']."' OR links.provide_port_node_b = '".$_GET['port']."')";
			$links = mysql_query($query_links, $subman) or die(mysql_error());
			$row_links = mysql_fetch_assoc($links);
			$totalRows_links = mysql_num_rows($links);
			
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = '".$row_getDevice['devicegroup']."'";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getCard = "SELECT portsports.*, cards.rack, cards.module, cards.slot, cards.id as cardid, cardtypes.name as cardtypename FROM portsports LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE portsports.id = '".$_GET['port']."'";
			$getCard = mysql_query($query_getCard, $subman) or die(mysql_error());
			$row_getCard = mysql_fetch_assoc($getCard);
			$totalRows_getCard = mysql_num_rows($getCard);
			
			?>
			
             Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_getDevice['devicegroup']; ?>&device=<?php echo $_GET['device']; ?>" title="View Device"><?php echo $row_getDevice['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> Line Card <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_getDevice['devicegroup']; ?>&amp;device=<?php echo $_GET['device']; ?>&amp;card=<?php echo $row_getCard['cardid']; ?>" title="Browse device line card"><?php if (!(isset($row_getCard['rack'])) && !(isset($row_getCard['module'])) && !(isset($row_getCard['slot']))) { echo "Virtual"; } else { if (isset($row_getCard['rack'])) { echo $row_getCard['rack']."/"; } if (isset($row_getCard['module'])) { echo $row_getCard['module'].'/'; } if (isset($row_getCard['slot'])) { echo $row_getCard['slot']; } } ?> <?php echo $row_getCard['cardtypename']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> Port <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getCard['port']; ?></strong><br />
    <br />
    
			<h3>Link View</h3>
            
            
            
            <?php if ($totalRows_links > 0 && $_GET['device'] != "" && $_GET['port'] != "") { ?>
            
                <?php 
				$count = 0;
				
				do { 
					
					mysql_select_db($database_subman, $subman);
					$query_linked = "SELECT links.*, portsdevices.devicegroup FROM links LEFT JOIN portsdevices ON portsdevices.id = links.provide_node_a WHERE links.id = '".$row_links['linked']."'";
					$linked = mysql_query($query_linked, $subman) or die(mysql_error());
					$row_linked = mysql_fetch_assoc($linked);
					$totalRows_linked = mysql_num_rows($linked);
					
					mysql_select_db($database_subman, $subman);
					$query_template = "SELECT servicetemplate.* FROM servicetemplate WHERE servicetemplate.id = '".$row_links['servicetemplate']."'";
					$template = mysql_query($query_template, $subman) or die(mysql_error());
					$row_template = mysql_fetch_assoc($template);
					$totalRows_template = mysql_num_rows($template);
					
					mysql_select_db($database_subman, $subman);
					$query_linkedfrom = "SELECT links.*, portsdevices.devicegroup FROM links LEFT JOIN portsdevices ON portsdevices.id = links.provide_node_a WHERE links.id = '".$row_links['linkedfrom']."'";
					$linkedfrom = mysql_query($query_linkedfrom, $subman) or die(mysql_error());
					$row_linkedfrom = mysql_fetch_assoc($linkedfrom);
					$totalRows_linkedfrom = mysql_num_rows($linkedfrom);
					
					mysql_select_db($database_subman, $subman);
					$query_customer = "SELECT * FROM customer WHERE customer.id = '".$row_links['provide_customer']."'";
					$customer = mysql_query($query_customer, $subman) or die(mysql_error());
					$row_customer = mysql_fetch_assoc($customer);
					$totalRows_customer = mysql_num_rows($customer);
					
					mysql_select_db($database_subman, $subman);
					$query_vpn = "SELECT vpn.*, provider.name as providername, provider.asnumber, provider.id as providerid FROM vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE vpn.id = '".$row_links['provide_vpn']."'";
					$vpn = mysql_query($query_vpn, $subman) or die(mysql_error());
					$row_vpn = mysql_fetch_assoc($vpn);
					$totalRows_vpn = mysql_num_rows($vpn);
					
					mysql_select_db($database_subman, $subman);
					$query_xconnect = "SELECT * FROM xconnectid WHERE xconnectid.id = '".$row_links['provide_xconnect']."'";
					$xconnect = mysql_query($query_xconnect, $subman) or die(mysql_error());
					$row_xconnect = mysql_fetch_assoc($xconnect);
					$totalRows_xconnect = mysql_num_rows($xconnect);
					
					mysql_select_db($database_subman, $subman);
					$query_vrf = "SELECT * FROM vrf WHERE vrf.id = '".$row_links['provide_vrf']."'";
					$vrf = mysql_query($query_vrf, $subman) or die(mysql_error());
					$row_vrf = mysql_fetch_assoc($vrf);
					$totalRows_vrf = mysql_num_rows($vrf);
					
					mysql_select_db($database_subman, $subman);
					$query_network = "SELECT * FROM networks WHERE networks.id = '".$row_links['provide_network']."'";
					$network = mysql_query($query_network, $subman) or die(mysql_error());
					$row_network = mysql_fetch_assoc($network);
					$totalRows_network = mysql_num_rows($network);
					
					mysql_select_db($database_subman, $subman);
					$query_node_a = "SELECT portsdevices.*, devicetypes.image, devicetypes.name as devicetypename FROM portsdevices LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE portsdevices.id = '".$row_links['provide_node_a']."'";
					$node_a = mysql_query($query_node_a, $subman) or die(mysql_error());
					$row_node_a = mysql_fetch_assoc($node_a);
					$totalRows_node_a = mysql_num_rows($node_a);
					
					mysql_select_db($database_subman, $subman);
					$query_node_b = "SELECT portsdevices.*, devicetypes.image, devicetypes.name as devicetypename FROM portsdevices LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE portsdevices.id = '".$row_links['provide_node_b']."'";
					$node_b = mysql_query($query_node_b, $subman) or die(mysql_error());
					$row_node_b = mysql_fetch_assoc($node_b);
					$totalRows_node_b = mysql_num_rows($node_b);
					
					mysql_select_db($database_subman, $subman);
					$query_vlan_node_a = "SELECT vlan.*, vlanpool.device, portsdevices.devicegroup FROM vlan LEFT JOIN vlanpool ON vlanpool.id = vlan.vlanpool LEFT JOIN portsdevices ON portsdevices.id = vlanpool.device WHERE vlan.id = '".$row_links['provide_vlan_node_a']."'";
					$vlan_node_a = mysql_query($query_vlan_node_a, $subman) or die(mysql_error());
					$row_vlan_node_a = mysql_fetch_assoc($vlan_node_a);
					$totalRows_vlan_node_a = mysql_num_rows($vlan_node_a);
					
					mysql_select_db($database_subman, $subman);
					$query_vlan_node_b = "SELECT vlan.*, vlanpool.device, portsdevices.devicegroup FROM vlan LEFT JOIN vlanpool ON vlanpool.id = vlan.vlanpool LEFT JOIN portsdevices ON portsdevices.id = vlanpool.device WHERE vlan.id = '".$row_links['provide_vlan_node_b']."'";
					$vlan_node_b = mysql_query($query_vlan_node_b, $subman) or die(mysql_error());
					$row_vlan_node_b = mysql_fetch_assoc($vlan_node_b);
					$totalRows_vlan_node_b = mysql_num_rows($vlan_node_b);
					
					mysql_select_db($database_subman, $subman);
					$query_card_node_a = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = '".$row_links['provide_card_node_a']."'";
					$card_node_a = mysql_query($query_card_node_a, $subman) or die(mysql_error());
					$row_card_node_a = mysql_fetch_assoc($card_node_a);
					$totalRows_card_node_a = mysql_num_rows($card_node_a);
					
					mysql_select_db($database_subman, $subman);
					$query_card_node_b = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = '".$row_links['provide_card_node_b']."'";
					$card_node_b = mysql_query($query_card_node_b, $subman) or die(mysql_error());
					$row_card_node_b = mysql_fetch_assoc($card_node_b);
					$totalRows_card_node_b = mysql_num_rows($card_node_b);
	
					
					mysql_select_db($database_subman, $subman);
					$query_port_node_a = "SELECT portsports.* FROM portsports WHERE portsports.id = '".$row_links['provide_port_node_a']."'";
					$port_node_a = mysql_query($query_port_node_a, $subman) or die(mysql_error());
					$row_port_node_a = mysql_fetch_assoc($port_node_a);
					$totalRows_port_node_a = mysql_num_rows($port_node_a);
					
					mysql_select_db($database_subman, $subman);
					$query_address_node_a = "SELECT addresses.* FROM addresses WHERE addresses.id = '".$row_port_node_a['router']."'";
					$address_node_a = mysql_query($query_address_node_a, $subman) or die(mysql_error());
					$row_address_node_a = mysql_fetch_assoc($address_node_a);
					$totalRows_address_node_a = mysql_num_rows($address_node_a);
					
					mysql_select_db($database_subman, $subman);
					$query_port_node_b = "SELECT portsports.* FROM portsports WHERE portsports.id = '".$row_links['provide_port_node_b']."'";
					$port_node_b = mysql_query($query_port_node_b, $subman) or die(mysql_error());
					$row_port_node_b = mysql_fetch_assoc($port_node_b);
					$totalRows_port_node_b = mysql_num_rows($port_node_b);
					
					mysql_select_db($database_subman, $subman);
					$query_address_node_b = "SELECT addresses.* FROM addresses WHERE addresses.id = '".$row_port_node_b['router']."'";
					$address_node_b = mysql_query($query_address_node_b, $subman) or die(mysql_error());
					$row_address_node_b = mysql_fetch_assoc($address_node_b);
					$totalRows_address_node_b = mysql_num_rows($address_node_b);
					
					mysql_select_db($database_subman, $subman);
					$query_subint_node_a = "SELECT subint.* FROM subint WHERE subint.id = '".$row_links['provide_subint_node_a']."'";
					$subint_node_a = mysql_query($query_subint_node_a, $subman) or die(mysql_error());
					$row_subint_node_a = mysql_fetch_assoc($subint_node_a);
					$totalRows_subint_node_a = mysql_num_rows($subint_node_a);
					
					mysql_select_db($database_subman, $subman);
					$query_address_node_a_subint = "SELECT addresses.* FROM addresses WHERE addresses.id = '".$row_subint_node_a['router']."'";
					$address_node_a_subint = mysql_query($query_address_node_a_subint, $subman) or die(mysql_error());
					$row_address_node_a_subint = mysql_fetch_assoc($address_node_a_subint);
					$totalRows_address_node_a_subint = mysql_num_rows($address_node_a_subint);
					
					mysql_select_db($database_subman, $subman);
					$query_subint_node_b = "SELECT subint.* FROM subint WHERE subint.id = '".$row_links['provide_subint_node_b']."'";
					$subint_node_b = mysql_query($query_subint_node_b, $subman) or die(mysql_error());
					$row_subint_node_b = mysql_fetch_assoc($subint_node_b);
					$totalRows_subint_node_b = mysql_num_rows($subint_node_b);
					
					mysql_select_db($database_subman, $subman);
					$query_address_node_b_subint = "SELECT addresses.* FROM addresses WHERE addresses.id = '".$row_subint_node_b['router']."'";
					$address_node_b_subint = mysql_query($query_address_node_b_subint, $subman) or die(mysql_error());
					$row_address_node_b_subint = mysql_fetch_assoc($address_node_b_subint);
					$totalRows_address_node_b_subint = mysql_num_rows($address_node_b_subint);
					
					mysql_select_db($database_subman, $subman);
					$query_timeslots_node_a = "SELECT timeslots.* FROM timeslots WHERE timeslots.portid = '".$row_links['provide_port_node_a']."'";
					$timeslots_node_a = mysql_query($query_timeslots_node_a, $subman) or die(mysql_error());
					$row_timeslots_node_a = mysql_fetch_assoc($timeslots_node_a);
					$totalRows_timeslots_node_a = mysql_num_rows($timeslots_node_a);
					
					mysql_select_db($database_subman, $subman);
					$query_timeslots_node_b = "SELECT timeslots.* FROM timeslots WHERE timeslots.portid = '".$row_links['provide_port_node_b']."'";
					$timeslots_node_b = mysql_query($query_timeslots_node_b, $subman) or die(mysql_error());
					$row_timeslots_node_b = mysql_fetch_assoc($timeslots_node_b);
					$totalRows_timeslots_node_b = mysql_num_rows($timeslots_node_b);
					
					mysql_select_db($database_subman, $subman);
					$query_routes = "SELECT linknets.*, networks.`network` AS _network, networks.`v6mask`, networks.mask, networks.container AS networkcontainer, networks.id AS networkid FROM linknets LEFT JOIN networks ON networks.id = linknets.network WHERE linknets.link = '".$row_links['id']."'";
					$routes = mysql_query($query_routes, $subman) or die(mysql_error());
					$row_routes = mysql_fetch_assoc($routes);
					$totalRows_routes = mysql_num_rows($routes);
					
					?>
                    
					
                    
                    <div class="rightclick">
                    
                    <form action="" method="post" target="_self" id="frm_recover_link_<?php echo $count; ?>" name="frm_recover_link_<?php echo $count; ?>">
                        	<input type="hidden" name="action" value="recover_link" />
                            <input type="hidden" value="<?php echo $row_links['id']; ?>" name="link" />
                            
                      </form>
                        <form action="" method="post" target="_self" id="frm_change_circuit_ref_<?php echo $count; ?>" name="frm_change_circuit_ref_<?php echo $count; ?>">
                        	<input type="hidden" name="action" value="change_cct" />
                            <input type="hidden" value="<?php echo $row_links['id']; ?>" name="link" />
                        </form>
                        <form action="" method="post" target="_self" id="frm_change_customer_<?php echo $count; ?>" name="frm_change_customer_<?php echo $count; ?>">
                        	<input type="hidden" name="action" value="change_customer" />
                            <input type="hidden" value="<?php echo $row_links['id']; ?>" name="link" />
                        </form>
                        <form action="" method="post" target="_self" id="frm_add_route_<?php echo $count; ?>" name="frm_add_route_<?php echo $count; ?>">
                        	<input type="hidden" name="action" value="add_route" />
                            <input type="hidden" value="<?php echo $row_links['id']; ?>" name="link" />
                        </form>
                        
					<h2><img src="images/link.gif" alt="Link" title="Link" align="absmiddle">&nbsp; <?php echo $row_links['provide_cct']; ?></h2></div>
					<h3><img src="images/customers_icon.gif" alt="Customer" title="Customer" width="20" align="absmiddle">&nbsp;<a href="?browse=customers&amp;container=<?php echo $_GET['container']; ?>&amp;customer=<?php echo $row_customer['id']; ?>" title="Browse customer"><?php echo $row_customer['name']; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php if ($totalRows_template > 0) { echo "Template: ".$row_template['name']; } else { echo "Template: None"; } ?></h3>
                   
                   <div class="ipm_linkview_ctrl">
                   
                   <?php if ((!($row_links['linked']) && !($row_links['linkedfrom'])) || ($row_links['linked'] && !($row_links['linkedfrom']))) { ?> <input type="button" class="input_standard" onClick="document.frm_recover_link_<?php echo $count; ?>.submit();" value="Recover Link(s)"><?php } ?> <?php if ($row_links['provide_layer'] == 3) { ?><input type="button" class="input_standard" onClick="document.frm_add_route_<?php echo $count; ?>.submit();" value="Add a network/route"> <?php } ?><input type="button" class="input_standard" onClick="document.frm_change_circuit_ref_<?php echo $count; ?>.submit();" value="Change Circuit Reference"> <input type="button" class="input_standard" onClick="frm_change_customer_<?php echo $count; ?>.submit();" value="Change Customer"><?php if ($row_links['linked'] != "") { ?>
                    <p><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_linked['devicegroup']; ?>&amp;device=<?php echo $row_linked['provide_node_a']; ?>&amp;port=<?php echo $row_linked['provide_port_node_a']; ?>&amp;linkview=1" title="Browse associated link"><img src="images/link.gif" alt="Linked circuit" title="Browse associated link" align="absmiddle" border="0" /></a> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_linked['devicegroup']; ?>&amp;device=<?php echo $row_linked['provide_node_a']; ?>&amp;port=<?php echo $row_linked['provide_port_node_a']; ?>&amp;linkview=1" title="Browse associated link">Browse associated link</a></p>
                    <?php } ?><?php if ($row_links['linkedfrom'] != "") { ?><p>
                    <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_linkedfrom['devicegroup']; ?>&amp;device=<?php echo $row_linkedfrom['provide_node_a']; ?>&amp;port=<?php echo $row_linkedfrom['provide_port_node_a']; ?>&amp;linkview=1" title="Browse associated link">Browse associated link</a> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_linkedfrom['devicegroup']; ?>&amp;device=<?php echo $row_linkedfrom['provide_node_a']; ?>&amp;port=<?php echo $row_linkedfrom['provide_port_node_a']; ?>&amp;linkview=1" title="Browse associated link"><img src="images/link.gif" alt="Linked circuit" title="Browse associated link" align="absmiddle" border="0" /></a></p>
                    <?php } ?>
                    
    </div>
                    
                    <div class="ipm_linkview">
                    
                        	<div class="ipm_linkview" id="ipm_linkview_nodea">
                            <img src="images/<?php echo $row_node_a['image']; ?>" alt="<?php echo $row_node_a['devicetypename']; ?>" height="30" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_node_a['devicegroup']; ?>&amp;device=<?php echo $row_node_a['id']; ?>" title="Browse device"><?php echo $row_node_a['name']; ?></a>
							</div>
                            <div class="ipm_linkview" id="ipm_linkview_porta_icon"><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_node_a['devicegroup']; ?>&amp;device=<?php echo $row_node_a['id']; ?>&amp;card=<?php echo $row_card_node_a['id']; ?>" title="Browse device card <?php echo $row_card_node_a['cardtypename']; ?> - <?php if (!(isset($row_card_node_a['rack'])) && !(isset($row_card_node_a['module'])) && !(isset($row_card_node_a['slot']))) { echo "Virtual"; } else { if (isset($row_card_node_a['rack'])) { echo $row_card_node_a['rack']."/"; } if (isset($row_card_node_a['module'])) { echo $row_card_node_a['module'].'/'; } if (isset($row_card_node_a['slot'])) { echo $row_card_node_a['slot']; } } ?>/<?php echo $row_port_node_a['port']; ?><?php if ($totalRows_subint_node_a > 0) { ?>.<?php echo $row_subint_node_a['subint']; ?><?php } ?>"><img src="images/ipm_icon_port.gif" alt="Port icon" /><br />
                            <?php if (!(isset($row_card_node_a['rack'])) && !(isset($row_card_node_a['module'])) && !(isset($row_card_node_a['slot']))) { echo "Virtual"; } else { if (isset($row_card_node_a['rack'])) { echo $row_card_node_a['rack']."/"; } if (isset($row_card_node_a['module'])) { echo $row_card_node_a['module'].'/'; } if (isset($row_card_node_a['slot'])) { echo $row_card_node_a['slot']; } } ?>/<?php echo $row_port_node_a['port']; ?><?php if ($totalRows_subint_node_a > 0) { ?>.<?php echo $row_subint_node_a['subint']; ?><?php } ?>
                         	</a>
                         	</div>
                            
                            <?php if ($row_links['provide_vlan_node_a']) { ?>
							
                            	<div class="ipm_linkview" id="ipm_linkview_vlana"><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_vlan_node_a['devicegroup']; ?>&amp;device=<?php echo $row_node_a['id']; ?>&amp;group=<?php echo $row_node_a['devicegroup']; ?>&amp;vlanpool=<?php echo $row_vlan_node_a['vlanpool']; ?>&amp;vlan=<?php echo $row_vlan_node_a['id']; ?>" title="Browse VLAN"><img src="images/ipm_icon_vlan.png" alt="VLAN icon" border="0" /><br />VLAN <?php echo $row_vlan_node_a['number']; ?></a>
                  				</div>

							<?php } else { ?>
                            	<div class="ipm_linkview" id="ipm_linkview_vlana"><img src="images/ipm_icon_vlan_none.png" alt="VLAN icon" border="0" /></div>
                            <?php } ?>
                            
                            <?php if ($totalRows_subint_node_a > 0) { 
									?>
                            
                            	<?php if ($row_links['provide_layer'] == 3 && $totalRows_node_a > 0) { 
								
									mysql_select_db($database_subman, $subman);
									$query_additional_address_node_a = "SELECT addresses.id AS addressid, linknetworks.id AS linknetid, linknetworks.network as networkid, networks.network AS _network, networks.container AS networkcontainer, networks.mask, networks.v6mask, addresses.* FROM linknetworks LEFT JOIN networks ON networks.id = linknetworks.network LEFT JOIN addresses ON addresses.network = linknetworks.network WHERE linknetworks.link = '".$row_links['id']."' AND (addresses.portid = '".$row_port_node_a['id']."' OR addresses.subintid = '".$row_subint_node_a['id']."')";
									$additional_address_node_a = mysql_query($query_additional_address_node_a, $subman) or die(mysql_error());
									$row_additional_address_node_a = mysql_fetch_assoc($additional_address_node_a);
									$totalRows_additional_address_node_a = mysql_num_rows($additional_address_node_a);
									
									?>
                                    
                                    <div class="ipm_linkview" id="ipm_linkview_ipa">
                        	
									<?php if ($row_network['v6mask'] == "" ) { ?><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=&amp;parent=<?php echo $row_network['id']; ?>" title="Browse network"><?php echo long2ip($row_address_node_a_subint['address']).get_slash($row_network['mask']); ?></a><?php } else { ?><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=&amp;parent=<?php echo $row_network['id']; ?>" title="Browse network"><?php echo Net_IPv6::compress(long2ipv6($row_address_node_a_subint['address'])); ?>/<?php echo $row_network['v6mask']; ?></a><?php } ?>
                                    <?php 
										if ($totalRows_additional_address_node_a > 0) { 
										do { ?>
                                    
                                    	<br /><?php
									if ($row_additional_address_node_a['v6mask'] == "") { 
                                     ?>
                                    <form action="" method="post" target="_self" name="frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>" id="frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>">
                                    	<input type="hidden" name="linknet" value="<?php echo $row_additional_address_node_a['linknetid']; ?>" />
                                        <input type="hidden" name="network" value="<?php echo $row_additional_address_node_a['_network']; ?>" />
                                        <input type="hidden" name="address" value="<?php echo $row_additional_address_node_a['addressid']; ?>" />
                                        <input type="hidden" name="mask" value="<?php echo $row_additional_address_node_a['mask']; ?>" />
                                        <input type="hidden" name="link" value="<?php echo $row_links['id']; ?>" />
                                        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                                        <input type="hidden" name="device" value="<?php echo $_GET['device']; ?>" />
                                        <input type="hidden" name="devicegroup" value="<?php echo $_GET['group']; ?>" />
                                        <input type="hidden" name="port" value="<?php echo $_GET['port']; ?>" />
                                        <input type="hidden" name="MM_delete" value="frm_delete_secondary" />
                                    </form>
                                    <?php if ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
                                    <a href="#" title="Delete secondary network" onClick="if (confirm('Are you sure you want to remove the secondary network <?php echo long2ip($row_additional_address_node_a['_network']); ?>/<?php echo get_slash($row_additional_address_node_a['mask']); ?>?')) { document.frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>.submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete secondary network" border="0" align="absmiddle" /></a>
                                    <?php }
                                    } else { 
									 ?>
                                    <form action="" method="post" target="_self" name="frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>" id="frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>">
                                    	<input type="hidden" name="linknet" value="<?php echo $row_additional_address_node_a['linknetid']; ?>" />
                                        <input type="hidden" name="network" value="<?php echo $row_additional_address_node_a['_network']; ?>" />
                                        <input type="hidden" name="address" value="<?php echo $row_additional_address_node_a['addressid']; ?>" />
                                        <input type="hidden" name="mask" value="<?php echo $row_additional_address_node_a['v6mask']; ?>" />
                                        <input type="hidden" name="link" value="<?php echo $row_links['id']; ?>" />
                                        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                                        <input type="hidden" name="device" value="<?php echo $_GET['device']; ?>" />
                                        <input type="hidden" name="devicegroup" value="<?php echo $_GET['group']; ?>" />
                                        <input type="hidden" name="port" value="<?php echo $_GET['port']; ?>" />
                                        <input type="hidden" name="MM_delete" value="frm_delete_secondary" />
                                    </form>
                                    <?php 
                                    if ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
                                    <a href="#" title="Delete secondary network" onClick="if (confirm('Are you sure you want to remove the secondary network <?php echo long2ipv6($row_additional_address_node_a['_network']); ?>/<?php echo $row_additional_address_node_a['v6mask']; ?>?')) { document.frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>.submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete secondary network" border="0" align="absmiddle" /></a>
                                    <?php }
                                    } ?>
                                    <?php if ($row_additional_address_node_a['v6mask'] == "" ) { ?><a href="?browse=networks&amp;container=<?php echo $row_additional_address_node_a['networkcontainer']; ?>&amp;group=&amp;parent=<?php echo $row_additional_address_node_a['network']; ?>" title="Browse network"><?php echo long2ip($row_additional_address_node_a['address']).get_slash($row_additional_address_node_a['mask']); ?></a><?php } else { ?><a href="?browse=networks&amp;container=<?php echo $row_additional_address_node_a['networkcontainer']; ?>&amp;group=&amp;parent=<?php echo $row_additional_address_node_a['network']; ?>" title="Browse network"><?php echo Net_IPv6::compress(long2ipv6($row_additional_address_node_a['address'])); ?>/<?php echo $row_additional_address_node_a['v6mask']; ?></a><?php } ?>
                                    
                                    <?php } while ($row_additional_address_node_a = mysql_fetch_assoc($additional_address_node_a)); 
										} ?>
                                        
                                        <br /><br /><form action="handler.php" method="post" name="providelink" target="_self">
                                
                                	<input type="hidden" value="step3" name="provide" />
                                    <input type="hidden" value="1" name="additional_address" />
                                    <input type="hidden" value="<?php echo $row_links['provide_node_a']; ?>" name="node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_node_b']; ?>" name="node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_layer']; ?>" name="layer" />
                                    <input type="hidden" value="<?php echo $row_links['provide_logical_node_a']; ?>" name="logical_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_logical_node_b']; ?>" name="logical_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_cct']; ?>" name="cct" />
                                    <input type="hidden" value="<?php echo $row_links['provide_customer']; ?>" name="customer" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vpn']; ?>" name="vpn" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vlan_node_a']; ?>" name="vlan_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vlan_node_b']; ?>" name="vlan_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_xconnectpool']; ?>" name="xconnect_pool" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vrf']; ?>" name="vrf" />
                                    <input type="hidden" value="<?php echo $row_links['provide_card_node_a']; ?>" name="card_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_card_node_b']; ?>" name="card_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_timeslots_node_a']; ?>" name="timeslots_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_timeslots_node_b']; ?>" name="timeslots_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_subint_node_a']; ?>" name="subint_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_subint_node_b']; ?>" name="subint_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_port_node_a']; ?>" name="port_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_port_node_b']; ?>" name="port_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['servicetemplate']; ?>" name="template" />
                                    <input type="hidden" value="<?php echo $row_links['id']; ?>" name="additional_address_link" />
                                    
                                    <input type="hidden" value="<?php echo $_GET['container']; ?>" name="container" />
                                    
                                    <input type="submit" name="submit" value="Add Addresses" class="input_standard" />
                                </form>
                                
                                </div>
                                
								<?php } else { ?>
                                
                                	<div class="ipm_linkview" id="ipm_linkview_ipa_none"></div>
                                    
                                <?php }
								
							} else { ?>
                            
	                            <?php if ($row_links['provide_layer'] == 3) { 
									
									mysql_select_db($database_subman, $subman);
									$query_additional_address_node_a = "SELECT addresses.id AS addressid, linknetworks.id AS linknetid, linknetworks.network as networkid, networks.network AS _network, networks.container AS networkcontainer, networks.mask, networks.v6mask, addresses.* FROM linknetworks LEFT JOIN networks ON networks.id = linknetworks.network LEFT JOIN addresses ON addresses.network = linknetworks.network WHERE linknetworks.link = '".$row_links['id']."' AND (addresses.portid = '".$row_port_node_a['id']."' OR addresses.subintid = '".$row_subint_node_a['id']."')";
									$additional_address_node_a = mysql_query($query_additional_address_node_a, $subman) or die(mysql_error());
									$row_additional_address_node_a = mysql_fetch_assoc($additional_address_node_a);
									$totalRows_additional_address_node_a = mysql_num_rows($additional_address_node_a);
									
									?>
                                    
                                    <div class="ipm_linkview" id="ipm_linkview_ipa">
						
									<?php if ($row_network['v6mask'] == "" ) { ?><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=&amp;parent=<?php echo $row_network['id']; ?>" title="Browse network"><?php echo long2ip($row_address_node_a['address']).get_slash($row_network['mask']); ?></a><?php } else { ?><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=&amp;parent=<?php echo $row_network['id']; ?>" title="Browse network"><?php echo Net_IPv6::compress(long2ipv6($row_address_node_a['address'])); ?>/<?php echo $row_network['v6mask']; ?></a><?php } ?>
									<?php 
										if ($totalRows_additional_address_node_a > 0) { 
										do { ?>
                                    
                                    	<br /><?php
									if ($row_additional_address_node_a['v6mask'] == "") { 
                                     ?>
                                    <form action="" method="post" target="_self" name="frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>" id="frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>">
                                    	<input type="hidden" name="linknet" value="<?php echo $row_additional_address_node_a['linknetid']; ?>" />
                                        <input type="hidden" name="network" value="<?php echo $row_additional_address_node_a['_network']; ?>" />
                                        <input type="hidden" name="address" value="<?php echo $row_additional_address_node_a['addressid']; ?>" />
                                        <input type="hidden" name="mask" value="<?php echo $row_additional_address_node_a['mask']; ?>" />
                                        <input type="hidden" name="link" value="<?php echo $row_links['id']; ?>" />
                                        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                                        <input type="hidden" name="device" value="<?php echo $_GET['device']; ?>" />
                                        <input type="hidden" name="devicegroup" value="<?php echo $_GET['group']; ?>" />
                                        <input type="hidden" name="port" value="<?php echo $_GET['port']; ?>" />
                                        <input type="hidden" name="MM_delete" value="frm_delete_secondary" />
                                    </form>
                                    <?php if ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
                                    <a href="#" title="Delete secondary network" onClick="if (confirm('Are you sure you want to remove the secondary network <?php echo long2ip($row_additional_address_node_a['_network']); ?>/<?php echo get_slash($row_additional_address_node_a['mask']); ?>?')) { document.getElementById('frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>').submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete secondary network" border="0" align="absmiddle" /></a>
                                    <?php }
                                    } else { 
									 ?>
                                    <form action="" method="post" target="_self" name="frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>" id="frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>">
                                    	<input type="hidden" name="linknet" value="<?php echo $row_additional_address_node_a['linknetid']; ?>" />
                                        <input type="hidden" name="network" value="<?php echo $row_additional_address_node_a['_network']; ?>" />
                                        <input type="hidden" name="address" value="<?php echo $row_additional_address_node_a['addressid']; ?>" />
                                        <input type="hidden" name="mask" value="<?php echo $row_additional_address_node_a['v6mask']; ?>" />
                                        <input type="hidden" name="link" value="<?php echo $row_links['id']; ?>" />
                                        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                                        <input type="hidden" name="device" value="<?php echo $_GET['device']; ?>" />
                                        <input type="hidden" name="devicegroup" value="<?php echo $_GET['group']; ?>" />
                                        <input type="hidden" name="port" value="<?php echo $_GET['port']; ?>" />
                                        <input type="hidden" name="MM_delete" value="frm_delete_secondary" />
                                    </form>
                                    <?php 
                                    if ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
                                    <a href="#" title="Delete secondary network" onClick="if (confirm('Are you sure you want to remove the secondary network <?php echo long2ipv6($row_additional_address_node_a['_network']); ?>/<?php echo $row_additional_address_node_a['v6mask']; ?>?')) { document.getElementById('frm_delete_secondary<?php echo $row_additional_address_node_a['linknetid']; ?>').submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete secondary network" border="0" align="absmiddle" /></a>
                                    <?php }
                                    } ?>
                                    <?php if ($row_additional_address_node_a['v6mask'] == "" ) { ?><a href="?browse=networks&amp;container=<?php echo $row_additional_address_node_a['networkcontainer']; ?>&amp;group=&amp;parent=<?php echo $row_additional_address_node_a['network']; ?>" title="Browse network"><?php echo long2ip($row_additional_address_node_a['address']).get_slash($row_additional_address_node_a['mask']); ?></a><?php } else { ?><a href="?browse=networks&amp;container=<?php echo $row_additional_address_node_a['networkcontainer']; ?>&amp;group=&amp;parent=<?php echo $row_additional_address_node_a['network']; ?>" title="Browse network"><?php echo Net_IPv6::compress(long2ipv6($row_additional_address_node_a['address'])); ?>/<?php echo $row_additional_address_node_a['v6mask']; ?></a><?php } ?>
                                    
                                    <?php } while ($row_additional_address_node_a = mysql_fetch_assoc($additional_address_node_a)); 
										} ?>
                                    
                                <br /><br /><form action="handler.php" method="post" name="providelink" target="_self">
                                
                                	<input type="hidden" value="step3" name="provide" />
                                    <input type="hidden" value="1" name="additional_address" />
                                    <input type="hidden" value="<?php echo $row_links['provide_node_a']; ?>" name="node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_node_b']; ?>" name="node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_layer']; ?>" name="layer" />
                                    <input type="hidden" value="<?php echo $row_links['provide_logical_node_a']; ?>" name="logical_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_logical_node_b']; ?>" name="logical_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_cct']; ?>" name="cct" />
                                    <input type="hidden" value="<?php echo $row_links['provide_customer']; ?>" name="customer" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vpn']; ?>" name="vpn" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vlan_node_a']; ?>" name="vlan_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vlan_node_b']; ?>" name="vlan_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_xconnectpool']; ?>" name="xconnect_pool" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vrf']; ?>" name="vrf" />
                                    <input type="hidden" value="<?php echo $row_links['provide_card_node_a']; ?>" name="card_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_card_node_b']; ?>" name="card_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_timeslots_node_a']; ?>" name="timeslots_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_timeslots_node_b']; ?>" name="timeslots_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_subint_node_a']; ?>" name="subint_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_subint_node_b']; ?>" name="subint_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_port_node_a']; ?>" name="port_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_port_node_b']; ?>" name="port_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['servicetemplate']; ?>" name="template" />
                                    <input type="hidden" value="<?php echo $row_links['id']; ?>" name="additional_address_link" />
                                    
                                    <input type="hidden" value="<?php echo $_GET['container']; ?>" name="container" />
                                    
                                    <input type="submit" name="submit" value="Add Addresses" class="input_standard" />
                                </form>
                                
                                </div>
				

								<?php } else { ?>
                                
                                	<div class="ipm_linkview" id="ipm_linkview_ipa_none"></div>
                                    
                                <?php } ?>
		
                            <?php } ?>
                            
                            
                            <?php if ($totalRows_vpn > 0) { ?>
                        	
                            <div class="ipm_linkview" id="ipm_linkview_vpna">
                            
                            <div style="position:relative; top: 50%; margin-top:-5px"><a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $row_vpn['providerid']; ?>&amp;vpn=<?php echo $row_vpn['id']; ?>" title="Browse VPN">[<?php echo $row_vpn['asnumber']; ?>] <?php echo $row_vpn['name']; ?></a> <?php echo getVPNLayer($row_vpn['layer']); ?><br />
                        	<?php if ($totalRows_vrf > 0) { ?>VRF: <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $row_vpn['providerid']; ?>&amp;vpn=<?php echo $row_vpn['id']; ?>&amp;vrf=<?php echo $row_vrf['id']; ?>" title="Browse VRF ports"><?php echo $row_vrf['name']; ?></a><?php } elseif ($totalRows_xconnect > 0) { ?>PW: <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $row_vpn['providerid']; ?>&amp;vpn=<?php echo $row_vpn['id']; ?>&amp;xconnect=<?php echo $row_xconnect['id']; ?>" title="Browse pseudowire ports"><?php echo $row_xconnect['xconnectid']; ?></a><?php } ?>
                            </div></div>
                            
                            <?php } else { ?>
                            	<div class="ipm_linkview" id="ipm_linkview_vpna_none"></div>
                            <?php } ?>
                     
	</div>
                           
                        	
							<?php if ($totalRows_node_b > 0) { ?>
                            
                            	<div class="ipm_linkviewb">
                            
								<?php if ($totalRows_vpn > 0 && $row_links['provide_pece'] != 1) { ?>
                                
                                <div class="ipm_linkviewb" id="ipm_linkview_vpnb">
                                
                                <div style="position:relative; top: 50%; margin-top:-5px"><a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $row_vpn['providerid']; ?>&amp;vpn=<?php echo $row_vpn['id']; ?>" title="Browse VPN">[<?php echo $row_vpn['asnumber']; ?>] <?php echo $row_vpn['name']; ?></a> <?php echo getVPNLayer($row_vpn['layer']); ?><br />
                                <?php if ($totalRows_vrf > 0) { ?>VRF: <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $row_vpn['providerid']; ?>&amp;vpn=<?php echo $row_vpn['id']; ?>&amp;vrf=<?php echo $row_vrf['id']; ?>" title="Browse VRF ports"><?php echo $row_vrf['name']; ?></a><?php } elseif ($totalRows_xconnect > 0) { ?>PW: <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $row_vpn['providerid']; ?>&amp;vpn=<?php echo $row_vpn['id']; ?>&amp;xconnect=<?php echo $row_xconnect['id']; ?>" title="Browse pseudowire ports"><?php echo $row_xconnect['xconnectid']; ?></a><?php } ?>
                                
                                </div></div>
                                
                                <?php } elseif ($row_links['provide_pece'] == 1) { ?>
                                    <div class="ipm_linkviewb" id="ipm_linkview_vpnb_none"></div>
                                <?php } else { ?>
                        			<div class="ipm_linkviewb" id="ipm_linkview_vpnb_none"></div>
                                <?php } ?>
                            
                             <?php if ($totalRows_subint_node_b > 0) { ?>
                            
                            	<?php if ($row_links['provide_layer'] == 3 && $totalRows_node_b > 0) { 
									mysql_select_db($database_subman, $subman);
									$query_additional_address_node_b = "SELECT linknetworks.id AS linknetid, linknetworks.network as networkid, networks.network AS _network, networks.container AS networkcontainer, networks.mask, networks.v6mask, addresses.* FROM linknetworks LEFT JOIN networks ON networks.id = linknetworks.network LEFT JOIN addresses ON addresses.network = linknetworks.network WHERE linknetworks.link = '".$row_links['id']."' AND (addresses.portid = '".$row_port_node_b['id']."' OR addresses.subintid = '".$row_subint_node_b['id']."')";
									$additional_address_node_b = mysql_query($query_additional_address_node_b, $subman) or die(mysql_error());
									$row_additional_address_node_b = mysql_fetch_assoc($additional_address_node_b);
									$totalRows_additional_address_node_b = mysql_num_rows($additional_address_node_b);
									
									?>
                                    
                                    <div class="ipm_linkviewb" id="ipm_linkview_ipb">
						
									<?php if ($row_network['v6mask'] == "" ) { ?><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=&amp;parent=<?php echo $row_network['id']; ?>" title="Browse network"><?php echo long2ip($row_address_node_b_subint['address']).get_slash($row_network['mask']); ?></a><?php } else { ?><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=&amp;parent=<?php echo $row_network['id']; ?>" title="Browse network"><?php echo Net_IPv6::compress(long2ipv6($row_address_node_b_subint['address'])); ?>/<?php echo $row_network['v6mask']; ?></a><?php } ?>
									<?php 
										if ($totalRows_additional_address_node_b > 0) { 
										do { ?>
                                    
                                    	<br /><?php
									if ($row_additional_address_node_b['v6mask'] == "") { 
                                     ?>
                                    
                                    <?php if ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
                                    <a href="#" title="Delete secondary network" onClick="if (confirm('Are you sure you want to remove the secondary network <?php echo long2ip($row_additional_address_node_b['_network']); ?>/<?php echo get_slash($row_additional_address_node_b['mask']); ?>?')) { document.frm_delete_secondary<?php echo $row_additional_address_node_b['linknetid']; ?>.submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete secondary network" border="0" align="absmiddle" /></a>
                                    <?php }
                                    } else { 
									 ?>
                                    
                                    <?php 
                                    if ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
                                    <a href="#" title="Delete secondary network" onClick="if (confirm('Are you sure you want to remove the secondary network <?php echo long2ipv6($row_additional_address_node_b['_network']); ?>/<?php echo $row_additional_address_node_b['v6mask']; ?>?')) { document.frm_delete_secondary<?php echo $row_additional_address_node_b['linknetid']; ?>.submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete secondary network" border="0" align="absmiddle" /></a>
                                    <?php }
                                    } ?>
                                    <?php if ($row_additional_address_node_b['v6mask'] == "" ) { ?><a href="?browse=networks&amp;container=<?php echo $row_additional_address_node_b['networkcontainer']; ?>&amp;group=&amp;parent=<?php echo $row_additional_address_node_b['network']; ?>" title="Browse network"><?php echo long2ip($row_additional_address_node_b['address']).get_slash($row_additional_address_node_b['mask']); ?></a><?php } else { ?><a href="?browse=networks&amp;container=<?php echo $row_additional_address_node_b['networkcontainer']; ?>&amp;group=&amp;parent=<?php echo $row_additional_address_node_b['network']; ?>" title="Browse network"><?php echo Net_IPv6::compress(long2ipv6($row_additional_address_node_b['address'])); ?>/<?php echo $row_additional_address_node_b['v6mask']; ?></a><?php } ?>
                                    
                                    <?php } while ($row_additional_address_node_b = mysql_fetch_assoc($additional_address_node_b)); 
										} ?>
                                        
                                        <br /><br /><form action="handler.php" method="post" name="providelink" target="_self">
                                
                                	<input type="hidden" value="step3" name="provide" />
                                    <input type="hidden" value="1" name="additional_address" />
                                    <input type="hidden" value="<?php echo $row_links['provide_node_a']; ?>" name="node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_node_b']; ?>" name="node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_layer']; ?>" name="layer" />
                                    <input type="hidden" value="<?php echo $row_links['provide_logical_node_a']; ?>" name="logical_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_logical_node_b']; ?>" name="logical_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_cct']; ?>" name="cct" />
                                    <input type="hidden" value="<?php echo $row_links['provide_customer']; ?>" name="customer" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vpn']; ?>" name="vpn" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vlan_node_a']; ?>" name="vlan_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vlan_node_b']; ?>" name="vlan_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_xconnectpool']; ?>" name="xconnect_pool" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vrf']; ?>" name="vrf" />
                                    <input type="hidden" value="<?php echo $row_links['provide_card_node_a']; ?>" name="card_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_card_node_b']; ?>" name="card_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_timeslots_node_a']; ?>" name="timeslots_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_timeslots_node_b']; ?>" name="timeslots_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_subint_node_a']; ?>" name="subint_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_subint_node_b']; ?>" name="subint_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_port_node_a']; ?>" name="port_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_port_node_b']; ?>" name="port_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['servicetemplate']; ?>" name="template" />
                                    <input type="hidden" value="<?php echo $row_links['id']; ?>" name="additional_address_link" />
                                    
                                    <input type="hidden" value="<?php echo $_GET['container']; ?>" name="container" />
                                    
                                    <input type="submit" name="submit" value="Add Addresses" class="input_standard" />
                                </form>
                                	
                               	  </div>
									 
									 <?php } else { ?>
                                
                                        <div class="ipm_linkviewb" id="ipm_linkview_ipb_none"></div>
                                        
                                    <?php } ?>
                                
                            <?php } else { ?>
                            
	                            <?php if ($row_links['provide_layer'] == 3) { 
									mysql_select_db($database_subman, $subman);
									$query_additional_address_node_b = "SELECT linknetworks.id AS linknetid, linknetworks.network as networkid, networks.network AS _network, networks.container AS networkcontainer, networks.mask, networks.v6mask, addresses.* FROM linknetworks LEFT JOIN networks ON networks.id = linknetworks.network LEFT JOIN addresses ON addresses.network = linknetworks.network WHERE linknetworks.link = '".$row_links['id']."' AND (addresses.portid = '".$row_port_node_b['id']."' OR addresses.subintid = '".$row_subint_node_b['id']."')";
									$additional_address_node_b = mysql_query($query_additional_address_node_b, $subman) or die(mysql_error());
									$row_additional_address_node_b = mysql_fetch_assoc($additional_address_node_b);
									$totalRows_additional_address_node_b = mysql_num_rows($additional_address_node_b);
									
									?>
                                    
                                    <div class="ipm_linkviewb" id="ipm_linkview_ipb">
						
									<?php if ($row_network['v6mask'] == "" ) { ?><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=&amp;parent=<?php echo $row_network['id']; ?>" title="Browse network"><?php echo long2ip($row_address_node_b['address']).get_slash($row_network['mask']); ?></a><?php } else { ?><a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=&amp;parent=<?php echo $row_network['id']; ?>" title="Browse network"><?php echo Net_IPv6::compress(long2ipv6($row_address_node_b['address'])); ?>/<?php echo $row_network['v6mask']; ?></a><?php } ?>
									
									<?php 
										if ($totalRows_additional_address_node_b > 0) { 
										do { ?>
                                    
                                    	<br /><?php
									if ($row_additional_address_node_b['v6mask'] == "") { 
                                     ?>
                                    
                                    <?php if ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
                                    <a href="#" title="Delete secondary network" onClick="if (confirm('Are you sure you want to remove the secondary network <?php echo long2ip($row_additional_address_node_b['_network']); ?>/<?php echo get_slash($row_additional_address_node_b['mask']); ?>?')) { document.frm_delete_secondary<?php echo $row_additional_address_node_b['linknetid']; ?>.submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete secondary network" border="0" align="absmiddle" /></a>
                                    <?php }
                                    } else { 
									 ?>
                                    
                                    <?php 
                                    if ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
                                    <a href="#" title="Delete secondary network" onClick="if (confirm('Are you sure you want to remove the secondary network <?php echo long2ipv6($row_additional_address_node_b['_network']); ?>/<?php echo $row_additional_address_node_b['v6mask']; ?>?')) { document.frm_delete_secondary<?php echo $row_additional_address_node_b['linknetid']; ?>.submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete secondary network" border="0" align="absmiddle" /></a>
                                    <?php }
                                    } ?>
                                    <?php if ($row_additional_address_node_b['v6mask'] == "" ) { ?><a href="?browse=networks&amp;container=<?php echo $row_additional_address_node_b['networkcontainer']; ?>&amp;group=&amp;parent=<?php echo $row_additional_address_node_b['network']; ?>" title="Browse network"><?php echo long2ip($row_additional_address_node_b['address']).get_slash($row_additional_address_node_b['mask']); ?></a><?php } else { ?><a href="?browse=networks&amp;container=<?php echo $row_additional_address_node_b['networkcontainer']; ?>&amp;group=&amp;parent=<?php echo $row_additional_address_node_b['network']; ?>" title="Browse network"><?php echo Net_IPv6::compress(long2ipv6($row_additional_address_node_b['address'])); ?>/<?php echo $row_additional_address_node_b['v6mask']; ?></a><?php } ?>
                                    
                                    <?php } while ($row_additional_address_node_b = mysql_fetch_assoc($additional_address_node_b)); 
										} ?>
									
                                    <br /><br /><form action="handler.php" method="post" name="providelink" target="_self">
                                
                                	<input type="hidden" value="step3" name="provide" />
                                    <input type="hidden" value="1" name="additional_address" />
                                    <input type="hidden" value="<?php echo $row_links['provide_node_a']; ?>" name="node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_node_b']; ?>" name="node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_layer']; ?>" name="layer" />
                                    <input type="hidden" value="<?php echo $row_links['provide_logical_node_a']; ?>" name="logical_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_logical_node_b']; ?>" name="logical_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_cct']; ?>" name="cct" />
                                    <input type="hidden" value="<?php echo $row_links['provide_customer']; ?>" name="customer" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vpn']; ?>" name="vpn" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vlan_node_a']; ?>" name="vlan_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vlan_node_b']; ?>" name="vlan_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_xconnectpool']; ?>" name="xconnect_pool" />
                                    <input type="hidden" value="<?php echo $row_links['provide_vrf']; ?>" name="vrf" />
                                    <input type="hidden" value="<?php echo $row_links['provide_card_node_a']; ?>" name="card_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_card_node_b']; ?>" name="card_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_timeslots_node_a']; ?>" name="timeslots_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_timeslots_node_b']; ?>" name="timeslots_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_subint_node_a']; ?>" name="subint_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_subint_node_b']; ?>" name="subint_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['provide_port_node_a']; ?>" name="port_node_a" />
                                    <input type="hidden" value="<?php echo $row_links['provide_port_node_b']; ?>" name="port_node_b" />
                                    <input type="hidden" value="<?php echo $row_links['servicetemplate']; ?>" name="template" />
                                    <input type="hidden" value="<?php echo $row_links['id']; ?>" name="additional_address_link" />
                                    
                                    <input type="hidden" value="<?php echo $_GET['container']; ?>" name="container" />
                                    
                                    <input type="submit" name="submit" value="Add Addresses" class="input_standard" />
                                </form>
                                
                                </div>
								
								<?php } else { ?>
                                
                                    <div class="ipm_linkviewb" id="ipm_linkview_ipb_none"></div>
                                    
                                <?php } ?>
                                
                                    
                            <?php } ?>
                            
                            <?php if ($row_links['provide_vlan_node_b']) { ?>
                            
                            <div class="ipm_linkviewb" id="ipm_linkview_vlanb">
                        	
                        	<a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_vlan_node_b['devicegroup']; ?>&amp;device=<?php echo $row_node_b['id']; ?>&amp;group=<?php echo $row_node_b['devicegroup']; ?>&amp;vlanpool=<?php echo $row_vlan_node_b['vlanpool']; ?>&amp;vlan=<?php echo $row_vlan_node_b['id']; ?>" title="Browse VLAN"><img src="images/ipm_icon_vlan.png" alt="VLAN icon" border="0" /><br />VLAN <?php echo $row_vlan_node_b['number']; ?></a>
                        	
                        	 </div>
                            
                            <?php } else { ?>
                            	<div class="ipm_linkview" id="ipm_linkview_vlanb"><img src="images/ipm_icon_vlan_none.png" alt="VLAN icon" border="0" /></div>
                            <?php } ?>
                            
                            <div class="ipm_linkview" id="ipm_linkview_portb_icon"><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_node_b['devicegroup']; ?>&amp;device=<?php echo $row_node_b['id']; ?>&amp;card=<?php echo $row_card_node_b['id']; ?>" title="Browse device card <?php echo $row_card_node_b['cardtypename']; ?> - <?php if (!(isset($row_card_node_b['rack'])) && !(isset($row_card_node_b['module'])) && !(isset($row_card_node_b['slot']))) { echo "Virtual"; } else { if (isset($row_card_node_b['rack'])) { echo $row_card_node_b['rack']."/"; } if (isset($row_card_node_b['module'])) { echo $row_card_node_b['module'].'/'; } if (isset($row_card_node_b['slot'])) { echo $row_card_node_b['slot']; } } ?>/<?php echo $row_port_node_b['port']; ?><?php if ($totalRows_subint_node_b > 0) { ?>.<?php echo $row_subint_node_b['subint']; ?><?php } ?>"><img src="images/ipm_icon_port.gif" alt="Port icon" /><br />
                            <?php if (!(isset($row_card_node_b['rack'])) && !(isset($row_card_node_b['module'])) && !(isset($row_card_node_b['slot']))) { echo "Virtual"; } else { if (isset($row_card_node_b['rack'])) { echo $row_card_node_b['rack']."/"; } if (isset($row_card_node_b['module'])) { echo $row_card_node_b['module'].'/'; } if (isset($row_card_node_b['slot'])) { echo $row_card_node_b['slot']; } } ?>/<?php echo $row_port_node_b['port']; ?><?php if ($totalRows_subint_node_b > 0) { ?>.<?php echo $row_subint_node_b['subint']; ?><?php } ?>
                         	</a>
                         	</div>
                            
                            <div class="ipm_linkviewb" id="ipm_linkview_nodeb">
                            
                            <img src="images/<?php echo $row_node_b['image']; ?>" alt="<?php echo $row_node_b['devicetypename']; ?>" height="30" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_node_b['devicegroup']; ?>&amp;device=<?php echo $row_node_b['id']; ?>" title="Browse device"><?php echo $row_node_b['name']; ?></a>
                            
                            </div>
                            
                            </div>
                            
                         <?php } elseif ($row_vpn['layer'] == 4) { ?>
                            
                            	<div class="ipm_linkviewb_vpn">
                                	<p align="center"><strong>This is a multipoint layer 2 VPN.  There could be multiple circuits terminating in this VPN. Click on the pseudowire number to browse other ports in this multipoint VPN.</strong></p>
                                </div>
                            	
                            <?php } elseif ($row_vpn['layer'] == 3) { ?>
                            
                            	<div class="ipm_linkviewb_vpn">
                                	<p align="center"><strong>This is a layer 3 VPN.  There could be multiple circuits terminating in this VPN. Click on the VRF name to browse other ports in this VPN.</strong></p>
                                </div>
                            	
                            <?php } else { ?>
                            
                            	<div class="ipm_linkviewb_none">
                                	<p align="center"><strong>No endpoint for this circuit has been defined.</strong></p>
                                </div>
                                
                            <?php } ?>
                            	
                    
                    	<div style="clear:both"></div>
                        
                         <?php if ($totalRows_routes > 0) { ?>
                         
                         <table>
                        <tr>
                        	
                        	<td><strong>Routes</strong>
                           	<?php do { ?>
                                	<br />
                                    <?php
									if ($row_routes['v6mask'] == "") { 
                                    if ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
                                    <a href="#" title="Delete route" onClick="if (confirm('Are you sure you want to remove the route <?php echo long2ip($row_routes['_network']); ?>/<?php echo get_slash($row_routes['mask']); ?>?')) { document.frm_delete_route<?php echo $row_routes['id']; ?>.submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete route" border="0" align="absmiddle" /></a>
                                    <?php } ?> 
                                    <a href="?browse=networks&amp;container=<?php echo $row_routes['networkcontainer']; ?>&amp;parent=<?php echo $row_routes['networkid']; ?>" title="Browse network"><?php echo long2ip($row_routes['_network']); ?><?php echo get_slash($row_routes['mask']); ?></a> --> <?php echo $row_routes['nexthop']; ?> 
                                    <form action="" method="post" target="_self" name="frm_delete_route<?php echo $row_routes['id']; ?>" id="frm_delete_route<?php echo $row_routes['id']; ?>">
                                    	<input type="hidden" name="linknet" value="<?php echo $row_routes['id']; ?>" />
                                        <input type="hidden" name="network" value="<?php echo $row_routes['_network']; ?>" />
                                        <input type="hidden" name="mask" value="<?php echo $row_routes['mask']; ?>" />
                                        <input type="hidden" name="nexthop" value="<?php echo $row_routes['nexthop']; ?>" />
                                        <input type="hidden" name="link" value="<?php echo $row_links['id']; ?>" />
                                        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                                        <input type="hidden" name="device" value="<?php echo $_GET['device']; ?>" />
                                        <input type="hidden" name="devicegroup" value="<?php echo $_GET['group']; ?>" />
                                        <input type="hidden" name="port" value="<?php echo $_GET['port']; ?>" />
                                        <input type="hidden" name="MM_delete" value="frm_delete_route" />
                                    </form>
                                    <?php } else { 
									if ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
                                    <a href="#" title="Delete route" onClick="if (confirm('Are you sure you want to remove the route <?php echo Net_IPv6::Compress(long2ipv6($row_routes['_network'])); ?>/<?php echo $row_routes['v6mask']; ?>?')) { document.frm_delete_route<?php echo $row_routes['id']; ?>.submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete route" border="0" align="absmiddle" /></a>
                                    <?php } ?> 
                                    <a href="?browse=networks&amp;container=<?php echo $row_routes['networkcontainer']; ?>&amp;parent=<?php echo $row_routes['networkid']; ?>" title="Browse network"><?php echo Net_IPv6::Compress(long2ipv6($row_routes['_network'])); ?>/<?php echo $row_routes['v6mask']; ?></a> --> <?php echo $row_routes['nexthop']; ?> 
                                    <form action="" method="post" target="_self" name="frm_delete_route<?php echo $row_routes['id']; ?>" id="frm_delete_route<?php echo $row_routes['id']; ?>">
                                    	<input type="hidden" name="linknet" value="<?php echo $row_routes['id']; ?>" />
                                        <input type="hidden" name="network" value="<?php echo $row_routes['_network']; ?>" />
                                        <input type="hidden" name="mask" value="<?php echo $row_routes['v6mask']; ?>" />
                                        <input type="hidden" name="nexthop" value="<?php echo $row_routes['nexthop']; ?>" />
                                        <input type="hidden" name="link" value="<?php echo $row_links['id']; ?>" />
                                        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
                                        <input type="hidden" name="device" value="<?php echo $_GET['device']; ?>" />
                                        <input type="hidden" name="devicegroup" value="<?php echo $_GET['group']; ?>" />
                                        <input type="hidden" name="port" value="<?php echo $_GET['port']; ?>" />
                                        <input type="hidden" name="MM_delete" value="frm_delete_route" />
                                    </form>
                                    <?php } ?>
                                <?php } while ($row_routes = mysql_fetch_assoc($routes)); ?>
                            </td>
                        </tr>
                      </table>
                      
                  <?php } ?>
                      
                      <br />
                        
                <?php 
					$count++;
					
				} while ($row_links = mysql_fetch_assoc($links)); ?>
            <?php } else { ?>
            	<p>There is no link information to display.  If you are trying to browse an associated link, it is possible that the associated link was removed manually.</p>
            <?php } ?>
		
        <?php
		}
		
		elseif ($_POST['action'] == "provide_link") { 
			
			mysql_select_db($database_subman, $subman);
			$query_serviceTemplates = "SELECT * FROM servicetemplate WHERE container = ".$_GET['container']." AND servicetemplate.disabled = 0 ORDER BY servicetemplate.name";
			$serviceTemplates = mysql_query($query_serviceTemplates, $subman) or die(mysql_error());
			$row_serviceTemplates = mysql_fetch_assoc($serviceTemplates);
			$totalRows_serviceTemplates = mysql_num_rows($serviceTemplates);
			?>
			
			
			
            <h5>Service Template</h5>
            
            <form action="handler.php" id="providelink" method="post" target="_self">
            
            	<select name="template" class="input_standard">
                
                	<?php if (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127) { ?>
                    <option value="">None (superusers only)</option>
					<?php } ?>
                                        
                    <?php
					if ($totalRows_serviceTemplates > 0) {
                    do {  
					mysql_select_db($database_subman, $subman);
					$query_serviceTemplateLink = "SELECT * FROM servicetemplate WHERE templatelink = ".$row_serviceTemplates['id']." AND servicetemplate.disabled = 0";
					$serviceTemplateLink = mysql_query($query_serviceTemplateLink, $subman) or die(mysql_error());
					$row_serviceTemplateLink = mysql_fetch_assoc($serviceTemplateLink);
					$totalRows_serviceTemplateLink = mysql_num_rows($serviceTemplateLink);
?>
                  <option value="<?php echo $row_serviceTemplates['id']?>" <?php if ($totalRows_serviceTemplateLink > 0 && !(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127)) { ?> disabled="disabled" <?php } elseif (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127)) { ?> selected="selected"<?php } ?>><?php echo $row_serviceTemplates['name']?> <?php if ($totalRows_serviceTemplateLink > 0) { ?> <em>- linked to <?php echo $row_serviceTemplateLink['name']; ?></em> <?php } ?></option>
                  <?php
} while ($row_serviceTemplates = mysql_fetch_assoc($serviceTemplates));
  $rows = mysql_num_rows($serviceTemplates);
  if($rows > 0) {
      mysql_data_seek($serviceTemplates, 0);
	  $row_serviceTemplates = mysql_fetch_assoc($serviceTemplates);
  } 
					} ?>
                
                </select>
            	
                <input type="hidden" value="step0" name="provide" />
                <input type="hidden" value="<?php echo $_GET['container']; ?>" name="container" />
                <br /><br />
                
                <a href="#" onClick="document.getElementById('providelink').submit();" class="input_providewizard">Next</a>
                
               	<a href="handler.php?action=provide_cancel&amp;container=<?php echo $_GET['container']; ?>" <?php if ($_SESSION['linked'] != "") { ?>onclick="if (window.confirm('This link is required by the template.  If you cancel now, only a superuser can provide the rest of this link.  Alternatively, you can cancel, and delete any other links associated with this template, and then re-provide all links.')) { } else { return false; }" <?php } ?> class="input_providewizard">Cancel</a>
            </form> <br />
            
        <?php	
		}
		
		elseif ($_POST['action'] == "add_card") {
			
			if (!(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 10 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 10 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
                <p class="text_red">Error: You are not authorised to view the selected content.</p>
                <?php 
                                exit();
                        } 
						
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.image, devicetypes.name as devicetype FROM portsdevices LEFT JOIN devicetypes ON portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_cardTypes = "SELECT cardtypes.* FROM cardtypes ORDER BY cardtypes.name";
			$cardTypes = mysql_query($query_cardTypes, $subman) or die(mysql_error());
			$row_cardTypes = mysql_fetch_assoc($cardTypes);
			$totalRows_cardTypes = mysql_num_rows($cardTypes);
			
			?>
                        
                        Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <strong><?php echo $row_getDevice['name']; ?></strong><br />
                <br />
            <form action="" method="post" name="frm_add_card" id="frm_add_card" onSubmit="MM_validateForm('startport','','RIsNum','endport','','RIsNum');return document.MM_returnValue">
              <table>
              	<tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Card Type:</td>
                  <td><select name="cardtype" class="input_standard" id="cardtype">
            <?php
do {  
?>
            <option value="<?php echo $row_cardTypes['id']?>"><?php echo $row_cardTypes['name']?></option>
            <?php
} while ($row_cardTypes = mysql_fetch_assoc($cardTypes));
  $rows = mysql_num_rows($cardTypes);
  if($rows > 0) {
      mysql_data_seek($cardTypes, 0);
	  $row_cardTypes = mysql_fetch_assoc($cardTypes);
  }
?>
          </select></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Timeslots:</td>
                  <td><input name="timeslots" type="text" class="input_standard" id="timeslots" size="10" maxlength="3" /> Leave blank for non-TDM cards</td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Bandwidth per Timeslot:</td>
                  <td><input type="text" name="bandwidth" size="10" maxlength="5" class="input_standard" id="bandwidth"> Leave blank for non-TDM cards</td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Rack / Module / Slot:</td>
                  <td><input type="text" name="rack" size="5" maxlength="2" class="input_standard" id="rack"> / <input type="text" name="module" size="5" maxlength="2" class="input_standard" id="module"> / <input type="text" name="slot" size="5" maxlength="2" class="input_standard" id="slot"> Leave all fields blank for virtual cards (e.g. SVIs, Etherchannels)</td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Start Port - End Port:</td>
                  <td><input type="text" name="startport" size="5" maxlength="5" class="input_standard" id="startport"> - <input type="text" name="endport" size="5" maxlength="5" class="input_standard" id="endport"></td>
                </tr>
                <tr> </tr>
                <tr> </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Add card" /> <input name="cancel" type="button" class="input_standard" id="cancel" onClick="MM_callJS('history.back();')" value="Cancel" /></td>
                </tr>
              </table>
              <input type="hidden" name="device" value="<?php echo $_GET['device']; ?>" />
              <input type="hidden" name="MM_insert" value="frm_add_card" />
            </form>
            <p>&nbsp;</p> 
		
        <?php
		}
		
		elseif ($_POST['action'] == "delete_card" && $_GET['card'] != "" && $_GET['group'] != "" && $_GET['device'] != "") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getCard = "SELECT cards.*, cardtypes.name as cardtypename, portsdevices.id as deviceid, portsdevices.name as devicename, portgroups.id as groupid, devicetypes.image, devicetypes.name as devicetype, portgroups.name as groupname FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE cards.id = ".$_GET['card']."";
			$getCard = mysql_query($query_getCard, $subman) or die(mysql_error());
			$row_getCard = mysql_fetch_assoc($getCard);
			$totalRows_getCard = mysql_num_rows($getCard);
			
            if (!(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($_GET['group'], $_SESSION['MM_Username']) > 20 && getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getCard['groupname']; ?>"><?php echo $row_getCard['groupname']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getCard['image']; ?>" alt="<?php echo $row_getCard['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $_GET['device']; ?>" title="Browse device"><?php echo $row_getCard['devicename']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php if (!(isset($row_getCard['rack'])) && !(isset($row_getCard['module'])) && !(isset($row_getCard['slot']))) { echo "Virtual"; } else { if (isset($row_getCard['rack'])) { echo $row_getCard['rack']."/"; } if (isset($row_getCard['module'])) { echo $row_getCard['module'].'/'; } if (isset($row_getCard['slot'])) { echo $row_getCard['slot']; } } ?> <?php echo $row_getCard['cardtypename']; ?></strong><br />
    <br />
    
    <form action="" method="post" target="_self" name="frm_delete_card" id="frm_delete_card">
      <p>Are you sure you want to delete this line card?</p>
      <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
      <input name="id" type="hidden" id="id" value="<?php echo $_GET['card']; ?>" />
      <input name="group" type="hidden" id="group" value="<?php echo $_GET['group']; ?>" />
      <input name="device" type="hidden" id="device" value="<?php echo $_GET['device']; ?>" />
      <input name="frm_delete_card" type="hidden" id="frm_delete_card" value="1" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
    </form>
<?php
		}
		
		elseif ($_POST['action'] == "delete_vlanpool" && $_GET['group'] != "" && $_GET['device'] != "" && $_GET['vlanpool'] != "") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getVlanpool = "SELECT vlanpool.*, portsdevices.id as deviceid, portsdevices.name as devicename, portgroups.id as groupid, devicetypes.image, devicetypes.name as devicetype, portgroups.name as groupname FROM vlanpool LEFT JOIN portsdevices ON portsdevices.id = vlanpool.device LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE vlanpool.id = ".$_GET['vlanpool']."";
			$getVlanpool = mysql_query($query_getVlanpool, $subman) or die(mysql_error());
			$row_getVlanpool = mysql_fetch_assoc($getVlanpool);
			$totalRows_getVlanpool = mysql_num_rows($getVlanpool);
			
            if (!(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($_GET['group'], $_SESSION['MM_Username']) > 20 && getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getVlanpool['groupname']; ?>"><?php echo $row_getVlanpool['groupname']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getVlanpool['image']; ?>" alt="<?php echo $row_getVlanpool['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $_GET['device']; ?>" title="Browse device"><?php echo $row_getVlanpool['devicename']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VLAN Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVlanpool['name']; ?></strong><br />
    <br />
    
    <form action="" method="post" target="_self" name="frm_delete_vlanpool" id="frm_delete_vlanpool">
      <p>Are you sure you want to delete this VLAN pool?</p>
      <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
      <input name="id" type="hidden" id="id" value="<?php echo $_GET['vlanpool']; ?>" />
      <input name="group" type="hidden" id="group" value="<?php echo $_GET['group']; ?>" />
      <input name="device" type="hidden" id="device" value="<?php echo $_GET['device']; ?>" />
      <input name="frm_delete_vlanpool" type="hidden" id="frm_delete_vlanpool" value="1" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
    </form>
<?php
		}
		
		elseif ($_POST['action'] == "delete_vlan" && $_GET['vlan'] != "" && $_GET['group'] != "" && $_GET['device'] != "" && $_GET['vlanpool'] != "") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getVlan = "SELECT vlan.*, vlanpool.id as vlanpoolid, vlanpool.name as vlanpoolname, portsdevices.id as deviceid, portsdevices.name as devicename, portgroups.id as groupid, devicetypes.image, devicetypes.name as devicetype, portgroups.name as groupname FROM vlan LEFT JOIN vlanpool ON vlanpool.id = vlan.vlanpool LEFT JOIN portsdevices ON portsdevices.id = vlanpool.device LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE vlan.id = ".$_GET['vlan']."";
			$getVlan = mysql_query($query_getVlan, $subman) or die(mysql_error());
			$row_getVlan = mysql_fetch_assoc($getVlan);
			$totalRows_getVlan = mysql_num_rows($getVlan);
			
            if (!(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($_GET['group'], $_SESSION['MM_Username']) > 20 && getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getVlan['groupname']; ?>"><?php echo $row_getVlan['groupname']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getVlan['image']; ?>" alt="<?php echo $row_getVlan['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $_GET['device']; ?>" title="Browse device"><?php echo $row_getVlan['devicename']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VLAN Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $_GET['device']; ?>&amp;vlanpool=<?php echo $_GET['vlanpool']; ?>" title="Browse VLAN Pool"><?php echo $row_getVlan['vlanpoolname']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VLAN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>[<?php echo $row_getVlan['number']; ?>] <?php echo $row_getVlan['name']; ?></strong><br />
    <br />
    
    <form action="" method="post" target="_self" name="frm_delete_vlan" id="frm_delete_vlan">
      <p>Are you sure you want to delete this VLAN?</p>
      <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
      <input name="id" type="hidden" id="id" value="<?php echo $_GET['vlan']; ?>" />
      <input name="group" type="hidden" id="group" value="<?php echo $_GET['group']; ?>" />
      <input name="device" type="hidden" id="device" value="<?php echo $_GET['device']; ?>" />
      <input name="vlanpool" type="hidden" id="vlanpool" value="<?php echo $_GET['vlanpool']; ?>" />
      <input name="frm_delete_vlan" type="hidden" id="frm_delete_vlan" value="1" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
    </form>
<?php
		}
		
		elseif ($_POST['action'] == "edit_vlan") {
			
			if (!(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 10 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 10 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
                <p class="text_red">Error: You are not authorised to view the selected content.</p>
                <?php 
                                exit();
                        } 
						
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.image, devicetypes.name as devicetype FROM portsdevices LEFT JOIN devicetypes ON portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getVlanPool = "SELECT * FROM vlanpool WHERE vlanpool.id = ".$_GET['vlanpool']."";
			$getVlanPool = mysql_query($query_getVlanPool, $subman) or die(mysql_error());
			$row_getVlanPool = mysql_fetch_assoc($getVlanPool);
			$totalRows_getVlanPool = mysql_num_rows($getVlanPool);
			
			mysql_select_db($database_subman, $subman);
			$query_customers = "SELECT * FROM customer WHERE container = '".$_GET['container']."' ORDER BY customer.name";
			$customers = mysql_query($query_customers, $subman) or die(mysql_error());
			$row_customers = mysql_fetch_assoc($customers);
			$totalRows_customers = mysql_num_rows($customers);
			
			mysql_select_db($database_subman, $subman);
			$query_getVlan = "SELECT * FROM vlan WHERE vlan.id = ".$_GET['vlan']."";
			$getVlan = mysql_query($query_getVlan, $subman) or die(mysql_error());
			$row_getVlan = mysql_fetch_assoc($getVlan);
			$totalRows_getVlan = mysql_num_rows($getVlan);
			?>
                        
                        Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $_GET['device']; ?>" title="Browse device"><?php echo $row_getDevice['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VLAN Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $_GET['device']; ?>&amp;vlanpool=<?php echo $_GET['vlanpool']; ?>" title="Browse VLAN pool"><?php echo $row_getVlanPool['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VLAN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>[<?php echo $row_getVlan['number']; ?>] <?php echo $row_getVlan['name']; ?></strong><br />
                <br />
            <form action="" method="post" name="frm_edit_vlan" id="frm_edit_vlan" onSubmit="MM_validateForm('vlanname','','R','number','','RinRange<?php echo $row_getVlanPool['poolstart']; ?>:<?php echo $row_getVlanPool['poolend']; ?>');return document.MM_returnValue">
              <table>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">VLAN Name:</td>
                  <td><input name="vlanname" type="text" class="input_standard" id="vlanname" size="32" maxlength="255" value="<?php echo $row_getVlan['name']; ?>" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Customer:</td>
                  <td><select name="customer" class="input_standard" id="customer">
            <?php
do {  
?>
            <option value="<?php echo $row_customers['id']?>" <?php if ($row_customers['id'] == $row_getVlan['customer']) echo "selected=\"selected\""; ?>><?php echo $row_customers['name']?></option>
            <?php
} while ($row_customers = mysql_fetch_assoc($customers));
  $rows = mysql_num_rows($customers);
  if($rows > 0) {
      mysql_data_seek($customers, 0);
	  $row_customers = mysql_fetch_assoc($customers);
  }
?>
          </select></td>
                </tr>
                <tr> </tr>
                <tr> </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Update VLAN" /> <input name="cancel" type="button" class="input_standard" id="cancel" onClick="MM_callJS('history.back();')" value="Cancel" /></td>
                </tr>
              </table>
              <input type="hidden" name="id" value="<?php echo $_GET['vlan']; ?>" />
              <input type="hidden" name="MM_update" value="frm_edit_vlan" />
            </form>
            <p>&nbsp;</p> 
		
        <?php
		}
		
		elseif ($_POST['action'] == "add_vlan") {
			
			if (!(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 10 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 10 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
                <p class="text_red">Error: You are not authorised to view the selected content.</p>
                <?php 
                                exit();
                        } 
						
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.image, devicetypes.name as devicetype FROM portsdevices LEFT JOIN devicetypes ON portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getVlanPool = "SELECT * FROM vlanpool WHERE vlanpool.id = ".$_GET['vlanpool']."";
			$getVlanPool = mysql_query($query_getVlanPool, $subman) or die(mysql_error());
			$row_getVlanPool = mysql_fetch_assoc($getVlanPool);
			$totalRows_getVlanPool = mysql_num_rows($getVlanPool);
			
			mysql_select_db($database_subman, $subman);
			$query_customers = "SELECT * FROM customer WHERE container = '".$_GET['container']."' ORDER BY customer.name";
			$customers = mysql_query($query_customers, $subman) or die(mysql_error());
			$row_customers = mysql_fetch_assoc($customers);
			$totalRows_customers = mysql_num_rows($customers);
			
			for ($i = $row_getVlanPool['poolstart']; $i < $row_getVlanPool['poolend']; $i ++) {
						
			mysql_select_db($database_subman, $subman);
			$query_nextVlan = "SELECT * FROM vlan WHERE vlan.number = ".$i." AND vlan.vlanpool = ".$_GET['vlanpool']."";
			$nextVlan = mysql_query($query_nextVlan, $subman) or die(mysql_error());
			$row_nextVlan = mysql_fetch_assoc($nextVlan);
			$totalRows_nextVlan = mysql_num_rows($nextVlan);

				if ( $totalRows_nextVlan == 0 ) {
							$nextVlan = $i;
							$i = $row_getVlanPool['poolend'];
							}
				}
			?>
                        
                        Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $_GET['device']; ?>" title="Browse device"><?php echo $row_getDevice['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVlanPool['name']; ?></strong><br />
                <br />
            <form action="" method="post" name="frm_add_vlan" id="frm_add_vlan" onSubmit="MM_validateForm('vlanname','','R','number','','RinRange<?php echo $row_getVlanPool['poolstart']; ?>:<?php echo $row_getVlanPool['poolend']; ?>');return document.MM_returnValue">
              <table>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">VLAN Name:</td>
                  <td><input name="vlanname" type="text" class="input_standard" id="vlanname" size="32" maxlength="255" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">VLAN iD:</td>
                  <td><input type="text" name="number" size="10" maxlength="5" class="input_standard" id="number" value="<?php echo $nextVlan; ?>"></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Customer:</td>
                  <td><select name="customer" class="input_standard" id="customer">
            <?php
do {  
?>
            <option value="<?php echo $row_customers['id']?>"><?php echo $row_customers['name']?></option>
            <?php
} while ($row_customers = mysql_fetch_assoc($customers));
  $rows = mysql_num_rows($customers);
  if($rows > 0) {
      mysql_data_seek($customers, 0);
	  $row_customers = mysql_fetch_assoc($customers);
  }
?>
          </select></td>
                </tr>
                <tr> </tr>
                <tr> </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Add VLAN" /> <input name="cancel" type="button" class="input_standard" id="cancel" onClick="MM_callJS('history.back();')" value="Cancel" /></td>
                </tr>
              </table>
              <input type="hidden" name="vlanpool" value="<?php echo $_GET['vlanpool']; ?>" />
              <input type="hidden" name="MM_insert" value="frm_add_vlan" />
            </form>
            <p>&nbsp;</p> 
		
        <?php
		}
		
		elseif ($_POST['action'] == "edit_vlanpool") {
			
			if (!(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 10 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 10 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
                <p class="text_red">Error: You are not authorised to view the selected content.</p>
                <?php 
                                exit();
                        } 
						
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.image, devicetypes.name as devicetype FROM portsdevices LEFT JOIN devicetypes ON portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getVlanpool = "SELECT * FROM vlanpool WHERE vlanpool.id = ".$_GET['vlanpool']."";
			$getVlanpool = mysql_query($query_getVlanpool, $subman) or die(mysql_error());
			$row_getVlanpool = mysql_fetch_assoc($getVlanpool);
			$totalRows_getVlanpool = mysql_num_rows($getVlanpool);
			?>
                        
                        Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $row_getDevice['id']; ?>" title="Browse device"><?php echo $row_getDevice['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVlanpool['name']; ?></strong><br />
                <br />
<form action="" method="post" name="frm_edit_vlanpool" id="frm_edit_vlanpool" onSubmit="MM_validateForm('poolname','','R','poolstart','','RIsNum','poolend','','RIsNum');return document.MM_returnValue">
              <table>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Pool Name:</td>
                  <td><input name="poolname" type="text" class="input_standard" id="poolname" size="32" maxlength="255" value="<?php echo $row_getVlanpool['name']; ?>" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Comments:</td>
                  <td><textarea name="comments" cols="32" rows="5" class="input_standard"><?php echo $row_getVlanpool['comments']; ?></textarea></td>
                </tr>
                <tr> </tr>
                <tr> </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Edit VLAN pool" /> <input name="cancel" type="button" class="input_standard" id="cancel" onClick="MM_callJS('history.back();')" value="Cancel" /></td>
                </tr>
      </table>
      <input type="hidden" name="id" value="<?php echo $row_getVlanpool['id']; ?>" />
              <input type="hidden" name="MM_update" value="frm_edit_vlanpool" />
    </form>
            <p>&nbsp;</p> 
            
        <?php	
		}
		
		elseif ($_POST['action'] == "add_vlanpool") {
			
			if (!(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 10 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 10 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
                <p class="text_red">Error: You are not authorised to view the selected content.</p>
                <?php 
                                exit();
                        } 
						
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.image, devicetypes.name as devicetype FROM portsdevices LEFT JOIN devicetypes ON portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			?>
                        
                        Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <?php echo $row_getDevice['name']; ?></strong><br />
                <br />
<form action="" method="post" name="frm_add_vlanpool" id="frm_add_vlanpool" onSubmit="MM_validateForm('poolname','','R','poolstart','','RIsNum','poolend','','RIsNum');return document.MM_returnValue">
              <table>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">Pool Name:</td>
                  <td><input name="poolname" type="text" class="input_standard" id="poolname" size="32" maxlength="255" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Start VLAN:</td>
                  <td><input type="text" name="poolstart" size="10" maxlength="5" class="input_standard" id="poolstart"></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Last VLAN:</td>
                  <td><input type="text" name="poolend" size="10" maxlength="5" class="input_standard" id="poolend"></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Comments:</td>
                  <td><textarea name="comments" cols="32" rows="5" class="input_standard"></textarea></td>
                </tr>
                <tr valign="baseline">
                	<td nowrap="nowrap" align="right" valign="top">Shared with group '<?php echo $row_getDeviceGroup['name']; ?>'?</td>
                    <td><input type="checkbox" name="shared" value="<?php echo $row_getDeviceGroup['id']; ?>" /></td>
                <tr> </tr>
                <tr> </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td><input type="submit" class="input_standard" value="Add VLAN pool" /> <input name="cancel" type="button" class="input_standard" id="cancel" onClick="MM_callJS('history.back();')" value="Cancel" /></td>
                </tr>
      </table>
      <input type="hidden" name="device" value="<?php echo $row_getDevice['id']; ?>" />
              <input type="hidden" name="MM_insert" value="frm_add_vlanpool" />
    </form>
            <p>&nbsp;</p> 
            
        <?php	
		}
		
		elseif ($_POST['action'] == "add_device") { 
			
			mysql_select_db($database_subman, $subman);
			$query_devicegroups = "SELECT * FROM portgroups WHERE container = ".$_GET['container']." ORDER BY portgroups.name";
			$devicegroups = mysql_query($query_devicegroups, $subman) or die(mysql_error());
			$row_devicegroups = mysql_fetch_assoc($devicegroups);
			$totalRows_devicegroups = mysql_num_rows($devicegroups);
			
			mysql_select_db($database_subman, $subman);
			$query_devicetypes = "SELECT * FROM devicetypes ORDER BY devicetypes.name";
			$devicetypes = mysql_query($query_devicetypes, $subman) or die(mysql_error());
			$row_devicetypes = mysql_fetch_assoc($devicetypes);
			$totalRows_devicetypes = mysql_num_rows($devicetypes);
            
            if (!(getDeviceGroupLevel($_GET['group'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
			} ?>
            
    <form action="<?php echo $editFormAction; ?>" method="post" name="form6" id="form6" onSubmit="MM_validateForm('devname','','R','descr','','R','managementip','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Device Name:</td>
          <td><input name="devname" type="text" class="input_standard" value="" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right" valign="top">Description:</td>
          <td><textarea name="descr" cols="32" rows="5" class="input_standard" id="descr"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Device Type:</td>
          <td><select name="devicetype" class="input_standard">
            <?php 
do {  
?>
            <option value="<?php echo $row_devicetypes['id']?>" ><?php echo $row_devicetypes['name']?></option>
            <?php
} while ($row_devicetypes = mysql_fetch_assoc($devicetypes));
?>
          </select></td>
        </tr>
        <tr> </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Device Group:</td>
          <td><select name="devicegroup" class="input_standard">
            <?php 
do {  
?>
            <option value="<?php echo $row_devicegroups['id']?>" <?php if (!(strcmp($row_devicegroups['id'], $_GET['group']))) {echo "SELECTED";} ?>><?php echo $row_devicegroups['name']?></option>
            <?php
} while ($row_devicegroups = mysql_fetch_assoc($devicegroups));
?>
          </select></td>
        </tr>
        <tr valign="baseline">
        	<td nowrap="nowrap" align="right">Management IPv4 address:</td>
            <td><input type="text" size="20" maxlength="35" name="managementip" class="input_standard" /></td>       
        </tr>
        <tr valign="baseline">
        	<td nowrap="nowrap" align="right">Read-Only SNMP Community String (v2c):</td>
            <td><input type="text" size="20" maxlength="35" name="snmpcommunity" class="input_standard" /></td>       
        </tr>
        <tr> </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add device" /></td>
        </tr>
      </table>
      <input type="hidden" name="MM_insert" value="form6" />
    </form>
    <p>&nbsp;</p>
<?php
		}
		
		elseif ($_POST['action'] == "delete_device") {  
			
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.image, devicetypes.name as devicetype FROM portsdevices LEFT JOIN devicetypes ON portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
            if (!(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 20 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
			} ?>
            
            Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <?php echo $row_getDevice['name']; ?></strong><br />
    <br />
		
     <form action="" method="post" target="_self" name="frm_delete_device" id="frm_delete_device">
      <p>Are you sure you want to delete this device?</p>
      <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
      <input name="id" type="hidden" id="id" value="<?php echo $_GET['device']; ?>" />
      <input name="group" type="hidden" id="group" value="<?php echo $_GET['group']; ?>" />
      <input name="MM_delete" type="hidden" id="MM_delete" value="frm_delete_device" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
    </form>
    
<?php
		}
		
		elseif ($_POST['action'] == "edit_device") {  
			
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.image, devicetypes.name as devicetype FROM portsdevices LEFT JOIN devicetypes ON portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
            if (!(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 10 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 10 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
			} ?>
            
            Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <?php echo $row_getDevice['name']; ?></strong><br />
    <br />
<form action="<?php echo $editFormAction; ?>" method="post" name="form7" id="form7" onSubmit="MM_validateForm('devname','','R','descr','','R', 'managementip','','R');return document.MM_returnValue">
  <table>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Device Name:</td>
      <td><input name="devname" type="text" class="input_standard" id="name" value="<?php echo htmlentities($row_getDevice['name'], ENT_COMPAT, 'utf-8'); ?>" size="32" maxlength="255" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right" valign="top">Description:</td>
      <td><textarea name="descr" cols="32" rows="5" class="input_standard"><?php echo htmlentities($row_getDevice['descr'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right" valign="top">Management IPv4 Address:</td>
      <td><input type="text" name="managementip" size="20" maxlength="38" class="input_standard" value="<?php echo htmlentities($row_getDevice['managementip'], ENT_COMPAT, 'utf-8'); ?>"></td>
    </tr>
    <tr valign="baseline">
        	<td nowrap="nowrap" align="right">Read-Only SNMP Community String (v2c):</td>
            <td><input type="text" size="20" maxlength="35" name="snmpcommunity" class="input_standard" value="<?php echo htmlentities($row_getDevice['snmpcommunity'], ENT_COMPAT, 'utf-8'); ?>" /></td>       
        </tr>
    <tr> </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" class="input_standard" value="Update device" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form7" />
  <input type="hidden" name="id" value="<?php echo $row_getDevice['id']; ?>" />
</form>
<p>&nbsp;</p>
<?php
		}
		elseif ($_GET['timeslotport'] != "") {
		
			if ($_GET['group'] != "" && $_GET['group'] != "0" && !(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
		
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.name as devicetype, devicetypes.image FROM portsdevices LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
		
			mysql_select_db($database_subman, $subman);
			$query_getPort = "SELECT portsports.*, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename FROM portsports LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE portsports.id = ".$_GET['timeslotport']."";
			$getPort = mysql_query($query_getPort, $subman) or die(mysql_error());
			$row_getPort = mysql_fetch_assoc($getPort);
			$totalRows_getPort = mysql_num_rows($getPort);

			mysql_select_db($database_subman, $subman);
			$query_port_timeslots = "SELECT timeslots.*, cards.rack, cards.module, cards.slot, portsports.port as portnumber, portsports.usage, addresses.address, addresses.id as addressid, addresses.network as addressnetwork,  networks.v6mask, customer.name as customername, customer.id as customerID, vpn.name as vpnname, vpn.id as vpnID FROM timeslots LEFT JOIN portsports ON portsports.id = timeslots.portid LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN addresses ON addresses.id = portsports.router LEFT JOIN networks ON networks.id = addresses.network LEFT JOIN customer ON customer.id = portsports.customer LEFT JOIN vpn ON vpn.id = portsports.vpn WHERE timeslots.portid = ".$_GET['timeslotport']." ORDER BY timeslots.timeslot";
			$port_timeslots = mysql_query($query_port_timeslots, $subman) or die(mysql_error());
			$row_port_timeslots = mysql_fetch_assoc($port_timeslots);
			$totalRows_port_timeslots = mysql_num_rows($port_timeslots); ?>
    Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>" title="View Device"><?php echo $row_getDevice['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> Card <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&amp;card=<?php echo $row_getPort['card']; ?>" title="Browse line card"><?php if (!(isset($row_getPort['rack'])) && !(isset($row_getPort['module'])) && !(isset($row_getPort['slot']))) { echo "Virtual"; } else { if (isset($row_getPort['rack'])) { echo $row_getPort['rack']."/"; } if (isset($row_getPort['module'])) { echo $row_getPort['module'].'/'; } if (isset($row_getPort['slot'])) { echo $row_getPort['slot']; } } ?> <?php echo $row_getPort['cardtypename']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> Port <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getPort['port']; ?></strong><br />
    <br />
    <?php if ($totalRows_port_timeslots > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><strong>Rack/Module/Slot/Port<br />
            :Timeslot</strong></td>
          <td><strong>Usage</strong></td>
          <td><strong>Primary IP Address</strong></td>
          <td><strong>Comments</strong></td>
          <td><strong>Customer</strong></td>
        </tr>
        <?php $count = 0;
		do {
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><strong><?php if (!(isset($row_port_timeslots['rack'])) && !(isset($row_port_timeslots['module'])) && !(isset($row_port_timeslots['slot']))) { echo "Virtual"; } else { if (isset($row_port_timeslots['rack'])) { echo $row_port_timeslots['rack']."/"; } if (isset($row_port_timeslots['module'])) { echo $row_port_timeslots['module'].'/'; } if (isset($row_port_timeslots['slot'])) { echo $row_port_timeslots['slot']; } } ?>/<?php echo $row_port_timeslots['portnumber']; ?><font color="#FF0000">:<?php echo $row_port_timeslots['timeslot']; ?></font></td>
          <td><?php echo $row_port_timeslots['usage']; ?></td>
          <td><?php if ($row_port_timeslots['router'] != "") { ?>
            <a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;parent=<?php echo $row_port_timeslots['addressnetwork']; ?>#<?php echo $row_port_timeslots['addressID']; ?>" title="View addressing"><?php if ($row_port_timeslots['v6mask'] == "") { echo long2ip($row_port_timeslots['address']); } else { echo Net_IPv6::compress(long2ipv6($row_port_timeslots['address'])); } ?></a>
            <?php } ?></td>
          <td><?php echo $row_device_ports['comments']; ?></td>
          <td><a href="?browse=customers&amp;container=<?php echo $_GET['container']; ?>&amp;customer=<?php echo $row_port_timeslots['customerID']; ?>" title="Browse this customer"><?php echo $row_port_timeslots['customername']; ?></a></td>
        </tr>
        <?php } while ($row_port_timeslots = mysql_fetch_assoc($port_timeslots)); ?>
      </table>
      <?php } // Show if recordset not empty 
				else { ?>
    <p>There are no timeslots allocated on this port.</p>
    <?php } ?>
    <?php
		}
	
		elseif ($_GET['port'] != "") {
		
			if ($_GET['group'] != "" && $_GET['group'] != "0" && !(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
		
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.name as devicetype, devicetypes.image, devicetypes.vlans FROM portsdevices LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
		
			mysql_select_db($database_subman, $subman);
			$query_getPort = "SELECT portsports.*, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, cards.id as cardid FROM portsports LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE portsports.id = ".$_GET['port']."";
			$getPort = mysql_query($query_getPort, $subman) or die(mysql_error());
			$row_getPort = mysql_fetch_assoc($getPort);
			$totalRows_getPort = mysql_num_rows($getPort);

			mysql_select_db($database_subman, $subman);
			$query_port_subints = "SELECT subint.*, addresses.address, addresses.id as addressid, addresses.network as addressnetwork, networks.v6mask, customer.name as customername, customer.id as customerID, vpn.name as vpnname, vpn.id as vpnID, provider.id as providerID, vlan.name as vlanname, vlan.id as vlanID, vlan.number as vlannumber, vlanpool.id as vlanpoolID, portsdevices.id as deviceID, portsdevices.devicegroup FROM subint LEFT JOIN addresses ON addresses.id = subint.router LEFT JOIN networks ON networks.id = addresses.network LEFT JOIN customer ON customer.id = subint.customer LEFT JOIN vpn ON vpn.id = subint.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN vlan ON vlan.id = subint.vlanid LEFT JOIN vlanpool ON vlanpool.id = vlan.vlanpool LEFT JOIN portsports ON portsports.id = subint.port LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN portsdevices ON portsdevices.id = cards.device WHERE subint.port = ".$_GET['port']." ORDER BY subint.subint";
			$port_subints = mysql_query($query_port_subints, $subman) or die(mysql_error());
			$row_port_subints = mysql_fetch_assoc($port_subints);
			$totalRows_port_subints = mysql_num_rows($port_subints); ?>
    Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>" title="View Device"><?php echo $row_getDevice['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> Line Card <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $_GET['device']; ?>&amp;card=<?php echo $row_getPort['cardid']; ?>" title="Browse device line card"><?php if (!(isset($row_getPort['rack'])) && !(isset($row_getPort['module'])) && !(isset($row_getPort['slot']))) { echo "Virtual"; } else { if (isset($row_getPort['rack'])) { echo $row_getPort['rack']."/"; } if (isset($row_getPort['module'])) { echo $row_getPort['module'].'/'; } if (isset($row_getPort['slot'])) { echo $row_getPort['slot']; } } ?> <?php echo $row_getPort['cardtypename']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> Port <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getPort['port']; ?></strong><br />
    <br />
    <?php if ($totalRows_port_subints > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><strong>Rack/Module/Slot/Port<br>
            .Subinterface</strong></td>
          <td><strong>Usage</strong></td>
          <td><strong>Primary IP Address</strong></td>
          <?php if ($row_getDevice['vlans'] == 1) { ?>
          <td><strong>VLAN</strong></td>
          <?php } ?>
          <td><strong>Comments</strong></td>
          <td><strong>VPN</strong></td>
          <td><strong>Customer</strong></td>
        </tr>
        <?php $count = 0;
		do { 
					
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><strong><?php if (!(isset($row_getPort['rack'])) && !(isset($row_getPort['module'])) && !(isset($row_getPort['slot']))) { echo "Virtual"; } else { if (isset($row_getPort['rack'])) { echo $row_getPort['rack']."/"; } if (isset($row_getPort['module'])) { echo $row_getPort['module'].'/'; } if (isset($row_getPort['slot'])) { echo $row_getPort['slot']; } } ?>/<?php echo $row_getPort['port']; ?>.<font color="#FF0000"><?php echo $row_port_subints['subint']; ?></font></strong> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&port=<?php echo $_GET['port']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a></td>
          <td><?php echo $row_port_subints['usage']; ?></td>
          <td><?php if ($row_port_subints['router'] != "" && $row_port_subints['router'] != 0) { ?>
            <a href="?browse=networks&container=<?php echo $_GET['container']; ?>&parent=<?php echo $row_port_subints['addressnetwork']; ?>#<?php echo $row_port_subints['addressID']; ?>" title="View addressing"><?php if ($row_port_subints['v6mask'] == "") { echo long2ip($row_port_subints['address']); } else { Net_IPv6::compress(long2ipv6($row_port_subints['address'])); } ?></a>
            <?php } ?>
            <?php if (($row_port_subints['router'] == "" || $row_port_subints['router'] == 0) && $_SESSION['_browseport_address'] != "" && $_SESSION['_browseport_container'] == $_GET['container']) { 
			  		
					mysql_select_db($database_subman, $subman);
					$query_address = "SELECT addresses.*, networks.v6mask FROM addresses LEFT JOIN networks ON networks.id = addresses.network WHERE addresses.id = ".$_SESSION['_browseport_address']."";
					$address = mysql_query($query_address, $subman) or die(mysql_error());
					$row_address = mysql_fetch_assoc($address);
					$totalRows_address = mysql_num_rows($address);
					?>
              		<input type="button" class="input_standard" onClick="MM_goToURL('parent','handler.php?action=browseport_setaddress&amp;subint=<?php echo $row_port_subints['id']; ?>&amp;container=<?php echo $_GET['container']; ?>');return document.MM_returnValue" value="Set as <?php if ($row_address['v6mask'] == "") { echo long2ip($row_address['address']); } else { echo Net_IPv6::compress(long2ipv6($row_address['address'])); } ?>" /> <input type="button" value="Cancel" class="input_standard" onClick="MM_goToURL('parent','handler.php?action=browseport_cancel&amp;container=<?php echo $_GET['container']; ?>');return document.MM_returnValue" />
              <?php } ?>
          </td>
          <?php if ($row_getDevice['vlans'] == 1) { ?>
          <td><?php if ($row_port_subints['vlanID'] != "") { ?><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_port_subints['devicegroup']; ?>&device=<?php echo $row_port_subints['deviceID']; ?>&vlanpool=<?php echo $row_port_subints['vlanpoolID']; ?>&vlan=<?php echo $row_port_subints['vlanID']; ?>" title="Browse this VLAN">[<?php echo $row_port_subints['vlannumber']; ?>] <?php echo $row_port_subints['vlanname']; ?></a><?php } ?></td>
          <?php } ?>
          <td><?php echo $row_port_subints['comments']; ?></td>
          <td><?php if ($row_port_subints['vpnID'] != "") { ?><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_port_subints['providerID']; ?>&vpn=<?php echo $row_port_subints['vpnID']; ?>" title="Browse this VPN"><?php echo $row_port_subints['vpnname']; ?></a><?php } ?></td>
          <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_port_subints['customerID']; ?>" title="Browse this customer"><?php echo $row_port_subints['customername']; ?></a></td>
        </tr>
        <?php } while ($row_port_subints = mysql_fetch_assoc($port_subints)); ?>
      </table>
      <?php } // Show if recordset not empty 
			else { ?>
    <p>There are no sub-interfaces on this port.</p>
    <?php
		}
		}
		elseif ($_GET['device'] != "" && $_GET['vlan'] != "") {
			
			if ($_GET['group'] != "" && $_GET['group'] != "0" && !(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_vlan_ports = "SELECT portsports.*, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, customer.name as customername, customer.id as customerID, addresses.address, addresses.network as addressnetwork, addresses.id as addressID, networks.v6mask, vpn.name as vpnname, vpn.id as vpnID, provider.id as providerID FROM portsports LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN customer ON customer.id = portsports.customer LEFT JOIN addresses ON addresses.id = portsports.router LEFT JOIN networks ON networks.id = addresses.network LEFT JOIN vpn ON vpn.id = portsports.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider WHERE portsports.vlanid = ".$_GET['vlan']." AND cards.device = ".$_GET['device']." ORDER BY cards.rack, cards.module, cards.slot, portsports.port";
			$vlan_ports = mysql_query($query_vlan_ports, $subman) or die(mysql_error());
			$row_vlan_ports = mysql_fetch_assoc($vlan_ports);
			$totalRows_vlan_ports = mysql_num_rows($vlan_ports);

			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getVlanPool = "SELECT * FROM vlanpool WHERE vlanpool.id = ".$_GET['vlanpool']."";
			$getVlanPool = mysql_query($query_getVlanPool, $subman) or die(mysql_error());
			$row_getVlanPool = mysql_fetch_assoc($getVlanPool);
			$totalRows_getVlanPool = mysql_num_rows($getVlanPool);
			
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.name as devicetype, devicetypes.image FROM portsdevices LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_subman, $subman);
			$query_getVlan = "SELECT * FROM vlan WHERE vlan.id = ".$_GET['vlan']."";
			$getVlan = mysql_query($query_getVlan, $subman) or die(mysql_error());
			$row_getVlan = mysql_fetch_assoc($getVlan);
			$totalRows_getVlan = mysql_num_rows($getVlan);
			
			mysql_select_db($database_subman, $subman);
			$query_port_subints = "SELECT subint.*, portsports.port, portsports.id as portid, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, addresses.address, addresses.id as addressid, addresses.network as addressnetwork, networks.v6mask, customer.name as customername, customer.id as customerID, vpn.name as vpnname, vpn.id as vpnID, provider.id as providerID, vlan.name as vlanname, vlan.id as vlanID, vlan.number as vlannumber FROM subint LEFT JOIN portsports ON portsports.id = subint.port LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN addresses ON addresses.id = subint.router LEFT JOIN networks ON networks.id = addresses.network LEFT JOIN customer ON customer.id = subint.customer LEFT JOIN vpn ON vpn.id = subint.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN vlan ON vlan.id = subint.vlanid WHERE subint.vlanid = ".$_GET['vlan']." AND cards.device = ".$_GET['device']." ORDER BY subint.subint";
			$port_subints = mysql_query($query_port_subints, $subman) or die(mysql_error());
			$row_port_subints = mysql_fetch_assoc($port_subints);
			$totalRows_port_subints = mysql_num_rows($port_subints);
			
		?>
    Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>" title="View Device"><?php echo $row_getDevice['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VLAN Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&vlanpool=<?php echo $_GET['vlanpool']; ?>" title="View VLAN pool"><?php echo $row_getVlanPool['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VLAN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>[<?php echo $row_getVlan['number']; ?>] <?php echo $row_getVlan['name']; ?></strong><br />
    <br />
    <?php if ($totalRows_vlan_ports > 0 || $totalRows_port_subints > 0) { ?>
    <table width="100%" border="0">
      <tr>
        <td><strong>Rack/Module/Slot/Port<br />
          .Subinterface </strong></td>
        <td><strong>Port Type</strong></td>
        <td><strong>Usage</strong></td>
        <td><strong>Primary IP Address</strong></td>
        <td><strong>Comments</strong></td>
        <td><strong>VPN</strong></td>
        <td><strong>Customer</strong></td>
      </tr>
    <?php if ($totalRows_vlan_ports > 0) { // Show if recordset not empty ?>
        <?php $count = 0;
		do { 
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><strong><?php if (!(isset($row_vlan_ports['rack'])) && !(isset($row_vlan_ports['module'])) && !(isset($row_vlan_ports['slot']))) { echo "Virtual"; } else { if (isset($row_vlan_ports['rack'])) { echo $row_vlan_ports['rack']."/"; } if (isset($row_vlan_ports['module'])) { echo $row_vlan_ports['module'].'/'; } if (isset($row_vlan_ports['slot'])) { echo $row_vlan_ports['slot']; } } ?>/<?php echo $row_vlan_ports['port']; ?></strong> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&port=<?php echo $row_vlan_ports['id']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a></td>
          <td><?php echo $row_vlan_ports['cardtypename']; ?></td>
          <td><?php echo $row_vlan_ports['usage']; ?></td>
          <td><?php if ($row_vlan_ports['router'] != "" && $row_vlan_ports['router'] != 0) { ?>
            <a href="?browse=networks&container=<?php echo $_GET['container']; ?>&parent=<?php echo $row_vlan_ports['addressnetwork']; ?>#<?php echo $row_vlan_ports['addressID']; ?>" title="View addressing"><?php if ($row_vlan_ports['v6mask'] == "") { echo long2ip($row_vlan_ports['address']); } else { echo Net_IPv6::compress(long2ipv6($row_vlan_ports['address'])); } ?></a>
            <?php } ?></td>
          <td><?php echo $row_vlan_ports['comments']; ?></td>
          <td><?php if ($row_vlan_ports['vpnID'] != "") { ?><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_vlan_ports['providerID']; ?>&vpn=<?php echo $row_vlan_ports['vpnID']; ?>" title="Browse this VPN"><?php echo $row_vlan_ports['vpnname']; ?></a><?php } ?></td>
          <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_vlan_ports['customerID']; ?>" title="Browse this customer"><?php echo $row_vlan_ports['customername']; ?></a></td>
        </tr>
        <?php } while ($row_vlan_ports = mysql_fetch_assoc($vlan_ports)); ?>
      <?php } // If recordset is not empty ?>
      <?php if ($totalRows_port_subints > 0) { // Show if recordset not empty ?>
        <?php $count = 0;
		do { 
					
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><strong><?php if (!(isset($row_port_subints['rack'])) && !(isset($row_port_subints['module'])) && !(isset($row_port_subints['slot']))) { echo "Virtual"; } else { if (isset($row_port_subints['rack'])) { echo $row_port_subints['rack']."/"; } if (isset($row_port_subints['module'])) { echo $row_port_subints['module'].'/'; } if (isset($row_port_subints['slot'])) { echo $row_port_subints['slot']; } } ?>/<?php echo $row_port_subints['port']; ?>.<font color="#FF0000"><?php echo $row_port_subints['subint']; ?></font></strong> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&port=<?php echo $row_port_subints['portid']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a></td>
          <td><?php echo $row_port_subints['cardtypename']; ?></td>
          <td><?php echo $row_port_subints['usage']; ?></td>
          <td><?php if ($row_port_subints['router'] != "" && $row_port_subints['router'] != 0) { ?>
            <a href="?browse=networks&container=<?php echo $_GET['container']; ?>&parent=<?php echo $row_port_subints['addressnetwork']; ?>#<?php echo $row_port_subints['addressID']; ?>" title="View addressing"><?php if ($row_vlan_ports['v6mask'] == "") { echo long2ip($row_port_subints['address']); } else { echo Net_IPv6::compress(long2ipv6($row_port_subints['address'])); } ?></a>
            <?php } ?></td>
          <td><?php echo $row_port_subints['comments']; ?></td>
          <td><?php if ($row_port_subints['vpnID'] != "") { ?><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_port_subints['providerID']; ?>&vpn=<?php echo $row_port_subints['vpnID']; ?>" title="Browse this VPN"><?php echo $row_port_subints['vpnname']; ?></a><?php } ?></td>
          <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_port_subints['customerID']; ?>" title="Browse this customer"><?php echo $row_port_subints['customername']; ?></a></td>
        </tr>
        <?php } while ($row_port_subints = mysql_fetch_assoc($port_subints)); ?>
        <?php } // Show if recordset not empty ?>
    </table>
    <?php
	}
  		else { ?>
    <p>There are no ports on this device assigned to this VLAN.</p>
    <?php
		}
		}
		elseif ($_GET['device'] != "" && $_GET['vlanpool'] != "") {
			
			if ($_GET['group'] != "" && $_GET['group'] != "0" && !(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_vlanpool_vlans = "SELECT vlan.*, customer.name as customername, customer.id as customerID FROM vlan LEFT JOIN customer ON customer.id = vlan.customer WHERE vlan.vlanpool = ".$_GET['vlanpool']." ORDER BY vlan.number";
			$vlanpool_vlans = mysql_query($query_vlanpool_vlans, $subman) or die(mysql_error());
			$row_vlanpool_vlans = mysql_fetch_assoc($vlanpool_vlans);
			$totalRows_vlanpool_vlans = mysql_num_rows($vlanpool_vlans);
            
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getVlanPool = "SELECT * FROM vlanpool WHERE vlanpool.id = ".$_GET['vlanpool']."";
			$getVlanPool = mysql_query($query_getVlanPool, $subman) or die(mysql_error());
			$row_getVlanPool = mysql_fetch_assoc($getVlanPool);
			$totalRows_getVlanPool = mysql_num_rows($getVlanPool);
			
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.name as devicetype, devicetypes.image FROM portsdevices LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice); ?>
    Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>" title="View Device"><?php echo $row_getDevice['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VLAN Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVlanPool['name']; ?></strong><br />
    <br />
    <?php if ($totalRows_vlanpool_vlans > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><strong>VLAN ID</strong></td>
          <td><strong>VLAN Name</strong></td>
          <td><strong>Customer</strong></td>
        </tr>
        <?php do { 
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><?php echo $row_vlanpool_vlans['number']; ?></td>
          <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&vlanpool=<?php echo $_GET['vlanpool']; ?>&vlan=<?php echo $row_vlanpool_vlans['id']; ?>" title="View VLAN ports"><?php echo $row_vlanpool_vlans['name']; ?></a></td>
          <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_vlanpool_vlans['customerID']; ?>" title="Browse this customer"><?php echo $row_vlanpool_vlans['customername']; ?></a></td>
        </tr>
        <?php } while ($row_vlanpool_vlans = mysql_fetch_assoc($vlanpool_vlans)); ?>
      </table>
      <?php } // Show if recordset not empty 
  		else { ?>
    <p>There are no VLANs in this VLAN pool.</p>
    <?php			
		}?>
    <?php	
		}
		
		elseif ($_GET['device'] != "" && $_GET['card'] != "") {
			
			if ($_GET['group'] != "" && $_GET['group'] != "0" && !(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getCard = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.id = ".$_GET['card']."";
			$getCard = mysql_query($query_getCard, $subman) or die(mysql_error());
			$row_getCard = mysql_fetch_assoc($getCard);
			$totalRows_getCard = mysql_num_rows($getCard);
			
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.name as devicetype, devicetypes.image, devicetypes.vlans FROM portsdevices LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_subman, $subman);
			$query_device_ports = "SELECT portsports.*, cards.rack, cards.module, cards.slot, cards.timeslots, cardtypes.name as cardtypename, customer.name as customername, customer.id as customerID, addresses.address, addresses.network as addressnetwork, addresses.id as addressID, networks.v6mask, vpn.name as vpnname, vpn.id as vpnID, provider.id as providerID, vlanpool.id as vlanpoolID, vlan.name as vlanname, vlan.id as vlanID, vlan.number as vlannumber, portsdevices.id as deviceID, portsdevices.devicegroup FROM portsports LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN customer ON customer.id = portsports.customer LEFT JOIN addresses ON addresses.id = portsports.router LEFT JOIN networks ON networks.id = addresses.network LEFT JOIN vpn ON vpn.id = portsports.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN vlan ON vlan.id = portsports.vlanid LEFT JOIN vlanpool ON vlanpool.id = vlan.vlanpool LEFT JOIN portsdevices ON portsdevices.id = cards.device WHERE cards.device = ".$_GET['device']." AND cards.id = ".$_GET['card']." ORDER BY cards.rack, cards.module, cards.slot, portsports.port";
			$device_ports = mysql_query($query_device_ports, $subman) or die(mysql_error());
			$row_device_ports = mysql_fetch_assoc($device_ports);
			$totalRows_device_ports = mysql_num_rows($device_ports); 
			
			mysql_select_db($database_subman, $subman);
			$query_portCount = "SELECT * FROM portsports WHERE portsports.card = '".$_GET['card']."'";
			$portCount = mysql_query($query_portCount, $subman) or die(mysql_error());
			$row_portCount = mysql_fetch_assoc($portCount);
			$totalRows_portCount = mysql_num_rows($portCount);
			?>
            

Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $_GET['device']; ?>" title="Browse device"><?php echo $row_getDevice['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php if (!(isset($row_getCard['rack'])) && !(isset($row_getCard['module'])) && !(isset($row_getCard['slot']))) { echo "Virtual"; } else { if (isset($row_getCard['rack'])) { echo $row_getCard['rack']."/"; } if (isset($row_getCard['module'])) { echo $row_getCard['module'].'/'; } if (isset($row_getCard['slot'])) { echo $row_getCard['slot']; } } ?> <?php echo $row_getCard['cardtypename']; ?></strong><br />
    <br />
	
    <p><?php echo (($row_getCard['endport'] - $row_getCard['startport']) + 1); ?> port(s) total, <?php echo $totalRows_portCount; ?> in use, <?php echo ((($row_getCard['endport'] - $row_getCard['startport']) + 1) - $totalRows_portCount); ?> available</p>
    <?php if ($totalRows_device_ports > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><strong>Rack/Module/Slot/Port</strong></td>
          <td><strong>Usage</strong></td>
          <td><strong>Primary IP Address</strong></td>
          <?php if ($row_getDevice['vlans'] == 1) { ?>
          <td><strong>VLAN</strong></td>
          <?php } ?>
          <td><strong>Comments</strong></td>
          <td><strong>VPN</strong></td>
          <td><strong>Customer</strong></td>
        </tr>
        <?php $count = 0;
		do {

			mysql_select_db($database_subman, $subman);
			$query_subints = "SELECT subint.* FROM subint WHERE subint.port = ".$row_device_ports['id']."";
			$subints = mysql_query($query_subints, $subman) or die(mysql_error());
			$row_subints = mysql_fetch_assoc($subints);
			$totalRows_subints = mysql_num_rows($subints);
			
			mysql_select_db($database_subman, $subman);
			$query_timeslots = "SELECT timeslots.* FROM timeslots WHERE timeslots.portid = ".$row_device_ports['id']."";
			$timeslots = mysql_query($query_timeslots, $subman) or die(mysql_error());
			$row_timeslots = mysql_fetch_assoc($timeslots);
			$totalRows_timeslots = mysql_num_rows($timeslots);
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><strong>
            <?php if ($row_device_ports['timeslots'] <= 0) { ?>
            <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&port=<?php echo $row_device_ports['id']; ?>" title="View port sub-interfaces"<?php if ($totalRows_subints > 0) { ?> style="color:#F00"<?php } ?>><?php if (!(isset($row_device_ports['rack'])) && !(isset($row_device_ports['module'])) && !(isset($row_device_ports['slot']))) { echo "Virtual"; } else { if (isset($row_device_ports['rack'])) { echo $row_device_ports['rack']."/"; } if (isset($row_device_ports['module'])) { echo $row_device_ports['module'].'/'; } if (isset($row_device_ports['slot'])) { echo $row_device_ports['slot']; } } ?>/<?php echo $row_device_ports['port']; ?></a>
            <?php } else { ?>
            <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&timeslotport=<?php echo $row_device_ports['id']; ?>" title="View port timeslots"<?php if ($totalRows_timeslots > 0) { ?> style="color:#F00"<?php } ?>><?php if (!(isset($row_device_ports['rack'])) && !(isset($row_device_ports['module'])) && !(isset($row_device_ports['slot']))) { echo "Virtual"; } else { if (isset($row_device_ports['rack'])) { echo $row_device_ports['rack']."/"; } if (isset($row_device_ports['module'])) { echo $row_device_ports['module'].'/'; } if (isset($row_device_ports['slot'])) { echo $row_device_ports['slot']; } } ?>/<?php echo $row_device_ports['port']; ?></a>
            <?php } ?>
             <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&port=<?php echo $row_device_ports['id']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a>
          </strong></td>
          <td><?php echo $row_device_ports['usage']; ?></td>
          <td><?php if ($totalRows_subints > 0) { } else { ?>
            <?php if ($row_device_ports['router'] != "" && $row_device_ports['router'] != 0) { ?>
            <a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;parent=<?php echo $row_device_ports['addressnetwork']; ?>#<?php echo $row_device_ports['addressID']; ?>" title="View addressing"><?php if ($row_device_ports['v6mask'] == "") { echo long2ip($row_device_ports['address']); } else { echo Net_IPv6::compress(long2ipv6($row_device_ports['address'])); } ?>
              <?php } ?>
              <?php if (($row_device_ports['router'] == "" || $row_device_ports['router'] == 0) && $_SESSION['_browseport_address'] != "" && $_SESSION['_browseport_container'] == $_GET['container']) { 
			  		
					mysql_select_db($database_subman, $subman);
					$query_address = "SELECT addresses.*, networks.v6mask FROM addresses LEFT JOIN networks ON networks.id = addresses.network WHERE addresses.id = ".$_SESSION['_browseport_address']."";
					$address = mysql_query($query_address, $subman) or die(mysql_error());
					$row_address = mysql_fetch_assoc($address);
					$totalRows_address = mysql_num_rows($address);
					?>
              		<input type="button" class="input_standard" onClick="MM_goToURL('parent','handler.php?action=browseport_setaddress&amp;portid=<?php echo $row_device_ports['id']; ?>&amp;container=<?php echo $_GET['container']; ?>');return document.MM_returnValue" value="Set as <?php if ($row_address['v6mask'] == "") { echo long2ip($row_address['address']); } else { echo Net_IPv6::compress(long2ipv6($row_address['address'])); } ?>" /> <input type="button" value="Cancel" class="input_standard" onClick="MM_goToURL('parent','handler.php?action=browseport_cancel&amp;container=<?php echo $_GET['container']; ?>');return document.MM_returnValue" />
              <?php } ?>
            </a>
            <?php } ?></td>
          <?php if ($row_getDevice['vlans'] == 1) { ?>
          <td><?php if ($row_device_ports['vlanID'] != "") { ?><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_device_ports['devicegroup']; ?>&device=<?php echo $row_device_ports['deviceID']; ?>&vlanpool=<?php echo $row_device_ports['vlanpoolID']; ?>&vlan=<?php echo $row_device_ports['vlanID']; ?>" title="Browse this VLAN">[<?php echo $row_device_ports['vlannumber']; ?>] <?php echo $row_device_ports['vlanname']; ?></a><?php } ?></td>
          <?php } ?>
          <td><?php echo $row_device_ports['comments']; ?></td>
          <td><?php if ($row_device_ports['vpnID'] != "" && $row_device_ports['vpnID'] != 0) { ?><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_device_ports['providerID']; ?>&vpn=<?php echo $row_device_ports['vpnID']; ?>" title="Browse VPN"><?php echo $row_device_ports['vpnname']; ?></a><?php } ?></td>
          <td><?php if ($totalRows_subints > 0 || $totalRows_timeslots > 0) { } else { ?>
            <a href="?browse=customers&amp;container=<?php echo $_GET['container']; ?>&amp;customer=<?php echo $row_device_ports['customerID']; ?>" title="Browse this customer"><?php echo $row_device_ports['customername']; ?></a>
            <?php } ?></td>
        </tr>
        <?php } while ($row_device_ports = mysql_fetch_assoc($device_ports)); ?>
      </table>
      <?php } // Show if recordset not empty 
				else { ?>
    <p>There are no ports allocated on this line card.</p>
    <?php } ?>            
            
		<?php	
		}
		
		
		elseif ($_GET['vlan'] != "") {
			
			if ($_GET['group'] != "" && $_GET['group'] != "0" && !(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_vlan_ports = "SELECT portsports.*, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, customer.name as customername, customer.id as customerID, addresses.address, addresses.network as addressnetwork, addresses.id as addressID, networks.v6mask, vpn.name as vpnname, vpn.id as vpnID, provider.id as providerID, portsdevices.name as devicename, portsdevices.id as deviceid FROM portsports LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN customer ON customer.id = portsports.customer LEFT JOIN addresses ON addresses.id = portsports.router LEFT JOIN networks ON networks.id = addresses.network LEFT JOIN vpn ON vpn.id = portsports.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN portsdevices ON portsdevices.id = cards.device WHERE portsports.vlanid = ".$_GET['vlan']." ORDER BY cards.rack, cards.module, cards.slot, portsports.port";
			$vlan_ports = mysql_query($query_vlan_ports, $subman) or die(mysql_error());
			$row_vlan_ports = mysql_fetch_assoc($vlan_ports);
			$totalRows_vlan_ports = mysql_num_rows($vlan_ports);

			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getVlanPool = "SELECT * FROM vlanpool WHERE vlanpool.id = ".$_GET['vlanpool']."";
			$getVlanPool = mysql_query($query_getVlanPool, $subman) or die(mysql_error());
			$row_getVlanPool = mysql_fetch_assoc($getVlanPool);
			$totalRows_getVlanPool = mysql_num_rows($getVlanPool);
			
			mysql_select_db($database_subman, $subman);
			$query_getVlan = "SELECT * FROM vlan WHERE vlan.id = ".$_GET['vlan']."";
			$getVlan = mysql_query($query_getVlan, $subman) or die(mysql_error());
			$row_getVlan = mysql_fetch_assoc($getVlan);
			$totalRows_getVlan = mysql_num_rows($getVlan);
			
			mysql_select_db($database_subman, $subman);
			$query_port_subints = "SELECT subint.*, portsports.port, portsports.id as portid, cards.rack, cards.module, cards.slot, cardtypes.name as cardtypename, addresses.address, addresses.id as addressid, addresses.network as addressnetwork, networks.v6mask, customer.name as customername, customer.id as customerID, vpn.name as vpnname, vpn.id as vpnID, provider.id as providerID, vlan.name as vlanname, vlan.id as vlanID, vlan.number as vlannumber, portsdevices.name as devicename, portsdevices.id as deviceid FROM subint LEFT JOIN portsports ON portsports.id = subint.port LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN addresses ON addresses.id = subint.router LEFT JOIN networks ON networks.id = addresses.network LEFT JOIN customer ON customer.id = subint.customer LEFT JOIN vpn ON vpn.id = subint.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN vlan ON vlan.id = subint.vlanid LEFT JOIN portsdevices ON portsdevices.id = cards.device WHERE subint.vlanid = ".$_GET['vlan']." ORDER BY subint.subint";
			$port_subints = mysql_query($query_port_subints, $subman) or die(mysql_error());
			$row_port_subints = mysql_fetch_assoc($port_subints);
			$totalRows_port_subints = mysql_num_rows($port_subints);
			
		?>
    Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> Shared VLAN Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&vlanpool=<?php echo $_GET['vlanpool']; ?>" title="View VLAN pool"><?php echo $row_getVlanPool['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> Shared VLAN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong>[<?php echo $row_getVlan['number']; ?>] <?php echo $row_getVlan['name']; ?></strong><br />
    <br />
    <?php if ($totalRows_vlan_ports > 0 || $totalRows_port_subints > 0) { ?>
    <table width="100%" border="0">
      <tr>
      <td><strong>Device</strong></td>
        <td><strong>Rack/Module/Slot/Port<br />
          .Subinterface </strong></td>
        <td><strong>Port Type</strong></td>
        <td><strong>Usage</strong></td>
        <td><strong>Primary IP Address</strong></td>
        <td><strong>Comments</strong></td>
        <td><strong>VPN</strong></td>
        <td><strong>Customer</strong></td>
      </tr>
    <?php if ($totalRows_vlan_ports > 0) { // Show if recordset not empty ?>
        <?php $count = 0;
		do { 
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
        <td><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $row_vlan_ports['deviceid']; ?>" title="Browse this device"><?php echo $row_vlan_ports['devicename']; ?></a></td>
          <td><strong><?php if (!(isset($row_vlan_ports['rack'])) && !(isset($row_vlan_ports['module'])) && !(isset($row_vlan_ports['slot']))) { echo "Virtual"; } else { if (isset($row_vlan_ports['rack'])) { echo $row_vlan_ports['rack']."/"; } if (isset($row_vlan_ports['module'])) { echo $row_vlan_ports['module'].'/'; } if (isset($row_vlan_ports['slot'])) { echo $row_vlan_ports['slot']; } } ?>/<?php echo $row_vlan_ports['port']; ?></strong> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $row_vlan_ports['deviceid']; ?>&port=<?php echo $row_vlan_ports['id']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a></td>
          <td><?php echo $row_vlan_ports['cardtypename']; ?></td>
          <td><?php echo $row_vlan_ports['usage']; ?></td>
          <td><?php if ($row_vlan_ports['router'] != "" && $row_vlan_ports['router'] != 0) { ?>
            <a href="?browse=networks&container=<?php echo $_GET['container']; ?>&parent=<?php echo $row_vlan_ports['addressnetwork']; ?>#<?php echo $row_vlan_ports['addressID']; ?>" title="View addressing"><?php if ($row_vlan_ports['v6mask'] == "") { echo long2ip($row_vlan_ports['address']); } else { echo Net_IPv6::compress(long2ipv6($row_vlan_ports['address'])); } ?></a>
            <?php } ?></td>
          <td><?php echo $row_vlan_ports['comments']; ?></td>
          <td><?php if ($row_vlan_ports['vpnID'] != "") { ?><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_vlan_ports['providerID']; ?>&vpn=<?php echo $row_vlan_ports['vpnID']; ?>" title="Browse this VPN"><?php echo $row_vlan_ports['vpnname']; ?></a><?php } ?></td>
          <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_vlan_ports['customerID']; ?>" title="Browse this customer"><?php echo $row_vlan_ports['customername']; ?></a></td>
        </tr>
        <?php } while ($row_vlan_ports = mysql_fetch_assoc($vlan_ports)); ?>
      <?php } // If recordset is not empty ?>
      <?php if ($totalRows_port_subints > 0) { // Show if recordset not empty ?>
        <?php $count = 0;
		do { 
					
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
        	<td><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $row_port_subints['deviceid']; ?>" title="Browse this device"><?php echo $row_port_subints['devicename']; ?></a></td>
          <td><strong><?php if (!(isset($row_port_subints['rack'])) && !(isset($row_port_subints['module'])) && !(isset($row_port_subints['slot']))) { echo "Virtual"; } else { if (isset($row_port_subints['rack'])) { echo $row_port_subints['rack']."/"; } if (isset($row_port_subints['module'])) { echo $row_port_subints['module'].'/'; } if (isset($row_port_subints['slot'])) { echo $row_port_subints['slot']; } } ?>/<?php echo $row_port_subints['port']; ?>.<font color="#FF0000"><?php echo $row_port_subints['subint']; ?></font></strong> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $row_port_subints['deviceid']; ?>&port=<?php echo $row_port_subints['portid']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a></td>
          <td><?php echo $row_port_subints['cardtypename']; ?></td>
          <td><?php echo $row_port_subints['usage']; ?></td>
          <td><?php if ($row_port_subints['router'] != "" && $row_port_subints['router'] != 0) { ?>
            <a href="?browse=networks&container=<?php echo $_GET['container']; ?>&parent=<?php echo $row_port_subints['addressnetwork']; ?>#<?php echo $row_port_subints['addressID']; ?>" title="View addressing"><?php if ($row_port_subints['v6mask'] == "") { echo long2ip($row_port_subints['address']); } else { echo Net_IPv6::compress(long2ipv6($row_port_subints['address'])); } ?></a>
            <?php } ?></td>
          <td><?php echo $row_port_subints['comments']; ?></td>
          <td><?php if ($row_port_subints['vpnID'] != "") { ?><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_port_subints['providerID']; ?>&vpn=<?php echo $row_port_subints['vpnID']; ?>" title="Browse this VPN"><?php echo $row_port_subints['vpnname']; ?></a><?php } ?></td>
          <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_port_subints['customerID']; ?>" title="Browse this customer"><?php echo $row_port_subints['customername']; ?></a></td>
        </tr>
        <?php } while ($row_port_subints = mysql_fetch_assoc($port_subints)); ?>
        <?php } // Show if recordset not empty ?>
    </table>
    <?php
	}
  		else { ?>
    <p>There are no ports assigned to this shared VLAN.</p>
    <?php
		}
		}
		
		elseif ($_GET['vlanpool'] != "") {
			
			if ($_GET['group'] != "" && $_GET['group'] != "0" && !(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
				
			mysql_select_db($database_subman, $subman);
			$query_vlanpool_vlans = "SELECT vlan.*, customer.name as customername, customer.id as customerID FROM vlan LEFT JOIN customer ON customer.id = vlan.customer WHERE vlan.vlanpool = ".$_GET['vlanpool']." ORDER BY vlan.number";
			$vlanpool_vlans = mysql_query($query_vlanpool_vlans, $subman) or die(mysql_error());
			$row_vlanpool_vlans = mysql_fetch_assoc($vlanpool_vlans);
			$totalRows_vlanpool_vlans = mysql_num_rows($vlanpool_vlans);
            
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getVlanPool = "SELECT * FROM vlanpool WHERE vlanpool.id = ".$_GET['vlanpool']."";
			$getVlanPool = mysql_query($query_getVlanPool, $subman) or die(mysql_error());
			$row_getVlanPool = mysql_fetch_assoc($getVlanPool);
			$totalRows_getVlanPool = mysql_num_rows($getVlanPool); ?>
            
    Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> Shared VLAN Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVlanPool['name']; ?></strong><br />
    <br />
    <?php if ($totalRows_vlanpool_vlans > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><strong>VLAN ID</strong></td>
          <td><strong>VLAN Name</strong></td>
          <td><strong>Customer</strong></td>
        </tr>
        <?php do { 
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><?php echo $row_vlanpool_vlans['number']; ?></td>
          <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&vlanpool=<?php echo $_GET['vlanpool']; ?>&vlan=<?php echo $row_vlanpool_vlans['id']; ?>" title="View VLAN ports"><?php echo $row_vlanpool_vlans['name']; ?></a></td>
          <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_vlanpool_vlans['customerID']; ?>" title="Browse this customer"><?php echo $row_vlanpool_vlans['customername']; ?></a></td>
        </tr>
        <?php } while ($row_vlanpool_vlans = mysql_fetch_assoc($vlanpool_vlans)); ?>
      </table>
      <?php } // Show if recordset not empty 
  		else { ?>
    <p>There are no VLANs in this shared VLAN pool.</p>
    <?php			
		}?>
    <?php	
		}
		
		elseif ($_GET['device'] != "") { 
		
			if ($_GET['group'] != "" && $_GET['group'] != "0" && !(getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 0 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 0 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}
		
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup);
			
			mysql_select_db($database_subman, $subman);
			$query_getDevice = "SELECT portsdevices.*, devicetypes.name as devicetype, devicetypes.image, devicetypes.vlans FROM portsdevices LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype WHERE portsdevices.id = ".$_GET['device']."";
			$getDevice = mysql_query($query_getDevice, $subman) or die(mysql_error());
			$row_getDevice = mysql_fetch_assoc($getDevice);
			$totalRows_getDevice = mysql_num_rows($getDevice);
			
			mysql_select_db($database_subman, $subman);
			$query_device_vlan_pools = "SELECT vlanpool.*, portsdevices.name as devicename FROM vlanpool LEFT JOIN portsdevices ON portsdevices.id = vlanpool.device WHERE vlanpool.device = ".$_GET['device']." OR vlanpool.devicegroup = ".$_GET['group']." ORDER BY vlanpool.devicegroup, vlanpool.name";
			$device_vlan_pools = mysql_query($query_device_vlan_pools, $subman) or die(mysql_error());
			$row_device_vlan_pools = mysql_fetch_assoc($device_vlan_pools);
			$totalRows_device_vlan_pools = mysql_num_rows($device_vlan_pools);

			mysql_select_db($database_subman, $subman);
			$query_device_cards = "SELECT cards.*, cardtypes.name as cardtypename FROM cards LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE cards.device = ".$_GET['device']." ORDER BY cards.rack, cards.`module`, cards.slot";
			$device_cards = mysql_query($query_device_cards, $subman) or die(mysql_error());
			$row_device_cards = mysql_fetch_assoc($device_cards);
			$totalRows_device_cards = mysql_num_rows($device_cards);
			
			mysql_select_db($database_subman, $subman);
			$query_device_customers = "select count(portsports.id) as physicalcount, count(subint.id) as logicalcount, customer.name as customername, customer.id as customerid, portsdevices.name as devicename from portsdevices left join cards on cards.device = portsdevices.id left join cardtypes on cardtypes.id = cards.cardtype inner join portsports on portsports.card = cards.id left join subint on subint.port = portsports.id left join customer on customer.id = portsports.customer or customer.id = subint.customer where portsdevices.id = ".$_GET['device']." group by customer.id order by customername,rack,module,slot,portsports.`port`,subint.subint,cardtypes.name asc";
			$device_customers = mysql_query($query_device_customers, $subman) or die(mysql_error());
			$row_device_customers = mysql_fetch_assoc($device_customers);
			$totalRows_device_customers = mysql_num_rows($device_customers);

?>
    Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>" title="<?php echo $row_getDeviceGroup['descr']; ?>"><?php echo $row_getDeviceGroup['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><img src="images/<?php echo $row_getDevice['image']; ?>" alt="<?php echo $row_getDevice['devicetype']; ?>" width="20" align="absmiddle" /> <?php echo $row_getDevice['name']; ?></strong><br />
    
    
    <br />
    <p><a href="#_vlanpools">Device Vlan Pools</a><br />
      <a href="#_linecards">Device Line Cards</a><br />
      <a href="#_customers">Device Customer Port Usage</a>
      
    <h3 id="_vlanpools">Device VLAN Pools</h3>
    <?php if ($totalRows_device_vlan_pools > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><strong>Pool Name</strong></td>
          <td><strong>Root Device (Pool Owner)</strong></td>
          <td><strong>First VLAN</strong></td>
          <td><strong>Last VLAN</strong></td>
          <td><strong>Comments</strong></td>
          <td><strong>Shared/Local</strong></td>
        </tr>
        <?php do { 
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $_GET['device']; ?>&vlanpool=<?php echo $row_device_vlan_pools['id']; ?>" title="View VLANs"><?php echo $row_device_vlan_pools['name']; ?></a></td>
          <td><?php echo $row_device_vlan_pools['devicename']; ?></td>
          <td><?php echo $row_device_vlan_pools['poolstart']; ?></td>
          <td><?php echo $row_device_vlan_pools['poolend']; ?></td>
          <td><?php echo $row_device_vlan_pools['comments']; ?></td>
          <td><?php if ($row_device_vlan_pools['devicegroup'] != "") { echo "Shared"; } else { echo "Local"; } ?></td>
        </tr>
        <?php } while ($row_device_vlan_pools = mysql_fetch_assoc($device_vlan_pools)); ?>
      </table>
      <?php } // Show if recordset not empty 
  
	  else { ?>
    <p>There are no VLAN pools for this device.</p>
    <?php			
		}?>
    <h3 id="_linecards">Device Line Cards</h3>
    <?php if ($totalRows_device_cards > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
		  <td><strong>Rack/Module/Slot</strong></td>
          <td><strong>Card Type</strong></td>
          <td><strong>Timeslots</strong></td>
          <td><strong>Start Port</strong></td>
          <td><strong>End Port</strong></td>
        </tr>
        <?php do { 
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $_GET['group']; ?>&amp;device=<?php echo $_GET['device']; ?>&amp;card=<?php echo $row_device_cards['id']; ?>" title="Browse line card ports"><?php if (!(isset($row_device_cards['rack'])) && !(isset($row_device_cards['module'])) && !(isset($row_device_cards['slot']))) { echo "Virtual"; } else { if (isset($row_device_cards['rack'])) { echo $row_device_cards['rack']."/"; } if (isset($row_device_cards['module'])) { echo $row_device_cards['module'].'/'; } if (isset($row_device_cards['slot'])) { echo $row_device_cards['slot']; } } ?></a></td>		
          <td><?php echo $row_device_cards['cardtypename']; ?></td>
          <td><?php echo $row_device_cards['timeslots']; ?></td>
          <td><?php echo $row_device_cards['startport']; ?></td>
          <td><?php echo $row_device_cards['endport']; ?></td>
        </tr>
        <?php } while ($row_device_cards = mysql_fetch_assoc($device_cards)); ?>
      </table>
      <?php } // Show if recordset not empty 
  else { ?>
    <p>There are no line cards for this device.</p>
    <?php
  } ?>
  
  <h3 id="_customers">Device Customer Port Usage</h3>
    <?php if ($totalRows_device_customers > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
		  <td><strong>Customer Name</strong></td>
          <td><strong>Physical Port Count</strong></td>
          <td><strong>Sub-Interface Count</strong></td>
        </tr>
        <?php do { 
			
			$count++;
	  		if ($count % 2) {
				$bgcolour = "#EAEAEA";
			}
			else {
				$bgcolour = "#F5F5F5";
			} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=customers&amp;container=<?php echo $_GET['container']; ?>&amp;customer=<?php echo $row_device_customers['customerid']; ?>" title="Browse customer"><?php echo $row_device_customers['customername']; ?></a></td>		
          <td><?php echo $row_device_customers['physicalcount']; ?></td>
          <td><?php echo $row_device_customers['logicalcount']; ?></td>
        </tr>
        <?php } while ($row_device_customers = mysql_fetch_assoc($device_customers)); ?>
      </table>
      <?php } // Show if recordset not empty 
  else { ?>
    <p>There are no customers to display for this device.</p>
    <?php
  } ?>
    <?php
		}
		
		elseif ($_GET['group'] != "") { 
		
			mysql_select_db($database_subman, $subman);
			$query_getDeviceGroup = "SELECT * FROM portgroups WHERE portgroups.id = ".$_GET['group']."";
			$getDeviceGroup = mysql_query($query_getDeviceGroup, $subman) or die(mysql_error());
			$row_getDeviceGroup = mysql_fetch_assoc($getDeviceGroup);
			$totalRows_getDeviceGroup = mysql_num_rows($getDeviceGroup); 
			
			?>
    Device Group <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getDeviceGroup['name']; ?></strong><br />
    
    <p><a href="#_devices">Devices</a><br />
    <a href="#_vlanpools">Shared VLAN Pools</a>
    </p>
    
    <?php
            if ($_GET['group'] != "" && $_GET['group'] != 0 && !(getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 0 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
				}

			mysql_select_db($database_subman, $subman);
			$query_devices = "SELECT portsdevices.*, devicetypes.name as devicetypename, devicetypes.image, devicetypes.vlans, addresses.address, addresses.id AS addressID, addresses.network as addressnetwork, networks.v6mask, portgroups.name as devicegroupname  FROM portsdevices LEFT JOIN addresses ON addresses.id = portsdevices.address LEFT JOIN networks ON networks.id = addresses.network LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE portsdevices.devicegroup = ".$_GET['group']." AND portgroups.container = ".$_GET['container']." ORDER BY portsdevices.name";
			$devices = mysql_query($query_devices, $subman) or die(mysql_error());
			$row_devices = mysql_fetch_assoc($devices);
			$totalRows_devices = mysql_num_rows($devices);
			
			mysql_select_db($database_subman, $subman);
			$query_vlanpools = "SELECT vlanpool.*, portsdevices.name as devicename, portsdevices.id as deviceid FROM vlanpool LEFT JOIN portsdevices ON portsdevices.id = vlanpool.device LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup WHERE vlanpool.devicegroup = ".$_GET['group']." AND portgroups.container = ".$_GET['container']." ORDER BY vlanpool.name";
			$vlanpools = mysql_query($query_vlanpools, $subman) or die(mysql_error());
			$row_vlanpools = mysql_fetch_assoc($vlanpools);
			$totalRows_vlanpools = mysql_num_rows($vlanpools);
		
?>
	<h4 id="_devices">Devices</h4>
    
    <?php if ($totalRows_devices > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td>&nbsp;</td>
          <td><strong>Device Name</strong></td>
          <td><strong>Router Loopback</strong></td>
          <td><strong>Description</strong></td>
        </tr>
        <?php $count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><img src="images/<?php echo $row_devices['image']; ?>" alt="<?php echo $row_devices['devicetypename']; ?>" width="20" /></td>
          <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $row_devices['id']; ?>" title="<?php echo $row_devices['descr']; ?>"><?php echo $row_devices['name']; ?></a></td>
          <td><?php echo $row_devices['managementip']; ?></td>
          <td><?php echo $row_devices['descr']; ?></td>
        </tr>
        <?php } while ($row_devices = mysql_fetch_assoc($devices)); ?>
      </table>
      <?php } // Show if recordset not empty 
  
  else { ?>
    <p>There are no devices in this device group.</p>
    <?php			
		}?>
        
        <h4 id="_vlanpools">Shared VLAN Pools</h4>
        
        <?php if ($totalRows_vlanpools > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><strong>Pool Name</strong></td>
          <td><strong>Root Device (Pool Owner)</strong></td>
          <td><strong>First VLAN</strong></td>
          <td><strong>Last VLAN</strong></td>
          <td><strong>Comments</strong></td>
        </tr>
        <?php $count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&vlanpool=<?php echo $row_vlanpools['id']; ?>" title="Browse shared VLAN pool"><?php echo $row_vlanpools['name']; ?></a></td>
          <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $_GET['group']; ?>&device=<?php echo $row_vlanpools['deviceid']; ?>" title="Browse device"><?php echo $row_vlanpools['devicename']; ?></a></td>
          <td><?php echo $row_vlanpools['poolstart']; ?></td>
          <td><?php echo $row_vlanpools['poolend']; ?></td>
          <td><?php echo $row_vlanpools['comments']; ?></td>
        </tr>
        <?php } while ($row_vlanpools = mysql_fetch_assoc($vlanpools)); ?>
      </table>
      <?php } // Show if recordset not empty 
  
  else { ?>
    <p>There are no shared VLAN pools for this device group.</p>
    <?php			
		}?>
        
    <?php
		} 
	}
	
	
	
	if ($_GET['browse'] == "vpns") { 
		
		if ($_POST['action'] == "add_rt" && $_GET['vpn'] != "" && $_GET['provider'] != "") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT * FROM vpn WHERE vpn.id = ".$_GET['vpn']."";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			
			mysql_select_db($database_subman, $subman);
			$query_providerRTPools = "SELECT * FROM rtpool WHERE rtpool.provider = ".$_GET['provider']." ORDER BY rtpool.rtstart, rtpool.rtend";
			$providerRTPools = mysql_query($query_providerRTPools, $subman) or die(mysql_error());
			$row_providerRTPools = mysql_fetch_assoc($providerRTPools);
			$totalRows_providerRTPools = mysql_num_rows($providerRTPools);
			
            if (!(getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 10 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>" title="Browse Provider"><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VPN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVpn['name']; ?> <span class="text_red"><?php echo getVPNLayer($row_getVpn['layer']); ?></span></strong><br />
    <br />
    <form action="<?php echo $editFormAction; ?>" method="post" name="form11" id="form11">
      <table>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">RT Pool:</td>
          <td><select name="rt" class="input_standard">
            <?php 
do {  
?>
            <option value="<?php echo $row_providerRTPools['id']?>" >[<?php echo $row_providerRTPools['rtstart']?> - <?php echo $row_providerRTPools['rtend']?>] <?php echo $row_providerRTPools['descr']?></option>
            <?php
} while ($row_providerRTPools = mysql_fetch_assoc($providerRTPools));
?>
          </select></td>
        </tr>
        <tr> </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right" valign="top">Description:</td>
          <td><textarea name="descr" cols="50" rows="5" class="input_standard"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" value="Add route target" class="input_standard" /> <input name="cancel" type="button" class="input_standard" onClick="MM_callJS('history.back();')" value="Cancel" /></td>
        </tr>
      </table>
      <input type="hidden" name="vpn" value="<?php echo $_GET['vpn']; ?>" />
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="MM_insert" value="form11" />
    </form>
    <p>&nbsp;</p>
<?php 
		}
		
		elseif ($_POST['action'] == "edit_vrf") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT * FROM vpn WHERE vpn.id = ".$_GET['vpn']."";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			
			mysql_select_db($database_subman, $subman);
			$query_getVrf = "SELECT * FROM vrf WHERE vrf.id = ".$_GET['vrf']."";
			$getVrf = mysql_query($query_getVrf, $subman) or die(mysql_error());
			$row_getVrf = mysql_fetch_assoc($getVrf);
			$totalRows_getVrf = mysql_num_rows($getVrf);
			
            if (!(getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 10 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>" title="Browse Provider"><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VPN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;vpn=<?php echo $_GET['vpn']; ?>" title="Browse VPN"><?php echo $row_getVpn['name']; ?></a> <span class="text_red"><?php echo getVPNLayer($row_getVpn['layer']); ?></span><img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VRF <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVrf['name']; ?></strong><br />
    <br />
    <form action="<?php echo $editFormAction; ?>" method="post" name="frm_edit_vrf" id="frm_edit_vrf" onSubmit="MM_validateForm('name','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">VRF Name:</td>
          <td><input name="name" type="text" class="input_standard" value="<?php echo $row_getVrf['name']; ?>" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right" valign="top">VRF Description:</td>
          <td><textarea name="descr" cols="50" rows="5" class="input_standard"><?php echo $row_getVrf['descr']; ?></textarea></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Update VRF" /> <input name="cancel" type="button" class="input_standard" onClick="MM_callJS('history.back();')" value="Cancel" /></td>
        </tr>
      </table>
      <input type="hidden" name="id" value="<?php echo $_GET['vrf']; ?>" />
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="MM_update" value="frm_edit_vrf" />
    </form>
    <p>&nbsp;</p>
<?php
		}
		
		elseif ($_POST['action'] == "add_vrf" && $_GET['vpn'] != "" && $_GET['provider'] != "") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT * FROM vpn WHERE vpn.id = ".$_GET['vpn']."";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			
			mysql_select_db($database_subman, $subman);
			$query_providerRDPools = "SELECT * FROM rdpool WHERE rdpool.provider = ".$_GET['provider']." ORDER BY rdpool.rdstart, rdpool.rdend";
			$providerRDPools = mysql_query($query_providerRDPools, $subman) or die(mysql_error());
			$row_providerRDPools = mysql_fetch_assoc($providerRDPools);
			$totalRows_providerRDPools = mysql_num_rows($providerRDPools);
			
            if (!(getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 10 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>" title="Browse Provider"><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VPN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVpn['name']; ?> <span class="text_red"><?php echo getVPNLayer($row_getVpn['layer']); ?></span></strong><br />
    <br />
    <form action="<?php echo $editFormAction; ?>" method="post" name="form10" id="form10" onSubmit="MM_validateForm('name','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">RD Pool:</td>
          <td><select name="rd" class="input_standard">
            <?php 
do {  
?>
            <option value="<?php echo $row_providerRDPools['id']?>" >[<?php echo $row_providerRDPools['rdstart']?> - <?php echo $row_providerRDPools['rdend']?>] <?php echo $row_providerRDPools['descr']?></option>
            <?php
} while ($row_providerRDPools = mysql_fetch_assoc($providerRDPools));
?>
          </select></td>
        </tr>
        <tr> </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">VRF Name:</td>
          <td><input name="name" type="text" class="input_standard" value="" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right" valign="top">VRF Description:</td>
          <td><textarea name="descr" cols="50" rows="5" class="input_standard"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add VRF" /> <input name="cancel" type="button" class="input_standard" onClick="MM_callJS('history.back();')" value="Cancel" /></td>
        </tr>
      </table>
      <input type="hidden" name="vpn" value="<?php echo $_GET['vpn']; ?>" />
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="MM_insert" value="form10" />
    </form>
    <p>&nbsp;</p>
<?php
		}
		
		elseif ($_POST['action'] == "delete_provider" && $_GET['provider'] != "") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
						
            if (!(getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20  || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></strong><br />
    <br />
    
    <form action="" method="post" target="_self" name="frm_delete_provider" id="frm_delete_provider">
      <p>Are you sure you want to delete this provider?</p>
      <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
      <input name="id" type="hidden" id="id" value="<?php echo $_GET['provider']; ?>" />
      <input name="MM_delete" type="hidden" id="MM_delete" value="frm_delete_provider" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
    </form>
<?php
		}
		
		elseif ($_POST['action'] == "delete_vpn" && $_GET['vpn'] != "" && $_GET['provider'] != "") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT * FROM vpn WHERE vpn.id = ".$_GET['vpn']."";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			
            if (!(getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 20 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>" title="Browse Provider"><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VPN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVpn['name']; ?> <span class="text_red"><?php echo getVPNLayer($row_getVpn['layer']); ?></span></strong><br />
    <br />
    
    <form action="" method="post" target="_self" name="frm_delete_vpn" id="frm_delete_vpn">
      <p>Are you sure you want to delete this VPN?</p>
      <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
      <input name="id" type="hidden" id="id" value="<?php echo $_GET['vpn']; ?>" />
      <input name="provider" type="hidden" id="provider" value="<?php echo $_GET['provider']; ?>" />
      <input name="frm_delete_vpn" type="hidden" id="frm_delete_vpn" value="1" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
    </form>
<?php
		}
		
		elseif ($_POST['action'] == "delete_vrf" && $_GET['vrf'] != "" && $_GET['vpn'] != "" && $_GET['provider'] != "") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT * FROM vpn WHERE vpn.id = ".$_GET['vpn']."";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			
			mysql_select_db($database_subman, $subman);
			$query_getVrf = "SELECT * FROM vrf WHERE vrf.id = ".$_GET['vrf']."";
			$getVrf = mysql_query($query_getVrf, $subman) or die(mysql_error());
			$row_getVrf = mysql_fetch_assoc($getVrf);
			$totalRows_getVrf = mysql_num_rows($getVrf);
			
            if (!(getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 20 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>" title="Browse Provider"><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VPN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $_GET['provider']; ?>&amp;vpn=<?php echo $_GET['vpn']; ?>" title="Browse VPN"><?php echo $row_getVpn['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VRF <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVrf['name']; ?></strong><br />
    <br />
    
    <form action="" method="post" target="_self" name="frm_delete_vrf" id="frm_delete_vrf">
      <p>Are you sure you want to delete this VRF?</p>
      <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
      <input name="id" type="hidden" id="id" value="<?php echo $_GET['vrf']; ?>" />
      <input name="vpn" type="hidden" id="vpn" value="<?php echo $_GET['vpn']; ?>" />
      <input name="provider" type="hidden" id="provider" value="<?php echo $_GET['provider']; ?>" />
      <input name="frm_delete_vrf" type="hidden" id="frm_delete_vrf" value="1" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
    </form>
<?php
		}
		
		elseif ($_POST['action'] == "edit_vpn" && $_GET['vpn'] != "") {
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT * FROM vpn WHERE vpn.id = ".$_GET['vpn']."";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
		
			if (!(getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 10 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>" title="Browse Provider"><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VPN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVpn['name']; ?></strong><br />
    <br />

    
    <form action="<?php echo $editFormAction; ?>" method="post" name="frm_edit_vpn" id="frm_edit_vpn" onSubmit="MM_validateForm('name','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">VPN Name:</td>
          <td><input name="name" type="text" class="input_standard" size="32" maxlength="255" value="<?php echo $row_getVpn['name']; ?>" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right" valign="top">Description:</td>
          <td><textarea name="descr" cols="50" rows="5" class="input_standard"><?php echo $row_getVpn['descr']; ?></textarea></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Update VPN" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="provider" value="<?php echo $_GET['provider']; ?>" />
      <input type="hidden" name="id" value="<?php echo $_GET['vpn']; ?>" />
      <input type="hidden" name="MM_update" value="frm_edit_vpn" />
    </form>
    <p>&nbsp;</p>
<?php	
		}
		
		elseif ($_POST['action'] == "add_vpn" && $_GET['provider'] != "") {
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
		
			if (!(getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></strong><br />
    <br />
    
    <form action="<?php echo $editFormAction; ?>" method="post" name="form9" id="form9" onSubmit="MM_validateForm('name','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">VPN Name:</td>
          <td><input name="name" type="text" class="input_standard" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right" valign="top">Description:</td>
          <td><textarea name="descr" cols="50" rows="5" class="input_standard"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Layer:</td>
          <td><select name="layer" class="input_standard">
            <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Layer 2</option>
            <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>Layer 3</option>
            <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>Layer 2 Multipoint</option>
          </select></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add VPN" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="provider" value="<?php echo $_GET['provider']; ?>" />
      <input type="hidden" name="MM_insert" value="form9" />
    </form>
    <p>&nbsp;</p>
<?php	
		}
		
		elseif ($_POST['action'] == "delete_rtpool") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getRTPool = "SELECT * FROM rtpool WHERE rtpool.id = ".$_GET['rtpool']."";
			$getRTPool = mysql_query($query_getRTPool, $subman) or die(mysql_error());
			$row_getRTPool = mysql_fetch_assoc($getRTPool);
			$totalRows_getRTPool = mysql_num_rows($getRTPool);
			
			if (!(getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
		
        RT Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getRTPool['rtstart']; ?> - <?php echo $row_getRTPool['rtend']; ?> <?php echo $row_getRTPool['descr']; ?></strong><br />
    <br />
    
    <form action="" method="post" target="_self" name="frm_delete_rtpool" id="frm_delete_rtpool">
      <p>Are you sure you want to delete this RT pool?</p>
      <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
      <input name="id" type="hidden" id="id" value="<?php echo $_GET['rtpool']; ?>" />
      <input name="MM_delete" type="hidden" id="MM_delete" value="frm_delete_rtpool" />
      <input name="provider" type="hidden" id="provider" value="<?php echo $_GET['provider']; ?>" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
    </form>
<?php
		}
		
		elseif ($_POST['action'] == "delete_rdpool") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getRDPool = "SELECT * FROM rdpool WHERE rdpool.id = ".$_GET['rdpool']."";
			$getRDPool = mysql_query($query_getRDPool, $subman) or die(mysql_error());
			$row_getRDPool = mysql_fetch_assoc($getRDPool);
			$totalRows_ggetRDPool = mysql_num_rows($getRDPool);
			
			if (!(getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>

    	RD Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getRDPool['rdstart']; ?> - <?php echo $row_getRDPool['rdend']; ?> <?php echo $row_getRDPool['descr']; ?></strong><br />
    <br />
    
    <form action="" method="post" target="_self" name="frm_delete_rdpool" id="frm_delete_rdpool">
      <p>Are you sure you want to delete this RD pool?</p>
      <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
      <input name="id" type="hidden" id="id" value="<?php echo $_GET['rdpool']; ?>" />
      <input name="MM_delete" type="hidden" id="MM_delete" value="frm_delete_rdpool" />
      <input name="provider" type="hidden" id="provider" value="<?php echo $_GET['provider']; ?>" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
    </form>
<?php
		}
		
		elseif ($_POST['action'] == "delete_pseudowirepool") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getXconnectPool = "SELECT * FROM xconnectpool WHERE xconnectpool.id = ".$_GET['xconnectpool']."";
			$getXconnectPool = mysql_query($query_getXconnectPool, $subman) or die(mysql_error());
			$row_getXconnectPool = mysql_fetch_assoc($getXconnectPool);
			$totalRows_getXconnectPool = mysql_num_rows($getXconnectPool);
			
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Pseudowire Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getXconnectPool['start']; ?> - <?php echo $row_getXconnectPool['end']; ?> <?php echo $row_getXconnectPool['descr']; ?></strong><br />
    <br />
    
    <form action="" method="post" target="_self" name="frm_delete_pseudowirepool" id="frm_delete_pseudowirepool">
      <p>Are you sure you want to delete this pseudowire pool?</p>
      <input name="confirm" type="submit" class="input_standard" id="confirm" value="Confirm" />
      <input name="id" type="hidden" id="id" value="<?php echo $_GET['xconnectpool']; ?>" />
      <input name="MM_delete" type="hidden" id="MM_delete" value="frm_delete_pseudowirepool" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
    </form>
<?php
		}
		
		elseif ($_POST['action'] == "edit_rtpool") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getRTPool = "SELECT * FROM rtpool WHERE rtpool.id = ".$_GET['rtpool']."";
			$getRTPool = mysql_query($query_getRTPool, $subman) or die(mysql_error());
			$row_getRTPool = mysql_fetch_assoc($getRTPool);
			$totalRows_getRTPool = mysql_num_rows($getRTPool);
			
			if (!(getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
		
        RT Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getRTPool['rtstart']; ?> - <?php echo $row_getRTPool['rtend']; ?> <?php echo $row_getRTPool['descr']; ?></strong><br />
    <br />
    
    <form action="<?php echo $editFormAction; ?>" method="post" name="frm_edit_rtpool" id="frm_edit_rtpool" onSubmit="MM_validateForm('descr','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Description:</td>
          <td><textarea name="descr" cols="32" rows="5" class="input_standard"><?php echo $row_getRTPool['descr']; ?></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Update RT pool" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="id" value="<?php echo $_GET['rtpool']; ?>" />
      <input type="hidden" name="MM_update" value="frm_edit_rtpool" />
    </form>
    <p>&nbsp;</p>
    <?php        
		}
		
		elseif ($_POST['action'] == "edit_rdpool") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getRDPool = "SELECT * FROM rdpool WHERE rdpool.id = ".$_GET['rdpool']."";
			$getRDPool = mysql_query($query_getRDPool, $subman) or die(mysql_error());
			$row_getRDPool = mysql_fetch_assoc($getRDPool);
			$totalRows_getRDPool = mysql_num_rows($getRDPool);
			
			if (!(getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>

    	RD Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getRDPool['rdstart']; ?> - <?php echo $row_getRDPool['rdend']; ?> <?php echo $row_getRDPool['descr']; ?></strong><br />
    <br />
    
    <form action="<?php echo $editFormAction; ?>" method="post" name="frm_edit_rdpool" id="frm_edit_rdpool" onSubmit="MM_validateForm('descr','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Description:</td>
          <td><textarea name="descr" cols="32" rows="5" class="input_standard"><?php echo $row_getRDPool['descr']; ?></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Update RD pool" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="id" value="<?php echo $_GET['rdpool']; ?>" />
      <input type="hidden" name="MM_update" value="frm_edit_rdpool" />
    </form>
    <p>&nbsp;</p>
    <?php        
		}
		
		elseif ($_POST['action'] == "edit_pseudowirepool") { 
			
			mysql_select_db($database_subman, $subman);
			$query_getXconnectPool = "SELECT * FROM xconnectpool WHERE xconnectpool.id = ".$_GET['xconnectpool']."";
			$getXconnectPool = mysql_query($query_getXconnectPool, $subman) or die(mysql_error());
			$row_getXconnectPool = mysql_fetch_assoc($getXconnectPool);
			$totalRows_getXconnectPool = mysql_num_rows($getXconnectPool);
			
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>

    <br />
    <form action="<?php echo $editFormAction; ?>" method="post" name="frm_edit_pseudowirepool" id="frm_edit_pseudowirepool" onSubmit="MM_validateForm('descr','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Description:</td>
          <td><textarea name="descr" cols="32" rows="5" class="input_standard"><?php echo $row_getXconnectPool['descr']; ?></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Update pseudowire pool" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="id" value="<?php echo $_GET['xconnectpool']; ?>" />
      <input type="hidden" name="MM_update" value="frm_edit_pseudowirepool" />
    </form>
    <p>&nbsp;</p>
    <?php        
		}
		
		elseif ($_POST['action'] == "add_rtpool") { 
		
			if (!(getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>

    <br />
    <form action="<?php echo $editFormAction; ?>" method="post" name="frm_add_rtpool" id="frm_add_rtpool" onSubmit="MM_validateForm('descr','','R','start','','RisNum','end','','RisNum');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Description:</td>
          <td><textarea name="descr" cols="32" rows="5" class="input_standard"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Pool Start:</td>
          <td><input type="text" name="start" id="start" size="10" maxlength="11" class="input_standard"></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Pool End:</td>
          <td><input type="text" name="end" id="end" size="10" maxlength="11" class="input_standard"></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add RT pool" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="provider" value="<?php echo $_GET['provider']; ?>" />
      <input type="hidden" name="MM_insert" value="frm_add_rtpool" />
    </form>
    <p>&nbsp;</p>
    <?php        
		}
		
		elseif ($_POST['action'] == "add_rdpool") { 
		
			if (!(getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>

    <br />
    <form action="<?php echo $editFormAction; ?>" method="post" name="frm_add_rdpool" id="frm_add_rdpool" onSubmit="MM_validateForm('descr','','R','start','','RisNum','end','','RisNum');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Description:</td>
          <td><textarea name="descr" cols="32" rows="5" class="input_standard"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Pool Start:</td>
          <td><input type="text" name="start" id="start" size="10" maxlength="11" class="input_standard"></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Pool End:</td>
          <td><input type="text" name="end" id="end" size="10" maxlength="11" class="input_standard"></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add RD pool" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="provider" value="<?php echo $_GET['provider']; ?>" />
      <input type="hidden" name="MM_insert" value="frm_add_rdpool" />
    </form>
    <p>&nbsp;</p>
    <?php        
		}
		
		elseif ($_POST['action'] == "add_pseudowirepool") { 
		
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>

    <br />
    <form action="<?php echo $editFormAction; ?>" method="post" name="frm_add_pseudowirepool" id="frm_add_pseudowirepool" onSubmit="MM_validateForm('descr','','R','start','','RisNum','end','','RisNum');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Description:</td>
          <td><textarea name="descr" cols="32" rows="5" class="input_standard"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Pool Start:</td>
          <td><input type="text" name="start" id="start" size="10" maxlength="11" class="input_standard"></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Pool End:</td>
          <td><input type="text" name="end" id="end" size="10" maxlength="11" class="input_standard"></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add pseudowire pool" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="MM_insert" value="frm_add_pseudowirepool" />
    </form>
    <p>&nbsp;</p>
    <?php        
		}
		
		elseif ($_POST['action'] == "add_provider") { 
		
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>

    <br />
    <form action="<?php echo $editFormAction; ?>" method="post" name="frm_add_provider" id="frm_add_provider" onSubmit="MM_validateForm('descr','','R','name','','R','asnumber','','RisNum');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Provider Name:</td>
          <td><input name="name" type="text" class="input_standard" id="name" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Provider AS#:</td>
          <td><input name="asnumber" type="text" class="input_standard" id="asnumber" size="20" maxlength="11" /></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Description:</td>
          <td><textarea name="descr" cols="32" rows="5" class="input_standard"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add provider" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="MM_insert" value="frm_add_provider" />
    </form>
    <p>&nbsp;</p>
    <?php        
		}
		
		elseif ($_POST['action'] == "edit_provider" && $_GET['provider'] != "") { 
		
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
		
			if (!(getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 

					exit();
			} ?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></strong><br />
    <br />
    <form action="<?php echo $editFormAction; ?>" method="post" name="form4" id="form4" onSubmit="MM_validateForm('descr','','R','name','','R','asnumber','','RisNum');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Provider Name:</td>
          <td><input name="name" type="text" class="input_standard" id="name" value="<?php echo htmlentities($row_getProvider['name'], ENT_COMPAT, 'utf-8'); ?>" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Provider AS#:</td>
          <td><input name="asnumber" type="text" class="input_standard" id="asnumber" value="<?php echo htmlentities($row_getProvider['asnumber'], ENT_COMPAT, 'utf-8'); ?>" size="20" maxlength="11" /></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">Description:</td>
          <td><textarea name="descr" cols="32" rows="5" class="input_standard"><?php echo htmlentities($row_getProvider['descr'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
        </tr>
        <tr valign="baseline">
          <td align="right" valign="middle" nowrap="nowrap">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Update provider" /></td>
        </tr>
      </table>
      <input type="hidden" name="container" value="<?php echo htmlentities($row_getProvider['container'], ENT_COMPAT, 'utf-8'); ?>" />
      <input type="hidden" name="MM_update" value="form4" />
      <input type="hidden" name="id" value="<?php echo $row_getProvider['id']; ?>" />
    </form>
    <p>&nbsp;</p>
    <?php        
		}
		
		elseif ($_GET['provider'] != "" && $_GET['rtpool'] != "") { 
		
			if (!(getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) > 0 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
			}
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
			
			mysql_select_db($database_subman, $subman);
			$query_getRTpool = "SELECT * FROM rtpool WHERE rtpool.id = ".$_GET['rtpool']."";
			$getRTpool = mysql_query($query_getRTpool, $subman) or die(mysql_error());
			$row_getRTpool = mysql_fetch_assoc($getRTpool);
			$totalRows_getRTpool = mysql_num_rows($getRTpool);
			
			mysql_select_db($database_subman, $subman);
			$query_rdpool_rts = "SELECT rt.*, vpn.name FROM rt LEFT JOIN rtvpn ON rtvpn.rt = rt.id LEFT JOIN vpn ON vpn.id = rtvpn.vpn WHERE rt.rtpool = ".$_GET['rtpool']." ORDER BY rt.rt";
			$rdpool_rts = mysql_query($query_rdpool_rts, $subman) or die(mysql_error());
			$row_rdpool_rts = mysql_fetch_assoc($rdpool_rts);
			$totalRows_rdpool_rts = mysql_num_rows($rdpool_rts);
			?>
            
           Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $row_getProvider['id']; ?>" title="<?php echo $row_getProvider['descr']; ?>"><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> RT Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getRTpool['rtstart']; ?> - <?php echo $row_getRTpool['rtend']; ?> <?php echo $row_getRTpool['descr']; ?></strong>
    <br /><br />

<?php if ($totalRows_rdpool_rts > 0) { ?>
<table width="50%" border="0">
  <tr>
    <td><strong>Route Target</strong></td>
    <td><strong>Description</strong></td>
    </tr>
  <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
  <tr bgcolor="<?php echo $bgcolour; ?>">
    <td><?php echo $row_rdpool_rts['rt']; ?></td>

    <td>[<?php echo $row_rdpool_rts['name']; ?>] <?php echo $row_rdpool_rts['descr']; ?></td>
    </tr>
  <?php } while ($row_rdpool_rts = mysql_fetch_assoc($rdpool_rts)); ?>
</table> 
<?php }

else { ?>
	<p>There are no route targets to display for this RT pool.</p>
<?php } ?>
            
      <?php
		}
		
		elseif ($_GET['provider'] != "" && $_GET['rdpool'] != "") { 
		
			if (!(getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) > 0 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
			} 
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
			
			mysql_select_db($database_subman, $subman);
			$query_getRDpool = "SELECT * FROM rdpool WHERE rdpool.id = ".$_GET['rdpool']."";
			$getRDpool = mysql_query($query_getRDpool, $subman) or die(mysql_error());
			$row_getRDpool = mysql_fetch_assoc($getRDpool);
			$totalRows_getRDpool = mysql_num_rows($getRDpool);

			mysql_select_db($database_subman, $subman);
			$query_rdpool_rds = "SELECT rd.*, vpn.name FROM rd LEFT JOIN vrf ON vrf.rd = rd.id LEFT JOIN vpnvrf ON vpnvrf.vrf = vrf.id LEFT JOIN vpn ON vpn.id = vpnvrf.vpn WHERE rd.rdpool = ".$_GET['rdpool']." ORDER BY rd.rd";
			$rdpool_rds = mysql_query($query_rdpool_rds, $subman) or die(mysql_error());
			$row_rdpool_rds = mysql_fetch_assoc($rdpool_rds);
			$totalRows_rdpool_rds = mysql_num_rows($rdpool_rds);
			?>
            
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $row_getProvider['id']; ?>" title="<?php echo $row_getProvider['descr']; ?>"><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> RD Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getRDpool['rdstart']; ?> - <?php echo $row_getRDpool['rdend']; ?> <?php echo $row_getRDpool['descr']; ?></strong>
    <br /><br />

<?php if ($totalRows_rdpool_rds > 0) { ?>
<table width="50%" border="0">
  <tr>
    <td><strong>Route Distinguisher</strong></td>
    <td><strong>Name</strong></td>
    </tr>
  <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
  <tr bgcolor="<?php echo $bgcolour; ?>">
    <td><?php echo $row_rdpool_rds['rd']; ?></td>

    <td>[<?php echo $row_rdpool_rds['name']; ?>] <?php echo $row_rdpool_rds['descr']; ?></td>
    </tr>
  <?php } while ($row_rdpool_rds = mysql_fetch_assoc($rdpool_rds)); ?>
</table>
<?php }
	else { ?>
    <p>There are no route distinguishers to display for this RD pool.</p>
  	<?php } ?>
    
<?php
		}
		
		elseif ($_GET['xconnectpool'] != "") { 
		
			if (!(getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0)) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
			} 
			
			mysql_select_db($database_subman, $subman);
			$query_getXconnectpool = "SELECT * FROM xconnectpool WHERE xconnectpool.id = ".$_GET['xconnectpool']."";
			$getXconnectpool = mysql_query($query_getXconnectpool, $subman) or die(mysql_error());
			$row_getXconnectpool = mysql_fetch_assoc($getXconnectpool);
			$totalRows_getXconnectpool = mysql_num_rows($getXconnectpool);

			mysql_select_db($database_subman, $subman);
			$query_xconnectpool_xconnects = "SELECT * FROM xconnectid WHERE xconnectid.xconnectpool = ".$_GET['xconnectpool']." ORDER BY xconnectid.xconnectid";
			$xconnectpool_xconnects = mysql_query($query_xconnectpool_xconnects, $subman) or die(mysql_error());
			$row_xconnectpool_xconnects = mysql_fetch_assoc($xconnectpool_xconnects);
			$totalRows_xconnectpool_xconnects = mysql_num_rows($xconnectpool_xconnects);
			?>
            
    Pseudowire Pool <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getXconnectpool['xconnectstart']; ?> - <?php echo $row_getXconnectpool['xconnectend']; ?> <?php echo $row_getXconnectpool['descr']; ?></strong><br />
    <br />
    
    <?php if ($totalRows_xconnectpool_xconnects > 0) { // Show if recordset not empty ?>
  <table width="50%" border="0">
    <tr>
      <td><strong>Pseudowire ID</strong></td>
      <td><strong>Pseudowire Description</strong></td>
    </tr>
    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><?php echo $row_xconnectpool_xconnects['xconnectid']; ?></td>
      <td><?php echo $row_xconnectpool_xconnects['descr']; ?></td>
    </tr>
    <?php } while ($row_xconnectpool_xconnects = mysql_fetch_assoc($xconnectpool_xconnects)); ?>
  </table>
  <?php } // Show if recordset not empty 
		else { ?>
        
        	<p>There are no pseudowires for this pseudowire pool.</p>
        
  <?php } ?>
<?php
			
		}
		
		elseif ($_GET['provider'] != "" && $_GET['vpn'] != "" && $_GET['vrf'] != "") { 
		
			if (!(getVpnLevel($_GET['vpn'],$_SESSION['MM_Username']) > 0 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 0 && getVpnLevel($_GET['vpn'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
			}
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT * FROM vpn WHERE vpn.id = ".$_GET['vpn']."";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
			
			mysql_select_db($database_subman, $subman);
			$query_getVrf = "SELECT * FROM vrf WHERE vrf.id = ".$_GET['vrf']."";
			$getVrf = mysql_query($query_getVrf, $subman) or die(mysql_error());
			$row_getVrf = mysql_fetch_assoc($getVrf);
			$totalRows_getVrf = mysql_num_rows($getVrf);
			
			mysql_select_db($database_subman, $subman);
$query_vrf_ports = "select portsports.*, cardtypes.name as cardtypename, cards.rack, cards.module, cards.slot, devicetypes.name as devicetypename, devicetypes.image, portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.name as devicegroupname, portgroups.id as devicegroupID, vlan.name as vlanname, vlan.number as vlannumber, customer.name as customername, customer.id as customerID, addresses.id as addressID, addresses.address, addresses.network as addressnetwork, networks.v6mask, vlan.vlanpool FROM portsports LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup LEFT JOIN vpn ON vpn.id = portsports.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN vrf ON vrf.id = portsports.vrfvc LEFT JOIN vlan ON vlan.id = portsports.vlanid LEFT JOIN customer ON customer.id = portsports.customer LEFT JOIN addresses ON addresses.id = portsports.router LEFT JOIN networks ON networks.id = addresses.network WHERE vpn.id = ".$_GET['vpn']." AND vrf.id = ".$_GET['vrf']." AND provider.id = ".$_GET['provider']." ORDER BY portsdevices.name, cards.rack, cards.module, cards.slot, portsports.port;";
$vrf_ports = mysql_query($query_vrf_ports, $subman) or die(mysql_error());
$row_vrf_ports = mysql_fetch_assoc($vrf_ports);
$totalRows_vrf_ports = mysql_num_rows($vrf_ports);

			mysql_select_db($database_subman, $subman);
$query_vrf_subints = "select subint.*, portsports.port as portnumber, cardtypes.name as cardtypename, cards.rack, cards.module, cards.slot, devicetypes.name as devicetypename, devicetypes.image, portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.name as devicegroupname, portgroups.id as devicegroupID, vlan.name as vlanname, vlan.number as vlannumber, customer.name as customername, customer.id as customerID, addresses.id as addressID, addresses.address, addresses.network as addressnetwork, networks.v6mask, vlan.vlanpool FROM subint LEFT JOIN portsports ON portsports.id = subint.port LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup LEFT JOIN vpn ON vpn.id = subint.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN vrf ON vrf.id = subint.vrfvc LEFT JOIN vlan ON vlan.id = subint.vlanid LEFT JOIN customer ON customer.id = subint.customer LEFT JOIN addresses ON addresses.id = subint.router LEFT JOIN networks ON networks.id = addresses.network WHERE vpn.id = ".$_GET['vpn']." AND vrf.id = ".$_GET['vrf']." AND provider.id = ".$_GET['provider']." ORDER BY portsdevices.name, cards.rack, cards.module, cards.slot, portsports.port, subint.subint;";
$vrf_subints = mysql_query($query_vrf_subints, $subman) or die(mysql_error());
$row_vrf_subints = mysql_fetch_assoc($vrf_subints);
$totalRows_vrf_subints = mysql_num_rows($vrf_subints);

			?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_getProvider['id']; ?>" title="<?php echo $row_getProvider['descr']; ?>"><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VPN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>&vpn=<?php echo $_GET['vpn']; ?>" title="<?php echo $row_getVpn['descr']; ?>"><?php echo $row_getVpn['name']; ?></a> <span class="text_red"><?php echo getVPNLayer($row_getVpn['layer']); ?></span> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VRF <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVrf['name']; ?></strong><br />
    <h3>VRF Device Port Usage</h3>
    <?php if ($totalRows_vrf_ports > 0 || $totalRows_vrf_subints > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0">
    <tr>
      <td><strong>Rack/Module/Slot/Port<br />
        .Subinterface</strong></td>
      <td><strong>Device Name</strong></td>
      <td><strong>Port Type</strong></td>
      <td><strong>Usage</strong></td>
      <td><strong>Primary IP Address</strong></td>
      <td><strong>VLAN</strong></td>
      <td><strong>Comments</strong></td>
      <td><strong>Customer</strong></td>
    </tr>
    <?php if ($totalRows_vrf_ports > 0) { // Show if recordset not empty ?>
    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><?php if (!(isset($row_vrf_ports['rack'])) && !(isset($row_vrf_ports['module'])) && !(isset($row_vrf_ports['slot']))) { echo "Virtual"; } else { if (isset($row_vrf_ports['rack'])) { echo $row_vrf_ports['rack']."/"; } if (isset($row_vrf_ports['module'])) { echo $row_vrf_ports['module'].'/'; } if (isset($row_vrf_ports['slot'])) { echo $row_vrf_ports['slot']; } } ?>/<?php echo $row_vrf_ports['port']; ?> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_vrf_ports['devicegroupID']; ?>&device=<?php echo $row_vrf_ports['deviceID']; ?>&port=<?php echo $row_vrf_ports['id']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a></td>
      <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_vrf_ports['devicegroupID']; ?>&device=<?php echo $row_vrf_ports['deviceID']; ?>" title="Browse this device"><?php echo $row_vrf_ports['devicename']; ?></a></td>
      <td><?php echo $row_vrf_ports['cardtypename']; ?></td>
      <td><?php echo $row_vrf_ports['usage']; ?></td>
      <td><?php if ($row_vrf_ports['addressID'] != "") { ?><a href="?browse=networks&container=<?php echo $_GET['container']; ?>&group=&parent=<?php echo $row_vrf_ports['addressnetwork']; ?>#<?php echo $row_vrf_ports['addressID']; ?>" title="Browse this network"><?php if ($row_vrf_ports['v6mask'] == "") { echo long2ip($row_vrf_ports['address']); } else { echo Net_IPv6::compress(long2ipv6($row_vrf_ports['address'])); } ?></a><?php } ?></td>
      <td><?php if ($row_vrf_ports['vlanid'] != "" && $row_vrf_ports['vlanid'] != 0) { ?><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_vrf_ports['devicegroupID']; ?>&device=<?php echo $row_vrf_ports['deviceID']; ?>&vlanpool=<?php echo $row_vrf_ports['vlanpool']; ?>&vlan=<?php echo $row_vrf_ports['vlanid']; ?>" title="Browse this VLAN">[<?php echo $row_vrf_ports['vlannumber']; ?>] <?php echo $row_vrf_ports['vlanname']; ?></a><?php } ?></td>
      <td><?php echo $row_vrf_ports['comments']; ?></td>
      <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_vrf_ports['customerID']; ?>" title="Browse this customer"><?php echo $row_vrf_ports['customername']; ?></a></td>
    </tr>
    <?php } while ($row_vrf_ports = mysql_fetch_assoc($vrf_ports)); ?>
    <?php } // If recordset is not empty ?>
    
    <?php if ($totalRows_vrf_subints > 0) { // Show if recordset not empty ?>
    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><?php if (!(isset($row_vrf_subints['rack'])) && !(isset($row_vrf_subints['module'])) && !(isset($row_vrf_subints['slot']))) { echo "Virtual"; } else { if (isset($row_vrf_subints['rack'])) { echo $row_vrf_subints['rack']."/"; } if (isset($row_vrf_subints['module'])) { echo $row_vrf_subints['module'].'/'; } if (isset($row_vrf_subints['slot'])) { echo $row_vrf_subints['slot']; } } ?>/<?php echo $row_vrf_subints['portnumber']; ?><font color="#FF0000">.<?php echo $row_vrf_subints['subint']; ?></font> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_vrf_subints['devicegroupID']; ?>&device=<?php echo $row_vrf_subints['deviceID']; ?>&port=<?php echo $row_vrf_subints['port']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a></td>
      <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_vrf_subints['devicegroupID']; ?>&device=<?php echo $row_vrf_subints['deviceID']; ?>" title="Browse this device"><?php echo $row_vrf_subints['devicename']; ?></a></td>
      <td><?php echo $row_vrf_subints['cardtypename']; ?></td>
      <td><?php echo $row_vrf_subints['usage']; ?></td>
      <td><?php if ($row_vrf_subints['addressID'] != "") { ?><a href="?browse=networks&container=<?php echo $_GET['container']; ?>&group=&parent=<?php echo $row_vrf_subints['addressnetwork']; ?>#<?php echo $row_vrf_subints['addressID']; ?>" title="Browse this network"><?php if ($row_vrf_subints['v6mask'] == "") { echo long2ip($row_vrf_subints['address']); } else { echo Net_IPv6::compress(long2ipv6($row_vrf_subints['address'])); } ?></a><?php } ?></td>
      <td><?php if ($row_vrf_subints['vlanid'] != "" && $row_vrf_subints['vlanid'] != 0) { ?><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_vrf_subints['devicegroupID']; ?>&device=<?php echo $row_vrf_subints['deviceID']; ?>&vlanpool=<?php echo $row_vrf_subints['vlanpool']; ?>&vlan=<?php echo $row_vrf_subints['vlanid']; ?>" title="Browse this VLAN">[<?php echo $row_vrf_subints['vlannumber']; ?>] <?php echo $row_vrf_subints['vlanname']; ?></a><?php } ?></td>
      <td><?php echo $row_vrf_subints['comments']; ?></td>
      <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_vrf_subints['customerID']; ?>" title="Browse this customer"><?php echo $row_vrf_subints['customername']; ?></a></td>
    </tr>
    <?php } while ($row_vrf_subints = mysql_fetch_assoc($vrf_subints)); ?>
  <?php } // Show if recordset not empty ?>
  </table>
  <?php  
	}
  		else { ?>
        
        	<p>There are no ports in this VRF.</p>
        
        <?php }
 
		}
		
		
		elseif ($_GET['provider'] != "" && $_GET['vpn'] != "" && $_GET['xconnect'] != "") { 
		
			if (!(getVpnLevel($_GET['vpn'],$_SESSION['MM_Username']) > 0 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 0 && getVpnLevel($_GET['vpn'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
			}
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT * FROM vpn WHERE vpn.id = ".$_GET['vpn']."";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
			
			mysql_select_db($database_subman, $subman);
			$query_getXconnect = "SELECT * FROM xconnectid WHERE xconnectid.id = ".$_GET['xconnect']."";
			$getXconnect = mysql_query($query_getXconnect, $subman) or die(mysql_error());
			$row_getXconnect = mysql_fetch_assoc($getXconnect);
			$totalRows_getXconnect = mysql_num_rows($getXconnect);
			
			mysql_select_db($database_subman, $subman);
$query_xconnect_ports = "select portsports.*, cardtypes.name as cardtypename, cards.rack, cards.module, cards.slot, devicetypes.name as devicetypename, devicetypes.image, portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.name as devicegroupname, portgroups.id as devicegroupID, vlan.name as vlanname, vlan.number as vlannumber, customer.name as customername, customer.id as customerID, addresses.id as addressID, addresses.address, addresses.network as addressnetwork, networks.v6mask, vlan.vlanpool FROM portsports LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup LEFT JOIN vpn ON vpn.id = portsports.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN xconnectid ON xconnectid.id = portsports.vrfvc LEFT JOIN vlan ON vlan.id = portsports.vlanid LEFT JOIN customer ON customer.id = portsports.customer LEFT JOIN addresses ON addresses.id = portsports.router LEFT JOIN networks ON networks.id = addresses.network WHERE vpn.id = ".$_GET['vpn']." AND xconnectid.id = ".$_GET['xconnect']." AND provider.id = ".$_GET['provider']." ORDER BY portsdevices.name, cards.rack, cards.module, cards.slot, portsports.port;";
$xconnect_ports = mysql_query($query_xconnect_ports, $subman) or die(mysql_error());
$row_xconnect_ports = mysql_fetch_assoc($xconnect_ports);
$totalRows_xconnect_ports = mysql_num_rows($xconnect_ports);

			mysql_select_db($database_subman, $subman);
$query_xconnect_subints = "select subint.*, portsports.port as portnumber, portsports.id as portID, cardtypes.name as cardtypename, cards.rack, cards.module, cards.slot, devicetypes.name as devicetypename, devicetypes.image, portsdevices.name as devicename, portsdevices.id as deviceID, portgroups.name as devicegroupname, portgroups.id as devicegroupID, vlan.name as vlanname, vlan.number as vlannumber, customer.name as customername, customer.id as customerID, addresses.id as addressID, addresses.address, addresses.network as addressnetwork, networks.v6mask, vlan.vlanpool FROM subint LEFT JOIN portsports ON portsports.id = subint.port LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN devicetypes ON devicetypes.id = portsdevices.devicetype LEFT JOIN portgroups ON portgroups.id = portsdevices.devicegroup LEFT JOIN vpn ON vpn.id = subint.vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id LEFT JOIN provider ON provider.id = providervpn.provider LEFT JOIN xconnectid ON xconnectid.id = subint.vrfvc LEFT JOIN vlan ON vlan.id = subint.vlanid LEFT JOIN customer ON customer.id = subint.customer LEFT JOIN addresses ON addresses.id = subint.router LEFT JOIN networks ON networks.id = addresses.network WHERE vpn.id = ".$_GET['vpn']." AND xconnectid.id = ".$_GET['xconnect']." AND provider.id = ".$_GET['provider']." ORDER BY portsdevices.name, cards.rack, cards.module, cards.slot, portsports.port, subint.subint;";
$xconnect_subints = mysql_query($query_xconnect_subints, $subman) or die(mysql_error());
$row_xconnect_subints = mysql_fetch_assoc($xconnect_subints);
$totalRows_xconnect_subints = mysql_num_rows($xconnect_subints);

			?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_getProvider['id']; ?>" title="<?php echo $row_getProvider['descr']; ?>"><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VPN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>&vpn=<?php echo $_GET['vpn']; ?>" title="<?php echo $row_getVpn['descr']; ?>"><?php echo $row_getVpn['name']; ?></a> <span class="text_red"><?php echo getVPNLayer($row_getVpn['layer']); ?></span> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> Pseudowire <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getXconnect['xconnectid']; ?> <?php echo $row_getXconnect['descr']; ?></strong><br />
    <h3>Pseudowire Device Port Usage</h3>
    <?php if ($totalRows_xconnect_ports > 0 || $totalRows_xconnect_subints > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0">
    <tr>
      <td><strong>Rack/Module/Slot/Port<br />
        .Subinterface</strong></td>
      <td><strong>Device Name</strong></td>
      <td><strong>Port Type</strong></td>
      <td><strong>Usage</strong></td>
      <td><strong>Primary IP Address</strong></td>
      <td><strong>VLAN</strong></td>
      <td><strong>Comments</strong></td>
      <td><strong>Customer</strong></td>
    </tr>
    <?php if ($totalRows_xconnect_ports > 0) { // Show if recordset not empty ?>
    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><?php if (!(isset($row_xconnect_ports['rack'])) && !(isset($row_xconnect_ports['module'])) && !(isset($row_xconnect_ports['slot']))) { echo "Virtual"; } else { if (isset($row_xconnect_ports['rack'])) { echo $row_xconnect_ports['rack']."/"; } if (isset($row_xconnect_ports['module'])) { echo $row_xconnect_ports['module'].'/'; } if (isset($row_xconnect_ports['slot'])) { echo $row_xconnect_ports['slot']; } } ?>/<?php echo $row_xconnect_ports['port']; ?> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_xconnect_ports['devicegroupID']; ?>&device=<?php echo $row_xconnect_ports['deviceID']; ?>&port=<?php echo $row_xconnect_ports['id']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a></td>
      <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_xconnect_ports['devicegroupID']; ?>&device=<?php echo $row_xconnect_ports['deviceID']; ?>" title="Browse this device"><?php echo $row_xconnect_ports['devicename']; ?></a></td>
      <td><?php echo $row_xconnect_ports['cardtypename']; ?></td>
      <td><?php echo $row_xconnect_ports['usage']; ?></td>
      <td><?php if ($row_xconnect_ports['addressID'] != "") { ?><a href="?browse=networks&container=<?php echo $_GET['container']; ?>&group=&parent=<?php echo $row_xconnect_ports['addressnetwork']; ?>#<?php echo $row_xconnect_ports['addressID']; ?>" title="Browse this network"><?php if ($row_xconnect_ports['v6mask'] == "") { echo long2ip($row_xconnect_ports['address']); } else { echo Net_IPv6::compress(long2ipv6($row_xconnect_ports['address'])); } ?></a><?php } ?></td>
      <td><?php if ($row_xconnect_ports['vlanid'] != "" && $row_xconnect_ports['vlanid'] != 0) { ?><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_xconnect_ports['devicegroupID']; ?>&device=<?php echo $row_xconnect_ports['deviceID']; ?>&vlanpool=<?php echo $row_xconnect_ports['vlanpool']; ?>&vlan=<?php echo $row_xconnect_ports['vlanid']; ?>" title="Browse this VLAN">[<?php echo $row_xconnect_ports['vlannumber']; ?>] <?php echo $row_xconnect_ports['vlanname']; ?></a><?php } ?></td>
      <td><?php echo $row_xconnect_ports['comments']; ?></td>
      <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_xconnect_ports['customerID']; ?>" title="Browse this customer"><?php echo $row_xconnect_ports['customername']; ?></a></td>
    </tr>
    <?php } while ($row_xconnect_ports = mysql_fetch_assoc($xconnect_ports)); ?>
    <?php } // If recordset is not empty ?>
    
    <?php if ($totalRows_xconnect_subints > 0) { // Show if recordset not empty ?>
    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><?php if (!(isset($row_xconnect_subints['rack'])) && !(isset($row_xconnect_subints['module'])) && !(isset($row_xconnect_subints['slot']))) { echo "Virtual"; } else { if (isset($row_xconnect_subints['rack'])) { echo $row_xconnect_subints['rack']."/"; } if (isset($row_xconnect_subints['module'])) { echo $row_xconnect_subints['module'].'/'; } if (isset($row_xconnect_subints['slot'])) { echo $row_xconnect_subints['slot']; } } ?>/<?php echo $row_xconnect_subints['portnumber']; ?><font color="#FF0000">.<?php echo $row_xconnect_subints['subint']; ?></font> <a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_xconnect_subints['devicegroupID']; ?>&device=<?php echo $row_xconnect_subints['deviceID']; ?>&port=<?php echo $row_xconnect_subints['portID']; ?>&linkview=1" title="View link information"><img src="images/link.gif" alt="View link information" border="0" align="absmiddle" /></a></td>
      <td><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_xconnect_subints['devicegroupID']; ?>&device=<?php echo $row_xconnect_subints['deviceID']; ?>" title="Browse this device"><?php echo $row_xconnect_subints['devicename']; ?></a></td>
      <td><?php echo $row_xconnect_subints['cardtypename']; ?></td>
      <td><?php echo $row_xconnect_subints['usage']; ?></td>
      <td><?php if ($row_xconnect_subints['addressID'] != "") { ?><a href="?browse=networks&container=<?php echo $_GET['container']; ?>&group=&parent=<?php echo $row_xconnect_subints['addressnetwork']; ?>#<?php echo $row_xconnect_subints['addressID']; ?>" title="Browse this network"><?php if ($row_xconnect_subints['v6mask'] == "") { echo long2ip($row_xconnect_subints['address']); } else { echo Net_IPv6::compress(long2ipv6($row_xconnect_subints['address'])); } ?></a><?php } ?></td>
      <td><?php if ($row_xconnect_subints['vlanid'] != "" && $row_xconnect_subints['vlanid'] != 0) { ?><a href="?browse=devices&container=<?php echo $_GET['container']; ?>&group=<?php echo $row_xconnect_subints['devicegroupID']; ?>&device=<?php echo $row_xconnect_subints['deviceID']; ?>&vlanpool=<?php echo $row_xconnect_subints['vlanpool']; ?>&vlan=<?php echo $row_xconnect_subints['vlanid']; ?>" title="Browse this VLAN">[<?php echo $row_xconnect_subints['vlannumber']; ?>] <?php echo $row_xconnect_subints['vlanname']; ?></a><?php } ?></td>
      <td><?php echo $row_xconnect_subints['comments']; ?></td>
      <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_xconnect_subints['customerID']; ?>" title="Browse this customer"><?php echo $row_xconnect_subints['customername']; ?></a></td>
    </tr>
    <?php } while ($row_xconnect_subints = mysql_fetch_assoc($xconnect_subints)); ?>
  <?php } // Show if recordset not empty ?>
  </table>
  <?php  
	}
  		else { ?>
        
        	<p>There are no ports for this pseudowire.</p>
        
    <?php }
 
		}
		
		elseif ($_GET['vpn'] != "") {
            
			if (!(getVpnLevel($_GET['vpn'],$_SESSION['MM_Username']) > 0 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 0 && getVpnLevel($_GET['vpn'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
			}
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider); 
			
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT * FROM vpn WHERE vpn.id = ".$_GET['vpn']."";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			
			mysql_select_db($database_subman, $subman);
			$query_vpn_vrfs = "SELECT vrf.*, rd.rd as rdnumber, rd.descr as rddescr, rdpool.rdstart, rdpool.rdend, rdpool.descr as rdpooldescr, rdpool.id as rdpoolID, provider.asnumber, provider.name as providername FROM vrf LEFT JOIN vpnvrf ON vpnvrf.vrf = vrf.id LEFT JOIN vpn ON vpn.id = vpnvrf.vpn LEFT JOIN rd ON rd.id = vrf.rd LEFT JOIN rdpool ON rdpool.id = rd.rdpool LEFT JOIN provider ON provider.id = rdpool.provider WHERE vpn.id = ".$row_getVpn['id']."";
			$vpn_vrfs = mysql_query($query_vpn_vrfs, $subman) or die(mysql_error());
			$row_vpn_vrfs = mysql_fetch_assoc($vpn_vrfs);
			$totalRows_vpn_vrfs = mysql_num_rows($vpn_vrfs);
			
			mysql_select_db($database_subman, $subman);
			$query_vpn_customers = "SELECT customer.* FROM customer LEFT JOIN vpncustomer ON vpncustomer.customer = customer.id LEFT JOIN vpn ON vpn.id = vpncustomer.vpn WHERE vpn.id = ".$row_getVpn['id']."";
			$vpn_customers = mysql_query($query_vpn_customers, $subman) or die(mysql_error());
			$row_vpn_customers = mysql_fetch_assoc($vpn_customers);
			$totalRows_vpn_customers = mysql_num_rows($vpn_customers);
			
			mysql_select_db($database_subman, $subman);
			$query_vpn_xconnects = "SELECT xconnectid.*, xconnectpool.id as xconnectpoolID, xconnectpool.descr as xconnectpooldescr FROM xconnectid LEFT JOIN xconnectpool ON xconnectpool.id = xconnectid.xconnectpool LEFT JOIN vpnxconnect ON vpnxconnect.xconnect = xconnectid.id LEFT JOIN vpn ON vpn.id = vpnxconnect.vpn WHERE vpn.id = ".$row_getVpn['id']."";
			$vpn_xconnects = mysql_query($query_vpn_xconnects, $subman) or die(mysql_error());
			$row_vpn_xconnects = mysql_fetch_assoc($vpn_xconnects);
			$totalRows_vpn_xconnects = mysql_num_rows($vpn_xconnects);
			
			mysql_select_db($database_subman, $subman);
			$query_vpn_rts = "SELECT rt.*, rtpool.descr as rtpoolname, provider.asnumber, provider.name as providername FROM rt LEFT JOIN rtvpn ON rtvpn.rt = rt.id LEFT JOIN vpn ON vpn.id = rtvpn.vpn LEFT JOIN rtpool ON rtpool.id = rt.rtpool LEFT JOIN provider ON provider.id = rtpool.provider WHERE vpn.id = ".$row_getVpn['id']."";
			$vpn_rts = mysql_query($query_vpn_rts, $subman) or die(mysql_error());
			$row_vpn_rts = mysql_fetch_assoc($vpn_rts);
			$totalRows_vpn_rts = mysql_num_rows($vpn_rts);

			?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $row_getProvider['id']; ?>" title="<?php echo $row_getProvider['descr']; ?>"><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></a> <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> VPN <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getVpn['name']; ?> <span class="text_red"><?php echo getVPNLayer($row_getVpn['layer']); ?></span></strong><br />
    <br />
    <p><a href="#_vrfs">VRFs</a><br />
    	<a href="#_rts">Route Targets</a><br />
      <a href="#_xconnects">Pseudowires</a><br />
      <a href="#_customers">Customers</a></p>
      
    <h3 id="_vrfs">VRFs</h3>
    <?php if ($totalRows_vpn_vrfs > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><strong>VRF Name</strong></td>
          <td><strong>VRF Description</strong></td>
          <td><strong>Route Distinguisher</strong></td>
          <td><strong>RD Pool</strong></td>
        </tr>
        <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>&vpn=<?php echo $_GET['vpn']; ?>&vrf=<?php echo $row_vpn_vrfs['id']; ?>" title="Browse VPN VRF"><?php echo $row_vpn_vrfs['name']; ?></a></td>
          <td><?php echo $row_vpn_vrfs['descr']; ?></td>
          <td><?php echo $row_vpn_vrfs['asnumber']; ?>:<?php echo $row_vpn_vrfs['rdnumber']; ?> <?php echo $row_vpn_vrfs['rddescr']; ?></td>
          <td><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>&rdpool=<?php echo $row_vpn_vrfs['rdpoolID']; ?>" title="Browse this RD pool"><?php echo $row_vpn_vrfs['rdpooldescr']; ?></a></td>
        </tr>
        <?php } while ($row_vpn_vrfs = mysql_fetch_assoc($vpn_vrfs)); ?>
      </table>
      <?php } // Show if recordset not empty 
  		else { ?>
    <p>There are no VRFs for this VPN (this is normal for layer 2 pseudowires).</p>
    <?php } ?>
    
    <h3 id="_rts">Route Targets</h3>
    
    <?php if ($totalRows_vpn_rts > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><input name="check_all" type="checkbox" class="input_standard" id="check_all" value="1" onClick="checkAll(document.getElementById('rt_action'), this.checked);" /></td>
          <td><strong>RT Pool</strong></td>
          <td><strong>Route Target</strong></td>
          <td><strong>Description</strong></td>
        </tr>
        <form action="" method="post" target="_self" id="rt_action" name="rt_action">
        	<input type="hidden" name="totalRows_vpn_rts" value="<?php echo $totalRows_vpn_rts; ?>" />
        	<input type="hidden" name="rt_action_type" value="delete" />
        <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        
        
            
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><input name="check_<?php echo $count; ?>" type="checkbox" class="input_standard" id="check_<?php echo $row_vpn_rts['id']; ?>" value="1" />
            <input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $row_vpn_rts['id']; ?>" /></td>
          <td><?php echo $row_vpn_rts['rtpoolname']; ?></td>
          <td><?php echo $row_vpn_rts['asnumber']; ?>:<?php echo $row_vpn_rts['rt']; ?></td>
          <td><?php echo $row_vpn_rts['descr']; ?></td>
        </tr>
        <?php } while ($row_vpn_rts = mysql_fetch_assoc($vpn_rts)); ?>
        </form>
      </table>
      <?php } // Show if recordset not empty 
  		else { ?>
    <p>There are no route targets for this VPN (this is normal for layer 2 pseudowires).</p>
    <?php } ?>
    
    <h3 id="_xconnects">Pseudowires</h3>
    <?php if ($totalRows_vpn_xconnects > 0) { // Show if recordset not empty ?>
      <table width="100%" border="0">
        <tr>
          <td><strong>Pseudowire ID</strong></td>
          <td><strong>Description</strong></td>
          <td><strong>Pseudowire Pool</strong></td>
        </tr>
        <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
        <tr bgcolor="<?php echo $bgcolour; ?>">
          <td><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>&vpn=<?php echo $_GET['vpn']; ?>&xconnect=<?php echo $row_vpn_xconnects['id']; ?>" title="Browse VPN pseudowire"><?php echo $row_vpn_xconnects['xconnectid']; ?></a></td>
          <td><?php echo $row_vpn_xconnects['descr']; ?></td>
          <td><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&xconnectpool=<?php echo $row_vpn_xconnects['xconnectpoolID']; ?>" title="Browse pseudowire pool"><?php echo $row_vpn_xconnects['xconnectpooldescr']; ?></a></td>
        </tr>
        <?php } while ($row_vpn_xconnects = mysql_fetch_assoc($vpn_xconnects)); ?>
      </table>
      <?php } // Show if recordset not empty 
  		else { ?>
    <p>There are no pseudowires for this VPN (this is normal for layer 3 MPLS VPN).</p>
    <?php } ?>
    
    <h3 id="_customers">Customers</h3>
    <?php if ($totalRows_vpn_customers > 0) { // Show if recordset not empty ?>
<table width="50%" border="0">
    <tr>
      <td><strong>Customer Name</strong></td>
      <td><strong>Account #</strong></td>
    </tr>
    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><a href="?browse=customers&container=<?php echo $_GET['container']; ?>&customer=<?php echo $row_vpn_customers['id']; ?>" title="Browse this customer"><?php echo $row_vpn_customers['name']; ?></a></td>
      <td><?php echo $row_vpn_customers['account']; ?></td>
    </tr>
    <?php } while ($row_vpn_customers = mysql_fetch_assoc($vpn_customers)); ?>
  </table>
  <?php } // Show if recordset not empty
  		else { ?>
        	
            <p>There are no customers attached to this VPN.</p>
            
       <?php } ?>
<?php	
		}
		
		elseif ($_GET['provider'] != "") {
            
			if (!(getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 0 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
    <p class="text_red">Error: You are not authorised to view the selected content.</p>
    <?php 
					exit();
			}
			
			mysql_select_db($database_subman, $subman);
			$query_getProvider = "SELECT * FROM provider WHERE provider.id = ".$_GET['provider']."";
			$getProvider = mysql_query($query_getProvider, $subman) or die(mysql_error());
			$row_getProvider = mysql_fetch_assoc($getProvider);
			$totalRows_getProvider = mysql_num_rows($getProvider);
			
			if (isset($_GET['sort'])) {
				$sort = $_GET['sort'];
			}
			else {
				$sort = "vpn.name";
			}
			if (isset($_GET['sortdir'])) {
				$sortdir = $_GET['sortdir'];
			}
			else {
				$sortdir = "ASC";
			}
			
			mysql_select_db($database_subman, $subman);
			$query_provider_vpns = "SELECT vpn.* FROM vpn LEFT JOIN providervpn ON providervpn.vpn = vpn.id WHERE providervpn.provider = ".$_GET['provider']." ORDER BY ".$sort." ".$sortdir."";
			$provider_vpns = mysql_query($query_provider_vpns, $subman) or die(mysql_error());
			$row_provider_vpns = mysql_fetch_assoc($provider_vpns);
			$totalRows_provider_vpns = mysql_num_rows($provider_vpns);
			
			mysql_select_db($database_subman, $subman);
			$query_provider_rdpools = "SELECT * FROM rdpool WHERE rdpool.provider = ".$_GET['provider']."";
			$provider_rdpools = mysql_query($query_provider_rdpools, $subman) or die(mysql_error());
			$row_provider_rdpools = mysql_fetch_assoc($provider_rdpools);
			$totalRows_provider_rdpools = mysql_num_rows($provider_rdpools);

			mysql_select_db($database_subman, $subman);
			$query_provider_rtpools = "SELECT * FROM rtpool WHERE rtpool.provider = ".$_GET['provider']." ORDER BY rtpool.rtstart";
			$provider_rtpools = mysql_query($query_provider_rtpools, $subman) or die(mysql_error());
			$row_provider_rtpools = mysql_fetch_assoc($provider_rtpools);
			$totalRows_provider_rtpools = mysql_num_rows($provider_rtpools);


			?>
    Provider <img src="images/arrow.gif" alt="Arrow" border="0" align="absmiddle" /> <strong><?php echo $row_getProvider['asnumber']; ?> <?php echo $row_getProvider['name']; ?></strong><br />
    <br />
    
    <p><a href="#_rdpools">Provider RD Pools</a><br />
    <a href="#_rtpools">Provider RT Pools</a><br />
    <a href="#_vpns">Provider VPNs</a></p>
    
    <h3 id="_rdpools">Provider RD Pools</h3>
    
    <?php if ($totalRows_provider_rdpools > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0">
    <tr>
      <td><strong>RD Start</strong></td>
      <td><strong>RD End</strong></td>
      <td><strong>Pool Description</strong></td>
    </tr>
    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><?php echo $row_provider_rdpools['rdstart']; ?></td>
      <td><?php echo $row_provider_rdpools['rdend']; ?></td>
      <td><a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $_GET['provider']; ;?>&amp;rdpool=<?php echo $row_provider_rdpools['id']; ?>" title="Browse this RD pool"><?php echo $row_provider_rdpools['descr']; ?></a></td>
    </tr>
    <?php } while ($row_provider_rdpools = mysql_fetch_assoc($provider_rdpools)); ?>
  </table>
  <?php } // Show if recordset not empty 
  		else { ?>
        
  			<p>There are no RD pools for this provider</p>
        
  <?php } ?>
  
  <h3 id="#_rtpools">Provider RT Pools</h3>
  <?php if ($totalRows_provider_rtpools > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0">
    <tr>
      <td><strong>RT Start</strong></td>
      <td><strong>RT End</strong></td>
      <td><strong>Pool Description</strong></td>
    </tr>
    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
        <td><?php echo $row_provider_rtpools['rtstart']; ?></td>
        <td><?php echo $row_provider_rtpools['rtend']; ?></td>
        <td><a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $_GET['provider']; ;?>&amp;rtpool=<?php echo $row_provider_rtpools['id']; ?>" title="Browse this RT pool"><?php echo $row_provider_rtpools['descr']; ?></a></td>
      </tr>
      <?php } while ($row_provider_rtpools = mysql_fetch_assoc($provider_rtpools)); ?>
  </table>
  <?php } // Show if recordset not empty 
  		else { ?>
        
        	<p>There are no RT pools for this provider.</p>
        
  <?php } ?>
<h3 id="_vpns">Provider VPNs</h3>

<?php if ($totalRows_provider_vpns > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0">
    <tr>
      <td><strong><a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $_GET['provider']; ?>&amp;sort=vpn.layer&amp;sortdir=<?php if ($sortdir == "ASC") { echo "DESC"; } else { echo "ASC"; } ?>" title="Sort by this column">VPN Type</a></strong><?php if ($sort == "vpn.layer") { ?><?php if ($sortdir == "ASC") { ?><img src="h_arrow_over.gif" alt="Sorted by this column (ascending)" /><?php } else { ?><img src="h_arrow_over_up.gif" alt="Sorted by this column (descending)" /><?php } ?><?php } ?></td>
      <td><strong><a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $_GET['provider']; ?>&amp;sort=vpn.name&amp;sortdir=<?php if ($sortdir == "ASC") { echo "DESC"; } else { echo "ASC"; } ?>" title="Sort by this column">VPN Name</a></strong><?php if ($sort == "vpn.name") { ?><?php if ($sortdir == "ASC") { ?><img src="h_arrow_over.gif" alt="Sorted by this column (ascending)" /><?php } else { ?><img src="h_arrow_over_up.gif" alt="Sorted by this column (descending)" /><?php } ?><?php } ?></td>
      <td><strong><a href="?browse=vpns&amp;container=<?php echo $_GET['container']; ?>&amp;provider=<?php echo $_GET['provider']; ?>&amp;sort=vpn.descr&amp;sortdir=<?php if ($sortdir == "ASC") { echo "DESC"; } else { echo "ASC"; } ?>" title="Sort by this column">Description</a></strong><?php if ($sort == "vpn.descr") { ?><?php if ($sortdir == "ASC") { ?><img src="h_arrow_over.gif" alt="Sorted by this column (ascending)" /><?php } else { ?><img src="h_arrow_over_up.gif" alt="Sorted by this column (descending)" /><?php } ?><?php } ?></td>
    </tr>
    <?php
  	$count = 0;
							do { 
								$count++;
								if ($count % 2) {
									$bgcolour = "#EAEAEA";
								}
								else {
									$bgcolour = "#F5F5F5";
								} ?>
    <tr bgcolor="<?php echo $bgcolour; ?>">
      <td><?php echo getVPNLayer($row_provider_vpns['layer']); ?></td>
      <td><a href="?browse=vpns&container=<?php echo $_GET['container']; ?>&provider=<?php echo $_GET['provider']; ?>&vpn=<?php echo $row_provider_vpns['id']; ?>" title="Browse this VPN"><?php echo $row_provider_vpns['name']; ?></a></td>
      <td><?php echo $row_provider_vpns['descr']; ?></td>
    </tr>
    <?php } while ($row_provider_vpns = mysql_fetch_assoc($provider_vpns)); ?>
  </table>
  <?php } // Show if recordset not empty 
  		else { ?>
        
   	<p>There are no VPNs for this provider</p>
        
  <?php } ?>
<?php	
		}
		else { ?>
		
		<?php if (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) { ?>
        <p><a href="#" onClick="document.getElementById('action').value = 'add_provider'; document.getElementById('frm_vpn_action').submit(); return false;">Add an MPLS provider</a></p>
        <p><a href="#" onClick="document.getElementById('action').value = 'add_pseudowirepool'; document.getElementById('frm_vpn_action').submit(); return false;">Add a pseudowire pool</a></p>
        <?php } ?>
        
		<?php }
	
	}
	elseif ($_GET['browse'] == "useradmin") {
		
		mysql_select_db($database_subman, $subman);
		$query_getUser = "SELECT * FROM user WHERE user.username = '".$_SESSION['MM_Username']."'";
		$getUser = mysql_query($query_getUser, $subman) or die(mysql_error());
		$row_getUser = mysql_fetch_assoc($getUser);
		$totalRows_getUser = mysql_num_rows($getUser);
		
		?>
        
<form action="<?php echo $editFormAction; ?>" method="post" name="form12" id="form12" onSubmit="MM_validateForm('firstname','','R','lastname','','R','password','','R','email','','RisEmail');return document.MM_returnValue">
  <table>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">First Name:</td>
      <td><input name="firstname" type="text" class="input_standard" id="firstname" value="<?php echo htmlentities($row_getUser['firstname'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Last Name:</td>
      <td><input name="lastname" type="text" class="input_standard" id="lastname" value="<?php echo htmlentities($row_getUser['lastname'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Password:</td>
      <td><input name="password" type="password" class="input_standard" id="password" value="<?php echo htmlentities($row_getUser['password'], ENT_COMPAT, 'utf-8'); ?>" size="20" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Email:</td>
      <td><input name="email" type="text" class="input_standard" id="email" value="<?php echo htmlentities($row_getUser['email'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Update profile" class="input_standard" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form12" />
  <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
  <input type="hidden" name="id" value="<?php echo $row_getUser['id']; ?>" />
</form>
<p>&nbsp;</p>
<?php }

	elseif ($_GET['browse'] == "reports") {
		
		mysql_select_db($database_subman, $subman);
		$query_customers = "SELECT * FROM customer WHERE container = '".$_GET['container']."' ORDER BY customer.name";
		$customers = mysql_query($query_customers, $subman) or die(mysql_error());
		$row_customers = mysql_fetch_assoc($customers);
		$totalRows_customers = mysql_num_rows($customers);
			
				  
?>
	<h3>Report Builder</h3>
    
    <?php if (isset($_GET['report']) && $_GET['report'] != "") {
		
		$query_report = "SELECT * FROM reports WHERE container = '".$_GET['container']."' AND username = '".$_SESSION['MM_Username']."' AND id = '".$_GET['report']."'";
		$report = mysql_query($query_report, $subman) or die(mysql_error());
		$row_report = mysql_fetch_assoc($report);
		$totalRows_report = mysql_num_rows($report);
		
		$query_reportobjects = "SELECT * FROM reportobjects WHERE report = '".$_GET['report']."'";
		$reportobjects = mysql_query($query_reportobjects, $subman) or die(mysql_error());
		$row_reportobjects = mysql_fetch_assoc($reportobjects);
		$totalRows_reportobjects = mysql_num_rows($reportobjects);
		
	?>
    
    <form action="" target="_self" name="frm_report_edit" id="frm_report_edit" method="post">
    	
        <p>Report Title</p>
    	<input type="text" class="input_standard" size="30" maxlength="255" name="report_title" id="report_title" value="<?php echo $row_report['title']; ?>" />
    	
        <p>Report Description</p>
        <textarea cols="50" rows="5" class="input_standard" name="report_descr" id="report_descr"><?php echo $row_report['descr']; ?></textarea>
        
       	<br />
      <input type="hidden" name="MM_update" value="frm_report_edit" />
        <input type="hidden" name="report_id" value="<?php echo $row_report['id']; ?>" />
        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
        <input type="submit" value="Update" class="input_standard" />&nbsp;
        
    </form>
    
    <p><strong>Report Objects</strong></p>
    
    <?php if ($totalRows_reportobjects > 0) { ?>
    <table width="50%" border="0">
     <tr>
     	<td width="20px">&nbsp;</td>
     	<td width="100px"><strong>Object Type</strong></td>
     	<td><strong>Object Value</strong></td>
    </tr>
    
    <?php do { ?>
    	
        <?php if ($row_reportobjects['reporttype'] == "network") {
			
			mysql_select_db($database_subman, $subman);
			$query_getNetwork = "SELECT * FROM networks WHERE networks.id = '".$row_reportobjects['objectid']."'";
			$getNetwork = mysql_query($query_getNetwork, $subman) or die(mysql_error());
			$row_getNetwork = mysql_fetch_assoc($getNetwork);
			$totalRows_getNetwork = mysql_num_rows($getNetwork);
			
			?>
            
			<tr>
				<td><a href="#" title="Delete object" onClick="if (confirm('Are you sure you want to remove this object?')) { document.frm_delete_reportobject<?php echo $row_reportobjects['id']; ?>.submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete report object" border="0" align="absmiddle" /></a></td>
				<td>Network</td>
				<td>            
            <?php                        
			if ($row_getNetwork['v6mask'] == "") { ?>
            	
                <a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_getNetwork['networkGroup']; ?>&amp;parent=<?php echo $row_getNetwork['id']; ?>" title="Browse network"><?php echo long2ip($row_getNetwork['network']); if ($row_getNetwork['mask'] == "255.255.255.255") { echo "/32"; } else { echo get_slash($row_getNetwork['mask']); } ?></a>
                
            <?php } else { ?>
            
            	<a href="?browse=networks&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_getNetwork['networkGroup']; ?>&amp;parent=<?php echo $row_getNetwork['id']; ?>" title="Browse network"><?php echo Net_IPv6::Compress(long2ipv6($row_getNetwork['network']))."/".$row_getNetwork['v6mask']; ?></a>
            
            <?php } ?>
            
            	</td>
            </tr>
            
        <?php } ?>
        
        <?php if ($row_reportobjects['reporttype'] == "vlanpool") {
			
			mysql_select_db($database_subman, $subman);
			$query_getVlanPool = "SELECT portsdevices.devicegroup AS `group`, vlanpool.* FROM vlanpool LEFT JOIN portsdevices ON portsdevices.id = vlanpool.device WHERE vlanpool.id = '".$row_reportobjects['objectid']."'";
			$getVlanPool = mysql_query($query_getVlanPool, $subman) or die(mysql_error());
			$row_getVlanPool = mysql_fetch_assoc($getVlanPool);
			$totalRows_getVlanPool = mysql_num_rows($getVlanPool);
			
			?>
            
			<tr>
				<td><a href="#" title="Delete object" onClick="if (confirm('Are you sure you want to remove this object?')) { document.frm_delete_reportobject<?php echo $row_reportobjects['id']; ?>.submit(); } else { return false; }"><img src="images/cancel.gif" alt="Delete report object" border="0" align="absmiddle" /></a></td>
				<td>VLAN Pool</td>
				<td><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_getVlanPool['group']; ?>&amp;device=<?php echo $row_getVlanPool['device']; ?>&amp;vlanpool=<?php echo $row_getVlanPool['id']; ?>" title="Browse VLAN pool"><?php echo $row_getVlanPool['name']; ?></a></td>
            </tr>
            
        <?php } ?>
            
            <form action="" method="post" target="_self" name="frm_delete_reportobject<?php echo $row_reportobjects['id']; ?>" id="frm_delete_reportobject<?php echo $row_reportobjects['id']; ?>">
                <input type="hidden" name="objectid" value="<?php echo $row_reportobjects['id']; ?>" />
                <input type="hidden" name="MM_delete" value="frm_delete_reportobject" />
                <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
        		<input type="hidden" name="report_id" value="<?php echo $_GET['report']; ?>" id="report_id" />
            </form>
                                    
    <?php } while ($row_reportobjects = mysql_fetch_assoc($reportobjects)); ?>
    
    </table>
    
    <br />
    
    <input type="button" class="input_standard" onclick="MM_openBrWindow('includes/reports.php?container=<?php echo $_GET['container']; ?>&amp;report=<?php echo $_GET['report']; ?>&amp;type=html','report','scrollbars=yes,width=800,height=600')" value="Generate HTML Report" />
        <input type="button" class="input_standard" onclick="MM_openBrWindow('includes/reportsExcel.php?container=<?php echo $_GET['container']; ?>&amp;report=<?php echo $_GET['report']; ?>&amp;type=excel','report','scrollbars=yes,width=800,height=600')" value="Generate Excel&reg; Report" />
    
    <?php } else { ?>
    
   	<p>There are no objects for this report.  To add objects to the report, browse a reportable object, right-click and select the 'Add to report builder' option and choose the report to add the object to.</p>
    
    <?php } ?>
    
    <?php } else { ?>
    
    <p>No report has been selected.  To create a report, browse a reportable object, then right-click and select the 'Add to report builder >> New report...' option.</p>
   
   
	<?php } ?>
    
<?php } ?>

  	<?php if ($totalRows_networks == 0 && $_GET['browse'] == "networks" && $row_parent['v6mask'] == "" && $totalRows_parent > 0) {
  	
  	echo "<hr />";
			if ($row_parent['mask'] == "255.255.255.254") {
				
				echo "<strong>Network:</strong> ".long2ip($row_parent['network'])." --> ".long2ip($row_parent['network']+1)." (".$remaining." remaining)";
				
			}
			else {
				
				echo "<strong>Network:</strong> ".$net['cidr']." (".$net['total']." addresses)";
				echo "<br>";
				echo "<strong>Broadcast:</strong> ".long2ip($net['broadcast']);
				echo "<br>";
				echo "<strong>Normal Host Range:</strong> ".long2ip($net['firstaddress'])." --> ".long2ip($net['lastaddress'])." (".$remaining." remaining)";
			
			}
		
        } ?>
        
</div>

<div>

  
  
<!--HTML for Context Menu 1-->
<ul id="contextmenu1" class="jqcontextmenu">


  <?php if ($_GET['browse'] == "networks") { 	?>
  
  <form action="" target="_self" name="frm_report_networks" id="frm_report_networks" method="post">
        <input type="hidden" name="report_type" value="network" id="report_type" />
        <input type="hidden" name="report_object_id" value="<?php echo $_GET['parent']; ?>" id="report_network_id" />
        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
        <input type="hidden" name="report_id" value="" id="report_id" />
        <input type="hidden" name="MM_insert" value="add_to_report" />
   </form>
   <form action="" target="_self" method="post" name="frm_report_new_network" id="frm_report_new_network">
        <input type="hidden" name="report_title" id="report_title" value="" />
        <input type="hidden" name="report_type" value="network" />
        <input type="hidden" name="report_object_id" value="<?php echo $_GET['parent']; ?>" />
        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
        <input type="hidden" name="MM_insert" value="new_report" />
    </form>
        
    <form action="" method="post" target="_self" id="frm_network_action">
      <input name="browse" type="hidden" id="browse2" value="<?php echo $_GET['browse']; ?>" />
      <input name="container" type="hidden" id="container2" value="<?php echo $_GET['container']; ?>" />
      <input name="group" type="hidden" id="group2" value="<?php echo $_GET['group']; ?>" />
      <input name="parent" type="hidden" id="parent2" value="<?php echo $_GET['parent']; ?>" />
      
      
      	<input type="hidden" name="action" id="action" value="delete_address">
        <?php 
		
		if ($totalRows_networks == 0 && $remaining > 0 && $_GET['parent'] != "" && ((getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) > 10) || ((getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 10) && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == "") || ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_address'; document.getElementById('frm_network_action').submit(); return false;">Add an address to this network</a></li>
        <?php } ?>
        <?php if ($totalRows_networks == 0 && $remaining != $net['total'] && $_GET['parent'] != "" && ((getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) > 20) || ((getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 20) && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == "") || ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20) && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == ""))) { ?>
         <li><a href="#" onClick="document.address_action.address_action_type.value = 'delete'; document.address_action.submit(); return false;">Delete selected address(es)</a></li>
        <?php } ?>
        <?php if ($_GET['parent'] != "" && $_GET['parent'] != 0 && ((getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) > 10) || ((getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 10) && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == "") || ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_network'; document.getElementById('frm_network_action').submit(); return false;">Edit this network</a></li>
        <?php } ?>
        <?php if ($_GET['parent'] != "" && $_GET['parent'] != 0 && ((getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) > 20) || ((getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 20) && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == "") || ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20) && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_network'; document.getElementById('frm_network_action').submit(); return false;">Delete this network</a></li>
        <?php } ?>
        <?php if (($totalRows_networks > 0 || ($totalRows_addresses == 0 && $totalRows_networks == 0)) && $_GET['parent'] != "" && $_GET['parent'] != 0 && ((getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) > 10) || ((getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 10) && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == "") || ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == ""))) { ?>
        <?php if ($row_parent['v6mask'] == "") { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_network'; document.getElementById('frm_network_action').submit(); return false;">Scan for free IPv4 networks</a></li>
        <?php } else { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_v6_network'; document.getElementById('frm_network_action').submit(); return false;">Scan for free IPv6 networks</a></li>
        <?php }
			} ?>
        <?php if (($totalRows_networks > 0 || ($totalRows_addresses == 0 && $totalRows_networks == 0)) && ((getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == 127) || ((getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == 127) && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == "") || ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127) && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == ""))) { ?>
        <?php if ($_GET['parent'] != "" && $_GET['parent'] != 0 && $row_parent['v6mask'] == "") { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_base_network'; document.getElementById('frm_network_action').submit(); return false;">Add a base IPv4 network</a></li>
        <?php } elseif ($_GET['parent'] != "" && $_GET['parent'] != 0) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_base_network_v6'; document.getElementById('frm_network_action').submit(); return false;">Add a a base IPv6 network</a></li>
        <?php } else { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_base_network'; document.getElementById('frm_network_action').submit(); return false;">Add a base IPv4 network</a></li>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_base_network_v6'; document.getElementById('frm_network_action').submit(); return false;">Add a base IPv6 network</a></li>
        <?php } ?>
        <?php } ?>
        <?php if ($_GET['networkgroups'] == 1) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_network_group'; document.getElementById('frm_network_action').submit(); return false;">Add a network group</a></li>
        	<?php if ($_GET['group'] != "") { ?>
        	<li><a href="#" onClick="document.getElementById('action').value = 'delete_network_group'; document.getElementById('frm_network_action').submit(); return false;">Delete this network group</a></li>
         	<?php } ?>
        <?php } ?>
        <?php if ($_GET['parent'] != "" && $_GET['parent'] != 0 && ((getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) > 0) || ((getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 0) && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == "") || ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0) && getNetGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getNetworkLevel($_GET['parent'],$_SESSION['MM_Username']) == ""))) { 
			
			mysql_select_db($database_subman, $subman);
			$query_reports = "SELECT * FROM reports WHERE container = '".$_GET['container']."' AND username = '".$_SESSION['MM_Username']."' ORDER BY reports.title";
			$reports = mysql_query($query_reports, $subman) or die(mysql_error());
			$row_reports = mysql_fetch_assoc($reports);
			$totalRows_reports = mysql_num_rows($reports);
			?>
        
		<li><a class="NOLINK"><img src="images/reports-icon.gif" width="20" height="20" alt="Add object to report builder" border="0" align="absmiddle"> Add to report builder</a>
        	<ul><li><a href="#" onClick="document.getElementById('frm_report_networks').submit(); return false;" onmouseover="document.getElementById('report_title_text').focus()">New report</a>
            	<ul><input type="text" size="30" maxlength="255" id="report_title_text" name="report_title_text" class="input_standard" />
                	<input type="button" class="input_standard" value="Create..." onclick="document.getElementById('report_title').value = document.getElementById('report_title_text').value; document.getElementById('frm_report_new_network').submit();"/>
                	</ul>
                </li>
            <?php if ($totalRows_reports > 0) { 
				do { ?>
                <li><a href="#" title="<?php echo $row_reports['descr']; ?>" onClick="document.getElementById('report_id').value = '<?php echo $row_reports['id']; ?>'; document.getElementById('frm_report_networks').submit(); return false;"><?php echo $row_reports['title']; ?></a></li>
               	<?php } while ($row_reports = mysql_fetch_assoc($reports)); ?>
            <?php } ?>
            </ul>
        </li>
        <?php } ?>
    </form>
    <?php } ?>
    <?php if ($_GET['browse'] == "vpns") { 	?>
    <form action="" method="post" target="_self" id="frm_vpn_action" name="frm_vpn_action">
      <input name="browse" type="hidden" id="browse" value="<?php echo $_GET['browse']; ?>" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="action" id="action">
        <?php if ($totalRows_vpn_providers > 0 && $_GET['provider'] != "" && (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_provider'; document.getElementById('frm_vpn_action').submit(); return false;">Edit this provider</a></li>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_vpn'; document.getElementById('frm_vpn_action').submit(); return false;">Add a VPN</a></li>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_rdpool'; document.getElementById('frm_vpn_action').submit(); return false;">Add an RD pool</a></li>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_rtpool'; document.getElementById('frm_vpn_action').submit(); return false;">Add an RT pool</a></li>
        <?php } ?>
        <?php if ($_GET['vpn'] != "") { 
			mysql_select_db($database_subman, $subman);
			$query_getVpn = "SELECT * FROM vpn WHERE vpn.id = ".$_GET['vpn']."";
			$getVpn = mysql_query($query_getVpn, $subman) or die(mysql_error());
			$row_getVpn = mysql_fetch_assoc($getVpn);
			$totalRows_getVpn = mysql_num_rows($getVpn);
			?>
        <?php if ($row_getVpn['layer'] == 3 && (getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 10 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_vrf'; document.getElementById('frm_vpn_action').submit(); return false;">Add a VRF to this VPN</a></li>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_rt'; document.getElementById('frm_vpn_action').submit(); return false;">Add a route target to this VPN</a></li>
        <?php if ($_GET['vrf'] != "" && (getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 10 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_vrf'; document.getElementById('frm_vpn_action').submit(); return false;">Edit this VRF</a></li>
        <?php } ?>
        <?php if ($_GET['rdpool'] != "" && (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_rdpool'; document.getElementById('frm_vpn_action').submit(); return false;">Edit this RD pool</a></li>
        <?php } ?>
        <?php if ($_GET['rtpool'] != "" && (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_rtpool'; document.getElementById('frm_vpn_action').submit(); return false;">Edit this RT pool</a></li>
        <?php } ?>
        <?php if ($_GET['rdpool'] != "" && (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_rdpool'; document.getElementById('frm_vpn_action').submit(); return false;">Delete this RD pool</a></li>
        <?php } ?>
        <?php if ($_GET['rtpool'] != "" && (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_rtpool'; document.getElementById('frm_vpn_action').submit(); return false;">Delete this RT pool</a></li>
        <?php } ?>
        <?php if ($_GET['vrf'] != "" && (getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 20 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_vrf'; document.getElementById('frm_vpn_action').submit(); return false;">Delete this VRF</a></li>
        <?php } ?>
        <?php if ($_GET['vpn'] != "" && $totalRows_vpn_rts > 0 && (getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 20 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.rt_action.rt_action_type.value = 'delete'; document.rt_action.submit(); return false;">Delete selected route target(s)</a></li>
        <?php } ?>
        <?php } ?>
        <?php if ($_GET['vpn'] != "" && (getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 10 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_vpn'; document.getElementById('frm_vpn_action').submit(); return false;">Edit this VPN</a></li>
        <?php } ?>
        <?php if ($_GET['vpn'] != "" && (getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) > 20 || (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == "" && getVpnLevel($_GET['vpn'], $_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_vpn'; document.getElementById('frm_vpn_action').submit(); return false;">Delete this VPN</a></li>
        <?php } ?>
        <?php } ?>
        <?php if ($_GET['rdpool'] != "" && (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_rdpool'; document.getElementById('frm_vpn_action').submit(); return false;">Edit this RD pool</a></li>
        <?php } ?>
        <?php if ($_GET['rtpool'] != "" && (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_rtpool'; document.getElementById('frm_vpn_action').submit(); return false;">Edit this RT pool</a></li>
        <?php } ?>
        <?php if ($_GET['rdpool'] != "" && (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_rdpool'; document.getElementById('frm_vpn_action').submit(); return false;">Delete this RD pool</a></li>
        <?php } ?>
        <?php if ($_GET['rtpool'] != "" && (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_rtpool'; document.getElementById('frm_vpn_action').submit(); return false;">Delete this RT pool</a></li>
        <?php } ?>
        <?php if (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_provider'; document.getElementById('frm_vpn_action').submit(); return false;">Add an MPLS provider</a></li>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_pseudowirepool'; document.getElementById('frm_vpn_action').submit(); return false;">Add a pseudowire pool</a></li>
        <?php } ?>
        <?php if ($_GET['xconnectpool'] != "" && getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_pseudowirepool'; document.getElementById('frm_vpn_action').submit(); return false;">Edit this pseudowire pool</a></li>
        <?php } ?>
        <?php if ($_GET['provider'] != "" && (getProviderLevel($_GET['provider'], $_SESSION['MM_Username']) > 20 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getProviderLevel($_GET['provider'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_provider'; document.getElementById('frm_vpn_action').submit(); return false;">Delete this MPLS provider</a></li>
        <?php } ?>
        <?php if ($_GET['xconnectpool'] != "" && getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_pseudowirepool'; document.getElementById('frm_vpn_action').submit(); return false;">Delete this pseudowire pool</a></li>
        <?php } ?>
    </form>
    <?php } ?>
    <?php if ($_GET['browse'] == "customers") { 	?>
    <form action="" method="post" target="_self" id="frm_customer_action" name="frm_customer_action">
      <input name="browse" type="hidden" id="browse" value="<?php echo $_GET['browse']; ?>" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="action" id="action">
      	<?php if (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_customer'; document.getElementById('frm_customer_action').submit(); return false;">Add a customer</a></li>
        <?php } ?>
        <?php if ($totalRows_customer > 0 && (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_customer'; document.getElementById('frm_customer_action').submit(); return false;">Edit this customer</a></li>
        <?php } ?>
    </form>
    <?php } ?>
    <?php if ($_GET['browse'] == "devices") { 	?>
    
    <form action="" target="_self" name="frm_report_networks" id="frm_report_vlanpools" method="post">
        <input type="hidden" name="report_type" value="vlanpool" id="report_type" />
        <input type="hidden" name="report_object_id" value="<?php echo $_GET['vlanpool']; ?>" id="report_vlanpool_id" />
        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
        <input type="hidden" name="report_id" value="" id="report_id" />
        <input type="hidden" name="MM_insert" value="add_to_report" />
   </form>
   <form action="" target="_self" method="post" name="frm_report_new_vlanpool" id="frm_report_new_vlanpool">
        <input type="hidden" name="report_title" id="report_title" value="" />
        <input type="hidden" name="report_type" value="vlanpool" />
        <input type="hidden" name="report_object_id" value="<?php echo $_GET['vlanpool']; ?>" />
        <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
        <input type="hidden" name="MM_insert" value="new_report" />
    </form>
    
    <form action="" method="post" target="_self" id="frm_device_action" name="frm_device_action">
      <input name="browse" type="hidden" id="browse" value="<?php echo $_GET['browse']; ?>" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="action" id="action">
		<?php if (isset($_GET['browsedevicetypes']) && getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_devicetype'; document.getElementById('frm_device_action').submit(); return false;">Add a device type</a></li>
        <?php } ?>      
      	<?php if (isset($_GET['browsecardtypes']) && getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_cardtype'; document.getElementById('frm_device_action').submit(); return false;">Add a line card type</a></li>
        <?php } ?>
        <?php if (isset($_GET['devicegroups']) && getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_devicegroup'; document.getElementById('frm_device_action').submit(); return false;">Add a device group</a></li>
        	<?php if ($_GET['group'] != "") { ?>
        	<li><a href="#" onClick="document.getElementById('action').value = 'delete_device_group'; document.getElementById('frm_device_action').submit(); return false;">Delete this device group</a></li>
         	<?php } ?>
        <?php } ?>
      	<?php if (($_GET['linkview'] != 1) && (getDeviceGroupLevel($_GET['group'], $_SESSION['MM_Username']) > 10 || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_device'; document.getElementById('frm_device_action').submit(); return false;">Add a device</a></li>
        <?php if (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) == 127) { ?>
        	<?php if ($_GET['browsetemplates'] == "service" && $_GET['template'] != "") { ?>
            	<li><a href="#" onClick="document.getElementById('action').value = 'add_script'; document.getElementById('frm_device_action').submit(); return false;">Add a script</a></li>
            	<li><a href="#" onClick="document.getElementById('action').value = 'add_field'; document.getElementById('frm_device_action').submit(); return false;">Add an arbitrary field</a></li>
                <?php if ($_GET['script'] != "") { ?>
                	<li><a href="#" onClick="document.getElementById('action').value = 'add_variable'; document.getElementById('frm_device_action').submit(); return false;">Add a script variable</a></li>
                <?php } ?>
            	<li><a href="#" onClick="document.getElementById('action').value = 'delete_servicetemplate'; document.getElementById('frm_device_action').submit(); return false;">Delete this service template</a></li>
            	<li><a href="#" onClick="document.getElementById('action').value = 'copy_servicetemplate'; document.getElementById('frm_device_action').submit(); return false;">Copy this service template</a></li>
            <?php } ?>
        <?php } ?>
        <?php } ?>
        <?php if (($_GET['device'] != "") && ($_GET['linkview'] != 1) && (getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 10 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 10 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'edit_device'; document.getElementById('frm_device_action').submit(); return false;">Edit this device</a></li>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_card'; document.getElementById('frm_device_action').submit(); return false;">Add a line card</a></li>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_vlanpool'; document.getElementById('frm_device_action').submit(); return false;">Add a VLAN pool</a></li>
        <?php if (($_GET['device'] != "") && (getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 20 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_device'; document.getElementById('frm_device_action').submit(); return false;">Delete this device</a></li>
        <?php } ?>
        <?php if ($_GET['vlanpool'] != "") { ?>
        	<li><a href="#" onClick="document.getElementById('action').value = 'edit_vlanpool'; document.getElementById('frm_device_action').submit(); return false;">Edit this VLAN pool</a></li>
        <?php } ?>
        <?php if ($_GET['vlan'] != "") { ?>
        	<li><a href="#" onClick="document.getElementById('action').value = 'edit_vlan'; document.getElementById('frm_device_action').submit(); return false;">Edit this VLAN</a></li>
        <?php } ?>
        <?php if (($_GET['device'] != "") && ($_GET['vlanpool'] != "") && (getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 20 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_vlanpool'; document.getElementById('frm_device_action').submit(); return false;">Delete this VLAN pool</a></li>
        <?php } ?>
        <?php if (($_GET['card'] != "") && (getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 20 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_card'; document.getElementById('frm_device_action').submit(); return false;">Delete this line card</a></li>
        <?php } ?>
        <?php if ($_GET['vlanpool'] != "") { ?>
        	<li><a href="#" onClick="document.getElementById('action').value = 'add_vlan'; document.getElementById('frm_device_action').submit(); return false;">Add a VLAN</a></li>
        <?php } ?>
        <?php } ?>
        <?php if (($_GET['device'] != "" && $_GET['vlan'] != "") && (getDeviceLevel($_GET['device'], $_SESSION['MM_Username']) > 20 || (getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) > 20 && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == "") || (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 20 && getDeviceGroupLevel($_GET['group'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['device'],$_SESSION['MM_Username']) == ""))) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'delete_vlan'; document.getElementById('frm_device_action').submit(); return false;">Delete this VLAN</a></li>
		<?php } ?>
		<?php if ($_GET['vlanpool'] != "" && $_GET['vlanpool'] != 0 && ((getDeviceLevel($_GET['parent'],$_SESSION['MM_Username']) > 0) || ((getDeviceGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) > 0) && getDeviceLevel($_GET['parent'],$_SESSION['MM_Username']) == "") || ((getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 0) && getDeviceGroupLevel($row_parent['networkGroup'],$_SESSION['MM_Username']) == "" && getDeviceLevel($_GET['parent'],$_SESSION['MM_Username']) == ""))) { 
			
			mysql_select_db($database_subman, $subman);
			$query_reports = "SELECT * FROM reports WHERE container = '".$_GET['container']."' AND username = '".$_SESSION['MM_Username']."' ORDER BY reports.title";
			$reports = mysql_query($query_reports, $subman) or die(mysql_error());
			$row_reports = mysql_fetch_assoc($reports);
			$totalRows_reports = mysql_num_rows($reports);
			?>
        
		<li><a class="NOLINK"><img src="images/reports-icon.gif" width="20" height="20" alt="Add object to report builder" border="0" align="absmiddle"> Add to report builder</a>
        	<ul><li><a href="#" onClick="document.getElementById('frm_report_vlanpools').submit(); return false;" onmouseover="document.getElementById('report_title_text').focus()">New report</a>
            	<ul><input type="text" size="30" maxlength="255" id="report_title_text" name="report_title_text" class="input_standard" />
                	<input type="button" class="input_standard" value="Create..." onclick="document.getElementById('report_title').value = document.getElementById('report_title_text').value; document.getElementById('frm_report_new_vlanpool').submit();"/>
                	</ul>
                </li>
            <?php if ($totalRows_reports > 0) { 
				do { ?>
                <li><a href="#" title="<?php echo $row_reports['descr']; ?>" onClick="document.getElementById('report_id').value = '<?php echo $row_reports['id']; ?>'; document.getElementById('frm_report_vlanpools').submit(); return false;"><?php echo $row_reports['title']; ?></a></li>
               	<?php } while ($row_reports = mysql_fetch_assoc($reports)); ?>
            <?php } ?>
            </ul>
        </li>
        <?php } ?>
    </form>
    <?php } ?>
    <?php if ($_GET['browse'] == "bgp") { 	?>
    <form action="" method="post" target="_self" id="frm_bgp_action" name="frm_bgp_action">
      <input name="browse" type="hidden" id="browse" value="<?php echo $_GET['browse']; ?>" />
      <input name="container" type="hidden" id="container" value="<?php echo $_GET['container']; ?>" />
      <input type="hidden" name="action" id="action">
      	<?php if (getContainerLevel($_GET['container'],$_SESSION['MM_Username']) > 10) { ?>
        <li><a href="#" onClick="document.getElementById('action').value = 'add_as'; document.getElementById('frm_bgp_action').submit(); return false;">Add a BGP Autonomous System</a></li>
        <?php } ?>
        <?php if (getContainerLevel($_GET['container'], $_SESSION['MM_Username']) > 20) { ?>
        <li><a href="#" onClick="document.as_action.as_action_type.value = 'delete'; document.as_action.submit(); return false;">Delete the selected Autonomous System(s)</a></li>
        <?php } ?>
    </form>
    <?php } ?>
    <?php if ($_GET['browse'] == "reports") { ?>
    
    	<form name="frm_delete_report" id="frm_delete_report" action="" method="post" target="_self">
        	<input type="hidden" name="MM_delete" id="MM_delete" value="frm_delete_report" />
            <input type="hidden" name="report" value="<?php echo $_GET['report']; ?>" />
            <input type="hidden" name="container" value="<?php echo $_GET['container']; ?>" />
        </form>
        
    	<li><a href="#" onClick="if (confirm('Are you sure you want to delete this report?')) { document.frm_delete_report.submit(); } else { return false; }">Delete this report</a></li>
    
    <?php } ?>

<li><a href="?<?php echo $_SERVER['QUERY_STRING']; ?>&print=1"><img src="images/icon_print.gif" alt="Print page" border="0"> Print this page...</a></li>
<?php if ($containerLevel > 10) { ?><li><a href="#" onClick="document.getElementById('frm_providelink').submit()"><img src="images/plus_icon.gif" alt="Provide Link" border="0" align="absmiddle" /> <strong>Provide Link</strong></a></li><?php } ?>

</ul>

<span align="center"><p><em>Right-click anywhere above for more options</em></p></span> 

</div>

<?php } ?>
                   
</div>
</body>
</html>