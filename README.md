# seo-tools

### Agnostic framework tool for automatic web-site monitoring (with Google Search Console API + Se_rp Scra_ping + external pages scrap)


require:
* edit crontab (crontab -e)
* upload json file with Google API Credentials outside (before) root project





### 1. keywords list - API --> fetch data from google search console API. Useful for fetch first X keywords more populars on my site.

cron: no



### 2. scrape-serp --> serp monitoring tool

cron: 1 time a week.

Every Monday:

*  API call: fetch first 20 keywords array from google search-console API
*  populate keywords array in config-ss.php
*  scraping serp for this gold-keywords
*  populate Db (scrape_serp tab)




### 3. scrape-keywords --> fetch 1400+ keywords from google serp
cron: no, manual update
TO DO: re-engineering using OOP




### 4. scrape inbound-links and monitoring it

What does it do?

*  crontab: every thuesday in the morning
*  take links pages list --> from config.php file array
*  for every page:

	*  check if page is indexed by google
 	*  check if is there a noindex nofollow tag in header
 	*  check if link TO mysite is present in this page (-->Regex)
	*  check if link TO mysite is follow or nofollow 
	*  send mail notification to admin mail



under construction (--> cron 1 time a week)






