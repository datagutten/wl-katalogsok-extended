<?php

class WL_CommonSearch{

  public static function query( $url ){
    $response = wp_remote_get( $url );

    if ( wp_remote_retrieve_response_code( $response ) == 200 ){
      return wp_remote_retrieve_body( $response );
    }
    else{
      _log('error: WL_CommonSearch::query');
      _log($response);
      return null;
    }
  }


  public static function buildQuery($host, $query_args=array() ){
    $query = $host;

    $query_string = null;
    if ( is_array($query_args) ){
      foreach ($query_args as $parameter => $value) {

        if (is_string($value)||is_numeric($value)){
          $query_string .= self::buildQueryParameter($parameter, $value);
        }
        else if ( is_array($value) ){
          foreach ($value as $key => $sub_value) {
            $query_string .= self::buildQueryParameter($parameter, $sub_value);
          }
        }
      }
    }

    if ( $query_string ){
      $query .= "?".$query_string;
    }


    return $query;
  }


  public static function buildQueryParameter( $parameter, $value ){
    return sprintf('%s=%s&', $parameter, $value );
  }


} // end of class