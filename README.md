=== Today X History ===
Contributors: saiyanweb
Donate link: 
Tags: history, events, shortcode, block, widget
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display historical events, notable births and deaths for a given date (defaults to today). Shortcode + Gutenberg block, with server-side caching.

== Description ==

Today X History is a WordPress plugin that shows historical events, births, and deaths for any date (defaults to today).  
Data source: the public "On This Day" API by byabbe.se (event descriptions are provided in English).  
The plugin interface and labels are translatable via standard WordPress `.po/.mo` files.

**Features**
- Shortcode and Gutenberg Block
- Events, Births, Deaths
- Server-side caching (configurable TTL)
- Light/Dark/Auto theme
- Works with any theme

**Shortcode Examples**
- `[tih]` — today’s events  
- `[tih type="births" limit="5"]` — 5 births for today  
- `[tih type="deaths" month="8" day="14"]` — deaths for August 14  
- Parameters: `type` (`events|births|deaths`), `limit` (int), `month` (1–12), `day` (1–31)

**Note on translations**
- Interface strings are translatable
- Historical descriptions come from the API and are in English

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install the ZIP via **Plugins → Add New**.  
2. Activate the plugin.  
3. Optionally, go to **Settings → TodayInHistory** to configure cache TTL and theme.  
4. Insert the shortcode on a page or post (examples above).

== Frequently Asked Questions ==

= Can I change the language of the event texts? =  
Interface labels can be translated via `.po/.mo`. Event texts are provided by the API in English.

= Does this work with caching plugins/CDNs? =  
Yes. The plugin implements its own server-side cache to minimize API calls.

== Screenshots ==

1. Frontend display  
2. Settings page (cache, theme, usage)

== Changelog ==

= 1.1.0 =
* Initial public release.

== Upgrade Notice ==

= 1.1.0 =
First public release.