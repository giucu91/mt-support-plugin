<?php
/**
 *	Plugin Name: MT Support Plugin
 *	Plugin URI: http://www.machothemes.com/
 *	Description: Create custom Support Page with Gravity Forms and HelpScout
 *	Version: 1.0.0
 *	Author: Macho Themes
 *	Author URI: http://www.machothemes.com
 */

// Enqueue Style
function mtsp_plugin_style() {
    wp_enqueue_style( 'mtsp-style', plugins_url( 'css/style.css', __FILE__ ) );
    wp_enqueue_style( 'mtsp-themeisle-style', plugins_url( 'css/themeisle.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'mtsp_plugin_style' );

// Include Gravity Form and HelpScout integration
require_once 'gravity-forms-help-scout-search.php';

// Include Shortcode
require_once 'plugin-shortcode.php';


// Function for get all gravity forms
function mtsp_get_gravity_forms() {
	$forms = RGFormsModel::get_forms( null, 'title' );
	$output = array( -1 => esc_html__( 'Select a form', 'mtsp') );
	foreach ( $forms as $form ) {
		$output[$form->id] = $form->title;
	}
	return $output;
}

// Create Customizer Settings
if( !function_exists( 'mtsp_customizer' ) ) {

	add_action( 'customize_register', 'mtsp_customizer', 11);
	function mtsp_customizer( $wp_customize ) {

		$wp_customize->add_panel( 'mtsp_support_panel', array(
			'priority' 	=> 30,
			'capability'=> 'edit_theme_options',
			'title' 	=> esc_html__( '[MT] Support', 'mtsp' )
		));

		$wp_customize->add_section( 'mtsp_support_boxes', array(
			'title'		=> esc_html__( '[MT] Support Boxes', 'decode-pro' ),
			'priority'	=> 1,
			'panel'		=> 'mtsp_support_panel'
		) );
		$wp_customize->add_section( 'mtsp_support_settings', array(
			'title'		=> esc_html__( '[MT] Support Settings', 'decode-pro' ),
			'priority'	=> 1,
			'panel'		=> 'mtsp_support_panel'
		) );

		// Support Boxes
		$wp_customize->add_setting( 'mtps_box1_title', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'Pre-sale Question?', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box1_title', array(
			'label'    	=> esc_html__( 'Box 1 Title', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));
		$wp_customize->add_setting( 'mtps_box1_content', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'You are more than welcome to ask questions before committing.', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box1_content', array(
			'type'		=> 'textarea',
			'label'    	=> esc_html__( 'Box 1 Content', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));
		$wp_customize->add_setting( 'mtps_box1_url', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( '#', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box1_url', array(
			'label'    	=> esc_html__( 'Box 1 URL', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));

		$wp_customize->add_setting( 'mtps_box2_title', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'Getting Started', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box2_title', array(
			'label'    	=> esc_html__( 'Box 2 Title', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));
		$wp_customize->add_setting( 'mtps_box2_content', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'Find everything to get our products up and running.', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box2_content', array(
			'type'		=> 'textarea',
			'label'    	=> esc_html__( 'Box 2 Content', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));
		$wp_customize->add_setting( 'mtps_box2_url', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( '#', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box2_url', array(
			'label'    	=> esc_html__( 'Box 2 URL', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));

		$wp_customize->add_setting( 'mtps_box3_title', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'Frequently Asked Questions', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box3_title', array(
			'label'    	=> esc_html__( 'Box 3 Title', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));
		$wp_customize->add_setting( 'mtps_box3_content', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'Perhaps you\'ve encountered a common issue or have a quick question.', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box3_content', array(
			'type'		=> 'textarea',
			'label'    	=> esc_html__( 'Box 3 Content', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));
		$wp_customize->add_setting( 'mtps_box3_url', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( '#', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box3_url', array(
			'label'    	=> esc_html__( 'Box 3 URL', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));

		$wp_customize->add_setting( 'mtps_box4_title', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'Advanced Documention', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box4_title', array(
			'label'    	=> esc_html__( 'Box 4 Title', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));
		$wp_customize->add_setting( 'mtps_box4_content', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'Familiar with code? Use the advanced docs to dig deeper into our products.', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box4_content', array(
			'type'		=> 'textarea',
			'label'    	=> esc_html__( 'Box 4 Content', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));
		$wp_customize->add_setting( 'mtps_box4_url', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( '#', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box4_url', array(
			'label'    	=> esc_html__( 'Box 4 URL', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));

		$wp_customize->add_setting( 'mtps_box5_title', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'Extensions & Themes', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box5_title', array(
			'label'    	=> esc_html__( 'Box 5 Title', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));
		$wp_customize->add_setting( 'mtps_box5_content', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'Everything you need to know about our extensions and themes.', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box5_content', array(
			'type'		=> 'textarea',
			'label'    	=> esc_html__( 'Box 5 Content', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));
		$wp_customize->add_setting( 'mtps_box5_url', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( '#', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box5_url', array(
			'label'    	=> esc_html__( 'Box 5 URL', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority'  => 2,
		));

		$wp_customize->add_setting( 'mtps_box6_title', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'Developer Documentation', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box6_title', array(
			'label'    	=> esc_html__( 'Box 6 Title', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority' 	=> 2,
		));
		$wp_customize->add_setting( 'mtps_box6_content', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( 'See all of our products\' functions, classes, actions, filters, etc.', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box6_content', array(
			'type'		=> 'textarea',
			'label'    	=> esc_html__( 'Box 6 Content', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority' 	=> 2,
		));
		$wp_customize->add_setting( 'mtps_box6_url', array(
			'transport' => 'refresh',
			'default'	=> esc_html__( '#', 'mtsp' )
		));
		$wp_customize->add_control( 'mtps_box6_url', array(
			'label'    	=> esc_html__( 'Box 6 URL', 'mtsp' ),
			'section'  	=> 'mtsp_support_boxes',
			'priority' 	=> 2,
		));

		// Settings

		// Left Button
		$wp_customize->add_setting( 'mtps_support_left_button_label', array(
			'transport' => 'refresh',
			'default' => esc_html__( 'View Full Documentation', 'mtsp' ),
		));
		$wp_customize->add_control( 'mtps_support_left_button_label', array(
			'label'    => esc_html__( 'Left Button Label', 'mtsp' ),
			'section'  => 'mtsp_support_settings',
			'priority'    => 1,
		));

		$wp_customize->add_setting( 'mtps_support_left_button_url', array(
			'transport' => 'refresh',
			'default' => '#'
		));
		$wp_customize->add_control( 'mtps_support_left_button_url', array(
			'label'    => esc_html__( 'Left Button URL', 'mtsp' ),
			'section'  => 'mtsp_support_settings',
			'priority'    => 1,
		));

		// Right Button
		$wp_customize->add_setting( 'mtps_support_right_button_label', array(
			'transport' => 'refresh',
			'default' => esc_html__( 'Submit Support Request', 'mtsp' ),
		));
		$wp_customize->add_control( 'mtps_support_right_button_label', array(
			'label'    => esc_html__( 'Right Button Label', 'mtsp' ),
			'section'  => 'mtsp_support_settings',
			'priority'    => 1,
		));

		// HelpScout Docs API
		$wp_customize->add_setting( 'mtps_support_docs_api', array(
			'transport' => 'refresh'
		));
		$wp_customize->add_control( 'mtps_support_docs_api', array(
			'label'    => esc_html__( 'HelpScout Docs API', 'mtsp' ),
			'section'  => 'mtsp_support_settings',
			'priority'    => 1,
		));

		// Gravity Form Shortcode
		if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
			
			$wp_customize->add_setting( 'mtps_support_gravity_form', array(
					'transport' => 'refresh',
			        'default' => -1,
			    )
			);
			 
			$wp_customize->add_control( 'mtps_support_gravity_form', array(
			        'type' => 'select',
			        'label' => __( 'Gravity Form Shortcode', 'mtsp' ),
			        'section' => 'mtsp_support_settings',
			        'choices' => mtsp_get_gravity_forms()
			    )
			);

		}

	}
}