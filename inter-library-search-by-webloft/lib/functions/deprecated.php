<?php
//***********************************************************
// Deprecated functions
//***********************************************************

function bestandsinfo ($status , $restriction) {
  return getStockInformationByStatusCode($status , $restriction);
}


function hent_enkeltpost($bibkode , $bibtype , $postid){
  return getSinglePost ($bibkode , $bibtype , $postid);
}


function bibnr_to_name($bibnr){
  return getLibraryNameById($bibnr);
}

function wl_katalogsok_func($atts) {
  return MBShortcode::searchCatalog($atts);
}

?>