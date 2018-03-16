<?php

// M&aring; ha tilgang til oversettelser
require_once dirname(__FILE__).'/../../../../../../wp-load.php';

// S&oslash;ker i Filmbib og returnerer treff som array etter v&aring;r standard

// Dette er malen for utskrift av ett treff:

/*
DETTE VAR HORISONTAL VISNING - DET HAR VI G&Aring;TT BORT FRA
$singlehtml = '<div class="wlkatalog_imageitem">' . "\n";
	$singlehtml .= '<a target="_blank" href="urlString">' . "\n";
	$singlehtml .= '<img src="bildeString" alt="tittelString" />';
	$singlehtml .= '<div class="wlkatalog_imageitem_overlay">' . "\n";
		$singlehtml .= '<h2>tittelString</h2><br />beskrivelseString';
	$singlehtml .= '</div>' . "\n";
	$singlehtml .= '</a>' . "\n";
$singlehtml .= '</div>' . "\n\n";
*/

$singlehtml = '<li>' . "\n";
	$singlehtml .= '<div class="omslag">' . "\n";
		$singlehtml .= '<a target="_blank" href="urlString">' . "\n";
		$singlehtml .= '<img class="wlkatalog_bokitem_bilde" src="omslagString" alt="tittelString" />' . "\n";
		$singlehtml .= "</a>" . "\n";
	$singlehtml .= '</div>';
	$singlehtml .= '<h3><a target="_blank" href="urlString">' . "\n";
	$singlehtml .= 'tittelString' . "\n";
	$singlehtml .= '</a></h3>' . "\n";
	$singlehtml .= '<p>beskrivelseString' . "\n";
	$singlehtml .= '<br><b>' . __('Kilde:', 'inter-library-search-by-webloft') . '</b> Filmbib.no</p>' . "\n";
	$singlehtml .= '<div style="clear:both;"></div>' . "\n";
$singlehtml .= '</li>' . "\n";

/********************************************************************/

$qsokeord = str_replace ("*" , "" , $qsokeord); // kan ikke trunkere i Filmbib!

$rawurl = "http://api.dvnor.no/vod/filmbib/search?q=<!QUERY!>";
$rawurl = str_replace ("<!QUERY!>" , $qsokeord , $rawurl); // sette inn s&oslash;keterm

$curl = curl_init();
curl_setopt ($curl, CURLOPT_URL, $rawurl);
curl_setopt ($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt ($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
//curl_setopt ($curl, CURLOPT_HTTPHEADER, array('X-Authorization: 941c8d233fa83806533306e3d27d6ece4f01932f'));
curl_setopt ($curl, CURLOPT_HTTPHEADER, array('X-Authorization: b1f0427aad43564435d3b5f4498a18acdd1f5a39'));
$filmbibber = curl_exec($curl);

// JSON jepp
$mydata = json_decode ($filmbibber);

// ... S&Aring; HVERT ENKELT TREFF
$teller = 0;
$filmbibtreff = '';

// kilde, tittel, url, bilde, beskrivelse, embedkode

foreach ($mydata->data as $entry) {
	$filmbibtreff[$teller]['kilde'] = "Filmbib";
	$filmbibtreff[$teller]['url'] = $entry->url;
	$filmbibtreff[$teller]['tittel'] = $entry->title;
	$filmbibtreff[$teller]['beskrivelse'] = trunc(strip_tags($entry->synopsis) , 50);
	$filmbibtreff[$teller]['bilde'] = $entry->image->medium;

	$teller++;

} // Slutt p&aring; hvert enkelt treff

// Skrive ut ett og ett treff samt legge det til i samletrefflista

if (is_array($filmbibtreff)) { // Hvis vi har treff

	echo '<ul class="ils-results">';

	// echo '<div class="wlkatalog_horizontal_scroll">' . "\n";
	// BARE VED HORISONTAL VISNING

	foreach ($filmbibtreff as $enkelttreff) {
		$videotreff[] = $enkelttreff; // legge til i samletreffliste

		// Skrive ut
		$outhtml = str_replace ("omslagString" , $enkelttreff['bilde'] , $singlehtml);
		$outhtml = str_replace ("urlString" , $enkelttreff['url'] , $outhtml);
		$outhtml = str_replace ("tittelString" , $enkelttreff['tittel'] , $outhtml);
		$outhtml = str_replace ("beskrivelseString" , $enkelttreff['beskrivelse'] , $outhtml);

		echo $outhtml;
	}

	// echo '</div>' . "\n";
	// BARE VED HORISONTAL VISNING

	echo '</ul>' . "\n\n";

} else { // Oh noes, ingen treff
	echo __('Beklager, ingen treff!', 'inter-library-search-by-webloft');
}

?>
