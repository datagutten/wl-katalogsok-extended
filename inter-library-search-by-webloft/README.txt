=== WL Katalogs&oslash;k ===
Contributors: webloeft
Tags: bibliotek, katalog, inter library search, search, s&oslash;k, s&oslash;king, s&oslash;kemotor, bibliotekkatalog, bibliofil, bibsys, koha, metas&oslash;k, library, bibvenn, webekspertene, webl&oslash;ft, webloft, e-bok, ebok, e-book, e-books, bibliotekarens beste venn, sundaune, buskerud fylkesbibliotek, openlibrary
Requires at least: 3.0
Tested up to: 4.8.3
Stable tag: 3.5.6
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Search your library catalog + movies and ebooks without leaving your own site.

== Description ==

Use the shortcode creator to make your own search engine. Search the library catalogue as well as e-books from bokselskap.no and Openlibrary, digitized free books from the National Library and movies from filmbib.no. Hits are displayed inline in your own site. You also have the option to display single posts on the page or post of your liking, as well as inserting the search form in the form of a widget anywhere on your site and sending the results to the post or page you prefer!

Options include which library catalogue to search, how many hits to display per page and whether to display book covers or not (could result in slower search). The output is easily customizable if you know CSS by editing the included style sheets.

The plugin currently supports Bibliofil, Mikromarc, Koha and Tidemann. Additional tabs are provided for displaying hits from filmbib.no, e-books from bokselskap.no and Openlibrary and digitized free books from the National Library (Bokhylla).


== Installation ==

= Uploading the plugin via the Wordpress control panel =

Make sure you have downloaded the .zip file containing the plugin. Then:

1. Go to 'Add' on the plugin administration panel
2. Proceed to 'Upload'
3. Choose the .zip file on your local drive containing the plugin
4. Click 'Install now'
5. Activate the plugin from the control panel
6. If you receive an error message, try activating the plugin one more time


= Upload the plugin via FTP =

Make sure you have downloaded the .zip file containing the plugin. Then:

1. Unzip the folder 'wl-katalogsok' to your local drive
2. Upload the folder 'wl-katalogsok' to the '/wp-content/plugins/' folder (or wherever you store your plugins)
3. Activate the plugin from the control panel
4. If you receive an error message, try activating the plugin one more time

... Or install it via the Wordpress repository!

= Usage =

First: Visit the settings page and select some sources for your search.

This plugin provides its own shortcode generator, available from the Tools menu within Wordpress. From here you can control the options available: Which library catalogue to search, how many hits to display per page and whether to search for book covers to display (disabling this will speed up your search if your server is slow). Visit the shortcode generator, then copy and paste the generated shortcode into a post or a page.

Alternatively, insert the "WL Katalogs&oslash;k" widget anywhere in your widget compatible theme. To use the widget, you must first create a page or post in Wordpress containing the [wl-ils] shortcode. This will act as a landing page, and the result of searches performed using the widget (from a sidebar, header or anywhere) will be displayed on this page.

Settings

While you can control each search form separately using the shortcode generator, some general options are given to you on the Settings->WL Katalogs&oslash;k page:

- Sources to search: Your results will be broken down into tabs. From here you can select which sources/tabs to include. NB! The plugin won't work until you have made a selection here!

- Page for displaying single posts: If you do not want your visitors to leave your site when clicking a single item, then set up a page or post containing the [wl-ils-enkeltpost] shortcode. Every page or post containing this shortcode will display in this dropdown menu, and the one you select will act as a landing page for viewing single posts. If you disable this setting, users will be taken to their library system (Bibliofil, Koha etc...) upon clicking a single post.

- Display link to advanced search: Sometimes simplicity just isn't good enough. Should your users want to perform more thorough and advanced searches, we suggest that they perform the same search in their native library system. This button will provide them with a simple way to do so.

- Open single posts in a new window: Could be useful for easy return to the search results (just close the tab or window viewing the single post).

- Book covers: Select or deselect sources for looking for book covers. More covers will make for prettier search results, but could slow down your search allthough we do our very best not to let you down.

- Automatically right truncate: Should every [search] get automatically turned into [search*]? Some of you have asked us for this, and some have asked us to turn it off. With this setting, everybody should be happy.



== Frequently Asked Questions ==

= My search is slow! =

There can be any number of reasons for this, of which most are beyond our control. What you could do regarding this plugin is to a) reduce the number of hits displayed per page and/or b) turn off display of book covers.

= I am not getting the search results I want! =

We are sorry to hear this. Perhaps using the "Advanced search button" will help you, as the native library system usually offer more complex options and settings for searching.


== Screenshots ==

1. Search form with results - free e-books
2. Display of single item


== Change log ==

= 3.5.7 =

* Bugfix: EbookSearchController, BokHyllaSearchController


= 3.5.6 =

* Bugfix: functions bibliofil_sok, tidemann_sok, mikromarc_sok

= 3.5.5 =

* Bugfix: Admin settings, options of field single post view

= 3.5.4 =

* New library: Berlevåg folkebibliotek

= 3.5.2 =

* Php7 fix

= 3.5.1 =

