<?php

// M&aring; ha tilgang til oversettelser
//require_once dirname(__FILE__).'/../../../../../../wp-load.php';

// S&oslash;ker i ebokbib og returnerer treff som array etter v&aring;r standard
// Trenger url, omslagsbilde, tittel, forfatter og kilde... flere?
// F&oslash;rst mal for visning av treff:
$singlehtml = '<div class="wlkatalog_bokitem">' . "\n";
	$singlehtml .= '<a target="_blank" href="urlString">' . "\n";
	$singlehtml .= '<img class="wlkatalog_bokitem_bilde" src="omslagString" alt="tittelString" />' . "\n";
	$singlehtml .= "</a>" . "\n";
	$singlehtml .= '<div class="eboktreff_beskrivelse">' . "\n";
		$singlehtml .= '<div class="topphoyre"><a target="_blank" href="pdfString"><img src="'.getIconUrl('pdf.png').'" alt="' . __('Les utdrag', 'inter-library-search-by-webloft') . '" /><br>' . __('Utdrag', 'inter-library-search-by-webloft') . '</a><br>' . "\n";
		$singlehtml .= '<a target="_blank" href="urlString"><img src="'.getIconUrl('ebokbib.png').'" alt="' . __('L&aring;n e-bok', 'inter-library-search-by-webloft') . '" /><br>' . __('L&aring;n e-bok', 'inter-library-search-by-webloft') . '</a></div>';
		$singlehtml .= '<a target="_blank" href="urlString">' . "\n";
		$singlehtml .= '<h2>tittelString</h2>' . "\n";
		$singlehtml .= '</a>' . "\n";
		$singlehtml .= '<h3>forfatterString</h3>' . "\n";
		$singlehtml .= '<p>metadataString</p>' . "\n";
		$singlehtml .= '<p>beskrivelseString</p>' . "\n";
	$singlehtml .= '</div>' . "\n";
$singlehtml .= '<br style="clear: both;">' . "\n";
$singlehtml .= '</div>' . "\n\n";


// S&aring; selve s&oslash;ket...


$makstreff = 100; // vet ikke helt hvor vi skal sette dette?
$search_string = str_replace ("%22" , "", $qsokeord);
$search_string = urldecode ($search_string);
$url = "http://ebokbib.no/cgi-bin/sru-ebokbib?operation=searchRetrieve&query=(norzig.possessingInstitution=2020000+AND+<!QUERY!>)"; // BRUKER AKERSHUS FYLKESBIB. FOREL&Oslash;PIG

//$url = "http://ebokbib.no/cgi-bin/sru-ebokbib?operation=searchRetrieve&query=cql.anywhere=<!QUERY!>norzig.possessingInstitution=2060000"

