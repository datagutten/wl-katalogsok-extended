<?php

class EbookSearchController extends WL_CommonSearchController{
	public $makstreff;
	public $search_string;
  public $QueryResult;
	public $SearchQuery;
	public $Url;

  function __construct( $request ){
    //_log('EbookSearchController::construct');

    foreach ($request as $key => $value) {
      $this->$key = $value;
    }

    $this->makstreff = 100;
    $this->SearchQuery = str_replace ("%22" , "", $this->wl_query);
    $this->SearchQuery = urldecode ($this->SearchQuery);

    $this->Url = "http://ebokbib.no/cgi-bin/sru-ebokbib?operation=searchRetrieve&query=(norzig.possessingInstitution=2020000+AND+<!QUERY!>)"; // BRUKER AKERSHUS FYLKESBIB. FOREL&Oslash;PIG

    parent::__construct();
  }


	function runQuery(){
		//_log('EbookSearchController::runQuery');
    $xml_file = WL_PLUGIN_PATH.'/local-db/bokselskap_publiseringsliste_2016-01-25.xml';

    $Xml = null;
    if ( file_exists($xml_file) ){
      $Xml = simplexml_load_file($xml_file);
    }
    else{
      _log('missing file:'.$xml_file);
    }

    if ( is_object($Xml) && isset($Xml->text->body->div->list->item) ){
      $this->QueryResult = $Xml;
    }
    else{
      return null;
    }
	}


  function getResultItemTemplate(){
    $singlehtml = '<li>' . "\n";
    $singlehtml .= wlils_ribbon(__('Last ned!', 'inter-library-search-by-webloft') , 'urlString' , '#00a');
      $singlehtml .= '<a target="_blank" href="urlString">' . "\n";
      $singlehtml .= '<img class="wlkatalog_bokitem_bilde" src="omslagString" alt="tittelString" />' . "\n";
      $singlehtml .= 'boskelskap';
      $singlehtml .= "</a>" . "\n";
      $singlehtml .= '<div class="eboktreff_beskrivelse">' . "\n";
        $singlehtml .= '<h3><a target="_blank" href="urlString">' . "\n";
        $singlehtml .= 'tittelString' . "\n";
        $singlehtml .= '</a></h3>' . "\n";
        $singlehtml .= '<div class="ansvar">forfatterString</div>' . "\n";
        $singlehtml .= '<p>beskrivelseString</p>' . "\n";
      $singlehtml .= '</div>' . "\n";
    $singlehtml .= '</li>' . "\n\n";

    return $singlehtml;
  }


	function printResults(){
    //_log('EbookSearchController::printResults');
    //_log($this->QueryResult);

    $html = null;

    $hit_counter = 0;
    $bokselskaptreff = null;

    if ( is_array($this->QueryResult->text->body->div->list->item) or is_object($this->QueryResult->text->body->div->list->item) ){
      $bokselskaptreff = array();
      foreach ($this->QueryResult->text->body->div->list->item as $index => $entry) {
        $ishit = false;

        $search_string = $this->wl_query;
        $invertert = 'ALDRI_I_LIVET_MIN_VENN'; // unng&aring;r warning ved tom string

        if ( stristr($this->wl_query , "\"") || stristr($this->wl_query, "%22") ) { // FRASES&Oslash;K!
          $search_string = trim(str_replace("\"" , "" , $this->wl_query) ); // Fjerne fnutter
          $search_string = trim(str_replace("%22" , "" , $search_string)); // Metode #2
        }
        else { // ikke invertert form, vi lager en invertert
          if (stristr($search_string , " ")) { // m&aring; være flere termer for &aring; invertere
            $vazelina = explode (" " , $search_string);
            $first = array_pop ($vazelina);
            $second = implode (" " , $vazelina);
            $invertert = trim ($first) . ", " . trim($second);
          }
        }

        if (
          mb_stristr($entry->ref->name[0] , $search_string)
          || mb_stristr($entry->ref->title , $search_string)
          || mb_stristr($entry->ref->name[0] , $invertert)
          || mb_stristr($entry->ref->title , $invertert)
          )
        {
          $ishit = true;
        }

        if ( $ishit == true ) { // VI HAR ET TREFF
          $bokselskaptreff[$hit_counter]['url'] = (string) $entry->ref->attributes()->target;
          $bokselskaptreff[$hit_counter]['forfatter'] = (string) $entry->ref->name[0];
          if (isset($entry->ref->name[1])) {
            $bokselskaptreff[$hit_counter]['utgitt'] = (string) $entry->ref->name[1];
          }
          $bokselskaptreff[$hit_counter]['tittel'] = (string) $entry->ref->title;
          $bokselskaptreff[$hit_counter]['aar'] = (string) $entry->ref->date;
          if ($bokselskaptreff[$hit_counter]['aar'] != "") {
            $bokselskaptreff[$hit_counter]['tittel'] .= " (" . $bokselskaptreff[$hit_counter]['aar'] . ")";
          }

          $bokselskaptreff[$hit_counter]['isbn'] = (string) $entry->attributes("xml",true)->id;
          $bokselskaptreff[$hit_counter]['isbn'] = str_replace ("isbn" , "" , $bokselskaptreff[$hit_counter]['isbn']);
          $bokselskaptreff[$hit_counter]['omslag'] = (string) $entry->p->ref->attributes()->target;

          $bokselskaptreff[$hit_counter]['beskrivelse'] = null;
          if ( isset($bokselskaptreff[$hit_counter]['utgitt']) ){
            $bokselskaptreff[$hit_counter]['beskrivelse'] = "<strong>" . __('Utgitt', 'inter-library-search-by-webloft') . "</strong>: " . $bokselskaptreff[$hit_counter]['utgitt'] . ". ";
          }

          $bokselskaptreff[$hit_counter]['beskrivelse'] .= "<br><strong>" . __('Kilde', 'inter-library-search-by-webloft') . "</strong>: bokselskap.no" . ". ";
          $bokselskaptreff[$hit_counter]['beskrivelse'] .= "<br><strong>" . __('ISBN', 'inter-library-search-by-webloft') . "</strong>: " . $bokselskaptreff[$hit_counter]['isbn'];

          $hit_counter++;
        }
      }
    }


    //$eboktreff = array();
    if (is_array($bokselskaptreff)) {
      $html .= '<ul class="ils-results">';
      $singlehtml = $this->getResultItemTemplate();

      foreach ($bokselskaptreff as $enkelttreff) {
        //$eboktreff[] = $enkelttreff; // legge til
        $item = $singlehtml;
        $item = str_replace ("urlString" , $enkelttreff['url'] , $singlehtml);
        $item = str_replace ("omslagString" , $enkelttreff['omslag'] , $item);
        $item = str_replace ("tittelString" , $enkelttreff['tittel'] , $item);
        $item = str_replace ("forfatterString" , $enkelttreff['forfatter'] , $item);
        $item = str_replace ("beskrivelseString" , $enkelttreff['beskrivelse'] , $item);

        $html .= $item;
      }
      $html .= "</ul>";
    }

    if ( $html ){
      $this->setResponse( $html, count($bokselskaptreff) );
    }


    return $this->Response;
  } // end of printResults



} // end of class