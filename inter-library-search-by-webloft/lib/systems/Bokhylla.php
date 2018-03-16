<?php

class Bokhylla{

  public static function sanitizeQuery( $query ){
    $query_tmp = trim ($query);
    $query_tmp = utf8_decode($query_tmp);
    $query_tmp = str_replace(" ", "+AND+", trim($query_tmp)); // Dette er semi-frases&oslash;k

    return $query_tmp;
  }

  public static function search( $url, $posisjon ) {
    // Vi m&aring; slenge p&aring; posisjon i URL-en
    $url = $url . "&startIndex=" . $posisjon;

    $xml_datafil = get_content($url);
    $xml_data    = simplexml_load_string($xml_datafil);
    $hitcounter   = 0;
    $treff        = '';


    foreach ($xml_data->entry as $entry) {
      if (isset($entry->link[5])) {
        $treff[$hitcounter]['pdflenke'] = $entry->link[5]->attributes()->href;
      }

      $namespaces = $entry->getNameSpaces(true);
      $nb = $entry->children($namespaces['nb']); // alle som er nb:ditten og nb:datten

      // ISBN
      $isbn = $nb->isbn;
      if (stristr($isbn , ";")) { // hvis det er flere inneholder strengen semikolon
        $isbn = trim(stristr($isbn , ";" , TRUE)); // da tar vi det f&oslash;rste
      }
      else {
        $isbn = trim ($isbn); // fint som det er. Takk.
      }
      $treff[$hitcounter]['isbn'] = $isbn;

      // URN
      $urn = $nb->urn;
      if (stristr($urn , ";")) { // hvis det er flere inneholder strengen semikolon
        $urn = trim(stristr($urn , ";" , TRUE)); // da tar vi det f&oslash;rste
      }
      else {
        $urn = trim ($urn); // fint som det er. Takk.
      }
      $treff[$hitcounter]['urn'] = $urn;

      // Omslag og lenke
      $treff[$hitcounter]['fulltekst'] = "http://urn.nb.no/" . $nb->urn;
      $treff[$hitcounter]['permalink'] = "http://urn.nb.no/" . $nb->urn;
      // $treff[$hitcounter]['omslag'] = COVER_SERVER . "/urn/" . substr($treff[$hitcounter]['urn'], 8) . ".jpg";
      $treff[$hitcounter]['omslag'] = null;

      $treff[$hitcounter]['tittel'] = $entry->title;
      $treff[$hitcounter]['tittelinfo'] = $entry->title;
      $treff[$hitcounter]['forfatter'] = $nb->namecreator;
      $treff[$hitcounter]['ansvarsangivelse'] = $nb->namecreator;
      $treff[$hitcounter]['opphav'] = $nb->namecreator;
      $treff[$hitcounter]['type'] = 'bok'; // Nei, sier De virkelig det?
      $treff[$hitcounter]['status'] = 'bokhylla';

      $treff[$hitcounter]['utgittaar'] = $nb->year;

      $beskrivelse = $entry->summary;
      $beskrivelse   = preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "\n", $beskrivelse)); // fjerne tabs, mellomrom...
      $treff[$hitcounter]['beskrivelse'] = $beskrivelse;

      $tempemneord = explode (";" , $nb->subjecttopic);
      foreach ($tempemneord as $ettemne) {
        $emneord[] = $ettemne;
      }
      $treff[$hitcounter]['emneord'] = $emneord;

      $hitcounter++;
    }

    return $treff;
  }


  public static function countResults($url) {
    $xml_datafil = get_content($url);
    $xml_data    = simplexml_load_string($xml_datafil);

    $feedsubtitle = $xml_data->subtitle;
    $antallfunnet = substr(stristr($feedsubtitle , " of ") , 4);

    return $antallfunnet;
  }


}// end of class