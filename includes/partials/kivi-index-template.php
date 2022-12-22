<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 * Privides a simple index page with somee filtering options.
 *
 * Default ordering is "publish_date" "DESC" : new items in WP will come first.
 * If multiple items are downloaded at once, their order might be random.
 * To reorder items, you can modify publish_date in WP admin for any item.
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/public/partials
 */
get_header();

$args = array(
    'post_type' => 'kivi_item',
    'orderby'	=> 'meta_value_num',
    'meta_key'  => '_realty_id',
    'order'		=> 'DESC',
    'posts_per_page' => 30,
    'meta_query'=> array(
            array( 'key' => '_ui_section_SUMMARY', 'compare' => 'EXISTS' )
    ),
);
$args['paged'] = ( get_query_var('paged') ? get_query_var('paged') : 1 );

$priceminval ="";
$pricemaxval ="";
$areaminval ="";
$realtytypeval ="";
$townval = "";
$toim_tyyppival = "";
$toim_tyyppi = "FOR_SALE";

if ( ! empty($_GET) ){
  /* There's a GET request and we need to filter the items to show */
  $pricemin = array();
  $pricemax = array();
  $areamin = array();
  $street =  array();
  $town =  array();
  $realty_id =  array();
  $postcode = array();
  $realtytype = array();
  $toim_tyyppi = array();
  $town_select = array();
  $quartertown = array();

  populate_searchcriteria( $pricemin, $_GET, 'kivi-item-asunto-hintamin', '_unencumbered_price', '>=', true);
  populate_searchcriteria( $pricemax, $_GET, 'kivi-item-asunto-hintamax', '_unencumbered_price', '<=', true);
  populate_searchcriteria( $areamin, $_GET, 'kivi-item-asunto-pamin', '_living_area_m2', '>=', true);
  populate_searchcriteria( $street, $_GET, 'kivi-item-asunto-osoite', '_street', 'LIKE');
  populate_searchcriteria( $town, $_GET, 'kivi-item-asunto-osoite', '_town', 'LIKE');
  populate_searchcriteria( $realty_id, $_GET, 'kivi-item-asunto-osoite', '_realty_unique_no', '=');
  populate_searchcriteria( $postcode, $_GET, 'kivi-item-asunto-osoite', '_postcode', '=');
  populate_searchcriteria( $realtytype, $_GET, '_realtytype', '_realtytype','=');
  populate_searchcriteria( $toim_tyyppi, $_GET, '_assignment_type', '_assignment_type','LIKE');
  populate_searchcriteria( $town_select, $_GET, '_town', '_town', 'LIKE');
  populate_searchcriteria( $quartertown, $_GET, 'kivi-item-asunto-osoite', '_quarteroftown', 'LIKE');

  $args['meta_query'] = array(
      'relation' => 'AND',
      array( 'key' => '_ui_section_SUMMARY', 'compare' => 'EXISTS' ),
      $pricemin,
      $pricemax,
      $areamin,
      $realtytype,
      $toim_tyyppi,
      $town_select,
      array(
        'relation' => 'OR',
        $street,
        $town,
        $quartertown,
        $postcode,
        $realty_id,
      )
  );

  /* Values for the form to match the filter criteria */
  $priceminval = get_posted_value( $_GET, 'kivi-item-asunto-hintamin' );
  $pricemaxval = get_posted_value( $_GET, 'kivi-item-asunto-hintamax' );
  $areaminval = get_posted_value( $_GET, 'kivi-item-asunto-pamin' );
  $realtytypeval = get_posted_value( $_GET, '_realtytype' );
  $townval = get_posted_value( $_GET, 'kivi-item-asunto-osoite' );
  $toim_tyyppi = get_posted_value( $_GET, '_assignment_type' );
}

query_posts($args);

