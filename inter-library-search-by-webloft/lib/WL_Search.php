<?php

class WL_Search{
  public $Query;
  public $Result;
  public $Library;
  public $LibraryId;
  public $LibraryIndex;
  public $AdvancedQueryUrl;
  public $CoverServer;


  function __construct( $library, $query  ){
    $this->Library = $library;
    $this->LibraryId = _is($library, 'department_id');
    $this->Query = $query;
    $this->CoverServer = COVER_SERVER;

    $this->sanitizeQuery();
  }


  function _get($Attribute){
    return $this->$Attribute;
  }


  function setLibraryIndex( $index ){
    $this->LibraryIndex = $index;
  }

  function getLibraryIndex(){
    return $this->LibraryIndex;
  }

  function setLibrarySystem($system){
    $this->Library['system'] = $system;
    $this->sanitizeQuery();
  }

  function getLibrarySystem(){
    return $this->Library['system'];
  }

  function getLibraryValue($index){
    if ( _is($this->Library, $index)  ){
      return _is($this->Library, $index);
    }
    else{
      return null;
    }
  }


  function sanitizeQuery(){

    $system = $this->Library['system'];
    //_log('WL_Search::sanitizeQuery');
    //_log($system);

    if ( $system == 'bibliofil' or $system == 'koha-sru' ) { // frases&oslash;k i Bibliofil
      $this->Query = Bibliofil::sanitizeQuery($this->Query);
    }

    if ($system == 'mikromarc') { // frases&oslash;k i Mikromarc
      $this->Query = Mikromarc::sanitizeQuery( $this->Query );
    }

    if ($system == 'tidemann') { // frases&oslash;k i Tidemann
      $this->Query = Tidemann::sanitizeQuery ( $this->Query );
    }

    if ($system == 'koha') { // frases&oslash;k i Koha
      $this->Query = Koha::sanitizeQuery ( $this->Query );
    }

    if ($system == 'bokhylla') {
      $this->Query = Bokhylla::sanitizeQuery ( $this->Query );
    }
  }



  function runQuery( $position, $treffperside ){
    //_log('WL_Search::runQuery');
    if ( $this->getLibrarySystem() == 'tidemann') {
      $url                          = $this->getLibraryValue('server') . "?version=1.2&operation=searchRetrieve&maximumRecords=" . $treffperside . "&recordSchema=marcxchange&query=" . $this->Query;
      $this->Result['items']        = tidemann_sok($url, $position);
      $this->Result['count-items']  = tidemann_antalltreff($url);
    }

    elseif ( $this->getLibrarySystem() == 'bibliofil') {
      $url = WL_CommonSearch::buildQuery( $this->getLibraryValue('server') , array( 'version' => '1.2', 'operation' => 'searchRetrieve', 'maximumRecords' => $treffperside, 'query' => 'cql.anywhere='. $this->Query ) );
      $Bibliofil = new Bibliofil($url, $position);
      $this->Result['items']   = $Bibliofil->search();
      $this->Result['count-items'] = $Bibliofil->countResults();
    }

    elseif ( $this->getLibrarySystem() == 'alma') {
      $url                          = $this->getLibraryValue('server') . "?version=1.2&operation=searchRetrieve&maximumRecords=" . $treffperside . "&recordSchema=marcxml&query=alma.all_for_ui=" . $this->Query;
      $this->Result['items']        = alma_sok($url, $position); // bruk vanlig s&oslash;k
      $this->Result['count-items']  = alma_antalltreff($url);
    }

    elseif ( $this->getLibrarySystem() == 'mikromarc') {
      $url =
        WL_CommonSearch::buildQuery(
          $this->getLibraryValue('server'),
          array(
            'httpAccept'      => 'text/xml',
            'version'         => '1.1',
            'operation'       => 'searchRetrieve',
            'maximumRecords'  =>  ( $treffperside <= 50 ) ? $treffperside : 50,
            'query'           => 'cql.anywhere='. $this->Query
          )
        );

      // _log($url);
      $this->Result = mikromarc_sok($url, $position);
    }

    elseif ( $this->getLibrarySystem() == 'koha-sru' ) {
      $Koha = new KohaSru( $this->getLibraryValue('server'), $this->Query, $position, $treffperside );
      $this->Result['items']   = $Koha->search();
      $this->Result['count-items'] = $Koha->countResults();
    }

    elseif ( $this->getLibrarySystem() == 'koha') {
      $Koha = new Koha( $this->getLibraryValue('server'), $this->Query, $position, $treffperside );

      $this->Result['items']   = $Koha->search();
      $this->Result['count-items'] = $Koha->countResults();
    }

    elseif ( $this->getLibrarySystem() == 'bokhylla') {
      $url = WL_CommonSearch::buildQuery( NB_NO, array( 'q' => $this->Query, 'fq' => array( 'mediatype:(B%C3%B8ker)', 'contentClasses:(bokhylla%20OR%20public)', 'digital:Ja' ) ) );
      // $url =  "http://www.nb.no/services/search/v2/search?q=" . $this->Query . "&fq=mediatype:(B%C3%B8ker)&fq=contentClasses:(bokhylla%20OR%20public)&fq=digital:Ja";

      $this->Result['items']        = Bokhylla::search( $url, $position );
      $this->Result['count-items']  = Bokhylla::countResults($url);
    }

    //_log($url);
    return $this->Result;
  }


