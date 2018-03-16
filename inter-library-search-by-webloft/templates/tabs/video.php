<?php

// S&oslash;ker opp video og viser treff
// Bruker $qsokeord som ubehandlet s&oslash;keord
//
// Trenger url, omslagsbilde, tittel, forfatter og kilde... flere?

$qsokeord = stripslashes(strip_tags($_REQUEST['qsokeord']));

// ******************* Disse basene skal med **********************
require_once ("../../lib/functions/functions.php"); // funksjoner vi har bruk for
include ("enkeltbaser/filmbib.php"); // Legge treff fra Filmbib til $videotreff

// ******************* De printer ut sine egne treff! **********************

@$pepsi = (int) count ($videotreff);
echo '<script>' . "\n";
echo 'jQuery("div.videoantalltreff" ).html("(' . $pepsi . ')")';
echo '</script>' . "\n";

?>
