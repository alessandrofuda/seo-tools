<?php 


class scrapeSerp {

	
	// properties
	public $key;
	public $miosito;
	public $user_agent;
	public $custom;
	public $html;
	public $html_array;
	public $url_position;
	public $note = null;
		


	// methods
	public function getContent($key, $user_agent, $custom = []) {



		// ********** !!!!!!!! PROBLEMA CON INDIRIZZO IP OLANDESE !!!!!!!!!!! effettua le ricerche da Google NEDERLANDS !!!!!!!!! *******************
		// ********** !!!!!!!! OGNI INDIRIZZO IP È GEOLOCALIZZATO !!!!!!!!!!! ***************
		// ***** su keywords in lingua italiana sembra funzionare correttamente !!? [ verificare ] 


		// simula 4 tipi di ricerca: 
		// 0- ricerca da barra indirizzi firefox ubuntu
		// 1- navigazione in incognito, ricerca digitata da barra indirizzi firefox
		// 2- come sopra, ma da chrome
		// 3- come sopra ma da navigazione in incognito
		$search_url = [
			'https://www.google.it/search?client=ubuntu&hl=it&channel=fs&q='. urlencode($key) . '&ie=utf-8&oe=utf-8&gws_rd=cr&num=30', //&num=30 --> 30 res per pag
			// 'https://www.google.it/search?client=ubuntu&hl=it&channel=fs&q='.urlencode($key).'&ie=utf-8&oe=utf-8&gfe_rd=cr&dcr=0&ei=5hS-WqvnCM_BXubzopgL&num=30',
			// 'https://www.google.it/search?q='.urlencode($key).'&hl=it&oq='.urlencode($key).'&aqs=chrome..69i57j0l5.1267j0j7&sourceid=chrome&ie=UTF-8&num=30',
			// 'https://www.google.it/search?q='.urlencode($key).'&hl=it&oq='.urlencode($key).'&aqs=chrome..69i57.1373j0j1&sourceid=chrome&ie=UTF-8&num=30',
				];
		$search_url = $search_url[0];   // rand(0,3)];

		// TESTING..
		// $search_url = 'https://informatic-solutions.it/';
		// $search_url = 'https://lumtest.com/myip.json';


		// scraping with curl
		$options = [
			CURLOPT_RETURNTRANSFER => true, 	// return web page
			CURLOPT_HEADER         => true, 	//return headers in addition to content
			CURLOPT_FOLLOWLOCATION => true, 	// follow redirects
			CURLOPT_ENCODING       => "", 		// handle all encodings
			CURLOPT_AUTOREFERER    => true, 	// set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120, 		// timeout on connect
			CURLOPT_TIMEOUT        => 120, 		// timeout on response
			CURLOPT_MAXREDIRS      => 10, 		// stop after 10 redirects
			CURLINFO_HEADER_OUT    => true,
			CURLOPT_SSL_VERIFYPEER => false, 	// Disabled SSL Cert checks
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			// CURLOPT_COOKIE         => (array_key_exists('cookies', $custom) ? $custom['cookies'] : null),
			// CURLOPT_USERAGENT      => (array_key_exists('user_agent', $custom) ? $custom['user_agent'] : $user_agent[ array_rand($user_agent) ]),
			// CURLOPT_REFERER 	      => '-',	
			
			// proxy !
			// CURLOPT_PROXY	   	   => (array_key_exists('proxy', $custom) ? $custom['proxy'] : null),  
			// CURLOPT_PROXYUSERPWD   => (array_key_exists('proxyuserpwd', $custom) ? $custom['proxyuserpwd'] : null),   
			// CURLOPT_PROXYTYPE      => CURLPROXY_SOCKS5,  // If expected to call with specific PROXY type
		];



		$ch = curl_init( $search_url );
		curl_setopt_array( $ch, $options );
		$rough_content = curl_exec($ch);
		$err = curl_errno( $ch );
		$errmsg = curl_error( $ch );
		$header = curl_getinfo( $ch );
		curl_close( $ch );


		$header_content = substr( $rough_content, 0, $header['header_size'] );
		$body_content = trim( str_replace( $header_content, '', $rough_content ) );
		preg_match_all( "#Set-Cookie:\\s+(?<cookie>[^=]+=[^;]+)#m", $header_content, $matches );
		$cookiesOut = implode( "; ", $matches['cookie'] );


		$header['errno'] 	= $err;
		$header['errmsg'] 	= $errmsg;
		$header['headers'] 	= $header_content;
		$header['content'] 	= $body_content;
		$header['cookies'] 	= $cookiesOut;
		
		$html = $header['content'];
		
		return $html;
		
	}



