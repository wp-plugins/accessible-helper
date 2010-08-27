<?php
/*
Plugin Name: Accessibility Helper
Plugin URI: http://quail-lib.org/wp
Description: Gives an overview of accessibility problems on a page
Version: alpha1
Author: Kevin Miller
Author URI: http://twitter.com/kevinmiyar
License: GPL3
*/

add_action( 'admin_menu', 'accessible_helper_menu' );
add_action( 'admin_init', 'accessible_helper_admin_init' );
add_filter( 'wp_insert_post_data', 'accessible_helper_filter' );
add_filter( 'wp_insert_page_data', 'accessible_helper_filter' );
add_action( 'wp_dashboard_setup', 'accessible_helper_add_dashboard_widgets' );
wp_register_script('beautytips_handle', WP_PLUGIN_URL. '/accessible_helper/js/jquery.bt.min.js', array('jquery') );
wp_register_script('accessibility_handle', WP_PLUGIN_URL. '/accessible_helper/js/accessibility.js' );

/**
*	Implementation of hook_admin_init()
*/
function accessible_helper_admin_init()
{
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'beautytips_handle' );
    wp_enqueue_script( 'accessibility_handle' );
}

/**
*	Implementation of hook_menu()
*/
function accessible_helper_menu() {
	add_submenu_page( 'plugins.php', 'Accessibility', 'Accessibility', 
					  'manage_options', 'accessibility', 'accessible_helper_options' );

	add_submenu_page( 'edit.php', 'Accessibility', 'Accessibility', 
					  'manage_options', 'accessibility', 'accessible_helper_overview_page' );

	add_options_page('Accessibility Options', 'Accessibility', 'manage_options', 
					 'accessibility-identifier', 'accessible_helper_options' );
	if ( function_exists ( 'add_meta_box' ) ) {
		add_meta_box( 'accessible_helper_post', 'Accessibility', 'accessible_helper_overview', 'post', 'normal', 'low' );
		add_meta_box( 'accessible_helper_post', 'Accessibility', 'accessible_helper_overview', 'page', 'normal', 'low' );
	}
}

/**
*	Returns an array of all formatted guidelines available with QUAIL.
*/	
function accessible_helper_guidelines() {
	return array('all' => 'All Tests',
				 'section508' => 'Section 508',
				 'wcag1a' => 'WCAG 1.0 A',
				 'wcag1aa' => 'WCAG 1.0 AA',
				 'wcag1aaa' => 'WCAG 1.0 AAA',
				 'wcag2a' => 'WCAG 2.0 A',
				 'wcag2aa' => 'WCAG 2.0 AA',
				 'wcag2aaa' => 'WCAG 2.0 AAA',
				 );
}

