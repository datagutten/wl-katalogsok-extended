<?php

// M&aring; ha tilgang til oversettelser
require_once dirname(__FILE__).'/../../../../../../wp-load.php';

// S&oslash;ker i bokselskap og returnerer treff som array etter v&aring;r standard

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


// S&aring; selve s&oslash;ket...


$makstreff = 100; // vet ikke helt hvor vi skal sette dette?

$search_string = $qsokeord;
$invertert = 'ALDRI_I_LIVET_MIN_VENN'; // unng&aring;r warning ved tom string

if (stristr($search_string , ",")) { // det er s&oslash;kt i invertert form, m&aring; ha mellomrom
	$search_string = str_replace ("," , ", " , $search_string);
	$search_string = str_replace (",  " , ", " , $search_string); // var det allerede?
} else { // ikke invertert form, vi lager en invertert
	if (stristr($search_string , " ")) { // m&aring; være flere termer for &aring; invertere
	$vazelina = explode (" " , $search_string);
	$first = array_pop ($vazelina);
	$second = implode (" " , $vazelina);
	$invertert = trim ($first) . ", " . trim($second);
	}
}

$xmlfilnavn = dirname(__FILE__).'/bokselskap_publiseringsliste_2016-01-25.xml';

$xmldata = simplexml_load_file($xmlfilnavn);

	$teller = 0;
	$bokselskaptreff = '';

	foreach ($xmldata->text->body->div->list->item as $entry) {
	$ishit = 0;
	// HAR VI ET TREFF?

	if (stristr($qsokeord , "\"") || stristr($qsokeord , "%22")) { // FRASES&Oslash;K!
		$search_string = trim(str_replace("\"" , "" , $qsokeord)); // Fjerne fnutter
		$search_string = trim(str_replace("%22" , "" , $search_string)); // Metode #2
		// S&aring; kan s&oslash;ketermen bare g&aring; gjennom kverna under


	} else { // Men hvis ikke frases&oslash;k

		// RUTINE FOR AND-S&Oslash;K HER N&Aring;R DEN TID KOMMER
	}

	if (mb_stristr($entry->ref->name[0] , $search_string) || mb_stristr($entry->ref->title , $search_string) || mb_stristr($entry->ref->name[0] , $invertert) || mb_stristr($entry->ref->title , $invertert)) {
		$ishit = 1; // Er treff
	}



	if ($ishit == "1") { // VI HAR ET TREFF
		$bokselskaptreff[$teller]['url'] = (string) $entry->ref->attributes()->target;
		$bokselskaptreff[$teller]['forfatter'] = (string) $entry->ref->name[0];
		if (isset($entry->ref->name[1])) {
			$bokselskaptreff[$teller]['utgitt'] = (string) $entry->ref->name[1];
		}
		$bokselskaptreff[$teller]['tittel'] = (string) $entry->ref->title;
		$bokselskaptreff[$teller]['aar'] = (string) $entry->ref->date;
		if ($bokselskaptreff[$teller]['aar'] != "") {
			$bokselskaptreff[$teller]['tittel'] .= " (" . $bokselskaptreff[$teller]['aar'] . ")";
		}
		$bokselskaptreff[$teller]['isbn'] = (string) $entry->attributes("xml",true)->id;
		$bokselskaptreff[$teller]['isbn'] = str_replace ("isbn" , "" , $bokselskaptreff[$teller]['isbn']);
		$bokselskaptreff[$teller]['omslag'] = (string) $entry->p->ref->attributes()->target;
		$bokselskaptreff[$teller]['beskrivelse'] = "<strong>" . __('Utgitt', 'inter-library-search-by-webloft') . "</strong>: " . $bokselskaptreff[$teller]['utgitt'] . ". ";
		$bokselskaptreff[$teller]['beskrivelse'] .= "<br><strong>" . __('Kilde', 'inter-library-search-by-webloft') . "</strong>: bokselskap.no" . ". ";
		$bokselskaptreff[$teller]['beskrivelse'] .= "<br><strong>" . __('ISBN', 'inter-library-search-by-webloft') . "</strong>: " . $bokselskaptreff[$teller]['isbn'];

		$teller++;
	}


	} // SLUTT P&Aring; HVERT ITEM

// Printe ut og legge til i felles treff

$eboktreff = array();
if (is_array($bokselskaptreff)) {
	echo '<ul class="ils-results">';

	foreach ($bokselskaptreff as $enkelttreff) {
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
else { // Ingen treff
	// _e('Ingen treff!', 'inter-library-search-by-webloft');
}
?>