	public function convers_blocco_in_array($html) {
		//trasformo in array le espressioni che si ripetono nell'elenco <ol> split() o explode() o  preg_split() e poi count
		$html_array = explode('<h3 class="r"><a href="/url?q=', $html);  //<div class="g">
		return $html_array; //array
	}



	public function get_string_between($string, $start, $end) {
    	
    	$string = ' ' . $string;
    	$ini = strpos($string, $start);
    	
    	if ($ini == 0) return '';
    	
    	$ini += strlen($start);
    	$len = strpos($string, $end, $ini) - $ini;
    	
    	return substr($string, $ini, $len);
	}


	public function estraz_position($miosito, $html_array) { 
		//prendo l'array e restituisco UN ALTRO ARRAY con valori che contengono $miosito
		$sottoarray = preg_grep('/^.*'.$miosito.'.*/', $html_array);
		//echo '<pre>'; var_dump($sottoarray); echo '</pre>';
		
		reset($sottoarray); //prende solo il primo elemento dell'array (utile se ci sono più occorrenze)
		
		$first_position_in_serp = key($sottoarray); //prende la key del solo primo elemento
		//echo '$position_in_serp: '; echo '<pre>'; var_dump($position_in_serp); echo '</pre>';
		
		
		// prendo il nuovo array e PER OGNI OCCORRENZA isolo la url completa ($url) che contiene $miosito
		foreach($sottoarray as $chiave => $value){ 			
			$first_url = $this->get_string_between( $value, 'https://','&' );		 
		} //fine foreach
		//echo '$first_url: '; echo '<pre>'; var_dump($first_url); echo '</pre>';
		
		//prendo il nuovo array e restituisco UN ALTRO ARRAY che contiene la/e sola/e CHIAVE/I che contengono il mio sito
		//la CHIAVE coincide con la posizione in Serp di $miosito
		
		return array('url' => $first_url, 'position' => $first_position_in_serp); //restituisce un array con una o più occorrenze. Ogni occorrenza contiene un ulteriore array !!!
	} 



	public function update_db($key, $url_position = null, $note= null) { //open_conn-->check-->insert_new_rec-->clean_olds-->close conn
		
		
		require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/scrape-serp/DB-class.php';


		$db = new DB;
		$conn = $db->connect();
		

		// Check db. Record già inserito OGGI?
		$today = date('d/m/Y');
		$sql0 = "select id from wp_analisi_serp where `keyw` = '$key' and `date` LIKE '%$today%'";
		$sql1 = $conn->query($sql0);
		if ($sql1 === false) {
			die("Errore su recupero key: " . $conn->error); 
			}
			
		$n = $sql1->num_rows;
		if($n > 0) { //insert solo se il record non è già presente con la data di oggi
			echo 'Tabella db già aggiornata oggi, nessun ulteriore aggiornamento necessario<br>';
			$conn->close();
			return; 			//  !!! IMPORTANTE !!! evita sovracc. tabella db --> esce da funzione, non aggiorna tab.
		}
		
		//aggiorna tab. Inserisce nuovo record solo SE NON già inserito OGGI !!
		$date = date('D d/m/Y H:i:s', time()+3600); //ora solare--> UTC+1h
		$url = $url_position['url'];
		$position  = $url_position['position'];
		if($position == null ) { $position = 0; }
		
		$sql = "INSERT INTO wp_analisi_serp (`keyw`,`date`,`url`,`position`,`note`) VALUES ('$key', '$date', '$url', '$position', '$note' )";

		if ($conn->query($sql) === FALSE) {
			die("Errore inserimento nuovo record in tab. Query: " . $sql . "  " . $conn->error . "<br>");
		} else {
			echo "Ok: new record inserted in Db. key: $key, date: $date<br>";
		}

		
		//pulisce tutti i record antecedenti l'Ultimo Mese
		$delete_old_records = $conn->query("DELETE FROM `wp_analisi_serp` where `mysql_date` < DATE_SUB(NOW(), INTERVAL 1 MONTH)");	
		if ($delete_old_records === FALSE) {
			die("Errore su cancellazione old records da tab db: " . $conn->error); 
		} else { 
			echo "Ok: clean old records in db (before last month)<br>"; 
		}
		

		//chiudo connessione
		$conn->close();

	} // fine update_db() 




}




