<?php
  // er $trefflisteside satt i shortcode? I s&aring;fall m&aring; vi lage ny target for form
  $resources = (isset($resources) ) ? $resources : 'ALLE';
  $skjulesoketips = (isset($skjulesoketips) ) ? $skjulesoketips : '1';

  if (@$trefflisteside > 0) {
    $resultatperma = get_permalink($trefflisteside);
    if ($resultatperma != "") { // klarte &aring; finne permalink?
      $sjokk = explode ("?" , $resultatperma); // alle query strings i $sjokk[1] HVIS DET ER NOEN
      $formaction = $sjokk[0];
      $formtarget = "_top";
    }
  }
?>

<div class="ils-search-form">
  <form id="wl-form" method="GET" class="wl-disable" <?php  echo ( ($action_url) ? sprintf(' action="%s" ', $action_url) : null ); ?> >
    <label for="search" class="wlkatalog_sokeord"><?php _e('S&oslash;keord:', 'inter-library-search-by-webloft'); ?>&nbsp;</label>
    <input type="text" value="<?= $has_get_query ?>" id="wl-search" name="wl_query" accept-charset="utf-8" />&nbsp;
    <input type="submit" value="<?php _e('S&oslash;k', 'inter-library-search-by-webloft');?>" class="wl-disable">
    <input type="hidden" name="ajax-url" id="ajax-url" value="<?= admin_url( 'admin-ajax.php' ); ?>" />
    <input type="hidden" value="<?= $library_id ?>" name="library_id" id="library_id" />
    <input type="hidden" name="omslagbokkilden"     value="<?= $omslagbokkilden ?>" />
    <input type="hidden" name="omslagnb"            value="<?= $omslagnb ?>" />
    <input type="hidden" name="treffbokhylla"       value="<?= $treffbokhylla ?>" />
    <input type="hidden" name="hamedbilder"         value="<?= $hamedbilder ?>" />
    <input type="hidden" name="treffperside"        value="<?= $results_per_page ?>" />
    <input type="hidden" name="hoyretrunk"          value="<?= $hoyretrunk ?>" />
    <input type="hidden" name="pagination"          value="1" id="pagination" />
    <input type="hidden" name="dobokhylla"          value="0" />
    <input type="hidden" name="viseavansertlenke"   value="<?= $viseavansertlenke ?>" />
    <input type="hidden" name="enkeltpostnyttvindu" value="<?= $enkeltpostnyttvindu ?>" />
    <input type="hidden" name="kilder"              value="<?php echo $resources ?>" id="resources" />
    <input type="hidden" name="skjulesoketips"      value="<?php echo $skjulesoketips ?>" />

<?php
  if (isset($sjokk[1])) { // Fantes det parametre p&aring; den trefflistesiden?
    $parameters = explode ("&" , $sjokk[1]); // array med parametre
    if (is_array($parameters)) {
      foreach ($parameters as $parameter) {
        $ettparameter = explode ("=" , $parameter);
        echo "<input type=\"hidden\" name=\"" . $ettparameter[0] . "\" value=\"" . $ettparameter[1] . "\" />";
      }
    }
  }
?>

<?php if (trim($enkeltpost) != ""): ?>
  <input type="hidden" name="enkeltposturl" value="<?= base64_encode(get_permalink($enkeltpost)) ?>" />
<?php endif; ?>
  </form>
</div>


<?php
$tabs = explode(',', $resources);
include( getConfigPath("resources.php") );
if ( is_array($tabs) ): ?>
<div class="wl-catalog wl-resources" id="wl-content">
  <div class="tabs">
    <ul >
     <?php foreach ($tabs as $key => $resource): ?>
       <li>
          <a href="#tab<?php echo $key ?>" class="<?php echo $resource; ?>" data-resource=<?php echo $resource; ?> ><?php echo trim($search_resources[$resource]); ?>
            <div style="display: inline;" class="wl-resource-count-results">
              <span class="result-count"></span>
              <img src="<?php echo getIconUrl('litenspinner.gif'); ?>" alt="<?php _e('Laster...', 'inter-library-search-by-webloft'); ?>" class="wl-tab-spinner" />
            </div>
          </a>
      </li>
    <?php endforeach; ?>
    </ul>
  </div>
</div>
<?php
endif;

?>

<div id="wl-search-results">
<?php foreach ($tabs as $key => $resource): ?>
  <div id="tab-<?php echo $resource ?>" class="wl-tab-content"></div>
<?php endforeach; ?>
</div>

<div id="wl-ajax-spinner" class="wl-ajax-spinner" ><img style="border: none; box-shadow: none;" src="<?= getIconUrl('spinner.gif'); ?>" alt="<?php _e('Laster...', 'inter-library-search-by-webloft'); ?>" /></div>