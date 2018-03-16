<?php

class OpenLibrarySearchController extends WL_CommonSearchController{
  public $QueryResult;

  function __construct( $request ){
    //_log('OpenLibrarySearchController::construct');

    foreach ($request as $key => $value) {
      $this->$key = $value;
    }

    $this->SearchQuery = $this->wl_query;

    if ( stristr($this->SearchQuery , ", ") ) { // det er s&oslash;kt i invertert form, men bare treff i vanlig form
      $wilbury = explode ("," , $this->SearchQuery );
      $first = array_pop ($wilbury); // tar den siste etter komma
      $second = implode (" " , $wilbury); // tar det foran komma
      $search_string = trim ($first) . " " . trim ($second);
      rop ($this->SearchQuery);
    }
    $this->makstreff = 100; // vet ikke helt hvor vi skal sette dette?
    $this->SearchQuery = str_replace ("\"" , "", $this->SearchQuery);
    $this->SearchQuery = str_replace (" " , "+", $this->SearchQuery );

    parent::__construct();
    $this->Url = "https://openlibrary.org/search.json?q=<!QUERY!>&has_fulltext=true";

  } // end of construct


  function getResultItemTemplate(){
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
    return $singlehtml;
  }


  function runQuery(){
    //_log('OpenLibrarySearchController::runQuery');
    $url = str_replace ("<!QUERY!>" , $this->SearchQuery , $this->Url); // sette inn s&oslash;keterm
    //_log($url);
    $response = wp_remote_get($url);
    if ( wp_remote_retrieve_response_code( $response ) == 200 ){
      $results = json_decode( wp_remote_retrieve_body( $response ) );
      $this->QueryResult = _is($results,'docs');
    }
  }


  function printResults(){
    $html = null;

    $hit_counter = 0;
    $totalt = 0;

    if ( is_array($this->QueryResult) ){
      foreach ($this->QueryResult as $treff) {
        // _log($treff);
        $year = null;
        if ($treff->public_scan_b == '1') {
          $totalt++;
          if ($hit_counter < $this->makstreff) {
            $openlibrarytreff[$hit_counter]['tittel'] = $treff->title;
            if (@$treff->subtitle != '') {
              $openlibrarytreff[$hit_counter]['tittel'] .= " : " . $treff->subtitle;
            }
            @$openlibrarytreff[$hit_counter]['forfatter'] = $treff->author_name[0];
            if (trim($openlibrarytreff[$hit_counter]['forfatter']) == "") {
              $openlibrarytreff[$hit_counter]['forfatter'] = "N.N.";
            }
            @$openlibrarytreff[$hit_counter]['omslag'] = "https://covers.openlibrary.org/b/olid/" . $treff->cover_edition_key . "-M.jpg";
            $openlibrarytreff[$hit_counter]['url'] = "https://openlibrary.org" . $treff->key;
            $year = _is($treff, 'first_publish_year');

            if ( $year  ) {
              $openlibrarytreff[$hit_counter]['tittel'] .= " (" . $year . ")";
            }
            $openlibrarytreff[$hit_counter]['beskrivelse'] = sprintf('<strong>%s</strong> %s<br/>', __('Forfatter:', 'inter-library-search-by-webloft'), $openlibrarytreff[$hit_counter]['forfatter'] );
            $openlibrarytreff[$hit_counter]['beskrivelse'] .= sprintf('<strong>%s</strong> Open Library', __('Kilde:', 'inter-library-search-by-webloft') );

            $hit_counter++;
          }
        }
      }
    }


    $openlibraryantalltreff = $totalt;

    // Printe ut og legge til i felles treff

    if ( @is_array($openlibrarytreff) ) {
      $html = '<ul class="ils-results">';

      $singlehtml = $this->getResultItemTemplate();
      foreach ($openlibrarytreff as $enkelttreff) {
        //$eboktreff[] = $enkelttreff; // legge til
        $item = $singlehtml;

        $item = str_replace ("urlString" , $enkelttreff['url'] , $item);
        $item = str_replace ("omslagString" , $enkelttreff['omslag'] , $item);
        $item = str_replace ("tittelString" , $enkelttreff['tittel'] , $item);
        $item = str_replace ("forfatterString" , $enkelttreff['forfatter'] , $item);
        $item = str_replace ("beskrivelseString" , $enkelttreff['beskrivelse'] , $item);

        $html .= $item;
      }
      $html .= "</ul>";
    }

    if ( $html ){
      $this->setResponse( $html, count($openlibrarytreff) );
    }

    return $this->Response;
  }


}