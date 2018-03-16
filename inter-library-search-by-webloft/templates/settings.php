<?php

$omslagbokkilden      = get_option('wl_katalogsok_option_omslagbokkilden' , '0');
$omslagnb             = get_option('wl_katalogsok_option_omslagnb' , '0');
$enkeltpost           = get_option('wl_katalogsok_option_enkeltpost' , '');
$treffbokhylla        = get_option('wl_katalogsok_option_treffbokhylla' , '');
$hoyretrunk           = get_option('wl_katalogsok_option_hoyretrunk' , '');
$viseavansertlenke    = get_option('wl_katalogsok_option_viseavansertlenke' , '');
$enkeltpostnyttvindu  = get_option('wl_katalogsok_option_enkeltpostnyttvindu' , '0');
$kilder               = get_option('wl_katalogsok_option_kilder' , '');
$skjulesoketips       = get_option('wl_katalogsok_option_skjulesoketips' , '');
?>
<div class="wrap">

  <h1><?php _e('WL Katalogs&oslash;k - innstillinger', 'inter-library-search-by-webloft'); ?></h1>

  <form method="post" action="options.php">

    <?php settings_fields('wl_katalogsok_options'); ?>
    <table class="wlkatalogsettingstabell">

      <tr>
        <td colspan="2">
          <h3><?php _e('Visning', 'inter-library-search-by-webloft'); ?></h3>
        </td>

      </tr>
      <tr>
        <td>
          <label for="wl_katalogsok_option_enkeltpost">
            <?php _e('Side for visning av enkeltposter:', 'inter-library-search-by-webloft'); ?>
          </label>
        </td>

        <td>
          <select name="wl_katalogsok_option_enkeltpost">
            <option value=""><?php _e('Ingen - g&aring; til biblioteksystemet', 'inter-library-search-by-webloft'); ?></option>
            <?php
              if ( $sp_posts = $this->get('SinglePagePosts') ) {
                foreach ($sp_posts as $key => $p) {
                  printf('<option value="%s" %s>%s</option>', $p->ID, selected( $p->ID, $enkeltpost, $echo=false ), $p->post_title );
                }
              }
            ?>
          </select>
        </td>
      </tr>
    <tr>
      <td colspan="2">
        <div class="wl-option-info">
        Du må opprette en egen side for visning av enkelposter.<br/>
        På denne siden skal du legge inn følgende shortcode: [wl-ils-enkeltpost].<br/>
        Da dukker siden opp som et valg her.
        </div>
      </td>
    </tr>
    <tr><td>
      <label for="wl_katalogsok_option_viseavansertlenke"><?php _e('Vise lenke til avansert s&oslash;k?', 'inter-library-search-by-webloft'); ?></label>
      </td><td>
      <input name="wl_katalogsok_option_viseavansertlenke" type="checkbox" value="1" <?php if ($viseavansertlenke == "1") { echo "checked";} ?> />
      </td>
    </tr>
    <tr>
      <td>
        <label for="wl_katalogsok_option_enkeltpostnyttvindu"><?php _e('&Aring;pne enkeltposter i nytt vindu?', 'inter-library-search-by-webloft'); ?></label>
      </td>
      <td>
        <input name="wl_katalogsok_option_enkeltpostnyttvindu" type="checkbox" value="1" <?php if ($enkeltpostnyttvindu == "1") { echo "checked";} ?> />
      </td>
    </tr>
    <tr>
      <td colspan="2"><h3><?php _e('Omslagsbilder', 'inter-library-search-by-webloft'); ?></h3></td>
    </tr>
    <tr>
      <td>
        <label for="wl_katalogsok_option_omslagbokkilden"><?php _e('Pr&oslash;ve &aring; finne omslag hos Bokkilden?', 'inter-library-search-by-webloft'); ?></label>
      </td>
      <td>
        <input name="wl_katalogsok_option_omslagbokkilden" type="checkbox" value="1" <?php if ($omslagbokkilden == "1") { echo "checked";} ?> />
      </td>
    </tr>
  <!--   <tr>
      <td>
        <label for="wl_katalogsok_option_omslagnb"><?php _e('Pr&oslash;ve &aring; finne omslag hos Nasjonalbiblioteket?', 'inter-library-search-by-webloft'); ?></label>
      </td>
      <td>
        <input name="wl_katalogsok_option_omslagnb" type="checkbox" value="1" <?php checked( '1', $omslagnb ); ?> />
      </td>
    </tr> -->
    <tr>
      <td colspan="2"><h3><?php _e('Selve s&oslash;ket', 'inter-library-search-by-webloft'); ?></h3></td>
    </tr>

    <tr>
      <td>
        <label for="wl_katalogsok_option_hoyretrunk"><?php _e('Automatisk h&oslash;yretrunkering? (Kan gi mye st&oslash;y!)', 'inter-library-search-by-webloft'); ?></label>
      </td>
      <td>
        <input name="wl_katalogsok_option_hoyretrunk" type="checkbox" value="1" <?php if ($hoyretrunk == "1") { echo "checked";} ?> />
      </td>
    </tr>

    <tr>
      <td>
        <label for="wl_katalogsok_option_skjulesoketips"><?php _e('Skjule knapp for s&oslash;ketips?', 'inter-library-search-by-webloft'); ?></label>
      </td>
      <td>
        <input name="wl_katalogsok_option_skjulesoketips" type="checkbox" value="1" <?php if ($skjulesoketips == "1") { echo "checked";} ?> />
      </td>
    </tr>

    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Oppdat&eacute;r', 'inter-library-search-by-webloft'); ?>" />
    </p>

  </form>
</div>
