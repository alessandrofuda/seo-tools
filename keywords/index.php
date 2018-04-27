<?php

/*  
*
* *** RESOURCES ***
* DOCUMENTZIONE API GOOGLE:
* https://support.google.com/googleapi/answer/7037264?hl=en&ref_topic=7013279
* LIBRERIA:
* https://console.developers.google.com/apis/library/webmasters.googleapis.com/?q=search&id=c3b816f7-b77b-4a5c-9731-a2950f0bbbb5&project=pr-1-api-monitoraggio-serp
* CREDENZIALI:
* https://console.developers.google.com/apis/credentials
*
* Tipo di account da usare: --> "Service Account" (Account di servizio) perchè chi fa la chiamata API NON è un utente,
* ma un'applicazione!. MA, Siccome l'applicazione viene installata su web -->viene usata anche OAuth2.0 
*
*
* Monitoraggio Serp Google utilizzando: 
*
* - Search Console API v.3
* - api key   (definita in config.php)  
*
* * Siccome si prendono dati dalla search console google l'app si deve autenticare e usare (NO: !l'auth OAuth 2.0!) Service Account  
* - php library ( https://github.com/google/google-api-php-client )
*
* - In Search Console Google: aggiunto nuovo user/proprietario: "account-di-servizio@pr-1-api-monitoraggio-serp.iam.gserviceaccount.com"
* 
*
* *** APP STRUCTURE ***
* - Authentication
* BACK-END:
* - da CRONTAB --> API call
* - STORE DATA IN DB
* FRONT-END:
* - call DATA from DB
* - HTML TABLE design (crf. scrape-serp)
*
*
*/


// Initialize the session // authentication middleware
session_start();
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
	$msg = urlencode('Login required !');
  	header("location: ../index.php?alertmsg=$msg");
  	exit;
}



require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/keywords/API-call.php';


$miosito_API_call = 'https://www.'.$miosito;

$apiCall = new SC_API_Call;
$keywords = $apiCall->gold_keywords($miosito_API_call,$to_date,$from_date);


echo '<h1>First 1.000 Gold Keywords List per Impressions</h1>';
echo '<h3>Range date: from <b>'. $from_date .'</b> to <b>' . $to_date . '</b></h3>';
echo '<p style="font-size:11px;">Source: Google Search Console API</p>';
echo '<table>';
echo '<thead style="text-align:left;"><th>N.</th><th>Keyword</th><th>Impressions</th></thead>';
echo '<tbody>';

$n = 0;
foreach ($keywords as $keyword) {
	echo '<tr><td>'.++$n.'</td><td>'.$keyword['keyword'].'</td><td>'.$keyword['impressions'].'</td></tr>';
}
echo '</tbody>';
echo '</table>';
?>






