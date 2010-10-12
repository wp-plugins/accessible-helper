<style type="text/css">

.quail_severe {
	border: 2px solid red;
}

.quail_moderate {
	border: 2px dashed #ff8700;
}

.quail_suggestion {
	border: 2px solid green;
}

</style>
<?php 
$post = get_post( $_GET['post'] ); ?>

<h2><?php _e ( 'Highlight of Accessibility Problems' ); ?><br/>
     <?php _e ( 'in', 'accessible_helper' ) ?> <?php print $post->post_title; ?></h2>
<p><a href="post.php?post=<?php print $post->ID; ?>&action=edit"><?php _e ( 'Return to Post', 'accessible_helper' ); ?></a></p>

<?php
include_once ( 'custom_reporters.php' );
$quail = new quail( $post->post_content, get_option (  'accessibility_guideline' ), 'string', 'wpReportDemo' );
$quail->setOption( 'cms_mode', true );
$quail->runCheck();
echo $quail->getReport();
