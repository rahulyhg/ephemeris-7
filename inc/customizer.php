<?php
/**
 * Ephemeris Customizer Setup and Custom Controls
 *
 * @package Ephemeris
 * @since Ephemeris 1.0
 */

/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */
class ephemeris_initialise_customizer_settings {
	// Get our default values
	private $defaults;

	public function __construct() {
		// Get our Customizer defaults
		$this->defaults = ephemeris_generate_defaults();

		// Register our sections
		add_action( 'customize_register', array( $this, 'ephemeris_add_customizer_sections' ) );

		// Register our social media controls
		add_action( 'customize_register', array( $this, 'ephemeris_register_social_controls' ) );

		// Register our Typography controls
		//add_action( 'customize_register', array( $this, 'ephemeris_register_typography_controls' ) );

		// Register our Layout controls
		//add_action( 'customize_register', array( $this, 'ephemeris_register_layout_controls' ) );

		// Register our WooCommerce controls, only if WooCommerce is active
		if( ephemeris_is_woocommerce_active() ) {
			add_action( 'customize_register', array( $this, 'ephemeris_register_woocommerce_controls' ) );
		}

		// Register our sample Custom Control controls
		add_action( 'customize_register', array( $this, 'ephemeris_register_sample_custom_controls' ) );

		// Register our sample default controls
		add_action( 'customize_register', array( $this, 'ephemeris_register_sample_default_controls' ) );

	}

	/**
	 * Register the Customizer sections
	 */
	public function ephemeris_add_customizer_sections( $wp_customize ) {
		/**
		 * Add our Social Icons Section
		 */
		$wp_customize->add_section( 'social_icons_section',
			array(
				'title' => esc_html__(  'Social Icons & Contact Info' ),
				'description' => esc_html__(  'Add your social media lnks and we’ll automatically match them with the appropriate icons. Drag and drop the URLs to rearrange their order.' )
			)
		);

		/**
		 * Add our WooCommerce Layout Section, only if WooCommerce is active
		 */
		 if( ephemeris_is_woocommerce_active() ) {
			 $wp_customize->add_section( 'woocommerce_layout_section',
	 			array(
	 				'title' => esc_html__(  'WooCommerce Layout' ),
	 				'description' => esc_html__(  'Adjust the layout of your WooCommerce shop.' )
	 			)
	 		);
 		}

		$wp_customize->add_section( 'sample_custom_controls_section',
			array(
				'title' => esc_html__(  'Sample Custom Controls' ),
				'description' => esc_html__(  'These are an example of Customizer Custom Controls.' )
			)
		);

		$wp_customize->add_section( 'default_controls_section',
			array(
				'title' => esc_html__(  'Default Controls' ),
				'description' => esc_html__(  'These are an example of the default Customizer Controls.' )
			)
		);

	}

