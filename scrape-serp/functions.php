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