* Library Råde Bibliotek, new sru address

= 3.5.0 =

* New library Bø Bibliotek
* Improved searching performance
* Various bugfixes

= 3.3.2 =

* Haugesund Folkebibliotek, new server address

= 3.3.1 =

* Ny library Sandnes bibliotek
* Bugfix: e-books if result is empty

= 3.3 =

* Refactored code
* Shortcode parameters in english, backward compatible with norwegian parameters
* Ny libraries (Aremark, Hamar, Nome)
* Full support for Mikromarc ( search, single view, order/reserve )
* Disabled cover import from bokforsider.webloft.no

= 3.2 =

* Option for selecting which tabs/sources should be displayed
* Internationalization - now supports translation
* First Mikromarc libraries added

= 3.1 =

* Experimental support for Mikromarc
* New tab: E-books from Openlibrary and bokselskap.no
* Bugfix: Informs you if there are no items belonging to a post in Bibliofil/Mikromarc
* New material types: Blurays and map books

= 3.0.1 =

* Now gives error message if no library catalogue is selected for searching (previously selected Akershus Fylkesbibliotek as default)
* Setting to display book covers is now on by default

= 3.0.0 =

* Tabbed result list, fetches results from several new sources (filmbib.no, bokhylla.no)
* Interface cleanup, settings easier to understand
* New setting to automatically right-truncate search
* No longer supports Bibsys
* Bugfix: Facebook sharing of single post didn't work due to typo in code
* Can now choose whether to open single posts in a new window or not
* New naming convention to match other WL plugins

= 2.4.4 =

* Fine tuning of Bibliofil search

= 2.4.3 =

* Bugfix: Better approach to searching for several words

= 2.4.2 =

* New library added: Skien
* Bugfix: Failed to load CSS when only widget was present in page (thank you, Nikolaj Blegvad!)
* Bugfix: Failed to display book cover when multiple URNs encountered in Bokhylla

= 2.4.1 =

* Merely cosmetics

= 2.4 =

* Can now send results to any premade post/page directly using shortcode argument
* Can now pick a background color, text color and rounded edges for the submit button in all widgets
* Option to include a link to the lbrary system's advanced search screen on result page

= 2.3.1 =

* Bugfix: Widget wasn't keeping the search term

= 2.3 =

* Feature: Now possible to find free books available online from the National Library (bokhylla.no)

= 2.2 =

* Bugfix: Widget now works with displaying single post on its own page
* Cleanup: Functions code no longer conflicts with other addons
* Visual: Fixed social media icons for sharing single posts

= 2.1.1 =

* Should no longer require the PEAR_Exception class

= 2.1 =

* Code rewrite to simplify a bit
* Responsive layout for mobile phones and tablets

= 2.0.4 =

* Bugfix: Fixed Moss bibliotek
* New library: Nedre Eiker bibliotek

= 2.0.3 =

* Bugfix: Widget redirecting search to result page didn't work

= 2.0.2 =

* Widget: Support for choosing the library catalogue to search in on a per-widget basis
* Shortcode: Support for specifying the library catalogue in the shortcode via the "mittbibliotek" parameter
* Now fetches PDF excerpt from MARC 856 where available
* Bugfix: Wrong character encoding in Facebook sharer window

= 2.0.1 =

* Initial public release
* Support for Tidemann libraries added
* Bugfix: Description in Facebook share window was empty

= 1.3 =

* Bugfix: Widget wasn't passing search query along to the search page
* Feature: Added Twitter and Facebook share buttons on single items
* Feature: Now possible to link directly to single items from the outside
* Bugfix: URI sometimes too long for browser when sending availability data in the query string (414 error)

= 1.2 =

* A LOT of additional info on each item (020$b,082$a,100$d,245$c,300$a/b,500$a,505$a,511$a,574$a,650$a,740$a etc.)
* Tab based view of single post information
* Code optimalization

= 1.1 =

* Bugfix "costa rica": Pagination when search query contains several words not inside quotes
* Bugfix: Treat search for several words as [word1 AND word2], not OR
* New layout

= 1.0 =

* First version

NORWEGIAN:

= 3.2 =

* Mulighet for &aring; velge hvilke faner/kilder som skal vises i s&oslash;ket
* St&oslash;tte for oversettelse til andre spr&aring;k
* F&oslash;rste Mikromarc-bibliotek lagt til - Aremark og Sandnes

= 3.1 =

* N&aring; med eksperimentell st&oslash;tte for Mikromarc
* Ny fane: E-b&oslash;ker fra Openlibrary og bokselskap.no
* Bugfix: Gir n&aring; beskjed hvis ingen eksemplarer finnes i Bibliofil/Mikromarc
* Nye materialtyper: Bluray og atlas

= 3.0.1 =

* Gir n&aring; feilmelding dersom ingen bibliotekkatalog er valgt for s&oslash;k (valgte tidligere Akershus Fylkesbibliotek som standard)
* Innstilling for &aring; vise bokomslag er n&aring; skrudd p&aring; som standard

= 3.0.0 =