  function setAdvancedQueryUrl(){
    if ( $this->getLibrarySystem() == 'bibliofil') {
      // http://www.akershus.fylkesbibl.no/cgi-bin/websok?mode=sok&st=a#soket
      $this->AdvancedQueryUrl = str_replace ("/sru" , "/websok" ,  $this->getLibraryValue('server') ) . "?mode=sok&st=a&pubsok_txt_0=" . $this->Query;
    }
    elseif ( $this->getLibrarySystem() == 'mikromarc') {
      $this->AdvancedQueryUrl = sprintf( "http://websok.mikromarc.no/Mikromarc3/web/search.aspx?ST=Form&Unit=%s&db=%s&SW=%s", $this->getLibraryValue('department_id') , $this->getLibraryIndex(),  $this->Query );
      // $this->AdvancedQueryUrl = "http://websok.mikromarc.no/Mikromarc3/web/search.aspx?SC=FT&SW=" . $this->Query . "&Unit=" . $this->getLibraryValue('department_id') . "&db=" . $this->getLibraryIndex();
    }
    elseif ( $this->getLibrarySystem() == 'tidemann') {
      // http://asp.bibliotekservice.no/flesberg/doclist.aspx?fquery=fr%3dnorge*+and+ba%3d001
      // http://asp.bibliotekservice.no/flesberg/search.aspx?type=0
      $this->AdvancedQueryUrl = str_replace ("_sru/nome.aspx" , "" ,  $this->getLibraryValue('server') ) . "/search.aspx?&type=0&fquery=fr%3d" . $this->Query . "*+and+ba%3d001";
    }
    elseif ( $this->getLibrarySystem() == 'koha') {
      $this->AdvancedQueryUrl =  $this->getLibraryValue('server') . "/cgi-bin/koha/opac-search.pl";
    }
    elseif ( $this->getLibrarySystem() == 'bokhylla') {
      $this->AdvancedQueryUrl = "http://www.bokhylla.no";
    }
    elseif ( $this->getLibrarySystem() == 'alma') {
      $this->AdvancedQueryUrl = "http://bibsys-almaprimo.hosted.exlibrisgroup.com/primo_library/libweb/action/dlSearch.do?institution=HBV&vid=" . $this->LibraryId . "&search_scope=default_scope&query=any,contains," . $this->Query;
    }
    else{
      $this->AdvancedQueryUrl = null;
    }

    return $this->AdvancedQueryUrl;
  }

  function setItemArray( $item ){
    return array(
      'ebokbibid'         => _is($item, 'ebokbibid'),
      'omslag'            => ( empty($item['omslag']) ? getIconUrl( 'ikke_digital.png' )  : $item['omslag']),
      'tittel'            => _is($item, 'tittelinfo'),
      'aar'               => _is($item, 'utgittaar'),
      'url'               => _is($item, 'permalink'),
      'external_link'     => _is($item, 'external_link'),
      'opphav'            => _is($item, 'opphav'),
      'pdflenke'          => _is($item, 'pdflenke'),
      'pdfutdrag'         => _is($item, 'pdfutdrag'),
      'lenke'             => _is($item, 'lenke'),
      'ansvarsangivelse'  => _is($item, 'ansvarsangivelse'),
      'status'            => _is($item, 'status'),
      'materialtype'      => _is($item, 'type'),
      // Set empty default values to avoid 'undefined index' errors
      'isbn'              => '',
      'dewey'             => '',
      'omfang'            => '',
      'titteloriginal'    => '',
      'fulltekst'         => ((isset($item['fulltekst']) && ($item['fulltekst'] != '')) ? $item['fulltekst'] : false),
      'description'       => (isset($item['beskrivelse']) ? trunc($item['beskrivelse'], 40) : ''),
      );
  }


