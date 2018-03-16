<?php

class Tidemann{
  public static function sanitizeQuery( $query ){
    $query_tmp = trim ($query);
    // hvis flere ord UTEN ANF&Oslash;RSEL setter vi AND mellom
    if ((stristr($query_tmp , " ")) && (!stristr($query_tmp , "\""))) {
        $query_tmp = str_replace(" ", "+AND+", $query_tmp);
    }

    if (stristr($query_tmp, "\"")) {
    //    $query_tmp = str_replace("\"", "", $query_tmp); // fjerne anf&oslash;rsel
    }
    $query_tmp = str_replace(" ", "+", trim($query_tmp)); // kan ikke ha mellomrom i URL


    return $query_tmp;
  }


  public static function getItem( $library, $item_id ){
    $url = $library['server'] . '?version=1.2&operation=searchRetrieve&maximumRecords=10&recordSchema=marcxchange&query=rec.identifier=' . $item_id;
    $treff = tidemann_sok($url, "1");
    $treff[0]['biblioteksystem'] = "tidemann";

    return $treff;
  }


}// end of class