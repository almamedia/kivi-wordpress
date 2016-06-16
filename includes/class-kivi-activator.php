<?php

/**
 * Fired during plugin activation
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/includes
 */

class Kivi_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		add_filter( 'cron_schedules', 'kivi_add_30_minutes_schedule' );
	}

}
