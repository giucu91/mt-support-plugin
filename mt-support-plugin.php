<?php
/**
 *	Plugin Name: MT Support Plugin
 *	Plugin URI: http://www.machothemes.com/
 *	Description: Create custom Support Page with Gravity Forms and HelpScout
 *	Version: 1.0.2
 *	Author: Macho Themes
 *	Author URI: http://www.machothemes.com
 */

/*
 * WordPress is_plugin_active method, called first
 */
if ( ! function_exists( 'is_plugin_active' ) ) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/* 
 * To require plugin files properly, create defines to avoide linking issues
 * @since ver 1.0.1
 */

define( 'TI_SUPPORT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'TI_SUPPORT_SUPPORT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Enqueue Style - TODO: Add Assets folder as a define
function mtsp_plugin_style() {
    wp_enqueue_style( 'mtsp-style', TI_SUPPORT_SUPPORT_PLUGIN_URL . 'css/style.css' );
    wp_enqueue_style( 'mtsp-themeisle-style', TI_SUPPORT_SUPPORT_PLUGIN_URL . 'css/themeisle.css' );
}
// add_action( 'wp_enqueue_scripts', 'mtsp_plugin_style' );

/*
 * Required Files
 * gravity-forms-help-scout-search.php: Gravity Forms and Helpscout API client
 * plugin-shortcode.php: Plugin Shortcode function
 * @since ver 1.0.1
 */

require_once( TI_SUPPORT_PLUGIN_PATH . '/gravity-forms-help-scout-search.php' );
require_once( TI_SUPPORT_PLUGIN_PATH . '/plugin-shortcode.php' );

add_filter( 'gform_init_scripts_footer', 'mt_support_add_gravityform_scripts_to_footer' );

function mt_support_add_gravityform_scripts_to_footer() {
    return true;
}

$gravity_form = get_theme_mod( 'mtps_support_gravity_form' );
if ( $gravity_form && $gravity_form != -1 ) {

	$filter_name = 'gform_get_form_filter_'.$gravity_form;
	add_filter( $filter_name, 'macho_filter_gravity_shortcode' );

	function macho_filter_gravity_shortcode( $html ) {

		$new = $html;
		$pattern = "/<script type='text\/javascript'>(.*?)<\/script>/";
		preg_match($pattern, $html, $matches);

		if ( !empty($matches) ) {
			global $mt_gf_script;
			$mt_gf_script = $matches[0];
			$new = str_replace($script, '', $html);
			add_action( 'wp_footer', create_function( '', 'global $mt_gf_script;echo $mt_gf_script;' ), 99 );
		}
		return $new;
	}
}

// Function for populate select with all products
add_filter( 'gform_pre_render', 'mtsp_populate_posts' );
add_filter( 'gform_pre_validation', 'mtsp_populate_posts' );
add_filter( 'gform_pre_submission_filter', 'mtsp_populate_posts' );
add_filter( 'gform_admin_pre_render', 'mtsp_populate_posts' );
function mtsp_populate_posts( $form ) {

    foreach ( $form['fields'] as &$field ) {
        if ( strpos( $field->cssClass, 'populate-products' ) === false && strpos( $field->cssClass, 'populate-first-name' ) === false 
        	&&  strpos( $field->cssClass, 'populate-last-name' ) === false &&  strpos( $field->cssClass, 'populate-email' ) === false ) {
            continue;
        }

        $current_user = wp_get_current_user();

        if ( strpos( $field->cssClass, 'populate-products' ) !== false ) {
        	// you can add additional parameters here to alter the posts that are retrieved
	        // more info: [http://codex.wordpress.org/Template_Tags/get_posts](http://codex.wordpress.org/Template_Tags/get_posts)
	        $posts = get_posts( 'numberposts=-1&post_status=publish&post_type=download' );

	        $choices = array();

	        foreach ( $posts as $post ) {
	            $choices[] = array( 'text' => $post->post_title, 'value' => $post->post_title );
	        }

	        // update 'Select a Post' to whatever you'd like the instructive option to be
	        $field->placeholder = 'Select a Product';
	        $field->choices = $choices;
        }elseif ( strpos( $field->cssClass, 'populate-first-name' ) !== false ) {
        	if ( isset($current_user->first_name) ) {
        		$field->defaultValue = $current_user->user_firstname;
        	}
        }elseif ( strpos( $field->cssClass, 'populate-last-name' ) !== false ) {
        	if ( isset($current_user->last_name) ) {
        		$field->defaultValue = $current_user->user_lastname;
        	}
        }elseif ( strpos( $field->cssClass, 'populate-email' ) !== false ) {
        	if ( isset($current_user->last_name) ) {
        		$field->defaultValue = $current_user->user_email;
        	}
        }
        

    }

    return $form;
}

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
		} else {

            // TODO: Add a notification that Gravity Forums have to be installed, before plugin activated.

        }
	}
}