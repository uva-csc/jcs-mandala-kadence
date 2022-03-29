<?php
class MandalaKadence {
	function __construct() {
		add_action('after_setup_theme', array($this, 'init'));
	}

	function init() {
		// Reigster styles and scripts for Mandala Kadence theme
		wp_enqueue_style( 'mandala-kadence-styles', get_stylesheet_directory_uri() . '/css/mandala-kadence.css',
		 	false,'1.0','all');
		wp_enqueue_script( 'mandala-kadence-scripts', get_stylesheet_directory_uri() . '/js/mandala-kadence.js',
			array ( 'jquery' ), 1.0, true);
	}

}

$mandalakadence = new MandalaKadence();