* Fanebasert treffvisning, henter treff fra flere nye kilder (filmbib.no, bokhylla.no)
* Renere grensesnitt, enklere innstillinger
* Ny innstilling for automatisk h&oslash;yretrunkering
* Ikke lenger st&oslash;tte for Bibsys
* Bugfix: Deling av enkeltposter p&aring; Facebook virket ikke pga. skrivefeil i koden.
* Kan n&aring; velge om enkeltposter skal &aring;pnes i et nytt vindu eller ikke
* Ny navngiving for &aring; matche andre Webl&oslash;ft-utvidelser

= 2.4.4 =

* Finjustering av Bibliofil-s&oslash;k

= 2.4.3 =

* Bugfix: Bedre h&aring;ndtering av frases&oslash;k

= 2.4.2 =

* Nytt bibliotek: Skien
* Bugfix: Stilark ble ikke lastet n&aring;r bare widget var til stede p&aring; siden (takk, Nikolaj Blegvad!)
* Bugfix: Viste ikke omslagsbilde n&aring;r boka hadde flere URN i Bokhylla

= 2.4.1 =

* Kun kosmetikk

= 2.4 =

* Kan n&aring; sende trefflisten til en hvilken som helst side/innlegg ved hjelp av argument i shortcode
* Kan n&aring; velge bakgrunnsfarge, tekstfarge og runde kanter p&aring; s&oslash;keknappen i widget
* Kan inkludere lenke til avansert s&oslash;k i ditt biblioteksystem p&aring; toppen av trefflista

= 2.3.1 =

* Bugfix: S&oslash;ketermen ble ikke med fra widget

= 2.3 =

* Nytt: N&aring; mulig &aring; s&oslash;ke blant gratis b&oslash;ker tilgjengelig p&aring; nett fra Nasjonalbiblioteket (Bokhylla)

= 2.2 =

* Bugfix: Widget fungerer n&aring; selv n&aring;r man skal vise enkeltposter p&aring; en egen side
* Opprydning: Koden med funksjoner vil ikke havne i konflikt med andre utvidelser
* Visuelt: Fikset ikonene for &aring; dele enkeltposter p&aring; sosiale media

= 2.1.1 =

* Skal ikke lenger være avhengig av PEAR_Exception-klassen

= 2.1 =

* Skrevet om koden for &aring; forenkle litt
* Responsivt design, layout tilpasset mobiltelefoner og nettbrett

= 2.0.4 =

* Bugfix: Fikset Moss bibliotek
* Nytt bibliotek: Nedre Eiker

= 2.0.3 =

* Bugfix: Widget omdirigerte ikke til riktig side for &aring; vise trefflisten

= 2.0.2 =

* Widget: Mulighet for &aring; velge hvilket biblioteks katalog det skal s&oslash;kes i for hver enkelt widget
* Kortkode: Mulighet for &aring; velge bibliotek-katalog i kortkode ved hjelp av "mittbibliotek"-parameteret
* Henter n&aring; PDF med utdrag fra MARC-felt 856 der dette finnes
* Bugfix: Feil tegnkoding i Facebooks delevindu

= 2.0.1 =

* F&oslash;rste offentlige slipp
* Lagt til st&oslash;tte for Tidemann
* Bugfix: Beskrivelse kom ikke med i delingsvinduet for Facebook

= 1.3 =

* Bugfix: S&oslash;kestrengen ble ikke med over fra widget til s&oslash;keside
* Forbedring: Lagt til Twitter- og Facebook-knapper for &aring; dele enkeltposter
* Forbedring: N&aring; kan du lenke direkte til sider med enkeltposter utenfra
* Bugfix: URI-en ble noen ganger for lang for nettleseren n&aring;r bestandsdata ble sendt meg URL-en (414-feil)

= 1.2 =

* MYE mer informasjon om hvert enkelt treff (020$b,082$a,100$d,245$c,300$a/b,500$a,505$a,511$a,574$a,650$a,740$a etc.)
* Fanebasert visning av informasjon for enkelttreff
* Kodeoptimalisering

= 1.1 =

* Bugfix "costa rica": Paginering n&aring;r man s&oslash;kte p&aring; flere ord uten &aring; sette i anf&oslash;rselstegn
* Bugfix: Behandle s&oslash;k etter flere ord som [ord1 OG ord2], ikke ELLER
* Ny layout

= 1.0 =

* F&oslash;rste versjon

== Upgrade Notice ==

Remember to select your sources from the settings page!

Important for upgrading users!
If you are updating the plugin from the control panel and you get an error, just try to activate the plugin again and it should be OK (this is because of an earlier name change). If you encounter additional problems, please contact us at bugfix@bibvenn.no.

NORWEGIAN:

Husk &aring; velge kilder fra innstillingene!

Viktig dersom du oppgraderer!
Hvis du oppdaterer utvidelsen vha. kontrollpanelet i Wordpress og f&aring;r en feilmelding, pr&oslash;v &aring; aktivere utvidelsen p&aring; nytt. Da skal det g&aring; seg til (dette skjer p&aring; grunn av et tidligere navnebytte). St&oslash;ter du p&aring; flere problemer, vennligst kontakt oss p&aring; bugfix@bibvenn.no.
