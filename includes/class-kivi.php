<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/includes
 */


class Kivi {

  /**
   * The loader that's responsible for maintaining and registering all hooks that power
   * the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      Kivi_Loader    $loader    Maintains and registers all hooks for the plugin.
   */
  protected $loader;

  /**
   * The unique identifier of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string    $plugin_name    The string used to uniquely identify this plugin.
   */
  protected $plugin_name;

  /**
   * The current version of the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string    $version    The current version of the plugin.
   */
  protected $version;

  /**
   * Define the core functionality of the plugin.
   *
   * Set the plugin name and the plugin version that can be used throughout the plugin.
   * Load the dependencies, define the locale, and set the hooks for the admin area and
   * the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function __construct() {

    $this->plugin_name = 'kivi';
    $this->version = '1.0.0';

    $this->load_dependencies();
    $this->set_locale();
    $this->define_admin_hooks();
    $this->define_public_hooks();

  }

  /**
   * Load the required dependencies for this plugin.
   *
   * Include the following files that make up the plugin:
   *
   * - Kivi_Loader. Orchestrates the hooks of the plugin.
   * - Kivi_i18n. Defines internationalization functionality.
   * - Kivi_Admin. Defines all hooks for the admin area.
   * - Kivi_Public. Defines all hooks for the public side of the site.
   *
   * Create an instance of the loader which will be used to register the hooks
   * with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function load_dependencies() {

    /**
     * The class responsible for orchestrating the actions and filters of the
     * core plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kivi-loader.php';

    /**
     * The class responsible for defining internationalization functionality
     * of the plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kivi-i18n.php';

    /**
     * The class responsible for doing the heavy data processing in the background
     *
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp-background-processing.php';

    /**
     * The class responsible for shit
     *
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-kivi-background-process.php';

    /**
     * The class responsible for defining all actions that occur in the admin area.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-kivi-admin.php';

    /**
     * The class responsible for defining all actions that occur in the public-facing
     * side of the site.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-kivi-public.php';

    $this->loader = new Kivi_Loader();

  }

  /**
   * Define the locale for this plugin for internationalization.
   *
   * Uses the Kivi_i18n class in order to set the domain and to register the hook
   * with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function set_locale() {

    $plugin_i18n = new Kivi_i18n();

    $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

  }

  /**
   * Register all of the hooks related to the admin area functionality
   * of the plugin.
   *
   * @since    1.0.0
   * @access   private
   */
  private function define_admin_hooks() {

    $plugin_admin = new Kivi_Admin( $this->get_plugin_name(), $this->get_version() );

    add_action('admin_init', array($this, 'register_kivi_settings'));
    add_action('admin_init', array($this, 'upload_file_size_increase'));
    add_action('admin_enqueue_scripts', array($this, 'kivi_enqueue_styles'));

    $this->loader->add_action( 'activated_plugin', $plugin_admin, 'start_scheduler' );
    $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'register_metadata_metabox' );
    $this->loader->add_action( 'deactivated_plugin', $plugin_admin, 'stop_scheduler' );
    $this->loader->add_action( 'init', $plugin_admin, 'register_kivi_item_post_type' );
    $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
    $this->loader->add_action( 'admin_menu', $plugin_admin, 'kivi_register_menu_page' );
    $this->loader->add_action( 'in_admin_header', $plugin_admin, 'show_status_bar' );
    $this->loader->add_action( 'wp_ajax_kivi_sync', $plugin_admin, 'kivi_sync' );
    $this->loader->add_action( 'kivi_items_sync', $plugin_admin, 'kivi_sync' );
    $this->loader->add_action( 'wp_ajax_kivi_stop', $plugin_admin, 'kivi_stop' );
    $this->loader->add_action( 'wp_ajax_kivi_reset', $plugin_admin, 'kivi_reset' );
    $this->loader->add_action( 'wp_ajax_kivi_set_remote_url', $plugin_admin, 'kivi_set_remote_url' );
    $this->loader->add_action( 'wp_ajax_kivi_save_settings', $plugin_admin, 'kivi_save_settings' );
    $this->loader->add_action( 'wp_ajax_kivi_save_settings', $plugin_admin, 'register_kivi_item_post_type' );

