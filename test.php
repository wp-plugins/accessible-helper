<?php 
$test = $_GET['test']; 
$test_info = accessible_helper_get_test($test);
echo '<div id="test-overview">';
echo '<h1>'. $test_info['title'] .'</h1>';
echo '<div>'. $test_info['body'] .'</div>';
echo '</div>';