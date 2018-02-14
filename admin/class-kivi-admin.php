<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/admin
 */

class Kivi_Admin {

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * @var Kivi_Background_Process
   */
  protected $process;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct( $plugin_name, $version ) {

    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->process = new Kivi_Background_Process();

  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kivi-admin.js', array( 'jquery', 'spectrum' ), $this->version, false );
    wp_enqueue_script( 'spectrum', plugin_dir_url( __FILE__ ) . 'js/spectrum.js', array( 'jquery' ), $this->version, false );

  }

  /**
   * Register Kivi plugins menu
   *
   * @since     1.0.0
   */
  public function kivi_register_menu_page() {
    add_menu_page(
        'KIVI',
        'KIVI',
        'manage_options',
        plugin_dir_path( dirname( __FILE__ ) ) .'admin/partials/kivi-admin-display.php',
        '',
        'dashicons-tickets',
        82
    );
  }


  /**
   * This function is hooked to an ajax call defined in class-kivi.php.
   * 1. XML is read from the remote source (KIVI)
   * 2. XML is parsed and data is put into an array
   * 3. data is put to background processing queue
   * 4. Non-existent items are deleted from wp
   * 5. background processing is dispatched
   */
  public function kivi_sync() {

    error_log('plugin activated!');

    update_option('kivi-show-statusbar', 1);

    /**
     * Stop multiple processes from being dispatched.
     */
    if ( $this->process->is_process_already_running() ) {
      wp_send_json(array('message'=>'Tausta-ajo jo käynnissä'));
      wp_die();
    }

    $active_items=[];
    $result = [];

    $baseurl = trim(preg_replace('/\/$/','', esc_attr(get_option('kivi-remote-url'))));
    $latest = trim( file_get_contents( $baseurl . '/LATEST.txt' ));
    $theurl = $baseurl . '/' . $latest;

    $doc = new DOMDocument();
    $z = new XMLReader;
    $res = $z->open(  $theurl );

    if( !$res ){
      error_log("Reading of the incoming XML failed.");
      update_option('kivi-show-statusbar', 0);
      wp_send_json(array('message'=>'Tiedoston lukeminen epäonnistui'));
      wp_die();
    }

    while ($z->read() && $z->name !== 'item');

    while ($z->name === 'item'){
      $node = simplexml_import_dom($doc->importNode($z->expand(), true));
      foreach ($node->children() as $foo) {
        if ( $foo->getName() == "image" ) {
          if ( $result != "" ) {
            $this->image_func( $foo, $result );
          }
        } elseif( $foo->getName() == "realtyrealtyoption") {
          if ( $result != "" ) {
            $this->realtyrealtyoption_func( $foo, $result );
          }
        }elseif( $foo->getName() == "areabasis_id") {
          if ( $result != "" ) {
            $this->areabasis_func( $foo, $result );
          }
        }elseif( $foo->getName() == "kivipresentation") {
          if ( $result != "" ) {
            $this->presentation_func( $foo, $result );
          }
        }elseif( $foo->getName() == "realty_vi_presentation") {
          if ( $result != "" ) {
            $this->realty_vi_presentation_func( $foo, $result );
          }
        }elseif( in_array( $foo->getName(), [ "unencumbered_price","price","debt"] )) {
          if ( $result != "" ) {
            $this->copy_int_func( $foo, $result );
          }
        }
        else {
          if ( $result != "" ) {
            $this->copy_func( $foo, $result );
          }
        }
      }

      $z->next('item');

      if( ! empty( get_kivi_option('kivi-prefilter-name' )) && ! empty(get_kivi_option('kivi-prefilter-value'))  ) {
        $filtername = get_kivi_option('kivi-prefilter-name');
        if( isset( $result[$filtername] ) && $result[$filtername] == get_kivi_option('kivi-prefilter-value' )){
          /* Filters match */
        }else {
          /* Filters don't match, ignore this item */
          continue;
        }
      }

      array_push($active_items, $result['realty_unique_no']);
      $this->process->push_to_queue( $result );
    }

    $this->process->items_delete( $active_items );

    $this->process->save()->dispatch();

    wp_send_json(array('message'=>'Tausta-ajo käynnistetty'));

    wp_die();
  }

  public function show_status_bar() {
    if ( get_option('kivi-show-statusbar') == 1 ) {
      echo '<div class="admin-status-bar">'.__('Tausta-ajo käynnissä. Ajon aikana käyttöliittymä saattaa tuntua hitaammalta kuin tavallisesti.', 'kivi').'</div>';
    }
  }


  /**
   * This function stops the dispatched background process
   * by calling wp_unschedule_event on the next occurrent
   * task.
   */
  public function kivi_stop() {
    update_option( 'kivi-show-statusbar', 0 );
    $this->process->stop();
    wp_send_json(array('message'=>'Tausta-ajo keskeytetty.'));
  }

