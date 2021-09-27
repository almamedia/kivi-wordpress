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
get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main"
              class="site-main kivi-template-single <?php echo Kivi_Public::get_css_classes( get_the_ID() ); ?>"
              role="main">

			<?php
			if ( have_posts() ) :
				the_post();

				$images = get_post_meta( get_the_ID(), '_ui_IMAGES', true );
				if ( $images ) : ?>
                    <div class="kivi-img-container">
                        <div class="slick-for">
							<?php foreach ( $images as $image ) : ?>
                                <div class="slick-for-image-wrapper">
									<?php
									echo Kivi_Public::get_img_tag( $image['V1066x'], $image['DESCRIPTION'], 'slick-for-image',
										'980x700,fit,q82' );
									?>
                                </div>
							<?php endforeach; ?>
                        </div>
                        <div class="slick-carousel">
							<?php foreach ( $images as $image ) : ?>
                                <div class="slick-carousel-image-wrapper">
									<?php
									echo Kivi_Public::get_img_tag( $image['V150x'], $image['DESCRIPTION'], 'slick-carousel-image');
									?>
                                </div>
							<?php endforeach; ?>
                        </div>
                    </div>
				<?php endif; ?>


                <div class="kivi-single-item-header">
                    <?php
                    $header_data = get_post_meta(get_the_ID(), '_ui_section_SUMMARY', true);
                    ?>
                    <p class="kivi-single-item-structure">
                        <?= $header_data['fields']['TYPE']['value'] ?>
                        <span aria-hidden='true'> | </span>
                        <?php echo get_post_meta( get_the_id(), '_flat_structure', true ) ?>
                    </p>
                    <h1 class="kivi-single-item-title">
	                    <?= $header_data['fields']['LOCATION']['value'] ?>
                    </h1>
                    <div class="kivi-item-details">
                        <div class="div">
                            <p class="kivi-item-details__price">
                                <span class="kivi-item-details__heading"><?= $header_data['fields']['PRICE']['label'] ?></span>
                                <br>
	                            <?= $header_data['fields']['PRICE']['value'] ?>
                            </p>
                        </div>
                        <div class="div">
                            <p class="kivi-item-details__area">
                                <span class="kivi-item-details__heading"><?= $header_data['fields']['LIVING_AREA_M2']['label'] ?></span>
                                <br>
	                            <?= $header_data['fields']['LIVING_AREA_M2']['value'] ?>
                            </p>
                        </div>
                        <div class="div">
                            <p class="kivi-item-details__buildyear">
                                <span class="kivi-item-details__heading"><?= $header_data['fields']['BUILD_YEAR']['label'] ?></span>
                                <br>
	                            <?= $header_data['fields']['BUILD_YEAR']['value'] ?>
                            </p>
                        </div>
                    </div>
                </div>


                <div class="kivi-single-item-infowrapper">

					<?php
                    $sections = array(
                        //"_ui_section_SUMMARY"   =>  'hide-by-default',
                        "_ui_section_BASICS"    =>  'show-by-default',
                        "_ui_section_CHARGES"   =>  'show-by-default',
                        "_ui_section_LIVING_COSTS" => 'show-by-default',
                        "_ui_section_ADDITIONAL_DETAILS" => 'hide-by-default',
                        "_ui_section_PREMISES_AND_MATERIALS" => 'hide-by-default',
                        "_ui_section_LOT"       =>  'hide-by-default',
                        "_ui_section_REALTY_COMPANY" => 'hide-by-default',
                    );

					foreach ( $sections as $section_identifier => $header_class ):
					    $section = get_post_meta(get_the_ID(), $section_identifier, true);
					    if(empty($section)){
					        break;
                        }
						?>
                        <section id="section-<?= $section_identifier ?>"
                                 class="kivi-single-item-body kivi-single-section">
                            <div class="kivi-header-wrapper">
                                <h3 class="kivi-single-item-body-header">
                                    <button class="kivi-toggle <?= $header_class ?>"
                                            data-target="table-<?= $section_identifier ?>"><?= $section['header'] ?></button>
                                </h3>
                            </div>
                            <table id="table-<?= $section_identifier ?>" class="kivi-item-table">
                                <tbody>
								<?php foreach ( $section['fields'] as $info_row ): ?>
                                    <tr>
                                        <th class='kivi-item-cell kivi-item-cell-header'><?= $info_row['label'] ?></th>
                                        <td class='kivi-item-cell kivi-item-cell-value'>
											<?php if ( is_array( $info_row['value'] ) ): ?>
                                                <ul class="kivi">
													<?php foreach ( $info_row['value'] as $key => $value ): ?>
                                                        <li><?= $value ?></li>
													<?php endforeach; ?>
                                                </ul>
											<?php else: ?>
												<?= $info_row['value'] ?>
											<?php endif; ?>
                                        </td>
                                    </tr>
								<?php endforeach; ?>
                                </tbody>
                            </table>
                        </section>
					<?php endforeach; ?>


					<?php if ( get_kivi_option( 'kivi-gmap-id' ) ) { ?>
                        <section class="kivi-single-item-body kivi-single-gmap">

                            <div id="map"></div>
                            <script type="text/javascript">
								<?php
								if ( get_post_meta( $post->ID, "_lat", true ) && get_post_meta( $post->ID, "_lon",
										true ) ):
									echo "var hasCoordinates = true;";
									echo 'var latlng = {lat:' . get_post_meta( $post->ID, "_lat",
											true ) . ', lng: ' . get_post_meta( $post->ID, "_lon", true ) . '};';
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
									<?php echo 'var address = "' . get_post_meta( get_the_id(), "_street",
										true ) . ', ' . get_post_meta( get_the_id(), "_town", true ) . '";';  ?>

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
                            <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_kivi_option( 'kivi-gmap-id' ); ?>&callback=initMap"
                                    async defer></script>
                        </section>
					<?php } ?>
                </div>

			<?php endif; ?>

            <section class="kivi-single-item-body kivi-item-after">
				<?php do_action( 'kivi_single_item_after' ); ?>
				<?php echo Kivi_Public::get_listing_button(); ?>
            </section>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php get_footer();
