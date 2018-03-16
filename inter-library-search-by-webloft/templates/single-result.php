<?php
$system = _is($library, 'system');


if ( $system == 'koha') {
  $utgitt = null;
  if ( $value = _is($treff,'utgitthvem') ){
    $utgitt = $treff['utgitthvem'];
  }
  if ( $value = _is($treff, 'utgitthvor') ) {
   $utgitt .= ", " . $value;
  }
  if ( $value = _is($treff, 'utgittaar') ) {
   $utgitt .= ", " . $value;
  }
}
else{
  $utgitt = ( ((isset($treff['utgitthvem'])) && (trim($treff['utgitthvem']) != "")) ? $treff['utgitthvem'] : "[s.n.]") . ', ' .
    (((isset($treff['utgitthvor'])) && trim($treff['utgitthvor']) ) ? $treff['utgitthvor'] : '[s.l.]')
    . (((isset($treff['utgittaar'])) && trim($treff['utgittaar']) ) ? ', ' . $treff['utgittaar'] : '');
}

  $ledige = 0;
  $uklar = false;

  $items = ( isset($treff['bestand']) ) ? $treff['bestand'] : null ;

  if ( $system == 'koha-sru' ){
    $ledige = $treff['available_items'];
  }

  elseif ( $system == 'bibliofil' or $system == 'tidemann' ){
    if ( $items && is_array($items) ) {
      foreach ($items as $index => $bestand) {
        if ( _is($bestand, 'h') == "0" || $index == 'h' && $bestand == '0' ){
         $ledige++;
        }
      }
    }
    else {
      $uklar = true;
    }
  }

  $booking_url = null;
  if ( $system != 'koha' ){
    $Booking = new WL_Booking($item_info);
    $booking_url = $Booking->get('Url');
  }
?>
<div class="ils-single-result wl-catalog">
    <div class="wl-image-container">
      <?php if ( isset($treff['omslag']) && $treff['omslag'] ): ?>
        <img src="<?= $treff['omslag'] ?>" alt="<?= _is($treff, 'tittelinfo') ?>" />
      <?php else: ?>
          <img src="<?= getIconUrl('ikke_digital.png'); ?>" alt="<?= _is($treff, 'tittelinfo') ?>" />
      <?php endif; ?>
    </div>

    <?php include('single/info-container.php'); ?>

    <div class="clear"></div>
    <?php if ( $system == 'koha' ):  ?>
        <?php include('single/koha.php'); ?>
    <?php else: ?>
      <?php include('single/tabs.php'); ?>
    <?php endif; ?>

    <br style="clear: both;">
</div>
