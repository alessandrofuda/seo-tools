<?php
/*
SCRIPT PER ESTRARRE TUTTE LE QUERY CORRELATE AD UNA PRIMA KEYWORD
PER BYPASSARE PROBLEMI DI FREE PROXY gira da LOCALHOST/scrape-keywords.php
PROCEDURA SCRIPT

1) vai su google usando proxy (privato perchè sul free non va un kazzo) !! Provvisoriamente: da localhost
2) query iniziale (es. Fotovoltaico)
3) estrai le 8 query correlate come array (regex: tra "rfs":[ e ])
4) verifica se non già presente in db --> e aggiungi ognuna in db
5) per ogni query correlata fai una ricerca su google
6) per ogni ricerca --> ricomincia da punto 3)
7) IMPORTANTE: per evitare un "infinite loop" definire con un contatore il numero massimo di loop eseguibili --> risolto con 4 livelli ricorsivi di "foreach"
8) IMPORTANTE: quando "aggiorna database" --> evitare la sovrascrittura del db, ma aggiungere soli eventuali nuovi records


*/



// Initialize the session // authentication middleware
session_start();
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
	$msg = urlencode('Login required !');
  	header("location: ../index.php?alertmsg=$msg");
  	exit;
}




//custom function
function get_string_between($string, $start, $end){
    	$string = ' ' . $string;
    	$ini = strpos($string, $start);
    	if ($ini == 0) return '';
    	$ini += strlen($start);
    	$len = strpos($string, $end, $ini) - $ini;
    	return substr($string, $ini, $len);
	}


function SearchAndExtractCorrelateKey($mainkey) {
	
	$mainkey = urlencode($mainkey);
	$search_url = 'http://www.google.it/search?client=ubuntu&channel=fs&q='. $mainkey . '&ie=utf-8&oe=utf-8&gws_rd=cr';

// Utilizzo PROXY con file_get_contents
/*$aContext = array(
  					'http' => array(
    						'proxy' => 'tcp://185.5.64.70:8080', //server and port of NTLM Authentication Proxy Server.
  							'request_fulluri' => True,
					),
				);
$cxContext = stream_context_create($aContext);
//var_dump($cxContext);*/


$output0 = file_get_contents($search_url); //, false, $cxContext); 
//var_dump($output0);

	
$output1 = get_string_between( $output0, ',"rfs":[',']' );
//var_dump($output1);


$output2 = str_replace('"', '', explode(',', $output1));  //array !!!!!!!!!!!!!	
	return $output2;
}

function conn_db() { 

	require_once $_SERVER['DOCUMENT_ROOT'].'/seo-tools/config.php';

	$conn = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME_2);
	
	if ($conn->connect_error) {
    die("Connessione a db fallita. Errore: " . $conn->connect_error);
	} //else { echo 'conness. OK!'; }
	
	$conn->set_charset("utf8");	 // change character set to utf8 --> importante per gli accenti!
	
	return $conn;
} 

function NumRecordDB($conn) {
	$sql = "select * from `keywords_list` where `eliminato` = 0";
	$num = $conn->query($sql);
	$num_records = $num->num_rows;
	return $num_records;
}

function close_conn($conn) {
	$conn->close();
}


function count_words($string) { //considerare apostrofi virgolette ecc... string --> tra doppi apici
	$string = addslashes(trim($string));
	$array = explode(' ', $string);
	$num_words = count($array);
	return stripslashes($num_words);
}


function AddToDb($conn, $key) {  // $key è un array !!
	
	$num_words = count_words($key);
	
	$key = addslashes(trim($key));
	$sql0 = "select `id` from `keywords_list` where `keywords` = '".$key."'";
	$sql1 = $conn->query($sql0);
	if ($sql1 === FALSE) {
		die("Errore su recupero key: " . $conn->error); 
	}
		
	$n = $sql1->num_rows;
	if($n > 0) {
		//echo 'Keyword già presente in db<br>';
		return; 			//  !!! IMPORTANTE !!! evita sovracc. tabella db --> esce da funzione, non aggiorna tab.
	}
	//aggiorna tab. Inserisce nuovo record solo SE NON già inserito
	$sql = "INSERT INTO `keywords_list` (`keywords`, `num_words`) VALUES ('".$key."', '$num_words')";
	$added = $conn->query($sql);
	if($added === FALSE) {
		die("Errore di inserimento key: ".$conn->error);
	}
	return $added;
}  //  FINE AddToDb()

