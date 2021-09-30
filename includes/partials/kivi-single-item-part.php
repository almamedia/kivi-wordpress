<?php

/**
 * Provide a public-facing view for the plugin
 *
 * Privides a template for a simple single item in listing (shortcode or index).
 * Uses helper functions from kivi-functions.php (view_*_info() et al.)
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/public/partials
 */
$view = array();
$section = get_post_meta( get_the_ID(), '_ui_section_SUMMARY', true);

$view['PRICE']          = $section['fields']['PRICE']['value'];
$view['LIVING_AREA_M2'] = $section['fields']['LIVING_AREA_M2']['value'];
$view['BUILD_YEAR']     = $section['fields']['BUILD_YEAR']['value'];
$view['TYPE']           = $section['fields']['TYPE']['value'];
$view['FLAT_STRUCTURE'] = get_post_meta(get_the_ID(), '_flat_structure', true );
$view['LOCATION']       = $section['fields']['LOCATION']['value'];


?>
<div class="kivi-index-item col <?php echo Kivi_Public::get_css_classes(get_the_id()); ?>">
  <a href="<?php the_permalink(); ?>" class="kivi-item-link">
    <div class="kivi-item-image">
        <img src="<?php echo esc_attr( Kivi_Public::get_primary_image_url( get_the_ID() ) ); ?>" class="attachment-medium_large size-medium_large wp-post-image" alt="" loading="lazy" />
    </div>
    <div class="kivi-item-body">
      <span class="kivi-item-body__structure limit-2">
        <?php echo esc_html( $view['TYPE'] ); ?>
        <?php if ( ! empty($view['FLAT_STRUCTURE']) ) {
          echo "<span aria-hidden='true'> | </span>" . esc_html( $view['FLAT_STRUCTURE'] ); }
        ?>
      </span>

      <h2 class="limit-2">
        <?php echo esc_html( $view['LOCATION'] ); ?>
      </h2>
      <div class="kivi-item-details">
        <div class="div">
          <p class="kivi-item-details__price" title="<?php ( Kivi_Public::is_for_rent_assignment(get_the_id()) ) ? _e('Vuokra', 'Kivi') :  _e('Hinta', 'Kivi'); ?>">
            <?php echo esc_html( $view['PRICE'] ); ?>
          </p>
        </div>
        <div class="div">
          <p class="kivi-item-details__area" title="<?php _e('Koko', 'Kivi')?>">
            <?php echo esc_html( $view['LIVING_AREA_M2'] ); ?>
          </p>
        </div>
        <div class="div">
          <p class="kivi-item-details__buildyear" title="<?php _e('Vuosi', 'Kivi')?>">
            <?php echo esc_html( $view['BUILD_YEAR'] ); ?>
          </p>
        </div>
      </div>
    </div>
  </a>
</div>
