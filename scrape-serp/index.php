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


//conn db
$db = new DB;
$conn = $db->connect();	

$query = "SELECT DISTINCT `keyw` FROM `wp_analisi_serp`";  
$result = $conn->query($query);
$keysss = $result->fetch_all();
foreach ($keysss as $key) {
	$keys[] = $key[0];
}


// array of assoc array of target urls for each keywords
$target_urls = get_target_url_per_keyword($conn);




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
		.target_url { display: inline-block; }
		.edit-btn { display: inline-block; }
		#monitor-table td .glyphicon { margin-left: 10px; font-size: 110%; border: 1px solid #DCDCDC; padding: 2px; border-radius: 3px; }
		.posit { text-align: center; font-size: x-large;}
		.this-week { background-color: #EDEDED; }
		.sub-date { font-size: x-small; }
		.variaz { text-align: center; font-weight: bold; font-size: x-large;}
		.notes { max-width: 100px; font-size: 11px;}
		footer { min-height: 50px; margin:30px auto; }
		#footer { padding:3% 0; text-align: center; color:#CCCCCC; line-height: 2;}
	</style>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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
</head>
<body>
	<div class="header">
		<h1>Keywords Monitoring Tool<h1>
		<h4>Ciclo scrap settimanale (ogni lunedì h. 6:00)</h4>
	</div>
	<table id="monitor-table">
		<thead>
			<tr style='text-align: center;'>
				<th><b>N.</b></th>
				<th><b>Key</b></th>
				<!--th><b>CPM ads</b></th-->
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


	// tabella HTML
	$row = 0;
	$n = 0;
	foreach ($keys as $key) {  // // tabella HTML  -- loop 2 html
		
		$row++;
		$target_url = array();

		//variabili tabella
		$key = trim($key);
		$p_sett_scorsa = pos_sett_scorsa($conn,$key); //array
		$p_sett_in_corso = pos_sett_in_corso($conn,$key); //array 
		//echo '<pre>'; var_dump($p_sett_in_corso); echo '</pre>';
		$p_sett_sc = intval($p_sett_scorsa['position']); // integer
		$date_sett_sc = $p_sett_scorsa['mysql_date'];

		
		$target_url = array_filter($target_urls, function($value) use ($key) {  // IMPORTANT !! filtra l'array MA NON cambia la index !!!!!!!!!!!!!!

			return strtolower($value['keyword']) == strtolower($key); // RENDERE CASE INSENSITIVE
		});

		
		$target_url = array_values($target_url); //[0];  // IMPORTANT !! get FIRST element of array tough the first element has key = 1 || 2 || 3 || ...
		$target_url = is_array($target_url) ? $target_url[0] : null;
		

		$url = $p_sett_in_corso['url'];
		// $p = intval($p_sett_in_corso['position']);
		$p = empty($url) ? 'N.D.' : intval($p_sett_in_corso['position']);
		$giorno = $p_sett_in_corso['mysql_date'];
		$annotaz = $p_sett_in_corso['note'];


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
		
		
		if($p < $p_sett_sc && $p != 0) 	  { $color_var = 'green'; }
		if($p > $p_sett_sc && $p != 0) 	  { $color_var = 'red'; }
		if($p_sett_sc > 0 && !is_numeric($p) ) { $variaz_perc_str = '!'; $color_var = 'red';}
		if($p === $p_sett_sc ) 			  { $color_var = 'orange'; }
		


		//tabella
		$target_url_id = $target_url['id'] !== null ? $target_url['id'] : '';   
?>
	   	
	   	<tr id='row-<?php echo $row; /* $target_url_id; */ ?>'>   
	   		<td><?php echo $row; ?></td>
	   		<td class="key"><?php echo $key; ?></td> 
	   		<td>
	   			<div class='url target_url'>
	   				<a href='<?php echo $target_url['target_url']; ?>' target='blank'><?php echo $target_url['target_url']; ?></a>
	   			</div>
	   			<div id='edit-btn-<?php echo $row; ?>' class='edit-btn'>
	   				<a href='#'><span class='glyphicon glyphicon-pencil'></span></a>
	   			</div>
	   			<form id='form-<?php echo $row; ?>' class="input_url" method="post">
	   				<input type="text" id='target_url-<?php echo $row; ?>' name='target_url-<?php echo $row; ?>' placeholder="Url.." value='<?php echo $target_url['target_url']; ?>'/>
	   			</form>
	   			<div id='cancel-btn-<?php echo $row; ?>'>
	   				<a href="#">Annulla</a>
	   			</div>
 			</td>
			<td>
				<div class='url'><a href='https://<?php echo $url; ?>' target='blank'><?php echo $url; ?></a></div>
			</td>
			<td class='posit'>
				<div class='p-num'><?php echo $p_sett_sc; ?></div>
				<div class='sub-date'><?php echo data_ita($date_sett_sc); ?></div>
			</td>
			<td class='posit this-week <?php echo $color; ?>'>
				<div class='p-num'><?php echo $p; ?></div>
				<div class='sub-date'><?php echo data_ita($giorno); ?></div>
			</td> 
			<td class='variaz <?php echo $color_var; ?>'><?php echo $variaz_perc_str; ?></td>
			<td class='notes'><?php echo $annotaz; ?></td>
		</tr>
		<script>
			$(document).ready(function(){

				// default
				$('#form-<?php echo $row; ?>').css('display','none'); 
				$('#cancel-btn-<?php echo $row; ?>').css('display','none'); 


				// view/hide edit/cancel btns
				$('#edit-btn-<?php echo $row; ?>').on('click', function(e) {
					e.preventDefault();
					$('#form-<?php echo $row; ?>').css('display','block'); 
					$('#edit-btn-<?php echo $row; ?>').css('display','none');
					$('#cancel-btn-<?php echo $row; ?>').css('display','block');
				});
				$('#cancel-btn-<?php echo $row; ?>').on('click', function(e) {
					e.preventDefault();
					$('#form-<?php echo $row; ?>').css('display','none');
					$('#edit-btn-<?php echo $row; ?>').css('display','inline-block');
					$('#cancel-btn-<?php echo $row; ?>').css('display','none');
				});

				// ajax call
				$('#form-<?php echo $row; ?> input').keypress(function(e) {
				    if(e.which == 13) {

				    	e.preventDefault();
				    	var keyword_<?php echo $row; ?> = $('#row-<?php echo $row; ?> .key').text().trim(); 
						var target_url_<?php echo $row; ?> = $('#target_url-<?php echo $row; ?>').val();
				        
				        $.ajax({
		                    url: 'ajax.php',  
		                    method:'POST',
		                    data:{
		                        keyword:keyword_<?php echo $row; ?>,
		                        target_url:target_url_<?php echo $row; ?>,
		                    },
		                   	success:function(target_url) {
		                    	// hide input form & cancel btn
		                    	$('#form-<?php echo $row; ?>').css('display','none'); 
								$('#cancel-btn-<?php echo $row; ?>').css('display','none'); 
		                    	// show && overwrite old text with newer
		                    	$('#row-<?php echo $row; ?> .target_url > a').attr('href', target_url).text(target_url);
		                    	// show edit-btn
		                    	$('#edit-btn-<?php echo $row; ?>').css('display','inline-block');
		                   	}
		                });
				    }
				});

			});
			
		</script>



<?php   	
	} // fine loop foreach

	$conn->close();


?>
			</tbody>
		</table>
	<?php include $_SERVER['DOCUMENT_ROOT'].'/seo-tools/footer.php'; ?>
	</body>
</html>