  public function kivi_reset() {
    update_option( 'kivi-show-statusbar', 0 );
    $this->process->reset();
    wp_send_json(array('message'=>'Tausta-ajo keskeytetty ja aiemmin lisätyt tiedot poistettu.'));
  }

  public function kivi_set_remote_url() {
    $new_value = $_POST['kivi-remote-url'];

    if( !isset( $new_value ) || $new_value == '' ) {
      wp_send_json(array('status'=>0, 'message'=>'Päivitys epäonnistui.'));
    }
    update_option( 'kivi-remote-url', $new_value );
    wp_send_json(array('status'=>1, 'message'=>$new_value));
  }

  public function kivi_save_settings() {
    update_option( 'kivi-brand-color', $_POST['kivi-brand-color'] );
    update_option( 'kivi-slug', $_POST['kivi-slug'] );
    update_option( 'kivi-show-statusbar', $_POST['kivi-show-statusbar'] );
    set_kivi_option('kivi-show-sidebar',  $_POST['kivi-show-sidebar'] );
    set_kivi_option('kivi-use-debt-free-price-on-shortcode',  $_POST['kivi-use-debt-free-price-on-shortcode'] );
    set_kivi_option('kivi-use-www-size',  $_POST['kivi-use-www-size'] );
	set_kivi_option('kivi-clean-values',  $_POST['kivi-clean-values'] );
    set_kivi_option('kivi-prefilter-name',  $_POST['kivi-prefilter-name'] );
    set_kivi_option('kivi-prefilter-value',  $_POST['kivi-prefilter-value'] );
    set_kivi_option('kivi-gmap-id', $_POST['kivi-gmap-id']);
    wp_send_json( array('status'=>1, 'message'=>$_POST['kivi-brand-color'].', '.$_POST['kivi-slug'].', '.$_POST['kivi-show-statusbar']) );
  }

  /*
  * Copy attributes from item to the result object just as thet are or mapped
  * values on some cases
  */
  public function copy_func(&$item, &$result){
    $mappings = array(
      "holdingtype_id" =>
      array(
        '4' => 'muu',
        '5' => 'asunto-osakeyhtiö',
        '6' => 'kiinteistöosakeyhtiö',
        '7' => 'osaomistus'
      ),
      "watercharge_type_id" =>
      array(
        '1237.1' => '€/kk',
        '1237.2' => '€/hlö/kk',
        '1237.3' => 'oma mittari',
        '1237.4' => 'sisältyy vastikkeeseen',
        '1237.5' => 'sisältyy vuokraan'
      )
    );
    if( "$item" &&  array_key_exists( $item->getName(), $mappings ) ){
      $result[$item->getName()] = $mappings[$item->getName()]["$item"];
    }else{
      $result[$item->getName()] = "$item";
    }
  }

  /* Copy attributes from item to the result object as integers  */
  public function copy_int_func(&$item, &$result){
    $result[$item->getName()] = intval( "$item" );
  }

  /* Copy images from the parsed object to the result obbject. Only originals. */
  public function image_func(&$foo, &$result){
    $use_www_size = get_kivi_option('kivi-use-www-size');
    $images=[];
    foreach ($foo->children() as $image) {
      $i=[];
      if( $image->getName() == 'image_item'){
        foreach ($image->children() as $prop) {
          $this->copy_func($prop, $i);
        }
      }
      if( $use_www_size && $i['image_itemimagetype_name'] == 'kivirealty-www'){
        array_push($images,$i);
      }
      if( ! $use_www_size && $i['image_itemimagetype_name'] == 'kivirealty-original'){
        array_push($images,$i);
      }
    }
    $result['images'] = $images;
  }

  /*
  * Copy presentation items, 0..n of them
  */
  public function presentation_func( &$foo, &$result ){
    $pres = array();
    foreach ($foo->children() as $p) {
      $i=array();
      if( $p->getName() == 'kivipresentation_item'){
        foreach ($p->children() as $prop) {
          $this->copy_func($prop, $i);
        }
        array_push($pres,$i);
      }
    }
    $result['presentations'] = $pres;
  }
  
  /*
  * Copy vi_presentation items, 0..n of them
  */
  public function realty_vi_presentation_func( &$foo, &$result ){
    $pres = array();
    foreach ($foo->children() as $p) {
      $i=array();
      if( $p->getName() == 'realty_vi_presentation_item'){
        foreach ($p->children() as $prop) {
          $this->copy_func($prop, $i);
        }
        array_push($pres,$i);
      }
    }
    $result['vi_presentations'] = $pres;
  }


