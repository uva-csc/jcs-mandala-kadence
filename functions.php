<?php

/* Class for admin settings for Mandala Kadence child theme */
require_once 'class-mandala-admin.php';

/* Class to extend kadence to allow for subsite banners, menus, etc. */
require_once 'class-mandala-kadence.php';

// Create Shortcode to Display Special Issue Post Types
function create_shortcode_issues_post_type(){
    $args = array(
                    'post_type'      => 'special-issue',
                    'posts_per_page' => '-1',
                    'publish_status' => 'published',
                 );
  
    $query = new WP_Query($args);
  
    if($query->have_posts()) :
  
					echo('<div id="archive-container" class="content-wrap grid-cols special-issue-archive grid-sm-col-1 grid-lg-col-1 item-image-style-beside">');

        while($query->have_posts()) :
  
            $query->the_post() ;
                      
        	do_action( 'kadence_loop_entry' );
  
        endwhile;
	
	echo('</div>');
  
        wp_reset_postdata();
  
    endif;    
  
} 
add_shortcode( 'issues-list', 'create_shortcode_issues_post_type' ); 
// shortcode code ends here