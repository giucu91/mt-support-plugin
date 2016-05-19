<?php

// Add Shortcode
function mtsp_shortcode_function() {

	new MTSP_Int_GF_HS;

	// Documentation Button
	$doc_button_label 		= get_theme_mod( 'mtps_support_left_button_label', esc_html__( 'View Full Documentation', 'mtsp' ) );
	$doc_button_url 		= get_theme_mod( 'mtps_support_left_button_url', '#' );

	// Request Button
	$request_button_label 	= get_theme_mod( 'mtps_support_right_button_label', esc_html__( 'Submit Support Request', 'mtsp' ) );
	$HelpScout_API = get_theme_mod( 'mtps_support_docs_api' );

	// Gravity Form
	$gravity_form = get_theme_mod( 'mtps_support_gravity_form' );
	if ( $gravity_form && $gravity_form != -1 ) {
		$gravity_shortcode = '[gravityform id='.$gravity_form.' title=false description=false ajax=true tabindex=49]';
	}else{
		$gravity_shortcode = '';
	}
	
	// Defaults for support Boxes
	$defaults = array(
		1 => array(
			'title' 	=> esc_html__( 'Pre-sale Question?', 'mtsp' ),
			'content' 	=> esc_html__( 'You are more than welcome to ask questions before committing.', 'mtsp' ),
			'url' 		=> '#'
			),
		2 => array(
			'title' 	=> esc_html__( 'Getting Started', 'mtsp' ),
			'content' 	=> esc_html__( 'Find everything to get our products up and running.', 'mtsp' ),
			'url' 		=> '#'
			),
		3 => array(
			'title' 	=> esc_html__( 'Frequently Asked Questions', 'mtsp' ),
			'content' 	=> esc_html__( 'Perhaps you\'ve encountered a common issue or have a quick question.', 'mtsp' ),
			'url' 		=> '#'
			),
		4 => array(
			'title' 	=> esc_html__( 'Advanced Documention', 'mtsp' ),
			'content' 	=> esc_html__( 'Familiar with code? Use the advanced docs to dig deeper into our products.', 'mtsp' ),
			'url' 		=> '#'
			),
		5 => array(
			'title' 	=> esc_html__( 'Extensions & Themes', 'mtsp' ),
			'content' 	=> esc_html__( 'Everything you need to know about our extensions and themes.', 'mtsp' ),
			'url' 		=> '#'
			),
		6 => array(
			'title' 	=> esc_html__( 'Developer Documentation', 'mtsp' ),
			'content' 	=> esc_html__( 'See all of our products\' functions, classes, actions, filters, etc.', 'mtsp' ),
			'url' 		=> '#'
			),
		);
	

	$output = '<div class="mt-support-container">';
	$output .= '<section id="support-page">';
	// Support Boxes
	$output .= '<div class="support-page-boxes">';
		for ($i=1; $i < 7; $i++) { 
		
			$title = get_theme_mod( 'mtps_box'.$i.'_title', $defaults[$i]['title'] );
			$content = get_theme_mod( 'mtps_box'.$i.'_content', $defaults[$i]['content'] );
			$url = get_theme_mod( 'mtps_box'.$i.'_url', $defaults[$i]['url'] );

			if ( $title || $content || $url ) {
				$output .= '<div class="support-box">';
					$output .= '<a href="'.$url.'" class="support-section-link">';
						$output .= '<h4 class="support-section-title">'.$title.'</h4>';
						$output .= '<p class="support-section-description">'.$content.'</p>';
					$output .= '</a>';
				$output .= '</div>';
			}

		}
	$output .= '</div>';

	$output .= '<div class="support-page-buttons">';
	if ( $doc_button_label && $doc_button_url  ) {
		$output .= '<a class="edd-docs-link orange-button button" href="'.$doc_button_url.'">'.$doc_button_label.'</a>';
	}
	if ( $request_button_label && $HelpScout_API && $gravity_shortcode != '' ) { 
		$output .= '<a id="request-ticket" class="button" href="#">'.$request_button_label.'</a>';
	}
	$output .= '</div>';

	$output .= '</section>';

	$output .= '<div class="support-form" style="display:none;">';
	$output .= do_shortcode($gravity_shortcode);	
	$output .= '</div>';

	$output .= '</div>';
	return $output;

}
add_shortcode( 'mt-support-content', 'mtsp_shortcode_function' );