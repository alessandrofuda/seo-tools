# seo-tools

### Agnostic framework tool for automatic web-site monitoring (with API Google Search Console + Se_rp Scra_ping)


require:
* edit crontab (crontab -e)
* insert json file with Google API Credentials outside (before) root project


### 1. keywords list - API --> fetch data from google search console API. Useful for fetch first X keywords more populars on my site.
cron: no



### 2. scrape-serp --> serp monitoring tool
cron: 1 time a week.

Every Monday:   1. a) API call: fetch first 20 keywords array from google search-console API
                2. b) populate keywords array in config-ss.php
                3. c) scraping serp for this gold-keywords
                4. d) populate Db (scrape_serp tab)



### 3. scrape-keywords --> fetch 1400+ keywords from google serp
cron: no


### 4. inbound links montoring
under construction (--> cron 1 time a week)






