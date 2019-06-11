<?php
/**
 * The Kivi background processing functionality. Based on WP_Background_Process.
 * The custom post type items are created here, metadata populated and media
 * downloaded and saved to media gallery. Item updates and deletions are here,
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
      $this->handle_parsed_item($item);

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
      $this->items_delete();

      // Reset the data source; somehow this seems like the only way to really
      // prevent the process from ticking again
      update_option('kivi-remote-url', get_option('kivi-remote-url'));
    }

    /**
     * Called once after background process is done and task queue is empty
     */
    protected function complete() {
      error_log("Complete called");
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
    public function handle_parsed_item( &$item ){
      if( $this->item_exists( $item) ){
        $this->item_update($item);
      }else {
        $this->item_add( $item );
      }
      return;
    }


    /* Check if the item already exists in wp database */
    public function item_exists( &$item ){
      $args = array(
        'meta_key' => '_realty_unique_no',
        'meta_value' => $item['realty_unique_no'],
		'meta_type' => 'NUMERIC',
        'post_type' => 'kivi_item',
		'post_status' => get_post_stati(),
      );
	  
	  $count = count( get_posts( $args ) );
	  
      return $count > 0;
    }

    /* Add the media to media library */
    public function add_media( &$image_url, &$image_type, &$image_order, &$post_id ){
		// add only if not already in WP ( search for original_image_url )
	   $args = array(
		 'meta_key' => 'original_image_url',
		 'meta_value' => $image_url,
		 'post_type' => 'attachment',
		 'post_status' =>'any',
	   );
	   $posts = get_posts( $args );
	   if( empty($posts) ) {
		  $ret = $this->kivi_save_image($image_url, $image_type, $image_order, $post_id);
	   }
	   elseif( 1 == count($posts) ) {
       $attachment = array(
         'ID' => $posts[0]->ID,
         'post_parent' => $post_id
       );
       wp_insert_attachment( $attachment );
     }
    }

    /**
    * Figure out if item needs to be modified. That is if the updatedate in the
    * post metadata is different from the one in the incoming XML.
	*
	* Removing item_update_content -call allows custom titles and content to be written 
	* in WP admin without overwriting after scheduled update.
    */
    public function item_update(&$item){
       $args = array(
        'meta_key' => '_realty_unique_no',
        'meta_value' => $item['realty_unique_no'],
		'meta_type' => 'NUMERIC',
        'post_type' => 'kivi_item',
		'post_status' => get_post_stati(),
      );
      $posts = get_posts( $args );
	  if( ! isset( $posts[0] ) ) {
		return;
	  }
	  $post = $posts[0];
      $d = get_post_meta( $post->ID, '_updatedate', $single=true);
      if( $item['updatedate'] === $d ){
		  
      }else {
        $this->item_update_metadata( $post->ID, $item );
		    $this->item_update_content( $post->ID, $item ); // comment this line to disable automatic updates for post_content and post_title.
      }

      /* Publish the post */
      $postarr = array();
      $postarr['ID'] = $post->ID;
      $postarr['post_status'] = 'publish';
      wp_update_post( $postarr );
    }

	/*
	* Update post_content and post_title for kivi_item.
	*/
	public function item_update_content($post_id, &$item){
		$postarr =  [];
		$postarr['post_content'] = $item['presentation'];
		$postarr['post_title'] = $item['flat_structure'] . ' ' . $item['town'] . ' ' . $item['street'];
		$postarr['ID'] = $post_id;
		wp_update_post( $postarr );
	}
	
    /*
    * Update all the metadata in the item, in case the item has any
    * modifications.
    */
    public function item_update_metadata( $post_id, &$item ) {
      foreach ( $item as $key => $value ) {
        if( $key == 'images' ){
          $new_images = array_column( $item['images'], 'image_url' );
          $current_images = [];
          $images = get_post_meta ( $post_id, '_kivi_item_image', $single = false );

          foreach ($images as $i) {
            $original_image_url = get_post_meta( $i, 'original_image_url', $single = true);
            if( ! in_array( $original_image_url, $new_images )   ) {
              wp_delete_attachment( $i, $force_delete = true );
              delete_post_meta( $post_id, '_kivi_item_image', $meta_value = $i);
            }else{
              array_push( $current_images, $original_image_url);
            }
          }

          foreach ($item['images'] as $i ) {
             if( ! in_array( $i['image_url'], $current_images) ){
               $this->add_media( $i['image_url'], $i['image_realtyimagetype_id'], $i['image_iv_order'],  $post_id );
             }
             else{ // current image
                 $args = array( // find current image
                     'meta_key' => 'original_image_url',
                     'meta_value' => $i['image_url'],
                     'post_type' => 'attachment',
                 );
                 $posts = get_posts( $args );
                 if( isset( $posts[0] ) && count($posts) == 1 ) { // should be only one
                     $posty = $posts[0];
                     if( get_post_meta( $posty->ID, 'image_order', true) != $i['image_iv_order']){
                         update_post_meta( $posty->ID, 'image_order', intval($i['image_iv_order']) );
                     }

                     // update post excerpt with image description ( post excerpt == attachment caption )
                     $post_arr = array(
                         'ID'           => $posty->ID,
                         'post_excerpt' => sanitize_text_field($i['image_desc']),
                     );
                     wp_update_post( $post_arr );

                     // maybe set as featured image
                     if( $i['image_iv_order'] == 1 || 'pääkuva' == $i['image_realtyimagetype_id'] ) {
                         set_post_thumbnail( $post_id, $posty->ID );
                     }
                 }

             }
          }
        } elseif( $key == 'iv_person_image_url' && $value ) {
			$image_url = get_post_meta( $post_id, '_sc_image_url', $single=true );
			if ( $image_url  !== $value ) {
			  $this->kivi_save_image( $value, 'iv_person_image_url', 0, $post_id );
			  // And delete old file here too...
			  $args = array(
				'meta_key' => '_sc_image_url',
				'meta_value' => $image_url,
				'post_type' => 'attachment',
			  );
			  $posts = get_posts( $args );
			  foreach( $posts as $attachment ) {
				  if( wp_get_post_parent_id( $attachment->ID ) == $post->ID ) {
					  wp_delete_attachment( $attachment->ID, false ); // move to trash (true -> force delete)
				  }
			  }
			}
		} else {
          update_post_meta( $post_id, '_'.$key, $value );
        }
      }
    }

    /*
    * Add new item to wp.
    * Post is created as a draft first, images downloaded, metadata updated.
    * Finally post is published.
    */
    public function item_add( &$item ){
      $postarr =  [];
      $postarr['post_type'] = 'kivi_item';
      $postarr['post_status'] = 'draft';
      $postarr['post_content'] = $item['presentation'];
      $postarr['post_title'] = $item['flat_structure'] . ' ' . $item['town'] . ' ' . $item['street'];
      $post_id = wp_insert_post ( $postarr );
	  add_post_meta ( $post_id, '_realty_unique_no', $item['realty_unique_no'], true );
      foreach ( $item as $key => $value ) {
        if ( $key == 'images' ) {
          foreach ($item['images'] as $key => $value) {
            $this->add_media( $value['image_url'], $value['image_realtyimagetype_id'], $value['image_iv_order'], $post_id );
          }
        }elseif( $key == 'iv_person_image_url' && $value ){
          $this->kivi_save_image($value, 'iv_person_image_url', 0, $post_id);
        }elseif ( $key == 'realty_unique_no' && $value) {
          add_post_meta ( $post_id, '_'.$key, $value, true );
        }elseif ( $value != "") {
          add_post_meta ( $post_id, '_'.$key, $value );
        }
      }
      /* Publish the post when all metadata and stuff is in place */
      $postarr =  [];
      $postarr['ID'] = $post_id;
      $postarr['post_status'] = 'publish';
      wp_update_post( $postarr );
    }

    /*
    * Delete the post based on post id. Related attacments are deleted, too.
    */
    public function item_delete($post_id){
      foreach ( get_post_meta ( $post_id, '_kivi_item_image', $single = false ) as $attach_id ) {
        wp_delete_attachment($attach_id, true);
      }
      foreach ( get_post_meta ( $post_id, '_kivi_iv_person_image', $single = false ) as $attach_id ) {
        wp_delete_attachment($attach_id, true);
      }
      wp_delete_post($post_id,true);
    }

    /*
    * Delete items whose _realty_unique_no is not in among active_items. This
    * Is used to delete (sold) items that no longer exist in the incoming XML.
    */
    public function items_delete( &$active_items = [] ){
      $args = array(
        'post_type' => 'kivi_item',
        'numberposts' => -1,
        'post_status' => get_post_stati(),
      );
      $posts = get_posts( $args );
      foreach ($posts as $post) {
        $realtyid = get_post_meta( $post->ID, '_realty_unique_no', $single = true );
        if( !in_array($realtyid, $active_items) ){
          $this->item_delete($post->ID);
        }
      }
    }


    /*
    * Save image into media library as an attachment to the according KIVI item
    */
    public function kivi_save_image($url, $image_type, $image_order=0, $post_id = 0) {
      $filename = basename($url);

      $upload_dir = wp_upload_dir();
      $upload_basedir = $upload_dir['basedir'];

      $filepath = $upload_basedir . '/' . $filename;

      if (file_exists($filepath)) {
        $filename = $filename . '-' . sha1($filename);
      }

	  $response = wp_remote_get($url);
	  if ( is_wp_error( $response ) ) {
		return;
  	  }
      $upload_file = wp_upload_bits($filename, null, wp_remote_retrieve_body( $response ));
      if (!$upload_file['error']) {
        $wp_filetype = wp_check_filetype($filename, null );
        $attachment = array(
          'post_mime_type' => $wp_filetype['type'],
          'post_parent' => $post_id,
          'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
          'post_content' => '',
          'post_status' => 'inherit'
        );
        $attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_id );
        if ( $attachment_id ) {
			
		  // Set featured image if not yet set
          if ( "pääkuva" == $image_type ) {
            set_post_thumbnail( $post_id, $attachment_id );
          }	
			
          require_once(ABSPATH . 'wp-admin/includes/image.php');
          $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
          wp_update_attachment_metadata( $attachment_id,  $attachment_data );
          if( $image_type == 'iv_person_image_url'){
            update_post_meta ( $post_id, '_kivi_iv_person_image', $attachment_id );
          }else{
            add_post_meta ( $post_id, '_kivi_item_image', $attachment_id );
          }
          add_post_meta( $attachment_id, 'original_image_url', $url);
          add_post_meta( $attachment_id, 'image_type', $image_type);
          if( $image_type != 'iv_person_image_url'){
            add_post_meta( $attachment_id, 'image_order', $image_order);
          }
          
          return wp_get_attachment_url($attachment_id);
        }
      }
    }


  }
