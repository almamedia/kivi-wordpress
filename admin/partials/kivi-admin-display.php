<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/admin/partials
 */
?>


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="admin-page-kivi">
    <div class="admin-page header">
        <div class="wrapper">
            <img src="<?php echo plugin_dir_url( __FILE__ ) . '../../img/kivi_logo_laatikolla_.svg'; ?>" alt="kivi logo"
                 width="120">
        </div>
    </div>
    <div class="admin-page page-body">
        <div class="wrapper">
            <form method="post" action="options.php" id="xmlform" autocomplete="off">
				<?php settings_fields( 'kivi-settings' ); ?>
				<?php do_settings_sections( 'kivi-settings' ); ?>
                <h2><?php _e( "Aseta URL ja tuo aineisto", "kivi" ); ?>:</h2>
                <input type="text" name="kivi-remote-url" id="kivi-remote-url" class="text-input"
                       value="<?php echo esc_attr( get_option( 'kivi-remote-url' ) ); ?>"
                       placeholder="<?php _e( 'Tähän laitetaan XML-tiedoston URL-osoite', 'kivi' ); ?>">
                <button type="button" id="xmlimport-sync" class="button button-secondary">
					<?php _e( "Tuo", "kivi" ); ?>
                </button>
                <div class="other-kivi-settings">
                    <label>
                        <div><?php _e( "Rajaa tuotavia kohteita", "kivi" ); ?></div>
                        <div class="description"><?php _e( "Rajaa aineistosta tuotavia kohteita aineiston kenttien mukaan. Esim. nettinäkyvyydelle valitun välittäjän mukaan voi rajata käyttäen kenttää iv_person_name." ); ?></div>
                        <input type="text" name="kivi-prefilter-name" id="kivi-prefilter-name" class="text-input"
                               value="<?php echo esc_attr( get_kivi_option( 'kivi-prefilter-name' ) ); ?>"
                               placeholder="<?php _e( 'elementin nimi',
							       'kivi' ); ?>"><?php _e( "Esisuodatuksen peruste", "kivi" ); ?>
                        <input type="text" name="kivi-prefilter-value" id="kivi-prefilter-value" class="text-input"
                               value="<?php echo esc_attr( get_kivi_option( 'kivi-prefilter-value' ) ); ?>"
                               placeholder="<?php _e( 'elementin arvo', 'kivi' ); ?>"><?php _e( "Esisuodatuksen arvo",
							"kivi" ); ?>
                    </label>

                    <p>
                        <label for="kivi-brand-color"><?php _e( "Brändiväri", "kivi" ); ?></label>
                        <input type="text" name="kivi-brand-color" id="kivi-brand-color" class="text-input"
                               value="<?php echo esc_attr( get_option( 'kivi-brand-color' ) ); ?>"
                               placeholder="<?php _e( 'Brändiväri, joka esiintyy esim. napeissa jne. Kivi -kohteita näytettäessä',
							       'kivi' ); ?>">
                    </p>
                    <label>
                        <input type="text" name="kivi-slug" id="kivi-slug" class="text-input"
                               value="<?php echo esc_attr( get_option( 'kivi-slug' ) ); ?>"
                               placeholder="<?php _e( 'esim. kohde', 'kivi' ); ?>"><?php _e( "Polkutunnus", "kivi" ); ?>
                    </label>
                    <label>
                        <input type="checkbox"
                               id="kivi-use-debt-free-price-on-shortcode" <?php if ( get_kivi_option( 'kivi-use-debt-free-price-on-shortcode' ) ) {
							echo "checked=''";
						} ?> name="kivi-use-debt-free-price-on-shortcode"
                               value=""><?php _e( 'Näytä velaton hinta shortcode-listauksissa (oletus: myyntihinta)' ) ?>
                    </label>
                    <label>
                        <input type="checkbox"
                               id="kivi-clean-values" <?php if ( get_kivi_option( 'kivi-clean-values' ) ) {
							echo "checked=''";
						} ?> name="kivi-clean-values"
                               value=""><?php _e( 'Siisti kohdesivun arvot. (Esim. hintoihin €, neliöt m², piilota menneet esittelyt)' ) ?>
                    </label>
                    <label><input type="text" name="kivi-gmap-id" id="kivi-gmap-id" class="text-input"
                                  value="<?php echo get_kivi_option( 'kivi-gmap-id' ) ?>" placeholder="google maps key">Google
                        maps api key</label>
                    <input type="hidden" name="kivi-show-statusbar" id="kivi-show-statusbar"
                           value="<?php echo esc_attr( get_option( 'kivi-show-statusbar' ) ); ?>">

                    <button type="button button-secondary" id="xmlimport-reset"
                            class="button button-secondary"><?php _e( 'Keskeytä', 'kivi' ); ?></button>

                    <div>
                        <h3>REST-API</h3>
                        <?php do_action('kivi-admin-rest-form'); ?>

                        <p><a href="#" onclick='document.getElementById("kivi-credentials").style.visibility="visible";'>Aseta tunnukset</a></p>
                        <div id="kivi-credentials" style="visibility: hidden;">
                            <label for="kivi-rest-user"><?php _e( "User name", "kivi" ); ?></label>
                            <input type="password" name="kivi-rest-user" id="kivi-rest-user" class="text-input"
                                   value="" autocomplete="new-password">
                            <label for="kivi-rest-pass"><?php _e( "Password", "kivi" ); ?></label>
                            <input type="password" name="kivi-rest-pass" id="kivi-rest-pass" class="text-input"
                                   value="" autocomplete="new-password">
                        </div>
                    </div>



                    <button type="button" id="save-kivi-settings"
                            class="button button-secondary"><?php _e( 'Tallenna asetukset', 'kivi' ); ?></button>
                </div>
            </form>

			<?php if ( ! wp_next_scheduled( 'kivi_items_sync' ) ) : ?>
                <p><?php _e( 'Automaattipäivitys on pois päältä. Tarkista asetukset, poista lisäosa käytöstä ja ota se uudelleen käyttöön.',
						'kivi' );
					?></p>
			<?php else: ?>
				<?php
				$date = new DateTime();
				$date->setTimestamp( wp_next_scheduled( 'kivi_items_sync' ) );
				$date->setTimezone( new DateTimeZone( 'Europe/Helsinki' ) );
				echo _( 'Seuraava tarkistus: ' ) . $date->format( 'd.m.Y H:i:s' ) . "\n";
				?>
			<?php endif; ?>
        </div>
    </div>
</div>
