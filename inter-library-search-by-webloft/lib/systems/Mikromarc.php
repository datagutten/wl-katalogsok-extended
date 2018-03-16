<?php

class Mikromarc{
  public static function sanitizeQuery( $query ){
    $query_tmp = trim ($query);

    $query_tmp = str_replace(" &aring; ", " ", $query_tmp); // korte ord g&aring;r ikke
    $query_tmp = str_replace(" i ", " ", $query_tmp); // korte ord g&aring;r ikke
    $query_tmp = str_replace(" en ", " ", $query_tmp); // korte ord g&aring;r ikke
    $query_tmp = str_replace(" et ", " ", $query_tmp); // korte ord g&aring;r ikke
    $query_tmp = str_replace(" ei ", " ", $query_tmp); // korte ord g&aring;r ikke
    $query_tmp = str_replace(" og ", " ", $query_tmp); // korte ord g&aring;r ikke

    if ((stristr($query_tmp , " ")) && (!stristr($query_tmp , "\""))) { // hvis flere ord UTEN ANF&Oslash;RSEL setter vi AND mellom samt en ny cql.anywhere (spesielt for Mikromarc)
      $query_tmp = str_replace(" ", "+AND+cql.anywhere%3d", $query_tmp);
      $query_tmp = str_replace("%2A", "", $query_tmp); // m&aring; fjerne * igjen hvis AND-s&oslash;k
    }
    $query_tmp = str_replace (". " , " " , $query_tmp); // space for ikke &aring; &oslash;delegge cql.anywhere
    $query_tmp = str_replace(" ", "+", trim($query_tmp)); // kan ikke ha mellomrom i URL
    $query_tmp = str_replace(",", "%2C+" , $query_tmp);
    $query_tmp = str_replace("\"", "%22", $query_tmp); // fikse fnutter

    return $query_tmp;
  }
}