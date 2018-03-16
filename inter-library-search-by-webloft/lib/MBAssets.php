<?php

add_action( 'wp_enqueue_scripts', array('MBAssets', 'wlkatalogsok_styles_and_scripts' )  );
add_action( 'admin_enqueue_scripts', array('MBAssets', 'wlkatalogsok_admin_styles_and_scripts' ) );

class MBAssets{

  public static function wlkatalogsok_styles_and_scripts() {
    $assets =
      array(
        'css' =>
          array(
            'wl_katalogsok-style' => 'wl-katalogsok.css'
          ),
        'js' =>
          array(
            'wl_katalogsok-iframe-script' => 'iframeheight.js',
            'wl_katalogsok-resizeiframe' => 'resizeiframe.js',
            'wl_katalogsok-script' => 'wl-katalogsok.js',
            'wl_katalogsok-tabs-script' => 'tabs.js',
          )
      );


    self::enqueueAssets($assets);
  }


  public static function wlkatalogsok_admin_styles_and_scripts() {

    $assets =
      array(
        'css' =>
          array(
            'wl_katalogsok-admin-style' => 'admin.css'
          ),
        'js' =>
          array(
            'wl_katalogsok-admin-script' => 'admin.js?v=3.5.0',
          )
      );

    self::enqueueAssets($assets);
  }


  public static function enqueueAssets( $assets ){
    foreach ($assets as $type => $type_rows) {
      if ( $type == 'css'){
        foreach ($type_rows as $handle => $file) {
          wp_enqueue_style( $handle, plugins_url(  'assets/css/'.$file, KS_FILE ), false, '3.5.0', 'all' );
        }
      }
      else if ( $type == 'js' ){
        foreach ($type_rows as $handle => $file) {
          wp_enqueue_script( $handle, plugins_url( 'assets/js/'.$file, KS_FILE ), array('jquery'), '3.5.0');
        }
      }
    }
  }


}

?>