?>
  <div id="primary" class="content-area">
    <main id="main" class="site-main kivi-index-archive" role="main">

      <h1 class="kivi-index-archive-title"><?php _e("Kohdelistaus", "kivi"); ?></h1>

		<?php do_action( 'kivi_index_archive_before', get_the_id() ); ?>

      <form action="<?php echo get_post_type_archive_link( 'kivi_item' ); ?>" method="get" class="kivi-item-filters">
        <div class="kivi-item-filters-wrapper">
          <div class="kivi-filter-cell">
            <label><?php _e('Asunnon tyyppi', 'kivi'); ?>
              <select name="_realtytype">
                <option <?php if ($realtytypeval == '') echo 'selected'; ?> value="" name=""><?php _e("-", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'kerrostalo') echo 'selected'; ?> value="kerrostalo" name="kerrostalo"><?php _e("Kerrostalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'omakotitalo') echo 'selected'; ?> value="omakotitalo" name="omakotitalo"><?php _e("Omakotitalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'rivitalo') echo 'selected'; ?> value="rivitalo" name="rivitalo"><?php _e("Rivitalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'paritalo') echo 'selected'; ?> value="paritalo" name="paritalo"><?php _e("Paritalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'erillistalo') echo 'selected'; ?> value="erillistalo" name="erillistalo"><?php _e("Erillistalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'puutalo') echo 'selected'; ?> value="puutalo" name="puutalo"><?php _e("Puutalo-osake", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'luhtitalo') echo 'selected'; ?> value="luhtitalo" name="luhtitalo"><?php _e("Luhtitalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'toimitila') echo 'selected'; ?> value="toimitila"><?php _e("Toimitila", "kivi"); ?></option>
				<option <?php if ($realtytypeval == 'metsätila') echo 'selected'; ?> value="metsätila"><?php _e("Maa- ja metsätilat", "kivi"); ?></option>
              </select>
          </div>
          <div class="kivi-filter-cell kivi-filter-cell-50">
            <label><?php _e('Sijainti', 'kivi'); ?>
              <input type="text" name="kivi-item-asunto-osoite" id="kivi-item-asunto-osoite" value="<?php echo esc_attr($townval); ?>" class="kivi-item-input" placeholder="<?php _e('Sijainti tai kohde', 'kivi'); ?>">
          </label>
          </div>
          <br />
          <div class="kivi-filter-cell kivi-filter-cell-15">
            <label><?php _e('Hinta min', 'kivi'); ?>
              <input type="text" name="kivi-item-asunto-hintamin" id="kivi-item-asunto-hintamin" value="<?php echo esc_attr($priceminval); ?>" class="kivi-item-input" placeholder="<?php _e('Hinta min', 'kivi'); ?>">
            </label>
          </div>
          <div class="kivi-filter-cell kivi-filter-cell-15">
            <label><?php _e('Hinta max', 'kivi'); ?>
              <input type="text" name="kivi-item-asunto-hintamax" id="kivi-item-asunto-hintamax" value="<?php echo esc_attr($pricemaxval); ?>" class="kivi-item-input" placeholder="<?php _e('Hinta max', 'kivi'); ?>">
            </label>
          </div>
          <div class="kivi-filter-cell kivi-filter-cell-15">
            <label><?php _e('Pinta-ala min', 'kivi'); ?>
              <input type="text" name="kivi-item-asunto-pamin" id="kivi-item-asunto-pamin" value="<?php echo esc_attr($areaminval); ?>" class="kivi-item-input" placeholder="<?php _e('Pinta-ala min', 'kivi'); ?>">
            </label>
          </div>
          <div class="kivi-filter-cell kivi-filter-cell">
            <label><?php _e('Toimeksiannon tyyppi', 'kivi'); ?></label>
            <select name="_assignment_type">
              <option value="">-</option>
              <option value="FOR_SALE" <?php if ($toim_tyyppi == 'FOR_SALE') echo 'selected'; ?>>Myynti</option>
              <option value="FOR_RENT" <?php if ($toim_tyyppi == 'FOR_RENT') echo 'selected'; ?>>Vuokra</option>
            </select>
          </div>
          <div class="kivi-filter-cell">
            <input type="submit" name="submit" class="button button-primary button-kivi" id="kivi-index-search" value="<?php _e('Hae', 'kivi'); ?>" />
          </div>
        </div>
      </form>

      <?php

      if ( isset($_GET["submit"]) ) :
        echo '<h3>' . __("Hakutulokset", "kivi") . '</h3>';
      endif;

      if ( have_posts() ) :
        ?><div class="kivi-index-item-list">
          <?php while ( have_posts() ) {
            the_post();
            if ( $overridden_template = locate_template( 'kivi-single-item-part.php' ) ) {
              load_template( $overridden_template, false );
            } else {
              load_template( dirname( __FILE__ ) . '/kivi-single-item-part.php', false );
            }
          }?>
          </div>
		</div>
        <div class="kivi-index-paginator">
	  <?php else: ?>
		<p class="kivi-no-items-info"><?php _e("Ei kohteita", "kivi"); ?></p>
      <?php endif;

      $pagination_args = array(
        'prev_text' => __('« Edellinen','kivi'),
        'next_text' => __('Seuraava »','kivi'),
      );
      echo paginate_links( $pagination_args ); ?>
      </div>
	  <?php do_action( 'kivi_index_archive_after', get_the_id() ); ?>
    </main><!-- #main -->
  </div><!-- #primary -->

<?php
//get_sidebar();
get_footer();