	/**
	 * Register our social media controls
	 */
	public function ephemeris_register_social_controls( $wp_customize ) {

		// Add our Checkbox switch setting and control for opening URLs in a new tab
		$wp_customize->add_setting( 'social_newtab',
			array(
				'default' => $this->defaults['social_newtab'],
				'transport' => 'postMessage',
				'sanitize_callback' => 'ephemeris_switch_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Toggle_Switch_Custom_control( $wp_customize, 'social_newtab',
			array(
				'label' => esc_html__( 'Open in new browser tab', 'ephemeris' ),
				'type' => 'checkbox',
				'settings' => 'social_newtab',
				'section' => 'social_icons_section'
			)
		) );

		$wp_customize->selective_refresh->add_partial( 'social_newtab',
			array(
				'selector' => '.social-icons',
				'container_inclusive' => true,
				'render_callback' => function() {
					echo ephemeris_get_social_media();
				},
				'fallback_refresh' => true
			)
		);

		// Add our Text Radio Button setting and Custom Control for controlling alignment of icons
		$wp_customize->add_setting( 'social_alignment',
			array(
				'default' => $this->defaults['social_alignment'],
				'transport' => 'postMessage',
				'sanitize_callback' => 'ephemeris_text_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Text_Radio_Button_Custom_Control( $wp_customize, 'social_alignment',
			array(
				'label' => esc_attr__( 'Alignment', 'ephemeris' ),
				'description' => esc_attr__( 'Choose the alignment for your social icons', 'ephemeris' ),
				'settings' => 'social_alignment',
				'section' => 'social_icons_section',
				'choices' => array(
					'alignleft' => esc_html__( 'Left' ),
					'alignright' => esc_html__( 'Right' )
				)
			)
		) );

		$wp_customize->selective_refresh->add_partial( 'social_alignment',
			array(
				'selector' => '.social-icons',
				'container_inclusive' => true,
				'render_callback' => function() {
					echo ephemeris_get_social_media();
				},
				'fallback_refresh' => true
			)
		);

		// Add our Sortable Repeater setting and Custom Control for Social media URLs
		$wp_customize->add_setting( 'social_urls',
			array(
				'default' => $this->defaults['social_urls'],
				'transport' => 'postMessage',
				'sanitize_callback' => 'ephemeris_url_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Sortable_Repeater_Custom_Control( $wp_customize, 'social_urls',
			array(
				'label' => esc_html__( 'Sortable Repeater', 'ephemeris' ),
				'description' => esc_html__( 'This is the field description, if needed', 'ephemeris' ),
				'settings' => 'social_urls',
				'section' => 'social_icons_section'
			)
		) );

		$wp_customize->selective_refresh->add_partial( 'social_urls',
			array(
				'selector' => '.social-icons',
				'container_inclusive' => true,
				'render_callback' => function() {
					echo ephemeris_get_social_media();
				},
				'fallback_refresh' => true
			)
		);

		// Add our Single Accordion setting and Custom Control to list the available Social Media icons
		$socialIconsList = array(
			'Behance' => esc_html__( 'fa-behance', 'ephemeris' ),
			'Bitbucket' => esc_html__( 'fa-bitbucket', 'ephemeris' ),
			'CodePen' => esc_html__( 'fa-codepen', 'ephemeris' ),
			'DeviantArt' => esc_html__( 'fa-deviantart', 'ephemeris' ),
			'Dribbble' => esc_html__( 'fa-dribbble', 'ephemeris' ),
			'Etsy' => esc_html__( 'fa-etsy', 'ephemeris' ),
			'Facebook' => esc_html__( 'fa-facebook', 'ephemeris' ),
			'Flickr' => esc_html__( 'fa-flickr', 'ephemeris' ),
			'Foursquare' => esc_html__( 'fa-foursquare', 'ephemeris' ),
			'GitHub' => esc_html__( 'fa-github', 'ephemeris' ),
			'Instagram' => esc_html__( 'fa-instagram', 'ephemeris' ),
			'Last.fm' => esc_html__( 'fa-lastfm', 'ephemeris' ),
			'LinkedIn' => esc_html__( 'fa-linkedin', 'ephemeris' ),
			'Medium' => esc_html__( 'fa-medium', 'ephemeris' ),
			'Pinterest' => esc_html__( 'fa-pinterest', 'ephemeris' ),
			'Google+' => esc_html__( 'fa-google-plus', 'ephemeris' ),
			'Reddit' => esc_html__( 'fa-reddit', 'ephemeris' ),
			'Slack' => esc_html__( 'fa-slack', 'ephemeris' ),
			'SlideShare' => esc_html__( 'fa-slideshare', 'ephemeris' ),
			'Snapchat' => esc_html__( 'fa-snapchat', 'ephemeris' ),
			'SoundCloud' => esc_html__( 'fa-soundcloud', 'ephemeris' ),
			'Spotify' => esc_html__( 'fa-spotify', 'ephemeris' ),
			'Stack Overflow' => esc_html__( 'fa-stack-overflow', 'ephemeris' ),
			'Tumblr' => esc_html__( 'fa-tumblr', 'ephemeris' ),
			'Twitch' => esc_html__( 'fa-twitch', 'ephemeris' ),
			'Twitter' => esc_html__( 'fa-twitter', 'ephemeris' ),
			'Vimeo' => esc_html__( 'fa-vimeo', 'ephemeris' ),
			'YouTube' => esc_html__( 'fa-youtube', 'ephemeris' )
		);
		$wp_customize->add_setting( 'social_url_icons',
			array(
				'default' => '',
				'sanitize_callback' => 'ephemeris_text_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Single_Accordion_Custom_Control( $wp_customize, 'social_url_icons',
			array(
				'label' => esc_html__( 'View list of available icons', 'ephemeris' ),
				'description' => $socialIconsList,
				'settings' => 'social_url_icons',
				'section' => 'social_icons_section'
			)
		) );

		// Add our Checkbox switch setting and Custom Control for displaying an RSS icon
		$wp_customize->add_setting( 'social_rss',
			array(
				'default' => $this->defaults['social_rss'],
				'transport' => 'postMessage',
				'sanitize_callback' => 'ephemeris_switch_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Toggle_Switch_Custom_control( $wp_customize, 'social_rss',
			array(
				'label' => esc_html__( 'Display RSS icon', 'ephemeris' ),
				'type' => 'checkbox',
				'settings' => 'social_rss',
				'section' => 'social_icons_section'
			)
		) );

		$wp_customize->selective_refresh->add_partial( 'social_rss',
			array(
				'selector' => '.social-icons',
				'container_inclusive' => true,
				'render_callback' => function() {
					echo ephemeris_get_social_media();
				},
				'fallback_refresh' => true
			)
		);

		// Add our Text field setting and Control for displaying the phone number
		$wp_customize->add_setting( 'social_phone',
			array(
				'default' => $this->defaults['social_phone'],
				'transport' => 'postMessage',
				'sanitize_callback' => 'ephemeris_text_sanitization'
			)
		);
		$wp_customize->add_control( 'social_phone',
			array(
				'label' => esc_html__( 'Display phone number', 'ephemeris' ),
				'type' => 'text',
				'settings' => 'social_phone',
				'section' => 'social_icons_section'
			)
		);

		$wp_customize->selective_refresh->add_partial( 'social_phone',
			array(
				'selector' => '.social-icons',
				'container_inclusive' => true,
				'render_callback' => function() {
					echo ephemeris_get_social_media();
				},
				'fallback_refresh' => true
			)
		);

	}

