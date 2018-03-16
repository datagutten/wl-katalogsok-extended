<?php

class FilmbibSearchController extends WL_CommonSearchController{
  public $Url;
  public $SeachQuery;
  public $Response;

  function __construct( $request ){
    //_log('FilmbibSearchController::construct');

    foreach ($request as $key => $value) {
      $this->$key = $value;
    }

    parent::__construct();
    $this->SeachQuery = str_replace ("*" , "" , $this->wl_query); // kan ikke trunkere i Filmbib!
    $this->Url = "http://api.dvnor.no/vod/filmbib/search?q=<!QUERY!>";
  }

  function runQuery(){
    //_log('FilmbibSearchController::runQuery');
    $url = str_replace ("<!QUERY!>" , $this->SeachQuery , $this->Url); // sette inn s&oslash;keterm

    //_log($url);
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
    //curl_setopt ($curl, CURLOPT_HTTPHEADER, array('X-Authorization: 941c8d233fa83806533306e3d27d6ece4f01932f'));
    curl_setopt ($curl, CURLOPT_HTTPHEADER, array('X-Authorization: b1f0427aad43564435d3b5f4498a18acdd1f5a39'));
    $response = curl_exec($curl);

    // JSON jepp
    $this->QueryResult = json_decode ($response);
  }


  function getResultItemTemplate(){
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

    return $singlehtml;
	}


  function printResults(){
    //_log('FilmbibSearchController::printResults');
    $filmbibtreff = array();

    if ( isset($this->QueryResult->data) ){
      foreach ( $this->QueryResult->data as $index => $entry) {
        $filmbibtreff[$index]['kilde'] = "Filmbib";
        $filmbibtreff[$index]['url'] = $entry->url;
        $filmbibtreff[$index]['tittel'] = $entry->title;
        $filmbibtreff[$index]['beskrivelse'] = trunc(strip_tags($entry->synopsis) , 50);
        $filmbibtreff[$index]['bilde'] = $entry->image->medium;
      } // end of foreach
    }

    if ( is_array($filmbibtreff) && !empty($filmbibtreff) ) {
      $html = null;
      $singlehtml = $this->getResultItemTemplate();
      $html = '<ul class="ils-results">';

      foreach ($filmbibtreff as $enkelttreff) {
        $videotreff[] = $enkelttreff;

        $item_html = str_replace ("omslagString" , $enkelttreff['bilde'] , $singlehtml);
        $item_html = str_replace ("urlString" , $enkelttreff['url'] , $item_html);
        $item_html = str_replace ("tittelString" , $enkelttreff['tittel'] , $item_html);
        $item_html = str_replace ("beskrivelseString" , $enkelttreff['beskrivelse'] , $item_html);

        $html .= $item_html;
      }

      $html .= '</ul>';
      if ( $html ){
        $this->setResponse( $html, count($filmbibtreff) );
      }
    }


    return $this->Response;
  }


} // end of class