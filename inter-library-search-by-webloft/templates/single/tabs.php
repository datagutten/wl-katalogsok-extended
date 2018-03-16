
        <div class="tabs">
          <ul style="position: static; margin: 0;">
            <!-- <li class="active"><a href="#tab1"><?php _e('Eksemplarer', 'inter-library-search-by-webloft'); ?></a></li> -->
            <li class="active"><a href="#tab2"><?php _e('Beskrivelse', 'inter-library-search-by-webloft'); ?></a></li>
            <li><a href="#tab3"><?php _e('Flere opplysninger', 'inter-library-search-by-webloft'); ?></a></li>
          </ul>

            <div class="tab-content" style="margin-top: 0px;">
              <!-- <div id="tab1" class="tab active"></div> -->
              <div id="tab2" class="tab active">

                <?php if (isset($treff['beskrivelse']) && trim($treff['beskrivelse']) != ''): ?>
                    <p><?= $treff['beskrivelse'] ?></p>
                <?php endif; ?>
                <p>
                  <?php if (isset($treff['omfang']) && trim($treff['omfang']) != ''): ?>
                    <strong><?php _e('Omfang:', 'inter-library-search-by-webloft'); ?></strong> <?= $treff['omfang'] ?><br>
                  <?php endif; ?>
                </p>


                <?php if (isset($treff['bestand']) && is_array($treff['bestand'])): ?>
                  <h3 class="item-info-title"><?php _e('Eksemplarer', 'inter-library-search-by-webloft'); ?></h3>
                  <?php //_log($treff['bestand']); ?>
                    <?php if ($system == 'tidemann' || $system == 'bibliofil' || $system == 'mikromarc'): ?>
                      <?php foreach ($treff['bestand'] as $bestand): ?>
                          <?php
                            $temp = array();
                            if ( $library_name = _is($bestand, 'bibnavn') ) {
                              $temp[] = $library_name;
                            }
                            if ( isset($bestand['b']) && $bestand['b'] ) {
                              $temp[] = $bestand['b'];
                            }
                            if ( isset($bestand['c']) && $bestand['c'] ) {
                              $temp[] = $bestand['c'];
                            }
                            $ferdig = implode (" / " , $temp);
                            echo $ferdig;

                            if ( !isset($bestand['h']) || !isset($bestand['f']) ) { // sett til ukjent hvis ikke satt
                              $bestand['h'] = "1";
                              $bestand['f'] = "-1";
                            }
                          ?>
                          : <strong><?= getStockInformationByStatusCode($bestand['h'], $bestand['f']) /* status, restriction */ ?></strong>
                          <?php if (($bestand['h'] == "4") || ($bestand['h'] == "5")): /* UTL&Aring;NT */ ?>
                            <?php setlocale (LC_TIME , "nb_NO"); // norsk dato ?>
                            til <?= strftime("%e.%m.%G" , strtotime($bestand['y'])) ?>
                          <?php endif; ?>
                          <br>

                      <?php endforeach; ?>
                      <?php elseif ( $system == 'koha-sru'): ?>
                        <?php
                          if ( $ledige ):
                            foreach ( $treff['bestand']['available'] as $bi => $row) {
                              printf('%s: %s <br/>', $row['library'], $row['count'] );
                            }
                          endif;
                         ?>
                      <?php endif; ?>


                  <?php endif; ?>
              <?php

              // Dekker hvis ebokbib eller hvis ingen eksemplarer
              if (!isset($treff['bestand']) || !is_array($treff['bestand'])) {
                if (isset($treff['ebokbibid']) && ($treff['ebokbibid'] != '')) {
                  echo '<a href="http://open.ebokbib.no/cgi-bin/sendvidere?mode=ebokbib&tnr=' . $treff['ebokbibid'] . '"><img class="ebokbiblogo" src="' .getIconUrl('ebokbib.png') .'" alt="EbokBib" /></a>' . __('Dette er en ebok som du m&aring; ha appen eBokBib for &aring; lese p&aring; nettbrett eller smarttelefon. Appen f&aring;r du i App Store (iOS) eller Google Play (Android). Klikk p&aring; logoen for &aring; l&aring;ne boka eller f&aring; mer informasjon.', 'inter-library-search-by-webloft');
                }
              }
              //  else {
              //   // echo "Ingen eksemplarer finnes!";
              // }
              ?>

              </div><!-- /#tab2 -->

               <div id="tab3" class="tab">
                <p>
                  <?php if ( isset($treff['originaltittel']) ): ?>
                      <strong><?php _e('Originaltittel:', 'inter-library-search-by-webloft'); ?></strong> <?= $treff['originaltittel'] ?><br>
                  <?php endif; ?>
                  <?php if ( is_array( _is($treff, 'dewey')) ): ?>
                      <strong><?php _e('Dewey:', 'inter-library-search-by-webloft'); ?></strong><?= implode (" / " , $treff['dewey']) ?><br>
                  <?php endif; ?>

                  <?php if ( $system == 'tidemann' || $system == 'bibliofil' ): ?>
                      <?php if (isset($treff['generellnote'])): ?>
                          <strong><?php _e('Generell note:', 'inter-library-search-by-webloft'); ?></strong> <?= (is_array($treff['generellnote']) ? implode (". ", $treff['generellnote']) : $treff['generellnote']) ?><br>
                      <?php endif; ?>
                      <?php if ( isset($treff['innholdsnote']) ): ?>
                          <strong><?php _e('Innholdsnote:', 'inter-library-search-by-webloft'); ?></strong> <?= (is_array($treff['innholdsnote']) ? implode (". ", $treff['innholdsnote']) : $treff['innholdsnote']) ?><br>
                      <?php endif; ?>
                      <?php if ( isset($treff['medarbeidere']) ): ?>
                          <strong><?php _e('Medvirkende:', 'inter-library-search-by-webloft'); ?></strong> <?= (is_array($treff['medarbeidere']) ? implode (". ", $treff['medarbeidere']) : $treff['medarbeidere']) ?><br>
                      <?php endif; ?>
                  <?php endif; ?>

                  <?php if ( isset($treff['titler']) ): ?>
                      <strong><?php _e('Tittelinformasjon:', 'inter-library-search-by-webloft'); ?></strong> <?= (is_array($treff['titler']) ? implode (" ; ", $treff['titler']) : $treff['titler']) ?><br>
                  <?php endif; ?>
                  <?php if ( isset($treff['emneord']) ): ?>
                      <strong><?php _e('Emneord:', 'inter-library-search-by-webloft'); ?></strong> <?= (is_array($treff['emneord']) ? implode (" ; ", $treff['emneord']) : $treff['emneord']) ?><br>
                  <?php endif; ?>

                  <?php if ( $system != 'koha' && isset($treff['ansvarsangivelse']) && $treff['ansvarsangivelse'] ): ?>
                  <strong><?php _e('Opphav', 'inter-library-search-by-webloft'); ?> : </strong><?= $treff['ansvarsangivelse'] ?><br>
                  <?php endif; ?>

                  <?php if ( $isbn = _is($treff, 'isbn') ): ?>
                    <strong><?php _e('ISBN', 'inter-library-search-by-webloft'); ?> :</strong> <?= $isbn; ?><br>
                  <?php endif; ?>
                  </p>
              </div><!-- /#tab3 -->

            </div><!-- .tab-content -->
        </div><!-- .tabs -->