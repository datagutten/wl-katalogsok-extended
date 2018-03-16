<?php

class MBAdmin{

  public $SinglePagePosts;

  public function __construct(){
    $this->SinglePagePosts = $this->getSinglePagePosts();

    add_action('admin_menu', array($this, 'addSubmenuPage') ); // shortcode generator
    add_action('admin_menu', array($this, 'addOptionsPage') ); // plugin settings
    add_action('admin_init', array($this, 'registerSettings') );
  }


  public function get($attr){
    if ( isset($this->$attr) ){
      return $this->$attr;
    }
    else{
      return null;
    }
  }


  public function getSinglePagePosts(){
    global $wpdb;
    $sql = "SELECT post_title, ID FROM {$wpdb->posts} WHERE post_content LIKe '%[wl-ils-enkeltpost%' AND post_status = 'publish'";

    return $wpdb->get_results($sql);
  }


  public function addOptionsPage() {
    add_options_page("WL katalogs&oslash;k", "WL Katalogs&oslash;k", "manage_options", "wl_katalogsok_options", array($this, "showOptionsPage") );
  }


  public function showOptionsPage() {
      if (!current_user_can('manage_options')) {
          wp_die(__("Du har ikke tilgang til denne siden"));
    }
    require getTemplatePath('settings.php');
  }


  public function addSubmenuPage() {
    add_submenu_page("tools.php", "WL Katalogs&oslash;k", "WL Katalogs&oslash;k", "edit_posts", "wl_katalogsok_tools", array($this, "showSubmenuPage") );
  }


  public function showSubmenuPage() {
    if (!current_user_can('edit_posts')) {
      wp_die(__("Du har ikke tilgang til denne siden"));
    }

    require_once getTemplatePath('shortcode-generator.php');
  }


  public function registerSettings() {
    // Add options to database if they don't already exist
    add_option("wl_katalogsok_option_omslagbokkilden", "0", "", "yes");
    add_option("wl_katalogsok_option_omslagnb", "0", "", "yes");
    add_option("wl_katalogsok_option_enkeltpost", "", "", "yes");
    add_option("wl_katalogsok_option_treffbokhylla", "0", "", "yes");
    add_option("wl_katalogsok_option_viseavansertlenke", "0", "", "yes");
    add_option("wl_katalogsok_option_enkeltpostnyttvindu", "0", "", "yes");
    add_option("wl_katalogsok_option_hoyretrunk", "0", "", "yes");
    add_option("wl_katalogsok_option_skjulesoketips", "0", "", "yes");


    // Register settings that this form is allowed to update
    register_setting('wl_katalogsok_options', 'wl_katalogsok_option_omslagbokkilden');
    register_setting('wl_katalogsok_options', 'wl_katalogsok_option_omslagnb');
    register_setting('wl_katalogsok_options', 'wl_katalogsok_option_enkeltpost');
    register_setting('wl_katalogsok_options', 'wl_katalogsok_option_treffbokhylla');
    register_setting('wl_katalogsok_options', 'wl_katalogsok_option_viseavansertlenke');
    register_setting('wl_katalogsok_options', 'wl_katalogsok_option_enkeltpostnyttvindu');
    register_setting('wl_katalogsok_options', 'wl_katalogsok_option_hoyretrunk');
    register_setting('wl_katalogsok_options', 'wl_katalogsok_option_skjulesoketips');
  }


} // end of class

new MBAdmin();