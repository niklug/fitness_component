<?php
define('DC_MV_AJAX',"../admin/");
require_once "../admin/config.inc.php";

class DBConnection{
	function getConnection(){
	  //change to your database server/user name/password
		mysql_connect(DC_MYSQL_HOST,DC_MYSQL_USER,DC_MYSQL_PASSWORD) or
         die("Could not connect: " . mysql_error());
    //change to your database name
		mysql_select_db(DC_MY_DATABASE) or 
		     die("Could not select database: " . mysql_error());
	}
}
?>