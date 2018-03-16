<?php header('Content-type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html>
<html lang="no">

<head>
  <meta charset="utf-8" />
  <title><?php _e('Treffliste', 'inter-library-search-by-webloft'); ?></title>
  <link href="//fonts.googleapis.com/css?family=Muli" rel="stylesheet" type="text/css" />

  <link rel="stylesheet" href="../assets/css/wl-katalogsok.css?t=<?= time(); ?>" />

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="../assets/js/wl-katalogsok.js"></script>
  <script src="../assets/js/mashuptabs.js"></script>
<script>
jQuery(document).ready(function(){
  jQuery("#tab3").load("<?php echo getTemplateUrl('tabs/bokhylla.php'); ?>" , "qsokeord=<?= $sokeord;?>");
  jQuery("#tab4").load("<?php echo getTemplateUrl('tabs/video.php'); ?>" , "qsokeord=<?= $sokeord;?>");
});
</script>
<script>

function initToggleSearchHints(){
  jQuery('._js-toggle-search-info').click(function(){
    jQuery('.search-info').toggle();
    return false;
  })
}


jQuery(document).ready(function(){
  initToggleSearchHints();
});
</script>

</head>



<body onLoad="hidereglitreframeLoading();">
<?php $inyttvindu = ($enkeltpostnyttvindu == "1") ? ' target="_blank"' : ' target="_top"'; ?>

<?php // Hvis ingen kilder er valgt vil vi ikke leke mer
if ( !$resources ) {
	die (__('FEIL: G&aring; til innstillinger for WL Katalogs&oslash;k og velg noen kilder &aring; s&oslash;ke i!'));
}
?>

<div id="divreglitreframeFrameHolder" class="wl-catalog" style="display:block;">

<div class="tabs">
	<ul>

<?php if (stristr($resources, 'biblioteket') || $resources == "ALLE") { ?>
	<li class="active"><a href="#tab1"><?php _e('Biblioteket', 'inter-library-search-by-webloft'); ?> (<?= (int) $result['count-items']; ?>)</a></li>
<?php } ?>

<?php if (stristr($resources , 'eboker') || $resources == "ALLE") { ?>
		<li>
      <a href="#tab2" ><?php _e('Frie e-b&oslash;ker', 'inter-library-search-by-webloft'); ?>
        <div style="display: inline;" class="ebokantalltreff">
          <img src="<?php echo getIconUrl('litenspinner.gif'); ?>" alt="<?php _e('Laster...', 'inter-library-search-by-webloft'); ?>" />
        </div>
      </a>
    </li>
<?php } ?>

<?php if (stristr($resources , 'bokhyllano') || $resources == "ALLE") { ?>
	<li>
    <a href="#tab3"><?php _e('Bokhylla.no', 'inter-library-search-by-webloft'); ?>
      <div style="display: inline;" class="bokhyllaantalltreff">
        <img src="<?php echo getIconUrl('litenspinner.gif'); ?>" alt="<?php _e('Laster...', 'inter-library-search-by-webloft'); ?>" />
      </div>
    </a>
  </li>
<?php } ?>

<?php if (stristr($resources , 'filmbibno') || $resources == "ALLE") { ?>
		<li>
      <a href="#tab4"><?php _e('Filmbib.no', 'inter-library-search-by-webloft'); ?>
        <div style="display: inline;" class="videoantalltreff">
          <img src="<?php echo getIconUrl('litenspinner.gif'); ?>" alt="<?php _e('Laster...', 'inter-library-search-by-webloft'); ?>" />
        </div>
      </a>
    </li>
<?php } ?>

	</ul>

	<div class="tab-content">

<?php // Vi m&aring; trikse det til, den f&oslash;rste som vises er aktiv
	$aktiv[0] = " active";
	$aktiv[1] = "";
	$aktivteller = 0;
?>

<?php if (stristr($resources, 'biblioteket') || $resources == "ALLE") { ?>

	<div id="tab1" class="tab<?= $aktiv[$aktivteller]; ?>">
		<?php $aktivteller = 1; ?>
    <?php if (count($results)): ?>

  	<?php include 'results-pager.php'; ?>

    <?php
      if ($viseavansertlenke == "1") { // "Vise lenke til avansert s&oslash;k" satt i innstillinger?
        printf('&nbsp;<a target="_blank" class="wl-button" href="%s">%s</a>', $avanserturl, __('G&aring; til avansert s&oslash;k', 'inter-library-search-by-webloft')  );
      }

      if ($skjulesoketips != "1") { // "Skjule s&oslash;ketips" satt i innstillinger?
        $text = '<p>'. __('<strong>Frasesøk:</strong> Ved å bruke anførselstegn rundt en frase søker du etter ordene som helhet (frase) og ikke ordene enkeltvis', 'inter-library-search-by-webloft' ).'</p>';
        $text .= '<p>'.__('<strong>Invertert søk:</strong> I noen tilfeller kan det lønne seg å invertere søket. Eks. Nesbø, Jo istedenfor Jo Nesbø', 'inter-library-search-by-webloft' ).'</p>';
        $text .= '<p>'. __('<strong>Avansert søk:</strong> For mer avanserte søk i bibliotekkatalogen, klikk på knappen Avansert søk under søkefeltet', 'inter-library-search-by-webloft' ).'</p>';

        printf ( '<a class="wl-button _js-toggle-search-info" href="#">%s</a>', __('S&oslash;ketips', 'inter-library-search-by-webloft') );
        printf('<div class="search-info">%s</div>', $text );
      }
    ?>

  <ul class="ils-results">
  <?php foreach ($results as $result): ?>
    <li>
      <?php if ($hamedbilder == "1"): /* skal vi egentlig vise bilder i det hele tatt, s&aring;nn i f&oslash;lge innstillingene? */ ?>
        <div class="omslag">
          <a<?=$inyttvindu;?> href="<?= $result['url'] ?>"><img src="<?= $result['omslag'] ?>" alt="<?= $result['tittel'] ?>" /></a>
        </div>
      <?php endif; ?>

    <?php if (($mittsystem != 'koha') && ($mittsystem != 'tidemann')): /* koha og tidemann har ikke materialtype, da dropper vi denne */ ?>
      <?php if ( $ebook_bid = _is($result,'ebokbibid') )  { // Ebokbib!
      	echo '<div class="material result-item '.$ebok_bid.'">';

      	printf ( '<div class="topphoyre"><a target="_blank" href="%s"><img src="%s" alt="%s" /><br><span class="materialtype">%s</span></a><br>',
            $result['pdfutdrag'],
            getIconUrl('pdf.png'),
            __('Les utdrag', 'inter-library-search-by-webloft'),
            __('Utdrag', 'inter-library-search-by-webloft')
        );

      	printf( '<a target="_blank" href="http://open.ebokbib.no/cgi-bin/sendvidere?mode=ebokbib&tnr=%s"><img src="%s" alt="%s" /><br><span class="materialtype">%s</span></a></div>',
            $result['ebokbibid'],
            getIconUrl('ebokbib.png'),
            __('L&aring;n e-bok', 'inter-library-search-by-webloft'),
            __('L&aring;n e-bok', 'inter-library-search-by-webloft')
        );
      	echo '</div>';

      } else { // hvis ikke ebokbib, vis materialtype etc. som under ?>
      <?php if ( $material_type = _is( $result, 'materialtype') ): ?>
        <div class="material result-item <?php echo $material_type; ?>">
          <img class="materialtype" src="<?=  getIconUrl($result['materialtype'].".png"); ?>" alt="<?= $result['materialtype'] ?>" /><br>
          <span class="materialtype"><?= $result['materialtype'] ?></span>
          <?php
            if ( $online_stock = _is($result, 'bestand') ){
              printf ('<span class="online-bestand">%s</span>',  $online_stock);
            }
          ?>
          <?php
          if ( $pdf_link = _is($result, 'pdflenke') ) {
            printf ('<br><br><a href="%s" title="%s" />', $pdf_link, __('Last ned som PDF!', 'inter-library-search-by-webloft') );
            printf ('<img src="%s" alt="%s" />', getIconUrl('pdf.png'), __('Last ned som PDF!', 'inter-library-search-by-webloft') );
            echo '</a>';
          }
          ?>
          </div>
      <?php endif; ?>
    <?php } // slutt p&aring; else hvis ikke ebokbib ?>
    <?php endif; ?>
        <h3>
          <?php //_log('results.php'); ?>
          <?php //_log($result); ?>
          <a<?=$inyttvindu;?> href="<?= $result['url'] ?>" class="result-item-url">
              <?= $result['tittel'] ?>
              <?php if ($result['aar'] !== false): ?>
                  (<?= $result['aar'] ?>)
              <?php endif; ?>
          </a>
        </h3>
        <div class="ansvar"><?= $result['opphav'] ?></div>

        <?php if ( $item_info = getBase64ItemInfo($result['url']) ) : ?>
        <?php $Booking = new MBBooking($item_info); ?>
          <?php if ( $booking_url = $Booking->get('Url') ): ?>
            <div class="result-item-booking-url" >
              <a class="link-order" href="<?php echo $booking_url; ?>" target="_blank" ><?php _e('Bestille/reservere', 'inter-library-search-by-webloft'); ?></a>
            </div>
          <?php endif; ?>
        <?php endif; ?>

        <p>
          <?php
          $tekstbody[] = trim($result['description']);
          //$tekstbody[] = trim($result['utdrag']);
          $tekstbody[] = trim($result['titteloriginal']);
          $tekstbody[] = trim($result['isbn']);
          $tekstbody[] = trim($result['omfang']);
          $tekstbody[] = trim($result['dewey']);
          $tekstbody[] = "<strong>" . __('Kilde:', 'inter-library-search-by-webloft') . ' </strong>' . __('Katalog', 'inter-library-search-by-webloft') . ", " . $mittbiblioteknavn;

          if (isset($result['lenke']) && $result['lenke'] != '') {
            foreach ($result['lenke'] as $enlenke) {
              $jefflynne = explode ("|x|" , $enlenke);
              $tekstbody[] = '<a class="postlenker" href="' . $jefflynne[1] . '">' . $jefflynne[0] . '</a>';
            }
          }
          foreach ($tekstbody as $onepiece) {
            if (trim($onepiece) != '') {
              echo $onepiece . "<br>";
            }
          }
          unset ($tekstbody);
        ?>
        </p>

        <div class="status">
          <?php if ($result['status'] == 'ledig'): ?>
            <?php _e('Ledig', 'inter-library-search-by-webloft'); ?> <div class="green dot"></div>
          <?php elseif ($result['status'] == 'ledig'): ?>
            <?php _e('Utl&aring;nt e.l.', 'inter-library-search-by-webloft'); ?> <div class="orange dot"></div>
          <?php elseif ($result['status'] == 'bokhylla'): ?>
            <?php _e('Online i Bokhylla', 'inter-library-search-by-webloft'); ?> <div class="green dot"></div>
          <?php elseif ($result['status'] == 'ikke-ledig'): ?>
            <?php _e('Ikke ledig', 'inter-library-search-by-webloft'); ?> <div class="red dot"></div>
          <?php else: ?>
            <?php _e('Uklar bestand', 'inter-library-search-by-webloft'); ?> <div class="orange dot"></div>
          <?php endif; ?>
        </div>

        <div style="clear:both;"></div>

      <?php if ( $full_text = _is($result, 'fulltekst') ): ?>
        <?php echo wlils_ribbon(__('Les p&aring; nett!', 'inter-library-search-by-webloft') , $full_text , '#a00'); ?>
      <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
  <div class="reglitre_results_header"><?php include 'results-pager.php'; ?></div>
<?php else: ?>
  <?php _e('Beklager, ingen treff!', 'inter-library-search-by-webloft'); ?>
<?php endif; ?>

</div>

<?php } // Slutt p&aring; hvis valgt som kilde ?>

<?php

	if (stristr($resources , 'eboker') || $resources == "ALLE") {

		echo '<div id="tab2" class="tab' . $aktiv[$aktivteller] . '">' . "\n";
		$aktivteller = 1;

		include ('tabs/eboker.php');
		echo '</div>';
	}

	if (stristr($resources , 'bokhyllano') || $resources == "ALLE") {
		echo '<div id="tab3" class="tab' . $aktiv[$aktivteller] . '">' . "\n";
		$aktivteller = 1;
		_e ('Henter treff... hold ut!', 'inter-library-search-by-webloft');
		echo '</div>';
	}

	if (stristr($resources, 'filmbibno') || $resources == "ALLE") {
		echo '<div id="tab4" class="tab' . $aktiv[$aktivteller] . '">' . "\n";
		$aktivteller = 1;
		_e ('Henter treff... hold ut!', 'inter-library-search-by-webloft');
		echo '</div>';
	}
?>


		</div>
	</div>
</div>

</div><!-- /reglitreframeFrameHolder -->

</body>
</html>