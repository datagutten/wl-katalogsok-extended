<?php

add_action( 'widgets_init', array('WL_ILS_Widget', 'registerWidget') );


class WL_ILS_Widget extends WP_Widget {

  protected $widget_slug = 'wl_katalogsok_widget';

  public static function registerWidget(){
    register_widget( 'WL_ILS_Widget' );
  }

  /*--------------------------------------------------*/
  /* Constructor
  /*--------------------------------------------------*/

  /**
   * Specifies the classname and description, instantiates the widget,
   * loads localization files, and includes necessary stylesheets and JavaScript.
   */
  public function __construct() {

    // Hooks fired when the Widget is activated and deactivated
    register_activation_hook( __FILE__, array( $this, 'activate' ) );
    register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

    parent::__construct(
      $this->get_widget_slug(),
      __( 'WL Katalogs&oslash;k', $this->get_widget_slug() ),
      array(
        'classname'  => $this->get_widget_slug().'-class',
        'description' => __( 'Setter inn s&oslash;kefelt for &aring; s&oslash;ke i b&oslash;ker, e-b&oslash;ker, film, TV og radio.', $this->get_widget_slug() )
      )
    );



    // Refreshing the widget's cached output with each new post
    add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
    add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
    add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );


  } // end constructor


  public function get_widget_slug() {
    return $this->widget_slug;
  }

  /*--------------------------------------------------*/
  /* Widget API Functions
  /*--------------------------------------------------*/

  /**
   * Outputs the content of the widget.
   *
   * @param array args  The array of form elements
   * @param array instance The current instance of the widget
   */
  public function widget( $args, $instance ) {
    // Check if there is a cached output
    $cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

    if ( !is_array( $cache ) )
      $cache = array();

    if ( ! isset ( $args['widget_id'] ) )
      $args['widget_id'] = $this->id;

    if ( isset ( $cache[ $args['widget_id'] ] ) )
      return print $cache[ $args['widget_id'] ];

    // go on with your widget logic, put everything into a string etc

    extract( $args, EXTR_SKIP );

    $widget_string = $before_widget;

    // Here is where you manipulate your widget's values based on their input fields

    ob_start();
    include( getTemplatePath('widget.php') );
    $widget_string .= ob_get_clean();
    $widget_string .= $after_widget;

    $cache[ $args['widget_id'] ] = $widget_string;
    wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );


    print $widget_string;

  } // end widget


  public function flush_widget_cache(){
      wp_cache_delete( $this->get_widget_slug(), 'widget' );
  }
  /**
   * Processes the widget's options to be saved.
   *
   * @param array new_instance The new instance of values to be generated via the update.
   * @param array old_instance The previous instance of values before the update.
   */


  public function update( $new_instance, $old_instance ) {

    $instance = $old_instance;

    // Here is where you update your widget's old values with the new, incoming values

    $instance['resultatside'] = strip_tags($new_instance['resultatside']);
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['katalog'] = strip_tags($new_instance['katalog']);

    return $instance;

  } // end widget

  /**
   * Generates the administration form for the widget.
   *
   * @param array instance The array of keys and values for the widget.
   */
  public function form( $instance ) {

    // Define default values for your variables
    $defaults = array( 'resultatside' => '' , 'tittel' => 'S&oslash;k i katalogen' , 'katalog' => '2020000');
    $instance = wp_parse_args(
      (array) $instance, $defaults
    );

    // Store the values of the widget in their own variable

    $resultatside = esc_attr( _is($instance,'resultatside') );
    $title        = esc_attr( _is($instance, 'title') );
    $katalog      = esc_attr( _is($instance,'katalog') );

    // Display the admin form
    include( getTemplatePath( 'admin.php' ) );

  } // end form


  public function widget_textdomain() {
    load_plugin_textdomain( 'inter-library-search-by-webloft', false, dirname( plugin_dir_path( KS_FILE ) ) . '/lang/' );
  }

} ///