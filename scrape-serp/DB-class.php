<?php 


class DB {

	
	public function connect() {
		

		require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/config.php';

		
		$connect_db = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME_1 );
		
		if ( mysqli_connect_errno() ) {
			printf("Connection failed: %s ", mysqli_connect_error());
			exit();
		}
		return $connect_db;
		
	}

	


}

