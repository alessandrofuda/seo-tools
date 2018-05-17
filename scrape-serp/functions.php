<?php 

function pos_mese_scorso() {

	//
	
}

function pos_sett_scorsa($conn,$key) {
	$week_ago = "SELECT `mysql_date`, `keyw`, `position` FROM `wp_analisi_serp` WHERE `keyw` = '$key' and `mysql_date` <= DATE_SUB(NOW(), INTERVAL 6 DAY) ORDER BY `mysql_date` DESC LIMIT 1";  // prende solo la data più recente a partire da sett passata!
	$query = $conn->query($week_ago);
	$p_sett_scorsa = $query->fetch_assoc();
	//echo '<pre>'; var_dump($p_sett_scorsa); echo '</pre><br/>';
	return $p_sett_scorsa; //array
}



function pos_sett_in_corso($conn,$key){
	$this_week = "SELECT `mysql_date`, `keyw`, `url_da_spingere`, `url`, `position`, `note` FROM `wp_analisi_serp` WHERE `keyw` = '$key' ORDER BY `mysql_date` DESC LIMIT 1";  // prende solo la data più recente!
	$query = $conn->query($this_week);
	$p_this_week = $query->fetch_assoc();
	//echo '<pre>'; var_dump($p_this_week); echo '</pre><br/>';
	return $p_this_week; //array
}



function data_ita($data_standard) {  // data standard: '2016-02-01 15:00:00'
	if($data_standard != null) {
		$data = explode(' ', $data_standard);
		$data_array = explode('-', $data[0]);
		return $data_array[2] .'/'. $data_array[1] .'/'. $data_array[0] .'<br/>'. $data[1];
	} else {
		return '';
		}

}




function get_target_url_per_keyword($conn) {  


	$target = "SELECT `id`,`keyword`,`target_url` FROM `target_url_per_keywords`";
	$query = $conn->query($target);
	
	while ($row = $query->fetch_assoc()) {
		$arrays[] = $row;
	}

	//echo '<pre>';
	//var_dump($arrays);
	//echo '</pre>';	

	return $arrays;

}









function set_target_url_per_keyword($conn,$key,$target_url) {
	
	// escape data
	$key = mysqli_real_escape_string($conn, trim($key));
	$target_url = mysqli_real_escape_string($conn, trim($target_url));

	// truncate too long strings - fix max lenght
	$key = mb_strimwidth($key, 0, 70,'...');  
	$target_url = mb_strimwidth($target_url, 0, 130, '...');
	

	// verify if $key yet exixst in table
	$k = "SELECT `keyword` FROM `target_url_per_keywords` WHERE `keyword` LIKE '$key'";
	$query = $conn->query($k);

	if($query->num_rows < 1) {

		// insert new
		$insert = "INSERT INTO `target_url_per_keywords` (`keyword`, `target_url`) VALUES ('$key', '$target_url')";
		$inserted = $conn->query($insert);
		
		if ($inserted === FALSE) {
			die('Error while new insert into DB. ('. __FILE__ . ')');
		}

	} else {

		// update existent row
		$update = "UPDATE `target_url_per_keywords` SET `target_url` = '$target_url' WHERE `keyword` LIKE '$key'";
		$updated = $conn->query($update);

		if ($updated === FALSE) {
			die('Error while updating into DB. ('. __FILE__ . ')');
		}

	} 


	return TRUE;
	

}
