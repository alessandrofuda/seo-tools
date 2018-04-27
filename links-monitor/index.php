<?php 

/**
 *    What does it do?
 *
 *	- crontab: every thuesday in the morning
 *  - take links pages list --> from config.php file array
 *  - for every page:
 *  	- check if page is indexed by google
 *		- check if is there a noindex nofollow tag in header
 *  	- check if link TO mysite is present in this page (-->Regex)
 *		- check if link TO mysite is follow or nofollow 
 *		- send mail notification to admin mail
 */


require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/config.php';

?>
<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
		<meta name="robots" content="noindex,nofollow">
		<title>Links Monitoring Tool</title>
		<link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
		<style>
			body { font-size: 12px; }
			.header { text-align: center; margin-bottom: 20px;}
			table { width: 100%; }
			table, tr, td, th { border: 1px solid #CCCCCC;}
			th { background-color: #E6E6E6; }
			td, th { max-width:800px; }
			.url a { color: #454141; font-size: 90%; }
			footer { min-height: 50px; margin:30px auto; }
			#footer { padding:3% 0; text-align: center; color:#CCCCCC; line-height: 2;}
		</style>
		<script src="//code.jquery.com/jquery-3.3.1.slim.min.js"></script>
		<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
		<script>
			$(document).ready( function () {
	    		$('#link-monitor-table').DataTable( {
	    			"pageLength": 50
	    		} );
			});
		</script>
	</head>
	<body>
		<div class="header">
			<h1>Links Monitoring Tool</h1>
		</div>
		<table id="link-monitor-table" class="">
			<thead>
				<tr>
					<th>N.</th>
					<th>Monitoring date</th>
					<th>External pages url</th>
					<th>Page indexed from Google?</th>
					<th>Is there 'noindex/nofollow' tag in head section page?</th>
					<th>Link to <?php echo $miosito; ?> is present?</th>
					<th>Is link 'nofollow'?</th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach ($externalPages as $key => $externalPage) {
					echo '<tr>';
					echo '	<td>'.$key.'</td>';
					echo '	<td></td>';
					echo '	<td><div class="url"><a href="'.$externalPage.'" target="_blank">'.$externalPage.'</a></div></td>';
					echo '	<td></td>';
					echo '	<td></td>';
					echo '	<td></td>';
					echo '	<td></td>';
					echo '</tr>';
				}
			?>
			</tbody>
		</table>
		<?php include $_SERVER['DOCUMENT_ROOT'].'/seo-tools/footer.php' ?>
	</body>
</html>