<?php

// M&aring; ha tilgang til oversettelser
require_once dirname(__FILE__).'/../../../../../../wp-load.php';

// S&oslash;ker i kirjastokaista og returnerer treff som array etter v&aring;r standard

// Dette er malen for utskrift av ett treff:

$singlehtml = '<div class="wlkatalog_videoitem">' . "\n";
	$singlehtml .= 'embedkodeString' . "\n";
$singlehtml .= '</div>' . "\n\n";

/*************************************************************/

$rawurl = "http://www.kirjastokaista.fi/api/get_search_results/?search=<!QUERY!>&count=" . $makstreff;

$rawurl = str_replace ("<!QUERY!>" , $qsokeord , $rawurl); // sette inn s&oslash;keterm
$kirjastokaistatreff = '';

// LASTE TREFFLISTE SOM JSON
$jsonfile = get_content($rawurl);

$kirjastodata = json_decode ($jsonfile, TRUE);

// FINNE ANTALL TREFF

$antalltreff['kirjastokaista'] = $kirjastodata['count_total'];

// ... S&Aring; HVERT ENKELT TREFF
$teller = 0;

// kilde, tittel, url, bilde, beskrivelse, embedkode

foreach ($kirjastodata['posts'] as $entry) {

	$kirjastokaistatreff[$teller]['kilde'] = "Kirjastokaista";
	$kirjastokaistatreff[$teller]['tittel'] = $entry['title'];
	$kirjastokaistatreff[$teller]['url'] = $entry['url'];

	// Thumbnail
	if (isset($entry['attachments'][0]['images']['progression-slider']['url'])) {
		$kirjastokaistatreff[$teller]['bilde'] = $entry['attachments'][0]['images']['progression-slider']['url'];
	}

	// Beskrivelse
	$kirjastokaistatreff[$teller]['beskrivelse'] = '';
	if (trim($entry['author']['first_name']) != "") {
		$kirjastokaistatreff[$teller]['beskrivelse'] .= "<b>" . __('Av:', 'inter-library-search-by-webloft') . " </b>" . $entry['author']['first_name'] . " " . $entry['author']['last_name'] . ". ";
	} else {
		$kirjastokaistatreff[$teller]['beskrivelse'] = "<b>" . __('Av:', 'inter-library-search-by-webloft') . " </b>" . $entry['author']['nickname'] . ". ";
	}
	if (isset($entry['custom_fields']['duration'][0])) {
		$kirjastokaistatreff[$teller]['beskrivelse'] .= "<b>" . __('Lengde:', 'inter-library-search-by-webloft') . " </b>" . gmdate("H:i:s", $entry['custom_fields']['duration'][0]) . ". ";
	}

	$kirjastokaistatreff[$teller]['beskrivelse'] .= strip_tags($entry['excerpt']);

	// Emneord
	$tagg = '';

	foreach ($entry['tags'] as $taggen) {
		if (isset($taggen['title'])) {
			$tagg[] = $taggen['title'];
		}
	}

	if (is_array($tagg)) {
		$kirjastokaistatreff[$teller]['beskrivelse'] .= "<b>" . __('Emneord:', 'inter-library-search-by-webloft') . " </b>" . implode (" / " , $tagg) . ". ";
	}

	$kirjastokaistatreff[$teller]['embedkode'] = $entry['custom_fields']['videoembed_videoembed'][0];
	//$kirjastokaistatreff[$teller]['embedkode'] = str_replace ('width="550" height="315" ' , 'style="width: 367px; height: 210px;" ' , $kirjastokaistatreff[$teller]['embedkode']); // endre bredde p&aring; iframe

	$teller++;
} // SLUTT P&Aring; HVERT ENKELT TREFF

// Skrive ut ett og ett treff samt legge det til i samletrefflista

if (is_array($kirjastokaistatreff)) {
	echo '<div class="wlkatalog_horizontal_scroll">' . "\n";
	foreach ($kirjastokaistatreff as $enkelttreff) {
		$lydtreff[] = $enkelttreff; // legge til i samletreffliste

		// Skrive ut
		$outhtml = str_replace ("embedkodeString" , $enkelttreff['embedkode'] , $singlehtml);
//		$outhtml = str_replace ("urlString" , $enkelttreff['url'] , $outhtml);
//		$outhtml = str_replace ("tittelString" , $enkelttreff['tittel'] , $outhtml);
//		$outhtml = str_replace ("beskrivelseString" , $enkelttreff['beskrivelse'] , $outhtml);

		echo $outhtml;
	}
	echo '</div>' . "\n";
	echo '<br>' . __('Flere treff fra Kirjastokaista?', 'inter-library-search-by-webloft');
	echo '<br><br>';
} else { // Ingen treff...
	echo __('Ingen treff i Kirjastokaista...', 'inter-library-search-by-webloft');
}
?>
