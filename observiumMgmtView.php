<?php require_once('Connections/subman.php'); ?>
<?php include('includes/standard_functions.php'); ?>
<?php include('includes/ipm_class.php');

$ipm = new ipm;

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

$pageLevel = $ipm->getPageLevel(7,$_SESSION['MM_Username']);
?>
<?php
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO observium (`host`, descr, username, pwd, dbname) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['host'], "text"),
                       GetSQLValueString($_POST['descr'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['pwd'], "text"),
                       GetSQLValueString($_POST['dbname'], "text"));

  mysql_select_db($database_subman, $subman);
  $Result1 = mysql_query($insertSQL, $subman) or die(mysql_error());

  $insertGoTo = "observiumMgmtView.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_subman, $subman);
$query_observiums = "SELECT * FROM `observium` ORDER BY `observium`.descr";
$observiums = mysql_query($query_observiums, $subman) or die(mysql_error());
$row_observiums = mysql_fetch_assoc($observiums);
$totalRows_observiums = mysql_num_rows($observiums);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IP Manager</title>
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

<script language="JavaScript" type="text/JavaScript">
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
//-->
</script>
</head>

<body>

<div class="rightclick">
<div class="banner">
  <img src="images/ipm_banner.gif" alt="IPM Home" name="ipm_banner" width="296" height="59" id="ipm_banner" />
</div>

<div class="ipm_body">

<div class="ipm_nav">
<!-- Sample menu definition -->
<div id="myslidemenu" class="jqueryslidemenu" style="z-index:10000">

<ul>
	<?php include('includes/standard_nav.php'); ?>
	<li><a class="NOLINK"><img src="images/observium_icon.gif" alt="Observium Servers" align="absmiddle" width="20" height="20" border="0" /> Observium Servers</a>
   	  <?php if ($totalRows_observiums > 0) { // Show if recordset not empty ?>
        	  <ul>
        	    <?php do { ?>
       	        <?php if (getObserviumLevel($row_observiums['id'],$_SESSION['MM_Username']) > 0 || ($ipm->getPageLevel(6,$_SESSION['MM_Username']) > 0 && getObserviumLevel($row_observiums['id'],$_SESSION['MM_Username']) == "")) { ?>      
       	        <li><a href="?browse=observiums&amp;observium=<?php echo $row_observiums['id']; ?>" title="<?php echo $row_observiums['host']; ?>"><?php echo $row_observiums['descr']; ?></a></li>
       	        <?php } ?>
       	        <?php } while ($row_observiums = mysql_fetch_assoc($observiums)); ?>
</ul>
       	    <?php } // Show if recordset not empty ?>
</li>
    <?php include('includes/standard_nav_footer.php'); ?>
</ul>
</div>
</div>

<div class="rightclick">

<?php if ($pageLevel < 127) { ?>
<p class="text_red">Error: You are not authorised to view the selected content.</p>
<?php 
	exit();
} ?>

<div id="containerView">
  <div id="containerView_body">
  <?php if ($_GET['browse'] == "observiums") {
        ?>
        
        <p>This functionality is not yet available.  Observium integration will be enabled in a future release.</p>
        
        <?php 
        
		if ($_POST['action'] == "add_observium") { ?>
		
    <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" onsubmit="MM_validateForm('host','','R','username','','R','pwd','','R','dbname','','R','descr','','R');return document.MM_returnValue">
      <table>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Host IP Address:</td>
          <td><input name="host" type="text" class="input_standard" id="host" value="" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right" valign="top">Description:</td>
          <td><textarea name="descr" cols="32" rows="5" class="input_standard" id="descr"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Username:</td>
          <td><input name="username" type="text" class="input_standard" id="username" value="" size="32" maxlength="255" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Password:</td>
          <td><input name="pwd" type="password" class="input_standard" id="pwd" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">DB Name:</td>
          <td><input name="dbname" type="text" class="input_standard" id="dbname" value="" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input type="submit" class="input_standard" value="Add Observium server" /></td>
        </tr>
      </table>
      <input type="hidden" name="MM_insert" value="form1" />
    </form>
    <p>&nbsp;</p>
    
<?php } ?>

  <?php } ?>

</div>

<div>


<!--HTML for Context Menu 1-->
<ul id="contextmenu1" class="jqcontextmenu">

  <?php if ($_GET['browse'] == "observiums") { ?>
    	<form action="observiumMgmtView.php?browse=observiums&observium=<?php echo $_GET['observium']; ?>" method="post" name="frm_observium_action" target="_self" id="frm_observium_action">
        	<input type="hidden" name="action" id="action">
        <li><a href="#" onClick="document.getElementById('action').value = 'add_observium'; document.getElementById('frm_observium_action').submit(); return false;">Add an Observium Server</a></li>
        </form>
    <?php } ?>
    
    </ul>

</div>
</div>
</div>

</body>
</html>
