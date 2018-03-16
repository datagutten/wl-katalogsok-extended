<?php

if ( ! defined( 'WPINC' ) ) {
  require_once("../../../../../wp-load.php");
}

// S&oslash;ker opp bokhyllaalle-b&oslash;ker og viser treff
// Bruker $qsokeord som ubehandlet s&oslash;keord
//
// Trenger url, omslagsbilde, tittel, forfatter og kilde... flere?

$qsokeord = cleanValue( _is($_REQUEST,'qsokeord') );

// ******************* Disse basene skal med **********************
require_once ("../../lib/functions/functions.php"); // funksjoner vi har bruk for
include ("enkeltbaser/bokhyllaalt.php"); // Legge treff fra bokhyllaalle til $bokhyllatreff
// ******************* De printer ut sine egne treff! **********************


$count_bokhylla_results = 0;
if ( isset($bokhyllatreff) && is_array($bokhyllatreff) ){
  $count_bokhylla_results = (int)count($bokhyllatreff);
}
echo '<script>' . "\n";
echo 'jQuery("div.bokhyllaantalltreff" ).html("(' . $count_bokhylla_results . ')")';
echo '</script>' . "\n";