function StatusColumn($id, $status, $eliminato) {    //$status: dafare, fatto, daaggiornare
	$style_dafare = '';
	$style_fatto = '';
	$style_daaggiornare = '';
	
	if($eliminato == '1') {
		
		echo '<p style="color:red; margin:30px auto; text-align:center;">key eliminata</p>';
		
	} else { 
	
		if($status == 'dafare') {
			$style_dafare = 'opacity:1; background-color: red; padding: 10px 0; border-radius: 5px;';
		} elseif($status == 'fatto') {
			$style_fatto = 'opacity:1; background-color: #1be41b; padding: 10px 0; border-radius: 5px;';
		} elseif($status == 'daaggiornare') {
			$style_daaggiornare = 'opacity:1; background-color: #fffc00; padding: 10px 0; border-radius: 5px;';
		}
	
	
		echo '<div id="button-status">';
		echo '<form id="dafare" action="#key-'.$id.'" method="post">';
		echo '<input style ="'.$style_dafare.'" type="submit" name="dafare-'.$id.'" value="Da Fare" />';
		echo '</form>';
		echo '<form id="fatto" action="#key-'.$id.'" method="post">';
		echo '<input style ="'.$style_fatto.'" type="submit" name="fatto-'.$id.'" value="Fatto" />';
		echo '</form>';
		echo '<form id="daaggiornare" action="#key-'.$id.'" method="post">';
		echo '<input style ="'.$style_daaggiornare.'" type="submit" name="daaggiornare-'.$id.'" value="Da Aggiornare" />';
		echo '</form>';
		echo '</div><!--#button-status-->';
	
	} // fine -- if eliminato
}  // FINE StatusColumn func


function DafareStatus($conn,$id) { 
		$dafare = "UPDATE `keywords_list` SET `status`= 'dafare' WHERE `id` = '$id'";
		if ($conn->query($dafare) === TRUE) {
    		$dafare_status = true;
    		} else {
    		$dafare_status = false;
			echo "Error updating id $id record to 'dafare': " . $conn->error;
		return $dafare_status;
			}
	 
} //  FINE DafareStatus function
	
	
function FattoStatus($conn,$id) { 
		$fatto = "UPDATE `keywords_list` SET `status`= 'fatto' WHERE `id` = '$id'";
		if ($conn->query($fatto) === TRUE) {
    		$fatto_status = true;
    		} else {
    		$fatto_status = false;
			echo "Error updating id $id record to 'fatto': " . $conn->error;
			}
		return $fatto_status;
} // FINE FattoStatus function


function DaaggiornareStatus($conn,$id) { 
		$daaggiornare = "UPDATE `keywords_list` SET `status`= 'daaggiornare' WHERE `id` = '$id'";
		if ($conn->query($daaggiornare) === TRUE) {
    		$daaggiornare_status = true;
    		} else {
    		$daaggiornare_status = false;
			echo "Error updating id $id record to 'daaggiornare': " . $conn->error;
			}
		return $daaggiornare_status;
}  //  FINE DaaggiornareStatus function


