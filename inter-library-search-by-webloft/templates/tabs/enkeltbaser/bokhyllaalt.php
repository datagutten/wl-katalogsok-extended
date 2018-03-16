<?php

// M&aring; ha tilgang til oversettelser


/*
S&oslash;ker i hele Bokhylla og legger til treff i $bokhyllatreff
Bruker $qsokeord som ubehandlet s&oslash;keterm
Trenger url, bilde, tittel, forfatter og kilde... flere?
*/

$makstreff = 50; // Flatt tak

// F&oslash;rst mal for visning av treff:

// M&Aring; TILPASSES

$singlehtml = '<li>' . "\n";
$singlehtml .= '<div class="omslag">' . "\n";
$singlehtml .= '<a target="_blank" href="urlString">' . "\n";
$singlehtml .= '<img src="bildeString" alt="tittelString" title="tittelString" >' . "\n";
$singlehtml .= '</a>' . "\n";
$singlehtml .= '</div>' . "\n";
$singlehtml .= '<h3>' . "\n";
$singlehtml .= '<a target="_blank" href="urlString">tittelString</a>' . "\n";
$singlehtml .= '</h3>' . "\n";
$singlehtml .= '<div class="ansvar">forfatterString</div>' . "\n";
$singlehtml .= '<p>beskrivelseString' . "\n";
$singlehtml .= '<br><b>' . __('Kilde:', 'inter-library-search-by-webloft') . '</b> bokhylla.no<br/>';
$singlehtml .= 'pdflenkeString</p>';
$singlehtml .= '<div style="clear:both;"></div>' . "\n";
$singlehtml .= wlils_ribbon(__('Les p&aring; nett!', 'inter-library-search-by-webloft') , 'urlString' , '#a00');
$singlehtml .= '</li>' . "\n";

// Get Search
$search_string = urlencode(cleanValue($qsokeord) );


$rawurl = WL_Search::buildQuery(
		NB_NO_SEARCH,
		$query_args=
			array(
				'q' => $search_string,
				'fq' => array(
					'mediatype:('. utf8_decode("Bøker") .')',
					'contentClasses:(bokhylla%20OR%20public)',
					'digital:Ja'
				),
				'itemsPerPage' => $makstreff,
				'ft' => false
			)
		);


// $rawurl = "http://www.nb.no/services/search/v2/search?q=<!QUERY!>&fq=mediatype:(" . utf8_decode("Bøker") . ")&fq=contentClasses:(bokhylla%20OR%20public)&fq=digital:Ja&itemsPerPage=" . $makstreff . "&ft=false";
// $rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl); // sette inn s&oslash;keterm


$bokhyllaalttreff = '';

// LASTE TREFFLISTE SOM XML
$xml = get_content($rawurl);

