<?php 


/**
* Google Search Console API Call
* 
*/
class SC_API_Call 
{
	
	//function __construct(argument)
	//{
	//	# code...
	//}

	public function gold_keywords($miosito,$to_date,$from_date) {
		
		require_once 'vendor/autoload.php';

		 
		// init Client
		$client = new Google_Client();
		$client->useApplicationDefaultCredentials();
		$client->addScope(Google_Service_Webmasters::WEBMASTERS); 


		// Webmaster service instance
		$webmastersService = new Google_Service_Webmasters($client);
		$searchanalytics = $webmastersService->searchanalytics;

		// Build query
		$request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest; 
		$request->setStartDate($from_date);   // '2018-02-02'
		$request->setEndDate($to_date);		// '2018-03-27'
		$request->setDimensions(['query']);  // array..
		// $request->setRowLimit($rowLimit);


		// filtro  FILTRO PER IMPRESSIONS NON CONSENTITO DA API. eSTRARRE TUTTI I VALORI E ORDINARE L'ARRAY PER KEY IMPRESSIONS ????????
		//$filtro = new Google_Service_Webmasters_ApiDimensionFilter;
		//$filtro->setDimension("impressions");
		//$filtro->setOperator("equals");
		//$filtro->setExpression("tablet");
		//$filtri->setFilters(array($filtro));
		//$request->setDimensionFilterGroups(array($filtri));


		// result of API call
		$qsearch = $searchanalytics->query($miosito, $request); 
		$rows = $qsearch->getRows(); 


		// DESC order by Impressions numbers --> !!!!!!!   https://stackoverflow.com/questions/4282413/sort-array-of-objects-by-object-fields
		usort($rows, function($first, $second){
			return $first->impressions < $second->impressions;  // 0 -1 
		});


		foreach ($rows as $row) {

			$keywords[] = [ 
							'keyword' => $row->keys[0], 
							'impressions' => intval($row->impressions), 
						  ];
		}


		//echo "<pre>";
		//print_r($keywords);
		//echo "</pre>";
		//die('ookk');



		return $keywords;
	}
}