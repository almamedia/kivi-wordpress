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
          <p class="kivi-item-details__price">
          <?php ( Kivi_Public::is_for_rent_assignment(get_the_id()) ) ? _e('Vuokra', 'Kivi') :  _e('Hinta', 'Kivi'); ?>
          <br>
            <?php echo ( $price = Kivi_Public::get_display_price(get_the_id()) ) ? $price : '-'; ?>
          </p>
        </div>
        <div class="div">
          <p class="kivi-item-details__area"><?php _e('Koko', 'Kivi')?><br>
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
          <p class="kivi-item-details__buildyear"><?php _e('Vuosi', 'Kivi')?><br>
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
