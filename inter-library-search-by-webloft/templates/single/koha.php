<?php if ( isset($treff['bestand']) && is_array($treff['bestand']) ): /* bare hvis vi har bestandinfo */ ?>
  <div class="bestandcontainer">
    <h3><?php _e('Eksemplarer', 'inter-library-search-by-webloft'); ?>:</h3>
    <p>
      <?php foreach ($treff['bestand'] as $bestand): ?>
          <?= $bestand->institution
                  . (isset($bestand->collection) ? "&nbsp;/&nbsp;{$bestand->collection}" : '')
                  . (isset($bestand->callnumber) ? "&nbsp;/&nbsp;{$bestand->callnumber}" : '') ?>
          :
          <?php
            echo bestandsinfo ($bestand->circulationStatus , $bestand->useRestriction); // status, restriction
            if (($bestand->circulationStatus == "4") || ($bestand->circulationStatus == "5")) { // UTL&Aring;NT
                setlocale (LC_TIME , "nb_NO"); // norsk dato
                echo __(' til ', 'inter-library-search-by-webloft') . strftime("%e. %B %G" , strtotime($bestand['y']));
            }
          ?>
          <br>
      <?php endforeach; ?>
    </p>
  </div><!-- /.bestandcontainer -->
<?php endif; ?>