function KeywordsColumn($id, $keywords, $eliminato) { 
	
	if($eliminato == 1) {  
		$elim_style = 'text-decoration: line-through;';	
		} else {
		$elim_style = 'text-decoration: none;';
		}
	
	//keyword
	echo '<div class="key" style="'.$elim_style.'">'.$keywords.'</div>';
	
	//container bottoni
	echo '<div id="update-key-'.$id.'" class="update-key">';
	
	//bottone modifica
	echo '<form id="modifica" action="#key-'.$id.'" method="post">';
	echo '<input type="submit" name="modifica-key-'.$id.'" value="Modifica" />';
	echo '</form>';
	
	echo ' -- ';
	
	//bottone elimina
	echo '<form id="elimina" style="display:inline-block;margin-bottom: 3px;" action="#key-'.$id.'" method="post">';
	echo '<input type="submit" name="elimina-key-'.$id.'" value="Elimina" />';
	echo '</form>';
	
	echo '<span style="font-size:10px;"><=></span>';
	
	//bottone ripristina
	echo '<form id="ripristina" style="display:inline-block;margin-bottom: 3px;" action="#key-'.$id.'" method="post">';
	echo '<input type="submit" name="ripristina-key-'.$id.'" value="Ripristina" />';
	echo '</form>';
	
	echo '</div><!--.update-key-->';
	
	
	if(isset($_POST['modifica-key-'.$id])) {
		//nascondi pulsanti sotto
		echo '<script>document.getElementById("update-key-'.$id.'").style.display = "none"</script>';
		//bottone riscrivi
		echo '<form id="riscrivi-key" style="display:block;margin-bottom: 10px;" action="#key-'.$id.'" method="post">';
		echo '<textarea style="width:100%;" name="riscrivi-key-'.$id.'" rows="2">'.$keywords.'</textarea>';
		echo '<input type="submit" name="rewrite-key-'.$id.'" value="Riscrivi Keyword" />';
		echo '&nbsp;&nbsp;<a style="font-size:small;" href ="javascript:history.go(-1)">annulla</a>';
		echo '</form>';
			
	} // fine if(isset($_POST['modifica-key-id']))
	
}  //  FINE KeywordsColumn function


function RiscriviKey($conn,$id) {	

	$new_key = addslashes($_POST['riscrivi-key-'.$id]);  //verificare apostrofi/accenti $new_key
	$query = "UPDATE `keywords_list` SET `keywords`= '$new_key' WHERE `id` = '$id'";
	if ($conn->query($query) === TRUE) {
   	$modificakey = true;
		} else {
		echo "Error while updating record. Error: " . $conn->error;
		$modificakey = false;
		}
	return $modificakey;
		
} //FINE RiscriviKey function


function EliminaKey($conn,$id) {
	$elimina = "UPDATE `keywords_list` SET `eliminato`= 1 WHERE `id` = '$id'";
	if ($conn->query($elimina) === TRUE) {
   	$eliminakey = true;
		} else {
		echo "Error while deleting record. Error: " . $conn->error;
		$eliminakey = false;
		}
	return $eliminakey;
} // FINE EliminaKey function


function RipristinaKey($conn,$id) {
	$ripristina = "UPDATE `keywords_list` SET `eliminato`= 0 WHERE `id` = '$id'";
	if ($conn->query($ripristina) === TRUE) {
		$ripristinakey = true;
    	} else {
    	$ripristinakey = false;
		echo "Error while restoring record: " . $conn->error;
		}
	return $ripristinakey;
} //FINE RipristinaKey



function UrlColumn($id,$url) {
	//Url posizionata
	echo '<div class="url">'.$url.'</div>';
	
	
	//bottone modifica url
	echo '<div id="update-url-'.$id.'" style="position:absolute; bottom:0; right:0;">';
	echo '<form id="modifica-url-'.$id.'" style="display:inline-block;margin-bottom: 3px;" action="#key-'.$id.'" method="post">';
	echo '<input type="submit" name="modifica-url-'.$id.'" value="Modifica" />';
	echo '</form>';
	echo '</div><!--#update-url-->';
	
	if(isset($_POST['modifica-url-'.$id])) {
		//nascondi pulsanti sotto
		echo '<script>document.getElementById("update-url-'.$id.'").style.display = "none"</script>';
		//bottone riscrivi
		echo '<form id="riscrivi-url" style="display:block;margin-bottom: 10px;" action="#key-'.$id.'" method="post">';
		echo '<textarea style="width:100%;" name="riscrivi-url-'.$id.'" rows="2">'.$url.'</textarea>';
		echo '<input type="submit" name="rewrite-url-'.$id.'" value="Riscrivi Url" />';
		echo '&nbsp;&nbsp;<a style="font-size:small;" href ="javascript:history.go(-1)">annulla</a>';
		echo '</form>';
			
	} // fine if(isset($_POST['modifica-url-id']))
	
}  //  FINE UrlColumn function


