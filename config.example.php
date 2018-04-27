<?php 

/**
 *
 *  Commons config for All 4 applications
 *
 */

$miosito = ''; // !!! IMPORTANTE: NON inserire 'HTTPS://' nè 'WWW', slash ..
$admin_mail = '';

// db conn
define('DB_HOST', 'localhost');
define('DB_USER', '');
define('DB_PASSWORD', '');
// define('DB_NAME', vedi sotto);

// API google search console - client authentication with  Service Account - json credential private keys
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $_SERVER['DOCUMENT_ROOT'] . '.................');





/** 
*
* 1) Keywords List - fetch keywords list from API google search console
*
*/

// results number
// $rowLimit_keywords_list = null; //20; // null --> 1000 results

// period from --> to
$from_date = date('Y-m-d', strtotime("-1 month"));  // a month Ago
$to_date = date('Y-m-d');  // today




 
/**
 *
 *  2) Scrape Serp
 *
 */
// Number of more popular keywords  to scrape/monitoring
$keywords_number = 10;

// da sostituire con la lista estratta dala chiamata API google
/*
$keys = [
	'keywords1',
	'keywords2',
	'keywords3'
	];
*/

//$referrer_arr = [
	//
//	];

$userAgents_arr = [
			// My real User Agents (from: Ubuntu-Firefox & Ubuntu-Chrome)
			// 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:59.0) Gecko/20100101 Firefox/59.0',  // mio firefox
			// 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; WOW64; Trident/4.0; SLCC1)',  // mio chrome
			// 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/65.0.3325.181 Chrome/65.0.3325.181 Safari/537.36', // mio chromium
			// 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/605.1 (KHTML, like Gecko) Version/11.0 Safari/605.1 Ubuntu/16.04 (3.18.11-0ubuntu1) Epiphany/3.18.11', // mio epiphany
			// 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
			// 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.7 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.7',
			// 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11) AppleWebKit/601.1.56 (KHTML, like Gecko) Version/9.0 Safari/601.1.56',
			// 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36',
			// 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36',
			// 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0',
			// 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36',
			// 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36',
			// 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.71 Safari/537.36',
			// 'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
			// 'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
			// 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
			// 'Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
			// 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)',
			];


$custom = [
		//'cookies'		=> '';
		//'user_agent'	=> '';

		// Proxy  !
		// ip ruota ad ogni richiesta !!
		// 'proxy' 		=> 'http://proxy_service...',   // PROXY details with port
		// 'proxyuserpwd' 	=> '',    // Use if proxy have username and password
		];


// db connection
define('DB_NAME_1', '...');






/**
 *
 * 3) Scrape Keywords
 *
 */
// db connection
define('DB_NAME_2', '...');



/**
 *
 * 4) Links Monitoring
 *
 */
define('DB_NAME_3', '...');
// spostare lista nel DB
$externalPages = [  ... ... ];  // list of external pages on which monitoring links

