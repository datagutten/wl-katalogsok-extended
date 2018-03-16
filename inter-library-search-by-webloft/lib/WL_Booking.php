<?php

class WL_Booking{
  protected $System;
  protected $PostId;
  protected $LibraryId;
  protected $Url;


  function __construct( $post_info  ){
    if ( $system  = _is( $post_info,'bibsystem' ) ){
      $this->set('System', $system);
    }

    if ( $postid  = _is( $post_info,'postid' ) ){
      $this->set('PostId', $postid);
    }

    if ( $library_id  = _is( $post_info,'bibkode' ) ){
      $this->set('LibraryId', $library_id);
    }


    $this->setUrl();
  }

  function set($Attribute, $value){
    $this->$Attribute = $value;
  }

  function get($Attribute){
    return $this->$Attribute;
  }

  function setUrl(){
    if ( $system = $this->get('System') ){
      if ( $system == 'mikromarc' ){
        $this->Url = $this->buildMikromarkUrl();
      }
      elseif ( $system == 'bibliofil' ){
        $this->Url = $this->buildBibliofilUrl();
      }
      elseif ( $system == 'tidemann' ){
        $this->Url = $this->buildTidemannUrl();
      }
      elseif ( $system == 'alma' ){
        $this->Url = '"http://bibsys-almaprimo.hosted.exlibrisgroup.com/primo_library/libweb/action/dlSearch.do?institution=HBV&vid=HBV&search_scope=default_scope&query=any,contains,71499882780002201"';
      }
      elseif ( $system == 'koha-sru' ){
        if ( wl_is_plugin_active('koha/koha.php' ) ){
          $page_id = get_option('koha-login-page-id');
          if ( is_numeric($page_id) ){
            $this->Url = get_permalink( $page_id );
          }
        }
        else{
          $this->Url = $this->buildKohaUrl();
        }
      }

      else{
        $this->Url = false;
      }
    }
  }


  function buildTidemannUrl(){
    global $libraries;

    include ( getConfigPath("library_list.php") );

    if ( _is($libraries, $this->LibraryId) ){

      $booking_url = $libraries[$this->LibraryId]['booking'];
      // i. e. http://www.bodo.folkebibl.no/cgi-bin/sru
      // $resource = 'mappami';
      // $query_string = '?jumpmode=reservering&tnr='.$this->PostId;

      // $base_url = str_replace('sru', $resource.$query_string, $base_url);

      return $booking_url;
    }
  }


  function buildKohaUrl(){
    global $libraries;

    include ( getConfigPath("library_list.php") );

    if ( _is($libraries, $this->LibraryId) ){
      $booking_url = $libraries[$this->LibraryId]['booking'];
      return $booking_url;
    }
  }


  function buildBibliofilUrl(){
    global $libraries;

    include ( getConfigPath("library_list.php") );

    if ( _is($libraries, $this->LibraryId) ){

      if ( isset($libraries[$this->LibraryId]['booking']) ){
        $base_url = $libraries[$this->LibraryId]['booking'];
      }
      else{
        $base_url = $libraries[$this->LibraryId]['server'];
        // i. e. http://www.bodo.folkebibl.no/cgi-bin/sru
        $resource = 'mappami';
        $base_url = str_replace('sru', $resource, $base_url);
      }

      $query_string = '?jumpmode=reservering&tnr='.$this->PostId;
      $booking_url = $base_url.$query_string;

      return $booking_url;
    }
  }



  function buildMikromarkUrl(){
    $url = null;
    include ( getConfigPath("library_list.php") );
    if ( $library = _is($libraries, $this->get('LibraryId') ) ){
      // _log($library);
      if ( _is($library, 'booking') ){
        $base_url = 'https://websok.mikromarc.no/Mikromarc3/'.$library['booking'].'/Member_Reservation.aspx';

        $query_args = array(
          'RegRes'    => '1',
          'Id'        => $this->get('PostId'),
          'list'      => $this->get('PostId'),
          'Unit'      => $library['department_id'],
          'db'        => $this->get('LibraryId'),
          'cookieset' => '1'
        );

        if ( $query_string = http_build_query($query_args) ){
          $url = $base_url.'?'.$query_string;
        }
      }
    }
    // _log($url);
    return $url;
  }


  public static function buildOrderButton( $booking_url ){
    $html = '<div class="result-item-booking-url" >';
    $html .= sprintf(' <a class="link-order" href="%s" target="_blank" >%s</a>', $booking_url, __('Bestille/reservere', 'inter-library-search-by-webloft') );
    $html .= '</div>';

    return $html;
  }


} // end of class
