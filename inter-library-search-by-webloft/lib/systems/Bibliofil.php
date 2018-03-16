<?php

class Bibliofil{
  protected $Url;
  protected $Position;
  protected $Xml;
  protected $XmlObject;
  protected $Results;
  protected $ResultsCount;


  function __construct( $url, $position ){
    $this->Url = $url;
    $this->Position = $position;
  }


  public static function sanitizeQuery( $query ){
    $query_tmp = trim ($query);
    $query_tmp = str_replace(" &aring; ", " ",  $query_tmp); // korte ord g&aring;r ikke
    $query_tmp = str_replace(" i ", " ",  $query_tmp); // korte ord g&aring;r ikke
    $query_tmp = str_replace(" en ", " ", $query_tmp); // korte ord g&aring;r ikke
    $query_tmp = str_replace(" et ", " ", $query_tmp); // korte ord g&aring;r ikke
    $query_tmp = str_replace(" ei ", " ", $query_tmp); // korte ord g&aring;r ikke
    $query_tmp = str_replace(" og ", " ", $query_tmp); // korte ord g&aring;r ikke


    if ((stristr($query_tmp , " ")) && (!stristr($query_tmp , "\"")) && (!stristr($query_tmp , ","))) { // hvis flere ord UTEN ANF&Oslash;RSEL ELLER KOMMA setter vi AND mellom
      $query_tmp = str_replace(" ", "+AND+", $query_tmp);
    }

    $query_tmp = str_replace ("." , "" , $query_tmp);
    $query_tmp = str_replace(" ", "+", trim($query_tmp)); // kan ikke ha mellomrom i URL
    $query_tmp = str_replace(",", "%2C+" , $query_tmp);
    $query_tmp = str_replace("\"", "%22", $query_tmp); // fikse fnutter


    if (stristr($query_tmp, "%2C+") && !stristr($query_tmp, "%22")) {
      $query_tmp = "%22" . $query_tmp . "%22";
    }

    return $query_tmp;
  }


  public static function getItem( $library, $item_id ){
    $url = $library['server'] . '?version=1.2&operation=searchRetrieve&maximumRecords=10&query=dc.identifier=/bib.identifierAuthority=local%20' . $item_id;
    $treff = bibliofil_sok($url, "1");
    $treff[0]['biblioteksystem'] = "bibliofil";

    return $treff;
  }


  function countResults() {
    $this->ResultsCount = 0;
    if ( is_object($this->XmlObject) ) {
      $namespaces = $this->XmlObject->getNameSpaces(true);
      $srw        = $this->XmlObject->children($namespaces['SRU']); // alle som er srw:ditten og srw:datten

      $this->ResultsCount = $srw->numberOfRecords;
    }

    return $this->ResultsCount;
  }


