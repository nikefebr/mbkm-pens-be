<?php
	function createDatabaseConnection() {
		$db_user = "PA0013";
		$db_pass = "837979";
		$database_connection = oci_connect($db_user, $db_pass, "10.252.209.213/orcl.mis.pens.ac.id");
		if($database_connection) {
			return $database_connection;
		} else {
            die("Connection failed: " . $database_connection);
		}	
	}
?>