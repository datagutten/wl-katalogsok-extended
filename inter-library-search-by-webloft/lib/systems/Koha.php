<?php

class Koha extends WL_CommonSearch{
  protected $Url;
  protected $Position;
  protected $ItemId;
  protected $Xml;
  protected $XmlObject;
  protected $Results;
  protected $ResultsCount;
  protected $Library;
  protected $PostsPerPage;
  protected $Query;


  function __construct( $server, $query, $position, $posts_per_page ){
    $this->Server = $server;
    $this->Query  = $query;
    $this->Position = $position;
    $this->PostsPerPage = $posts_per_page;
    $this->setQueryUrl();
  }


  function setQueryUrl(){

    $query_args =
      array(
        'idx'         => 'kw',
        'count'           =>  ( $this->PostsPerPage <= 50 ) ? $this->PostsPerPage : 25,
        'q'           => $this->Query,
        'sort_by'    => 'relevance',
        "format"     => 'rss2'
      );


    $url = $this->Server.'/cgi-bin/koha/opac-search.pl';


    $this->QueryUrl = $this->buildQuery ( $url, $query_args );
    // _log($this->QueryUrl);
  }




  public static function sanitizeQuery( $query ){
    $query_tmp = trim ($query);
    if (stristr($query_tmp, "\"")) {
      $query_tmp   = str_replace("\"", "", $query_tmp); // fjerne anf&oslash;rsel
      $kohafrase = 1; // frases&oslash;k aktivt - se lenger ned n&aring;r URL defineres.
    }
    $query_tmp = urlencode($query_tmp);

    return $query_tmp;
  }


  function countResults() {
    $this->ResultsCount = 0;

    if ( $this->XmlObject ){
      $this->ResultsCount = $this->XmlObject->channel->children('opensearch', true)->totalResults;
    }

    return $this->ResultsCount;
  }


  function search()  {

    $this->Xml = get_content( $this->QueryUrl );

    $this->XmlObject  = simplexml_load_string( $this->Xml );
    $totalhtml    = '';
    $pendel       = 0;
    $hitcounter   = 0;
    $treff        = '';

    foreach ( $this->XmlObject->channel->item as $item) {
      $treff[$hitcounter]['permalink']  = (string)$item->link;
      $treff[$hitcounter]['tittel']     = (string)$item->title;
      $treff[$hitcounter]['tittelinfo'] = (string)$treff[$hitcounter]['tittel'];

      if (isset($item->description->p[0])) { // Koha-kn&oslash;l
        $beskrivelsetemp = strip_tags($item->description->p[0]);
        $beskrivelsetemp = preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "\n", $beskrivelsetemp));
      }
      else {
        $beskrivelsetemper = explode("<p>", $item->description);
        $beskrivelsetemp   = $beskrivelsetemper[1];
        $beskrivelsetemp   = strip_tags($item->description);
        $beskrivelsetemp   = preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "\n", $beskrivelsetemp)); // fjerne tabs, mellomrom...

        $beskrivelsetemper = explode("Place Hold on", $beskrivelsetemp); // vil ikke ha med den siste "Place hold on"-teksten
        $beskrivelsetemp   = $beskrivelsetemper[0];
      }
      $beskrivelsetemp                   = str_replace("By ", "", $beskrivelsetemp); // Hvorfor st&aring;r det "By " i beskrivelsen?
      $treff[$hitcounter]['beskrivelse'] = $beskrivelsetemp;

      $treff[$hitcounter]['orgisbn'] = (string)$item->children('dc', true)->identifier;
      $treff[$hitcounter]['orgisbn'] = str_replace("ISBN ", "", $treff[$hitcounter]['orgisbn']); // fjerne ISBN
      $treff[$hitcounter]['isbn'] = str_replace("-", "", $treff[$hitcounter]['orgisbn']); // fjerne bindestrek
      $treff[$hitcounter]['isbn'] = str_replace(" ", "", $treff[$hitcounter]['isbn']); // fjerne mellomrom

      // Fjerne ISBN fra beskrivelsesteksten
      $treff[$hitcounter]['beskrivelse'] = str_replace ($treff[$hitcounter]['orgisbn'] , "" , $treff[$hitcounter]['beskrivelse']);

      $hitcounter++;

    } // slutt p&aring; hvert item


    $this->Results = $treff;
    return $this->Results;
  }


} // end of class