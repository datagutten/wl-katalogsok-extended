<?php

// Hvis vi kommer direkte hit trenger vi WP-funksjonalitet!
if ( ! defined( 'WPINC' ) ) {
	require_once("../../../../wp-load.php");
}


// turn on for debug

/*
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
*/

/*
CURL dokumentasjon:
http://semlabs.co.uk/journal/object-oriented-curl-class-with-multi-threading
*/

// $time_start = microtime(true);

$library_id   				= cleanValue($_REQUEST['library_id']);
$omslagbokkilden	    = cleanValue($_REQUEST['omslagbokkilden']);
$omslagnb        	    = cleanValue($_REQUEST['omslagnb']);
$hamedbilder     	    = cleanValue($_REQUEST['hamedbilder']);
$treffbokhylla   	    = (int) ($_REQUEST['treffbokhylla']);
$hoyretrunk   		    = (int) ($_REQUEST['hoyretrunk']);
$viseavansertlenke 		= (int) ($_REQUEST['viseavansertlenke']);
$enkeltpostnyttvindu	= (int) ($_REQUEST['enkeltpostnyttvindu']);
$resources        		= cleanValue($_REQUEST['kilder']);
$skjulesoketips 			= (int) ($_REQUEST['skjulesoketips']);
$sokeord              = cleanValue($_REQUEST['webloftsok_query']) ;

$treffperside         = (int) ($_REQUEST['treffperside']);

if ( !$treffperside ) { $treffperside = 9999; }

if ( _is($_REQUEST, 'posisjon') ) {
  $posisjon = (int)($_REQUEST['posisjon']);
}
else {
  $posisjon = 1;
}

$qsokeord = $sokeord; // r&aring; s&oslash;keterm til senere bruk

if ($hoyretrunk == "1") {
  $sokeord .= "*";
}

// Jukse til s&oslash;keord
// Stjerne hvis h&oslash;yretrunkering er angitt i innstillinger

$sokeord = str_replace(", ", ",", $sokeord); // fjerne mellomrom i invertert form
$sokeord = str_replace("*", "%2A", $sokeord); // ekte asterisk &oslash;delegger s&oslash;ket

// Finne server og biblioteksystem ut fra libnr.

include( getConfigPath("library_list.php") );

if (_is($libraries, $library_id) ){
  $library  = $libraries[$library_id];
  $Search 	= new WL_Search($library, $sokeord);
  $Search->setLibraryIndex($library_id);


  $mittbiblioteknavn  = $library['name'];
  $minserver          = $library['server'];
  $mittsystem         = $library['system'];
  $minavdkode         = $library['department_id'];

  // S&Oslash;K I BOKHYLLA - MITTSYSTEM BLIR BOKHYLLA
  if ( _is($_REQUEST, 'dobokhylla') == "1" ) { // vi skal vise treff fra Bokhylla
    $Search->setLibrarySystem('bokhylla');
  }
}

$result = $Search->runQuery($posisjon, $treffperside);
if ( $result['count-items'] > 0) { // kan være tom
	if ($hamedbilder == "1") { // bare hvis innstillingen "ha med bilder i det hele tatt" er satt
		foreach ( $result['items'] as $enkelttreff => &$treff) {
			// Det enkleste er &aring; bruke v&aring;r egen server hvis vi har ISBN
			if ( _is($treff, 'isbn') && !isset($treff['omslag']) ) { // vi har ISBN men ikke omslag
				$treff = $Search->getItemInfoFromNationalLibraryByIsbn($treff);
			}

			// Hvis denne innstillingen er sl&aring;tt p&aring; og vi fortsatt ikke har omslag
			if (($omslagbokkilden == "1") && (!isset($treff['omslag']))) {
				// Finne info fra Bokkilden
				// Hvis vi har ISBN
				if ( $isbn = _is($treff, 'isbn') ) {
					$treff = $Search->getItemInfoFromBokkilden($treff);
				}
			}

			// Siste fors&oslash;k: S&oslash;ke i NB via URN , hvis omslag fra NB er sl&aring;tt p&aring; OG vi fortsatt ikke har omslag OG vi ikke har ISBN (har allerede s&oslash;kt p&aring; ISBN)
			// Men det m&aring; være b&oslash;ker (annet finnes jo ikke i NB)
			if (($omslagnb == "1") && (!isset($treff['omslag'])) && (!isset($treff['isbn'])) && ($treff['type'] == "bok")) {
				// Vi s&oslash;ker p&aring; tittel og ser hvilke URN-er vi f&aring;r
				$treff = $Search->getItemInfoFromNationalLibraryByTitle($treff);
			} // slutt p&aring; hvis omslagnb er skrudd p&aring;
		} // slutt p&aring; foreach
	} // slutt p&aring; sjekk om "ha med bilder"-innstilling er satt
} // slutt p&aring; sjekk om antallfunnet > 0


