<?php

add_action( 'wp_ajax_wl_search', array('WL_Ajax', 'search')  );
add_action( 'wp_ajax_nopriv_wl_search', array('WL_Ajax', 'search') );


class WL_Ajax{


  public static function sanitizeQuery( $wl_query ){
    $wl_query = rawurldecode($wl_query);
    $wl_query = str_replace(',', ' ', $wl_query );
    $wl_query = str_replace('+', ' ', $wl_query );
    $wl_query = preg_replace('/\s+/', ' ', $wl_query );

    return $wl_query;
  }

  public static function search(){
    //_log('WL_Ajax::search');
    $response = '';

    if ( isset($_REQUEST['query']) ){
      $parameters = explode('&', $_REQUEST['query']);
      $search = array();

      if ( is_array($parameters) ){
        foreach ($parameters as $key => $p) {
          $p_tmp = explode('=', $p);
          $search[$p_tmp[0]] = $p_tmp[1];
        }
      }

      $resource = _is($_REQUEST, 'resource');

      $search['wl_query'] = self::sanitizeQuery( $search['wl_query'] );
      // _log( $search['wl_query'] );
      //_log('resource: '.$resource);
      if ( !empty($search) ){

        if ( !$resource or $resource == 'biblioteket' or $resource == 'bibliotek' ){
          $SearchController = new LibrarySearchController($search);
          $SearchController->startSearch();
          $response = $SearchController->printResults();
        }
        elseif ( $resource == 'eboker'){
          $SearchController = new EbookSearchController($search);
          $SearchController->runQuery();
          $response = $SearchController->printResults();

          $SearchController = new OpenLibrarySearchController($search);
          $SearchController->runQuery();
          $response_2 = $SearchController->printResults();

          if ( _is($response_2, 'count') ){
            $response['html'] .= $response_2['html'];
            $response['count'] += $response_2['count'];
          }
        }
        elseif ( $resource == 'bokhylla' or $resource == 'bokhyllano' ){
          $SearchController = new BokHyllaSearchController($search);
          $SearchController->runQuery();
          $response = $SearchController->printResults();
        }
        elseif ( $resource == 'filmbibno' or $resource == 'filmbib' ){
          $SearchController = new FilmbibSearchController($search);
          $SearchController->runQuery();
          $response = $SearchController->printResults();
        }

      }
    }

    echo json_encode( $response );
    wp_die();
  } // end of search


} // end of class