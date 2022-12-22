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

$section = get_post_meta( get_the_ID(), '_ui_section_SUMMARY', true );

$section_fields = array();
if( isset( $section['fields'] ) && is_array( $section['fields'] ) ) {
	$section_fields = $section['fields'];
}

$view    = array();
$view['PRICE'] = $view['AREA_M2'] = $view['BUILD_YEAR'] = $view['TYPE'] = '';
$view['FLAT_STRUCTURE'] = $view['LOCATION'] = $view['IMAGE_URL'] = '';

if( isset( $section_fields['RENDERED_PRICE'] ) ) {
	$view['PRICE'] = $section_fields['RENDERED_PRICE']['value'];
}

if( isset( $section_fields['AREA_M2'] ) ) {
	$view['AREA_M2'] = $section_fields['AREA_M2']['value'];

	// fix api response value null for plot area
	$view['AREA_M2'] = str_replace( "null / ", '', $view['AREA_M2'] );
}

if( isset( $section_fields['BUILD_YEAR'] ) ) {
	$view['BUILD_YEAR'] = $section_fields['BUILD_YEAR']['value'];
}

if( isset( $section_fields['TYPE'] ) ) {
	$view['TYPE'] = $section_fields['TYPE']['value'];
}

if( isset( $section_fields['LOCATION'] ) ) {
	$view['LOCATION'] = $section_fields['LOCATION']['value'];
}

$view['FLAT_STRUCTURE'] = get_post_meta( get_the_ID(), '_flat_structure', true );
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
