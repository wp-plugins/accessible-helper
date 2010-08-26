<?php
if( $_GET['test'] == 'list' ) {
	echo '<h2>'. __( 'Manage Error Messages', 'accessible_helper' ) .'</h2>';
	echo '<ul>';
	$translations = fopen( ABSPATH .
		'wp-content/plugins/accessible_helper/quail/quail/guidelines/translations/en.txt', 'r' );
	if ( $translations ) {
	  while ( $translation = fgetcsv( $translations ) ) {
			i f( count( $translation ) == 4 ) {
				if ( $test_info = get_option( 'accessibility_test_'. $test ) ) {
					$title = $test_info['title'];
				}
				else {
					$title = $translation[0];
				}
				print '<li><a href="?page=accessibility&test='. $title .'">'.
					 $translation[1].
					 '</a></li>';
			}
		}
	}
	echo '</ul>';

}
else {
	wp_enqueue_script( 'post' );
	if ( user_can_richedit() ) {
		wp_enqueue_script( 'common' );
	 wp_enqueue_script( 'jquery-color' );
	 wp_admin_css( 'thickbox' );
	 wp_print_scripts( 'post' );
	 wp_print_scripts( 'media-upload' );
	 wp_print_scripts( 'jquery' );
	 wp_print_scripts( 'jquery-ui-core' );
	 wp_print_scripts( 'jquery-ui-tabs' );
	 wp_print_scripts( 'tiny_mce' );
	 wp_print_scripts( 'editor' );
	 wp_print_scripts( 'editor-functions' );
	 add_thickbox( );
	 wp_tiny_mce( );
	 wp_admin_css( );
	 wp_enqueue_script( 'utils' );
	 do_action( "admin_print_styles-post-php" );
	 do_action( 'admin_print_styles' );
	 remove_all_filters( 'mce_external_plugins' );


	}
	add_thickbox( );
	if ( $_POST['submit'] ) {
		$test = array( 'title' => stripslashes( $_POST['title'] ),
					 'body' => stripslashes( $_POST['content'] ) );
		update_option( 'accessibility_test_'. $_POST['testname'], $test );
	}
	else {
		$test = accessible_helper_get_test( $_GET['test'] );
	}
	echo '<h2>'. __( 'Update Error Message', 'accessible_helper' ) .'</h2>';
	if ( $_POST['submit'] ) {
		?>
		<div id="message" class="updated fade"><p><strong><?php _e( 'Error Message Saved.' ) ?></strong></p></div>
		<?php
	}
	?>

	<form method="post">
 		<input type="hidden" name="testname" value="<?php print $_GET['test']; ?>"/>
 		<div class="narrow">
 		<h3><label for="title">Test Title</label></h3>
 		<div id="titlediv">
	 		<p>
	 	    <input type="text" value="<?php echo htmlspecialchars( $test['title'] ); ?>" id="title" name="title">
	 		</p>
		</div>
			<div id="poststuff">
			<div id="<?php echo user_can_richedit( ) ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
			
			<?php the_editor( $test['body'] ); ?>
			</div>
			</div>
 		<p><input type="submit" name="submit" value="Update Test"></p>
 		</div>
	</form>
	<?php
	
}