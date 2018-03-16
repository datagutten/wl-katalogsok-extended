<?php

// S&oslash;ker opp e-b&oslash;ker og viser treff
// Bruker $qsokeord som ubehandlet s&oslash;keord
//

// Vi dropper e-b&oslash;ker fra NB, for d&aring;rlige metadata, ingen direktelenke og ikke noe omslag

// ************** Disse basene skal med **********************
//include ("enkeltbaser/ebokbib.php"); // Hente e-b&oslash;ker fra Ebokbib
include ("enkeltbaser/bokselskap.php"); // Hente treff fra Bokselskap
include ("enkeltbaser/openlibrary.php"); // Hente treff fra Open Library
// ************** Printer ut sine egne treff! **********************

if ( isset($eboktreff) && is_array($eboktreff) ){
  echo '<script>' . "\n";
  echo 'jQuery("div.ebokantalltreff" ).html("(' . count ($eboktreff) . ')")';
  echo '</script>' . "\n";
}