$url = str_replace ("<!QUERY!>" , $search_string , $url);
	$sru_datafil = get_content($url);
	$sru_data    = simplexml_load_string($sru_datafil);
	$namespaces = $sru_data->getNameSpaces(true);
	$srw        = $sru_data->children($namespaces['SRU']); // alle som er srw:ditten og srw:datten

	// S&aring; ta selve filen og plukke ut det vi skal ha

	$hepphepp = str_replace("marcxchange:", "", $sru_datafil);
	$hepphepp = strip_tags($hepphepp, "<record><leader><controlfield><datafield><subfield>");
	$hepphepp = stristr($hepphepp, "<record");

	$newfile = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	$newfile .= "<collection>\n";
	$newfile .= $hepphepp;
	$newfile .= "</collection>";

	// Retrieve a set of MARC records from a file

	require_once 'File/MARCXML.php';

	$journals = new File_MARCXML($newfile, File_MARC::SOURCE_STRING);
	// Iterate through the retrieved records

	$totalhtml  = '';
	$pendel     = 0;
	$hitcounter = 0;
	$treff      = '';

	while ($record = $journals->next()) {

		// initialize variables


		if ($record->getField("001")) {
			$identifier = $record->getField("001");
			$treff[$hitcounter]['identifier'] = trim(substr($identifier, 5)); // fjerne feltkoden i starten
			$treff[$hitcounter]['url'] = "http://open.ebokbib.no/cgi-bin/sendvidere?mode=ebokbib&tnr=" . $treff[$hitcounter]['identifier'];
		}


		if ($record->getField("245")) {
			$tittel                          = $record->getField("245")->getSubfield("a");
			$treff[$hitcounter]['tittel']    = substr($tittel, 5); // fjerne feltkoden i starten
			$subtittel                       = $record->getField("245")->getSubfield("b");
			$treff[$hitcounter]['subtittel'] = substr($subtittel, 5); // fjerne feltkoden i starten
			if ($record->getField("245")->getSubfield("c")) {
				$ansvar                      = $record->getField("245")->getSubfield("c");
				$treff[$hitcounter]['ansvarsangivelse'] = substr($ansvar, 5); // fjerne feltkoden i starten
			}
		}

		if ($record->getField("574")) { // Originaltittel
			$originaltittel = $record->getField("574")->getSubfield("a");
			$originaltittel = substr($originaltittel, 5); // fjerne feltkoden i starten
			$originaltittel = str_ireplace ("originaltittel:" , "", $originaltittel);
			$originaltittel = str_ireplace ("originaltittel :" , "", $originaltittel);
			$originaltittel = str_ireplace ("originaltitler:" , "", $originaltittel);
			$originaltittel = str_ireplace ("originaltitler :" , "", $originaltittel);
			$treff[$hitcounter]['originaltittel'] = trim($originaltittel);
		}

		if ($record->getField("100")) {
			$forfatter                       = $record->getField("100")->getSubfield("a");
			$treff[$hitcounter]['forfatter'] = substr($forfatter, 5); // fjerne feltkoden i starten
			if ($record->getField("100")->getSubfield("d")) {
				$forfatterliv                = $record->getField("100")->getSubfield("d");
				$treff[$hitcounter]['forfatterliv'] = substr($forfatterliv, 5); // fjerne feltkoden i starten
			}
		}

		if ($record->getField("110")) {
			$korporasjon                       = $record->getField("110")->getSubfield("a");
			$treff[$hitcounter]['korporasjon'] = substr($korporasjon, 5); // fjerne feltkoden i starten
		}

		if ($record->getField("20")) {
			$isbn                       = $record->getField("20")->getSubfield("a");
			$treff[$hitcounter]['isbn'] = substr($isbn, 5); // fjerne feltkoden i starten
			if ($record->getField("20")->getSubfield("b")) {
				$heftetbundet = $record->getField("20")->getSubfield("b");
				$treff[$hitcounter]['heftetbundet'] = substr($heftetbundet, 5); // fjerne feltkoden i starten
			}
		}

		if ($record->getField("520")) {
			$beskrivelse                       = $record->getField("520")->getSubfield("a");
			$treff[$hitcounter]['beskrivelse'] = substr($beskrivelse, 5); // fjerne feltkoden i starten
		}

		if ($record->getField("260")) {
			$utgitthvor                       = $record->getField("260")->getSubfield("a");
			$treff[$hitcounter]['utgitthvor'] = substr($utgitthvor, 5);
			$utgitthvem                       = $record->getField("260")->getSubfield("b");
			$treff[$hitcounter]['utgitthvem'] = substr($utgitthvem, 5);
			$utgittaar                        = $record->getField("260")->getSubfield("c");
			$utgittaar                        = substr($utgittaar, 5);
			$utgittaar                        = str_replace("[", "", $utgittaar); // disse to linjene fjerner [ og ] i &aring;rstall
			$treff[$hitcounter]['utgittaar']  = str_replace("]", "", $utgittaar);
			$utgittaar                        = str_replace("<", "", $utgittaar); // disse to linjene fjerner < og > i &aring;rstall
			$treff[$hitcounter]['utgittaar']  = str_replace(">", "", $utgittaar);

		}

		if ($record->getField("300")) { // omfang
			$omfang = $record->getField("300")->getSubfield("a");
			$omfang = substr($omfang, 5);
			if ($record->getField("300")->getSubfield("b")) {
				$cheese = $record->getField("300")->getSubfield("b");
				$cheese = substr($cheese, 5);
				$omfang .= " : " . $cheese;
			}
		$treff[$hitcounter]['omfang'] = $omfang;
		}

		// Ansvarsangivelse

		if (isset($treff[$hitcounter]['ansvarsangivelse'])) {
			$treff[$hitcounter]['opphav'] = $treff[$hitcounter]['ansvarsangivelse'];
		}

		if (isset($treff[$hitcounter]['forfatter'])) {
			$treff[$hitcounter]['opphav'] = $treff[$hitcounter]['forfatter'];
			if (isset($treff[$hitcounter]['forfatterliv'])) {
				$treff[$hitcounter]['opphav'] .= " (" . $treff[$hitcounter]['forfatterliv'] . ")";
			}
		}

		if (isset($treff[$hitcounter]['korporasjon'])) {
			$treff[$hitcounter]['opphav'] = $treff[$hitcounter]['korporasjon'];
		}

		// Tittel

		$treff[$hitcounter]['tittelinfo'] = $treff[$hitcounter]['tittel'];
		if ($treff[$hitcounter]['subtittel'] != '') {
			$treff[$hitcounter]['tittelinfo'] .= " : " . $treff[$hitcounter]['subtittel'];
		}

		// Sette sammen metadatainfo til &oslash;verst

		if ((isset($treff[$hitcounter]['utgitthvem'])) && ($treff[$hitcounter]['utgitthvem'] != '')) {
			$temputgitt[] = $treff[$hitcounter]['utgitthvem'];
		}

		if ((isset($treff[$hitcounter]['utgitthvor'])) && ($treff[$hitcounter]['utgitthvor'] != '')) {
			$temputgitt[] = $treff[$hitcounter]['utgitthvor'];
		}

		if ((isset($treff[$hitcounter]['utgittaar'])) && ($treff[$hitcounter]['utgittaar'] != '')) {
			$temputgitt[] = $treff[$hitcounter]['utgittaar'];
		}

		$treff[$hitcounter]['metadata'] = "<strong>" . __('Kilde:', 'inter-library-search-by-webloft') . " </strong>ebokbib.no<br>";

		$treff[$hitcounter]['metadata'] .= "<strong>" . __('Utgitt:', 'inter-library-search-by-webloft') . " </strong>" . implode ($temputgitt , ", ") . "<br>";
		unset ($temputgitt);

		$treff[$hitcounter]['metadata'] .= "<strong>" . __('Omfang:', 'inter-library-search-by-webloft') . "</strong>" . $treff[$hitcounter]['omfang'] . "<br>";

		// GJ&Oslash;RE TITTELINFO PEN:
		$treff[$hitcounter]['tittelinfo'] = str_replace(": :", ":", $treff[$hitcounter]['tittelinfo']);


		// REPETERBARE FELTER SJEKKES HER

		foreach ($record->getFields() as $tag => $subfields) {

			// Lese utdrag: Sjekke 856

			if ($tag == '856') {
				foreach ($subfields->getSubfields() as $code => $value) {
					$ettfelt[(string) $code] = substr((string) $value, 5);
				}
				if ((isset($ettfelt['z'])) && ($ettfelt['z'] == "Les utdrag")) {
					if (isset($ettfelt['u'])) {
						$treff[$hitcounter]['pdfutdrag'] = $ettfelt['u'];
					}
				}

			}

			// Dewey: Sjekke 082 $a

			if ($tag == '082') {
				foreach ($subfields->getSubfields() as $code => $value) {
					$ettfelt[(string) $code] = substr((string) $value, 5);
				}
				if (isset($ettfelt['a'])) {
					$dewey = $ettfelt['a'];
					$endewey[] = $dewey;
				}
			}

			// Emneord: Sjekke 650 $a

			if ($tag == '650') {
				foreach ($subfields->getSubfields() as $code => $value) {
					$ettfelt[(string) $code] = substr((string) $value, 5);
				}
				if ((isset($ettfelt['a'])) && ($ettfelt['a'] != "")) {
					$emneord = $ettfelt['a'];
					$ettemneord[] = $emneord;
				}
			}

		}

		if (isset($ettemneord) && (is_array($ettemneord))) {
			$ettemneord = array_unique ($ettemneord);
			sort ($ettemneord);
			$treff[$hitcounter]['emneord'] = $ettemneord;
		}

		@$treff[$hitcounter]['dewey'] = $endewey;
		unset($endewey, $ettemneord);

		// pr&oslash;ve &aring; finne omslag
		$treff[$hitcounter] = hente_omslag($treff[$hitcounter]);

		$hitcounter++;



	} // slutt p&aring; hvert item

// Printe ut og legge til i felles treff

if (@is_array($treff)) {
	foreach ($treff as $enkelttreff) {
		$eboktreff[] = $enkelttreff; // legge til

		// Skrive ut
		$outhtml = str_replace ("urlString" , $enkelttreff['url'] , $singlehtml);
		$outhtml = str_replace ("omslagString" , $enkelttreff['omslag'] , $outhtml);
		$outhtml = str_replace ("tittelString" , $enkelttreff['tittel'] , $outhtml);
		$outhtml = str_replace ("forfatterString" , $enkelttreff['opphav'] , $outhtml);
		$outhtml = str_replace ("beskrivelseString" , trunc ($enkelttreff['beskrivelse'] , 50) , $outhtml);
		$outhtml = str_replace ("metadataString" , $enkelttreff['metadata'] , $outhtml);
		$outhtml = str_replace ("pdfString" , $enkelttreff['pdfutdrag'] , $outhtml);

		echo $outhtml;
	}
} else { // Ingen treff
	echo __('Beklager, ingen treff!', 'inter-library-search-by-webloft');
}

?>
