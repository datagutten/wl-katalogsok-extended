<?php

class LibrarySearchController extends WL_CommonSearchController{
  protected $Library;
  protected $Results;
  protected $QueryResult;
  protected $enkelinfo;
  protected $SingleItemUrl;

  function __construct( $request ){
    foreach ($request as $key => $value) {
      $this->$key = $value;
    }

    $this->library_id           = cleanValue( _is($this, 'library_id' ) );
    $this->omslagbokkilden      = cleanValue( _is($this, 'omslagbokkilden' ) );
    $this->omslagnb             = cleanValue( _is($this, 'omslagnb') );
    $this->hamedbilder          = cleanValue( _is($this, 'hamedbilder' ));
    $this->wl_query             = cleanValue( _is($this, 'wl_query' ));
    $this->resources            = cleanValue( _is($this, 'resources') );

    $this->treffbokhylla        = (int) ( _is($this, 'treffbokhylla') );
    $this->hoyretrunk           = (int) ( _is($this, 'hoyretrunk') );
    $this->viseavansertlenke    = (int) ( _is($this, 'viseavansertlenke') );
    $this->enkeltpostnyttvindu  = (int) ( _is($this, 'enkeltpostnyttvindu') );
    $this->skjulesoketips       = (int) ( _is($this, 'skjulesoketips') );
    $this->treffperside         = (int) ( _is($this, 'treffperside', 25) );
    $this->posisjon             = (int) ( _is($this, 'pagination', 1) );

    $this->openNewWindow = ( $this->enkeltpostnyttvindu == "1") ? ' target="_blank"' : ' target="_top"';
    $this->SingleItemUrl = getSingleItemUrl();

    parent::__construct();

    if ( $this->hoyretrunk == "1" ) {
      $this->wl_query .= "*";
    }

    // Jukse til s&oslash;keord
    // Stjerne hvis h&oslash;yretrunkering er angitt i innstillinger
    $this->wl_query = str_replace(", ", ",", $this->wl_query); // fjerne mellomrom i invertert form
    $this->wl_query = str_replace("*", "%2A", $this->wl_query); // ekte asterisk &oslash;delegger s&oslash;ket
  }


  function get( $attribute ){
    return $this->$attribute;
  }


