<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 * Privides a simple index page with somee filtering options.
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/public/partials
 */
get_header();
$brand_styling = ' style="background-color:'.get_option("kivi-brand-color").';"';

$huonelukuarvo ="";
$priceminval ="";
$pricemaxval ="";
$areaminval ="";
$areamaxval ="";
$realtytypeval ="";
$townval = "";
$toim_tyyppival = "";

if ( ! empty($_GET) ){
  /* There's a GET request and we need to filter the items to show */
  $roomcount = array();
  $pricemin = array();
  $pricemax = array();
  $areamin = array();
  $areamax = array();
  $street =  array();
  $town =  array();
  $postcode = array();
  $realtytype = array();
  $toim_tyyppi = array();

  populate_searchcriteria( $roomcount, $_GET, 'kivi-item-asunto-huoneluku-select', '_flattype_id', '=');
  populate_searchcriteria( $pricemin, $_GET, 'kivi-item-asunto-hintamin', '_unencumbered_price', '>=', true);
  populate_searchcriteria( $pricemax, $_GET, 'kivi-item-asunto-hintamax', '_unencumbered_price', '<=', true);
  populate_searchcriteria( $areamin, $_GET, 'kivi-item-asunto-pamin', '_living_area_m2', '>=', true);
  populate_searchcriteria( $areamax, $_GET, 'kivi-item-asunto-pamax', '_living_area_m2', '<=', true);
  populate_searchcriteria( $street, $_GET, 'kivi-item-asunto-osoite', '_street', 'LIKE');
  populate_searchcriteria( $town, $_GET, 'kivi-item-asunto-osoite', '_town', 'LIKE');
  populate_searchcriteria( $postcode, $_GET, 'kivi-item-asunto-osoite', '_postcode', '=');
  populate_searchcriteria( $realtytype, $_GET, 'kivi-item-asunto-type-select', '_realtytype_id','=');
  populate_searchcriteria( $toim_tyyppi, $_GET, 'kivi-item-toimeksianto-tyyppi', '_assignment_type','LIKE');

  $args = array(
    'post_type' => 'kivi_item',
    'posts_per_page' => 10,
    'meta_query' => array(
      'relation' => 'AND',
      $roomcount,
      $pricemin,
      $pricemax,
      $areamin,
      $areamax,
      $realtytype,
      $toim_tyyppi,
      array(
        'relation' => 'OR',
        $street,
        $town,
        $postcode,
      )
    ),
	'orderby'	=> 'meta_value',
	'meta_key' 	=> '_homepage_publish_date',
	'meta_type'	=> 'DATETIME',
	'order'		=> 'DESC',
  );
  query_posts($args);

  /* Values for the form to match the filter criteria */

  $huonelukuarvo = get_posted_value( $_GET, 'kivi-item-asunto-huoneluku-select');
  $priceminval = get_posted_value( $_GET, 'kivi-item-asunto-hintamin' );
  $pricemaxval = get_posted_value( $_GET, 'kivi-item-asunto-hintamax' );
  $areaminval = get_posted_value( $_GET, 'kivi-item-asunto-pamin' );
  $areamaxval = get_posted_value( $_GET, 'kivi-item-asunto-pamax' );
  $realtytypeval = get_posted_value( $_GET, 'kivi-item-asunto-type-select' );
  $townval = get_posted_value( $_GET, 'kivi-item-asunto-osoite' );
  $toim_tyyppi = get_posted_value( $_GET, 'kivi-item-toimeksianto-tyyppi' );

}
else{
	$args = array(
		'post_type' => 'kivi_item',
		'orderby'	=> 'meta_value',
		'meta_key' 	=> '_homepage_publish_date',
		'meta_type'	=> 'DATETIME',
		'order'		=> 'DESC',
		'posts_per_page' => 10,
	);
	$args['paged'] = ( get_query_var('paged') ? get_query_var('paged') : 1 );
	query_posts($args);
}