	/**
	 * Register our WooCommerce Layout controls
	 */
	public function ephemeris_register_woocommerce_controls( $wp_customize ) {

		// Add our Checkbox switch setting and control for displaying a sidebar on the shop page
		$wp_customize->add_setting( 'woocommerce_shop_sidebar',
			array(
				'default' => $this->defaults['woocommerce_shop_sidebar'],
				'transport' => 'postMessage',
				'sanitize_callback' => 'ephemeris_switch_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Toggle_Switch_Custom_control( $wp_customize, 'woocommerce_shop_sidebar',
			array(
				'label' => esc_html__( 'Show sidebar on Shop page', 'ephemeris' ),
				'type' => 'checkbox',
				'settings' => 'woocommerce_shop_sidebar',
				'section' => 'woocommerce_layout_section'
			)
		) );

		// Add our Simple Notice setting and control for displaying a message about the WooCommerce shop sidebars
		$wp_customize->add_setting( 'woocommerce_other_sidebar',
			array(
				'transport' => 'postMessage',
				'sanitize_callback' => 'ephemeris_text_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Simple_Notice_Custom_control( $wp_customize, 'woocommerce_other_sidebar',
			array(
				'label' => esc_html__( 'Cart, Checkout & My Account sidebars', 'ephemeris' ),
				'description' 	=> esc_html__('The Cart, Checkout and My Account pages are displayed using shortcodes. To remove the sidebar from these Pages, simply edit each Page and change the Template (in the Page Attributes Panel) to Full-width Page.', 'ephemeris'),
				'settings' => 'woocommerce_other_sidebar',
				'section' => 'woocommerce_layout_section'
			)
		) );

	}

