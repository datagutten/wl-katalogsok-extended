<?php

add_shortcode("wl-ils", array('MBShortcode', "searchCatalog" ) );
add_shortcode("wl-ils-enkeltpost", array('MBShortcode', "showSingleItem") );

class MBShortcode{

  public static function searchCatalog($atts) {

    extract(
        shortcode_atts(
          array(
            'library_id' => '',
            'results_per_page' => '100',
            'show_images' => '1',
            'target_page' => null,
            'resources' => 'biblioteket,bokhyllano,eboker,filmbibno',
            // deprecated attributes
            'mittbibliotek' => '',
            'treffperside' => '100',
            'hamedbilder' => '1',
            'kilder' => 'biblioteket,bokhyllano,eboker,filmbibno',
          ),
          $atts)
        );

    $enkeltpost           = get_option('wl_katalogsok_option_enkeltpost' , '');
    $omslagbokkilden      = get_option('wl_katalogsok_option_omslagbokkilden' , '0');
    $omslagnb             = get_option('wl_katalogsok_option_omslagnb' , '0');
    $viseavansertlenke    = get_option('wl_katalogsok_option_viseavansertlenke' , '0');
    $treffbokhylla        = get_option('wl_katalogsok_option_treffbokhylla' , '0');
    $hoyretrunk           = get_option('wl_katalogsok_option_hoyretrunk' , '0');
    $enkeltpostnyttvindu  = get_option('wl_katalogsok_option_enkeltpostnyttvindu' , '');
    $skjulesoketips       = get_option('wl_katalogsok_option_skjulesoketips' , '1');

    if ( $mittbibliotek && !$library_id ){
      $library_id = $mittbibliotek;
    }

    if ( $treffperside && !$results_per_page ){
      $results_per_page = $treffperside;
    }

    if ( $hamedbilder && !$show_images ){
      $show_images = $hamedbilder;
    }

    if ( $kilder && !$resources ){
      $resources = $kilder;
    }

    $action_url = null;
    if ( $target_page && is_numeric($target_page) ){
      $action_url = get_permalink($target_page);
    }


    if ( !$library_id ) {
      die (__('<b><i>Melding fra Wordpress-utvidelsen WL Katalogs&oslash;k:</i></b><br />Shortcode m&aring; inneholde en bibliotekkatalog du vil s&oslash;ke i.<br />') . "<a href=\"" . admin_url( 'tools.php?page=wl_katalogsok_tools') . "\">" . __('G&aring; til Verkt&oslash;y->WL Katalogs&oslash;k i Wordpress for &aring; lage en shortcode.') . "</a>");
    }

    // Hvis vi kommer fra widget er en del ting satt i URL
    $has_get_query = null;
    if ( _is($_REQUEST, 'webloftsok_query') ) {
      $has_get_query = cleanValue($_REQUEST['webloftsok_query']);
    }

    if ( _is($_REQUEST, 'enkeltposturl') ) { // kan være satt i widget
      $enkeltposturl = cleanValue($_REQUEST['enkeltposturl']);
    }

    $brukbibliotek = $library_id; // hvis ikke bruk fra shortcode
    if ( _is($_REQUEST, 'katalog') ) {
      $brukbibliotek = cleanValue($_REQUEST['katalog']) ;
    }

    // Gjemmer iframe og viser spinner mens lasting
    ob_start();
    require dirname(KS_FILE) . '/templates/search-form.php';
    $out = ob_get_clean();


    return $out;
  }


  public static function showSingleItem ($atts) {
    $postout = null;

    if ( $info = _is($_GET, 'enkeltpostinfo') ) {
      $item_info = unserialize(base64_decode($info));

      $library = null;
      if ( $library_id = _is($item_info, 'bibkode') ){
        $library = getLibraryById($library_id);
      }

      if ( _is($item_info, 'biblioteksystem') == "koha") { // hvis Koha har vi f&aring;tt all info i query string
        $treff = $item_info;
      }
      else{
        if ( $library ){
          $treff = WL_Search::getSinglePost( $library, $item_info['postid'] );
          $treff = hente_omslag ($treff); // legger til omslag
          $treff = krydre_some ($treff); // legger til Twitt og face
        }
      }

      ob_start();
      require getTemplatePath('single-result.php');
      $postout = ob_get_clean();
    }
    else {
      return ("Ingen post angitt!");
    }

    return $postout;
  }


} // end of class