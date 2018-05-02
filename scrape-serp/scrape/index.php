<?php 

/** 
* SOLO PER UTENTE LOGGATO !!
* Scraping per monitoraggio Serp Google
* schedulare da crontab 
* Mon h.04.00
*
*	php -f /var/www/alessandrofuda.it/html/seo-tools/scrape-serp/scrape/index.php [per ora accesso solo con login]
**/


// if request is NOT from CLI, but from WEB
if( php_sapi_name() !== 'cli'){
	// Initialize the session - authentication middleware
	session_start();
	if(!isset($_SESSION['username']) || empty($_SESSION['username'])) { // If session variable is not set it will redirect to login page
		$msg = urlencode('Login required !');
	  	header("location: ../index.php?alertmsg=$msg");
		exit;
	}
} else {
	// IMPORTANT!: se lo script è lanciato direttamente da CRONTAB / CLI la variabile globale $_SERVER['DOCUMENT_ROOT'] NON viene settata 
	// perchè la request NON passa per il web server !!
	$_SERVER['DOCUMENT_ROOT'] = __DIR__.'/../../..';

}



// istanzia la classe scrapeSerp e inserisce dati in DB
require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/scrape-serp/scrape/class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/keywords/API-call.php';


$apiCall_for_scrape = new SC_API_Call;
$miosito_API_call = 'https://www.'.$miosito;
$keywords = $apiCall_for_scrape->gold_keywords($miosito_API_call,$to_date,$from_date);
$keywords = array_slice($keywords, 0, $keywords_number);  // select from search console only firsts X keywords
foreach ($keywords as $keyword) {
	$keys[] = $keyword['keyword'];
}


// echo '<pre>';
// print_r($keys);
// echo '</pre>';
// die('stop');



// TEST
//$miosito = 'moto.it';
//$keys = ['prova','prova moto'];




// controlli
if(!$keys || $keys == null || $keys == '' || $keys == [] ) {  //controllo 1
	die('Errore: nessuna keyword trovata');
}

if(!$miosito || $miosito == null || $miosito == '') {  //controllo 2
	die('Errore: definire il sito da analizzare');
}


// istantiate class
$serp = new scrapeSerp;

foreach ($keys as $key) {

	// 1)
	$html = $serp->getContent($key, $userAgents_arr, $custom);

	if(!$html || $html == null || $html == '' || $html == false) {  //controllo 3
		$mioip = $_SERVER['REMOTE_ADDR'];
		die("Errore: problema con l'estrazione della pagina da G. Problema con la funzione file_get_contents(). Controllare se G. blocca le richieste da questo IP ( $mioip )");
	}


	// 2)
	$html_array = $serp->convers_blocco_in_array($html); 
	//echo '<pre>'; var_dump($array); echo '</pre>';
			
	if(!$html_array || $html_array == null || $html_array == '' || $html_array == false) {   //controllo 4
		die("Errore: problema con la funzione convers_blocco_in_array(), verificare l'aggancio html");
	}


	// 3)
	$url_position = $serp->estraz_position($miosito, $html_array); //  array inside array
	//echo '$url_position: '; echo '<pre>'; var_dump($url_position); echo '</pre>';
			
	if(!$url_position || $url_position == null || $url_position == '' || $url_position == false) {  //controllo 6
		die("Errore: problema con la funzione estraz_position() o preg_grep() o get_string_between().<br/>Verificare anche l'aggancio nel parsing dell'html.");
	}


	// 4) 
	$update_db = $serp->update_db($key, $url_position, $note = null); 


	

	// 5)
	if(count($keys) > 1) {
		$s = rand(50,240);
		set_time_limit($s + 30);
		sleep($s);
	}

}



// ***************************  INVIO ALERT MAIL  ******************************* 
	
	// $day = date('D');
	// $current_time = date('H:i:s'); //date('H:i:s');
	// $start_time = date('06:45:00');
	// $end_time = date('11:15:00');
	
	//if( $day == 'Mon' && $current_time >= $start_time && $current_time <= $end_time ) { // invio mail	
		//if( max($max_position) >= 4 || in_array(null, $max_position, true) ) {
   	
   	$subject = 'Monitoraggio search Google ';
   	$subject .= date('D d/m/Y - H:i:s');
   	$message = '<p>Report posizionamento key:'.'<br/>';
   	$message .= '<a href="https://alessandrofuda.it/seo-tools/scrape-serp">https://alessandrofuda.it/seo-tools/scrape-serp</a>'.'</p>';
	$message .= '<p>'.'(Monitoraggio schedulato da crontab)<br/>'.'</p>';
   	$mail_headers  = 'MIME-Version: 1.0'."\r\n";
	$mail_headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
	$mail_headers .= 'From: serp-monitoring@alessandrofuda.it'."\r\n";
	$mail_headers .= 'Reply-to: '.$admin_mail."\r\n";
	
	mail($admin_mail, $subject, $message, $mail_headers); 
	 	
   		//} // fine if max() 
    //} // fine if $day & $time
   
// *****************************  FINE BLOCCO MAIL  *********************************  





