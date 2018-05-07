<?php 

// FRONT-END APPLICATION


// Initialize the session - authentication middleware
session_start();
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){ // If session variable is not set it will redirect to login page
	$msg = urlencode('Login required !');
  	header("location: ../index.php?alertmsg=$msg");
  	exit;
}



require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/scrape-serp/DB-class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/scrape-serp/functions.php';



//TEST
//$key = array('testing'); //, 'prova');
//$miosito = 'wikipedia.org';

?>
<!doctype html>
<html>
<head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<meta name="robots" content="noindex,nofollow">
	<title>Keywords Monitoring Tool</title>
	<!-- bootstrap 3 -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<style>
		body { font-size: 12px; }
		.header { text-align: center; margin-bottom: 20px;}
		.error { color:red;text-align:center; }
		.green { color: #38f000; }
		.orange { color: #ffcf00; }
		.red { color: red; }
		table { width: 100%; }
		table, tr, td, th { border: 1px solid #CCCCCC;}
		th { background-color: #4A4444; color:#DCDCDC;}
		td, th { max-width:800px; }
		/*th.sorting { min-width: 180px; } */
		.url a { color: #454141; font-size: 90%; }
		.posit { text-align: center; font-size: x-large;}
		.this-week { background-color: #EDEDED; }
		.sub-date { font-size: x-small; }
		.variaz { text-align: center; font-weight: bold; font-size: x-large;}
		.notes { max-width: 100px; font-size: 11px;}
		footer { min-height: 50px; margin:30px auto; }
		#footer { padding:3% 0; text-align: center; color:#CCCCCC; line-height: 2;}
	</style>

	<script src="//code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<!-- datatables-->
	<link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
	<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
	<script>
		$(document).ready( function () {
    		$('#monitor-table').DataTable( {
    			"pageLength": 50
    		} );
		});
	</script>

	<!-- tabledit --> 
	<script src="/vendor/jquery-tabledit/jquery.tabledit.min.js"></script>
	<script>
		$('#monitor-table').Tabledit({
		    url: 'update-db.php',
		    editButton: false,
		    deleteButton: false,
		    hideIdentifier: true,
		    columns: {
		        identifier: [0, 'id'],
		        editable: [[2, 'firstname'], [3, 'lastname']]
		    }
		});
	</script>
</head>
<body>
	<div class="header">
		<h1>Keywords Monitoring Tool<h1>
		<h4>Ciclo scrap settimanale (ogni lunedì h. 6:00)</h4>
		<div class="todo">
			<div class="todo-tit">TODO:</div>
			<ul>
				<li>confronto su settimana/mese</li>
				<li>add percentuale a variazione</li>
				<li>migliorare controlli scraping (nel matching nell'html)</li>
				<li>Collegare a API google: fetch from google API search console most popular $keys array </li>
				<li>individuare dal blog le "url da spingere"</li>
			</ul>
		</div>
	</div>
	<table id="monitor-table">
		<thead>
			<tr style='text-align: center;'>
				<th><b>N.</b></th>
				<th><b>Key</b></th>
				<?php /* <th><b>CPM ads</b></th> */ ?>
				<th><b>Url da spingere</b></th>
				<th><b>Url posizionata</b></th>
				<th><b>Position Sett Scorsa</b></th>
				<th><b>Position Sett In Corso</b></th>
				<th><b>Variazione</b></th>
				<th><b>Note</b></th>
			</tr>
		</thead>
		<tbody>
<?php 
	
	//conn db
	$db = new DB;
	$conn = $db->connect();	

	$query = "SELECT DISTINCT `keyw` FROM `wp_analisi_serp`";  
	$result = $conn->query($query);
	$keysss = $result->fetch_all();
	foreach ($keysss as $key) {
		$keys[] = $key[0];
	}

	
	// echo '<pre>';
	// print_r($keys);
	// echo '</pre>';



	// assoc array of target urls for each keywords
	$target_url = get_target_url_per_keyword($conn);
	echo '<pre>';
	var_dump($target_url);
	echo '</pre>';


		
	// tabella HTML
	$row = 0;
	foreach ($keys as $key) {  // // tabella HTML  -- loop 2 html
			
		//variabili tabella
		$key = trim($key);
		$p_sett_scorsa = pos_sett_scorsa($conn,$key); //array
		$p_sett_in_corso = pos_sett_in_corso($conn,$key); //array // finire!!!
		//echo '<pre>'; var_dump($p_sett_in_corso); echo '</pre>';
		
		$p_sett_sc = intval($p_sett_scorsa['position']); // integer
		$date_sett_sc = $p_sett_scorsa['mysql_date'];
		


		/// FINIRE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


		
		// $keyw_id = 
		//$url_da_spingere = $target_url[$key];

		$test = array_filter($target_url, function($value){ return ($value == $key); });
		echo '<pre>';
		var_dump($test);
		echo '</pre>';

		
		// $url_da_spingere = ...... $target_url target_url
		



		$url = $p_sett_in_corso['url'];
		// $p = intval($p_sett_in_corso['position']);
		$p = empty($url) ? 'N.D.' : intval($p_sett_in_corso['position']);
		$giorno = $p_sett_in_corso['mysql_date'];
		$annotaz = $p_sett_in_corso['note'];







		//TEST
		// $p_sett_sc = 10;








		if(is_numeric($p_sett_sc) && $p_sett_sc > 0 ) {

			$variaz_perc = -(($p - $p_sett_sc) / $p_sett_sc ) * 100;
			$variaz_perc = round( $variaz_perc, 0 );

			if ($variaz_perc == 0) {
				$variaz_perc_str = $variaz_perc;
			} elseif ($variaz_perc > 0) {
				$variaz_perc_str = '+'.$variaz_perc.'%';
			} else {
				$variaz_perc_str = $variaz_perc.'%';
			}

		} else {

			$variaz_perc_str = 'N.A.';
		}

		
		
		//colors	
		if($p == 0) { $color = 'red'; }
		if($p >= 1 && $p <= 3)  { $color = 'green'; }
		if($p >= 4 && $p <= 6)  { $color = 'orange'; } 
		if($p >= 7 /*&& $p <= 10*/) { $color = 'red'; }
		
		//if($p_sett_scorsa == 0) { $color = 'red'; }
		//if($p_sett_scorsa >= 1 && $p_sett_scorsa <= 3)  { $color = 'green'; }
		//if($p_sett_scorsa >= 4 && $p_sett_scorsa <= 6)  { $color = 'orange'; } 
		//if($p_sett_scorsa >= 7 && $p_sett_scorsa <= 10) { $color = 'red'; }
		
		
		if($p < $p_sett_sc && $p != 0) 	  { $color_var = 'green'; }
		if($p > $p_sett_sc && $p != 0) 	  { $color_var = 'red'; }
		if($p_sett_sc > 0 && !is_numeric($p) ) { $variaz_perc_str = '!'; $color_var = 'red';}
		if($p === $p_sett_sc ) 			  { $color_var = 'orange'; }
		



		//tabella
	   	echo "<tr id='".$id."'>";
	   	echo 	"<td>" . (++$row) . "</td>";
	   	echo 	"<td>" . $key . "</td>"; 
	   	// echo "<td>" . " " . "</td>";
	   	echo 	"<td>";
	   	echo 		"<div class='url to-be-positioned'><a href='https://".$url_da_spingere."' target='blank'>".$url_da_spingere."</a></div>"; ?>




	   	<div class='btn-section'>
	   		<a href="#" id="edit-<?php echo $row; ?>" data-type="text" data-pk="" data-url="/post" data-title="Edit url">Edit</a>
	   	</div>
	   	





<?php 	echo 	"</td>";
	   	echo 	"<td><div class='url'><a href='https://".$url."' target='blank'>".$url."</a></div></td>";
	   	echo 	"<td class='posit'><div class='p-num'>" . $p_sett_sc ."</div><div class='sub-date'>". data_ita($date_sett_sc) ."</div>"."</td>"; //sett scorsa (lunedì)
	   	echo 	"<td class='posit this-week $color'><div class='p-num'>" . $p . "</div><div class='sub-date'>". data_ita($giorno) ."</div></td>"; 
	   	echo 	"<td class='variaz $color_var'>".$variaz_perc_str ."</td>";
	   	echo 	"<td class='notes'>" . $annotaz . "</td>";
	   	echo "</tr>";
   	
	} 

	$conn->close();


?>
			</tbody>
		</table>
		<?php include $_SERVER['DOCUMENT_ROOT'].'/seo-tools/footer.php' ?>
	</body>
</html>
