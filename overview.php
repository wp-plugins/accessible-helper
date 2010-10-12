<?php
$post = get_post( $_GET['post'] );
?>
<style type="text/css">

.severity-group {
	padding: 10px;
}
.test {
	margin-left: 20px;
}
ul.problems {
	margin-left: 20px;	
	border-top: 1px solid #aaa;
}
ul.problems li {
	padding-top: 3px;
	padding-bottom: 3px;
	font-family: monospace;
	border-bottom: 1px solid #aaa;
}

</style>
<h2><?php _e ( 'Accessibility overview' ); ?> of <?php print $post->post_title; ?></h2>
<p><a href="post.php?post=<?php print $post->ID; ?>&action=edit">Return to Post</a></p>
<?php
$quail = new quail( $post->post_content, get_option( 'accessibility_guideline' ), 'string', 'array' );
$quail->setOption ( 'cms_mode', true );
$quail->runCheck();
$results = $quail->getReport();
$errors = accessible_helper_cleanup_results( $results );
foreach ( $errors as $severity => $error ) {
	echo '<div class="severity-group">';
	echo '<h3>'. accessible_helper_get_severity( $severity ) .'</h3>';
	echo '<p>'. $error['total'] .' problems</p>';
	unset ( $error['total'] );
	foreach ( $error as $test ) {
		echo '<div class="test">';
		echo '<h4>'. $test['title'] .'</h4>';
		echo '<div>';
		echo $test['body'];
		echo '</div>';
		if ( is_array( $test['problems'] ) ) {
			echo '<h4>'. __ ( 'Problems', 'accessible_helper' ) .'</h4>';

			echo '<ul class="problems">';
			foreach ( $test['problems'] as $problem ) {
				if ( $problem['element'] ) {
					echo '<li>';
					if ( $problem['line'] ) {
						echo '<strong>'. __ ( 'Line No.', 'accessible_helper' ) . $problem['line'] .'</strong>';
					}
					echo $problem['element'];
				}
			}
			echo '</ul>';
		}
		echo '</div>';
	}
	echo '</div>';
}