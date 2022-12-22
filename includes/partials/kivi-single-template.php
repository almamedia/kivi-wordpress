<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 * Privides a template for a simple single item page.
 * Uses helper functions from kivi-functions.php (view_*_info() et al.)
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/public/partials
 */
get_header();
if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<div id="primary" class="content-area">
    <main id="main"
          class="site-main kivi-template-single <?php echo Kivi_Public::get_css_classes( get_the_ID() ); ?>"
          role="main">

        <?php
        $images = get_post_meta( get_the_ID(), '_ui_IMAGES', true );
        if ( $images ) : ?>
            <div class="kivi-img-container">
                <div class="slick-for">
                    <?php foreach ( $images as $image ) : ?>
                        <div class="slick-for-image-wrapper">
                            <?php echo Kivi_Public::get_img_tag(
                                $image['V1066x'], $image['DESCRIPTION'], 'slick-for-image' ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="slick-carousel">
                    <?php foreach ( $images as $image ) : ?>
                        <div class="slick-carousel-image-wrapper">
                            <?php echo Kivi_Public::get_img_tag(
                                $image['V150x'], $image['DESCRIPTION'], 'slick-carousel-image' ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>


        <div class="kivi-single-item-header">
            <?php
            $header_data = get_post_meta( get_the_ID(), '_ui_section_SUMMARY', true );
			if( isset( $header_data['fields']['AREA_M2']['value'] ) ) { // fix api response value null for plot area
				$header_data['fields']['AREA_M2']['value'] = str_replace("null / ", '', $header_data['fields']['AREA_M2']['value']);
			}
            ?>
            <p class="kivi-single-item-structure">
                <?= $header_data['fields']['TYPE']['value'] ?? '' ?>
                <span aria-hidden='true'> | </span>
                <?php echo get_post_meta( get_the_ID(), '_flat_structure', true ); ?>
            </p>
            <h1 class="kivi-single-item-title">
                <?= $header_data['fields']['LOCATION']['value'] ?? '' ?>
            </h1>
            <div class="kivi-item-details">
                <div class="div">
                    <p class="kivi-item-details__price">
                    <span class="kivi-item-details__heading">
                        <?= $header_data['fields']['RENDERED_PRICE']['label'] ?? '' ?>
                    </span>
                        <br>
                        <?= $header_data['fields']['RENDERED_PRICE']['value'] ?? '' ?>
                    </p>
                </div>
                <div class="div">
                    <p class="kivi-item-details__area">
                    <span class="kivi-item-details__heading">
                        <?= $header_data['fields']['AREA_M2']['label'] ?? '' ?>
                    </span>
                        <br>
                        <?= $header_data['fields']['AREA_M2']['value'] ?? '' ?>
                    </p>
                </div>
                <div class="div">
                    <p class="kivi-item-details__buildyear">
                    <span class="kivi-item-details__heading">
                        <?= $header_data['fields']['BUILD_YEAR']['label'] ?? '' ?>
                    </span>
                        <br>
                        <?= $header_data['fields']['BUILD_YEAR']['value'] ?? '' ?>
                    </p>
                </div>
            </div>
            <div class="presentation-text">
	            <?= wpautop( $header_data['fields']['PRESENTATION']['value']  ?? '' ) ?>
            </div>
	        <?php do_action( "kivi_single_presentation_text_after" ); ?>
        </div>


        <div class="kivi-single-item-infowrapper">

            <?php
            $sections = array(
                //"_ui_section_SUMMARY"   =>  'hide-by-default',
                "_ui_section_BASICS"                 => 'show-by-default',
                "_ui_section_CHARGES"                => 'show-by-default',
                "_ui_section_LIVING_COSTS"           => 'show-by-default',
                "_ui_section_ADDITIONAL_DETAILS"     => 'hide-by-default',
                "_ui_section_PREMISES_AND_MATERIALS" => 'hide-by-default',
                "_ui_section_LOT"                    => 'hide-by-default',
                "_ui_section_REALTY_COMPANY"         => 'hide-by-default',
                "_ui_section_PRESENTATION"           => 'hide-by-default',
                "_ui_section_AGENT"                  => 'show-by-default',
                "_ui_section_COMPANY"                => 'hide-by-default',
            );

            foreach ( $sections as $section_identifier => $header_class ):
                $section = get_post_meta( get_the_ID(), $section_identifier, true );
                if ( empty( $section ) ) {
                    continue;
                }
                ?>
                <section id="section-<?= esc_attr( $section_identifier ) ?>"
                         class="kivi-single-item-body kivi-single-section">
                    <div class="kivi-header-wrapper">
                        <h3 class="kivi-single-item-body-header">
                            <button class="kivi-toggle <?= esc_attr( $header_class ) ?>"
                                    data-target="section-content-<?= esc_attr( $section_identifier ) ?>">
                                <?= esc_html( $section['header'] ) ?>
                            </button>
                        </h3>
                    </div>
                    <div id="section-content-<?= esc_attr( $section_identifier ) ?>">
                        <?php echo "<!-- action  kivi_single{$section_identifier} -->"; ?>
                        <?php do_action( "kivi_single{$section_identifier}" ); ?>
                        <table class="kivi-item-table">
                            <tbody>
                            <?php foreach ( $section['fields'] as $field_key => $info_row ): ?>
                                <tr class="info-row-<?= esc_attr( $field_key ) ?>">
                                    <th class='kivi-item-cell kivi-item-cell-header info-label-<?= esc_attr( $field_key ) ?>'>
                                        <?= esc_html( $info_row['label'] ) ?>
                                    </th>
                                    <td class='kivi-item-cell kivi-item-cell-value info-value-<?= esc_attr( $field_key ) ?>'>
                                        <?php if ( is_array( $info_row['value'] ) ): ?>
                                            <ul class="info-value-list">
                                                <?php foreach ( $info_row['value'] as $value ): ?>
                                                    <li><?= esc_html( $value ) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <?= esc_html( $info_row['value'] ) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

	                    <?php
						$sub_sections = array();
						if( isset( $section['sections'] ) ){
							$sub_sections =  (array) $section['sections'];
						}
						?>
	                    <?php foreach( $sub_sections as $sub_section ): ?>
                            <table class="kivi-item-table">
                                <thead>
                                <tr>
                                    <th colspan="2"><?= esc_html( $sub_section['header'] ) ?></th>
                                </tr>
                                </thead>
                                <tbody>
			                    <?php foreach ( $sub_section['fields'] as $field_key => $info_row ): ?>
                                    <tr class="info-row-<?= esc_attr( $field_key ) ?>">
                                        <th class='kivi-item-cell kivi-item-cell-header info-label-<?= esc_attr( $field_key ) ?>'>
						                    <?= esc_html( $info_row['label'] ) ?>
                                        </th>
                                        <td class='kivi-item-cell kivi-item-cell-value info-value-<?= esc_attr( $field_key ) ?>'>
						                    <?php if ( is_array( $info_row['value'] ) ): ?>
                                                <ul class="info-value-list">
								                    <?php foreach ( $info_row['value'] as $value ): ?>
                                                        <li><?= esc_html( $value ) ?></li>
								                    <?php endforeach; ?>
                                                </ul>
						                    <?php else: ?>
							                    <?= esc_html( $info_row['value'] ) ?>
						                    <?php endif; ?>
                                        </td>
                                    </tr>
			                    <?php endforeach; ?>
                                </tbody>
                            </table>
	                    <?php endforeach; ?>



                        <?php echo "<!-- action kivi_single{$section_identifier}_after -->"; ?>
                        <?php do_action( "kivi_single{$section_identifier}_after" ); ?>
                    </div>
                </section>
            <?php endforeach; ?>


            <?php if ( get_kivi_option( 'kivi-gmap-id' ) ) : ?>
                <section class="kivi-single-item-body kivi-single-gmap">

                    <div id="map"></div>
                    <script type="text/javascript">
                        <?php
                        $lat = get_post_meta( get_the_ID(), "_ui_LOCATION_LAT", true );
                        $lon = get_post_meta( get_the_ID(), "_ui_LOCATION_LON", true );
                        if ( ! empty( $lat ) && ! empty( $lon ) ):
                            echo "var hasCoordinates = true;";
                            echo 'var latlng = {lat:' . esc_js( $lat ) . ', lng: ' . esc_js( $lon ) . '};';
                        else:
                            echo "var hasCoordinates = false;";
                        endif;
                        ?>
                        function initMap() {

                            var geocoder = new google.maps.Geocoder();

                            // Specify features and elements to define styles.
                            var styleArray = [
                                {
                                    featureType: "all",
                                    stylers: [
                                        {saturation: -60}
                                    ]
                                }, {
                                    featureType: "road.arterial",
                                    elementType: "geometry",
                                    stylers: [
                                        {hue: "#00ffee"},
                                        {saturation: 20}
                                    ]
                                }, {
                                    featureType: "poi.business",
                                    elementType: "labels",
                                    stylers: [
                                        {visibility: "off"}
                                    ]
                                }
                            ];


                            var mapOptions = {
                                zoom: 15,
                                scrollwheel: false,
                                draggable: false,
                                draggableCursor: 'default',
                                // Apply the map style array to the map.
                                styles: styleArray,
                            }
                            var map = new google.maps.Map(document.getElementById("map"), mapOptions);
                            <?php echo 'var address = "' . esc_js( get_post_meta( get_the_id(), "_street",
                                true ) ) . ', ' . esc_js( get_post_meta( get_the_id(), "_town", true ) ) . '";';  ?>

                            // Set the marker on the map for the first time
                            setMarker(geocoder, map, address);

                            // Listen for window resize and draw the marker again
                            google.maps.event.addDomListener(window, 'resize', function () {
                                setMarker(geocoder, map, address);
                            });
                        }

                        function setMarker(geocoder, map, address) {
                            if (hasCoordinates) {
                                map.setCenter(latlng);
                                var marker = new google.maps.Marker({
                                    map: map,
                                    position: latlng,
                                });
                                marker.setMap(map);
                            } else {
                                geocoder.geocode({'address': address}, function (results, status) {
                                    if (status == google.maps.GeocoderStatus.OK) {
                                        map.setCenter(results[0].geometry.location);
                                        var marker = new google.maps.Marker({
                                            map: map,
                                            position: results[0].geometry.location
                                        });
                                    } else {
                                        console.log('Geocode error');
                                    }
                                });
                            }
                        }
                    </script>
                    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr( get_kivi_option( 'kivi-gmap-id' ) ); ?>&callback=initMap"
                            async defer></script>
                </section>
            <?php endif; ?>
        </div>

        <section class="kivi-single-item-body kivi-item-after">
            <?php do_action( 'kivi_single_item_after' ); ?>
            <?php echo Kivi_Public::get_listing_button(); ?>
        </section>

    </main><!-- #main -->
</div><!-- #primary -->

<?php endwhile; else : ?>
    <p><?php esc_html_e( 'Sorry, no posts matched your criteria.' ); ?></p>
<?php endif; ?>

<?php get_footer();