  function getItemInfoFromNationalLibraryByIsbn($item){
    // _log('WL_Search::getItemInfoFromNationalLibraryByIsbn');

    $tempisbn = cleanIsbn($item['isbn']);
    // _log($tempisbn);
    $omslag   = $this->CoverServer . "/isbn/" . $tempisbn . ".jpg";
    // _log($omslag);
    // if ( url_exists($omslag) ) { // cover url exists
    //   $item['omslag'] = $omslag;
    // }
    $item_info = null;
    $Result = $this->getItemInfoFromNationalLibrary('isbn', $item['isbn'] );

    $entry = _is($Result, 'entry');
    if ( is_array($entry) ){
      foreach ($entry as $item) {
        $namespaces = $item->getNameSpaces(true);
        $nb         = $item->children($namespaces['nb']); // alle som er nb:ditten og nb:datten
        $item['fulltekst'] = "http://urn.nb.no/" . $nb->urn;
      }
    }

    if ( $item ){
      $item_info = $item;
    }


    return $item_info;
  }


  function getItemInfoFromNationalLibraryByTitle($item){
    $item_info = null;
    $Result = $this->getItemInfoFromNationalLibrary('title',  urlencode($item['tittel']) );

    $entry = _is($Result, 'entry');
    if ( is_array($entry) ){
      foreach ($entry as $item) {
        $namespaces = $item->getNameSpaces(true);
        $nb         = $item->children($namespaces['nb']); // alle som er nb:ditten og nb:datten

        $item['fulltekst'] = "http://urn.nb.no/" . $nb->urn; // grabber lenke ogs&aring; med det samme

        // $omslag     = $this->CoverServer . "/urn/" . substr(($nb->urn), 8) . ".jpg";
        // if ((url_exists($omslag)) && ($nb->urn != '')) {
        //   $item['omslag']    = $omslag;
        // }
      }
    }

    if ( $item ){
      $item_info = $item;
    }

    return $item;
   }


  function getItemInfoFromNationalLibrary($key, $value){
    $search_query = "http://www.nb.no/services/search/v2/search?q=*&fq=".$key.":%22" . $value . "%22&fq=contentClasses:(public%20OR%20bokhylla)";
    $tybring   = get_content($search_query);

    return  simplexml_load_string($tybring);
  }


  function getItemInfoFromBokkilden($item){
    $url = "http://partner.bokkilden.no/SamboWeb/partner.do?format=XML&uttrekk=5&ept=3&xslId=117&enkeltsok=" . $item['isbn'];
    $item = null;

    //_log($url);
    $result = wp_remote_get( $url );
    $response_code = wp_remote_retrieve_response_code( $result );
    $response_body = wp_remote_retrieve_body($result );

    if ( $response_code  == 200 && $response_body != '<?xml version="1.0" encoding="UTF-8"?><Produkter/>' ){
      $item = array();
      $xml = $response_body;
      $firsttry       = simplexml_load_string($xml);
      $item['omslag'] = ( is_object($firsttry) && isset($firsttry->Produkt->BildeURL) ) ? $firsttry->Produkt->BildeURL : null ;

      $item['omslag'] = str_replace("&width=80", "&width=300", $item['omslag']); // knegg, knegg
      if (!isset($item['beskrivelse'])) {
        $item['beskrivelse'] = (string)$firsttry->Produkt->Ingress;
      }
    }


    return $item;
  }


  public static function getSinglePost ( $library , $item_id) {
    $treff = null;

    if ( $library ){
      $library_type = strtolower($library['system']);
      if ( $library_type == "bibliofil" ){
        $treff = Bibliofil::getItem($library, $item_id);
      }
      elseif ( $library_type == "koha-sru" ){
        $Koha = new KohaSru($library['server'], $query=null, $position=1, $posts_per_page=1 );
        $Koha->setItemId($item_id);
        $treff = $Koha->search();
      }
      // elseif ( $library_type == "koha" ){
      //   // to do
      // }
      elseif (strtolower($library_type) == "mikromarc") {
        $url =
          WL_CommonSearch::buildQuery(
            $library['server'],
            array(
              'version'         => '1.2',
              'httpAccept'      => 'text/xml',
              'operation'       => 'searchRetrieve',
              'maximumRecords'  => '10',
              'query'           => 'rec.identifier=' . $item_id
            )
          );

        $treff = mikromarc_sok($url, "1");
        $treff[0] = $treff['items'][0] ;
        $treff[0]['biblioteksystem'] = "mikromarc";
      }
      elseif ( strtolower($library_type) == "tidemann" ) {
        $treff = Tidemann::getItems(  $library, $item_id );
      }
      elseif (strtolower($library_type) == "alma") {
        $treff = Alma::getItem($library, $item_id);
      }

      return $treff[0];
    }
    else{
      return null;
    }
  } // end function


} // end of class