?>
  <div id="primary" class="content-area">
    <main id="main" class="site-main kivi-index-archive" role="main">

      <h1 class="kivi-index-archive-title"><?php _e("Kohdelistaus", "kivi"); ?></h1>

      <form action="<?php echo get_post_type_archive_link( 'kivi_item' ); ?>" method="get" class="kivi-item-filters">
        <div class="kivi-item-filters-wrapper">
          <div class="kivi-filter-cell">
            <label><?php _e('Asunnon tyyppi', 'kivi'); ?>
              <select name="kivi-item-asunto-type-select">
                <option <?php if ($realtytypeval == '') echo 'selected'; ?> value="" name=""><?php _e("-", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'kerrostalo') echo 'selected'; ?> value="kerrostalo" name="kerrostalo"><?php _e("Kerrostalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'omakotitalo') echo 'selected'; ?> value="omakotitalo" name="omakotitalo"><?php _e("Omakotitalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'rivitalo') echo 'selected'; ?> value="rivitalo" name="rivitalo"><?php _e("Rivitalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'paritalo') echo 'selected'; ?> value="paritalo" name="paritalo"><?php _e("Paritalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'erillistalo') echo 'selected'; ?> value="erillistalo" name="erillistalo"><?php _e("Erillistalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'puutalo') echo 'selected'; ?> value="puutalo" name="puutalo"><?php _e("Puutalo-osake", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'luhtitalo') echo 'selected'; ?> value="luhtitalo" name="luhtitalo"><?php _e("Luhtitalo", "kivi"); ?></option>
                <option <?php if ($realtytypeval == 'toimitila') echo 'selected'; ?> value="toimitila"><?php _e("Toimitila", "kivi"); ?></option>
              </select>
          </div>
          <div class="kivi-filter-cell kivi-filter-cell-50">
            <label><?php _e('Sijainti', 'kivi'); ?>
              <input type="text" name="kivi-item-asunto-osoite" id="kivi-item-asunto-osoite" value="<?php echo esc_attr($townval); ?>" class="kivi-item-input" placeholder="<?php _e('Sijainti: esim. Tampere tai 00100', 'kivi'); ?>">
          </label>
          </div>
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
          <div class="kivi-filter-cell kivi-filter-cell-15">
            <label><?php _e('Pinta-ala max', 'kivi'); ?>
              <input type="text" name="kivi-item-asunto-pamax" id="kivi-item-asunto-pamax" value="<?php echo esc_attr($areamaxval); ?>" class="kivi-item-input" placeholder="<?php _e('Pinta-ala max', 'kivi'); ?>">
            </label>
          </div>
          <div class="kivi-filter-cell">
            <label><?php _e('Huoneluku', 'kivi'); ?>
              <select name="kivi-item-asunto-huoneluku-select" value="<?php echo esc_attr($huonelukuarvo); ?>">
                <option name="default" value="">-</option>
                <option name="yksio" value="yksio" <?php if ($huonelukuarvo == 'yksio') echo 'selected'; ?> ><?php _e('Yksiö', 'kivi'); ?></option>
                <option name="kaksio" value="kaksio" <?php if ($huonelukuarvo == 'kaksio') echo 'selected'; ?>><?php _e('2 huonetta', 'kivi'); ?></option>
                <option name="kolmio" value="kolmio" <?php if ($huonelukuarvo == 'kolmio') echo 'selected'; ?>><?php _e('3 huonetta', 'kivi'); ?></option>
                <option name="4 h" value="4 h" <?php if ($huonelukuarvo == '4 h') echo 'selected'; ?>><?php _e('4 huonetta', 'kivi'); ?></option>
                <option name="5 h" value="5 h" <?php if ($huonelukuarvo == '5 h') echo 'selected'; ?>><?php _e('5 huonetta', 'kivi'); ?></option>
                <option name="6 h ja enemmän" value="6 h ja enemmän" <?php if ($huonelukuarvo == '6 h ja enemmän') echo 'selected'; ?>><?php _e('Yli 5 huonetta', 'kivi'); ?></option>
              </select>
            </label>
          </div>
          <div class="kivi-filter-cell kivi-filter-cell">
            <label><?php _e('Toimeksiannon tyyppi', 'kivi'); ?></label>
            <select name="kivi-item-toimeksianto-tyyppi">
              <option value="">-</option>
              <option value="myyntitoimeksianto" <?php if ($toim_tyyppi == 'myyntitoimeksianto') echo 'selected'; ?>>Myynti</option>
              <option value="vuokranantaja" <?php if ($toim_tyyppi == 'vuokranantaja') echo 'selected'; ?>>Vuokra</option>
              <option value="uudiskohde" <?php if ($toim_tyyppi == 'uudiskohde') echo 'selected'; ?>>Vain uudiskohteet</option>
            </select>
          </div>
          <div class="kivi-filter-cell">
            <input type="submit" name="submit" class="button button-primary button-kivi" id="kivi-index-search"<?php echo esc_attr($brand_styling); ?> value="<?php _e('Hae', 'kivi'); ?>" />
          </div>
        </div>
      </form>

      <?php

      if ( isset($_GET["submit"]) ) :
        echo '<h3>' . __("Hakutulokset", "kivi") . '</h3>';
      endif;

      if ( have_posts() ) :
        ?><div class="kivi-index-item-list">
          <?php while ( have_posts() ) : the_post(); ?>
            <div class="kivi-index-item <?php echo Kivi_Public::get_css_classes(get_the_id()); ?>">
				      <a href="<?php the_permalink(); ?>" class="kivi-item-link">
                <div class="kivi-item-image">
                  <?php
                    if ( has_post_thumbnail() ) { the_post_thumbnail('medium_large'); }
                  ?>
                </div>
                <div class="kivi-item-body">
                  <span class="kivi-item-body__structure">
                    <?php echo ucfirst( get_post_meta( get_the_id(), '_realtytype_id', true ) ) ?>
                    <?php if (get_post_meta( get_the_id(), '_flat_structure', true ) ) {
                      echo "<span aria-hidden='true'> | </span>" . get_post_meta(get_the_id(), '_flat_structure', true ); }
                    ?>
                  </span>

                  <h2>
                    <?php echo ucfirst( get_post_meta( get_the_id(), '_street', true ) ) . ", " . ucfirst( get_post_meta(get_the_id(), '_quarteroftown', true ) ) . ", " .  get_post_meta( get_the_id(), '_town', true )?>
                  </h2>
                  <div class="kivi-item-details">
                    <div class="div">
                      <p>
                      <?php ( Kivi_Public::is_for_rent_assignment(get_the_id()) ) ? _e('Vuokra', 'Kivi') :  _e('Hinta', 'Kivi'); ?>
                      <br>
                        <?php echo ( $price = Kivi_Public::get_display_price(get_the_id()) ) ? $price : '-'; ?>
                      </p>
                    </div>
                    <div class="div">
                      <p><?php _e('Koko', 'Kivi')?><br>
                        <?php
                          if ( get_post_meta(get_the_id(), '_living_area_m2', true) != "" ) {
                            echo intval(get_post_meta(get_the_id(), '_living_area_m2', true)).' m²';
                          }
                          else {
                            echo '-';
                          } ?>
                      </p>
                    </div>
                    <div class="div">
                      <p><?php _e('Vuosi', 'Kivi')?><br>
                        <?php
                          if ( get_post_meta(get_the_id(), '_rc_buildyear2', true) != "" ) {
                            echo get_post_meta(get_the_id(), '_rc_buildyear2', true);
                          }
                          else {
                            echo '-';
                          } ?>
                      </p>
                    </div>
                  </div>
                </div>
              </a>
            </div>
			    <?php endwhile; ?></div>
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
    </main><!-- #main -->
  </div><!-- #primary -->

<?php
get_sidebar();
get_footer(); ?>
