<?php
/**
 * Template part for displaying a post
 *
 * @package kadence
 */

namespace Kadence;

?>

<article <?php post_class( 'entry content-bg loop-entry' ); ?>>
	<?php
		/**
		 * Hook for entry thumbnail.
		 *
		 * @hooked Kadence\loop_entry_thumbnail
		 */
		do_action( 'kadence_loop_entry_thumbnail' );
	?>
	<div class="entry-content-wrap">
			
		<!-- Special Issue Custom Field -->
		<?php if(get_field('issue_number')) {
    		echo '<p class="special-issues-number">Special Issue #' . get_field('issue_number') . '</p>';
		} ?>
		
		<?php
		/**
		 * Hook for entry content.
		 *
		 * @hooked Kadence\loop_entry_header - 10
		 * @hooked Kadence\loop_entry_summary - 20
		 * @hooked Kadence\loop_entry_footer - 30
		 */
		do_action( 'kadence_loop_entry_content' );
		?>

        <!-- Special Issue Abstract Link -->
        <?php if(get_field('abstract_link')) {
            echo '<p class="special-issues-link"><a href="' . get_field('abstract_link') . '">Read the full abstract.</a></p>';
        } ?>

        <!-- Special Issue Collection Link -->
        <?php if(get_field('collection_link')) {
            echo '<p class="special-issues-link"><a href="' . get_field('collection_link') . '">Read the issue.</a></p>';
        } ?>

    </div>
</article>
