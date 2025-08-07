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
// shortcode to Display Special Issue Post Types code ends here

// Metatags for indexing

// Disable Yoast meta tags and structured data
add_filter('wpseo_json_ld_output', function() { return false; });
add_filter('wpseo_opengraph',  function() { return false; });
add_filter('wpseo_twitter',  function() { return false; });
add_filter('wpseo_metadesc',  function() { return false; });

function custom_article_meta_tags() {
    if (is_singular('article')) {
        global $post;

        // Basic info
        $title = get_field('main_article_title');
        $subtitle = get_field('article_subtitle');
        if (strlen($subtitle) > 0) {
            $title .= ': ' . $subtitle;
        }
        $authordata = get_field('article_authors');
        $authors = [];
        foreach ($authordata as $author) {
            if ($author && isset($author->ID)) {
                $authors[] = get_the_title($author);
            }
        }
        $issue_id = get_field('special_issue');
        $issue = get_field('issue_number', $issue_id);
        $year = get_field('dates');
        $year = array_pop($year);
        $year = date(DATE_ATOM, strtotime($year));
        $date_scholar = get_the_date('Y/m/d', $year);
        $pages = get_field('page_range');
        $doi = get_field('doi');
        $abstract = get_field('abstract');
        $image = get_the_post_thumbnail_url($post, 'full');
        if ($issue_id) {
            $image = get_the_post_thumbnail_url($issue_id, 'full');
        }
        $url = get_permalink($post);

        /** Chat GPT initial suggestion
         * $title = get_the_title($post);
         * $description = get_the_excerpt($post) ?: wp_trim_words(strip_tags($post->post_content), 30);
         * $url = get_permalink($post);
         * $author = get_the_author_meta('display_name', $post->post_author);
         * $date = get_the_date('c', $post); // ISO 8601 format
         * $image = get_the_post_thumbnail_url($post, 'full') ?: 'https://example.com/default-image.jpg';
         * $site_name = get_bloginfo('name');
         * $twitter_handle = '@YourTwitterHandle'; // Optional
         *
         * // Optional: custom field for citation PDF
         * $pdf_url = get_post_meta($post->ID, 'citation_pdf_url', true);
         */
        // -- Open Graph --
        echo "<meta property='og:title' content='" . esc_attr($title) . "' data-label='csc-custom' />\n";
        echo "<meta property='og:description' content='" . esc_attr($abstract) . "' />\n";
        echo "<meta property='og:type' content='article' />\n";
        echo "<meta property='og:url' content='" . esc_url($url) . "' />\n";
        echo "<meta property='og:image' content='" . esc_url($image) . "' />\n";
        echo "<meta property='og:site_name' content='Journal of Contemplative Studies' />\n";
        echo "<meta property='article:published_time' content='" . esc_attr($year) . "' />\n";
        foreach ($authors as $author) {
            echo "<meta property='article:author' content='" . esc_attr($author) . "' />\n";
        }

        // -- Twitter Card --
        echo "<meta name='twitter:card' content='summary_large_image' />\n";
        echo "<meta name='twitter:title' content='" . esc_attr($title) . "' />\n";
        echo "<meta name='twitter:description' content='" . esc_attr($abstract) . "' />\n";
        echo "<meta name='twitter:image' content='" . esc_url($image) . "' />\n";

        // -- SEO Meta --
        echo "<meta name='description' content='" . esc_attr($abstract) . "' />\n";
        echo "<link rel='canonical' href='" . esc_url($url) . "' />\n";

        // -- Google Scholar Meta Tags --
        echo "<meta name='citation_title' content='" . esc_attr($title) . "' />\n";
        echo "<meta name='citation_publication_date' content='" . esc_attr($date_scholar) . "' />\n";
        foreach ($authors as $author) {
            echo "<meta name='citation_author' content='" . esc_attr($author) . "' />\n";
        }
        echo "<meta name='citation_journal_title' content='Journal of Contemplative Studies' />\n";
        echo "<meta name='citation_issue' content='" . esc_attr($issue) . "' />\n";
        echo "<meta name='citation_firstpage' content='" . esc_attr($pages['start_page']) . "' />\n";
        echo "<meta name='citation_lastpage' content='" . esc_attr($pages['end_page']) . "' />\n";
        echo "<meta name='citation_publication_date' content='" . esc_attr($year) . "' />\n";
        echo "<meta name='citation_doi' content='" . esc_attr($doi) . "' />\n";
        echo "<meta name='citation_publication_date' content='" . esc_attr($date_scholar) . "' />\n";

        // if ($pdf_url) echo "<meta name='citation_pdf_url' content='" . esc_url($pdf_url) . "' />\n";

        // --- JSON-LD Structured Data ---
        $jsonld = [
            "@context" => "https://schema.org",
            "@type" => "ScholarlyArticle",
            "headline" => $title,
            "datePublished" => $year,
            "image" => $image,
            "url" => $url,
            "publisher" => [
                "@type" => "Organization",
                "name" => "Journal of Contemplative Studies",
            ],
            "description" => $abstract,
        ];

        // Add authors
        if ($authors && is_array($authors)) {
            $jsonld["author"] = [];
            foreach ($authors as $author) {
                $name = is_array($author) ? $author['name'] : $author;
                $jsonld["author"][] = [
                    "@type" => "Person",
                    "name" => $name
                ];
            }
        }

        // Add journal hierarchy if available
        if ($issue) {
            $jsonld["isPartOf"] = [
                "@type" => "PublicationIssue",
                "issueNumber" => $issue,
                "isPartOf" => [
                    "@type" => "Periodical",
                    "name" => "Journal of Contemplative Studies",
                ]
            ];
        }

        // Add page range
        if ($pages['start_page']) $jsonld["pageStart"] = $pages['start_page'];
        if ($pages['end_page']) $jsonld["pageEnd"] = $pages['end_page'];

        // Add DOI
        if ($doi) {
            $jsonld["identifier"] = [
                "@type" => "PropertyValue",
                "propertyID" => "DOI",
                "value" => $doi
            ];
        }

        echo "<script type='application/ld+json'>" . json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";

    } else {
        $post_type = get_post_type();
        echo "<meta property='post_type' content='" . esc_attr($post_type) . "' data-label='csc-custom' />\n";
    }
}
add_action('wp_head', 'custom_article_meta_tags');

/*
function custom_meta_tags_test() {
    error_log("************ HERE ************");
    $msg = is_singular('article') ? "This is an Article!!!": "NOT AN ARTICLE";
    echo "<meta property='og:title' content='My Custom meta og title! $msg' />\n";
}

add_action('wp_head', 'custom_meta_tags_test');
*/