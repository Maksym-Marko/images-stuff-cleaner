<?php
/*
Plugin Name: Image's stuff cleaner
Plugin URI: https://github.com/Maxim-us/wp-plugin-skeleton
Description: Brief description
Author: Marko Maksym
Version: 1.0
Author URI: https://github.com/Maxim-us
*/

add_action( 'admin_menu', 'register_image_stuff_cleaner_page' );

function register_image_stuff_cleaner_page() {

	add_menu_page( 
		'Images cleaner',
		'Images cleaner',
		'edit_others_posts',
		'register_image_stuff_cleaner_slug',
		'register_image_stuff_cleaner_func' );

}

function register_image_stuff_cleaner_func() { ?>

	<div> <?php

	global $wpdb;

	/*
	*	Remove unattached images
	*/
	$unattached_images = $wpdb->get_results(
		"SELECT id 
		FROM wp_posts
		WHERE post_type = 'attachment'
			AND post_parent = '0'
			"
	);

	$count_of_unattached_images = array();

	echo '<pre>';

	foreach ( $unattached_images as $key => $value ) {

				array_push( $count_of_unattached_images, $value->id );	

/* DANGER */	// $del_image = wp_delete_attachment( $value->id, true );

				// var_dump( $del_image );		

	}

	// var_dump( count( $count_of_unattached_images ) );
		
	echo '</pre>';

	/*
	* remove extra sizes
	*/
	$images = $wpdb->get_results(
		"SELECT id 
		FROM wp_posts
		WHERE post_type = 'attachment'"
	); ?>

<pre>
		<?php  
		// var_dump( $images ); 

			$count_of_images = array();

			$path = dirname( __FILE__ );			

			$root_path = str_replace( '/plugins/images-stuff-cleaner', '', $path) . '/';			

			// size name
			$image_size = 'entry_without_sidebar';

			foreach ( $images as $key => $value ) {
				
				$image_id = $value->id;				

				$img = wp_get_attachment_image_src( $image_id, $image_size );

				if( $img[3] == true ) {

					$image_url = $img[0];

					$path_to_upload = preg_replace( '/.*wp-content\//', '', $image_url );

					$path_to_image = $root_path . $path_to_upload;					

					$file = $path_to_image;

					array_push( $count_of_images, $file );					

					// var_dump( $file );

					/*
					* file remove
					*/
/* DANGER */		// $delete_file = unlink( $file );

					// var_dump( $delete_file );

					/*
					* database cleanup
					*/

						$image_data_in_db = $wpdb->get_row(
							"SELECT meta_id, meta_value FROM wp_postmeta
							WHERE post_id = $image_id
								AND meta_key = '_wp_attachment_metadata'"
						);

						// meta id
						$meta_id = $image_data_in_db->meta_id;
						
						// image meta
						$image_meta = maybe_unserialize( $image_data_in_db->meta_value );

						// var_dump( $image_meta );

						// image sizes array
						$image_sizes_array = $image_meta['sizes'];

						// remove this size from array
						unset( $image_sizes_array[$image_size] );

						$image_meta['sizes'] = $image_sizes_array;

						/*
						*	Update data in DB
						*/
						$update_image_meta = maybe_serialize( $image_meta );

/* DANGER */			// $db_cleaned = $wpdb->update( 'wp_postmeta',
						// 	array(
						// 		'meta_value' => $update_image_meta
						// 	),
						// 	array( 'meta_id' => $meta_id )
						// );

						// var_dump( $db_cleaned );

				}

			}

			// var_dump( count( $count_of_images ) );

		?>

	</pre>

	
	</div>

<?php } ?>