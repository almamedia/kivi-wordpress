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
    <main id="main" class="site-main kivi-template-single" role="main">
      <div class="kivi-single-item-header">
        <div id="wrapper">
          <h1 class="kivi-single-item-title">
            <?php
              the_title();
              if ( get_post_meta($post->ID, '_living_area_m2', true) != "" ) {
                echo ', '.number_format(intval(get_post_meta($post->ID, '_living_area_m2', true)), 0, ".", " ").'m2';
              }
              if ( get_post_meta($post->ID, '_unencumbered_price', true) != "" ) {
                echo ', <span style=white-space:nowrap;>'.number_format(intval(get_post_meta($post->ID, '_unencumbered_price', true)), 0, ".", " ").' €</span>';
              }
            ?>
          </h1>
          <p>
            <?php
              if ( get_post_meta($post->ID, '_realtytype_id', true) != "" ) {
                echo ucfirst(get_post_meta($post->ID, '_realtytype_id', true));
              }
              if ( get_post_meta($post->ID, '_street', true) != "" ) {
                echo ', '.get_post_meta($post->ID, '_street', true);
              }
              if ( get_post_meta($post->ID, '_postcode', true) != "" ) {
                echo ', '.get_post_meta($post->ID, '_postcode', true);
              }
              if ( get_post_meta($post->ID, '_addr_town_area', true) != "" ) {
                echo ', '.get_post_meta($post->ID, '_addr_town_area', true);
              }
            ?>
          </p>
        </div>
      </div>
      <?php
      if ( have_posts() ) : the_post();

        $args = array(
          'post_parent' => get_the_ID(),
          'post_mime_type' => 'image',
          'post_type' => 'attachment',
          'orderby' => 'meta_value_num',
          'meta_key' => 'image_order',
          'order' => 'ASC'
        );

        $attachments = get_children( $args );
        if ( $attachments ) {
          ?>
          <div class="slick-for">
            <?php
            foreach ( $attachments as $attachment ) : ?>
              <div class="slick-for-image-wrapper">
                <?php
                  $image_attrs = wp_get_attachment_image_src( $attachment->ID, 'large' );
                  echo '<img src="' . $image_attrs[0] . '" class="slick-for-image current" alt="item image">';
                ?>
              </div><?php
            endforeach;
            ?>
          </div>
          <div class="slick-carousel">
              <?php
              foreach ( $attachments as $attachment ) {
                ?>
                <div class="slick-carousel-image-wrapper">
                <?php
                  echo '<img src="' . wp_get_attachment_thumb_url( $attachment->ID ) . '" class="slick-carousel-image current" alt="item image">';
                ?>
                </div><?php
              } ?>
          </div><?php
        }

        $brand_styling = ' style="background-color:'.get_option("kivi-brand-color").';"';
      ?>

      <div class="kivi-single-item-infowrapper">
      <?php
      if(trim(get_the_content()) != "") {
      ?>
        <section class="kivi-single-item-body">
          <div class="wrapper">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>><?php _e('Kuvaus', 'kivi'); ?></h3>
            </div>
            <?php the_content(); ?>
          </div>
        </section>
        <?php
        }
        ?>

        <section class="kivi-single-item-body">
          <div class="wrapper">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>><?php _e('Yhteystiedot ja esittelyt', 'kivi'); ?></h3>
            </div>
            <table class="kivi-item-table">
              <tbody>
                <?php view_contact_info( $post->ID);?>
              </tbody>
            </table>
          </div>
        </section>

        <section class="kivi-single-item-body">
          <div class="wrapper">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>><?php _e('Asunnon perustiedot', 'kivi'); ?></h3>
            </div>
            <table class="kivi-item-table">
              <tbody>
                <?php
                  view_basic_info( $post->ID);
                ?>
              </tbody>
            </table>
          </div>
        </section>

        <section class="kivi-single-item-body">
          <div class="wrapper">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>><?php _e('Hinta ja kustannukset', 'kivi'); ?></h3>
            </div>
            <table class="kivi-item-table">
              <tbody>
                <?php
                  view_cost_info( $post->ID);
                ?>
              </tbody>
            </table>
          </div>
        </section>

        <section class="kivi-single-item-body">
          <div class="wrapper">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>><?php _e('Asunnon lisätiedot', 'kivi'); ?></h3>
            </div>
            <table class="kivi-item-table">
              <tbody>
                <?php
                  view_additional_info( $post->ID);
                ?>
              </tbody>
            </table>
          </div>
        </section>

        <section class="kivi-single-item-body">
          <div class="wrapper">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>><?php _e('Asunnon tilat ja materiaalit', 'kivi'); ?></h3>
            </div>
            <table class="kivi-item-table">
              <tbody>
                <?php
                  view_materials_info( $post->ID);
                ?>
              </tbody>
            </table>
          </div>
        </section>

        <section class="kivi-single-item-body">
          <div class="wrapper">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>><?php _e('Taloyhtiö', 'kivi'); ?></h3>
            </div>
            <table class="kivi-item-table">
              <tbody>
                <?php
                  view_housing_company_info( $post->ID);
                ?>
              </tbody>
            </table>
          </div>
        </section>

        <?php if( get_kivi_option('kivi-gmap-id') ){ ?>
        <section class="kivi-single-item-body">
          <div class="wrapper">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>><?php _e('Kartta kaikki tiedot', 'kivi'); ?></h3>
            </div>
              <div id="map"></div>
          </div>
          <script type="text/javascript">

            function initMap() {

              var geocoder = new google.maps.Geocoder();

              // Specify features and elements to define styles.
              var styleArray = [
                {
                  featureType: "all",
                  stylers: [
                   { saturation: -60 }
                  ]
                },{
                  featureType: "road.arterial",
                  elementType: "geometry",
                  stylers: [
                    { hue: "#00ffee" },
                    { saturation: 20 }
                  ]
                },{
                  featureType: "poi.business",
                  elementType: "labels",
                  stylers: [
                    { visibility: "off" }
                  ]
                }
              ];


              var mapOptions = {
                zoom: 8,
                center: {lat: -34.397, lng: 150.644},
                scrollwheel: false,
                // Apply the map style array to the map.
                styles: styleArray,
                zoom: 8
              }
              var map = new google.maps.Map(document.getElementById("map"), mapOptions);
              <?php echo 'var address = "' . get_post_meta($post->ID, "_street", true) . ', '. get_post_meta($post->ID, "_town", true) . '";';  ?>

              // Set the marker on the map for the first time
              setMarker(geocoder, map, address);

              // Listen for window resize and draw the marker again
              google.maps.event.addDomListener(window, 'resize', function() {
                setMarker(geocoder, map, address);
              });
            }

            function setMarker(geocoder, map, address) {
              geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                  map.setCenter(results[0].geometry.location);
                  var marker = new google.maps.Marker({
                      map: map,
                      position: results[0].geometry.location
                  });
                } else {
                  console.log('Ceocode error: too many requests at once');
                }
              });
            }
          </script>
          <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_kivi_option('kivi-gmap-id'); ?>&callback=initMap" async defer></script>
        </section>
        <?php } ?>
      </div>

      <?php endif; ?>

    </main><!-- #main -->
  </div><!-- #primary -->

<?php
if(get_kivi_option('kivi-show-sidebar')){
  get_sidebar();
}

get_footer(); ?>