    add_filter( 'cron_schedules', 'kivi_add_schedule' );

  }

  public function register_kivi_settings() {
    register_setting( 'kivi-settings', 'kivi-remote-url' );
    register_setting( 'kivi-settings', 'kivi-brand-color' );
    register_setting( 'kivi-settings', 'kivi-slug' );
    register_setting( 'kivi-settings', 'kivi-show-statusbar' );
  }

  public function kivi_enqueue_styles() {
    wp_enqueue_style($this->get_plugin_name(), plugin_dir_url(__FILE__) . '../admin/css/kivi-admin.css');
  }

  public function upload_file_size_increase() {
    @ini_set( 'upload_max_size' , '64M' );
    @ini_set( 'post_max_size', '64M');
    @ini_set( 'max_execution_time', '300' );
  }

  /**
   * Register all of the hooks related to the public-facing functionality
   * of the plugin.
   *
   * @since    1.0.0
   * @access   private
   */
  private function define_public_hooks() {

    $plugin_public = new Kivi_Public( $this->get_plugin_name(), $this->get_version() );

    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

    $this->loader->add_filter( 'cmb_meta_boxes', $plugin_public, 'kivi_item_metaboxes' );
    //add_action( 'init', array($this, 'map_post_meta') );
    add_filter( 'single_template', array($this, 'load_single_kivi_template') );
    add_filter( 'archive_template', array($this, 'load_kivi_index_template') ) ;

    add_filter('redirect_canonical', function ($redirect_url) {
      if (is_singular('kivi_item')){
        $redirect_url = false;
      }
      return $redirect_url;
    });

    add_action('init', array($this, 'register_shortcodes'));
	
    if( get_kivi_option('kivi-clean-values') ) {
		$this->loader->add_filter( 'kivi_viewable_value', $plugin_public, 'filter_viewable_values', 10, 3 );
		$this->loader->add_filter( 'kivi_viewable_value', $plugin_public, 'filter_presentation_date', 10, 3 );
	}
  }

  public function load_kivi_index_template( $index_template ) {
    $template_name = 'kivi-index-template.php';
    
    if ( is_post_type_archive( "kivi_item" ) ) {
		$theme_provided_template = get_stylesheet_directory() . "/" . $template_name;
		if( file_exists( $theme_provided_template ) ) {
			return $theme_provided_template;
		}else{
			return dirname(__FILE__) . '/partials/' . $template_name ;
		}
    }
    return $index_template;
  }


  public function load_single_kivi_template($template) {
    global $post;
    $template_name = 'kivi-single-template.php';
    $theme_provided_template = get_stylesheet_directory() . "/". $template_name;
    if ($post->post_type == "kivi_item"){
      if( file_exists( $theme_provided_template )) {
        return $theme_provided_template;
      }else{
        return dirname(__FILE__) . '/partials/'.$template_name ;
      }
    }
    return $template;
  }


  /**
   * Run the loader to execute all of the hooks with WordPress.
   *
   * @since    1.0.0
   */
  public function run() {
    $this->loader->run();
  }

  /**
   * The name of the plugin used to uniquely identify it within the context of
   * WordPress and to define internationalization functionality.
   *
   * @since     1.0.0
   * @return    string    The name of the plugin.
   */
  public function get_plugin_name() {
    return $this->plugin_name;
  }

  /**
   * The reference to the class that orchestrates the hooks with the plugin.
   *
   * @since     1.0.0
   * @return    Kivi_Loader    Orchestrates the hooks of the plugin.
   */
  public function get_loader() {
    return $this->loader;
  }

  /**
   * Retrieve the version number of the plugin.
   *
   * @since     1.0.0
   * @return    string    The version number of the plugin.
   */
  public function get_version() {
    return $this->version;
  }


  /* Register shortcodes for listing items based on item properties */
  public function register_shortcodes(){
    add_shortcode('taloyhtio', array($this, 'realtycompany_shortcode'));
    add_shortcode('kunta', array($this, 'town_shortcode'));
    add_shortcode('tyyppi', array($this, 'realtytype_shortcode'));
    add_shortcode('toimeksianto', array($this, 'assignmenttype_shortcode'));
    add_shortcode('tuoteryhma', array($this, 'itemgroup_shortcode'));
    add_shortcode('kivi', array($this, 'kivi_shortcode'));
  }

  public function kivi_list_shortcode( $meta_query, $attributes = array() ){

    $per_page = -1;
    if( isset( $attributes['count'] ) && intval($attributes['count']) ){
      $per_page = intval($attributes['count']);
    }
    $order = "ASC";
    if( isset( $attributes['order'] ) && !empty($attributes['order']) ){
      if( trim(strtolower($attributes['order'])) == "desc" ){
        $order = "DESC";
      }
    }
    $args = array(
      'post_type' => 'kivi_item',
      'orderby' => 'publish_date',
      'order' => $order,
      'posts_per_page' => $per_page,
      'meta_query' => $meta_query
    );

    $items = query_posts($args);

    if ( have_posts() ) :
      ob_start();
      while (have_posts()) : the_post();

        if ( $overridden_template = locate_template( 'kivi-single-item-part.php' ) ) {
          load_template( $overridden_template, false );
        } else {
          load_template( dirname( __FILE__ ) . '/partials/kivi-single-item-part.php', false );
        }

      endwhile;
    endif;
    wp_reset_query();
    return ob_get_clean();
  }

  /**
   * A generic shortcode [kivi].
   * Any attributes will do the filtering, ex. [kivi town="Helsinki" assignment_type="vuokra"] will display all rentals
   * in Helsinki. An attribute count="X" can be used to limit results.
   * Relation is hard coded to "AND" and attribute must be found. If results is 0, it's propably either mistyped
   * attribute name or too many filters resulting zero items.
   * There are two custom attributes:
   *  - count int (Default: no limit -1)
   *  - order "desc|asc" (Default: asc)
   * @param $attributes shortcode attributes as an array
   * @return string
   */
  public function kivi_shortcode( $attributes = array() ) {

    $meta_query = array( 'relation' => 'AND' );

    $nofilter = array("count", "order");

    // compare should be "LIKE" with these
    $like = array('assignment_type');

    if( is_array( $attributes ) ) {
      foreach ($attributes as $key => $value) {
        $compare = "=";
        if (in_array($key, $like)) {
          $compare = "LIKE";
        }

        if (!in_array(strtolower($key), $nofilter)) { // do not filter all attributes
          $meta_query[] = array(
            'key' => '_' . $key,
            'value' => $value,
            'compare' => $compare
          );
        }
      }
    }

    return $this->kivi_list_shortcode($meta_query, $attributes);
  }

  public function assignmenttype_shortcode($attributes){
    $meta_query = array(
      'relation' => 'AND',
      array(
        'key' => '_assignment_type',
        'value' => $attributes["tyyppi"],
        'compare' => 'LIKE'
      )
    );
    return $this->kivi_list_shortcode($meta_query);
  }

  public function realtycompany_shortcode($attributes) {
    $meta_query = array(
      'relation' => 'AND',
      array(
        'key' => '_realtycompany',
        'value' => $attributes["nimi"],
        'compare' => '='
      )
    );
    return $this->kivi_list_shortcode($meta_query);
  }

  public function itemgroup_shortcode($attributes) {
    $toimeksianto=null;
    if (array_key_exists('toimeksianto', $attributes)) {
      $toimeksianto = $attributes["toimeksianto"];
    }
    $meta_query = array(
      'relation' => 'AND',
      array(
        'key' => '_itemgroup_id',
        'value' => $attributes["nimi"],
        'compare' => '='
      ),
      array (
        'key' => '_assignment_type',
        'value' => $toimeksianto ,
        'compare' => 'LIKE'
      )
    );
    return $this->kivi_list_shortcode( $meta_query );
  }

  public function realtytype_shortcode($attributes) {
    $toimeksianto=null;
    if (array_key_exists('toimeksianto', $attributes)) {
      $toimeksianto = $attributes["toimeksianto"];
    }
    $meta_query = array(
      'relation' => 'AND',
      array(
        'key' => '_realtytype_id',
        'value' => $attributes["nimi"],
        'compare' => '='
      ),
      array (
        'key' => '_assignment_type',
        'value' => $toimeksianto ,
        'compare' => 'LIKE'
      )
    );
    return $this->kivi_list_shortcode( $meta_query );
  }

  public function town_shortcode($attributes) {
    $meta_query = array(
      'relation' => 'AND',
      array(
        'key' => '_town',
        'value' => $attributes["nimi"],
        'compare' => '='
      )
    );
    return $this->kivi_list_shortcode($meta_query);
  }
}
