<?php

/**
 * Mandala Kadence Theme Class : A child theme of Kadence for use with Mandala content
 *
 * This theme allows for custom styling of Kadence to work with Mandala content. The stylesheets are written in
 * SCSS but must be processed into CSS which is also stored in this repo. In the init() function the theme only
 * adds the CSS file. Only generic styles for adapting any Mandala content to the Kadence theme should be placed
 * in this theme’s css. Custom CSS for subsites or specific sites should use the subsite class and custom CSS.
 *
 * This theme also allows for subsites. This requires the use of custom metadata for pages. This is best implemented
 * through the plugin Advanced Custom Fields. The necessary fields are:
 *
 *      subsite_nav : The ID number of the custom menu to use
 *      subsite_title : The Title for the subsite to display in the header in place of the blogname
 *      subsite_class : A special class to put in the <body> element to identify pages of a subsite.
 *
 * Any page *or children of a page* that has these fields set will display the specific menu and title in the header
 * and attach the subsite class to the body along with the generic class "subsite".
 *
 * @author  Than Grove
 * @package  Mandala Kadence
 * @version  1.0
 */
class MandalaKadence {
	/**
	 * The construct function adds actions and filters for
	 */
	function __construct() {
		// Custom Actions
		add_action('after_setup_theme', array($this, 'init'));
		add_action('get_template_part_template-parts/header/navigation', array($this, 'subsite_nav'));
		add_action('kadence_site_branding', array($this, 'subsite_title'));

		// Custom Filters
		add_filter('body_class', array($this, 'subsite_class'));
		add_filter('pre_option_blogname', array($this, 'subsite_bname'));
	}

	function init() {
		// Reigster styles and scripts for Mandala Kadence theme
		wp_enqueue_style( 'mandala-kadence-styles', get_stylesheet_directory_uri() . '/css/mandala-kadence.css',
		 	false,'1.0','all');
		wp_enqueue_script( 'mandala-kadence-scripts', get_stylesheet_directory_uri() . '/js/mandala-kadence.js',
			array ( 'jquery' ), 1.0, true);

		// Add custom styles from /css/custom folder (just files ending in *.css)
		$custom_folder = get_stylesheet_directory() . '/css/custom';
		$files = array_diff(scandir($custom_folder), array('.', '..'));
		$cssfiles = array_filter($files, function($fnm) {
			return str_ends_with($fnm, '.css');
		});

		foreach($cssfiles as $n => $cssfile) {
			// error_log(get_stylesheet_directory_uri() . "/css/custom/$cssfile");
			wp_enqueue_style( 'mandala-kadence-custom-styles-' . $n,  get_stylesheet_directory_uri() . "/css/custom/$cssfile",
				false,'1.0','all');
		}
	}
	public function subsite_bname($bname) {
		global $post;
		$myid = get_the_ID();
		$subsite_title = $this->get_ancestor_value($myid, 'subsite_title');
		if (!empty($subsite_title)) {
			return $subsite_title;
		}
		return $bname;
	}

	/**
	 * This function is called by the filter for "body_class". It adds specific classes to the body for subsites
	 *
	 * @return array|string[]
	 */
	public function subsite_class() {
		global $post;
		$myid = get_the_ID();
		$subsite_class = $this->get_ancestor_value($myid, 'subsite_class');
		if (!empty($subsite_class)) {
			$subsite_class = explode(' ', $subsite_class);
			$subsite_class = array_merge(array('subsite'), $subsite_class); # add generic "subsite" class to body as well
			return $subsite_class;
		}
		// Add "subsite" body class to any page with subsite title defined
		$subsite_title = $this->get_ancestor_value($myid, 'subsite_title');
		if (!empty($subsite_title)) {
			return array('subsite');
		}
		return array();
	}

	public function subsite_title() {
		global $post;
		$myid = get_the_ID();
		$subsite_title = $this->get_ancestor_value($myid, 'subsite_title');
		if (!empty($subsite_title)) {
			echo '<div id="subsite-title"><span class="subsite-title">' . $subsite_title . '</span></div>';
		}
	}

	public function subsite_nav() {
		global $post;
		// Custom fields for subsite existence and subsite alt text.
		$myid = get_the_ID();
		$subsite = $this->get_ancestor_value($myid, 'subsite_menu');
		if (!empty($subsite)) {
			$menu_args = array(
				'menu'            => 31,
				'menu_class'      => 'menu',
				'container'       => 'div',
				'container_id'    => 'subsite-menu',
				'container_class' => 'subsite-menu primary-menu-container header-menu-container',
				'theme_location'  => 'main_center',
				'echo'            => false,
			);
			$menu      = wp_nav_menu( $menu_args );
			$prefels   = '<div id="mandala-subsite-menu"
							 class="site-header-item site-header-focus-item site-header-item-main-navigation
	                                header-navigation-layout-stretch-false header-navigation-layout-fill-stretch-false" 
	                        data-section="kadence_customizer_primary_navigation">
						<nav id="site-navigation" 
							 class="main-navigation header-navigation nav--toggle-sub 
									header-navigation-style-underline header-navigation-dropdown-animation-none" 
							role="navigation" aria-label="Primary Navigation">';
			$suffels   = '</nav></div>';
			echo "$prefels $menu $suffels";
		}
	}

	public function get_ancestor_value($pid, $varname) {
		if (empty($pid) || !is_numeric($pid)) { return false; }
		$val = get_post_meta($pid, $varname, true);
		if (empty($val)) {
			$thepost = get_post($pid);
			if (!empty($thepost->post_parent)) {
				$val = $this->get_ancestor_value($thepost->post_parent, $varname);
			}
		}
		return $val;
	}

}

$mandalakadence = new MandalaKadence();