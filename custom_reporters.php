<?php

include_once(ABSPATH .'wp-content/plugins/accessible-helper/quail/quail/reporters/reporter.demo.php');


/**
*	The custom reporter class that shows a highlighted view of errors in a post.
*/
class reportWpReportDemo extends reportDemo {
	
	/**
	*	@var array An array of the classnames to be associated with items
	*/	
	var $classnames = array(QUAIL_TEST_SEVERE => 'quail_severe',
							QUAIL_TEST_MODERATE => 'quail_moderate',
							QUAIL_TEST_SUGGESTION => 'quail_suggestion',
							);
	
	var $images = array(QUAIL_TEST_SEVERE => 'severe.png',
							QUAIL_TEST_MODERATE => 'moderate.png',
							QUAIL_TEST_SUGGESTION => 'suggestion.png',
							);
	
	/**
	*	The getReport method - we iterate through every test item and
	*	add additional attributes to build the report UI.
	*	@return string A fully-formed HTML document.
	*/
	function getReport() {
		$severity = get_option( 'accessibility_severity' );
		$problems = $this->guideline->getReport();
		if(is_array($problems)) {
			foreach($problems as $testname => $test) {
				if($severity[$test['severity']] && is_array($test)) {
					foreach($test as $k => $problem) {
						if(is_object($problem) && property_exists($problem, 'element') && is_object($problem->element)) {
							$existing = $problem->element->getAttribute('class');
							$problem->element->setAttribute('class', 
							$existing .' '. $this->classnames[$test['severity']]);
							$link = $this->dom->createElement('a');
							$link = $problem->element->parentNode->insertBefore($link, $problem->element);
							$link->setAttribute('href', 'edit.php?page=accessibility&type=test&test='. $testname);
							$link->setAttribute('class', 'error-hover');
							$image = $this->dom->createElement('img');
							$image = $link->appendChild($image);
							$image->setAttribute('alt', $test['title']);
							$image->setAttribute('src', '../wp-content/plugins/accessible-helper/images/'. 
														$this->images[$test['severity']]);
						}
					}
				}
			}
		}		

		return $this->dom->saveHTML();
	}
}