/**
*	Accessibility admin options page. This is where the admin sets the guideline
*	to use for the site, as well as the severity levels to cover during testing.
*/
function accessible_helper_options() {
  if ( $_GET['test'] ) {
  	include( 'test_settings.php' );
  	return null;
  }
  if ( function_exists('current_user_can') && !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  if ( isset($_POST['submit']) ) {
  	update_option( 'accessibility_guideline', $_POST['guideline'] );
  	update_option( 'accessibility_severity', $_POST['severity'] );
  }
  $severity = get_option( 'accessibility_severity' );
  ?>
  	<h2><?php _e('Accessibility Options', 'accessible_helper') ?></h2>
  	<a href="?page=accessibility&test=list"><?php _e('Manage Error Messages') ?></a>
  	<?php if ( !empty($_POST['submit'] ) ) : ?>
		<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
	<?php endif; ?>
  	<div class="narrow">
	  	<form action="" method="post" id="accessibility-options" style="margin: auto; width: 400px; ">
	  		<h3><label for="guideline">Guideline</label></h3>
	  		<p>
	  	       <select name="guideline" id="guideline">
	  			<?php foreach(accessible_helper_guidelines() as $label => $name): ?>
	  				<option value="<?php echo $label; ?>"<?php if ( get_option( 'accessibility_guideline' ) == $label ) { print ' selected'; } ?>><?php echo $name; ?></option>
	  			<?php endforeach; ?>
	  		   </select>
	  		</p>
	  		<h3><label for="severity">Severity Levels</label></h3>
	  		<p>
	  	       <ul>
	  	       <li><input type="checkbox" name="severity[1]" value="1" id="severity-1" <?php if ( $severity[1] ) print 'checked="checked'; ?>>
	  	       		<label for="severity-1">Severe</label></li>
	  	       <li><input type="checkbox" name="severity[2]" value="2" id="severity-2" <?php if ( $severity[2] ) print 'checked="checked'; ?>>
	  	       		<label for="severity-2">Moderate</label></li>
	  	       <li><input type="checkbox" name="severity[3]" value="3" id="severity-3" <?php if ( $severity[3] ) print 'checked="checked'; ?>>
	  	       		<label for="severity-3">Suggestions</label></li>
			</ul>
	  		</p>
	  		<p><input type="submit" name="submit" value="Save Settings"></p>
	  	</form>
	</div>
  <?php
}

/**
*	Filter callback function. This does the heavy-lifting of checking the 
*	accessibility of content and then saving that data as metadata attached
*	to the post.
*/
function accessible_helper_filter($data, $postarr = array()) {
	if ( !$data['post_content'] || !$data['guid']) {
		return $data;
	}
	global $post;
	accessible_helper_include_library();
	$quail = new quail( $data['post_content'], get_option( 'accessibility_guideline' ), 'string', 'array' );
	$quail->setOption( 'cms_mode', true );	
	$quail->runCheck();
	$results = $quail->getReport();
	$errors = accessible_helper_cleanup_results( $results );	
	foreach ( $errors as $level => $total ) {
		add_post_meta( $post->ID, '_accessibility_'. $level, $total['total'], true );
	}
	return $data;
}

/**
*	Because QUAIL just dumps all tests on you, this cleans up tests that had
*	no errors, as well as tests which had a severity level different than those
*	enabled by the site admin.
*/
function accessible_helper_cleanup_results($results) {
	$severity = get_option( 'accessibility_severity' );
	$errors = array();
	foreach($results as $testname => $result) {
		if ( $severity[$result['severity']] 
		     && ($result['problems']['pass'] === false || count( $result['problems'] ) > 0) ) {
			if ( $result['problems']['pass'] === false ) {
				$errors[$result['severity']]['total']++;
			}
			else {
				$errors[$result['severity']]['total'] += count( $result['problems'] ) - 1;
			}
			$errors[$result['severity']][$testname] = $result;
		}
	}
	return $errors;
}

/**
*	Returns the human-readable version of a given numeric severity level.
*/
function accessible_helper_get_severity($level = null) {
	$severity = array(1 => __( 'Severe Errors', 'accessible_helper' ),
					  2 => __( 'Moderate Errors', 'accessible_helper' ),
					  3 => __( 'Suggestions', 'accessible_helper' ), );
	if ( $level ) {
		return $severity[$level];
	}
	return $severity;
}

/**
*	The overview widget for the admin interface.
*/
function accessible_helper_overview( $post ) {
	if ( !$post->post_content ) {
		echo '<p>Accessibility information will be available once this post is saved.</p>';
		return null;
	}
	foreach( accessible_helper_get_severity() as $severity => $label ) {
		$total = get_post_meta( $post->ID, '_accessibility_'. $severity, true );
		
		if ( $total ) {
			echo '<p class="accessibility-level-'. $severity .'"><span class="label">'. 
			  $label .':</span> '. $total .'</p>';
		}
	}
	echo '<p><a href="edit.php?page=accessibility&post='. $post->ID .'">View More Information</a> | ';
	echo '<a href="edit.php?page=accessibility&post='. $post->ID .'&type=highlight">View Highlighted Info</a></p>';

}

/**
*	Includes the QUAIL library
*/
function accessible_helper_include_library() {
	if ( !file_exists(ABSPATH .'wp-content/plugins/accessible_helper/quail/quail/quail.php') ) {
		return $data;
	}
	return include_once(ABSPATH .'wp-content/plugins/accessible_helper/quail/quail/quail.php');
}

/**
*	The overview page that helps users see accessibility problems with a given page or post.
*	We switch between includes based on the 'type' URL parameter.
*/
function accessible_helper_overview_page() {
	accessible_helper_include_library();
	switch( $_GET['type'] ) {
		case 'overview':
			include('overview.php');
			break;
		case 'highlight':
			include('highlight.php');
			break;
		case 'test':
			include('test.php');
			break;
		default;
			include('overview.php');
	}
}

/**
*	Retrieves the translated or user-prepared error codes about a test.
*	@param string The test class name from QUAIL
*/
function accessible_helper_get_test( $test ) {
	if ( !$test_info = get_option('accessibility_test_'. $test) ) {
		$translations = fopen(ABSPATH .'wp-content/plugins/accessible_helper/quail/quail/guidelines/translations/en.txt', 'r');
		if ($translations ) {
		    while ( $translation = fgetcsv($translations) ) {
				if ( count($translation) == 4 && $translation[0] == $test ) {
					$test_info['title'] = $translation[1];
					$test_info['body'] =  $translation[2];
					return $test_info;
				}
			}
		}
	}
	return $test_info;
}