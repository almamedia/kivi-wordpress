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
?>
<div class="kivi-index-item col <?php echo Kivi_Public::get_css_classes(get_the_id()); ?>">
  <a href="<?php the_permalink(); ?>" class="kivi-item-link">
    <div class="kivi-item-image">
        <img src="<?php echo esc_attr( Kivi_Public::get_primary_image_url( get_the_ID() ) ); ?>" class="attachment-medium_large size-medium_large wp-post-image" alt="" loading="lazy" />
    </div>
    <div class="kivi-item-body">
      <span class="kivi-item-body__structure limit-2">
        <?php echo ucfirst( get_post_meta( get_the_id(), '_realtytype_id', true ) ) ?>
        <?php if (get_post_meta( get_the_id(), '_flat_structure', true ) ) {
          echo "<span aria-hidden='true'> | </span>" . get_post_meta(get_the_id(), '_flat_structure', true ); }
        ?>
      </span>

      <h2 class="limit-2">
        <?php echo ucfirst( get_post_meta( get_the_id(), '_street', true ) ) . ", " . ucfirst( get_post_meta(get_the_id(), '_quarteroftown', true ) ) . ", " .  get_post_meta( get_the_id(), '_town', true )?>
      </h2>
      <div class="kivi-item-details">
        <div class="div">
          <p class="kivi-item-details__price" title="<?php ( Kivi_Public::is_for_rent_assignment(get_the_id()) ) ? _e('Vuokra', 'Kivi') :  _e('Hinta', 'Kivi'); ?>">
          <!--<?php ( Kivi_Public::is_for_rent_assignment(get_the_id()) ) ? _e('Vuokra', 'Kivi') :  _e('Hinta', 'Kivi'); ?>
          <br>-->
            <?php echo ( $price = Kivi_Public::get_display_price(get_the_id()) ) ? $price : '-'; ?>
          </p>
        </div>
        <div class="div">
          <p class="kivi-item-details__area" title="<?php _e('Koko', 'Kivi')?>">
            <?php
              if ( get_post_meta(get_the_id(), '_living_area_m2', true) != "" ) {
                echo intval(get_post_meta(get_the_id(), '_living_area_m2', true)).' m²';
              } ?>
          </p>
        </div>
        <div class="div">
          <p class="kivi-item-details__buildyear" title="<?php _e('Vuosi', 'Kivi')?>">
            <?php
              if ( get_post_meta(get_the_id(), '_rc_buildyear2', true) != "" ) {
                echo get_post_meta(get_the_id(), '_rc_buildyear2', true);
              } ?>
          </p>
        </div>
      </div>
    </div>
  </a>
</div>
