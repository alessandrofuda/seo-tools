<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/scrape-serp/DB-class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/scrape-serp/functions.php';



// target_url insert/update into 'target_url_per_keywords' table
if(isset($_POST['keyword']) && isset($_POST['target_url'])) {

	//conn db
	$db = new DB;
	$conn = $db->connect();	

	// var_dump($_POST['keyword']);
	// var_dump($_POST['target_url']);



	$keyw =  $_POST['keyword'] !== null ? $_POST['keyword'] : null;
	$url_da_spingere = $_POST['target_url'] !== null ? $_POST['target_url'] : null;


	
	$setted = set_target_url_per_keyword($conn, $keyw, $url_da_spingere);  // TRUE/null

	if($setted !== TRUE) {
		die('Error from \'set_target_url_per_keyword()\' function in '. __FILE__ );
	}


	echo $url_da_spingere;
	
	return;

} 




