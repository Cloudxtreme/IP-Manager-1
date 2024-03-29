<?php include('standard_functions.php');
session_start();

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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IP Manager: Executing scripts, please wait...</title>
<link href="../css/default.css" rel="stylesheet" type="text/css" />
<link href="../dropdowntabfiles/bluetabs.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php
ob_implicit_flush(true);

if (isset($_SESSION['script'])) {

	echo "<p>Executing script...</p>";
	
	$filename = generatePassword();
	
	$script = escapeshellcmd($_SESSION['script']);
	
	#passthru($script." >> ".$scriptpath."tmp/".$filename." 2>&1 &");

	passthru($script);
	
	#sleep(2);
	
	#$handle = fopen($scriptpath."tmp/".$filename, "r");
	#$line = '';
	#while ($line != "END") {
  	#	$line = fgets($handle);
  	#	echo $line;
  	#	ob_flush();
  	#	flush();
  	#	sleep(1);
  	#	$count++;
  	#	if ($count >= 30) {
  	#		$line = "END";
  	#	}
  		
	#}
	
	#fclose($handle);
	#unlink($scriptpath."tmp/".$filename);
	
	unset($_SESSION['script']);

}

?>
</body>
</html>