  /*
  * Handle arrea basis which happens to be a bit special
  * case in the xml. There's supposed to be only one of these
  * but still it's in a composite property
  */
  public function areabasis_func( &$foo, &$result ){
    $mappings = array(
      '1225.10' => 'yhtiöjärjestyksen mukainen',
      '1225.20' => 'isännöitsijäntodistuksen mukainen',
      '1225.30' => 'tarkistusmitattu'
     );
    foreach ($foo->children() as $opt) {
      if( $opt->getName() == 'areabasis_id_item'){
        if( array_key_exists("$opt",$mappings) ){
          $result['areabasis_id'] = $mappings["$opt"];
        }
        break;
      }
    }
  }

  /*
  * Handle realtyoptions. These hold many different types of
  * classified information about the items. These are grouped
  * in realytoptiongroups or they're in default group (no group in xml)
  */
  public function realtyrealtyoption_func( &$foo, &$result ){
    $opts=[];
    foreach ($foo->children() as $opt) {
      if( $opt->getName() == 'realtyrealtyoption_item'){
        $group = '';
        $val = '';
        foreach ($opt->children() as $prop) {
          if( $prop->getName() == 'realtyoptiongroup_id'){ $group = "$prop"; }
          elseif( $prop->getName() == 'realtyoption'){ $val = "$prop"; }
        }
        if( $group==''){$group='_default';}
        if($val){
          if( !array_key_exists( $group, $opts )){
            $opts[$group] = [];
          }
          array_push($opts[$group], $val);
        }
      }
    }
    $result['realtyrealtyoptions'] = $opts;
  }

  /*
  * This is the custom post type that represents the KIVI item
  */
  public function register_kivi_item_post_type() {
    if ( get_option('kivi-slug') ) :
      $slug = get_option('kivi-slug');
    else :
      $slug = 'kohde';
    endif;

    $labels = array(
      'name'                  => __( 'KIVI items', 'kivi_item' ),
      'singular_name'         => __( 'Item', 'kivi_item' ),
      'menu_name'             => __( 'KIVI items', 'kivi_item' ),
      'name_admin_bar'        => __( 'KIVI items', 'kivi_item' ),
      'archives'              => __( 'Item listing', 'kivi_item' ),
      'parent_item_colon'     => __( 'Parent Item:', 'kivi_item' ),
      'all_items'             => __( 'All items', 'kivi_item' ),
      'add_new_item'          => __( 'Add Item', 'kivi_item' ),
      'add_new'               => __( 'Add new', 'kivi_item' ),
      'new_item'              => __( 'New Item', 'kivi_item' ),
      'edit_item'             => __( 'Edit Item', 'kivi_item' ),
      'update_item'           => __( 'Update Item', 'kivi_item' ),
      'view_item'             => __( 'View Item', 'kivi_item' ),
      'search_items'          => __( 'Search items', 'kivi_item' ),
      'not_found'             => __( 'Not found.', 'kivi_item' ),
      'not_found_in_trash'    => __( 'Not found in trash.', 'kivi_item' ),
      'featured_image'        => __( 'Featured Item image', 'kivi_item' ),
      'set_featured_image'    => __( 'Set featured Item image', 'kivi_item' ),
      'remove_featured_image' => __( 'Remove featured image', 'kivi_item' ),
      'use_featured_image'    => __( 'Use as featured image', 'kivi_item' ),
      'insert_into_item'      => __( 'Insert into Item', 'kivi_item' ),
      'uploaded_to_this_item' => __( 'Uploaded to this Item', 'kivi_item' ),
      'items_list'            => __( 'Item list', 'kivi_item' ),
      'items_list_navigation' => __( 'Comapny navigation', 'kivi_item' ),
      'filter_items_list'     => __( 'Filter items', 'kivi_item' ),
    );
    $rewrite = array(
      'slug'                  => $slug,
      'with_front'            => true,
      'pages'                 => true,
      'feeds'                 => true,
    );
    $args = array(
      'label'                 => __( 'Item', 'kivi_item' ),
      'description'           => __( 'Item type', 'kivi_item' ),
      'labels'                => $labels,
      'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes', ),
      'taxonomies'            => array(),
      'hierarchical'          => false,
      'public'                => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'menu_position'         => 20,
      'show_in_admin_bar'     => false,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => true,
      'exclude_from_search'   => false,
      'publicly_queryable'    => true,
      'rewrite'               => $rewrite,
      'show_in_rest'          => true,
      'capability_type'       => 'page',
    );

    register_post_type( 'kivi_item', $args );

    flush_rewrite_rules();
  }

  /*
  * Start the scheduler that runs the background process ie. checks
  * the new xml and does it's stuff every 30 minutes.
  */
  public function start_scheduler() {
    if (! wp_next_scheduled ( 'kivi_items_sync' )) {
      wp_schedule_event(time(), 'every15minutes', 'kivi_items_sync');
    }
  }

  public function stop_scheduler() {
    wp_clear_scheduled_hook('kivi_items_sync');
  }

}
