# seo-tools

### Agnostic framework tool for automatic web-site monitoring (with Google Search Console API + Se_rp Scra_ping + external pages scrap)


require:
* edit crontab (crontab -e)
* insert json file with Google API Credentials outside (before) root project





### 1. keywords list - API --> fetch data from google search console API. Useful for fetch first X keywords more populars on my site.
cron: no



### 2. scrape-serp --> serp monitoring tool
cron: 1 time a week.

Every Monday:
	a.	API call: fetch first 20 keywords array from google search-console API
	b.	populate keywords array in config-ss.php
	c.	scraping serp for this gold-keywords
	d.	populate Db (scrape_serp tab)



### 3. scrape-keywords --> fetch 1400+ keywords from google serp
cron: no


### 4. scrape inbound-links and monitoring it
under construction (--> cron 1 time a week)






