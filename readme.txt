=== Vacancy Scraper UA ===
Contributors: darkstardust, stellarhermitua
Tags: vacancies, work.ua, rоbota.ua, scraper
Requires at least: 6.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A plugin to fetch publicly available job postings from Work.ua (via scraping) and Robota.ua (via public API) and display them on your WordPress site.

== Description ==

A plugin that retrieves (copies) publicly available job postings from Work.ua (by scraping public company pages) and 
Robota.ua (via their public API, no API keys required), including vacancy title, salary (if shown) and city. 
Listings can be displayed anywhere on your site using shortcodes or helper functions. 
The plugin provides display settings (block styles) and a toggle to enable or disable city-based filtering 
(using the public city list from the postings and/or Robota.ua’s public dictionary). 
It uses only open, public data and the company's public ID from the URL. 
It does not collect or store any personal data (neither yours nor your visitors'), and it does not copy the company name.


= External Data sources (Work.ua & Robota.ua) =

- Work.ua — Ukrainian job board.
  – Terms of Use / Privacy: https://www.work.ua/about-us/conditions/
  – Data used: ONLY publicly available job postings from a company’s public page.
    The plugin copies vacancy title, salary (if shown), and city. A consolidated
    city list is built from these postings to enable filtering.
  – Company ID: taken from the public company URL.

- Robota.ua — Ukrainian employment portal with public endpoints.
  – Terms & Privacy:
    • Terms (job seekers): https://images.cf-rabota.com.ua/2017/03/TOU_for_Users_rabota.ua.pdf
    • Terms (employers):   https://images.cf-rabota.com.ua/alliance/terms_of_use_employer_v4.pdf
    • Privacy Policy:      https://images.cf-rabota.com.ua/2024/privacy_offer_20.11.2024.pdf
  – Data used: ONLY publicly available data via public API; no API keys required.
    Endpoints used:
      • https://api.robota.ua/companies/{company_id}/published-vacancies
        (fetch the company’s publicly published vacancies)
      • https://api.robota.ua/dictionary/city
        (fetch the site-wide public city dictionary once to support city filtering)
  – Company ID: taken from the public company URL.

General note: The plugin requests and displays only public/open data from those sites.
It does not collect or store any personal data—neither yours nor your visitors’.

= File Permissions =
The plugin requires write permissions to its assets/css/ directory. 
Please ensure your server configuration allows WordPress to write to this folder.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/vacancy-scraper-ua/` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure company IDs in the plugin settings.

== Changelog ==

= 1.1 =
* Added: Dynamic CSS generation for frontend styling
* Improved: Admin interface with color pickers
* Fixed: Minor code errors

= 1.0 =
* Initial release.

== Upgrade Notice ==
= 1.1 =
Fixed bugs in data output, connection of style files and scripts

= 1.0 =
Initial release.

== Frequently Asked Questions ==
= Does it require an API key? =
No, Work.ua does not have an API and is scraped directly. Rabota.ua API does not require keys for company job listings.

= Can I use different colors for different job boards? =
Yes! Each job board (Work.ua and Robota.ua) has its own independent color settings.
