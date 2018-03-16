<?php

$forrigelink = $_SERVER['REQUEST_URI'] . "&posisjon=" . ($posisjon - $treffperside);
$nestelink   = $_SERVER['REQUEST_URI'] . "&posisjon=" . ($posisjon + $treffperside);

if ($mittsystem == "bokhylla") {
  $ibokhylla = __(' i Bokhylla ', 'inter-library-search-by-webloft');
}
else {
  $ibokhylla = " ";
}

$forrigeposisjon = ($posisjon - $treffperside);
$nesteposisjon   = ($posisjon + $treffperside - 1);
if ($nesteposisjon > $Search->Result['count-items']) {
  $nesteposisjon = $Search->Result['count-items'];
}

// Hva skal det st&aring; p&aring; knappen?
if (($nesteposisjon + $treffperside) > $Search->Result['count-items']) {
  $antalligjen = $Search->Result['count-items'] - $nesteposisjon;
}
else {
  $antalligjen = $treffperside;
}

?>
<div class="ils-results-pager">
  <?php if ( $Search->Result['count-items'] >= $treffperside ): ?>
    <div class="buttons">
      <?php if ($forrigeposisjon >= 1): ?>
        <button onclick="history.go(-1);">&laquo; <?php _e('Forrige', 'inter-library-search-by-webloft'); ?> <?= $treffperside ?></button>
      <?php endif; ?>

      <?php if ($nesteposisjon < $Search->Result['count-items']): ?>
        <button onclick="showreglitreframeLoading();location.href='<?= $nestelink  ?>'"><?php _e('Neste', 'inter-library-search-by-webloft'); ?> <?= $antalligjen ?> &raquo;</button>
      <?php endif;  ?>
    </div>

    <p>
    <?php printf( esc_html__( 'Viser treff %1$s-%2$s av %3$s ved s&oslash;k%4$setter %5$s.', 'inter-library-search-by-webloft'), $posisjon, $nesteposisjon, $Search->Result['count-items'], $ibokhylla, $qsokeord ); ?>
    </p>

    <?php else: ?>
    <p>
    <?php printf( esc_html__( 'Viser treff 1-%1$s ved s&oslash;k %2$s etter %3$s.', 'inter-library-search-by-webloft'), $Search->Result['count-items'], $ibokhylla, $qsokeord ); ?>
    </p>

  <?php endif; ?>
</div>
