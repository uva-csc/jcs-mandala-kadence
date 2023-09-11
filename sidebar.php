<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kadence
 */

namespace Kadence;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! kadence()->has_sidebar() ) {
	return;
}
kadence()->print_styles( 'kadence-sidebar' );

include_once('mobile-basic-buttons.php');

// Check Mandala Plugin option to see if sidebar is closed and add 'display: none;'
$options = get_option( 'mandala_plugin_options' );
if ($options['default_sidebar'] * 1 == 0) {
    $closestyle = ' style="display: none;"';
}
?>
<aside id="secondary" role="complementary" class="primary-sidebar widget-area <?php echo esc_attr( kadence()->sidebar_id_class() ); ?> sidebar-link-style-<?php echo esc_attr( kadence()->option( 'sidebar_link_style' ) ); ?>"<?php echo $closestyle; ?>>
	<div class="sidebar-inner-wrap">
		<?php
		/**
		 * Hook for before sidebar.
		 */
		do_action( 'kadence_before_sidebar' );

		kadence()->display_sidebar();
		/**
		 * Hook for after sidebar.
		 */
		do_action( 'kadence_after_sidebar' );
		?>
	</div>
</aside><!-- #secondary -->