function RiscriviUrl($conn,$id) { 
	$new_url = addslashes($_POST['riscrivi-url-'.$id]);  //verificare apostrofi/accenti $new_url
	$query = "UPDATE `keywords_list` SET `url_posizionata`= '$new_url' WHERE `id` = '$id'";
	if ($conn->query($query) === TRUE) {
		$riscriviurl = true;
   	} else {
   	$riscriviurl = false;
		echo "Error while updating record 'url_posizionata'. Errore: " . $conn->error;
		}
	return $riscriviurl;
}




//-------------------	C O R E --------------	-------------------	--------------------	-------



$conn = conn_db();


//PAGINAZIONE
$limit = 100;  //per paginazione: 100 records per pagina
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };  
$start_from = ($page-1) * $limit;


if(isset($_POST['aggiorna'])) {
	
$mainkey = 'fotovoltaico';

$output2 = SearchAndExtractCorrelateKey($mainkey);  // restituisce array con 8 keywords !!!
//echo 'Output2:<br/><pre>'; var_dump($output2); echo'</pre>';


//TEST
//$output2 = array('testing','testàèòùììng');

foreach ($output2 as $value2) {   // foreach1
	AddToDb($conn,$value2);
	$output3 = SearchAndExtractCorrelateKey($value2);
	//echo 'Output3:<br/><pre>'; var_dump($output3); echo'</pre>';

	foreach ($output3 as $value3){    // foreach2
		AddToDb($conn,$value3);
		$output4 = SearchAndExtractCorrelateKey($value3);
		//echo 'Output4:<br/><pre>'; var_dump($output4); echo'</pre>';
		
		foreach ($output4 as $value4){ // foreach3
			AddToDb($conn,$value4);
			$output5 = SearchAndExtractCorrelateKey($value4);
			//echo 'Output5:<br/><pre>'; var_dump($output5); echo'</pre>';
			
				foreach ($output5 as $value5){ // foreach4 - SOLO AddToDb()
					AddToDb($conn,$value5);
				}  // FINE foreach4
			
		}  // FINE foreach3
		
	}   // FINE foreach2

//sleep(1); //seconds

}  // FINE foreach1 



} // FINE - if(isset($_POST['aggiorna']))


$select_all = $conn->query("SELECT * FROM `keywords_list`");
while ($ogg = $select_all->fetch_object()) {
	//var_dump($ogg);
	
	if(isset($_POST['riscrivi-key-'.$ogg->id])) {
		$keymodificata = RiscriviKey($conn,$ogg->id);
		if($keymodificata === false) {
			echo "<script>alert('Problema con riscrittura Key in Db!')</script>";
		}
	}
	
	if(isset($_POST['elimina-key-'.$ogg->id])) {
		$eliminakey = EliminaKey($conn,$ogg->id);
		if($eliminakey === false) {
			echo "<script>alert('Problema con riscrittura Key in Db!')</script>";
		}
	}
	
	if(isset($_POST['ripristina-key-'.$ogg->id])) {
		$ripristinakey = RipristinaKey($conn,$ogg->id);
		if($ripristinakey === false) {
			echo "<script>alert('Problema con ripristino Key in Db!')</script>";
		}
	}
	 
	if(isset($_POST['riscrivi-url-'.$ogg->id])) { 
		$riscriviurl = RiscriviUrl($conn,$ogg->id);
		if($riscriviurl === false) {
			echo "<script>alert('Problema con riscrittura Url in Db!')</script>";
		}
					} 
	if(isset($_POST['dafare-'.$ogg->id])) {
		$dafare_status = DafareStatus($conn,$ogg->id);
		if($dafare_status === false) {
			echo "<script>alert('Problema con riscrittura Status Dafare in Db!')</script>";
		}
	}
	
	if(isset($_POST['fatto-'.$ogg->id])) {
		$fatto_status = FattoStatus($conn,$ogg->id);
		if($fatto_status === false) {
			echo "<script>alert('Problema con riscrittura Status Fatto in Db!')</script>";
		}
	}
	
	if(isset($_POST['daaggiornare-'.$ogg->id])) {
		$daaggiornare_status = DaaggiornareStatus($conn,$ogg->id);
		if($daaggiornare_status === false) {
			echo "<script>alert('Problema con riscrittura Status Daaggiornare in Db!')</script>";
		}
	}
	
} // FINE ciclo WHILE

