<?php

class WL_CommonSearchController extends WL_CommonSearch{
  public $Response;
  public $wl_query;

  function __construct(){
    $this->setResponse();
  }

  public function setResponse( $html = null, $count = 0 ){
    if ( !trim($html) ){
      $html = __('Beklager, s&oslash;k p&aring; "%s" ga ingen treff', 'inter-library-search-by-webloft');
      $html = sprintf($html, rawurldecode($this->wl_query) );
    }


    $this->Response = array(
      'html' => $html,
      'count' => $count
      );
  } // end of set response


} // end of class