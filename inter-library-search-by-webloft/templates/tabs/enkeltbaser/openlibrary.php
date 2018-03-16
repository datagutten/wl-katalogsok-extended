<?php

// M&aring; ha tilgang til oversettelser
require_once dirname(__FILE__).'/../../../../../../wp-load.php';

// S&oslash;ker i Openlibrary og returnerer treff som array etter v&aring;r standard

// Trenger url, omslagsbilde, tittel, forfatter og kilde... flere?

// F&oslash;rst mal for visning av treff:

$singlehtml = '<li>' . "\n";
$singlehtml .= wlils_ribbon(__('Last ned!', 'inter-library-search-by-webloft') , 'urlString' , '#00a');
	$singlehtml .= '<a target="_blank" href="urlString">' . "\n";
	$singlehtml .= '<img class="wlkatalog_bokitem_bilde" src="omslagString" alt="tittelString" />' . "\n";
	$singlehtml .= "</a>" . "\n";
	$singlehtml .= '<div class="eboktreff_beskrivelse">' . "\n";
		$singlehtml .= '<h3><a target="_blank" href="urlString">' . "\n";
		$singlehtml .= 'tittelString' . "\n";
		$singlehtml .= '</a></h3>' . "\n";
		$singlehtml .= '<div class="ansvar">forfatterString</div>' . "\n";
		$singlehtml .= '<p>beskrivelseString</p>' . "\n";
	$singlehtml .= '</div>' . "\n";
$singlehtml .= '</li>' . "\n\n";


// S&oslash;ke i Openlibrary

$search_string = $qsokeord;

if (stristr($search_string , ", ")) { // det er s&oslash;kt i invertert form, men bare treff i vanlig form
	$wilbury = explode ("," , $search_string);
	$first = array_pop ($wilbury); // tar den siste etter komma
	$second = implode (" " , $wilbury); // tar det foran komma
	$search_string = trim ($first) . " " . trim ($second);
rop ($search_string);
}

$makstreff = 100; // vet ikke helt hvor vi skal sette dette?
$search_string = str_replace ("\"" , "", $search_string); // fnutter f&aring;r det til &aring; henge
$search_string = str_replace (" " , "+", $search_string);


$rawurl = "https://openlibrary.org/search.json?q=<!QUERY!>&has_fulltext=true";
$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn s&oslash;keterm

$resultsfile = get_content($rawurl);
$allresults = json_decode($resultsfile);


$results = _is($allresults,'docs');

// Hvert enkelt treff

$teller = 0;
$totalt = 0;

foreach ($results as $treff) {
	// _log($treff);
	$year = null;
	if ($treff->public_scan_b == '1') {
		$totalt++;
		if ($teller < $makstreff) {
		$openlibrarytreff[$teller]['tittel'] = $treff->title;
			if (@$treff->subtitle != '') {
				$openlibrarytreff[$teller]['tittel'] .= " : " . $treff->subtitle;
			}
			@$openlibrarytreff[$teller]['forfatter'] = $treff->author_name[0];
			if (trim($openlibrarytreff[$teller]['forfatter']) == "") {
				$openlibrarytreff[$teller]['forfatter'] = "N.N.";
			}
			@$openlibrarytreff[$teller]['omslag'] = "https://covers.openlibrary.org/b/olid/" . $treff->cover_edition_key . "-M.jpg";
			$openlibrarytreff[$teller]['url'] = "https://openlibrary.org" . $treff->key;
			$year = _is($treff, 'first_publish_year');

			if ( $year  ) {
				$openlibrarytreff[$teller]['tittel'] .= " (" . $year . ")";
			}
		$openlibrarytreff[$teller]['beskrivelse'] = "<strong>" . __('Forfatter:', 'inter-library-search-by-webloft') . "</strong> " . $openlibrarytreff[$teller]['forfatter'] . "<br>";
		$openlibrarytreff[$teller]['beskrivelse'] .= "<strong>" . __('Kilde:', 'inter-library-search-by-webloft') . "</strong> Open Library.";

		$teller++;
		}
	}
}

$openlibraryantalltreff = $totalt;

// Printe ut og legge til i felles treff

if (@is_array($openlibrarytreff)) {
	echo '<ul class="ils-results">';

	foreach ($openlibrarytreff as $enkelttreff) {
		$eboktreff[] = $enkelttreff; // legge til

		// Skrive ut

		$outhtml = str_replace ("urlString" , $enkelttreff['url'] , $singlehtml);
		$outhtml = str_replace ("omslagString" , $enkelttreff['omslag'] , $outhtml);
		$outhtml = str_replace ("tittelString" , $enkelttreff['tittel'] , $outhtml);
		$outhtml = str_replace ("forfatterString" , $enkelttreff['forfatter'] , $outhtml);
		$outhtml = str_replace ("beskrivelseString" , $enkelttreff['beskrivelse'] , $outhtml);

		echo $outhtml;
	}
	echo "</ul>";
}
else {
	echo __('Beklager, ingen treff!', 'inter-library-search-by-webloft');
}
?>
