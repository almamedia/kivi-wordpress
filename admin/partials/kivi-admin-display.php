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
			<br />
			<a href="https://github.com/almamedia/kivi-wordpress/blob/REST-API-instead-XML/README.md#settings" target="_blank">
				<?php _e( "Lisäosan dokumentaatio GitHubissa", "kivi" ); ?>
			</a>
        </div>
    </div>
    <div class="admin-page page-body">
        <div class="wrapper">
            <form method="post" action="options.php" id="xmlform" autocomplete="off">
				<?php settings_fields( 'kivi-settings' ); ?>
				<?php do_settings_sections( 'kivi-settings' ); ?>

                <div class="kivi-settings kivis-settings-rest">

                    <h3>REST-API</h3>
		            <?php do_action( 'kivi-admin-rest-form' ); ?>

                    <p><a href="#"
                          onclick='document.getElementById("kivi-credentials").style.visibility="visible";'>Aseta
                            tunnukset</a></p>
                    <div id="kivi-credentials" style="visibility: hidden;">
                        <label for="kivi-rest-user"><?php _e( "User name", "kivi" ); ?></label>
                        <input type="password" name="kivi-rest-user" id="kivi-rest-user" class="text-input"
                               value="" autocomplete="new-password">
                        <label for="kivi-rest-pass"><?php _e( "Password", "kivi" ); ?></label>
                        <input type="password" name="kivi-rest-pass" id="kivi-rest-pass" class="text-input"
                               value="" autocomplete="new-password">
                    </div>

                </div>


                <div class="kivi-settings kivi-settings-other">

                    <p>
                        <label for="kivi-brand-color"><?php _e( "Brändiväri", "kivi" ); ?></label>
                        <input type="text" name="kivi-brand-color" id="kivi-brand-color" class="text-input"
                               value="<?php echo esc_attr( get_option( 'kivi-brand-color' ) ); ?>"
                               placeholder="<?php _e( 'Brändiväri, joka esiintyy esim. napeissa jne. Kivi -kohteita näytettäessä',
							       'kivi' ); ?>">
                    </p>
                    <label for="kivi-slug"><?php _e( "Polkutunnus", "kivi" ); ?></label>
                    <input type="text" name="kivi-slug" id="kivi-slug" class="text-input"
                           value="<?php echo esc_attr( get_option( 'kivi-slug' ) ); ?>"
                           placeholder="<?php _e( 'esim. kohde', 'kivi' ); ?>"
                    />


                    <label for="kivi-gmap-id">Google maps api key</label>
                    <input type="text" name="kivi-gmap-id" id="kivi-gmap-id" class="text-input"
                           value="<?php echo get_kivi_option( 'kivi-gmap-id' ) ?>"
                           placeholder="google maps key"
                    />

                </div>


                <div class="kivi-settings">

                    <h3><?php _e( "Rajaa tuotavia kohteita", "kivi" ); ?></h3>
                    <p class="description"><?php _e( "Rajaa aineistosta tuotavia kohteita aineiston kenttien mukaan. Esim. nettinäkyvyydelle valitun välittäjän sähköpostin mukaan voi rajata käyttäen kenttää _ui_CONTACT_EMAIL." ); ?></p>
                    <label for="kivi-prefilter-name"><?php _e( "Esisuodatuksen peruste", "kivi" ); ?></label>
                    <input type="text" name="kivi-prefilter-name" id="kivi-prefilter-name" class="text-input"
                           value="<?php echo esc_attr( get_kivi_option( 'kivi-prefilter-name' ) ); ?>"
                           placeholder="<?php _e( 'elementin nimi',
			                   'kivi' ); ?>">
                    <label for="kivi-prefilter-value"><?php _e( "Esisuodatuksen arvo",
				            "kivi" ); ?></label>
                    <input type="text" name="kivi-prefilter-value" id="kivi-prefilter-value" class="text-input"
                           value="<?php echo esc_attr( get_kivi_option( 'kivi-prefilter-value' ) ); ?>"
                           placeholder="<?php _e( 'elementin arvo', 'kivi' ); ?>">
                </div>

                <button type="button" id="save-kivi-settings"
                        class="button button-secondary"><?php _e( 'Tallenna asetukset', 'kivi' ); ?></button>

                <button type="button button-secondary" id="import-reset"
                        class="button button-secondary"><?php _e( 'Keskeytä', 'kivi' ); ?></button>

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
