<?php

/**
 * Provide a public-facing view for the plugin
 *
 * Privides a template for a simple single item in listing (shortcode or index).
 * Uses helper functions from kivi-functions.php (get*) and _ui_section_SUMMARY -data from rest api uidata -endpoint.
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/public/partials
 */
$view    = array();
$section = get_post_meta( get_the_ID(), '_ui_section_SUMMARY', true );

$view['PRICE']          = $section['fields']['UNENCUMBERED_PRICE']['value'];
$view['AREA_M2']        = $section['fields']['AREA_M2']['value'];
$view['BUILD_YEAR']     = $section['fields']['BUILD_YEAR']['value'];
$view['TYPE']           = $section['fields']['TYPE']['value'];
$view['FLAT_STRUCTURE'] = get_post_meta( get_the_ID(), '_flat_structure', true );
$view['LOCATION']       = $section['fields']['LOCATION']['value'];
$view['IMAGE_URL']          = Kivi_Public::get_primary_image_url( get_the_ID() );


?>
<div class="kivi-index-item col <?php echo Kivi_Public::get_css_classes( get_the_ID() ); ?>">
    <a href="<?php the_permalink(); ?>" class="kivi-item-link">
        <div class="kivi-item-image">
	        <?php if ( ! empty( $view['IMAGE_URL']  ) ) : ?>
                <img src="<?php echo $view['IMAGE_URL'] ; ?>" alt="" loading="lazy"/>
	        <?php endif; ?>
        </div>
        <div class="kivi-item-body">
            <span class="kivi-item-body__structure limit-2">
                <?php echo esc_html( $view['TYPE'] ); ?>
	            <?php if ( ! empty( $view['FLAT_STRUCTURE'] ) ) : ?>
                    <span aria-hidden='true'> | </span> <?php echo esc_html( $view['FLAT_STRUCTURE'] ); ?>
	            <?php endif; ?>
            </span>

            <h2 class="limit-2"><?php echo esc_html( $view['LOCATION'] ); ?></h2>

            <div class="kivi-item-details">
                <div class="div">
                    <p class="kivi-item-details__price"
                       title="<?php ( Kivi_Public::is_for_rent_assignment( get_the_ID() ) ) ? _e( 'Vuokra',
					     'Kivi' ) : _e( 'Hinta', 'Kivi' ); ?>">
						<?php echo esc_html( $view['PRICE'] ); ?>
                    </p>
                </div>
                <div class="div">
                    <p class="kivi-item-details__area" title="<?php _e( 'Koko', 'Kivi' ) ?>">
						<?php echo esc_html( $view['AREA_M2'] ); ?>
                    </p>
                </div>
                <div class="div">
                    <p class="kivi-item-details__buildyear" title="<?php _e( 'Vuosi', 'Kivi' ) ?>">
						<?php echo esc_html( $view['BUILD_YEAR'] ); ?>
                    </p>
                </div>
            </div>
        </div>
    </a>
</div>
