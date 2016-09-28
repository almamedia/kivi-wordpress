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

  }

  public function load_kivi_index_template($index_template) {
    global $post;
    $template_name = 'kivi-index-template.php';
    $theme_provided_template = get_stylesheet_directory() . "/". $template_name;
    if ($post->post_type == "kivi_item"){
      if( file_exists( $theme_provided_template )) {
        return $theme_provided_template;
      }else{
        return dirname(__FILE__) . '/partials/'.$template_name ;
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



  public function register_shortcodes(){
    add_shortcode('taloyhtio', array($this, 'realtycompany_shortcode'));
    add_shortcode('kunta', array($this, 'town_shortcode'));
    add_shortcode('tyyppi', array($this, 'realtytype_shortcode'));
  }

  public function kivi_list_shortcode($key, $value,  $toimeksianto=""){
    $html = '<div class="kivi-item-list"><div class="kivi-item-list-wrapper">';

    $args = array(
      'post_type' => 'kivi_item',
      'posts_per_page' => -1,
      'meta_query' => array(
        'relation' => 'AND',
        array(
          'key' => $key,
          'value' => $value,
          'compare' => '='
        ),
		array (
		   
				'key' => '_assignment_type',
				'value' => $toimeksianto ,
				'compare' => 'LIKE'
				)
      )
    );

    $items = query_posts($args);

    $brand_styling = ' style="color:'.get_option("kivi-brand-color").';"';
    
    if ( have_posts() ) :
      while (have_posts()) : the_post();
        $kivi_item_desc = ucfirst(get_post_meta(get_the_ID(), '_realtytype_id', true));
        if ( get_post_meta(get_the_ID(), '_street', true) != "" ) {
          $kivi_item_desc = $kivi_item_desc . ' | '.get_post_meta(get_the_ID(), '_street', true);
        }
        if ( get_post_meta(get_the_ID(), '_postcode', true) != "" ) {
          $kivi_item_desc = $kivi_item_desc . ' | '.get_post_meta(get_the_ID(), '_postcode', true);
        }
        if ( get_post_meta(get_the_ID(), '_addr_town_area', true) != "" ) {
          $kivi_item_desc = $kivi_item_desc . ' | '.get_post_meta(get_the_ID(), '_addr_town_area', true);
        }

        $kivi_item_title_desc = '';
        if ( get_post_meta(get_the_ID(), '_living_area_m2', true) != "" ) {
          $kivi_item_title_desc = $kivi_item_title_desc .get_post_meta(get_the_ID(), '_living_area_m2', true).'m2';
        }
        if ( get_post_meta(get_the_ID(), '_price', true) != "" ) {
          $kivi_item_title_desc = $kivi_item_title_desc . ', '.number_format(intval(get_post_meta(get_the_ID(), '_price', true)), 0, ".", " ").' €';
        }

        $html = $html.'<div class="kivi-item-list-item"><a href="'.get_the_permalink().'" class="kivi-item-list-item-image-wrapper"><img src="'.wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), "single-post-thumbnail" )[0].'" class="kivi-item-list-item-image"></a><div class="kivi-item-list-body"><h3 class="kivi-item-list-heading"><a class="kivi-item-list-link"'.$brand_styling.' href="'.get_the_permalink().'">'."<div class='kivi-item-shortcode-link-desc1'>".get_the_title()."</div>"."<div class='kivi-item-shortcode-link-desc2'>".$kivi_item_title_desc.'</div></a></h3><p class="kivi-item-list-item-meta">'.$kivi_item_desc.'</p></div></div>';
      endwhile;
    endif;
    wp_reset_query();
    $html = $html.'</div></div>';
    return $html;
  }

  public function realtycompany_shortcode($attributes) {
    return $this->kivi_list_shortcode('_realtycompany', $attributes["nimi"]);
  }

  public function realtytype_shortcode($attributes) {
	  
	$toimeksianto=null;
    
	if (array_key_exists('toimeksianto', $attributes)) {
		$toimeksianto = $attributes["toimeksianto"];
    }

	  
    return $this->kivi_list_shortcode('_realtytype_id', $attributes["nimi"],$toimeksianto);
  }

  public function town_shortcode($attributes) {
    return $this->kivi_list_shortcode('_town', $attributes["nimi"]);
  }

}