  function startSearch(){
    // _log('WL_SearchController::startSearch');
    $this->Results = array();
    include( getConfigPath("library_list.php") );
    // _log( 'lib id:'. $this->library_id);
    if (_is($libraries, $this->library_id) ){
      // _log('lib is set');
      $this->Library  = $libraries[$this->library_id];

      $Search = new WL_Search($this->Library, $this->wl_query);
      $Search->setLibraryIndex($this->library_id);

      // $minavdkode         = $this->Library['department_id'];

      // S&Oslash;K I BOKHYLLA - MITTSYSTEM BLIR BOKHYLLA
      if ( $this->dobokhylla == "1" ) { // vi skal vise treff fra Bokhylla
        $Search->setLibrarySystem('bokhylla');
      }

      $this->QueryResult = $Search->runQuery($this->posisjon, $this->treffperside);

      $this->setPagination();

      //_log('WL_SearchController, has results: '. $this->QueryResult['count-items']);
      if ( $this->QueryResult['count-items'] > 0) { // kan være tom
        if ($this->hamedbilder == "1") { // show images
          foreach ( $this->QueryResult['items'] as $enkelttreff => &$treff) {
            // Det enkleste er &aring; bruke v&aring;r egen server hvis vi har ISBN
            // if ( _is($treff, 'isbn') && !isset($treff['omslag']) ) { // vi har ISBN men ikke omslag
            //   $treff = $Search->getItemInfoFromNationalLibraryByIsbn($treff);
            // }

            // Hvis denne innstillingen er sl&aring;tt p&aring; og vi fortsatt ikke har omslag
            if ( $this->omslagbokkilden == "1" && !isset($treff['omslag']) ) {
              // Finne info fra Bokkilden
              // Hvis vi har ISBN
              if ( $isbn = _is($treff, 'isbn') ) {
                $external_info = $Search->getItemInfoFromBokkilden($treff);

                if ( $cover = trim(_is($external_info,'omslag')) ){
                  $treff['omslag'] = $cover;
                }
                if ( $description = trim(_is($external_info,'beskrivelse')) ){
                  $treff['beskrivelse'] = $description;
                }
              }
            }


            // Siste fors&oslash;k: S&oslash;ke i NB via URN , hvis omslag fra NB er sl&aring;tt p&aring; OG vi fortsatt ikke har omslag OG vi ikke har ISBN (har allerede s&oslash;kt p&aring; ISBN)
            // Men det m&aring; være b&oslash;ker (annet finnes jo ikke i NB)
            if (($this->omslagnb == "1") && (!isset($treff['omslag'])) && (!isset($treff['isbn'])) && ($treff['type'] == "bok")) {
              // Vi s&oslash;ker p&aring; tittel og ser hvilke URN-er vi f&aring;r
              $treff = $Search->getItemInfoFromNationalLibraryByTitle($treff);
            } // slutt p&aring; hvis omslagnb er skrudd p&aring;
          } // slutt p&aring; foreach
        } // slutt p&aring; sjekk om "ha med bilder"-innstilling er satt
      } // slutt p&aring; sjekk om antallfunnet > 0

      /*if ( $single_item_url =  _is($_GET, 'enkeltposturl') ) { // Vi har det i referer, overstyrer den vi hadde
        $single_item_url = base64_decode(urldecode($single_item_url) );
      }*/

      if (  $this->Library['system'] != 'bokhylla') { // Meningsl&oslash;st med enkeltpostvisning ved s&oslash;k bare i Bokhylla
        if ( $this->SingleItemUrl ) { // det finnes en url til side hvor enkeltposter skal vises
          //_log($this->QueryResult['items']);
          if ( is_array($this->QueryResult['items']) && count($this->QueryResult['items']) ) {
            foreach ( $this->QueryResult['items'] as $item_index => &$result_item) { // for hvert treff i trefflista
              $result_item['biblioteksystem'] = $this->Library['system'] ;

              if ( $this->Library['system']  == "koha") { // Hvis koha - p&oslash;s all treffinfo inn i URL
                $result_item['bibkode'] = $this->library_id;
                $treffinfo = base64_encode(serialize($result_item));
              }
              else{
                $this->enkelinfo[$item_index]['bibsystem'] = $this->Library['system'];
                $this->enkelinfo[$item_index]['postid']    = _is( $result_item, 'identifier');
                $this->enkelinfo[$item_index]['bibkode']   = $this->library_id;
                $treffinfo = base64_encode( serialize($this->enkelinfo[$item_index]) );
              }


              if ( $permalink = _is($result_item, 'permalink') ){
                $result_item['external_link'] = $permalink;
              }


              if( stristr($this->SingleItemUrl , "?") ) { // Har allerede query variables
                $result_item['permalink'] =
                  $this->SingleItemUrl .
                    "&system=" .  $this->Library['system']  .
                    "&enkeltpostinfo=" . $treffinfo;
              }
              else { // Dette er den f&oslash;rste
                $result_item['permalink'] =
                  $this->SingleItemUrl .
                    "?system=" .  $this->Library['system']  .
                    "&enkeltpostinfo=" . $treffinfo;
              }


              $result_item['url'] = $result_item['permalink'];
            }
          }
        }
      }


      //_log('WL_SearchController: count items');
      if ( $this->QueryResult['count-items'] > 0) { // kan være tom
        foreach ( $this->QueryResult['items'] as $enkelttreff => &$treff) {
          // Verdier for hvert treff, som skal lagres i $results og sendes videre til results.php-malen
          $data = $Search->setItemArray($treff);

          if ( $isbn = trim(_is($treff, 'isbn')) ){
            $altmedisbn = $isbn;

            if ( $value = _is($treff, 'heftetbundet') ) {
              $altmedisbn .= " (" . $value . ")";
            }
            $data['isbn'] = "<strong>" . __('ISBN: ', 'inter-library-search-by-webloft') . "</strong>" . $altmedisbn . "\n";
          }

          if ( $pages = _is($treff,'omfang') ) {
            $data['omfang'] = "<strong>" . __('Omfang: ', 'inter-library-search-by-webloft') . "</strong>" . $pages;
          }

          if ( $org_title = _is($treff, 'originaltittel')  ) {
            $data['titteloriginal'] = "<strong>" . __('Originaltittel: ', 'inter-library-search-by-webloft') . "</strong>" . $org_title . "\n";
          }


          // BESTAND I BIBLIOFIL

          // Finner vi alltid i 850 - men hvis ikke er det utilgjengelig

          /*
          i 850 finner vi:

          $a  Institution/location  Eiende bibliotek/avdeling
          $b  Sublocation/collection  Filial- avdelings- eller samlingskode
          $c  Shelving location Hyllesignatur
          $f  Use restrictions  (Not in NORMARC)
          $h  Circulation status  (Not in NORMARC)
          $x  Date of circulation status  (Not in NORMARC)
          $y  Loan expiry date  (Not in NORMARC)

          */

          $bestandhtml = null;
          $available  = $utlant = $limited = $unavailable = 0;
          //Ikke vis bestand for serier
          if ( ($this->Library['system'] == 'bibliofil' or $this->Library['system'] == 'mikromarc') && empty($treff['serie']) ){

            if ( isset($treff['bestand']) && is_array($treff['bestand'])) {
              foreach ($treff['bestand'] as $enkelteks) {
                // _log($enkelteks);
                $status = $begrensning = 0;
                if ( _is($enkelteks,"h") ){
                  $status = $enkelteks["h"];
                }

                if ( _is($enkelteks,"f") ){
                  $begrensning = $enkelteks["f"];
                }


                if ( $status == 0){
                  if ( $begrensning == "2" || $begrensning == "3" || $begrensning == "4" || $begrensning == "6" ) {
                    $limited++;
                  }
                  else {
                    $available++;
                  }
                }
                elseif ( $status == 4 ){
                  $utlant++;
                }
                else{
                  $unavailable++;
                }
              } //foreach
            } // if
            $bestandhtml = "<br>\n";

            $totaleks = (int)$available + (int)$limited + (int)$utlant + (int)$unavailable;


            if ( $available > 0 ) {
              $data['status'] = 'ledig';
              $bestandhtml .= "<div class=\"tilgang_boks wl-catalog\">";
              $bestandhtml .= __('Ledig', 'inter-library-search-by-webloft') . '&nbsp;:&nbsp' . $available . "<br>\n";
              $bestandhtml .= "<div class=\"green dot\"></div>";
              $bestandhtml .= "</div>\n";
            }
            elseif ( $available == 0 && ( ($limited + $utlant) > 0)) {
              $data['status'] = 'utlant';
              $bestandhtml .= "<div class=\"tilgang_boks wl-catalog\">";
              $bestandhtml .= __('Utl&aring;nt el.l.', 'inter-library-search-by-webloft') . "&nbsp;:&nbsp;" . ($limited + $utlant) . "<br>\n";
              $bestandhtml .= "<div class=\"orange dot\"></div>";
              $bestandhtml .= "</div>\n";
            }
            elseif ($unavailable > 0) { // unavailable
              $data['status'] = 'ikke-ledig';
              $bestandhtml .= "<div class=\"tilgang_boks wl-catalog\">";
              $bestandhtml .= __('Ikke ledig', 'inter-library-search-by-webloft') . "&nbsp;:&nbsp;" . $unavailable . "<br>\n";
              $bestandhtml .= "<div class=\"red dot\"></div>";
              $bestandhtml .= "</div>\n";
            }
            else {
              $data['status'] = 'uklar';
              $bestandhtml .= "<div class=\"tilgang_boks wl-catalog\">";
              $bestandhtml .= __('Uklar bestand!', 'inter-library-search-by-webloft') . "<br>\n";
              $bestandhtml .= "<div class=\"orange dot\"></div>";
              $bestandhtml .= "</div>\n";
            }
          }
          elseif ( $this->Library['system'] == 'koha-sru' ){

            foreach ($treff['bestand'] as $status => $row) {
              $data['status'] = 'ledig';
              if ( $status == 'available' ){
                $bestandhtml .= $this->getStatusBox( __('Ledig', 'inter-library-search-by-webloft'), $treff['available_items'], $css_status='green dot', $css_box = 'tilgang_boks wl-catalog' );
              }
              else if ( $status == 'lent' && count($row) ){
                _log('lent');

              }

            }
          }

          // S&aring; bytter vi ut hvis vi har noe

          if ( $bestandhtml ) {
            // _log($bestandhtml);
            $data['bestand'] = $bestandhtml;
          }

          $data['utdrag'] = null;
          if ( $pdf_excerpt = _is($treff, 'pdfutdrag') ) {
            $data['utdrag'] = '[<a target="_blank" href="' . $pdf_excerpt . '">' . __('Les utdrag', 'inter-library-search-by-webloft') . '</a>]' . "\n";
          }

          $this->Results[] = $data;
        }
      }

      else{
        $this->Results = array();
      }
    }

    // _log($this->Results);
  } // close start search


