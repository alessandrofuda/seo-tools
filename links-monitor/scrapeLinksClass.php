<?php 

class scrapeLinks {

	// properties
	public $url;

	// methods
	public function __contruct($url){

		$this->url = $url;

	}



	public function getExternalPage() {  // scraping

		// 

	}


	public function isIndexedOnGoogleSerp() {   // scompone il link e fa ricerca su google. Se la pagina esce --> page is well indexed from Google
												// es. url: www.adnkronos/notizie/impianti-fotovoltaici --> search: "adnkronos notizie impianti fotovoltaici" 

		//

	}

	public function isThereNoindexNofollowTag() {  // head analysis

		//

	}


	public function getLinksToMysite() {  // page parsing: how many links to my site?? Follow or Nofollow?

		//

	}


	public function areNofollowLinks() {

		//

	}

	public function sendMailNotification() {

		//

	}



}