?>
<html>
<head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<meta name="robots" content="noindex,nofollow">
	<title></title>
	<style>
		input{ font-size:12px; }
		table{border-collapse: collapse; font-size: 11px; width: 100%;}
		tr.table-head{background-color: #EFEFEF; text-align: center;}
		td{border: 1px solid;}
		td.counter{text-align: center;}
		td.id{text-align: center;}
		td.data{text-align: center;} 
		td.keywords{position:relative; min-width:350px; max-width:500px;}
		td.numb-words{ width:40px; text-align:center; }
		td.status{max-width: 110px;}
		td.url{position:relative;}
		.update-key {position:absolute; bottom:0; right:0;}
		#modifica{display:inline-block;margin-bottom: 3px;}
		#button-status input{width: 98%; opacity: 0.5; cursor: pointer;}
		#dafare input:hover, #fatto input:hover, #daaggiornare input:hover{opacity:1;}	
		#tot-records{ margin-bottom: 20px; }
		.pagination{text-align:center; margin: 10px;}
		.pagination a{margin:5px;}
	</style>
</head>
<body>
<form id="aggiornaDB" action="#" method="post">
	<input type="submit" name="aggiorna" value="Aggiorna DB" /> <span style="font-size: x-small;">(circa 3 minuti! - Prima di aggiornare fare <span style="color: red;">Backup</span> del DB)</span>
</form>
<div id="tot-records" >
	Totale keywords in db <span style="font-size:small;">(non cancellate)</span>: <?php echo NumRecordDB($conn); ?>
</div>
<table>
	<tr class='table-head'>
		<td class='counter'><b>N.</b></td>
		<td class='id'><b>Id</b></td>
		<td class='data'><b>Data<br/>prima<br/>rilevazione</b></td>
		<td class='keywords'><b>Keywords</b></td>
		<td class='numb-words'><b>Num. parole</b><br/>Ordina per..</td>
		<td class='status'><b>Status</b></td>
		<td class='url'><b>Url posizionata</b></td>
		<td class='position'><b>Posizione in Serp</b></td>
	</tr>
<?php
$result = $conn->query("SELECT * FROM `keywords_list` WHERE `eliminato` = '0' ORDER BY `num_words` DESC LIMIT $start_from , $limit"); 
$counter = 0;
while ($obj = $result->fetch_object()) {	
	//echo '<pre>'; var_dump($obj); echo '</pre>';
	
	echo "<tr id='key-".$obj->id."'>";
	echo "<td class='counter'>" . ++$counter . "</td>";
   echo "<td class='id'>" . $obj->id . "</td>";
   echo "<td class='data'>" . str_replace(" ","<br/>",$obj->data_prima_rilevazione) . "</td>"; 
   echo "<td class='keywords'>"; KeywordsColumn($obj->id,$obj->keywords,$obj->eliminato); echo "</td>";
   echo "<td class='numb-words'>" . $obj->num_words . "</td>";
   echo "<td class='status'>"; StatusColumn($obj->id, $obj->status, $obj->eliminato); echo "</td>"; 
   echo "<td class='url'>"; UrlColumn($obj->id, $obj->url_posizionata); echo "</td>";
   echo "<td class='position'>" . '' . "</td>";
   echo "</tr>";
}
$result->close();   // close result set


?>
</table>

<?php  // pagination
$total_records = NumRecordDB($conn);
$total_pages = ceil($total_records / $limit);
$pagLink = "<div class='pagination'>";  
for ($i=1; $i<=$total_pages; $i++) {  
             $pagLink .= "<a href='scrape-keywords.php?page=".$i."'>".$i."</a>";  
};  
echo $pagLink . "</div>";  
?>

<div><br/></div>

<?php  close_conn($conn);  ?>
</body>
</html>