	/**
	 * Register our sample custom controls
	 */
	public function ephemeris_register_sample_custom_controls( $wp_customize ) {

		// Test of Slider Custom Control
		$wp_customize->add_setting( 'sample_header_font_size',
			array(
				'default' => '18',
				'transport' => 'postMessage',
				'sanitize_callback' => 'ephemeris_sanitize_integer'
			)
		);
		$wp_customize->add_control( new Skyrocket_Slider_Custom_Control( $wp_customize, 'sample_header_font_size',
			array(
				'label' => esc_html__( 'Slider Control (px)', 'ephemeris' ),
				'settings' => 'sample_header_font_size',
				'section' => 'sample_custom_controls_section',
				'input_attrs' => array(
					'min' => 10,
					'max' => 50,
					'step' => 2,
				),
			)
		) );

		// Test of Image Radio Button Custom Control
		$wp_customize->add_setting( 'sample_image_options',
			array(
				'default' => 'sidebarright',
				'sanitize_callback' => 'ephemeris_text_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Image_Radio_Button_Custom_Control( $wp_customize, 'sample_image_options',
			array(
				'label' => esc_attr__( 'Image Radio Button Control', 'ephemeris' ),
				'description' => esc_attr__( 'Sample custom control description', 'ephemeris' ),
				'settings' => 'sample_image_options',
				'section' => 'sample_custom_controls_section',
				'choices' => array(
					'sidebarleft' => array(
						'image' => trailingslashit( get_template_directory_uri() ) . 'images/sidebar-left.png',
						'name' => esc_html__( 'Left Sidebar' )
					),
					'sidebarnone' => array(
						'image' => trailingslashit( get_template_directory_uri() ) . 'images/sidebar-none.png',
						'name' => esc_html__( 'No Sidebar' )
					),
					'sidebarright' => array(
						'image' => trailingslashit( get_template_directory_uri() ) . 'images/sidebar-right.png',
						'name' => esc_html__( 'Right Sidebar' )
					)
				)
			)
		) );

		$wp_customize->add_setting( 'sample_text_layout',
			array(
				'default' => 'right',
				'sanitize_callback' => 'ephemeris_text_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Text_Radio_Button_Custom_Control( $wp_customize, 'sample_text_layout',
			array(
				'label' => esc_attr__( 'Text Radio Button Control', 'ephemeris' ),
				'description' => esc_attr__( 'Sample custom control description', 'ephemeris' ),
				'settings' => 'sample_text_layout',
				'section' => 'sample_custom_controls_section',
				'choices' => array(
					'left' => esc_html__( 'Left' ),
					'centered' => esc_html__( 'Centered' ),
					'right' => esc_html__( 'Right' )
				)
			)
		) );

		// Test of Image Checkbox Custom Control
		$wp_customize->add_setting( 'sample_header_font_style',
			array(
				'default' => 'stylebold',
				'sanitize_callback' => 'ephemeris_text_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Image_checkbox_Custom_Control( $wp_customize, 'sample_header_font_style',
			array(
				'label' => esc_attr__( 'Image Checkbox Control', 'ephemeris' ),
				'description' => esc_attr__( 'Sample custom control description', 'ephemeris' ),
				'settings' => 'sample_header_font_style',
				'section' => 'sample_custom_controls_section',
				'choices' => array(
					'stylebold' => array(
						'image' => trailingslashit( get_template_directory_uri() ) . 'images/Bold.png',
						'name' => esc_html__( 'Bold' )
					),
					'styleitalic' => array(
						'image' => trailingslashit( get_template_directory_uri() ) . 'images/Italic.png',
						'name' => esc_html__( 'Italic' )
					),
					'styleallcaps' => array(
						'image' => trailingslashit( get_template_directory_uri() ) . 'images/AllCaps.png',
						'name' => esc_html__( 'All Caps' )
					),
					'styleunderline' => array(
						'image' => trailingslashit( get_template_directory_uri() ) . 'images/Underline.png',
						'name' => esc_html__( 'Underline' )
					)
				)
			)
		) );

		// Test of Single Accordion Control
		$sampleIconsList = array(
			'Behance' => esc_html__( 'fa-behance', 'ephemeris' ),
			'Bitbucket' => esc_html__( 'fa-bitbucket', 'ephemeris' ),
			'CodePen' => esc_html__( 'fa-codepen', 'ephemeris' ),
			'DeviantArt' => esc_html__( 'fa-deviantart', 'ephemeris' ),
			'Dribbble' => esc_html__( 'fa-dribbble', 'ephemeris' ),
			'Etsy' => esc_html__( 'fa-etsy', 'ephemeris' ),
			'Facebook' => esc_html__( 'fa-facebook', 'ephemeris' ),
			'Flickr' => esc_html__( 'fa-flickr', 'ephemeris' ),
			'Foursquare' => esc_html__( 'fa-foursquare', 'ephemeris' ),
			'GitHub' => esc_html__( 'fa-github', 'ephemeris' ),
		);
		$wp_customize->add_setting( 'sample_single_accordion',
			array(
				'default' => '',
				'sanitize_callback' => 'ephemeris_text_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Single_Accordion_Custom_Control( $wp_customize, 'sample_single_accordion',
			array(
				'label' => esc_html__( 'Single Accordion Control', 'ephemeris' ),
				'description' => $sampleIconsList,
				'settings' => 'sample_single_accordion',
				'section' => 'sample_custom_controls_section'
			)
		) );

		// Test of Alpha Color Picker Control
		$wp_customize->add_setting( 'sample_body_font_alpha_color',
			array(
				'default' => 'rgba(209,0,55,0.7)',
				'transport' => 'postMessage'
			)
		);
		$wp_customize->add_control( new Skyrocket_Customize_Alpha_Color_Control( $wp_customize, 'sample_body_font_alpha_color',
			array(
				'label' => esc_attr__( 'Alpha Color Picker Control', 'ephemeris' ),
				'section' => 'sample_custom_controls_section',
				'settings' => 'sample_body_font_alpha_color',
				'show_opacity' => true,
				'palette' => array(
					'#000',
					'#fff',
					'#df312c',
					'#df9a23',
					'#eef000',
					'#7ed934',
					'#1571c1',
					'#8309e7'
				)
			)
		) );

		// Test of Simple Notice control
		$wp_customize->add_setting( 'sample_simple_notice',
			array(
				'transport' => 'postMessage',
				'sanitize_callback' => 'ephemeris_text_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Simple_Notice_Custom_control( $wp_customize, 'sample_simple_notice',
			array(
				'label' => esc_html__( 'Simple Notice Control', 'ephemeris' ),
				'description' 	=> esc_html__('This Custom Control allows you to display a simple title and description to your users.', 'ephemeris'),
				'settings' => 'sample_simple_notice',
				'section' => 'sample_custom_controls_section'
			)
		) );

		// Test of Google Font Select Control
		$wp_customize->add_setting( 'sample_body_font',
			array(
			 'default' => '{"font":"Open Sans","regularweight":"regular","italicweight":"italic","boldweight":"700","category":"sans-serif"}'
			)
		);
		$wp_customize->add_control( new Skyrocket_Google_Font_Select_Custom_Control( $wp_customize, 'sample_body_font',
			array(
				'label' => esc_attr__( 'Google Font Control', 'ephemeris' ),
				'description' => esc_attr__( 'Sample custom control description', 'ephemeris' ),
				'section' => 'sample_custom_controls_section',
				'settings' => 'sample_body_font'
			)
		) );

	}

