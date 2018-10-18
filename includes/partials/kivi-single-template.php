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
    <main id="main" class="site-main kivi-template-single <?php echo Kivi_Public::get_css_classes( get_the_id() ); ?>" role="main">
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
          <div class="kivi-img-container">
            <div class="slick-for">
              <?php
              foreach ( $attachments as $attachment ) : ?>
                <div class="slick-for-image-wrapper">
                  <?php
                    echo wp_get_attachment_image( $attachment->ID, $size = array("1200", "800"), false, array( "class" => "slick-for-image", "alt" => "" ) );
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
                    echo wp_get_attachment_image( $attachment->ID, $size = array("150", "100"), false, array( "class" => "slick-carousel-image", "alt" => "" ) );
                  ?>

                  </div><?php
                } ?>
            </div>
          </div><?php
        }

        $brand_styling = ' style="background-color:'.get_option("kivi-brand-color").';"';
      ?>
          <div class="kivi-single-item-header">
      <p class="kivi-single-item-structure"> <?php echo ucfirst( get_post_meta( get_the_id(), '_realtytype_id', true ) ) ?>  <span aria-hidden='true'> | </span> <?php echo  get_post_meta( get_the_id(), '_flat_structure', true )  ?> </p>
      <h1 class="kivi-single-item-title">
        <?php
          echo ucfirst( get_post_meta( get_the_id(), '_street', true ) ) . ", " . ucfirst( get_post_meta( get_the_id(), '_quarteroftown', true ) ) . ", " .  get_post_meta( get_the_id(), '_town', true );
        ?>
      </h1>
      <div class="kivi-item-details">
        <div class="div">
          <p><?php ( Kivi_Public::is_for_rent_assignment(get_the_id()) ) ? _e('Vuokra', 'Kivi') :  _e('Hinta', 'Kivi'); ?>
            <br>
            <?php echo ( $price = Kivi_Public::get_display_price(get_the_id()) ) ? $price : '-'; ?>
          </p>
        </div>
        <div class="div">
          <p><?php _e('Koko', 'Kivi')?><br>
            <?php
              if ( get_post_meta($post->ID, '_living_area_m2', true) != "" ) {
                echo get_post_meta($post->ID, '_living_area_m2', true).' m²';
              }
              else {
                echo '-';
              } ?>
          </p>
        </div>
        <div class="div">
          <p><?php _e('Vuosi', 'Kivi')?><br>
            <?php
              if ( get_post_meta($post->ID, '_rc_buildyear2', true) != "" ) {
                echo get_post_meta($post->ID, '_rc_buildyear2', true);
              }
              else {
                echo '-';
              } ?>
          </p>
        </div>
      </div>
    </div>
      <div class="kivi-single-item-infowrapper">
      <?php
      if(trim(get_the_content()) != "") {
      ?>
        <section class="kivi-single-item-body kivi-single-the-content">
            <?php the_content(); ?>
        </section>
        <?php
        }
        ?>

        <?php if(view_contact_info( $post->ID)): ?>
        <section class="kivi-single-item-body kivi-single-contact-info">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>>
                <button class="kivi-toggle" data-target="yhteystiedot"><?php _e('Yhteystiedot ja esittelyt', 'kivi'); ?></button>
              </h3>
            </div>
            <table id="yhteystiedot" class="kivi-item-table">
              <tbody>
                <?php echo view_contact_info( $post->ID);?>
                <tr class="kivi-iv-person">
                  <th>
                    <div class="kivi-iv-person-container">
                      <img class="kivi-iv-person-image" src="<?php echo get_iv_person_image($post->ID); ?>" alt="välittäjä" />
                    </div>
                  </th>
                </tr>
              </tbody>
            </table>
        </section>
        <?php endif; ?>

        <section class="kivi-single-item-body kivi-single-basic-info">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>>
                <button class="kivi-toggle" data-target="perustiedot"><?php _e('Asunnon perustiedot', 'kivi'); ?></button>
              </h3>
            </div>
            <table id="perustiedot" class="kivi-item-table">
              <tbody>
                <?php echo view_basic_info( $post->ID); ?>
              </tbody>
            </table>
        </section>

        <section class="kivi-single-item-body kivi-single-cost-info">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>>
                <button class="kivi-toggle" data-target="hinta"><?php _e('Hinta ja kustannukset', 'kivi'); ?></button>
              </h3>
            </div>
            <table id="hinta" class="kivi-item-table">
              <tbody>
                <?php echo view_cost_info( $post->ID); ?>
              </tbody>
            </table>
        </section>

        <section class="kivi-single-item-body kivi-single-additional-info">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>>
                <button class="kivi-toggle hide-by-default" data-target="lisatiedot"><?php _e('Asunnon lisätiedot', 'kivi'); ?></button>
              </h3>
            </div>
            <table id="lisatiedot" class="kivi-item-table">
              <tbody>
                <?php echo view_additional_info( $post->ID); ?>
              </tbody>
            </table>
        </section>

        <?php if( $materials = view_materials_info( $post->ID) ): ?>
        <section class="kivi-single-item-body kivi-single-materials-info">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>>
                <button class="kivi-toggle hide-by-default" data-target="materiaalit"><?php _e('Asunnon tilat ja materiaalit', 'kivi'); ?></button>
              </h3>
            </div>
            <table id="materiaalit" class="kivi-item-table">
              <tbody>
                <?php echo $materials; ?>
              </tbody>
            </table>
        </section>
        <?php endif; ?>

        <?php if( $housingco = view_housing_company_info( $post->ID) ): ?>
        <section class="kivi-single-item-body kivi-single-housing-company-info">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>>
                <button class="kivi-toggle hide-by-default" data-target="taloyhtio"><?php _e('Taloyhtiö', 'kivi'); ?></button>
              </h3>
            </div>
            <table id="taloyhtio" class="kivi-item-table">
              <tbody>
                <?php echo $housingco; ?>
              </tbody>
            </table>
        </section>
        <?php endif; ?>

        <?php if( $services = view_services_info( $post->ID)->__toString() ): ?>
		    <section class="kivi-single-item-body kivi-single-services-info">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>>
                <button class="kivi-toggle hide-by-default" data-target="palvelut"><?php _e('Palvelut ja liikenneyhteydet', 'kivi'); ?></button>
              </h3>
            </div>
            <table id="palvelut" class="kivi-item-table">
              <tbody>
                <?php echo $services; ?>
              </tbody>
            </table>
        </section>
        <?php endif; ?>

        <?php if( get_kivi_option('kivi-gmap-id') ){ ?>
        <section class="kivi-single-item-body kivi-single-gmap">
            <div class="kivi-header-wrapper">
              <h3 class="kivi-single-item-body-header"<?php echo $brand_styling; ?>><?php _e('Kartta kaikki tiedot', 'kivi'); ?></h3>
            </div>
              <div id="map"></div>
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
