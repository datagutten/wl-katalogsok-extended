<?php

// M&aring; ha tilgang til oversettelser
require_once dirname(__FILE__).'/../../../../../../wp-load.php';

// S&oslash;ker i Nasjonalbibliotekets EPUB-b&oslash;ker og returnerer treff som array etter v&aring;r standard

// DENNE M&Aring; TESTES, ER IKKE FERDIG. Bl.a. mangler omslagsbilde

// Trenger url, omslagsbilde, tittel, forfatter og kilde... flere?

// F&oslash;rst mal for visning av treff:

$singlehtml = '<div class="wlkatalog_bokitem">' . "\n";
	$singlehtml .= '<a href="urlString">' . "\n";
	$singlehtml .= '<img class="wlkatalog_bokitem_bilde" src="omslagString" alt="tittelString" />' . "\n";
	$singlehtml .= "</a>" . "\n";
	$singlehtml .= '<div class="eboktreff_beskrivelse">' . "\n";
		$singlehtml .= '<a href="urlString">' . "\n";
		$singlehtml .= '<h2>tittelString</h2>' . "\n";
		$singlehtml .= '</a>' . "\n";
		$singlehtml .= '<h3>forfatterString</h3>' . "\n";
		$singlehtml .= '<p>beskrivelseString</p>' . "\n";
	$singlehtml .= '</div>' . "\n";
$singlehtml .= '<br style="clear: both;">' . "\n";
$singlehtml .= '</div>' . "\n\n";


// S&aring; selve s&oslash;ket...

$makstreff = 100; // vet ikke helt hvor vi skal sette dette?

$rawurl = "http://www.nb.no/services/search/v2/search?q=<!QUERY!>&fq=contentClasses:epub&fq=digital:Ja&itemsPerPage=" . $makstreff;

$rawurl = str_replace ("<!QUERY!>" , $qsokeord , $rawurl); // sette inn s&oslash;keterm
$nbepubtreff = '';

// LASTE TREFFLISTE SOM XML
$xmlfile = get_content($rawurl);

if(substr($xmlfile, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake
	$xmldata = simplexml_load_string($xmlfile);

	// ... S&Aring; HVERT ENKELT TREFF
	$teller = 0;
	foreach ($xmldata->entry as $entry) {
		if ($teller < $makstreff) {

			// METADATA SOM XML FOR DETTE TREFFET
			$childxml = ($entry->link[0]->attributes()->href); // Dette er XML med metadata
			$xmlfile = get_content($childxml);
			$childxmldata = simplexml_load_string($xmlfile);
			$namespaces = $entry->getNameSpaces(true);
			$nb = $entry->children($namespaces['nb']);

			// FINNE URN
			if (stristr($nb->urn , ";")) {
				$tempura = explode (";" , $nb->urn);
				$urn = trim((string) $tempura[1]); // vi tar nummer 2
			} else {
				$urn = (string) $nb->urn;
			}

			$nbepubtreff[$teller]['bilde'] = "http://www.nb.no/services/image/resolver?url_ver=geneza&urn=" . $urn . "_0001&maxLevel=5&level=4&col=0&row=0&resX=9000&resY=9000&tileWidth=1024&tileHeight=1024";

			$nbepubtreff[$teller]['url'] = "http://urn.nb.no/" . $urn;
			if ((isset($entry->title)) && ($entry->title != '')) {
				$nbepubtreff[$teller]['tittel'] = (string) $entry->title;
			} else {
				$nbepubtreff[$teller]['tittel'] = __('(Uten tittel)', 'inter-library-search-by-webloft');
			}

			if ((isset($nb->mainentry)) && ($nb->mainentry != '')) {
				$nbepubtreff[$teller]['ansvar'] = (string) $nb->mainentry;
			} else {
				$nbepubtreff[$teller]['ansvar'] = 'N.N.';
			}

			$nbepubtreff[$teller]['beskrivelse'] = ''; // ikke beskrivelse for disse
			$nbepubtreff[$teller]['kilde'] = __('Nasjonalbiblioteket - EPUB', 'inter-library-search-by-webloft');

			$teller++;
		}
	} // SLUTT P&Aring; HVERT ENKELT TREFF

} // Slutt p&aring; "vi fikk XML tilbake"


// Printe ut og legge til i felles treff

if (is_array($nbepubtreff)) {
	foreach ($nbepubtreff as $enkelttreff) {
		$eboktreff[] = $enkelttreff; // legge til

		// Skrive ut
		$outhtml = str_replace ("urlString" , $enkelttreff['url'] , $singlehtml);
		$outhtml = str_replace ("omslagString" , "http://lorempixel.com/300/400/" , $outhtml);
		$outhtml = str_replace ("tittelString" , $enkelttreff['tittel'] , $outhtml);
		$outhtml = str_replace ("forfatterString" , $enkelttreff['forfatter'] , $outhtml);
		$outhtml = str_replace ("beskrivelseString" , $enkelttreff['beskrivelse'] , $outhtml);

		echo $outhtml;
	}
} else { // Ingen treff
	echo __('Ingen treff fra EPUB hos Nasjonalbiblioteket...', 'inter-library-search-by-webloft');
}
?>
