<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_subman = "localhost";
$database_subman = "ipmanager";
$username_subman = "ipmanager";
$password_subman = "ipmanager";
$subman = mysql_pconnect($hostname_subman, $username_subman, $password_subman) or trigger_error(mysql_error(),E_USER_ERROR); 
?>