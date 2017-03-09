<?php
namespace CranleighPeople;


if ( !class_exists('CranleighPeople\Settings' ) ):

	require_once( plugin_dir_path( dirname(__FILE__) ).'settingsapiwrapper.php' );

	class Settings {
	
	    private $settings_api;
	
	    function __construct() {
	        $this->settings_api = new \WeDevs_Settings_API;
	
	        add_action( 'admin_init', array($this, 'admin_init') );
	        add_action( 'admin_menu', array($this, 'admin_menu') );
	    }
	
	    function admin_init() {
	
	        //set the settings
	        $this->settings_api->set_sections( $this->get_settings_sections() );
	        $this->settings_api->set_fields( $this->get_settings_fields() );
	
	        //initialize settings
	        $this->settings_api->admin_init();
	    }
	
	    function admin_menu() {
	        add_options_page( 'Cranleigh People', 'Cranleigh People', 'manage_options', 'cranleigh_people_settings', array($this, 'plugin_page') );
	    }
	
	    function get_settings_sections() {
	        $sections = array(
	            array(
	                'id' => 'cran_people_basic',
	                'title' => __( 'Cranleigh People', 'cranleigh-2016' )
	            ),
	        );
	        return $sections;
	    }
	
	    /**
	     * Returns all the settings fields
	     *
	     * @return array settings fields
	     */
	    function get_settings_fields() {
	        $settings_fields = array(
		        "cran_people_basic" => array(
			        array(
	                    'name'    => 'load_cpt',
	                    'label'   => __( 'Load Custom Post Type', 'cranleigh-2016' ),
	                    'desc'    => __( 'Do you want to load the custom post type, or just the widgets and shortcodes?', 'cranleigh-2016' ),
	                    'type'    => 'radio',
	                    'options' => array(
	                        'yes' => 'Yes, load everything.',
	                        'no'  => 'No, I only need the widgets and shortcodes.'
	                    ),
	                    'default' => 'no'
	                ),
	                array(
		                'name' => 'isams_controlled',
		                'label' => __("Under Isams Control", 'cranleigh-2016'),
		                'desc' => __("If this instance under iSAMS control?", "cranleigh-2016"),
		                'type' => 'radio',
		                'options' => [
			                'yes' => 'Yes, it is',
			                'no' => 'No it\'s not, please let me edit things like a normal Wordpress post'
		                ]
	                ),
	               
					array(
						'name' => 'default_photo_attachment_id',
						"label" => __("Default Photo Attachment ID", "cranleigh-2016"),
						"desc" => "The attachment ID of the photo you want to use for the default photo",
						"type" => "text",
						"sanitize_callback" => "intval",
						"default" => 32492
					)
		        )
	        );
	
	        return $settings_fields;
	    }
	
	    function plugin_page() {
	        echo '<div class="wrap">';
	
	        $this->settings_api->show_navigation();
	        $this->settings_api->show_forms();
	
	        echo '</div>';
	    }
	
	    /**
	     * Get all the pages
	     *
	     * @return array page names with key value pairs
	     */
	    function get_pages() {
	        $pages = get_pages();
	        $pages_options = array();
	        if ( $pages ) {
	            foreach ($pages as $page) {
	                $pages_options[$page->ID] = $page->post_title;
	            }
	        }
	
	        return $pages_options;
	    }
	
	}
	endif;
	
