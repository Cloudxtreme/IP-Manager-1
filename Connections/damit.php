<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_damit = "localhost";
$database_damit = "damit";
$username_damit = "damit";
$password_damit = "damit";
$damit = mysql_pconnect($hostname_damit, $username_damit, $password_damit) or trigger_error(mysql_error(),E_USER_ERROR); 
?>