<?php
/*
Plugin Name: WL Katalogs&oslash;k
Plugin URI: http://www.bibvenn.no/
Description: Interlibrary search for your Wordpress site! NORWEGIAN: Setter inn s&oslash;kefelt som lar deg s&oslash;ke i mange forskjellige bibliotekssystemer.
Version: 3.5.7
Author: H&aring;kon Sundaune / Bibliotekarens beste venn
Author URI: http://www.bibvenn.no/
Text Domain: inter-library-search-by-webloft
*/


define('ILS_URL', plugins_url('' , __FILE__));
define('KS_FILE',  __FILE__ );
define('WL_PLUGIN_PATH', dirname( __FILE__ ) );



include('conf/globals.php');
include('systemer.php');
include('lib/functions/deprecated.php');
include('lib/functions/functions.php');
include('lib/utils.php');

include('lib/systems/WL_CommonSearch.php');

include('lib/systems/Alma.php');
include('lib/systems/Bibliofil.php');
include('lib/systems/Mikromarc.php');
include('lib/systems/Tidemann.php');
include('lib/systems/KohaSru.php');
include('lib/systems/Koha.php');

include('lib/search-controller/WL_CommonSearchController.php');
include('lib/search-controller/LibrarySearchController.php');
include('lib/search-controller/OpenLibrarySearchController.php');
include('lib/search-controller/EbookSearchController.php');
include('lib/search-controller/BokHyllaSearchController.php');
include('lib/search-controller/FilmbibSearchController.php');

include('lib/MBAssets.php');
include('lib/WL_Shortcode.php');
include('lib/WL_Booking.php');
include('lib/WL_Search.php');
include('lib/admin/MBAdmin.php');
include('lib/WL_ILS_Widget.php');
include('lib/WL_Ajax.php');


add_action( 'plugins_loaded', 'wlkatalogsok_load_textdomain' );
function wlkatalogsok_load_textdomain() {
  load_plugin_textdomain( 'inter-library-search-by-webloft', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}