  function search() {
      // Vi m&aring; slenge p&aring; posisjon i URL-en
      $url = $this->Url . "&startRecord=" . $this->Position;
      // _log($url);
      $this->Xml = wp_remote_retrieve_body( wp_remote_get( $url ) );
      // $sru_datafil = get_content($url);
      // _log($this->Xml);
      // $xml = str_replace("\n", null, $this->Xml );
      $this->XmlObject = simplexml_load_string($this->Xml);

      if (!method_exists($this->XmlObject, 'getNameSpaces') ){
        return null;
      }

      // $namespaces = $this->XmlObject->getNameSpaces(true);
      // $srw        = $this->XmlObject->children($namespaces['SRU']); // alle som er srw:ditten og srw:datten

      $hepphepp = str_replace("marcxchange:", "", $this->Xml);
      $hepphepp = strip_tags($hepphepp, "<record><leader><controlfield><datafield><subfield>");
      $hepphepp = stristr($hepphepp, "<record");

      $newfile = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
      $newfile .= "<collection>\n";
      $newfile .= $hepphepp;
      $newfile .= "</collection>";

      // Retrieve a set of MARC records from a file

      require WL_PLUGIN_PATH.'/File/MARCXML.php';

      $journals = new File_MARCXML($newfile, File_MARC::SOURCE_STRING);

      $totalhtml  = '';
      $pendel     = 0;
      $hitcounter = 0;
      $treff      = array();;

      while ($record = $journals->next()) {
        if ($record->getField("001")) {
          $identifier = $record->getField("001");
          $treff[$hitcounter]['identifier'] = trim(substr($identifier, 5)); // fjerne feltkoden i starten
        }

        if ($record->getField("996")) {
          $permalink = $record->getField("996")->getSubfield("u");
          $permalink = substr($permalink, 5); // fjerne feltkoden i starten
          if (stristr($permalink, "http:")) { // hvis begynner med http:
            $treff[$hitcounter]['permalink'] = $permalink;
          } else { // ellers m&aring; vi legge til http:
            $treff[$hitcounter]['permalink'] = "http://" . $permalink;
          }
        } else { // no permalink
          $treff[$hitcounter]['permalink'] = "";
        }

        if ($record->getField("245")) {
          $tittel                          = $record->getField("245")->getSubfield("a");
          $treff[$hitcounter]['tittel']    = substr($tittel, 5); // fjerne feltkoden i starten
          $subtittel                       = $record->getField("245")->getSubfield("b");
          $treff[$hitcounter]['subtittel'] = substr($subtittel, 5); // fjerne feltkoden i starten
          if ($record->getField("245")->getSubfield("c")) {
            $ansvar                      = $record->getField("245")->getSubfield("c");
            $treff[$hitcounter]['ansvarsangivelse'] = substr($ansvar, 5); // fjerne feltkoden i starten
          }
        }

        if ($record->getField("574")) { // Originaltittel
          $originaltittel = $record->getField("574")->getSubfield("a");
          $originaltittel = substr($originaltittel, 5); // fjerne feltkoden i starten
          $originaltittel = str_ireplace ("originaltittel:" , "", $originaltittel);
          $originaltittel = str_ireplace ("originaltittel :" , "", $originaltittel);
          $originaltittel = str_ireplace ("originaltitler:" , "", $originaltittel);
          $originaltittel = str_ireplace ("originaltitler :" , "", $originaltittel);
          $treff[$hitcounter]['originaltittel'] = trim($originaltittel);
        }

        if ($record->getField("100")) {
          $forfatter                       = $record->getField("100")->getSubfield("a");
          $treff[$hitcounter]['forfatter'] = substr($forfatter, 5); // fjerne feltkoden i starten
          if ($record->getField("100")->getSubfield("d")) {
            $forfatterliv                = $record->getField("100")->getSubfield("d");
            $treff[$hitcounter]['forfatterliv'] = substr($forfatterliv, 5); // fjerne feltkoden i starten
          }
        }

        if ($record->getField("110")) {
          $korporasjon                       = $record->getField("110")->getSubfield("a");
          $treff[$hitcounter]['korporasjon'] = substr($korporasjon, 5); // fjerne feltkoden i starten
        }

        if ($record->getField("20")) {
          $isbn                       = $record->getField("20")->getSubfield("a");
          $treff[$hitcounter]['isbn'] = substr($isbn, 5); // fjerne feltkoden i starten
          if ($record->getField("20")->getSubfield("b")) {
            $heftetbundet = $record->getField("20")->getSubfield("b");
            $treff[$hitcounter]['heftetbundet'] = substr($heftetbundet, 5); // fjerne feltkoden i starten
          }
        }

        if ($record->getField("520")) {
          $beskrivelse                       = $record->getField("520")->getSubfield("a");
          $treff[$hitcounter]['beskrivelse'] = substr($beskrivelse, 5); // fjerne feltkoden i starten
        }

        if ($record->getField("260")) {
          $utgitthvor                       = $record->getField("260")->getSubfield("a");
          $treff[$hitcounter]['utgitthvor'] = substr($utgitthvor, 5);
          $utgitthvem                       = $record->getField("260")->getSubfield("b");
          $treff[$hitcounter]['utgitthvem'] = substr($utgitthvem, 5);
          $utgittaar                        = $record->getField("260")->getSubfield("c");
          $utgittaar                        = substr($utgittaar, 5);
          $utgittaar                        = str_replace("[", "", $utgittaar); // disse to linjene fjerner [ og ] i &aring;rstall
          $treff[$hitcounter]['utgittaar']  = str_replace("]", "", $utgittaar);
          $utgittaar                        = str_replace("<", "", $utgittaar); // disse to linjene fjerner < og > i &aring;rstall
          $treff[$hitcounter]['utgittaar']  = str_replace(">", "", $utgittaar);

        }

        if ($record->getField("300")) { // omfang
          $omfang = $record->getField("300")->getSubfield("a");
          $omfang = substr($omfang, 5);
          if ($record->getField("300")->getSubfield("b")) {
            $cheese = $record->getField("300")->getSubfield("b");
            $cheese = substr($cheese, 5);
            $omfang .= " : " . $cheese;
          }
        $treff[$hitcounter]['omfang'] = $omfang;
        }


        if ($record->getField("019")) {
          $materialkode                       = $record->getField("019")->getSubfield("b");
          $treff[$hitcounter]['materialkode'] = substr($materialkode, 5);

          // Hvis flere adskilt med komma g&aring;r vi for den f&oslash;rste

          if (stristr($treff[$hitcounter]['materialkode'], ",")) {
            $temp                               = explode(",", $treff[$hitcounter]['materialkode']);
            $treff[$hitcounter]['materialkode'] = $temp[0];
          }
        }

        // Ansvarsangivelse

        if (isset($treff[$hitcounter]['ansvarsangivelse'])) {
          $treff[$hitcounter]['opphav'] = $treff[$hitcounter]['ansvarsangivelse'];
        }

        if (isset($treff[$hitcounter]['forfatter'])) {
          $treff[$hitcounter]['opphav'] = $treff[$hitcounter]['forfatter'];
          if (isset($treff[$hitcounter]['forfatterliv'])) {
            $treff[$hitcounter]['opphav'] .= " (" . $treff[$hitcounter]['forfatterliv'] . ")";
          }
        }

        if (isset($treff[$hitcounter]['korporasjon'])) {
          $treff[$hitcounter]['opphav'] = $treff[$hitcounter]['korporasjon'];
        }

        // Tittel

        $treff[$hitcounter]['tittelinfo'] = $treff[$hitcounter]['tittel'];
        if ($treff[$hitcounter]['subtittel'] != '') {
          $treff[$hitcounter]['tittelinfo'] .= " : " . $treff[$hitcounter]['subtittel'];
        }
        if (isset($treff[$hitcounter]['materialkode'])) {
          if ($treff[$hitcounter]['materialkode'] == 'ee') { // DVD?
            $treff[$hitcounter]['tittelinfo'] .= " : DVD";
          }
        }
        if (isset($treff[$hitcounter]['materialkode'])) {
          if ($treff[$hitcounter]['materialkode'] == 'ef') { // DVD?
            $treff[$hitcounter]['tittelinfo'] .= " : Bluray";
          }
        }

          // GJ&Oslash;RE TITTELINFO PEN:
          $treff[$hitcounter]['tittelinfo'] = str_replace(": :", ":", $treff[$hitcounter]['tittelinfo']);

      // Ikon
        if (isset($treff[$hitcounter]['materialkode'])) { // materialkode er angitt

          switch ($treff[$hitcounter]['materialkode']) {
            case "ab":
              $treff[$hitcounter]['type'] = "atlas";
              break;
            case "ee":
              $treff[$hitcounter]['type'] = "dvd";
              break;
            case "ef":
              $treff[$hitcounter]['type'] = "bluray";
              break;
            case "l":
              $treff[$hitcounter]['type'] = "bok";
              break;
            case "dc":
              $treff[$hitcounter]['type'] = "cd";
              break;
            case "de":
              $treff[$hitcounter]['type'] = "digikort";
              break;
            case "ga":
              $treff[$hitcounter]['type'] = "nedlastbar";
              break;
            case "dd":
              $treff[$hitcounter]['type'] = "lyd";
              break;
            case "di":
              $treff[$hitcounter]['type'] = "lydbok";
              break;
            case "dz":
              $treff[$hitcounter]['type'] = "mp3-lyd";
              break;
            case "c":
              $treff[$hitcounter]['type'] = "note";
              break;
            case "ed":
              $treff[$hitcounter]['type'] = "vhs";
              break;
            case "dg":
              $treff[$hitcounter]['type'] = "musikk";
              break;
            default:
              $treff[$hitcounter]['type'] = "ukjent";
              break;
          }

        } else { // materialkode ikke angitt, ergo ukjent
          $treff[$hitcounter]['type'] = "ukjent";
        }

        // REPETERBARE FELTER SJEKKES HER

        foreach ($record->getFields() as $tag => $subfields) {

          if ($tag == '015') { // E-bok, sa De?
            foreach ($subfields->getSubfields() as $code => $value) {
              $ettfelt[(string) $code] = substr((string) $value, 5);
            }
            if (@$ettfelt['b'] == "eBokBibID") {
              $treff[$hitcounter]['ebokbibid'] = $ettfelt['a'];
            }
            unset($ettfelt);
          }

          // Bestand: Sjekke 850

          if ($tag == '850') {
            foreach ($subfields->getSubfields() as $code => $value) {
              $ettfelt[(string) $code] = substr((string) $value, 5);
            }
            $ettfelt['bibnavn'] = bibnr_to_name($ettfelt['a']);
            $etteks[] = $ettfelt;
            unset($ettfelt);
          }


          // Lese utdrag: Sjekke 856

          if ($tag == '856') {
            foreach ($subfields->getSubfields() as $code => $value) {
              $ettfelt[(string) $code] = substr((string) $value, 5);
            }
            if ((isset($ettfelt['z'])) && ($ettfelt['z'] == "Les utdrag")) {
              if (isset($ettfelt['u'])) {
                $treff[$hitcounter]['pdfutdrag'] = $ettfelt['u'];
              }
            }

          }

          // Dewey: Sjekke 082 $a

          if ($tag == '082') {
            foreach ($subfields->getSubfields() as $code => $value) {
              $ettfelt[(string) $code] = substr((string) $value, 5);
            }
            if (isset($ettfelt['a'])) {
              $dewey = $ettfelt['a'];
              $endewey[] = $dewey;
            }
          }

          // Emneord: Sjekke 650 $a

          if ($tag == '650') {
            foreach ($subfields->getSubfields() as $code => $value) {
              $ettfelt[(string) $code] = substr((string) $value, 5);
            }
            if ((isset($ettfelt['a'])) && ($ettfelt['a'] != "")) {
              $emneord = $ettfelt['a'];
              $ettemneord[] = $emneord;
            }
          }

          // Generell note: Sjekke 500 $a

          if ($tag == '500') {
            foreach ($subfields->getSubfields() as $code => $value) {
              $ettfelt[(string) $code] = substr((string) $value, 5);
            }
            if ((isset($ettfelt['a'])) && ($ettfelt['a'] != "")) {
              $generellnote = $ettfelt['a'];
              $engenerellnote[] = $generellnote;
            }
          }

          // Innholdsnote: Sjekke 505 $a

          if ($tag == '505') {
            foreach ($subfields->getSubfields() as $code => $value) {
              $ettfelt[(string) $code] = substr((string) $value, 5);
            }
            if ((isset($ettfelt['a'])) && ($ettfelt['a'] != "")) {
              $innholdsnote = $ettfelt['a'];
              $eninnholdsnote[] = $innholdsnote;
            }
          }

          // Medarbeidernote: Sjekke 511 $a

          if ($tag == '511') {
            foreach ($subfields->getSubfields() as $code => $value) {
              $ettfelt[(string) $code] = substr((string) $value, 5);
            }
            if ((isset($ettfelt['a'])) && ($ettfelt['a'] != "")) {
              $medarbeidere = $ettfelt['a'];
              $enmedarbeidere[] = $medarbeidere;
            }
          }

          // Titler: Sjekke 740 $a

          if ($tag == '740') {
            foreach ($subfields->getSubfields() as $code => $value) {
              $ettfelt[(string) $code] = substr((string) $value, 5);
            }
            if ((isset($ettfelt['a'])) && ($ettfelt['a'] != "")) {
              $titler = $ettfelt['a'];
              $entittel[] = $titler;
            }
          }

        }

        if (isset($ettemneord) && (is_array($ettemneord))) {
          $ettemneord = array_unique ($ettemneord);
          sort ($ettemneord);
          $treff[$hitcounter]['emneord'] = $ettemneord;
        }
        @$treff[$hitcounter]['bestand'] = $etteks;
        @$treff[$hitcounter]['dewey'] = $endewey;
        @$treff[$hitcounter]['generellnote'] = $engenerellnote;
        @$treff[$hitcounter]['innholdsnote'] = $eninnholdsnote;
        @$treff[$hitcounter]['medarbeidere'] = $enmedarbeidere;
        @$treff[$hitcounter]['titler'] = $entittel;

        unset($etteks, $endewey, $ettemneord, $engenerellnote, $eninnholdsnote, $enmedarbeidere, $entittel);

        $hitcounter++;
      } // slutt p&aring; hvert item

      /*
      Omslag (hvordan?)
      Tittel (&aring;rstall)   ev     Tittel : DVD (&aring;rstall)
      Forfatter
      Beskrivelse (ligger i 520 $a noen ganger)
      Ikon basert p&aring; materialtype (liste i dokumenttyper.pdf)

      AKTUELLE KODER:
      ee eller ef (DVD eller Bluray)
      l (bok)
      dc (CD)
      de (digikort)
      ga (nedlastbar fil)
      dd (avspiller med lydfil)
      di (lydbok)
      dz (mp3, vi bruker lyd)
      c (Musikktrykk)
      ed (Videokassett VHS)
      dg (Musikk)

      ALLE IKONER VI TRENGER: https://www.iconfinder.com/iconsets/windows-8-metro-style

      IKONER: Bok, lyd, note, film DVD, film VHS

      */

      $this->Results = $treff;

      return $this->Results;
    }



} // end of class