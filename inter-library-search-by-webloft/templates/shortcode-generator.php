<div class="wrap">
  <h1><?php _e('WL Katalogs&oslash;k - lag din egen shortcode', 'inter-library-search-by-webloft'); ?></h1>
  <p><?php _e('P&aring; denne siden kan du gj&oslash;re alle valgene for hvordan s&oslash;ket skal se ut p&aring; siden din. N&aring; du lagrer disse valgene ved &aring; klikke p&aring; knappen nederst p&aring; siden, blir feltet med kortkoden (shortcode) oppdatert. Denne kan du s&aring; kopiere og lime inn i et innlegg eller p&aring; en side.', 'inter-library-search-by-webloft');?>
  </p>

  <form action="#" id="wlkatalogshortcodeform">
    <table class="wlkatalogsettingstabell">
      <tr>
        <td>
          <label for="wl_katalogsok_option_kilder"><?php _e('Hvilke kilder vil du hente treff fra i s&oslash;ket ditt?<br />Standard er alle, men her kan du plukke bort de du ikke vil ha: ', 'inter-library-search-by-webloft'); ?></label>
        </td>
        <td>
          <?php // Hvilke taber skal vi ha med? De m&aring; IKKE ha et navn inkludert i et annet! ?>
          <input name="wl_katalogsok_option_kilder[]" id="resource-1" class="wl-search-resource" type="checkbox" value="biblioteket" checked/>
          <label for="resource-1"><?php _e('Biblioteket' , 'inter-library-search-by-webloft'); ?></label><br/>

          <input name="wl_katalogsok_option_kilder[]" id="resource-3" class="wl-search-resource" type="checkbox" value="bokhyllano" checked/>
           <label for="resource-3"><?php _e('Bokhylla.no' , 'inter-library-search-by-webloft'); ?></label><br />

          <input name="wl_katalogsok_option_kilder[]" id="resource-2" class="wl-search-resource" type="checkbox" value="eboker" checked/>
          <label for="resource-2"><?php _e('Frie e-b&oslash;ker' , 'inter-library-search-by-webloft'); ?></label><br />

          <input name="wl_katalogsok_option_kilder[]" id="resource-4" class="wl-search-resource" type="checkbox" value="filmbibno" checked/>
          <label for="resource-4"><?php _e('Filmbib.no' , 'inter-library-search-by-webloft'); ?></label>
        </td>
      </tr>
      <tr>
        <td>
          <label for="wl_katalogsok_option_mittbibliotek"><?php _e('Angi hvilken bibliotekkatalog det skal s&oslash;kes i:', 'inter-library-search-by-webloft'); ?></label></td>
        <td>
        <select name="wl_katalogsok_option_mittbibliotek">
          <option value="NULL"><?php _e('(Du m&aring; velge et bibliotek!)', 'inter-library-search-by-webloft'); ?></option>
          <?php
          // $catalog = get_option(  );
          //
          $catalog = null;
          include ( getConfigPath("library_list.php") );
          foreach ($libraries as $library_id => $library) {
            printf ('<option value="%s" %s>%s</option>', $library_id, selected($catalog, $library_id, false), $library['name'] );
          }
          ?>
        </select>
        </td>
      </tr>
      <tr>
        <td>
          <label for="wl_katalogsok_option_target_page"><?php _e('Landingsside:', 'inter-library-search-by-webloft'); ?></label></td>
        <td>
        <select name="wl_katalogsok_option_target_page">
          <option value=""></option>
          <?php

          $args = array(
            'posts_per_page'   => '-1',
            'orderby'          => 'post_title',
            'order'            => 'ASC',
            'post_type'        => 'page',

            'post_status'      => 'publish',
            'suppress_filters' => true
            );
            if ( $posts_array = get_posts( $args ) ){
              foreach ($posts_array as $index => $post) {
                printf ('<option value="%s" %s>%s</option>', $post->ID, false, $post->post_title );
              }
            }
          ?>
        </select>
        </td>
      </tr>
      <tr>
        <td><label for="wl_katalogsok_option_treffperside"><?php _e('Antall treff som skal vises per side:', 'inter-library-search-by-webloft'); ?></label></td>
        <td>
          <select name="wl_katalogsok_option_treffperside">
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="75">75</option>
            <option value="100">100</option>
          </select>
        </td>
      </tr><tr>
      <td><label for="wl_katalogsok_option_hamedbilder"><?php _e('Vise omslagsbilder?', 'inter-library-search-by-webloft'); ?></label></td>
      <td><input id="wl_katalogsok_option_hamedbilder" name="wl_katalogsok_option_hamedbilder" type="checkbox" value="1" checked /></td>
      </tr>
    </table>
  </form>


  <h3><?php _e('Shortcode til &aring; kopiere og lime inn i innlegg eller p&aring; sider', 'inter-library-search-by-webloft'); ?></h3>
  <div id="wlkatalogferdigshortcode">
    <span id="shortcode-content"></span>
    <span id="shortcode-explanation"><?php _e('(Shortcode dukker opp her n&aring;r du velger en bibliotekkatalog)', 'inter-library-search-by-webloft'); ?></span>
  </div>
</div>