//**********************************************************************************
// Hvis vi skal vise poster p&aring; egne sider m&aring; vi fikse alle permalenkene
//**********************************************************************************
// Vi m&aring; g&aring; gjennom 'HTTP_REFERER' for &aring; finne enkeltposturl

// OBS! Koha kan ikke bruke enkel, ny m&aring;te for vi har ikke unik post-ID som kan s&oslash;kes opp

$single_item_url = null;
if ( $single_item_url =  _is($_GET, 'enkeltposturl') ) { // Vi har det i referer, overstyrer den vi hadde
  $single_item_url = base64_decode(urldecode($single_item_url) );
}


if ($mittsystem != 'bokhylla') { // Meningsl&oslash;st med enkeltpostvisning ved s&oslash;k bare i Bokhylla
	if ( $single_item_url ) { // det finnes en url til side hvor enkeltposter skal vises
		if ( is_array($result['items']) && count($result['items'])) {
			foreach ( $result['items'] as $mangetreff => &$etttreff) { // for hvert treff i trefflista
				$etttreff['biblioteksystem'] = $mittsystem;
				if ($mittsystem == "koha") { // Hvis koha - p&oslash;s all treffinfo inn i URL
					$treffinfo             = base64_encode(serialize($etttreff));
				}
        else { // men hvis ikke sender vi postID, bibtype, avdelingskode
					$enkelinfo['bibsystem']  = $mittsystem;
					$enkelinfo['postid']     = $etttreff['identifier'];
					$enkelinfo['bibkode']    = $library_id;
					$treffinfo               = base64_encode(serialize($enkelinfo));
				}

				if(stristr($single_item_url , "?")) { // Har allerede query variables
					$etttreff['permalink'] = $single_item_url . "&system=" . $mittsystem . "&enkeltpostinfo=" . $treffinfo;
				}
				else { // Dette er den f&oslash;rste
					$etttreff['permalink'] = $single_item_url . "?system=" . $mittsystem . "&enkeltpostinfo=" . $treffinfo;
				}

				$etttreff['url'] = $etttreff['permalink']; // vil gjerne bruke disse om hverandre
			}
		}
	}
}



$results = array();

