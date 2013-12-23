<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_radiator = "localhost";
$database_radiator = "authdb";
$username_radiator = "radiator";
$password_radiator = "radiator";
$radiator = mysql_pconnect($hostname_radiator, $username_radiator, $password_radiator) or trigger_error(mysql_error(),E_USER_ERROR); 
?>