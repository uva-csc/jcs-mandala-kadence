<?php

class MandalaKadence {
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

	public function subsite_class() {
		global $post;
		$myid = get_the_ID();
		$subsite_class = $this->get_ancestor_value($myid, 'subsite_class');
		if (!empty($subsite_class)) {
			$subsite_class = explode(' ', $subsite_class);
			return $subsite_class;
		}
		return [];
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