if ( $result['count-items'] > 0) { // kan være tom
	foreach ( $result['items'] as $enkelttreff => &$treff) {
		// Verdier for hvert treff, som skal lagres i $results og sendes videre til results.php-malen
		$data = $Search->setItemArray($treff);
		if ((isset($treff['isbn'])) && (trim($treff['isbn']) != "")) {
			$altmedisbn = trim($treff['isbn']);
			if ((isset($treff['heftetbundet'])) && (trim($treff['heftetbundet']) != "")) {
				$altmedisbn .= " (" . $treff['heftetbundet'] . ")";
			}
			$data['isbn'] = "<strong>" . __('ISBN: ', 'inter-library-search-by-webloft') . "</strong>" . $altmedisbn . "\n";
		}

		if ((isset($treff['omfang'])) && (trim($treff['omfang']) != "")) {
			$data['omfang'] = "<strong>" . __('Omfang: ', 'inter-library-search-by-webloft') . "</strong>" . $treff['omfang'];
		}

		if ((isset($treff['originaltittel'])) && (trim($treff['originaltittel']) != "")) {
			$data['titteloriginal'] = "<strong>" . __('Originaltittel: ', 'inter-library-search-by-webloft') . "</strong>" . $treff['originaltittel'] . "\n";
		}


		// BESTAND I BIBLIOFIL

		// Finner vi alltid i 850 - men hvis ikke er det utilgjengelig

		/*
		i 850 finner vi:

		$a	Institution/location	Eiende bibliotek/avdeling
		$b	Sublocation/collection	Filial- avdelings- eller samlingskode
		$c	Shelving location	Hyllesignatur
		$f	Use restrictions	(Not in NORMARC)
		$h	Circulation status	(Not in NORMARC)
		$x	Date of circulation status	(Not in NORMARC)
		$y	Loan expiry date	(Not in NORMARC)

		*/

		// Bibliofil og Mikromarc er like mht. bestandsinfo, tenker jeg vi sier
		if ( $mittsystem == 'bibliofil' || $mittsystem == 'mikromarc' ){
			$tilgjengelig  = 0;
			$utlant        = 0;
			$utilgjengelig = 0;
			$begrenset     = 0;
			$bestandhtml   = '';

			if (is_array($treff['bestand'])) { // Bare hvis array
				foreach ($treff['bestand'] as $enkelteks) {
					@$status      = $enkelteks["h"];
					@$begrensning = $enkelteks	["f"];
					switch ($status) {
						case "0":
							if (($begrensning == "2") || ($begrensning == "3") || ($begrensning == "4") || ($begrensning == "6")) {
								$begrenset++;
							} else {
								$tilgjengelig++;
							}
							break;
						case "4":
							$utlant++;
							break;
						default:
							$utilgjengelig++;
							break;
					}
				}
			}
			$bestandhtml = "<br>\n";

			$totaleks = (int)$tilgjengelig + (int)$begrenset + (int)$utlant + (int)$utilgjengelig;


			if ($tilgjengelig > 0) {
				$data['status'] = 'ledig';
				$bestandhtml .= "<div class=\"tilgang_boks wl-catalog\">";
				$bestandhtml .= __('Ledig', 'inter-library-search-by-webloft') . '&nbsp;:&nbsp' . $tilgjengelig . "<br>\n";
				$bestandhtml .= "<div class=\"green dot\"></div>";
				$bestandhtml .= "</div>\n";
			} elseif (($tilgjengelig == 0) && (($begrenset + $utlant) > 0)) {
				$data['status'] = 'utlant';
				$bestandhtml .= "<div class=\"tilgang_boks\">";
				$bestandhtml .= __('Utl&aring;nt el.l.', 'inter-library-search-by-webloft') . "&nbsp;:&nbsp;" . ($begrenset + $utlant) . "<br>\n";
				$bestandhtml .= "<div class=\"orange dot\"></div>";
				$bestandhtml .= "</div>\n";
			} elseif ($utilgjengelig > 0) { // utilgjengelig
				$data['status'] = 'ikke-ledig';
				$bestandhtml .= "<div class=\"tilgang_boks\">";
				$bestandhtml .= __('Ikke ledig', 'inter-library-search-by-webloft') . "&nbsp;:&nbsp;" . $utilgjengelig . "<br>\n";
				$bestandhtml .= "<div class=\"red dot\"></div>";
				$bestandhtml .= "</div>\n";
			} else {
				$data['status'] = 'uklar';
				$bestandhtml .= "<div class=\"tilgang_boks\">";
				$bestandhtml .= __('Uklar bestand!', 'inter-library-search-by-webloft') . "<br>\n";
				$bestandhtml .= "<div class=\"orange dot\"></div>";
				$bestandhtml .= "</div>\n";
			}
		}

		// S&aring; bytter vi ut hvis vi har noe

		if ((isset($bestandhtml)) && ($bestandhtml != '')) {
			$data['bestand'] = $bestandhtml;
		}

		if ((isset($treff['pdfutdrag'])) && ($treff['pdfutdrag'] != "")) {
			$utdraghtml = '[<a target="_blank" href="' . $treff['pdfutdrag'] . '">' . __('Les utdrag', 'inter-library-search-by-webloft') . '</a>]' . "\n";
			$data['utdrag'] = $utdraghtml;
		} else {
			$data['utdrag'] = '';
		}

		$results[] = $data;
	}
}
else
{
	// trefflisten var tom
	$results = array();
}



// En siste ting: Hva er lenken til avansert s&oslash;k? Alts&aring; lenken inn til systemets egen s&oslash;keskjerm?

$avanserturl = $Search->setAdvancedQueryUrl();

include  ( getTemplatePath( 'results.php' ) ) ;
?>