if(substr($xml, 0, 5) == "<?xml") { // vi fikk en XML-fil tilbake

	$xmldata = simplexml_load_string($xml);

	$antalltreff['bokhylla'] = (int) substr(stristr($xmldata->subtitle, " of ") , 4);

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

			$bokhyllaalttreff[$teller]['tittel'] = (string) $entry->title;
			$bokhyllaalttreff[$teller]['forfatter'] = (string) $nb->namecreator;

			// UTGITT

			unset ($utgitt);
			if (isset($childxmldata->originInfo->place[1])) {
				$utgitt[] = $childxmldata->originInfo->place[1];
			}

			if (isset($childxmldata->originInfo->publisher)) {
				$utgitt[] = $childxmldata->originInfo->publisher;
			}

			if (isset($childxmldata->originInfo->dateIssued[0])) {
				$utgitt[] = $childxmldata->originInfo->dateIssued[0];
			}
			$bokhyllaalttreff[$teller]['utgitt'] = implode (" " , $utgitt);

			if (isset($childxmldata->physicalDescription->extent)) {
				$bokhyllaalttreff[$teller]['omfang'] = (string) $childxmldata->physicalDescription->extent;
			}

			// BESKRIVELSE
			$bokhyllaalttreff[$teller]['beskrivelse'] = "<b>" . __('Utgitt:', 'inter-library-search-by-webloft') . " </b>" . $bokhyllaalttreff[$teller]['utgitt'] . ". ";
			$bokhyllaalttreff[$teller]['beskrivelse'] .= "<b>" . __('Omfang:', 'inter-library-search-by-webloft') . " </b>" . $bokhyllaalttreff[$teller]['omfang'] . ". ";


			if (isset($childxmldata->note)) {
				//$bokhyllatreff[$teller]['beskrivelse'] .= $childxmldata->note . ". ";
			}

			// URN
			// BOKOMSLAG, SE http://www-sul.stanford.edu/iiif/image-api/1.1/#parameters
			if (stristr($nb->urn , ";")) {
				$tempura = explode (";" , $nb->urn);
				$urn = trim($tempura[1]); // vi tar nummer 2
			} else {
				$urn = $nb->urn[0];
			}

			$delavurn = substr($urn , 8);

			// if ($urn != "") {
			//
			// 	$bokhyllaalttreff[$teller]['bilde'] = "http://bokforsider.webloft.no/urn/" . $delavurn . ".jpg";
			// } else {
			// 	$bokhyllaalttreff[$teller]['bilde'] = $generiskbokomslag; // DEFAULTOMSLAG
			// }

			$bokhyllaalttreff[$teller]['bilde'] = getIconUrl('ikke_digital.png'); // DEFAULTOMSLAG

			$bokhyllaalttreff[$teller]['url'] = "http://urn.nb.no/" . $urn;
			$bokhyllaalttreff[$teller]['id'] = $urn;

			// Finnes PDF?

			if (((string) $nb->digital == "true") && stristr((string) $nb->contentclasses , "public")) {
				$bokhyllaalttreff[$teller]['pdf'] = "http://www.nb.no/nbsok/content/pdf?urn=URN:NBN:" . $delavurn;
				$bokhyllaalttreff[$teller]['pdflenke'] = '<a target="_blank" href="' . $bokhyllaalttreff[$teller]['pdf'] . '"><img src="'.getIconUrl('pdf.png').'" alt="' . __('Last ned som PDF', 'inter-library-search-by-webloft') . '" /></a>';
			}



			$teller++;
		}
	} // SLUTT P&Aring; HVERT ENKELT TREFF

} // slutt p&aring; "vi fikk XML-fil tilbake

// Printe ut og legge til i felles treff

if (is_array($bokhyllaalttreff)) {
	echo '<ul class="ils-results">';
	foreach ($bokhyllaalttreff as $enkelttreff) {
		$bokhyllatreff[] = $enkelttreff; // legge til

		// Skrive ut
		$outhtml = str_replace ("urlString" , $enkelttreff['url'] , $singlehtml);
		$outhtml = str_replace ("bildeString" , $enkelttreff['bilde'] , $outhtml);
		$outhtml = str_replace ("tittelString" , $enkelttreff['tittel'] , $outhtml);
		$outhtml = str_replace ("forfatterString" , $enkelttreff['forfatter'] , $outhtml);
		$outhtml = str_replace ("beskrivelseString" , $enkelttreff['beskrivelse'] , $outhtml);
		if (@trim($enkelttreff['pdflenke']) != '') {
			$outhtml = str_replace ("pdflenkeString" , $enkelttreff['pdflenke'] , $outhtml);
		} else {
			$outhtml = str_replace ("pdflenkeString" , '' , $outhtml);
		}

		echo $outhtml;
	}
	echo '</ul>' . "\n\n";

	if ($antalltreff['bokhylla'] > $makstreff) {
		$rawurl = "http://www.nb.no/nbsok/search?action=search&mediatype=b&oslash;ker&format=Digitalt tilgjengelig&CustomDateFrom=&CustomDateTo=&pageSize=50&sortBy=ranking&searchString=<!QUERY!>%20%26ft=false";
		$rawurl = str_replace ("<!QUERY!>" , $search_string , $rawurl);

// KOMMET HIT

		echo (sprintf (esc_html__('Funnet %d treff.', 'inter-library-search-by-webloft') , $antalltreff['bokhylla'])) . '<a target="_blank" href="' . $rawurl . '">' . __('Se alle treffene p&aring; nb.no', 'inter-library-search-by-webloft') . '</a>!';
	}
} else { // Ingen treff
	echo __('Beklager, ingen treff!', 'inter-library-search-by-webloft');
}