  function getStatusBox( $label, $content, $css_status, $css_box = 'tilgang_boks' ){
    return sprintf('<div class="%s">%s &nbsp;:&nbsp %s<br/><div class="%s"></div></div>', $css_box, $label, $content, $css_status);
  }


  function setPagination(){
    // _log('WL_SearchController::setPagination');
    $ibokhylla = " ";

    if ($this->Library['system'] == "bokhylla") {
      $ibokhylla = __(' i Bokhylla ', 'inter-library-search-by-webloft');
    }

    $prev_page = $this->posisjon - $this->treffperside;
    $next_page = $this->posisjon + $this->treffperside ;
    if ($next_page > $this->QueryResult['count-items']) {
      $next_page = $this->QueryResult['count-items'];
    }

    $antalligjen = $this->treffperside;
    if ( ($next_page + $this->treffperside) > $this->QueryResult['count-items']) {
      $antalligjen = $this->QueryResult['count-items'] - $next_page;
    }

    $this->Pagination = '<div class="ils-results-pager">';


    if ( $this->QueryResult['count-items'] >= $this->treffperside ){
      $this->Pagination .= sprintf('<span>%s</span>', sprintf( esc_html__( 'Viser treff %1$s-%2$s av %3$s ved s&oslash;k%4$setter %5$s.', 'inter-library-search-by-webloft'), $this->posisjon, $next_page, $this->QueryResult['count-items'], $ibokhylla, rawurldecode($this->wl_query) ) );
      $this->Pagination .= '<div class="buttons">';

      if ( $prev_page >= 1){
        $this->Pagination .= sprintf('<button class="wl-pagination prev" data-position="%s">&laquo; %s</button>', $prev_page, __('Forrige', 'inter-library-search-by-webloft'), $this->treffperside );
      }

      if ( $next_page < $this->QueryResult['count-items'] ){
        $this->Pagination .= sprintf( '<button class="wl-pagination next" data-position="%s" >%s %s&raquo;</button>', $next_page, __('Neste', 'inter-library-search-by-webloft'), $antalligjen );
      }

      $this->Pagination .=  '</div>';
    }
    else{
      $this->Pagination .= sprintf('<p>%s</p>', sprintf( esc_html__( 'Viser treff 1-%1$s ved s&oslash;k %2$s etter %3$s.', 'inter-library-search-by-webloft'), $this->QueryResult['count-items'], $ibokhylla, rawurldecode($this->wl_query) ) );
    }

    $this->Pagination .= '</div>';
  }