	/**
	 * Register our sample default controls
	 */
	public function ephemeris_register_sample_default_controls( $wp_customize ) {

		// Test of Text Control
        $wp_customize->add_setting( 'sample_default_text',
  			array(
  				'default' => ''
  			)
        );
        $wp_customize->add_control( 'sample_default_text',
  			array(
 				'label' => 'Default Text Control',
 				'description' => 'Text controls Type can be either text, email, url, number, hidden, or date',
  				'section' => 'default_controls_section',
 				'settings' => 'sample_default_text',
 				'type' => 'text'
  			)
  		);

		 // Test of Standard Checkbox Control
 		$wp_customize->add_setting( 'sample_default_checkbox',
 			array(
 			'default' => 0
 			)
 		);
 		$wp_customize->add_control( 'sample_default_checkbox',
 			array(
 				'label' => esc_html__( 'Default Checkbox Control', 'ephemeris' ),
				'section'  => 'default_controls_section',
 				'settings' => 'sample_default_checkbox',
 				'type'=> 'checkbox',
 				'std' => '1'
 			)
 		);

 		// Test of Standard Select Control
 		$wp_customize->add_setting( 'sample_default_select',
 			array(
 				'default'=>'jet-fuel'
 			)
 		);
 		$wp_customize->add_control( 'sample_default_select',
 			array(
 				'label' => 'Standard Select Control',
 				'section' => 'default_controls_section',
				'settings' => 'sample_default_select',
				'type' => 'select',
 				'choices' => array(
 	            'wordpress' => 'WordPress',
 	            'hamsters' => 'Hamsters',
 	            'jet-fuel' => 'Jet Fuel',
 	            'nuclear-energy' => 'Nuclear Energy'
 				)
 			)
 		);

		// Test of Standard Radio Control
 		$wp_customize->add_setting( 'sample_default_radio',
 			array(
 				'default'=>'spider-man'
 			)
 		);
 		$wp_customize->add_control( 'sample_default_radio',
 			array(
 				'label' => 'Standard Radio Control',
 				'section' => 'default_controls_section',
				'settings' => 'sample_default_radio',
				'type' => 'radio',
 				'choices' => array(
 	            'captain-america' => 'Captain America',
 	            'iron-man' => 'Iron Man',
 	            'spider-man' => 'Spider-Man',
 	            'thor' => 'Thor'
 				)
 			)
 		);

		// Test of Dropdown Pages Control
       $wp_customize->add_setting( 'sample_default_dropdownpages',
 			array(
 				'default' => '0'
 			)
       );
       $wp_customize->add_control( 'sample_default_dropdownpages',
 			array(
				'label' => 'Default Dropdown Pages Control',
 				'section' => 'default_controls_section',
				'settings' => 'sample_default_dropdownpages',
				'type' => 'dropdown-pages'
 			)
 		);

		// Test of Textarea Control
       $wp_customize->add_setting( 'sample_default_textarea',
 			array(
 				'default' => ''
 			)
       );
       $wp_customize->add_control( 'sample_default_textarea',
 			array(
				'label' => 'Default Textarea Control',
 				'section' => 'default_controls_section',
				'settings' => 'sample_default_textarea',
				'type' => 'textarea'
 			)
 		);

		// Test of Color Control
       $wp_customize->add_setting( 'sample_default_color',
 			array(
 				'default' => '#333'
 			)
       );
       $wp_customize->add_control( 'sample_default_color',
 			array(
				'label' => 'Default Color Control',
 				'section' => 'default_controls_section',
				'settings' => 'sample_default_color',
				'type' => 'color'
 			)
 		);

	}
}

/**
 * Load all our Customizer Custom Controls
 */
require_once trailingslashit( dirname(__FILE__) ) . 'custom-controls.php';

/**
 * Initialise our Customizer settings
 */
new ephemeris_initialise_customizer_settings();
