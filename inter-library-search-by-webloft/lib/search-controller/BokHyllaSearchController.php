<?php

class BokHyllaSearchController extends WL_CommonSearchController{

	function __construct( $request ){
		//_log('Bokhylla::construct');

    foreach ($request as $key => $value) {
      $this->$key = $value;
    }

		$this->makstreff = 50; // vet ikke helt hvor vi skal sette dette?

    parent::__construct();

		$this->SearchQuery = urlencode(cleanValue($this->wl_query) );

		$this->Url = $this->buildQuery(
        NB_NO_SEARCH,
        $query_args=
        array(
          'q' => $this->SearchQuery,
          'fq' => array(
            'mediatype:('. utf8_decode("Bøker") .')',
            'contentClasses:(bokhylla%20OR%20public)',
            'digital:Ja'
          ),
          'itemsPerPage' => $this->makstreff,
          'ft' => false
        )
      );
	}

  function runQuery( ){
    // _log('BokHyllaSearchController::runQuery');
    // _log($this->Url);
    $this->XmlString = file_get_contents($this->Url);
    //_log( $this->XmlString );

    if ( !trim($this->XmlString)  ){
      _log('no result, get error');
    }
  }


	function getResultItemTemplate(){
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

    return $singlehtml;
	}


  function printResults(){
    // _log('BokHyllaSearchController::printResults');

    $bokhyllaalttreff = array();

    $xmldata = null;
    if ( $this->XmlString ){
      $xmldata = simplexml_load_string($this->XmlString);
    }
    else{
      _log('empty xml string: BokHyllaSearchController::printResults()');
    }

    $antalltreff['bokhylla'] = (int) substr(stristr($xmldata->subtitle, " of ") , 4);

    if ( is_object($xmldata) && isset($xmldata->entry) ){
      $index = 0;
      foreach ( $xmldata->entry as  $entry) {
        if ( $index < $this->makstreff ) {

          $childxml = ($entry->link[0]->attributes()->href); // Dette er XML med metadata

          $response = wp_remote_get( (string)$childxml  );

          $xmlfile = null;
          if ( wp_remote_retrieve_response_code( $response ) == 200 ){
            $xmlfile = wp_remote_retrieve_body( $response );

            $childxmldata = simplexml_load_string($xmlfile);
            $namespaces = $entry->getNameSpaces(true);
            $nb = $entry->children($namespaces['nb']);

            $bokhyllaalttreff[$index] = array();

            $bokhyllaalttreff[$index]['tittel'] = (string) $entry->title;
            $bokhyllaalttreff[$index]['forfatter'] = (string) $nb->namecreator;

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
            $bokhyllaalttreff[$index]['utgitt'] = implode (" " , $utgitt);

            if (isset($childxmldata->physicalDescription->extent)) {
              $bokhyllaalttreff[$index]['omfang'] = (string) $childxmldata->physicalDescription->extent;
            }

            // BESKRIVELSE
            $bokhyllaalttreff[$index]['beskrivelse'] = "<b>" . __('Utgitt:', 'inter-library-search-by-webloft') . " </b>" . $bokhyllaalttreff[$index]['utgitt'] . ". ";
            $bokhyllaalttreff[$index]['beskrivelse'] .= "<b>" . __('Omfang:', 'inter-library-search-by-webloft') . " </b>" . $bokhyllaalttreff[$index]['omfang'] . ". ";


            //if (isset($childxmldata->note)) {
              //$bokhyllatreff[$index]['beskrivelse'] .= $childxmldata->note . ". ";
            //}

            // URN
            // BOKOMSLAG, SE http://www-sul.stanford.edu/iiif/image-api/1.1/#parameters
            if( stristr($nb->urn , ";") )
            {
              $tempura = explode (";" , $nb->urn);
              $urn = trim($tempura[1]); // vi tar nummer 2
            }
            else
            {
              $urn = $nb->urn[0];
            }

            $delavurn = substr($urn , 8);

            // if ($urn != "") {
            //
            //  $bokhyllaalttreff[$teller]['bilde'] = "http://bokforsider.webloft.no/urn/" . $delavurn . ".jpg";
            // } else {
            //  $bokhyllaalttreff[$teller]['bilde'] = $generiskbokomslag; // DEFAULTOMSLAG
            // }

            $bokhyllaalttreff[$index]['bilde'] = getIconUrl('ikke_digital.png'); // DEFAULTOMSLAG

            $bokhyllaalttreff[$index]['url'] = "http://urn.nb.no/" . $urn;
            $bokhyllaalttreff[$index]['id'] = $urn;

            // Finnes PDF?

            if ( ( (string)$nb->digital == "true" ) && stristr( (string)$nb->contentclasses , "public") ) {
              $bokhyllaalttreff[$index]['pdf'] = "http://www.nb.no/nbsok/content/pdf?urn=URN:NBN:" . $delavurn;
              $bokhyllaalttreff[$index]['pdflenke'] = '<a target="_blank" href="' . $bokhyllaalttreff[$index]['pdf'] . '"><img src="'.getIconUrl('pdf.png').'" alt="' . __('Last ned som PDF', 'inter-library-search-by-webloft') . '" /></a>';
            }
          }


        } // if
        $index++;
      }  // foreach
    }


    if ( is_array($bokhyllaalttreff) && !empty($bokhyllaalttreff) ) {
      // _log( 'count($bokhyllaalttreff)' );
      // _log( count($bokhyllaalttreff) );
      $singlehtml = $this->getResultItemTemplate();

      $html = '<ul class="ils-results">';
      foreach ($bokhyllaalttreff as $enkelttreff) {
        $bokhyllatreff[] = $enkelttreff; // legge til

        $item_html = str_replace ("urlString" , $enkelttreff['url'] , $singlehtml);
        $item_html = str_replace ("bildeString" , $enkelttreff['bilde'] , $item_html);
        $item_html = str_replace ("tittelString" , $enkelttreff['tittel'] , $item_html);
        $item_html = str_replace ("forfatterString" , $enkelttreff['forfatter'] , $item_html);
        $item_html = str_replace ("beskrivelseString" , $enkelttreff['beskrivelse'] , $item_html);

        if ( @trim($enkelttreff['pdflenke']) != '') {
          $item_html = str_replace ("pdflenkeString" , $enkelttreff['pdflenke'] , $item_html);
        }
        else {
          $item_html = str_replace ("pdflenkeString" , '' , $item_html);
        }

        $html .= $item_html;

      }
      $html .= '</ul>';

      if ( $antalltreff['bokhylla'] > $this->makstreff) {
        $this->Url = "http://www.nb.no/nbsok/search?action=search&mediatype=b&oslash;ker&format=Digitalt tilgjengelig&CustomDateFrom=&CustomDateTo=&pageSize=50&sortBy=ranking&searchString=<!QUERY!>%20%26ft=false";
        $this->Url = str_replace ("<!QUERY!>" , $this->SearchQuery , $this->Url);

        $html .= sprintf (esc_html__('Funnet %d treff.', 'inter-library-search-by-webloft') , $antalltreff['bokhylla'])
                  . '<a target="_blank" href="' . $this->Url . '"> '
                  . __('Se alle treffene p&aring; nb.no', 'inter-library-search-by-webloft')
                  . '</a>!';
      }


      if ( $html ){
        $this->setResponse( $html, count($bokhyllaalttreff) );
      }
    }


    return $this->Response;
  } // end of printResults


} // end of class