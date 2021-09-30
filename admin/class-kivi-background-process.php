<?php
/**
 * The Kivi background processing functionality. Based on WP_Background_Process.
 * The custom post type items are created here, metadata populated. Item updates and deletions are here,
 * too.
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/admin
 */

class Kivi_Background_Process extends WP_Background_Process {

	/**
	 * Action hook to start the background process
	 */
	protected $action = 'kivi_sync';

	/**
	 * Called on every item on task queue.
	 */
	protected function task( $item ) {
		/* Add or update item */
		$this->handle_parsed_item( $item );

		sleep( intval( apply_filters( 'kivi-background-task-sleep', 1 ) ) );

		return false;
	}

	/**
	 * Stop the background process.
	 */
	public function stop() {
		// Delete queue
		$this->delete( $this->get_batch()->key );
	}

	/**
	 * Stops the background process and deletes all imported KIVI data.
	 */
	public function reset() {

		// Delete queue
		$this->delete( $this->get_batch()->key );

		// Delete items
		$this->items_delete_all();

		// Reset the data source; somehow this seems like the only way to really
		// prevent the process from ticking again
		update_option( 'kivi-remote-url', get_option( 'kivi-remote-url' ) );
	}

	/**
	 * Called once after background process is done and task queue is empty
	 */
	protected function complete() {
		error_log( "Complete called" );
		update_option( 'kivi-show-statusbar', 0 );
		parent::complete();
	}


	/**
	 * Stop multiple processes from being dispatched.
	 */
	public function is_process_already_running() {
		return $this->is_process_running();
	}

	/*  Do all the needed stuff for the parsed item */
	public function handle_parsed_item( &$item ) {
		$this->item_update( $item );
		return;
	}


	/* Check if the item already exists in wp database */
	public function item_exists( $item ) {
		$args = array(
			'meta_query'  => array(
				array(
					'key'           => '_realty_unique_no',
					'value'         => $item['realty_unique_no'],
					'type'          => 'NUMERIC',
					'cache_results' => false,
				)
			),
			'post_type'   => 'kivi_item',
			'post_status' => get_post_stati(),
		);

		$count = count( get_posts( $args ) );

		return $count > 0;
	}


	/**
	 * Figure out if item needs to be modified. That is if the updatedate in the
	 * post metadata is different from the one in the incoming XML.
	 *
	 * Removing item_update_content -call allows custom titles and content to be written
	 * in WP admin without overwriting after scheduled update.
	 */
	public function item_update( &$item ) {
		$postarr                = array();
		$item_post_id = 0;
		$post_date = '';
		$args  = array(
			'meta_query'  => array(
				array(
					'key'   => '_realty_unique_no',
					'value' => $item['realty_unique_no'],
					'type'  => 'NUMERIC',
				)
			),
			'post_type'   => 'kivi_item',
		);
		$posts = get_posts( $args );

		if( count($posts) ){
			$item_post = $posts[0];
			$item_post_id = $item_post->ID;
			$post_date = $item_post->post_date;
		}

		$postarr['ID'] = $item_post_id;

		$uidata = KiviRest::getUiData($item['realty_unique_no']);
		$meta                   = array();

		$postarr['post_type']   = 'kivi_item';
		$postarr['post_title']  = wp_strip_all_tags( $item['flat_structure'] . ' ' . $item['town'] . ' ' . $item['street'] );

		foreach( $item as $key => $data ) {
			$meta['_'.$key] = $data;
		}

		foreach( $uidata as $key => $data ) {
			$meta['_ui_'.$key] = $data;
		}
		if(is_array($uidata['sections'])){
			foreach( $uidata['sections'] as $key => $data ) {
				$meta['_ui_section_'.$key] = $data;
			}
		}

		$log = 'item_update ';
		if($item_post_id == 0){
			$log .= '(new) ';
		}
		$log .= current_time('mysql');
		$log .= " ".md5(wp_json_encode($item).wp_json_encode($uidata));


		$postarr['meta_input'] = $meta;
		$postarr['post_status'] = 'publish';
		$postarr['post_date'] = $post_date;

		$new_id = wp_insert_post( $postarr );
		add_post_meta( $new_id, '_kivi_log', $log);

	}



	public function items_delete_all() {
		$args  = array(
			'post_type'   => 'kivi_item',
			'numberposts' => - 1,
			'post_status' => get_post_stati(),
		);
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			wp_delete_post( $post->ID, true );
		}
	}

}
