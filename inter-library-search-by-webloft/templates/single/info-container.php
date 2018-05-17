<div class="infocontainer">
  <h2><?= str_replace(": :" , ":", _is($treff, 'tittelinfo') ) ?></h2>
  <p>
    <?php if ( isset ($treff['forfatter']) && $treff['forfatter'] ): ?>
      <strong><?php _e('Forfatter', 'inter-library-search-by-webloft'); ?> : </strong><?= $treff['forfatter'] ?><br>
    <?php endif; ?>

    <?php if (!empty($utgitt)): ?>
        <strong><?php _e('Utgitt', 'inter-library-search-by-webloft'); ?> : </strong><?= $utgitt ?><br>
    <?php endif; ?>

    <?php if (!empty($treff['omfang'])): ?>
        <strong><?php _e('Omfang', 'inter-library-search-by-webloft'); ?> :</strong> <?= $treff['omfang'] ?><br>
    <?php endif; ?>


    <?php if ( isset($treff['bestand']) ):  ?>
      <?php if ($ledige > 0): ?>
        <div class="green dot" title="<?php _e('Ledig!', 'inter-library-search-by-webloft'); ?>"></div>&nbsp;<?php _e('Ledig', 'inter-library-search-by-webloft'); ?><br><br>
      <?php elseif ($uklar):  ?>
        <div class="red dot" title="<?php _e('Uklar bestand', 'inter-library-search-by-webloft'); ?>"></div>&nbsp;<?php _e('Uklar bestand - kontakt biblioteket!', 'inter-library-search-by-webloft'); ?><br><br>
      <?php else: ?>
        <div class="red dot" title="<?php _e('Ingen ledige!', 'inter-library-search-by-webloft'); ?>"></div>&nbsp;<?php _e('Ingen ledige...', 'inter-library-search-by-webloft'); ?><br><br>
      <?php endif; ?>
    <?php endif; ?>


    <?php if ( $pdf_link = _is($treff, 'pdfutdrag') ): ?>
      [<a href="<?= $pdf_link; ?>"><?php _e('Les utdrag', 'inter-library-search-by-webloft'); ?></a>]<br>
    <?php endif; ?>

    <?php if ( $system == 'koha'  && _is($treff, 'innholdsnote') ): ?>
        <?php foreach ($treff['innholdsnote'] as $key => $text_row) {
          printf('<p>%s</p>', $text_row );
        }?>
    <?php endif; ?>


	<?php if( !empty($treff['serie']) ): ?>
		<h3>Serie:</h3>
		<ul>
		<?Php foreach($treff['serie'] as $item) {
			printf('<li><a href="%s">%s %s</a></li>'."\n", $item['permalink'], $item['tittel'], $item['subtittel']);
		} ?>
	</ul>
	<?php endif; ?>
        <div class="buttons">
            <?php if ( isset($treff['fulltekst']) ): /* finnes den p&aring; nett? */ ?>
                <a class="link-online" href="<?php echo $treff['fulltekst'] ?>"><?php _e('Les p&aring; nett', 'inter-library-search-by-webloft'); ?></a>
            <?php endif; ?>
            <?php if ($booking_url): ?>
                <a class="link-order" href="<?php echo $booking_url ?>"><?php _e('Bestille/reservere', 'inter-library-search-by-webloft'); ?></a>
            <?php endif; ?>
            <?php if (isset($treff['permalink'])): ?>
                <a class="link-online" href="<?php echo $treff['permalink'] ?>"><?php _e('Vis i katalogen', 'inter-library-search-by-webloft'); ?></a>
            <?php endif; ?>
        </div>
    <?php $booking_url = false; $uklar = false; /* m&aring; rydde opp */ ?>

    </p>
</div><!-- /.infocontainer -->