  function printResults(){
    $html = null;
    // _log('WL_SearchController::printResults');
    // _log($this->Results);

    if ( is_array($this->Results) && !empty($this->Results) ){
      $html .= $this->get('Pagination');
      $html .= '<ul class="ils-results">';

      foreach ( $this->Results as $item_index => $result ):
        //_log($result);
        $html .= '<li>';

        if ( $this->hamedbilder == "1" ){
          $html .= sprintf('<div class="omslag"><a %s href="%s"><img src="%s" alt="%s" /></a></div>', $this->openNewWindow, $result['url'], $result['omslag'], $result['tittel'] );
        }

        if ( $this->Library['system'] != 'koha' &&  $this->Library['system'] != 'tidemann' ): /* koha og tidemann har ikke materialtype, da dropper vi denne */
          $ebook_bid = trim( _is($result,'ebokbibid') );

          if ( isset($ebook_bid) && $ebook_bid ) {
            $html .= '<div class="material result-item '.$ebook_bid.'">';

            $html .= '<div class="topphoyre">';
            $html .= sprintf ( '<a target="_blank" href="%s"><img src="%s" alt="%s" /><br><span class="materialtype">%s</span></a><br>',
                $result['pdfutdrag'],
                getIconUrl('pdf.png'),
                __('Les utdrag', 'inter-library-search-by-webloft'),
                __('Utdrag', 'inter-library-search-by-webloft')
            );

            $html .= sprintf( '<a target="_blank" href="http://open.ebokbib.no/cgi-bin/sendvidere?mode=ebokbib&tnr=%s"><img src="%s" alt="%s" /><br><span class="materialtype">%s</span></a>',
                $result['ebokbibid'],
                getIconUrl('ebokbib.png'),
                __('L&aring;n e-bok', 'inter-library-search-by-webloft'),
                __('L&aring;n e-bok', 'inter-library-search-by-webloft')
            );

            $html .= '</div>';
            $html .= '</div>';
          }

          else {
            if ( $material_type = _is( $result, 'materialtype') ){
              $html .= sprintf('<div class="material result-item %s">', $material_type );
              $html .= sprintf( '<img class="materialtype" src="%s" alt="%s" /><br>', getIconUrl($result['materialtype'].".png"), $result['materialtype'] );
              $html .= sprintf('<span class="materialtype">%s</span>', $result['materialtype'] );
            }

            if ( $online_stock = _is($result, 'bestand') ){
              $html .= sprintf ('<span class="online-bestand">%s</span>',  $online_stock);
            }

            if ( $pdf_link = _is($result, 'pdflenke') ) {
              $html .= sprintf ('<br><br><a href="%s" title="%s" />', $pdf_link, __('Last ned som PDF!', 'inter-library-search-by-webloft') );
              $html .= sprintf ('<img src="%s" alt="%s" />', getIconUrl('pdf.png'), __('Last ned som PDF!', 'inter-library-search-by-webloft') );
              $html .= '</a>';
            }

            $html .= '</div>';
          }
        endif;

      $html .= sprintf( '<h3><a %s href="%s" class="result-item-url">', $this->openNewWindow, $result['url']  );
      $html .= $result['tittel'];

      if ( $result['aar'] !== false ){
        $html .= " ".$result['aar'];
      }

      $html .= '</a></h3>';
      $html .= sprintf('<div class="ansvar">%s</div>', $result['opphav'] );


      if ( isset($this->enkelinfo[$item_index]) ){
        $Booking = new WL_Booking( $this->enkelinfo[$item_index] );

        if ( $booking_url = $Booking->get('Url') ){
          $html .= WL_Booking::buildOrderButton($booking_url);
        }
      }



        $html .= '<p class="result-item-description">';

      $tekstbody[] = trim($result['description']);
      //$tekstbody[] = trim($result['utdrag']);
      $tekstbody[] = trim($result['titteloriginal']);
      $tekstbody[] = trim($result['isbn']);
      $tekstbody[] = trim($result['omfang']);
      $tekstbody[] = trim($result['dewey']);
      $tekstbody[] = sprintf('<strong>%s %s</strong>', __('Kilde:', 'inter-library-search-by-webloft'), $this->Library['name'] );
        //  __('Katalog', 'inter-library-search-by-webloft') . ", " . $mittbiblioteknavn;

      if (isset($result['lenke']) && $result['lenke'] != '') {
        foreach ($result['lenke'] as $enlenke) {
          $jefflynne = explode ("|x|" , $enlenke);
          $tekstbody[] = '<a class="postlenker" href="' . $jefflynne[1] . '">' . $jefflynne[0] . '</a>';
        }
      }
      foreach ($tekstbody as $onepiece) {
        if (trim($onepiece) != '') {
          $html .= $onepiece . "<br>";
        }
      }
      unset ($tekstbody);

      $html .= '</p>';

      $html .= '<div class="status">';
      if ($result['status'] == 'ledig'):
        $html .=  __('Ledig', 'inter-library-search-by-webloft');
        $html . '<div class="green dot"></div>';
      elseif ($result['status'] == 'ledig'):
        $html .= __('Utl&aring;nt e.l.', 'inter-library-search-by-webloft');
        $html .= '<div class="orange dot"></div>';
      elseif ($result['status'] == 'bokhylla'):
        $html .=  __('Online i Bokhylla', 'inter-library-search-by-webloft');
        $html .= '<div class="green dot"></div>';
      elseif ($result['status'] == 'ikke-ledig'):
        $html .= __('Ikke ledig', 'inter-library-search-by-webloft');
        $html .= '<div class="red dot"></div>';
      else:
        $html .= __('Uklar bestand', 'inter-library-search-by-webloft');
        $html .= '<div class="orange dot"></div>';
      endif;

      //$html .= '</div> <div style="clear:both;"></div>';

      if ( $full_text = _is($result, 'fulltekst') ){
        $html .= wlils_ribbon(__('Les p&aring; nett!', 'inter-library-search-by-webloft') , $full_text , '#a00');
      }

      $html .= '</li>';
      endforeach;
      $html .= '</ul>';


      if ( $html ){
        $html .= $this->get('Pagination');
        $this->setResponse( $html, (int) $this->QueryResult['count-items'] );
      }


    }
    return $this->Response;
  } // end of printResults





} // end of class